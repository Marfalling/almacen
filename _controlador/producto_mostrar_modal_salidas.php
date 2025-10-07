<?php
//=======================================================================
// CONTROLADOR: producto_mostrar_modal_salidas.php
// Modal de productos con stock para salidas
//=======================================================================
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_salidas.php"); 

// Parámetros de DataTables
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

// Parámetros adicionales
$id_almacen = isset($_POST['id_almacen']) ? intval($_POST['id_almacen']) : 0;
$id_ubicacion = isset($_POST['id_ubicacion']) ? intval($_POST['id_ubicacion']) : 0;
$tipoMaterial = isset($_POST['tipoMaterial']) ? intval($_POST['tipoMaterial']) : 0;

// Ordenamiento
$orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDirection = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

// Mapeo de columnas para ordenamiento
$columns = ['cod_material', 'nom_producto', 'nom_producto_tipo', 'nom_unidad_medida', 'mar_producto', 'mod_producto', 'stock_disponible'];
$orderColumn = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'nom_producto';

// Validar que haya almacén y ubicación seleccionados
if ($id_almacen <= 0 || $id_ubicacion <= 0) {
    echo json_encode([
        "draw" => $draw,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => "Debe seleccionar un almacén y una ubicación"
    ]);
    exit;
}

// USAR LAS FUNCIONES RENOMBRADAS PARA SALIDAS
$totalRecords = NumeroRegistrosTotalProductosConStockParaSalida($id_almacen, $id_ubicacion, $tipoMaterial);
$filteredRecords = NumeroRegistrosFiltradosProductosConStockParaSalida($searchValue, $id_almacen, $id_ubicacion, $tipoMaterial);
$productos = MostrarProductosConStockParaSalida($length, $start, $searchValue, $orderColumn, $orderDirection, $id_almacen, $id_ubicacion, $tipoMaterial);

// Preparar datos para DataTables
$data = [];

foreach ($productos as $producto) {
    $stock = floatval($producto['stock_disponible']);
    
    // Formatear stock con color
    if ($stock > 0) {
        $stock_formatted = '<span class="text-success font-weight-bold">' . number_format($stock, 2) . '</span>';
    } else {
        $stock_formatted = '<span class="text-danger">0.00</span>';
    }
    
    // Botón de selección - Compatible con la función seleccionarProducto() de la vista
    if ($stock > 0) {
        $boton = '<button class="btn btn-sm btn-success" 
                    onclick="seleccionarProducto(' . $producto['id_producto'] . ', \'' . 
                    addslashes($producto['nom_producto']) . '\', ' . $stock . ')" 
                    title="Seleccionar producto">
                    <i class="fa fa-check"></i> Seleccionar
                  </button>';
    } else {
        $boton = '<button class="btn btn-sm btn-secondary" disabled 
                    title="Este producto no tiene stock disponible en la ubicación seleccionada">
                    Sin Stock
                  </button>';
    }
    
    $data[] = [
        $producto['cod_material'] ?: 'N/A',
        $producto['nom_producto'] ?: 'N/A',
        $producto['nom_producto_tipo'] ?: 'N/A',
        $producto['nom_unidad_medida'] ?: 'N/A',
        $producto['mar_producto'] ?: 'N/A',
        $producto['mod_producto'] ?: 'N/A',
        $stock_formatted,
        $boton
    ];
}

// Respuesta JSON para DataTables
$response = [
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $filteredRecords, 
    "data" => $data
];

header('Content-Type: application/json');
echo json_encode($response);
?>