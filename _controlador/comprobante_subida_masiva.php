<?php

require_once("../_conexion/sesion.php");
require_once("../_conexion/conexion.php");
require_once("../_modelo/m_comprobante.php");

header('Content-Type: application/json');

$id_compra = isset($_POST['id_compra']) ? intval($_POST['id_compra']) : 0;
$enviar_proveedor = isset($_POST['enviar_proveedor']) && $_POST['enviar_proveedor'] == '1';
$enviar_contabilidad = isset($_POST['enviar_contabilidad']) && $_POST['enviar_contabilidad'] == '1';
$enviar_tesoreria = isset($_POST['enviar_tesoreria']) && $_POST['enviar_tesoreria'] == '1';

if ($id_compra <= 0 || empty($_FILES['archivos'])) {
    echo json_encode(['success' => false, 'mensaje' => 'Datos incompletos']);
    exit;
}

$archivos = $_FILES['archivos'];
$exitosos = 0;
$fallidos = 0;

for ($i = 0; $i < count($archivos['name']); $i++) {
    $archivo = [
        'name' => $archivos['name'][$i],
        'type' => $archivos['type'][$i],
        'tmp_name' => $archivos['tmp_name'][$i],
        'error' => $archivos['error'][$i],
        'size' => $archivos['size'][$i]
    ];

    // 1️⃣ Extraer serie y número del nombre del archivo
    $nombre_sin_ext = pathinfo($archivo['name'], PATHINFO_FILENAME); // F001-1234
    if (!preg_match('/^([A-Z0-9]+)[-_](\d+)$/i', $nombre_sin_ext, $match)) {
        $fallidos++;
        continue; // nombre no válido
    }

    $serie = strtoupper($match[1]);
    $numero = ltrim($match[2]);

    // 2️⃣ Buscar comprobante correspondiente
    $sql = "SELECT id_comprobante FROM comprobante 
            WHERE serie = '$serie' AND numero = '$numero' AND id_compra = $id_compra
            LIMIT 1";
    $res = mysqli_query($con, $sql);

    if (!$res) {
        error_log("❌ Error SQL: " . mysqli_error($con) . " | Consulta: $sql");
        $fallidos++;
        continue;
    }

    $row = mysqli_fetch_assoc($res);

    if (!$row) {
        error_log("⚠️ No se encontró comprobante para serie=$serie numero=$numero compra=$id_compra");
        $fallidos++;
        continue;
    }

    $id_comprobante = $row['id_comprobante'];

    // 3️⃣ Procesar archivo (guardar en servidor)
    $resultado_archivo = procesarArchivoMasivo($archivo);

    if (!$resultado_archivo['error']) {
        // 4️⃣ Asociar a comprobante
        $resultado = SubirVoucherComprobante(
            $id_comprobante,
            $resultado_archivo['ruta'],
            $_SESSION['id_personal'],
            $enviar_proveedor,
            $enviar_contabilidad,
            $enviar_tesoreria
        );

        if (strpos($resultado, 'SI|') === 0) {
            $exitosos++;
        } else {
            $fallidos++;
        }
    } else {
        $fallidos++;
    }
}

echo json_encode([
    'success' => true,
    'exitosos' => $exitosos,
    'fallidos' => $fallidos
]);

function procesarArchivoMasivo($archivo) {
    $resultado = ['error' => false, 'mensaje' => '', 'ruta' => null];
    
    if ($archivo['size'] > 5 * 1024 * 1024) {
        $resultado['error'] = true;
        $resultado['mensaje'] = 'Archivo muy grande';
        return $resultado;
    }

    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    $extensiones_permitidas = ['pdf', 'jpg', 'jpeg', 'png'];
    
    if (!in_array($extension, $extensiones_permitidas)) {
        $resultado['error'] = true;
        $resultado['mensaje'] = 'Formato no permitido';
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
        $resultado['mensaje'] = 'No se pudo guardar';
    }

    return $resultado;
}
?>