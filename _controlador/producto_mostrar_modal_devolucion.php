<?php
header('Content-Type: application/json');
require_once("../_modelo/m_producto.php");

// Obtener parámetros
$id_almacen = isset($_POST['id_almacen']) ? intval($_POST['id_almacen']) : 0;
$id_ubicacion = isset($_POST['id_ubicacion']) ? intval($_POST['id_ubicacion']) : 0;

// Configuración para DataTables
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

// Obtener columna y dirección de ordenamiento
$columns = ['cod_material', 'nom_producto', 'nom_producto_tipo', 'nom_unidad_medida', 'mar_producto', 'mod_producto', 'stock_disponible'];
$orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 1;
$orderDirection = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';
$orderColumn = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'nom_producto';

// Obtener datos del modelo (sin filtro de tipo de material)
$productos = MostrarProductosConStock($length, $start, $searchValue, $orderColumn, $orderDirection, $id_almacen, $id_ubicacion, 0);
$totalRecords = NumeroRegistrosTotalProductosConStock($id_almacen, $id_ubicacion, 0);
$filteredRecords = NumeroRegistrosFiltradosProductosConStock($searchValue, $id_almacen, $id_ubicacion, 0);

// Formatear datos para DataTables
$data = array();
foreach ($productos as $producto) {
    $data[] = array(
        $producto['cod_material'] ?: 'N/A',
        $producto['nom_producto'],
        $producto['nom_producto_tipo'],
        $producto['nom_unidad_medida'],
        $producto['mar_producto'] ?: 'N/A',
        $producto['mod_producto'] ?: 'N/A',
        number_format($producto['stock_disponible'], 2),
        '<button class="btn btn-sm btn-primary" onclick="seleccionarProducto(' . 
        $producto['id_producto'] . ', \'' . 
        addslashes($producto['nom_producto']) . '\', ' . 
        $producto['stock_disponible'] . ')">Seleccionar</button>'
    );
}

// Respuesta en formato JSON para DataTables
echo json_encode(array(
    "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $filteredRecords,
    "data" => $data
));
?>
