<?php
header('Content-Type: application/json; charset=utf-8');
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_pedidos.php");

$id_pedido = isset($_POST['id_pedido']) ? intval($_POST['id_pedido']) : null;

if ($id_pedido) {
    // Llama a la función del modelo que aprobará el pedido técnicamente
    $resultado = AprobarPedidoTecnica($id_pedido, $id_personal);

    if ($resultado) {
        echo json_encode([
            "tipo_mensaje" => "success",
            "mensaje" => "Pedido aprobado técnicamente."
        ]);
    } else {
        echo json_encode([
            "tipo_mensaje" => "error",
            "mensaje" => "Error al aprobar técnicamente el pedido."
        ]);
    }
} else {
    echo json_encode([
        "tipo_mensaje" => "error",
        "mensaje" => "ID de pedido no proporcionado."
    ]);
}
exit;