<?php
header('Content-Type: application/json; charset=utf-8');
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_compras.php");
require_once("../_modelo/m_proveedor.php");
require_once("../_modelo/m_detraccion.php");

$id_compra = isset($_POST['id_compra']) ? intval($_POST['id_compra']) : 0;

if (!$id_compra) {
    echo json_encode(['success' => false, 'message' => 'ID no válido']);
    exit;
}

// Verificar si tiene aprobaciones
include("../_conexion/conexion.php");
$sql_check = "SELECT 
                id_personal_aprueba_tecnica,
                id_personal_aprueba_financiera
              FROM compra 
              WHERE id_compra = $id_compra";
$resultado = mysqli_query($con, $sql_check);
$compra_check = mysqli_fetch_assoc($resultado);

if (!empty($compra_check['id_personal_aprueba_tecnica']) || 
    !empty($compra_check['id_personal_aprueba_financiera'])) {
    mysqli_close($con);
    echo json_encode([
        'success' => false, 
        'message' => 'No se puede editar una orden con aprobación iniciada'
    ]);
    exit;
}
mysqli_close($con);

// Obtener datos
$orden_data = ConsultarCompraPorId($id_compra);
$orden_detalle = ConsultarCompraDetalle($id_compra);
$proveedores = MostrarProveedores();
$detracciones = ObtenerDetracciones();

if (empty($orden_data)) {
    echo json_encode(['success' => false, 'message' => 'Orden no encontrada']);
    exit;
}

echo json_encode([
    'success' => true,
    'orden' => $orden_data[0],
    'detalles' => $orden_detalle,
    'proveedores' => $proveedores,
    'detracciones' => $detracciones
]);
exit;
?>