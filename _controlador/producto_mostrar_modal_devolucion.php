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
$productos = MostrarProductosConStockPorTipo($length, $start, $searchValue, $orderColumn, $orderDirection, $id_almacen, $id_ubicacion, 1);
$totalRecords = NumeroRegistrosTotalProductosConStockPorTipo($id_almacen, $id_ubicacion, 1);
$filteredRecords = NumeroRegistrosFiltradosProductosConStockPorTipo($searchValue, $id_almacen, $id_ubicacion, 1);

// -------------------------------------------------------
// ESTILO VISUAL: COLORES, BOTONES Y FORMATEO DE STOCK
// -------------------------------------------------------
$data = array();

foreach ($productos as $producto) {
    $stock = floatval($producto['stock_disponible']);
    
    // Formatear stock con color (verde si hay, rojo si no)
    if ($stock > 0) {
        $stock_formatted = '<span class="text-success font-weight-bold">' . number_format($stock, 2) . '</span>';
    } else {
        $stock_formatted = '<span class="text-danger">0.00</span>';
    }

    // Botón de selección
    if ($stock > 0) {
        $btnSeleccionar = '<button type="button"
                            class="btn btn-sm d-inline-flex align-items-center justify-content-center"
                            style="background-color:#3b82f6; color:white; width:32px; height:32px; border-radius:6px;"
                            onclick="seleccionarProducto(' . 
                                $producto['id_producto'] . ', \'' . 
                                addslashes($producto['nom_producto']) . '\', ' . 
                                $stock . ')"
                                data-toggle="tooltip"
                                data-placement="top"
                            title="Seleccionar producto">
                            <i class="fa fa-check"></i>
                        </button>';
    } else {
        $btnSeleccionar = '<button type="button" disabled
            class="btn btn-sm d-inline-flex align-items-center justify-content-center"
            style="background-color:#b0b0b0; color:white; width:32px; height:32px; border-radius:6px;"
            data-toggle="tooltip"
            data-placement="top"
            title="Sin stock disponible">
            <i class="fa fa-check"></i>
        </button>';
    }

    // Construir fila para DataTables
    $data[] = array(
        $producto['cod_material'] ?: 'N/A',
        $producto['nom_producto'],
        $producto['nom_producto_tipo'],
        $producto['nom_unidad_medida'],
        $producto['mar_producto'] ?: 'N/A',
        $producto['mod_producto'] ?: 'N/A',
        $stock_formatted,
        $btnSeleccionar
    );
}

// -------------------------------------------------------
// RESPUESTA JSON PARA DATATABLES
// -------------------------------------------------------
echo json_encode(array(
    "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $filteredRecords,
    "data" => $data
));
?>
