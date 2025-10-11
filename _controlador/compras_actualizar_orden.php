<?php
header('Content-Type: application/json; charset=utf-8');
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_pedidos.php");

if (!isset($_POST['actualizar_orden_modal'])) {
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida']);
    exit;
}

$id_compra = isset($_POST['id_compra']) ? intval($_POST['id_compra']) : 0;
$proveedor = isset($_POST['proveedor_orden']) ? intval($_POST['proveedor_orden']) : 0;
$moneda = isset($_POST['moneda_orden']) ? intval($_POST['moneda_orden']) : 0;
$observacion = isset($_POST['observaciones_orden']) ? $_POST['observaciones_orden'] : '';
$direccion = isset($_POST['direccion_envio']) ? $_POST['direccion_envio'] : '';
$plazo_entrega = isset($_POST['plazo_entrega']) ? $_POST['plazo_entrega'] : '';
$porte = isset($_POST['tipo_porte']) ? $_POST['tipo_porte'] : '';
$fecha_orden = isset($_POST['fecha_orden']) ? $_POST['fecha_orden'] : date('Y-m-d');
$items = isset($_POST['items_orden']) ? $_POST['items_orden'] : [];
$id_detraccion = isset($_POST['id_detraccion']) ? intval($_POST['id_detraccion']) : null;

if (!$id_compra || !$proveedor || !$moneda || empty($items)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Complete todos los campos obligatorios'
    ]);
    exit;
}

$resultado = ActualizarOrdenCompra(
    $id_compra,
    $proveedor,
    $moneda,
    $observacion,
    $direccion,
    $plazo_entrega,
    $porte,
    $fecha_orden,
    $items,
    $id_detraccion
);

if ($resultado == "SI") {
    echo json_encode([
        'success' => true,
        'message' => 'Orden actualizada exitosamente'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error al actualizar: ' . $resultado
    ]);
}
exit;
?>