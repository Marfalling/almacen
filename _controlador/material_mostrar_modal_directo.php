<?php
require_once("../_conexion/sesion.php");
require_once("../_conexion/conexion.php");

// Configurar la respuesta JSON
header('Content-Type: application/json');

// Verificar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Recibir parámetros
$id_almacen = isset($_POST['id_almacen']) ? intval($_POST['id_almacen']) : 0;
$id_ubicacion = isset($_POST['id_ubicacion']) ? intval($_POST['id_ubicacion']) : 0;

// Parámetros de DataTables
$draw = intval($_POST['draw']);
$start = intval($_POST['start']);
$length = intval($_POST['length']);
$search_value = $_POST['search']['value'];

// Construir condiciones de búsqueda
$where_conditions = array();
$where_conditions[] = "p.est_producto = 1"; // Solo productos activos
$where_conditions[] = "mt.est_material_tipo = 1"; // Solo materiales activos
$where_conditions[] = "mt.nom_material_tipo = 'CONSUMIBLES'"; // Solo consumibles

if ($id_almacen > 0) {
    $where_conditions[] = "mov_stock.id_almacen = $id_almacen";
}

if ($id_ubicacion > 0) {
    $where_conditions[] = "mov_stock.id_ubicacion = $id_ubicacion";
}

// Construir condición de búsqueda
$search_condition = "";
if (!empty($search_value)) {
    $search_value_safe = mysqli_real_escape_string($con, $search_value);
    $search_condition = " AND (p.cod_material LIKE '%$search_value_safe%' 
                            OR p.nom_producto LIKE '%$search_value_safe%')";
}

// Consulta principal
$sql = "SELECT 
    p.id_producto,
    p.cod_material,
    p.nom_producto,
    mt.nom_material_tipo,
    um.nom_unidad_medida
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
            WHEN tipo_movimiento = 2 THEN -cant_movimiento
            ELSE 0
        END) AS stock_actual
    FROM movimiento 
    WHERE est_movimiento = 1
    GROUP BY id_producto, id_almacen, id_ubicacion
) mov_stock ON p.id_producto = mov_stock.id_producto
WHERE " . implode(" AND ", $where_conditions) . $search_condition . "
GROUP BY p.id_producto
ORDER BY p.nom_producto ASC";

// Ejecutar consulta
$result = mysqli_query($con, $sql);

// Verificar si la consulta tuvo éxito
if (!$result) {
    echo json_encode(['error' => 'Error en la consulta: ' . mysqli_error($con)]);
    exit;
}

// Contar registros totales
$total_records_sql = "SELECT COUNT(DISTINCT p.id_producto) as total 
FROM producto p
INNER JOIN material_tipo mt ON p.id_material_tipo = mt.id_material_tipo
LEFT JOIN (
    SELECT 
        id_producto,
        id_almacen,
        id_ubicacion,
        SUM(CASE
            WHEN tipo_movimiento = 1 THEN cant_movimiento
            WHEN tipo_movimiento = 2 THEN -cant_movimiento
            ELSE 0
        END) AS stock_actual
    FROM movimiento 
    WHERE est_movimiento = 1
    GROUP BY id_producto, id_almacen, id_ubicacion
) mov_stock ON p.id_producto = mov_stock.id_producto
WHERE " . implode(" AND ", $where_conditions);


$total_result = mysqli_query($con, $total_records_sql);
if (!$total_result) {
    echo json_encode(['error' => 'Error al contar registros: ' . mysqli_error($con)]);
    exit;
}
$total_records = mysqli_fetch_assoc($total_result)['total'];

$filtered_result = mysqli_query($con, "SELECT COUNT(*) as total FROM ($sql) as filtered");
if (!$filtered_result) {
    echo json_encode(['error' => 'Error al contar registros filtrados: ' . mysqli_error($con)]);
    exit;
}
$filtered_records = mysqli_fetch_assoc($filtered_result)['total'];
// Agregar ordenamiento y límites
$sql .= " LIMIT $start, $length";

// Ejecutar consulta principal de nuevo para obtener los datos
$result = mysqli_query($con, $sql);

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    // Botón de acción para seleccionar el producto
    $action_btn = '<button type="button" class="btn btn-primary btn-sm" 
                    onclick="seleccionarProducto(' . $row['id_producto'] . ', \'' . 
                    addslashes($row['nom_producto']) . '\', \'' . 
                    addslashes($row['nom_unidad_medida']) . '\', 0)">
                    <i class="fa fa-check"></i> Seleccionar
                  </button>';
    
    $data[] = array(
        $row['cod_material'],
        $row['nom_producto'],
        $row['nom_material_tipo'],
        $row['nom_unidad_medida'],
        'N/A', // No mostrar stock
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