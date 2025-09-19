<?php
//=======================================================================
// CONTROLADOR: producto_mostrar_modal.php - UNIFICADO PARA NUEVO Y EDITAR
//=======================================================================
header('Content-Type: application/json');
require_once("../_modelo/m_producto.php");

// Obtener parámetros
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

// CORRECCIÓN UNIFICADA: Manejar ambos parámetros (tipo_producto y tipo_pedido)
$tipoProducto = 0;

// Primero verificar si viene desde pedido nuevo (usa 'tipo_pedido')
if (isset($_POST['tipo_pedido']) && !empty($_POST['tipo_pedido'])) {
    $tipoProducto = intval($_POST['tipo_pedido']);
}
// Luego verificar si viene desde pedido editar (usa 'tipo_producto')
elseif (isset($_POST['tipo_producto']) && !empty($_POST['tipo_producto'])) {
    $tipoProducto = intval($_POST['tipo_producto']);
}

// Obtener columna y dirección de ordenamiento
$columns = ['cod_material', 'nom_producto', 'nom_producto_tipo', 'nom_unidad_medida', 'mar_producto', 'mod_producto'];
$orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 1;
$orderDirection = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';
$orderColumn = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'nom_producto';

// Validar que se haya especificado un tipo de producto válido
if ($tipoProducto <= 0) {
    echo json_encode(array(
        "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => "Debe seleccionar un tipo de pedido válido antes de buscar productos"
    ));
    exit();
}

// CORRECCIÓN: Usar funciones específicas que filtren por tipo de producto
$productos = MostrarProductosModalFiltrado($length, $start, $searchValue, $orderColumn, $orderDirection, $tipoProducto);
$totalRecords = NumeroRegistrosTotalProductosModal($tipoProducto);
$filteredRecords = NumeroRegistrosFiltradosProductosModal($searchValue, $tipoProducto);

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
        '<button class="btn btn-sm btn-primary" onclick="seleccionarProducto(' . 
        $producto['id_producto'] . ', \'' . 
        addslashes($producto['nom_producto']) . '\', ' . 
        $producto['id_unidad_medida'] . ', \'' . 
        addslashes($producto['nom_unidad_medida']) . '\')">Seleccionar</button>'
    );
}

// Respuesta en formato JSON para DataTables
echo json_encode(array(
    "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $filteredRecords,
    "data" => $data,
    "debug" => array(
        "tipo_producto_recibido" => $tipoProducto,
        "total_productos" => count($productos)
    )
));
?>