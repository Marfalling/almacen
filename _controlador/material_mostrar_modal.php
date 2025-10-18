<?php
require_once("../_conexion/sesion.php");
require_once("../_conexion/conexion.php");

// Configurar la respuesta JSON
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);

// Verificar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Recibir parámetros
$id_almacen = isset($_POST['id_almacen']) ? intval($_POST['id_almacen']) : 0;
$id_ubicacion = isset($_POST['id_ubicacion']) ? intval($_POST['id_ubicacion']) : 0;
$solo_con_stock = isset($_POST['solo_con_stock']) ? $_POST['solo_con_stock'] : false;

// Parámetros de DataTables
$draw = intval($_POST['draw']);
$start = intval($_POST['start']);
$length = intval($_POST['length']);
$search_value = $_POST['search']['value'];

// Construir la consulta base
$where_conditions = array();
$where_conditions[] = "p.est_producto = 1";
$where_conditions[] = "p.id_producto_tipo = 1"; // Solo materiales, no servicios

if ($id_almacen > 0) {
    $where_conditions[] = "mov_stock.id_almacen = $id_almacen";
}

if ($id_ubicacion > 0) {
    $where_conditions[] = "mov_stock.id_ubicacion = $id_ubicacion";
}

// Si se requiere solo productos con stock
if ($solo_con_stock) {
    $where_conditions[] = "stock_actual > 0";
}

// Construir condición de búsqueda
$search_condition = "";
if (!empty($search_value)) {
    $search_value_safe = mysqli_real_escape_string($con, $search_value);
    $search_condition = " AND (p.cod_material LIKE '%$search_value_safe%' 
                            OR p.nom_producto LIKE '%$search_value_safe%' 
                            OR mt.nom_material_tipo LIKE '%$search_value_safe%'
                            OR um.nom_unidad_medida LIKE '%$search_value_safe%')";
}

// Consulta principal con stock calculado
$sql = "SELECT 
    p.id_producto,
    p.cod_material,
    p.nom_producto,
    mt.nom_material_tipo,
    um.nom_unidad_medida,
    COALESCE(stock_actual, 0) as stock_actual,
    COALESCE(stock_reservado, 0) as stock_reservado,
    (COALESCE(stock_actual, 0) - COALESCE(stock_reservado, 0)) as stock_disponible
FROM producto p
INNER JOIN material_tipo mt ON p.id_material_tipo = mt.id_material_tipo
INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
LEFT JOIN (
    SELECT 
        id_producto,
        id_almacen,
        id_ubicacion,
        SUM(CASE
            WHEN tipo_movimiento = 1 THEN cant_movimiento
            WHEN tipo_movimiento = 2 AND tipo_orden != 5 THEN -cant_movimiento
            ELSE 0
        END) AS stock_actual,
        SUM(CASE
            WHEN tipo_movimiento = 2 AND tipo_orden = 5 AND est_movimiento = 1 THEN cant_movimiento
            ELSE 0
        END) AS stock_reservado
    FROM movimiento 
    WHERE est_movimiento = 1
    " . ($id_almacen > 0 ? " AND id_almacen = $id_almacen" : "") . "
    " . ($id_ubicacion > 0 ? " AND id_ubicacion = $id_ubicacion" : "") . "
    GROUP BY id_producto, id_almacen, id_ubicacion
) mov_stock ON p.id_producto = mov_stock.id_producto
WHERE " . implode(" AND ", $where_conditions) . $search_condition;

// Contar registros totales sin filtro
$total_records_sql = "SELECT COUNT(*) as total 
FROM producto p
INNER JOIN material_tipo mt ON p.id_material_tipo = mt.id_material_tipo
INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
LEFT JOIN (
    SELECT 
        id_producto,
        id_almacen,
        id_ubicacion,
        SUM(CASE
            WHEN tipo_movimiento = 1 THEN cant_movimiento
            WHEN tipo_movimiento = 2 AND tipo_orden != 5 THEN -cant_movimiento
            ELSE 0
        END) AS stock_actual,
        SUM(CASE
            WHEN tipo_movimiento = 2 AND tipo_orden = 5 AND est_movimiento = 1 THEN cant_movimiento
            ELSE 0
        END) AS stock_reservado
    FROM movimiento 
    WHERE est_movimiento = 1
    " . ($id_almacen > 0 ? " AND id_almacen = $id_almacen" : "") . "
    " . ($id_ubicacion > 0 ? " AND id_ubicacion = $id_ubicacion" : "") . "
    GROUP BY id_producto, id_almacen, id_ubicacion
) mov_stock ON p.id_producto = mov_stock.id_producto
WHERE " . implode(" AND ", $where_conditions);

$total_result = mysqli_query($con, $total_records_sql);
$total_records = mysqli_fetch_assoc($total_result)['total'];

// Contar registros filtrados
$filtered_sql = $sql;
$filtered_result = mysqli_query($con, "SELECT COUNT(*) as total FROM ($filtered_sql) as filtered");
$filtered_records = mysqli_fetch_assoc($filtered_result)['total'];

// Agregar ordenamiento y límites
$sql .= " ORDER BY p.nom_producto ASC LIMIT $start, $length";

// Ejecutar consulta principal
$result = mysqli_query($con, $sql);

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    // Botón de acción para seleccionar el producto
    $action_btn = '<button type="button" class="btn btn-primary btn-sm" 
                    onclick="seleccionarProducto(' . $row['id_producto'] . ', \'' . 
                    addslashes($row['nom_producto']) . '\', \'' . 
                    addslashes($row['nom_unidad_medida']) . '\', ' . 
                    $row['stock_disponible'] . ')">
                    <i class="fa fa-check"></i> Seleccionar
                  </button>';
    
    $data[] = array(
        $row['cod_material'],
        $row['nom_producto'],
        $row['nom_material_tipo'],
        $row['nom_unidad_medida'],
        number_format($row['stock_actual'], 2),       // Físico
        number_format($row['stock_reservado'], 2),    // Reservado
        number_format($row['stock_disponible'], 2),   // Disponible
        $action_btn
    );
}

// Preparar respuesta para DataTables
$response = array(
    "draw" => $draw,
    "recordsTotal" => $total_records,
    "recordsFiltered" => $filtered_records,
    "data" => $data
);

mysqli_close($con);
echo json_encode($response);
?>