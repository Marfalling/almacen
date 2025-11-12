<?php
require_once("../_conexion/sesion.php");
require_once("../_conexion/conexion.php");

// Configurar la respuesta JSON
header('Content-Type: application/json; charset=utf-8');
mysqli_set_charset($con, "utf8");

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
$where_conditions[] = "p.est_producto = 1";
$where_conditions[] = "mt.est_material_tipo = 1";
$where_conditions[] = "pt.nom_producto_tipo = 'MATERIAL'";
$where_conditions[] = "mt.nom_material_tipo IN ('CONSUMIBLES', 'HERRAMIENTAS', 'NA')";

// Construir condición de búsqueda
$search_condition = "";
if (!empty($search_value)) {
    $search_value_safe = mysqli_real_escape_string($con, $search_value);
    $search_condition = " AND (p.cod_material LIKE '%$search_value_safe%' 
                            OR p.nom_producto LIKE '%$search_value_safe%'
                            OR mt.nom_material_tipo LIKE '%$search_value_safe%')";
}

// Consulta principal CON CÁLCULO DE STOCK REAL
$sql = "SELECT 
    p.id_producto,
    p.cod_material,
    p.nom_producto,
    p.mar_producto,
    p.mod_producto,
    mt.nom_material_tipo,
    pt.nom_producto_tipo,
    um.nom_unidad_medida,
    COALESCE(
        (SELECT SUM(
            CASE 
                WHEN m.tipo_movimiento = 1 THEN m.cant_movimiento 
                WHEN m.tipo_movimiento = 2 THEN -m.cant_movimiento 
                ELSE 0 
            END
        )
        FROM movimiento m
        WHERE m.id_producto = p.id_producto
        AND m.id_almacen = $id_almacen
        AND m.id_ubicacion = $id_ubicacion
        AND m.est_movimiento = 1), 
    0) as stock_actual
FROM producto p
INNER JOIN material_tipo mt ON p.id_material_tipo = mt.id_material_tipo
INNER JOIN producto_tipo pt ON p.id_producto_tipo = pt.id_producto_tipo
INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
WHERE " . implode(" AND ", $where_conditions) . $search_condition . "
GROUP BY p.id_producto
ORDER BY p.nom_producto ASC";

// Ejecutar consulta
$result = mysqli_query($con, $sql);

// Verificar si la consulta tuvo éxito
if (!$result) {
    echo json_encode([
        'error' => 'Error en la consulta: ' . mysqli_error($con),
        'sql' => $sql
    ]);
    exit;
}

// Contar registros totales (sin filtro de búsqueda)
$total_records_sql = "SELECT COUNT(DISTINCT p.id_producto) as total 
FROM producto p
INNER JOIN material_tipo mt ON p.id_material_tipo = mt.id_material_tipo
INNER JOIN producto_tipo pt ON p.id_producto_tipo = pt.id_producto_tipo
WHERE " . implode(" AND ", $where_conditions);

$total_result = mysqli_query($con, $total_records_sql);
if (!$total_result) {
    echo json_encode(['error' => 'Error al contar registros: ' . mysqli_error($con)]);
    exit;
}
$total_records = mysqli_fetch_assoc($total_result)['total'];

// Contar registros filtrados (con búsqueda)
$filtered_sql = "SELECT COUNT(DISTINCT p.id_producto) as total 
FROM producto p
INNER JOIN material_tipo mt ON p.id_material_tipo = mt.id_material_tipo
INNER JOIN producto_tipo pt ON p.id_producto_tipo = pt.id_producto_tipo
WHERE " . implode(" AND ", $where_conditions) . $search_condition;

$filtered_result = mysqli_query($con, $filtered_sql);
if (!$filtered_result) {
    echo json_encode(['error' => 'Error al contar registros filtrados: ' . mysqli_error($con)]);
    exit;
}
$filtered_records = mysqli_fetch_assoc($filtered_result)['total'];

// Agregar límites para paginación
$sql .= " LIMIT $start, $length";

// Ejecutar consulta principal con límites
$result = mysqli_query($con, $sql);

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    // Construir descripción completa del producto
    $descripcion_completa = $row['nom_producto'];
    if (!empty($row['mar_producto'])) {
        $descripcion_completa .= ' - ' . $row['mar_producto'];
    }
    if (!empty($row['mod_producto'])) {
        $descripcion_completa .= ' (' . $row['mod_producto'] . ')';
    }
    
    // Stock actual (ya calculado en la consulta)
    $stock_actual = floatval($row['stock_actual']);
    
    // Botón de acción para seleccionar el producto
    $action_btn = '<button type="button" class="btn btn-primary btn-sm" 
                    onclick="seleccionarProducto(' . $row['id_producto'] . ', \'' . 
                    htmlspecialchars(addslashes($descripcion_completa), ENT_QUOTES, 'UTF-8') . '\', \'' . 
                    htmlspecialchars(addslashes($row['nom_unidad_medida']), ENT_QUOTES, 'UTF-8') . '\', ' . $stock_actual . ')">
                    <i class="fa fa-check"></i> Seleccionar
                </button>';
    
    $data[] = array(
        htmlspecialchars($row['cod_material'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($descripcion_completa),
        htmlspecialchars($row['nom_material_tipo'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($row['nom_unidad_medida'], ENT_QUOTES, 'UTF-8'),
        number_format($stock_actual, 2), 
        $action_btn
    );
}

// Preparar respuesta para DataTables
$response = array(
    "draw" => $draw,
    "recordsTotal" => intval($total_records),
    "recordsFiltered" => intval($filtered_records),
    "data" => $data
);

mysqli_close($con);
echo json_encode($response);
?>