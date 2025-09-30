<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_compras.php");

$id_compra = isset($_POST['id_compra']) ? intval($_POST['id_compra']) : 0;

if ($id_compra > 0 && isset($_FILES['documento'])) {
    // Carpeta donde se guardan los archivos (en el servidor)
    $target_dir = __DIR__ . "/../uploads/compras/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Generar nombre único
    $nombre_archivo = "compra_" . $id_compra . "_" . time() . "_" . basename($_FILES["documento"]["name"]);
    $target_file = $target_dir . $nombre_archivo;

    // Guardar archivo físico
    if (move_uploaded_file($_FILES["documento"]["tmp_name"], $target_file)) {
        // Guardamos solo el nombre del archivo en la BD
        if (GuardarDocumentoCompra($id_compra, $nombre_archivo)) {
            echo json_encode([
                "tipo_mensaje" => "success",
                "mensaje" => "Documento cargado correctamente."
            ]);
        } else {
            echo json_encode([
                "tipo_mensaje" => "error",
                "mensaje" => "No se pudo registrar en la BD."
            ]);
        }
    } else {
        echo json_encode([
            "tipo_mensaje" => "error",
            "mensaje" => "Error al subir el archivo."
        ]);
    }
} else {
    echo json_encode([
        "tipo_mensaje" => "error",
        "mensaje" => "Datos incompletos."
    ]);
}