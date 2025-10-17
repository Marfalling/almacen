<?php
require_once("../_conexion/sesion.php");

header('Content-Type: application/json');

/* Verificar permiso
if (!verificarPermisoEspecifico('anular_salidas')) {
    echo json_encode(['success' => false, 'message' => 'No tienes permiso para anular salidas.']);
    exit;
}*/

if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de salida no recibido.']);
    exit;
}

$id_salida = intval($_POST['id']);

require_once("../_modelo/m_salidas.php");
require_once("../_modelo/m_auditoria.php");

$id_usuario = $_SESSION['id'] ?? 0;
$result = AnularSalida($id_salida, $id_usuario);

if ($result === "SI") {
    //GrabarAuditoria($id_usuario, $_SESSION['usuario_sesion'] ?? '', 'ANULACIÓN DE SALIDA', 'SALIDAS', "Anuló salida ID: $id_salida");
    echo json_encode(['success' => true, 'message' => 'La salida fue anulada correctamente.']);
} else {
    //GrabarAuditoria($id_usuario, $_SESSION['usuario_sesion'] ?? '', 'ERROR ANULAR SALIDA', 'SALIDAS', "Error en salida ID: $id_salida - $result");
    echo json_encode(['success' => false, 'message' => $result]);
}