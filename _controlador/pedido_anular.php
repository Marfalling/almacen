<?php
//-----------------------------------------------------------------------
// CONTROLADOR: pedido_anular.php
//-----------------------------------------------------------------------
require_once('../_modelo/m_anulaciones.php');

header('Content-Type: application/json');

$id_pedido = $_POST['id_pedido'] ?? null;
$id_personal = $_POST['id_personal'] ?? null; // o puedes obtenerlo de $_SESSION si lo manejas ahí

if (!$id_pedido) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de pedido no recibido.'
    ]);
    exit;
}

$resultado = AnularPedido($id_pedido, $id_personal);

if ($resultado) {
    echo json_encode([
        'success' => true,
        'message' => 'Pedido anulado y stock liberado correctamente.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No se pudo anular el pedido (puede que ya esté anulado o haya ocurrido un error).'
    ]);
}
?>