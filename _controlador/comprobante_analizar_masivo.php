<?php
require_once("../_conexion/sesion.php");
require_once("../_conexion/conexion.php");

header('Content-Type: application/json');

if (empty($_FILES['archivos'])) {
    echo json_encode(['success' => false, 'mensaje' => 'No se recibieron archivos']);
    exit;
}

$archivos = $_FILES['archivos'];
$correctos = [];
$conflictos = [];
$errores = [];

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
    $numero = $match[2];

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

    // 3️⃣ Clasificar según coincidencias
    if ($num_coincidencias == 0) {
        $errores[] = [
            'archivo' => $nombre_archivo,
            'motivo' => "No se encontró comprobante $serie-$numero con estado 'Por Pagar'"
        ];
    } elseif ($num_coincidencias == 1) {
        // ✅ Coincidencia única
        $row = mysqli_fetch_assoc($res);
        $id_comprobante = $row['id_comprobante'];

        // 4️⃣ Validar que no tenga voucher ya registrado
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

        // ✅ Agregar a correctos
        $correctos[] = [
            'archivo' => $nombre_archivo,
            'serie' => $serie,
            'numero' => $numero,
            'id_comprobante' => $id_comprobante,
            'nom_proveedor' => $row['nom_proveedor'],
            'ruc_proveedor' => $row['ruc_proveedor'],
            'archivo_temporal' => base64_encode(file_get_contents($archivo['tmp_name'])), // 🔥 Guardar contenido
            'extension' => strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION))
        ];
    } else {
        // ⚠️ Múltiples coincidencias
        $opciones = [];
        while($row = mysqli_fetch_assoc($res)) {
            $opciones[] = $row;
        }

        $conflictos[] = [
            'archivo' => $nombre_archivo,
            'serie' => $serie,
            'numero' => $numero,
            'opciones' => $opciones,
            'archivo_temporal' => base64_encode(file_get_contents($archivo['tmp_name'])), // 🔥 Guardar contenido
            'extension' => strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION))
        ];
    }
}

// 5️⃣ Devolver clasificación completa
echo json_encode([
    'success' => true,
    'correctos' => $correctos,
    'conflictos' => $conflictos,
    'errores' => $errores
]);
?>