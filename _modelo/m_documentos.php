<?php
require_once("../_conexion/conexion.php");

// Guardar documento para cualquier entidad
function GuardarDocumento($entidad, $id_entidad, $nombre_archivo, $id_personal = null) {
    include("../_conexion/conexion.php");
    $entidad = mysqli_real_escape_string($con, $entidad);
    $id_entidad = intval($id_entidad);
    $nombre_archivo = mysqli_real_escape_string($con, $nombre_archivo);
    $id_personal_val = $id_personal ? intval($id_personal) : "NULL";

    $sql = "INSERT INTO documentos (entidad, id_entidad, documento, id_personal)
            VALUES ('$entidad', $id_entidad, '$nombre_archivo', $id_personal_val)";
    $res = mysqli_query($con, $sql);

    return $res;
}

// Mostrar documentos de una entidad
function MostrarDocumentos($entidad, $id_entidad) {
    include("../_conexion/conexion.php");


    $entidad = mysqli_real_escape_string($con, $entidad);
    $id_entidad = intval($id_entidad);

    $sql = "SELECT * 
            FROM documentos 
            WHERE entidad = '$entidad' AND id_entidad = $id_entidad 
            ORDER BY fec_subida DESC";
    $res = mysqli_query($con, $sql);

    $docs = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $docs[] = $row;
    }

    return $docs;
}

// Eliminar documento (incluye archivo físico)
function EliminarDocumento($id_doc) {
    include("../_conexion/conexion.php");


    $id_doc = intval($id_doc);

    // Buscar archivo físico antes de borrar
    $sql = "SELECT entidad, documento, id_entidad FROM documentos WHERE id_doc = $id_doc";
    $res = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($res);

    if ($row) {
        $archivo = "../uploads/" . $row['entidad'] . "/" . $row['documento'];
        if (file_exists($archivo)) {
            unlink($archivo); // Borrar archivo físico
        }
    }

    // Borrar de la BD
    $sql_del = "DELETE FROM documentos WHERE id_doc = $id_doc";
    $res_del = mysqli_query($con, $sql_del);

    return $res_del;
}