<?php
// Desactiva los errores (ni pantalla ni error_log)
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 0);

// llamada a funciones
require_once("_modelo/m_reporte.php");

// recibir datos del app
$id_usuario = isset($_POST['id_usuario']) ? $_POST['id_usuario'] : null;

$cantidad = 0;
if ($id_usuario !== null) {
    $cantidad = CantidadReporteUsuario($id_usuario);
}

if ($cantidad == 1) {
    $rpta = " reporte realizado";
} else {
    $rpta = " reportes realizados";
}

echo $cantidad . $rpta;
?>
