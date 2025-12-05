<?php 
header('Content-Type: application/json');
//llamada a funciones
require_once("_modelo/m_reporte.php");

//recibir dede el app
$id_reporte = $_POST['id_reporte'];

$reporte = MostrarReporteEvidenciaID($id_reporte);

//respuesta al app
echo json_encode($reporte);
?>