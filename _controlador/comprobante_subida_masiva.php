<?php

require_once("../_conexion/sesion.php");
require_once("../_conexion/conexion.php");
require_once("../_modelo/m_comprobante.php");

header('Content-Type: application/json');

// Recibir datos
$datos = json_decode(file_get_contents('php://input'), true);

if (empty($datos['archivos_a_registrar'])) {
    echo json_encode(['success' => false, 'mensaje' => 'No hay archivos para registrar']);
    exit;
}

$archivos_a_registrar = $datos['archivos_a_registrar'];
$enviar_proveedor = isset($datos['enviar_proveedor']) && $datos['enviar_proveedor'] == 1;
$enviar_contabilidad = isset($datos['enviar_contabilidad']) && $datos['enviar_contabilidad'] == 1;
$enviar_tesoreria = isset($datos['enviar_tesoreria']) && $datos['enviar_tesoreria'] == 1;
$enviar_compras = isset($datos['enviar_compras']) && $datos['enviar_compras'] == 1;

$exitosos = 0;
$errores = [];
$fecha_voucher = date('Y-m-d');

// Procesar TODOS los archivos en masa
foreach ($archivos_a_registrar as $item) {
    $nombre_archivo = $item['archivo'];
    $id_comprobante = intval($item['id_comprobante']);
    $contenido_base64 = $item['archivo_temporal'];
    $extension = $item['extension'];

    // 1️⃣ Validar extensión
    $extensiones_permitidas = ['pdf', 'jpg', 'jpeg', 'png'];
    if (!in_array($extension, $extensiones_permitidas)) {
        $errores[] = [
            'archivo' => $nombre_archivo,
            'motivo' => 'Formato no permitido (solo PDF, JPG, PNG)'
        ];
        continue;
    }

    // 2️⃣ Decodificar y guardar archivo
    $contenido_archivo = base64_decode($contenido_base64);
    
    if (strlen($contenido_archivo) > 5 * 1024 * 1024) {
        $errores[] = [
            'archivo' => $nombre_archivo,
            'motivo' => 'Archivo muy grande (máx. 5MB)'
        ];
        continue;
    }

    $carpeta_destino = __DIR__ . "/../_upload/vouchers/";
    
    if (!is_dir($carpeta_destino)) {
        mkdir($carpeta_destino, 0777, true);
    }

    $nombre_unico = "voucher_" . time() . "_" . uniqid() . "." . $extension;
    $ruta_destino = $carpeta_destino . $nombre_unico;

    if (!file_put_contents($ruta_destino, $contenido_archivo)) {
        $errores[] = [
            'archivo' => $nombre_archivo,
            'motivo' => 'No se pudo guardar el archivo en el servidor'
        ];
        continue;
    }

    // 3️⃣ Registrar en base de datos
    $resultado = SubirVoucherComprobante(
        $id_comprobante,
        $nombre_unico,
        $_SESSION['id_personal'],
        $enviar_proveedor,
        $enviar_contabilidad,
        $enviar_tesoreria,
        $enviar_compras,
        $fecha_voucher
    );

    if (strpos($resultado, 'SI|') === 0) {
        $exitosos++;
    } else {
        // Si falla el registro, eliminar archivo físico
        if (file_exists($ruta_destino)) {
            unlink($ruta_destino);
        }
        
        $errores[] = [
            'archivo' => $nombre_archivo,
            'motivo' => 'Error al guardar en base de datos: ' . $resultado
        ];
    }
}

// Devolver resultado final
echo json_encode([
    'success' => true,
    'exitosos' => $exitosos,
    'fallidos' => count($errores),
    'errores' => $errores
]);
?>