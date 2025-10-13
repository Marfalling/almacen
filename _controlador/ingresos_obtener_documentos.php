<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_documentos.php");

$id_compra = isset($_POST['id_compra']) ? intval($_POST['id_compra']) : 0;

if ($id_compra > 0) {
    $documentos = MostrarDocumentos('ingresos', $id_compra);
    
    echo json_encode([
        "success" => true,
        "documentos" => $documentos
    ]);
} else {
    echo json_encode([
        "success" => false,
        "documentos" => []
    ]);
}