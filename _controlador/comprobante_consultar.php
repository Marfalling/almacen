<?php
// ====================================================================
// ENDPOINT AJAX: Consultar datos de un comprobante
// ====================================================================

header('Content-Type: application/json; charset=utf-8');

session_start();

if (!isset($_SESSION['id_personal'])) {
    echo json_encode(['error' => 'No tiene permisos para realizar esta acción.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método de solicitud no permitido.']);
    exit;
}

if (!isset($_POST['id_comprobante']) || empty($_POST['id_comprobante'])) {
    echo json_encode(['error' => 'No se especificó el comprobante a consultar.']);
    exit;
}

$id_comprobante = intval($_POST['id_comprobante']);

require_once("../_modelo/m_comprobante.php");

$comprobante = ConsultarComprobante($id_comprobante);

if (!$comprobante) {
    echo json_encode(['error' => 'Comprobante no encontrado.']);
    exit;
}

// Retornar datos en formato JSON
echo json_encode($comprobante);
?>