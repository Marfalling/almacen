<?php
header('Content-Type: application/json; charset=utf-8');

require_once("../_conexion/sesion.php");
require_once("../_modelo/m_anulaciones.php");

$id_compra = isset($_POST['id_compra']) ? intval($_POST['id_compra']) : 0;

if ($id_compra > 0) {
    $resultado = AnularCompra($id_compra, $id_personal);

    if ($resultado) {
        echo json_encode([
            "tipo_mensaje" => "success",
            "mensaje" => "Compra anulada exitosamente."
        ]);
    } else {
        echo json_encode([
            "tipo_mensaje" => "error",
            "mensaje" => "Error al anular la compra o ya está anulada."
        ]);
    }
} else {
    echo json_encode([
        "tipo_mensaje" => "error",
        "mensaje" => "ID de compra no válido."
    ]);
}