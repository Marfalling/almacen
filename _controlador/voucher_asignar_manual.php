<?php
require_once("../_conexion/sesion.php");
require_once("../_conexion/conexion.php");
require_once("../_modelo/m_comprobante.php");

header('Content-Type: application/json');

// Recibir datos del modal
$id_comprobante = isset($_POST['id_comprobante']) ? intval($_POST['id_comprobante']) : 0;
$archivo = isset($_POST['archivo']) ? mysqli_real_escape_string($con, $_POST['archivo']) : '';

// Recibir checks de envío
$enviar_proveedor = isset($_POST['enviar_proveedor']) && $_POST['enviar_proveedor'] == '1';
$enviar_contabilidad = isset($_POST['enviar_contabilidad']) && $_POST['enviar_contabilidad'] == '1';
$enviar_tesoreria = isset($_POST['enviar_tesoreria']) && $_POST['enviar_tesoreria'] == '1';

if ($id_comprobante <= 0 || empty($archivo)) {
    echo json_encode(['success' => false, 'mensaje' => 'Datos incompletos']);
    exit;
}

// Procesar asignación usando tu función existente
$fecha_voucher = date('Y-m-d');
$resultado = SubirVoucherComprobante(
    $id_comprobante,
    $archivo,
    $_SESSION['id_personal'],
    $enviar_proveedor,     // respetando checkbox
    $enviar_contabilidad,  // respetando checkbox
    $enviar_tesoreria,     // respetando checkbox
    $fecha_voucher
);

if (strpos($resultado, 'SI|') === 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'mensaje' => $resultado]);
}
?>