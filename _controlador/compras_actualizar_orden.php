<?php
header('Content-Type: application/json; charset=utf-8');
require_once("../_conexion/conexion.php");
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_pedidos.php");

if (!isset($_POST['actualizar_orden_modal'])) {
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida']);
    exit;
}

$id_compra = isset($_POST['id_compra']) ? intval($_POST['id_compra']) : 0;
$proveedor = isset($_POST['proveedor_orden']) ? intval($_POST['proveedor_orden']) : 0;
$moneda = isset($_POST['moneda_orden']) ? intval($_POST['moneda_orden']) : 0;
$observacion = isset($_POST['observaciones_orden']) ? $_POST['observaciones_orden'] : '';
$direccion = isset($_POST['direccion_envio']) ? $_POST['direccion_envio'] : '';
$plazo_entrega = isset($_POST['plazo_entrega']) ? $_POST['plazo_entrega'] : '';
$porte = isset($_POST['tipo_porte']) ? $_POST['tipo_porte'] : '';
$fecha_orden = isset($_POST['fecha_orden']) ? $_POST['fecha_orden'] : date('Y-m-d');
$items = isset($_POST['items_orden']) ? $_POST['items_orden'] : [];
$id_detraccion = isset($_POST['id_detraccion']) ? intval($_POST['id_detraccion']) : null;
$items_eliminados = isset($_POST['items_eliminados']) ? $_POST['items_eliminados'] : '';

if (!$id_compra || !$proveedor || !$moneda) {
    echo json_encode([
        'success' => false, 
        'message' => 'Complete todos los campos obligatorios'
    ]);
    exit;
}

try {
    // PASO 1: OBTENER ID_PEDIDO
    $sql_get_pedido = "SELECT id_pedido FROM compra WHERE id_compra = ?";
    $stmt_pedido = $con->prepare($sql_get_pedido);
    $stmt_pedido->bind_param("i", $id_compra);
    $stmt_pedido->execute();
    $result_pedido = $stmt_pedido->get_result();
    $row_pedido = $result_pedido->fetch_assoc();
    $id_pedido = $row_pedido ? $row_pedido['id_pedido'] : 0;
    $stmt_pedido->close();

    if (!$id_pedido) {
        throw new Exception("No se encontró el pedido asociado");
    }

    // PASO 2: ELIMINAR ITEMS MARCADOS
    if (!empty($items_eliminados)) {
        $ids_eliminar = explode(',', $items_eliminados);
        
        foreach ($ids_eliminar as $id_detalle) {
            $id_detalle = intval(trim($id_detalle));
            if ($id_detalle > 0) {
                $sql_get_producto = "SELECT id_producto FROM compra_detalle WHERE id_compra_detalle = ?";
                $stmt_get = $con->prepare($sql_get_producto);
                $stmt_get->bind_param("i", $id_detalle);
                $stmt_get->execute();
                $result_get = $stmt_get->get_result();
                $row_producto = $result_get->fetch_assoc();
                $id_producto_eliminado = $row_producto ? $row_producto['id_producto'] : 0;
                $stmt_get->close();

                $sql_eliminar = "DELETE FROM compra_detalle WHERE id_compra_detalle = ? AND id_compra = ?";
                $stmt = $con->prepare($sql_eliminar);
                $stmt->bind_param("ii", $id_detalle, $id_compra);
                $stmt->execute();
                $stmt->close();

                if ($id_producto_eliminado > 0) {
                    $sql_liberar = "UPDATE pedido_detalle 
                                   SET est_pedido_detalle = 1 
                                   WHERE id_pedido = ? 
                                   AND id_producto = ? 
                                   AND est_pedido_detalle = 2";
                    $stmt_liberar = $con->prepare($sql_liberar);
                    $stmt_liberar->bind_param("ii", $id_pedido, $id_producto_eliminado);
                    $stmt_liberar->execute();
                    $stmt_liberar->close();
                }
            }
        }
    }

    if (empty($items)) {
        echo json_encode([
            'success' => false,
            'message' => 'Debe mantener al menos un item en la orden'
        ]);
        exit;
    }

    // PASO 3: PROCESAR ITEMS (EXISTENTES Y NUEVOS)
    foreach ($items as $key => $item) {
        $es_nuevo = isset($item['es_nuevo']) && $item['es_nuevo'] == '1';
        $precio_unitario = floatval($item['precio_unitario']);
        
        if ($es_nuevo) {
            // AGREGAR NUEVO ITEM
            $id_producto = intval($item['id_producto']);
            $cantidad = floatval($item['cantidad']);
            $id_pedido_detalle = intval($item['id_pedido_detalle']);
            
            $sql_insert = "INSERT INTO compra_detalle (
                              id_compra, id_producto, cant_compra_detalle, prec_compra_detalle, est_compra_detalle
                           ) VALUES (?, ?, ?, ?, 1)";
            $stmt_insert = $con->prepare($sql_insert);
            $stmt_insert->bind_param("iidd", $id_compra, $id_producto, $cantidad, $precio_unitario);
            $stmt_insert->execute();
            $stmt_insert->close();
            
            // Marcar pedido_detalle como cerrado (estado 2)
            $sql_cerrar = "UPDATE pedido_detalle 
                          SET est_pedido_detalle = 2 
                          WHERE id_pedido_detalle = ?";
            $stmt_cerrar = $con->prepare($sql_cerrar);
            $stmt_cerrar->bind_param("i", $id_pedido_detalle);
            $stmt_cerrar->execute();
            $stmt_cerrar->close();
            
        } else {
            // ACTUALIZAR ITEM EXISTENTE
            $id_compra_detalle = intval($key);
            
            $sql_update = "UPDATE compra_detalle 
                          SET prec_compra_detalle = ? 
                          WHERE id_compra_detalle = ?";
            $stmt_update = $con->prepare($sql_update);
            $stmt_update->bind_param("di", $precio_unitario, $id_compra_detalle);
            $stmt_update->execute();
            $stmt_update->close();
        }
    }

    // PASO 4: ACTUALIZAR LA ORDEN
    $resultado = ActualizarOrdenCompra(
        $id_compra,
        $proveedor,
        $moneda,
        $observacion,
        $direccion,
        $plazo_entrega,
        $porte,
        $fecha_orden,
        $items,
        $id_detraccion
    );

    if ($resultado == "SI") {
        echo json_encode([
            'success' => true,
            'message' => 'Orden actualizada exitosamente'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar: ' . $resultado
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

exit;
?>