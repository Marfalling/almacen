<?php
header('Content-Type: application/json');
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_pago.php");

$id_pago = isset($_POST['id_pago']) ? intval($_POST['id_pago']) : 0;

if ($id_pago <= 0) {
    echo json_encode(['success' => false, 'message' => 'Pago inválido.']);
    exit;
}

$res = AnularPago($id_pago);

echo json_encode($res);
exit;