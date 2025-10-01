<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_documentos.php"); 

$id_doc = isset($_POST['id_doc']) ? intval($_POST['id_doc']) : 0;

if ($id_doc > 0) {
    if (EliminarDocumento($id_doc)) {
        echo json_encode([
            "tipo_mensaje" => "success",
            "mensaje" => "Documento eliminado correctamente."
        ]);
    } else {
        echo json_encode([
            "tipo_mensaje" => "error",
            "mensaje" => "No se pudo eliminar el documento."
        ]);
    }
} else {
    echo json_encode([
        "tipo_mensaje" => "error",
        "mensaje" => "ID de documento no proporcionado."
    ]);
}
