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

// Construir la consulta base
$where_conditions = array();
$where_conditions[] = "p.est_producto = 1";
$where_conditions[] = "p.id_producto_tipo = 1"; // Solo materiales, no servicios

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
    COALESCE(stock_físico, 0) as stock_físico,
    COALESCE(stock_comprometido, 0) as stock_comprometido,
    (COALESCE(stock_físico, 0) - COALESCE(stock_comprometido, 0)) as stock_disponible
FROM producto p
INNER JOIN material_tipo mt ON p.id_material_tipo = mt.id_material_tipo
INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
LEFT JOIN (
    SELECT 
        id_producto,
        id_almacen,
        id_ubicacion,
        SUM(CASE
            WHEN tipo_movimiento = 1 AND tipo_orden != 3 THEN cant_movimiento
            WHEN tipo_movimiento = 2 THEN -cant_movimiento
            ELSE 0
        END) AS stock_físico,
        SUM(CASE
            WHEN tipo_movimiento = 2 AND tipo_orden = 5 AND est_movimiento = 1 THEN cant_movimiento
            ELSE 0
        END) AS stock_comprometido
    FROM movimiento 
    WHERE est_movimiento != 0
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
            WHEN tipo_movimiento = 1 AND tipo_orden != 3 THEN cant_movimiento
            WHEN tipo_movimiento = 2 THEN -cant_movimiento
            ELSE 0
        END) AS stock_físico,
        SUM(CASE
            WHEN tipo_movimiento = 2 AND tipo_orden = 5 AND est_movimiento = 1 THEN cant_movimiento
            ELSE 0
        END) AS stock_comprometido
    FROM movimiento 
    WHERE est_movimiento != 0
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
    $stock_fisico = floatval($row['stock_físico']);
    $stock_comprometido = floatval($row['stock_comprometido']);
    $stock_disponible = floatval($row['stock_disponible']);

    // ======= FORMATO DE STOCK CON COLOR =======
    if ($stock_disponible > 0) {
        $stock_disp_html = '<span class="text-success font-weight-bold">' . number_format($stock_disponible, 2) . '</span>';
    } else {
        $stock_disp_html = '<span class="text-danger">0.00</span>';
    }

    // ======= BOTÓN DE ACCIÓN =======
    if ($stock_disponible > 0) {
        $action_btn = '<button type="button" 
                        class="btn btn-sm btn-success d-inline-flex align-items-center gap-1"
                        onclick="seleccionarProducto(' . $row['id_producto'] . ', \'' . 
                        htmlspecialchars(addslashes($row['nom_producto']), ENT_QUOTES, 'UTF-8'). '\', \'' . 
                        htmlspecialchars(addslashes($row['nom_unidad_medida']), ENT_QUOTES, 'UTF-8') . '\', ' . 
                        $stock_disponible . ')"
                        title="Seleccionar producto">
                        <i class="fa fa-check"></i><span>Seleccionar</span>
                    </button>';
    } else {
        $action_btn = '<button type="button" class="btn btn-sm btn-secondary" disabled>
                        Sin Stock
                      </button>';
    }

    $data[] = array(
        htmlspecialchars($row['cod_material'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($row['nom_producto'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($row['nom_material_tipo'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($row['nom_unidad_medida'], ENT_QUOTES, 'UTF-8'),
        number_format($stock_fisico, 2),          // Físico
        number_format($stock_comprometido, 2),    // Comprometido
        $stock_disp_html,                         // Disponible con color
        $action_btn                               // Botón adaptativo
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