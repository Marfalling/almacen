<?php 
header('Content-Type: application/json');
//llamada a funciones
require_once("_modelo/m_reporte.php");

//recibir dede el app
$id_usuario = $_REQUEST['id_usuario'];

$reporte = MostrarReporteUsuario($id_usuario);

//respuesta al app
echo json_encode($reporte);
?>