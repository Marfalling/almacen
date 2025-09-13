<?php
require_once("../_conexion/sesion.php");
require_once("../_conexion/conexion.php");

// Configurar la respuesta JSON
header('Content-Type: application/json');

// Verificar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'tipo_mensaje' => 'error',
        'mensaje' => 'Método no permitido'
    ]);
    exit;
}

// Recibir parámetros
$id_uso_material = isset($_POST['id_uso_material']) ? intval($_POST['id_uso_material']) : 0;

if ($id_uso_material <= 0) {
    echo json_encode([
        'tipo_mensaje' => 'error',
        'mensaje' => 'ID de uso de material no válido'
    ]);
    exit;
}

// Verificar que el uso de material existe y no está ya anulado
$sql_verificar = "SELECT usm.est_uso_material, usm.id_almacen, usm.id_ubicacion
                 FROM uso_material usm 
                 WHERE usm.id_uso_material = $id_uso_material";
$result_verificar = mysqli_query($con, $sql_verificar);

if (!$result_verificar || mysqli_num_rows($result_verificar) == 0) {
    echo json_encode([
        'tipo_mensaje' => 'error',
        'mensaje' => 'Uso de material no encontrado'
    ]);
    mysqli_close($con);
    exit;
}

$uso_data = mysqli_fetch_assoc($result_verificar);

if ($uso_data['est_uso_material'] == 0) {
    echo json_encode([
        'tipo_mensaje' => 'error',
        'mensaje' => 'El uso de material ya está anulado'
    ]);
    mysqli_close($con);
    exit;
}

// Iniciar transacción
mysqli_autocommit($con, false);

try {
    // Obtener detalles del uso de material para revertir stock
    $sql_detalles = "SELECT umd.id_producto, umd.cant_uso_material_detalle
                    FROM uso_material_detalle umd
                    WHERE umd.id_uso_material = $id_uso_material 
                    AND umd.est_uso_material_detalle = 1";
    
    $result_detalles = mysqli_query($con, $sql_detalles);
    
    if (!$result_detalles) {
        throw new Exception('Error al obtener detalles del uso de material');
    }
    
    // ✅ SOLUCIÓN: Marcar como inactivos los movimientos del uso de material en lugar de crear movimientos de reversión
    $sql_desactivar_movimientos = "UPDATE movimiento SET est_movimiento = 0 
                                  WHERE id_orden = $id_uso_material 
                                  AND tipo_orden = 4 
                                  AND est_movimiento = 1";
    
    if (!mysqli_query($con, $sql_desactivar_movimientos)) {
        throw new Exception('Error al desactivar movimientos del uso de material');
    }

    // Anular uso de material
    $sql_anular = "UPDATE uso_material SET est_uso_material = 0 WHERE id_uso_material = $id_uso_material";
    
    if (!mysqli_query($con, $sql_anular)) {
        throw new Exception('Error al anular el uso de material');
    }
    
    // Confirmar transacción
    mysqli_commit($con);
    
    echo json_encode([
        'tipo_mensaje' => 'success',
        'mensaje' => 'Uso de material anulado correctamente. El stock ha sido devuelto al almacén.'
    ]);

} catch (Exception $e) {
    // Revertir transacción
    mysqli_rollback($con);
    
    echo json_encode([
        'tipo_mensaje' => 'error',
        'mensaje' => $e->getMessage()
    ]);
}

// Restaurar autocommit
mysqli_autocommit($con, true);
mysqli_close($con);
?>