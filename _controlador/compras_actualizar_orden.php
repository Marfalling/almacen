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
$items_eliminados = isset($_POST['items_eliminados']) ? $_POST['items_eliminados'] : '';

//  Capturar detracción, retención y percepción
$id_detraccion = null;
$id_retencion = null;
$id_percepcion = null;

if (isset($_POST['id_detraccion']) && !empty($_POST['id_detraccion'])) {
    $id_detraccion = intval($_POST['id_detraccion']);
}

if (isset($_POST['id_retencion']) && !empty($_POST['id_retencion'])) {
    $id_retencion = intval($_POST['id_retencion']);
}

if (isset($_POST['id_percepcion']) && !empty($_POST['id_percepcion'])) {
    $id_percepcion = intval($_POST['id_percepcion']);
}

if (!$id_compra || !$proveedor || !$moneda) {
    echo json_encode([
        'success' => false, 
        'message' => 'Complete todos los campos obligatorios'
    ]);
    exit;
}

try {
    // PASO 1: Verificar que la orden esté en estado válido para edición
    $sql_check = "SELECT c.est_compra, c.id_pedido,
                         c.id_personal_aprueba_tecnica,
                         c.id_personal_aprueba_financiera
                  FROM compra c 
                  WHERE c.id_compra = ?";
    $stmt_check = $con->prepare($sql_check);
    $stmt_check->bind_param("i", $id_compra);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $compra_check = $result_check->fetch_assoc();
    $stmt_check->close();

    if (!$compra_check) {
        throw new Exception("Orden no encontrada");
    }

    // Verificar que no tenga aprobaciones
    if (!empty($compra_check['id_personal_aprueba_tecnica']) || 
        !empty($compra_check['id_personal_aprueba_financiera'])) {
        throw new Exception("No se puede editar una orden con aprobación iniciada");
    }

    // Verificar que esté en estado pendiente
    if ($compra_check['est_compra'] != 1) {
        throw new Exception("Solo se pueden editar órdenes en estado Pendiente");
    }

    $id_pedido = $compra_check['id_pedido'];

    // PASO 2: Procesar items eliminados
    $productos_afectados = [];
    
    if (!empty($items_eliminados)) {
        $ids_eliminar = array_filter(array_map('trim', explode(',', $items_eliminados)));
        
        foreach ($ids_eliminar as $id_detalle) {
            $id_detalle = intval($id_detalle);
            if ($id_detalle > 0) {
                // Obtener producto antes de eliminar
                $sql_get_producto = "SELECT id_producto FROM compra_detalle WHERE id_compra_detalle = ?";
                $stmt_get = $con->prepare($sql_get_producto);
                $stmt_get->bind_param("i", $id_detalle);
                $stmt_get->execute();
                $result_get = $stmt_get->get_result();
                $row_producto = $result_get->fetch_assoc();
                $stmt_get->close();

                if ($row_producto) {
                    $id_producto_eliminado = intval($row_producto['id_producto']);
                    $productos_afectados[] = $id_producto_eliminado;

                    // Eliminar el detalle
                    $sql_eliminar = "DELETE FROM compra_detalle WHERE id_compra_detalle = ? AND id_compra = ?";
                    $stmt_eliminar = $con->prepare($sql_eliminar);
                    $stmt_eliminar->bind_param("ii", $id_detalle, $id_compra);
                    $stmt_eliminar->execute();
                    $stmt_eliminar->close();
                }
            }
        }
    }

    // PASO 3: Validar que queden items
    if (empty($items)) {
        throw new Exception("Debe mantener al menos un item en la orden");
    }

    // PASO 4: Preparar arrays para actualización
    $items_actualizar = [];
    $archivos_homologacion = [];

    foreach ($items as $key => $item) {
        if (!isset($item['es_nuevo']) || $item['es_nuevo'] != '1') {
            // Item existente
            $items_actualizar[$key] = $item;
        }
    }

    // Manejar archivos de homologación si vienen
    if (isset($_FILES['homologacion'])) {
        foreach ($_FILES['homologacion']['name'] as $key => $nombre) {
            if (!empty($nombre)) {
                $archivos_homologacion[$key] = [
                    'name' => $_FILES['homologacion']['name'][$key],
                    'type' => $_FILES['homologacion']['type'][$key],
                    'tmp_name' => $_FILES['homologacion']['tmp_name'][$key],
                    'error' => $_FILES['homologacion']['error'][$key],
                    'size' => $_FILES['homologacion']['size'][$key]
                ];
            }
        }
    }

    // PASO 5: Actualizar la orden usando la función del modelo
    $resultado = ActualizarOrdenCompra(
        $id_compra,
        $proveedor,
        $moneda,
        $observacion,
        $direccion,
        $plazo_entrega,
        $porte,
        $fecha_orden,
        $items_actualizar,
        $id_detraccion,
        $archivos_homologacion,
        $id_retencion,
        $id_percepcion
    );

    if ($resultado != "SI") {
        // Detectar si es un error de validación de cantidades
        if (strpos($resultado, 'ERROR:') === 0) {
            // Es un error de validación, remover el prefijo "ERROR: "
            $mensaje_limpio = str_replace('ERROR: ', '', $resultado);
            
            echo json_encode([
                'success' => false,
                'message' => $mensaje_limpio,
                'tipo' => 'validacion' //  Indicador de tipo de error
            ]);
        } else {
            // Es otro tipo de error
            echo json_encode([
                'success' => false,
                'message' => $resultado,
                'tipo' => 'sistema'
            ]);
        }
        exit;
    }

    // PASO 6: Verificar reapertura de items afectados
    $productos_afectados = array_unique($productos_afectados);
    foreach ($productos_afectados as $id_producto) {
        VerificarReaperturaItem($id_pedido, $id_producto);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Orden actualizada exitosamente'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'tipo' => 'sistema' 
    ]);
}

exit;
?>