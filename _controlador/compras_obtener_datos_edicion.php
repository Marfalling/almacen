<?php
header('Content-Type: application/json; charset=utf-8');
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_compras.php");
require_once("../_modelo/m_proveedor.php");
require_once("../_modelo/m_detraccion.php");
require_once("../_modelo/m_centro_costo.php"); 

$id_compra = isset($_POST['id_compra']) ? intval($_POST['id_compra']) : 0;

if (!$id_compra) {
    echo json_encode(['success' => false, 'message' => 'ID no válido']);
    exit;
}

// Verificar si tiene aprobaciones
include("../_conexion/conexion.php");

$sql_check = "SELECT 
                id_personal_aprueba_financiera
              FROM compra 
              WHERE id_compra = ?";
              
$stmt_check = $con->prepare($sql_check);
$stmt_check->bind_param("i", $id_compra);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$compra_check = $result_check->fetch_assoc();
$stmt_check->close();

if (!empty($compra_check['id_personal_aprueba_financiera'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'No se puede editar una orden con aprobación iniciada'
    ]);
    exit;
}

// Obtener datos de la orden
$orden_data = ConsultarCompraPorId($id_compra);

if (empty($orden_data)) {
    echo json_encode(['success' => false, 'message' => 'Orden no encontrada']);
    exit;
}

// OBTENER EL TIPO DE PRODUCTO DEL PEDIDO
$sql_tipo = "SELECT p.id_producto_tipo 
             FROM pedido p
             INNER JOIN compra c ON c.id_pedido = p.id_pedido
             WHERE c.id_compra = ?";
             
$stmt_tipo = $con->prepare($sql_tipo);
$stmt_tipo->bind_param("i", $id_compra);
$stmt_tipo->execute();
$result_tipo = $stmt_tipo->get_result();
$row_tipo = $result_tipo->fetch_assoc();
$stmt_tipo->close();

if ($row_tipo) {
    $orden_data[0]['id_producto_tipo'] = $row_tipo['id_producto_tipo'];
} else {
    $orden_data[0]['id_producto_tipo'] = 1;
}

//  OBTENER DETALLES CON CENTROS DE COSTO (USA LA FUNCIÓN CORRECTA)
$orden_detalle = ConsultarCompraDetalleConCentros($id_compra);

error_log(" Cargando datos de compra ID: $id_compra");
error_log("   📋 Detalles cargados: " . count($orden_detalle));

//  LOG DE CENTROS CARGADOS
foreach ($orden_detalle as $detalle) {
    $num_centros = is_array($detalle['centros_costo']) ? count($detalle['centros_costo']) : 0;
    error_log("   📝 Detalle {$detalle['id_compra_detalle']}: {$num_centros} centros | Pedido detalle: {$detalle['id_pedido_detalle']}");
    if ($num_centros > 0) {
        error_log("      Centros: " . implode(', ', $detalle['centros_costo']));
    }
}

//  CARGAR CENTROS DE COSTO ACTIVOS PARA EL SELECT2
$centros_costo = MostrarCentrosCostoActivos();

error_log("    Centros de costo disponibles: " . count($centros_costo));

$proveedores = MostrarProveedores();
$detracciones = ObtenerDetracciones();

echo json_encode([
    'success' => true,
    'orden' => $orden_data[0],
    'detalles' => $orden_detalle,
    'proveedores' => $proveedores,
    'detracciones' => $detracciones,
    'centros_costo' => $centros_costo 
]);

mysqli_close($con);
exit;
?>