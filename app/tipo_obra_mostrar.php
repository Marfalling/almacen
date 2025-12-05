<?php 
//llamada a funciones
require_once("_modelo/m_tipo_obra.php");
$tipo_obra = MostrarTipoObra();

//respuesta al app
echo json_encode($tipo_obra);
?>