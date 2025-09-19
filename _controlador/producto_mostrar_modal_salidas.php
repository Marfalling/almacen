<?php
//=======================================================================
// CONTROLADOR: producto_mostrar_modal_salidas.php
//=======================================================================
header('Content-Type: application/json');
require_once("../_modelo/m_producto.php");

// Obtener parámetros
$id_almacen = isset($_POST['id_almacen']) ? intval($_POST['id_almacen']) : 0;
$id_ubicacion = isset($_POST['id_ubicacion']) ? intval($_POST['id_ubicacion']) : 0;
$tipoMaterial = isset($_POST['tipo_material']) ? intval($_POST['tipo_material']) : 0;

// Configuración para DataTables
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

// Obtener columna y dirección de ordenamiento
$columns = ['cod_material', 'nom_producto', 'nom_producto_tipo', 'nom_unidad_medida', 'mar_producto', 'mod_producto', 'stock_disponible'];
$orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 1;
$orderDirection = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';
$orderColumn = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'nom_producto';

// VALIDACIÓN: Excluir material tipo "NA" (id = 1)
if ($tipoMaterial == 1) {
    echo json_encode(array(
        "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => "No se pueden mostrar productos del tipo 'NA' para salidas. Este tipo está reservado para servicios."
    ));
    exit();
}

// Obtener datos del modelo con filtro de tipo de material
$productos = MostrarProductosConStock($length, $start, $searchValue, $orderColumn, $orderDirection, $id_almacen, $id_ubicacion, $tipoMaterial);
$totalRecords = NumeroRegistrosTotalProductosConStock($id_almacen, $id_ubicacion, $tipoMaterial);
$filteredRecords = NumeroRegistrosFiltradosProductosConStock($searchValue, $id_almacen, $id_ubicacion, $tipoMaterial);

// Formatear datos para DataTables
$data = array();
foreach ($productos as $producto) {
    $stock_disponible = floatval($producto['stock_disponible']);
    
    // VALIDACIÓN: Determinar si el botón debe estar habilitado o deshabilitado
    $botonClass = $stock_disponible > 0 ? 'btn-primary' : 'btn-secondary';
    $botonDisabled = $stock_disponible > 0 ? '' : 'disabled';
    $botonTexto = $stock_disponible > 0 ? 'Seleccionar' : 'Sin Stock';
    
    // Crear el botón con validaciones
    $botonHtml = '<button class="btn btn-sm ' . $botonClass . '" ' . $botonDisabled;
    if ($stock_disponible > 0) {
        $botonHtml .= ' onclick="seleccionarProducto(' . 
                      $producto['id_producto'] . ', \'' . 
                      addslashes($producto['nom_producto']) . '\', ' . 
                      $stock_disponible . ')"';
    } else {
        $botonHtml .= ' title="Este producto no tiene stock disponible en la ubicación seleccionada"';
    }
    $botonHtml .= '>' . $botonTexto . '</button>';
    
    $data[] = array(
        $producto['cod_material'] ?: 'N/A',
        $producto['nom_producto'],
        $producto['nom_producto_tipo'],
        $producto['nom_unidad_medida'],
        $producto['mar_producto'] ?: 'N/A',
        $producto['mod_producto'] ?: 'N/A',
        '<span class="' . ($stock_disponible > 0 ? 'text-success' : 'text-danger') . '">' . 
        number_format($stock_disponible, 2) . '</span>',
        $botonHtml
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