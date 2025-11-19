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

// Obtener moneda
$moneda = isset($_GET['moneda']) ? intval($_GET['moneda']) : 0;

// Nombre dinámico según tipo de moneda
switch ($moneda) {
    case 1:
        $filename = "Reporte_Comprobantes_Soles.xls";
        break;
    case 2:
        $filename = "Reporte_Comprobantes_Dolares.xls";
        break;
    default:
        $filename = "Reporte_Comprobantes.xls";
        break;
}

/*
// Ejecutar consulta
$consulta = obtenerComprobantesEstado1($moneda);

// Pasar $filename a la vista
require('../_vista/v_excel_reporte.php');

// Actualizar registros
ActualizarComprobantesEstado($moneda);
*/

if ($moneda === 0) {
    
    // Obtener todos los comprobantes sin filtro
    $consulta = obtenerComprobantesGeneral();

    // Mostrar vista Excel
    require('../_vista/v_excel_reporte.php');

    // NO ejecutar ActualizarComprobantesEstado()

} else {

    // Obtener comprobantes filtrados por moneda
    $consulta = obtenerComprobantesEstado1($moneda);

    // Mostrar vista Excel
    require('../_vista/v_excel_reporte.php');

    // Actualizar estados
    ActualizarComprobantesEstado($moneda);
}

?>