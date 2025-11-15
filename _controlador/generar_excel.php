<?php
//INICIAR SESION
include("../_conexion/sesion.php");
//---------------------------------------------------------
//Llamar a MODELO
require_once('../_modelo/m_comprobante.php');
//---------------------------------------------------------
//$fd=$_GET['fd'];
//$fh=$_GET['fh'];
//---------------------------------------------------------
//$consulta = MostrarReporteGeneralFecha($fd,$fh);


$consulta = obtenerComprobantesEstado1();

//---------------------------------------------------------
require_once('../_vista/v_excel_reporte.php');

ActualizarComprobantesEstado();

?>