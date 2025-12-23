<?php
header('Content-Type: application/json; charset=utf-8');
require_once("../_conexion/conexion.php");
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_pedidos.php");
require_once("../_modelo/m_compras.php");

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
                         c.id_personal_aprueba_financiera,
                         p.id_producto_tipo
                  FROM compra c 
                  INNER JOIN pedido p ON c.id_pedido = p.id_pedido
                  WHERE c.id_compra = ?";
    
    $stmt_check = $con->prepare($sql_check);
    
    if ($stmt_check === false) {
        throw new Exception("Error en la consulta SQL: " . $con->error);
    }
    
    $stmt_check->bind_param("i", $id_compra);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $compra_check = $result_check->fetch_assoc();
    $stmt_check->close();

    if (!$compra_check) {
        throw new Exception("Orden no encontrada");
    }

    // Verificar que no tenga aprobaciones (SOLO FINANCIERA)
    if (!empty($compra_check['id_personal_aprueba_financiera'])) {
        throw new Exception("No se puede editar una orden con aprobación iniciada");
    }

    // Verificar que esté en estado pendiente
    if ($compra_check['est_compra'] != 1) {
        throw new Exception("Solo se pueden editar órdenes en estado Pendiente");
    }

    $id_pedido = $compra_check['id_pedido'];
    
    //  DETECTAR TIPO DE ORDEN Y SI VIENE DE PEDIDO
    $es_orden_servicio = ($compra_check['id_producto_tipo'] == 2);
    $viene_de_pedido = ($id_pedido > 0);
    
    error_log("📋 Actualizando compra ID: $id_compra | Pedido: $id_pedido | Viene de pedido: " . ($viene_de_pedido ? 'SÍ' : 'NO'));

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
                
                if ($stmt_get === false) {
                    throw new Exception("Error al obtener producto: " . $con->error);
                }
                
                $stmt_get->bind_param("i", $id_detalle);
                $stmt_get->execute();
                $result_get = $stmt_get->get_result();
                $row_producto = $result_get->fetch_assoc();
                $stmt_get->close();

                if ($row_producto) {
                    $id_producto_eliminado = intval($row_producto['id_producto']);
                    $productos_afectados[] = $id_producto_eliminado;

                    //  ELIMINAR CENTROS DE COSTO DEL DETALLE (IGUAL QUE SALIDAS)
                    $sql_eliminar_cc = "DELETE FROM compra_detalle_centro_costo 
                                       WHERE id_compra_detalle = ?";
                    $stmt_eliminar_cc = $con->prepare($sql_eliminar_cc);
                    if ($stmt_eliminar_cc) {
                        $stmt_eliminar_cc->bind_param("i", $id_detalle);
                        $stmt_eliminar_cc->execute();
                        $stmt_eliminar_cc->close();
                        error_log("   🗑️ Centros de costo eliminados para detalle $id_detalle");
                    }

                    // Eliminar el detalle
                    $sql_eliminar = "DELETE FROM compra_detalle WHERE id_compra_detalle = ? AND id_compra = ?";
                    $stmt_eliminar = $con->prepare($sql_eliminar);
                    
                    if ($stmt_eliminar === false) {
                        throw new Exception("Error al eliminar detalle: " . $con->error);
                    }
                    
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

    //  PASO 3.5: VALIDAR CENTROS DE COSTO (SOLO SI NO VIENE DE PEDIDO - IGUAL QUE SALIDAS)
    if (!$viene_de_pedido) {
        error_log("⚠️ Compra NO viene de pedido - Validando centros de costo");
        
        $errores_centros = [];
        foreach ($items as $key => $item) {
            $centros = isset($item['centros_costo']) && is_array($item['centros_costo']) 
                       ? $item['centros_costo'] 
                       : [];
            
            if (empty($centros)) {
                $id_compra_detalle = isset($item['id_compra_detalle']) ? intval($item['id_compra_detalle']) : 0;
                if ($id_compra_detalle > 0) {
                    $sql_producto = "SELECT pr.nom_producto 
                                    FROM compra_detalle cd
                                    INNER JOIN producto pr ON cd.id_producto = pr.id_producto
                                    WHERE cd.id_compra_detalle = ?";
                    $stmt_prod = $con->prepare($sql_producto);
                    $stmt_prod->bind_param("i", $id_compra_detalle);
                    $stmt_prod->execute();
                    $result_prod = $stmt_prod->get_result();
                    $row_prod = $result_prod->fetch_assoc();
                    $stmt_prod->close();
                    
                    $nombre = $row_prod ? $row_prod['nom_producto'] : "Item #$key";
                    $errores_centros[] = "$nombre: Debe tener al menos un centro de costo";
                }
            }
        }
        
        if (!empty($errores_centros)) {
            echo json_encode([
                'success' => false,
                'message' => implode('<br>', $errores_centros),
                'tipo' => 'validacion'
            ]);
            exit;
        }
    } else {
        error_log("✅ Compra viene de pedido - No valida centros (se heredan automáticamente)");
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

    // 🔹 PASO 5: ACTUALIZAR LA ORDEN SEGÚN TIPO
    if ($es_orden_servicio) {
        // ORDEN DE SERVICIO
        $resultado = ActualizarOrdenServicio(
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
    } else {
        // ORDEN DE MATERIAL
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
    }

    if ($resultado != "SI") {
        // Detectar si es un error de validación de cantidades
        if (strpos($resultado, 'ERROR:') === 0) {
            // Es un error de validación, remover el prefijo "ERROR: "
            $mensaje_limpio = str_replace('ERROR: ', '', $resultado);
            
            echo json_encode([
                'success' => false,
                'message' => $mensaje_limpio,
                'tipo' => 'validacion' // Indicador de tipo de error
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

    //  PASO 5.5: SINCRONIZAR CENTROS DE COSTO (IGUAL QUE SALIDAS)
    error_log(" Sincronizando centros de costo para " . count($items_actualizar) . " items");
    
    foreach ($items_actualizar as $key => $item) {
        $id_compra_detalle = isset($item['id_compra_detalle']) ? intval($item['id_compra_detalle']) : 0;
        
        if ($id_compra_detalle <= 0) {
            error_log("   ⚠️ Item sin id_compra_detalle, saltando");
            continue;
        }
        
        // 🔹 OBTENER id_pedido_detalle
        $sql_pedido_detalle = "SELECT id_pedido_detalle 
                              FROM compra_detalle 
                              WHERE id_compra_detalle = ?";
        $stmt_pd = $con->prepare($sql_pedido_detalle);
        $stmt_pd->bind_param("i", $id_compra_detalle);
        $stmt_pd->execute();
        $result_pd = $stmt_pd->get_result();
        $row_pd = $result_pd->fetch_assoc();
        $stmt_pd->close();
        
        $id_pedido_detalle = $row_pd ? intval($row_pd['id_pedido_detalle']) : 0;
        
        error_log("   📝 Procesando detalle $id_compra_detalle | Pedido detalle: $id_pedido_detalle");
        
        if ($viene_de_pedido && $id_pedido_detalle > 0) {
            //  HEREDAR CENTROS DEL PEDIDO (IGUAL QUE SALIDAS)
            error_log("      🔄 Sincronizando centros desde pedido_detalle $id_pedido_detalle");
            
            // Paso 1: Eliminar centros actuales
            $sql_eliminar_cc = "DELETE FROM compra_detalle_centro_costo 
                              WHERE id_compra_detalle = ?";
            $stmt_del = $con->prepare($sql_eliminar_cc);
            $stmt_del->bind_param("i", $id_compra_detalle);
            $stmt_del->execute();
            $stmt_del->close();
            
            // Paso 2: Obtener centros del pedido
            $sql_centros_pedido = "SELECT id_centro_costo 
                                  FROM pedido_detalle_centro_costo 
                                  WHERE id_pedido_detalle = ?";
            
            $stmt_centros = $con->prepare($sql_centros_pedido);
            $stmt_centros->bind_param("i", $id_pedido_detalle);
            $stmt_centros->execute();
            $result_centros = $stmt_centros->get_result();
            
            $centros_sincronizados = 0;
            
            // Paso 3: Insertar centros del pedido
            while ($row_centro = $result_centros->fetch_assoc()) {
                $id_centro = intval($row_centro['id_centro_costo']);
                
                $sql_insert_cc = "INSERT INTO compra_detalle_centro_costo 
                                (id_compra_detalle, id_centro_costo) 
                                VALUES (?, ?)";
                
                $stmt_ins = $con->prepare($sql_insert_cc);
                $stmt_ins->bind_param("ii", $id_compra_detalle, $id_centro);
                $stmt_ins->execute();
                $stmt_ins->close();
                
                $centros_sincronizados++;
                error_log("         ✅ Centro $id_centro sincronizado");
            }
            
            $stmt_centros->close();
            
            error_log("      ✅ $centros_sincronizados centros sincronizados desde pedido");
            
        } else {
            // 🔹 USAR CENTROS DEL FORMULARIO (COMPRAS SIN PEDIDO)
            $centros_costo = isset($item['centros_costo']) && is_array($item['centros_costo']) 
                            ? $item['centros_costo'] 
                            : [];
            
            error_log("      🔄 Guardando centros del formulario: " . count($centros_costo) . " centros");
            
            // Eliminar centros existentes
            $sql_eliminar_cc = "DELETE FROM compra_detalle_centro_costo 
                              WHERE id_compra_detalle = ?";
            $stmt_del = $con->prepare($sql_eliminar_cc);
            $stmt_del->bind_param("i", $id_compra_detalle);
            $stmt_del->execute();
            $stmt_del->close();
            
            // Insertar nuevos centros
            foreach ($centros_costo as $id_centro) {
                $id_centro = intval($id_centro);
                if ($id_centro > 0) {
                    $sql_cc = "INSERT INTO compra_detalle_centro_costo 
                             (id_compra_detalle, id_centro_costo) 
                             VALUES (?, ?)";
                    $stmt_ins = $con->prepare($sql_cc);
                    $stmt_ins->bind_param("ii", $id_compra_detalle, $id_centro);
                    $stmt_ins->execute();
                    $stmt_ins->close();
                    
                    error_log("         ✅ Centro $id_centro guardado");
                }
            }
        }
    }

    // PASO 6: Verificar reapertura de items afectados
    $productos_afectados = array_unique($productos_afectados);
    foreach ($productos_afectados as $id_producto) {
        VerificarReaperturaItem($id_pedido, $id_producto);
    }

    //  MENSAJE SEGÚN TIPO
    $tipo_mensaje = $es_orden_servicio ? 'servicio' : 'compra';

    echo json_encode([
        'success' => true,
        'message' => "Orden de {$tipo_mensaje} actualizada exitosamente"
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