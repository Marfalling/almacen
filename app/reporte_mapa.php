<?php 
header('Content-Type: application/json');

// llamada a funciones
require_once("_modelo/m_reporte.php");

// recibir desde el app, evitando warning si no se envía el parámetro
$id_usuario = isset($_REQUEST['id_usuario']) ? $_REQUEST['id_usuario'] : null;

// si no se recibe id_usuario, devolver array vacío
if (!$id_usuario) {
    echo json_encode([]); // sin error
    exit;
}

// llamar función solo si hay id_usuario
$reporte = MostrarReporteMapa($id_usuario);

// respuesta al app
echo json_encode($reporte);
?>
