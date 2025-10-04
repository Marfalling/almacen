<?php
header('Content-Type: application/json; charset=utf-8');

require_once("../_conexion/sesion.php");
require_once("../_modelo/m_anulaciones.php");

if (!isset($id_personal) || empty($id_personal)) {
    echo json_encode([
        "tipo_mensaje" => "error",
        "mensaje" => "Sesión no válida."
    ]);
    exit;
}

$id_compra = isset($_POST['id_compra']) ? intval($_POST['id_compra']) : 0;
$id_pedido = isset($_POST['id_pedido']) ? intval($_POST['id_pedido']) : 0;

if ($id_compra > 0 && $id_pedido > 0) {
    $resultado = AnularCompraPedido($id_compra, $id_pedido, $id_personal);

    if ($resultado) {
        echo json_encode([
            "tipo_mensaje" => "success",
            "mensaje" => "Orden de compra y pedido anulados correctamente."
        ]);
    } else {
        echo json_encode([
            "tipo_mensaje" => "error",
            "mensaje" => "Error al anular."
        ]);
    }
} else {
    echo json_encode([
        "tipo_mensaje" => "error",
        "mensaje" => "Parámetros inválidos."
    ]);
}