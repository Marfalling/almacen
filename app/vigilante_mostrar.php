<?php 
//llamada a funciones
require_once("_modelo/m_vigilante.php");
$vigilante = MostrarVigilante();

//respuesta al app
echo json_encode($vigilante);
?>