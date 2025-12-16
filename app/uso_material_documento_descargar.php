<?php
if (!isset($_GET['archivo'])) {
    die('Archivo no especificado');
}

$nombre_archivo = basename($_GET['archivo']);
$ruta_archivo = "../_archivos/uso_material/" . $nombre_archivo;

if (!file_exists($ruta_archivo)) {
    die('Archivo no encontrado');
}

// Detectar tipo MIME
$extension = pathinfo($nombre_archivo, PATHINFO_EXTENSION);
$mime_types = [
    'pdf' => 'application/pdf',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'xls' => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
];

$content_type = isset($mime_types[$extension]) ? $mime_types[$extension] : 'application/octet-stream';

header('Content-Type: ' . $content_type);
header('Content-Disposition: inline; filename="' . $nombre_archivo . '"');
header('Content-Length: ' . filesize($ruta_archivo));
readfile($ruta_archivo);
exit;
?>