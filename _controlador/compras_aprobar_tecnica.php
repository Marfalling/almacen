<?php
header('Content-Type: application/json; charset=utf-8');
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_compras.php");

$id_compra = isset($_POST['id_compra']) ? intval($_POST['id_compra']) : null;

if ($id_compra) {
    $resultado = AprobarCompraTecnica($id_compra, $id_personal);

    if ($resultado) {
        echo json_encode([
            "tipo_mensaje" => "success",
            "mensaje" => "Compra aprobada técnicamente."
        ]);
    } else {
        echo json_encode([
            "tipo_mensaje" => "error",
            "mensaje" => "Error al aprobar técnicamente la compra."
        ]);
    }
} else {
    echo json_encode([
        "tipo_mensaje" => "error",
        "mensaje" => "ID de compra no proporcionado."
    ]);
}
exit;
