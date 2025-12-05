<?php 
header('Content-Type: application/json');
//llamada a funciones
require_once("_modelo/m_reporte.php");

//recibir dede el app
$id_tipo_obra = $_GET['id_tipo_obra'];

$codigo = GenerarCodigo($id_tipo_obra);

//respuesta al app
echo $codigo;
?>