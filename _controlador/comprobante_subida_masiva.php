<?php
require_once("../_conexion/sesion.php");
require_once("../_conexion/conexion.php");
require_once("../_modelo/m_comprobante.php");

header('Content-Type: application/json');

$enviar_proveedor = isset($_POST['enviar_proveedor']) && $_POST['enviar_proveedor'] == '1';
$enviar_contabilidad = isset($_POST['enviar_contabilidad']) && $_POST['enviar_contabilidad'] == '1';
$enviar_tesoreria = isset($_POST['enviar_tesoreria']) && $_POST['enviar_tesoreria'] == '1';

if (empty($_FILES['archivos'])) {
    echo json_encode(['success' => false, 'mensaje' => 'Datos incompletos']);
    exit;
}

$archivos = $_FILES['archivos'];
$exitosos = 0;
$errores = []; // 🔥 NUEVO: Array para almacenar errores detallados

for ($i = 0; $i < count($archivos['name']); $i++) {
    $archivo = [
        'name' => $archivos['name'][$i],
        'type' => $archivos['type'][$i],
        'tmp_name' => $archivos['tmp_name'][$i],
        'error' => $archivos['error'][$i],
        'size' => $archivos['size'][$i]
    ];

    $nombre_archivo = $archivo['name'];

    // 1️⃣ Extraer serie y número del nombre del archivo
    $nombre_sin_ext = pathinfo($nombre_archivo, PATHINFO_FILENAME);
    if (!preg_match('/^([A-Z0-9]+)[-_](\d+)$/i', $nombre_sin_ext, $match)) {
        $errores[] = [
            'archivo' => $nombre_archivo,
            'motivo' => 'Formato de nombre inválido (debe ser SERIE-NUMERO)'
        ];
        continue;
    }

    $serie = strtoupper($match[1]);
    $numero = ltrim($match[2]);

    // 2️⃣ Buscar comprobante correspondiente
    $sql = "SELECT c.id_comprobante, p.nom_proveedor, p.ruc_proveedor
        FROM comprobante c
        INNER JOIN compra co ON co.id_compra = c.id_compra
        INNER JOIN proveedor p ON p.id_proveedor = co.id_proveedor
        WHERE c.serie = '$serie' AND c.numero = '$numero' AND c.est_comprobante = 2";

    $res = mysqli_query($con, $sql);

    if (!$res) {
        error_log("❌ Error SQL: " . mysqli_error($con));
        $errores[] = [
            'archivo' => $nombre_archivo,
            'motivo' => 'Error en la base de datos'
        ];
        continue;
    }

    $num_coincidencias = mysqli_num_rows($res);

    if ($num_coincidencias == 0) {
        $errores[] = [
            'archivo' => $nombre_archivo,
            'motivo' => "No se encontró comprobante $serie-$numero con estado 'Por Pagar'"
        ];
        continue;
    } elseif ($num_coincidencias == 1) {
        // Coincidencia única → asignar directamente
        $row = mysqli_fetch_assoc($res);
        $id_comprobante = $row['id_comprobante'];

        // ✅ VALIDAR ANTES DE PROCESAR EL ARCHIVO
        $sql_check_pago = "SELECT 
            COALESCE(SUM(CASE WHEN fg_comprobante_pago = 1 AND est_comprobante_pago = 1 THEN 1 ELSE 0 END),0) AS cnt_monto,
            COALESCE(SUM(CASE WHEN fg_comprobante_pago = 2 AND est_comprobante_pago = 1 THEN 1 ELSE 0 END),0) AS cnt_impuesto,
            c.id_detraccion
        FROM comprobante_pago cp
        INNER JOIN comprobante c ON cp.id_comprobante = c.id_comprobante
        WHERE cp.id_comprobante = $id_comprobante
        GROUP BY c.id_detraccion";
        
        $res_check_pago = mysqli_query($con, $sql_check_pago);
        
        if ($res_check_pago && mysqli_num_rows($res_check_pago) > 0) {
            $pago_data = mysqli_fetch_assoc($res_check_pago);
            $cnt_monto = intval($pago_data['cnt_monto']);
            $cnt_impuesto = intval($pago_data['cnt_impuesto']);
            $tiene_detraccion = floatval($pago_data['id_detraccion']) > 0;
            
            if (!$tiene_detraccion && $cnt_monto >= 1) {
                $errores[] = [
                    'archivo' => $nombre_archivo,
                    'motivo' => 'Ya tiene un voucher de pago registrado'
                ];
                continue;
            }
            
            if ($tiene_detraccion && $cnt_monto >= 1 && $cnt_impuesto >= 1) {
                $errores[] = [
                    'archivo' => $nombre_archivo,
                    'motivo' => 'Ya tiene ambos pagos registrados (monto + detracción)'
                ];
                continue;
            }
        }

        $resultado_archivo = procesarArchivoMasivo($archivo);
        if (!$resultado_archivo['error']) {
            $fecha_voucher = date('Y-m-d');
            $resultado = SubirVoucherComprobante(
                $id_comprobante,
                $resultado_archivo['ruta'],
                $_SESSION['id_personal'],
                $enviar_proveedor,
                $enviar_contabilidad,
                $enviar_tesoreria,
                $fecha_voucher
            );

            if (strpos($resultado, 'SI|') === 0) {
                $exitosos++;
            } else {
                $errores[] = [
                    'archivo' => $nombre_archivo,
                    'motivo' => 'Error al guardar en base de datos'
                ];
            }
        } else {
            $errores[] = [
                'archivo' => $nombre_archivo,
                'motivo' => $resultado_archivo['mensaje']
            ];
        }
    } else {
        // ⚠️ Conflicto: varias coincidencias
        $opciones = [];
        while($row = mysqli_fetch_assoc($res)) {
            $opciones[] = $row;
        }

        echo json_encode([
            'conflicto' => true,
            'archivo' => $nombre_archivo,
            'serie' => $serie,
            'numero' => $numero,
            'opciones' => $opciones
        ]);
        exit;
    }
}

echo json_encode([
    'success' => true,
    'exitosos' => $exitosos,
    'errores' => $errores // 🔥 NUEVO: Devolver errores detallados
]);

function procesarArchivoMasivo($archivo) {
    $resultado = ['error' => false, 'mensaje' => '', 'ruta' => null];
    
    if ($archivo['size'] > 5 * 1024 * 1024) {
        $resultado['error'] = true;
        $resultado['mensaje'] = 'Archivo muy grande (máx. 5MB)';
        return $resultado;
    }

    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    $extensiones_permitidas = ['pdf', 'jpg', 'jpeg', 'png'];
    
    if (!in_array($extension, $extensiones_permitidas)) {
        $resultado['error'] = true;
        $resultado['mensaje'] = 'Formato no permitido (solo PDF, JPG, PNG)';
        return $resultado;
    }

    $carpeta_destino = __DIR__ . "/../_upload/vouchers/";
    
    if (!is_dir($carpeta_destino)) {
        mkdir($carpeta_destino, 0777, true);
    }

    $nombre_archivo = "voucher_" . time() . "_" . uniqid() . "." . $extension;
    $ruta_destino = $carpeta_destino . $nombre_archivo;

    if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
        $resultado['ruta'] = $nombre_archivo;
    } else {
        $resultado['error'] = true;
        $resultado['mensaje'] = 'No se pudo guardar el archivo en el servidor';
    }

    return $resultado;
}
?>