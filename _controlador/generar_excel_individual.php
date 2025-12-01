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
$id_compra= isset($_GET['compra']) ? intval($_GET['compra']) : 0;

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

if ($moneda != 0) {
    $consulta = obtenerComprobantesComEstado1($moneda, $id_compra);

    // Mostrar vista Excel
    require('../_vista/v_excel_reporte.php');

    // Actualizar estados
    ActualizarComprobantesComEstado($moneda, $id_compra);
}

?>