<?php 
//llamada a funciones
require_once("_modelo/m_turno.php");
$turno = MostrarTurno();

//respuesta al app
echo json_encode($turno);
?>