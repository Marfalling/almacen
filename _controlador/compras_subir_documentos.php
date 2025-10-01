<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_documentos.php"); 

// Recibimos datos
$entidad = isset($_POST['entidad']) ? trim($_POST['entidad']) : '';
$id_entidad = isset($_POST['id_entidad']) ? intval($_POST['id_entidad']) : 0;

if (!empty($entidad) && $id_entidad > 0 && isset($_FILES['documento'])) {
    // Carpeta de destino dinámica según entidad
    $target_dir = __DIR__ . "/../uploads/" . $entidad . "/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Generar nombre único del archivo
    $nombre_archivo = $entidad . "_" . $id_entidad . "_" . time() . "_" . basename($_FILES["documento"]["name"]);
    $target_file = $target_dir . $nombre_archivo;

    // Subir archivo físico
    if (move_uploaded_file($_FILES["documento"]["tmp_name"], $target_file)) {
        // Guardar en la BD
        if (GuardarDocumento($entidad, $id_entidad, $nombre_archivo, $_SESSION['id_personal'])) {
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