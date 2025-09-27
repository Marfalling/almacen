<?php
header('Content-Type: application/json; charset=utf-8');

require_once("../_conexion/sesion.php");
require_once("../_modelo/m_compras.php");

$id_compra = isset($_POST['id_compra']) ? intval($_POST['id_compra']) : 0;
$id_pedido = isset($_POST['id_pedido']) ? intval($_POST['id_pedido']) : 0;

if ($id_compra > 0 && $id_pedido > 0) {
    $res1 = AnularCompra($id_compra, $id_personal);
    $res2 = AnularPedido($id_pedido, $id_personal);

    if ($res1 && $res2) {
        echo json_encode([
            "tipo_mensaje" => "success",
            "mensaje" => "Compra y Pedido anulados correctamente."
        ]);
    } else {
        echo json_encode([
            "tipo_mensaje" => "error",
            "mensaje" => "Error al anular compra o pedido."
        ]);
    }
} else {
    echo json_encode([
        "tipo_mensaje" => "error",
        "mensaje" => "Parámetros inválidos."
    ]);
}