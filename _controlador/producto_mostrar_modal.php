<?php
header('Content-Type: application/json');

// Variables para paginación y búsqueda
$limit = isset($_POST['length']) ? $_POST['length'] : 10; // Número de registros por página
$offset = isset($_POST['start']) ? $_POST['start'] : 0;    // Desde qué registro empezar
$search = isset($_POST['search']['value']) ? $_POST['search']['value'] : ''; // Búsqueda global
$startIndex = $offset + 1;  // Esto asegura que empieza desde el valor correcto
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;

// Obtener el nombre de la columna según el índice
$columns = ['cod_material', 'nom_producto', 'nom_producto_tipo', 'nom_unidad_medida', 'mar_producto', 'mod_producto', '']; // Cambia según tus columnas

$orderColumnIndex = $_POST['order'][0]['column']; // Índice de la columna a ordenar
$orderDirection = $_POST['order'][0]['dir']; // Dirección de orden (asc/desc)
$orderColumn = $columns[$orderColumnIndex]; // Nombre de la columna según el índice

require_once("../_modelo/m_producto.php");
$totalRecords = NumeroRegistrosTotalProductos();
$data = MostrarProductoMejoradoModal($limit, $offset, $search, $orderColumn, $orderDirection, $startIndex);
$totalFiltered = NumeroRegistrosFiltradosModalProductos($search);

// Preparar la respuesta en formato JSON
$response = [
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalFiltered,
    "data" => $data
];

// Enviar la respuesta
echo json_encode($response);
?>