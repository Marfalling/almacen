<?php
header('Content-Type: application/json');

// Variables para paginación y búsqueda
$limit = isset($_POST['length']) ? $_POST['length'] : 10;
$offset = isset($_POST['start']) ? $_POST['start'] : 0;
$search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
$startIndex = $offset + 1;
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;

// NUEVO: Obtener el filtro de tipo de pedido
$tipoPedido = isset($_POST['tipo_pedido']) ? intval($_POST['tipo_pedido']) : 0;

// Obtener el nombre de la columna según el índice
$columns = ['cod_material', 'nom_producto', 'nom_producto_tipo', 'nom_unidad_medida', 'mar_producto', 'mod_producto', ''];

$orderColumnIndex = $_POST['order'][0]['column'];
$orderDirection = $_POST['order'][0]['dir'];
$orderColumn = $columns[$orderColumnIndex];

require_once("../_modelo/m_producto.php");

// Pasar el filtro de tipo de pedido a las funciones
$totalRecords = NumeroRegistrosTotalProductos($tipoPedido);
$data = MostrarProductoMejoradoModal($limit, $offset, $search, $orderColumn, $orderDirection, $startIndex, $tipoPedido);
$totalFiltered = NumeroRegistrosFiltradosModalProductos($search, $tipoPedido);

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