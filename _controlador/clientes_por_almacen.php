<?php
// ====================================================================
// CONTROLADOR: clientes_por_almacen.php
// Devuelve los clientes activos asociados a un almacén en JSON
// ====================================================================
header('Content-Type: application/json');

require_once("../_conexion/conexion.php"); // Conexión a arcealmacen
require_once("../_modelo/m_clientes.php");

// Obtener ID de almacén desde POST o GET
$id_almacen = isset($_POST['id_almacen']) ? intval($_POST['id_almacen']) : (isset($_GET['id_almacen']) ? intval($_GET['id_almacen']) : 0);

if ($id_almacen <= 0) {
    echo json_encode([]);
    exit;
}

// Llamar a la función
$clientes = MostrarClientesPorAlmacen($id_almacen);

// Devolver JSON
echo json_encode($clientes);
exit;
?>