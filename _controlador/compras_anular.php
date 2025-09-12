<?php
$_REQUEST['id_compra'] = isset($_REQUEST['id_compra']) ? $_REQUEST['id_compra'] : null;
if ($_REQUEST['id_compra']) {
    require_once("../_conexion/sesion.php");
    require_once("../_modelo/m_compras.php");

    $id_compra = intval($_REQUEST['id_compra']);
    $resultado = AnularCompra($id_compra, $id_personal);

    if ($resultado) {
        echo json_encode([
            "tipo_mensaje" => "success",
            "mensaje" => "Compra anulada exitosamente."
        ]);
    } else {
        echo json_encode([
            "tipo_mensaje" => "error",
            "mensaje" => "Error al anular la compra."
        ]);
    }
} else {
    echo json_encode([
        "tipo_mensaje" => "error",
        "mensaje" => "ID de compra no proporcionado."
    ]);
}
?>