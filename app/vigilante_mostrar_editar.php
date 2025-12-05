<?php
$id_reporte = $_GET['id_reporte'];
//llamada a funciones
require_once("_modelo/m_vigilante.php");
$vigilante = MostrarVigilante2($id_reporte);
//respuesta al app
echo json_encode($vigilante);
?>