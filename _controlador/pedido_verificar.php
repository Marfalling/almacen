<?php
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('ver_pedidos')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'PEDIDOS', 'VERIFICAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

// CARGAR MODELOS PRIMERO (antes de cualquier HTML)
require_once("../_modelo/m_pedidos.php");
require_once("../_modelo/m_obras.php");
require_once("../_modelo/m_proveedor.php");
require_once("../_modelo/m_moneda.php");
require_once("../_modelo/m_compras.php");
require_once("../_modelo/m_detraccion.php");
require_once("../_modelo/m_movimientos.php"); 
require_once("../_modelo/m_almacen.php");
require_once("../_modelo/m_ubicacion.php");
require_once("../_modelo/m_salidas.php");        

$id_pedido = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
$id_compra_editar = isset($_REQUEST['id_compra']) ? intval($_REQUEST['id_compra']) : 0;
$modo_editar = ($id_compra_editar > 0);

// PARA SALIDAS
$id_salida_editar = isset($_REQUEST['id_salida']) ? intval($_REQUEST['id_salida']) : 0;
$modo_editar_salida = ($id_salida_editar > 0);

$alerta = null;

// ============================================================================
// PROCESAR FORMULARIOS (ANTES DE CUALQUIER HTML)
// ============================================================================

// VERIFICAR ITEM (con validación de estado del pedido)
if (isset($_REQUEST['verificar_item'])) {
    $id_pedido_detalle = intval($_REQUEST['id_pedido_detalle']);
    $cant_os = floatval($_REQUEST['cantidad_para_os']);
    $cant_oc = floatval($_REQUEST['fin_cant_pedido_detalle']);
    $id_personal = $_SESSION['id_personal'] ?? 0;

    //  VALIDACIÓN: La cantidad debe ser mayor a 0
    if ($cant_os <= 0 && $cant_oc <= 0) {
        $alerta = [
            "icon" => "error",
            "title" => "Cantidad inválida",
            "text" => "La cantidad verificada debe ser mayor a 0"
        ];
    } else {
        // 1) Obtener detalle para saber a qué pedido pertenece
        $detalle = ConsultarDetallePorId($id_pedido_detalle);
        
        if (!$detalle) {
            $alerta = [
                "icon" => "error",
                "title" => "Error",
                "text" => "Detalle no encontrado."
            ];
        } else {
            $id_pedido_real = intval($detalle['id_pedido']);

            // 2) Obtener estado actual del pedido
            $pedido_check = ConsultarPedido($id_pedido_real);
            
            if (empty($pedido_check)) {
                $alerta = [
                    "icon" => "error",
                    "title" => "Error",
                    "text" => "Pedido no encontrado."
                ];
            } else {
                $pedido_row = $pedido_check[0];
                $estado_pedido = intval($pedido_row['est_pedido']);
                
                //  CORRECCIÓN: Permitir verificación en estados 1 (Pendiente) y 2 (Completado)
                // NO permitir en: 0 (Anulado), 3 (Aprobado), 4 (Ingresado), 5 (Finalizado)
                if ($estado_pedido == 0) {
                    $alerta = [
                        "icon" => "error",
                        "title" => "Pedido anulado",
                        "text" => "No se puede verificar items de un pedido anulado."
                    ];
                } elseif ($estado_pedido >= 3) {
                    $alerta = [
                        "icon" => "warning",
                        "title" => "Acción no permitida",
                        "text" => "No se puede verificar este item. El pedido ya fue aprobado o finalizado."
                    ];
                } else {
                    // 3) Proceder con la verificación (estados 1 o 2)
                    $rpta = verificarItem($id_pedido_detalle, $cant_oc, $cant_os);

                    if ($rpta == "SI") {
                        // ===============================================================
                        //  REGISTRO DE MOVIMIENTO tipo_orden = 5 (pedido / stock comprometido)
                        // ===============================================================
                        $id_producto   = intval($detalle['id_producto']);
                        $id_almacen    = intval($pedido_row['id_almacen']);
                        $id_ubicacion  = intval($pedido_row['id_ubicacion']);
                        //$cantidad_pedida = floatval($new_cant_fin);

                        //  Obtener stock actual (físico y disponible)
                        $stock = ObtenerStockProducto($id_producto, $id_almacen, $id_ubicacion);
                        $stock_disponible = floatval($stock['stock_disponible']);

                        // Evaluar cantidad a reservar
                        if ($stock_disponible >= $cantidad_pedida) {
                            $cantidad_reservar = $cantidad_pedida; // stock suficiente
                        } elseif ($stock_disponible > 0 && $stock_disponible < $cantidad_pedida) {
                            $cantidad_reservar = $stock_disponible; // stock parcial
                        } else {
                            $cantidad_reservar = 0; // sin stock
                        }

                        //  Registrar movimiento si hay algo que reservar
                       /* if ($cantidad_reservar > 0) {
                            RegistrarMovimientoPedido(
                                $id_pedido_real,
                                $id_producto,
                                $id_almacen,
                                $id_ubicacion,
                                $cantidad_reservar
                            );
                        }*/
                        // ===============================================================
                        //  Verificación exitosa
                        header("Location: pedido_verificar.php?id=$id_pedido_real&success=verificado");
                        exit;
                    } else {
                        $alerta = [
                            "icon" => "error",
                            "title" => "Error al verificar",
                            "text" => str_replace("ERROR: ", "", $rpta)
                        ];
                    }
                }
            }
        }
    }
}
// ============================================================================
// CREAR ORDEN (Detectar si es Material o Servicio)
// ============================================================================
if (isset($_REQUEST['crear_orden'])) {
    $id_pedido = $_REQUEST['id'];
    
    //  DETECTAR TIPO DE PEDIDO
    $pedido_info = ConsultarPedido($id_pedido);
    $es_orden_servicio = ($pedido_info[0]['id_producto_tipo'] == 2);
    
    $proveedor = $_REQUEST['proveedor_orden'];
    $moneda = $_REQUEST['moneda_orden'];
    $id_personal = $_SESSION['id_personal'];
    $observacion = $_REQUEST['observaciones_orden'];
    $direccion = $_REQUEST['direccion_envio'];
    $plazo_entrega = $_REQUEST['plazo_entrega'];
    $porte = $_REQUEST['tipo_porte'];
    $fecha_orden = $_REQUEST['fecha_orden'];
    $items = $_REQUEST['items_orden'];
    
    $id_detraccion = isset($_REQUEST['id_detraccion']) && !empty($_REQUEST['id_detraccion']) ? intval($_REQUEST['id_detraccion']) : null;
    $id_retencion = isset($_REQUEST['id_retencion']) && !empty($_REQUEST['id_retencion']) ? intval($_REQUEST['id_retencion']) : null;
    $id_percepcion = isset($_REQUEST['id_percepcion']) && !empty($_REQUEST['id_percepcion']) ? intval($_REQUEST['id_percepcion']) : null;
    
    // Manejar archivos
    $archivos_homologacion = [];
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
    
    //  VALIDACIONES BÁSICAS
    if (empty($proveedor) || empty($moneda) || empty($fecha_orden)) {
        echo "ERROR: Complete todos los campos obligatorios (Proveedor, Moneda y Fecha)";
        exit;
    } elseif (empty($items)) {
        echo "ERROR: Debe tener al menos un item en la orden";
        exit;
    }
    
    //  LLAMAR FUNCIÓN SEGÚN TIPO
    if ($es_orden_servicio) {
        $rpta = CrearOrdenServicio(
            $id_pedido, $proveedor, $moneda, $id_personal, 
            $observacion, $direccion, $plazo_entrega, $porte, 
            $fecha_orden, $items, $id_detraccion, $archivos_homologacion,
            $id_retencion, $id_percepcion
        );
    } else {
        $rpta = CrearOrdenCompra(
            $id_pedido, $proveedor, $moneda, $id_personal, 
            $observacion, $direccion, $plazo_entrega, $porte, 
            $fecha_orden, $items, $id_detraccion, $archivos_homologacion,
            $id_retencion, $id_percepcion
        );
    }
    
    //  CORRECCIÓN: SI HAY ERROR, DEVOLVERLO Y NO REDIRIGIR
    if ($rpta != "SI") {
        echo $rpta;
        exit;
    }
    
    //  SOLO DEVOLVER ÉXITO - EL FRONTEND REDIRIGIRÁ
    echo "SI";
    exit;
}

// ============================================================================
// ACTUALIZAR ORDEN (CORREGIDO)
// ============================================================================
if (isset($_REQUEST['actualizar_orden'])) {
    $id_compra = intval($_REQUEST['id_compra']);
    $id_pedido = intval($_REQUEST['id']);
    
    // DETECTAR TIPO DE PEDIDO
    $pedido_info = ConsultarPedido($id_pedido);
    $es_orden_servicio = ($pedido_info[0]['id_producto_tipo'] == 2);
    
    $proveedor_sel = $_REQUEST['proveedor_orden'];
    $moneda_sel = $_REQUEST['moneda_orden'];
    $observacion = $_REQUEST['observaciones_orden'];
    $direccion = $_REQUEST['direccion_envio'];
    $plazo_entrega = $_REQUEST['plazo_entrega'];
    $porte = $_REQUEST['tipo_porte'];
    $fecha_orden = $_REQUEST['fecha_orden'];
    $items = $_REQUEST['items_orden'] ?? [];
    
    $id_detraccion = isset($_REQUEST['id_detraccion']) && !empty($_REQUEST['id_detraccion']) ? intval($_REQUEST['id_detraccion']) : null;
    $id_retencion = isset($_REQUEST['id_retencion']) && !empty($_REQUEST['id_retencion']) ? intval($_REQUEST['id_retencion']) : null;
    $id_percepcion = isset($_REQUEST['id_percepcion']) && !empty($_REQUEST['id_percepcion']) ? intval($_REQUEST['id_percepcion']) : null;
    
    $archivos_homologacion = [];
    if (isset($_FILES['homologacion'])) {
        $archivos_homologacion = $_FILES['homologacion'];
    }
    
    // VALIDACIONES BÁSICAS
    if (empty($proveedor_sel) || empty($moneda_sel) || empty($fecha_orden)) {
        echo "ERROR: Complete todos los campos obligatorios (Proveedor, Moneda y Fecha)";
        exit;
    } elseif (empty($items)) {
        echo "ERROR: Debe tener al menos un item en la orden";
        exit;
    }
    
    include("../_conexion/conexion.php");
    
    // ========================================================================
    // 🔹 PASO 1: ELIMINAR FÍSICAMENTE LOS ITEMS MARCADOS
    // ========================================================================
    $items_eliminados = [];
    if (isset($_REQUEST['items_eliminados']) && !empty($_REQUEST['items_eliminados'])) {
        $items_eliminados = json_decode($_REQUEST['items_eliminados'], true);
        if (!is_array($items_eliminados)) {
            $items_eliminados = [];
        }
    }
    
    if (!empty($items_eliminados)) {
        error_log("🗑️ Items marcados para eliminar: " . print_r($items_eliminados, true));
        
        foreach ($items_eliminados as $id_compra_detalle_eliminar) {
            $id_compra_detalle_eliminar = intval($id_compra_detalle_eliminar);
            
            if ($id_compra_detalle_eliminar <= 0) continue;
            
            error_log("🗑️ Eliminando físicamente compra_detalle ID: $id_compra_detalle_eliminar");
            
            // Obtener id_pedido_detalle ANTES de eliminar
            $sql_get_detalle = "SELECT id_pedido_detalle, id_producto FROM compra_detalle 
                                WHERE id_compra_detalle = ?";
            $stmt = $con->prepare($sql_get_detalle);
            $stmt->bind_param("i", $id_compra_detalle_eliminar);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $id_pedido_detalle_afectado = intval($row['id_pedido_detalle']);
                
                // ELIMINAR FÍSICAMENTE
                $sql_eliminar = "DELETE FROM compra_detalle WHERE id_compra_detalle = ?";
                $stmt_eliminar = $con->prepare($sql_eliminar);
                $stmt_eliminar->bind_param("i", $id_compra_detalle_eliminar);
                
                if ($stmt_eliminar->execute()) {
                    error_log("✅ Item eliminado físicamente de la BD");
                    
                    // VERIFICAR REAPERTURA DEL DETALLE
                    if ($es_orden_servicio) {
                        VerificarReaperturaItemServicioPorDetalle($id_pedido_detalle_afectado);
                    } else {
                        VerificarEstadoItemPorDetalle($id_pedido_detalle_afectado);
                    }
                } else {
                    error_log("❌ Error al eliminar item: " . $stmt_eliminar->error);
                }
                
                $stmt_eliminar->close();
            }
            
            $stmt->close();
        }
    }
    
    // ========================================================================
    // 🔹 PASO 2: SEPARAR Y CORREGIR IDs DE ITEMS RESTANTES
    // ========================================================================
    $items_existentes = [];
    $items_nuevos = [];
    
    foreach ($items as $key => $item) {
        $es_nuevo = isset($item['es_nuevo']) && $item['es_nuevo'] == '1';
        
        // 🔹 ASEGURAR QUE TODOS TENGAN id_pedido_detalle
        $id_pedido_detalle = 0;
        
        if (isset($item['id_pedido_detalle']) && !empty($item['id_pedido_detalle'])) {
            $id_pedido_detalle = intval($item['id_pedido_detalle']);
        } elseif (isset($item['id_detalle']) && !empty($item['id_detalle'])) {
            $id_pedido_detalle = intval($item['id_detalle']);
        } else {
            // Si no tiene, buscar en compra_detalle
            if (!$es_nuevo && is_numeric($key)) {
                $id_compra_detalle = intval($key);
                $sql_buscar = "SELECT id_pedido_detalle FROM compra_detalle WHERE id_compra_detalle = ?";
                $stmt = $con->prepare($sql_buscar);
                $stmt->bind_param("i", $id_compra_detalle);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $id_pedido_detalle = intval($row['id_pedido_detalle']);
                }
                $stmt->close();
            }
        }
        
        // 🔹 ASEGURAR QUE TENGA AMBOS CAMPOS
        $item['id_detalle'] = $id_pedido_detalle;
        $item['id_pedido_detalle'] = $id_pedido_detalle;
        
        if ($es_nuevo) {
            $items_nuevos[] = [
                'id_pedido_detalle' => $id_pedido_detalle,
                'id_producto' => intval($item['id_producto']),
                'cantidad' => floatval($item['cantidad']),
                'precio_unitario' => floatval($item['precio_unitario']),
                'igv' => floatval($item['igv'])
            ];
        } else {
            $items_existentes[$key] = $item;
        }
    }
    
    // ========================================================================
    // 🔹 LOGS DE DEBUG
    // ========================================================================
    error_log("📋 ACTUALIZAR ORDEN - ID Compra: $id_compra");
    error_log("📦 Items Existentes: " . count($items_existentes));
    error_log("🆕 Items Nuevos: " . count($items_nuevos));
    
    foreach ($items_existentes as $k => $it) {
        error_log("   [Existente $k] id_detalle: " . ($it['id_detalle'] ?? 'NO TIENE') . " | id_pedido_detalle: " . ($it['id_pedido_detalle'] ?? 'NO TIENE'));
    }
    
    foreach ($items_nuevos as $idx => $nuevo) {
        error_log("   [Nuevo $idx] id_pedido_detalle: {$nuevo['id_pedido_detalle']} | cantidad: {$nuevo['cantidad']}");
    }
    
    // ========================================================================
    // 🔹 PASO 3: ACTUALIZAR SEGÚN TIPO
    // ========================================================================
    if ($es_orden_servicio) {
        $rpta = ActualizarOrdenServicio(
            $id_compra, $proveedor_sel, $moneda_sel,
            $observacion, $direccion, $plazo_entrega, $porte,
            $fecha_orden, $items_existentes, $id_detraccion,
            $archivos_homologacion, $id_retencion, $id_percepcion
        );
    } else {
        $rpta = ActualizarOrdenCompra(
            $id_compra, $proveedor_sel, $moneda_sel,
            $observacion, $direccion, $plazo_entrega, $porte,
            $fecha_orden, $items_existentes, $id_detraccion,
            $archivos_homologacion, $id_retencion, $id_percepcion
        );
    }
    
    if ($rpta != "SI") {
        echo $rpta;
        mysqli_close($con);
        exit;
    }
    
    // ========================================================================
    // INSERTAR ITEMS NUEVOS (CON VALIDACIÓN)
    // ========================================================================
    foreach ($items_nuevos as $nuevo_item) {
        $id_producto = $nuevo_item['id_producto'];
        $cantidad = $nuevo_item['cantidad'];
        $precio = $nuevo_item['precio_unitario'];
        $igv = $nuevo_item['igv'];
        $id_pedido_detalle = $nuevo_item['id_pedido_detalle'];
        
        // 🔹 VALIDAR ANTES DE INSERTAR
        $item_validar = [
            'nuevo-temp' => [
                'id_detalle' => $id_pedido_detalle,
                'id_pedido_detalle' => $id_pedido_detalle,
                'id_producto' => $id_producto,
                'cantidad' => $cantidad,
                'precio_unitario' => $precio,
                'igv' => $igv,
                'es_nuevo' => 1
            ]
        ];
        
        // 🔹 VALIDAR (pasando id_compra actual para excluirlo)
        if ($es_orden_servicio) {
            $errores_nuevo = ValidarCantidadesOrdenServicio($id_pedido, $item_validar, $id_compra);
        } else {
            $errores_nuevo = ValidarCantidadesOrden($id_pedido, $item_validar, $id_compra);
        }
        
        if (!empty($errores_nuevo)) {
            echo "ERROR: " . implode(". ", $errores_nuevo);
            mysqli_close($con);
            exit;
        }
        
        // 🔹 INSERTAR SI PASA LA VALIDACIÓN
        $nombre_archivo_hom = null;
        if (isset($archivos_homologacion[$id_pedido_detalle]) && !empty($archivos_homologacion[$id_pedido_detalle]['name'])) {
            $archivo = $archivos_homologacion[$id_pedido_detalle];
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $nombre_archivo_hom = "hom_compra_" . $id_compra . "_prod_" . $id_producto . "_" . uniqid() . "." . $extension;
            $ruta_destino = "../_archivos/homologaciones/" . $nombre_archivo_hom;
            
            if (!file_exists("../_archivos/homologaciones/")) {
                mkdir("../_archivos/homologaciones/", 0777, true);
            }
            
            move_uploaded_file($archivo['tmp_name'], $ruta_destino);
        }
        
        $hom_sql = $nombre_archivo_hom ? "'" . mysqli_real_escape_string($con, $nombre_archivo_hom) . "'" : "NULL";
        
        $sql_insert = "INSERT INTO compra_detalle (
                          id_compra, id_pedido_detalle, id_producto, 
                          cant_compra_detalle, prec_compra_detalle, 
                          igv_compra_detalle, hom_compra_detalle, est_compra_detalle
                       ) VALUES (?, ?, ?, ?, ?, ?, $hom_sql, 1)";
        $stmt = $con->prepare($sql_insert);
        $stmt->bind_param("iiiddd", $id_compra, $id_pedido_detalle, $id_producto, $cantidad, $precio, $igv);
        
        if (!$stmt->execute()) {
            error_log("❌ ERROR al insertar item nuevo: " . $stmt->error);
            echo "ERROR: No se pudo insertar el item nuevo";
            $stmt->close();
            mysqli_close($con);
            exit;
        }
        $stmt->close();
        
        error_log("✅ Item nuevo insertado - Detalle: $id_pedido_detalle | Cantidad: $cantidad");
        
        // 🔹 VERIFICAR CIERRE DEL DETALLE
        if ($es_orden_servicio) {
            $cant_ordenada_para_este_detalle = ObtenerCantidadYaOrdenadaServicioPorDetalle($id_pedido_detalle);
            
            $sql_get_original = "SELECT cant_pedido_detalle FROM pedido_detalle WHERE id_pedido_detalle = ?";
            $stmt_orig = $con->prepare($sql_get_original);
            $stmt_orig->bind_param("i", $id_pedido_detalle);
            $stmt_orig->execute();
            $res_original = $stmt_orig->get_result();
            $row_original = $res_original->fetch_assoc();
            $cant_original = $row_original ? floatval($row_original['cant_pedido_detalle']) : 0;
            $stmt_orig->close();
            
            if ($cant_ordenada_para_este_detalle >= $cant_original) {
                $sql_cerrar = "UPDATE pedido_detalle SET est_pedido_detalle = 2 WHERE id_pedido_detalle = ?";
                $stmt_cerrar = $con->prepare($sql_cerrar);
                $stmt_cerrar->bind_param("i", $id_pedido_detalle);
                $stmt_cerrar->execute();
                $stmt_cerrar->close();
                
                error_log("✅ Item servicio cerrado - ID: $id_pedido_detalle");
            }
        } else {
            VerificarEstadoItemPorDetalle($id_pedido_detalle);
        }
    }
    
    mysqli_close($con);
    
    echo "SI";
    exit;
}

// FINALIZAR VERIFICACIÓN
if (isset($_REQUEST['finalizar_verificacion'])) {
    $id_pedido = $_REQUEST['id'];
    
    $resultado = FinalizarPedido($id_pedido);
    
    if ($resultado['success']) {
        header("Location: pedidos_mostrar.php?success=finalizado");
        exit;
    } else {
        $alerta = [
            "icon" => $resultado['tipo'],
            "title" => "Error",
            "text" => $resultado['mensaje']
        ];
    }
}

// ============================================================================
// GRABAR SALIDA
// ============================================================================
if (isset($_REQUEST['crear_salida'])) {

    include("../_conexion/conexion.php");
    
    ob_clean();
    header('Content-Type: application/json');
    
    try {
        // Capturar datos del formulario
        $id_pedido = intval($_REQUEST['id_pedido']);
        $id_personal = $_SESSION['id_personal'] ?? 0;
        $ndoc_salida = mysqli_real_escape_string($con, trim($_REQUEST['ndoc_salida']));
        $fec_salida = mysqli_real_escape_string($con, $_REQUEST['fecha_salida']);
        $id_almacen_origen = intval($_REQUEST['almacen_origen_salida']);
        $id_ubicacion_origen = intval($_REQUEST['ubicacion_origen_salida']);
        $id_almacen_destino = intval($_REQUEST['almacen_destino_salida']);
        $id_ubicacion_destino = intval($_REQUEST['ubicacion_destino_salida']);
        $obs_salida = mysqli_real_escape_string($con, trim($_REQUEST['observaciones_salida']));
        
        // VALIDACIONES BÁSICAS
        if (empty($ndoc_salida)) {
            echo json_encode([
                'success' => false,
                'message' => 'El número de documento es obligatorio'
            ]);
            exit;
        }
        
        if (empty($fec_salida)) {
            echo json_encode([
                'success' => false,
                'message' => 'La fecha de salida es obligatoria'
            ]);
            exit;
        }
        
        if ($id_almacen_origen <= 0 || $id_ubicacion_origen <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Debe seleccionar almacén y ubicación de origen'
            ]);
            exit;
        }
        
        if ($id_almacen_destino <= 0 || $id_ubicacion_destino <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Debe seleccionar almacén y ubicación de destino'
            ]);
            exit;
        }
        
        // DECODIFICAR JSON DE MATERIALES
        $materiales = [];
        
        if (isset($_REQUEST['items_salida'])) {
            if (is_string($_REQUEST['items_salida'])) {
                $items_array = json_decode($_REQUEST['items_salida'], true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error al decodificar items: ' . json_last_error_msg()
                    ]);
                    exit;
                }
            } else {
                $items_array = $_REQUEST['items_salida'];
            }
            
            if (is_array($items_array)) {
                foreach ($items_array as $item) {
                    if (empty($item['id_producto']) || empty($item['cantidad'])) {
                        continue;
                    }
                    
                    $id_producto = intval($item['id_producto']);
                    $cantidad = floatval($item['cantidad']);
                    $descripcion = isset($item['descripcion']) ? trim($item['descripcion']) : '';
                    $id_pedido_detalle = isset($item['id_pedido_detalle']) && $item['id_pedido_detalle'] > 0
                                        ? intval($item['id_pedido_detalle']) 
                                        : 0;
                    
                    if ($cantidad <= 0) {
                        echo json_encode([
                            'success' => false,
                            'message' => "La cantidad para '{$descripcion}' debe ser mayor a 0"
                        ]);
                        exit;
                    }
                    
                    $materiales[] = [
                        'id_producto' => $id_producto,
                        'id_pedido_detalle' => $id_pedido_detalle,
                        'descripcion' => $descripcion,
                        'cantidad' => $cantidad
                    ];
                }
            }
        }
        
        if (empty($materiales)) {
            echo json_encode([
                'success' => false,
                'message' => 'Debe agregar al menos un material a la salida'
            ]);
            exit;
        }
        
        // LLAMAR A LA FUNCIÓN GRABAR SALIDA
        $resultado = GrabarSalida(
            2,
            $id_almacen_origen,
            $id_ubicacion_origen,
            $id_almacen_destino,
            $id_ubicacion_destino,
            $ndoc_salida,
            $fec_salida,
            $obs_salida,
            $id_personal,
            $id_personal,
            $id_personal,
            $materiales,
            $id_pedido
        );
        
        // PROCESAR RESULTADO
        if (is_array($resultado)) {
            if (isset($resultado['success']) && $resultado['success'] === true) {
                error_log("✅ Salida creada correctamente - ID: " . ($resultado['id_salida'] ?? 'N/A'));
                mysqli_close($con);
                echo "SI";
                exit;
            } else {
                $mensaje_error = isset($resultado['message']) ? $resultado['message'] : 'Error desconocido al crear la salida';
                error_log("❌ Error al crear salida: $mensaje_error");
                mysqli_close($con);
                echo "ERROR: " . $mensaje_error;
                exit;
            }
        } elseif (is_string($resultado)) {
            if (strpos($resultado, 'OK') === 0 || strpos($resultado, 'SI') === 0) {
                error_log("✅ Salida creada correctamente");
                mysqli_close($con);
                echo "SI";
                exit;
            } else {
                $mensaje_error = str_replace(['ERROR: ', 'ERROR DE VALIDACIÓN: '], '', $resultado);
                error_log("❌ Error al crear salida: $mensaje_error");
                mysqli_close($con);
                echo "ERROR: " . $mensaje_error;
                exit;
            }
        } else {
            error_log("❌ Error: Respuesta inesperada del sistema");
            mysqli_close($con);
            echo "ERROR: Respuesta inesperada del sistema";
            exit;
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error del sistema: ' . $e->getMessage()
        ]);
    }
    
    exit;
}

// ============================================================================
// ACTUALIZAR SALIDA (CORREGIDO - DECODIFICAR JSON)
// ============================================================================
if (isset($_REQUEST['actualizar_salida'])) {
    include("../_conexion/conexion.php");
    
    $id_salida = intval($_REQUEST['id_salida']);
    $id_pedido = intval($_REQUEST['id_pedido']);
    $ndoc_salida = mysqli_real_escape_string($con, trim($_REQUEST['ndoc_salida']));
    $fec_salida = mysqli_real_escape_string($con, $_REQUEST['fecha_salida']);
    $id_almacen_origen = intval($_REQUEST['almacen_origen_salida']);
    $id_ubicacion_origen = intval($_REQUEST['ubicacion_origen_salida']);
    $id_almacen_destino = intval($_REQUEST['almacen_destino_salida']);
    $id_ubicacion_destino = intval($_REQUEST['ubicacion_destino_salida']);
    $obs_salida = mysqli_real_escape_string($con, trim($_REQUEST['observaciones_salida']));
    $id_personal = $_SESSION['id_personal'] ?? 0;
    
    // VALIDACIONES BÁSICAS
    if (empty($ndoc_salida) || empty($fec_salida)) {
        echo "ERROR: Complete todos los campos obligatorios";
        mysqli_close($con);
        exit;
    }
    
    if ($id_almacen_origen <= 0 || $id_ubicacion_origen <= 0 || 
        $id_almacen_destino <= 0 || $id_ubicacion_destino <= 0) {
        echo "ERROR: Debe seleccionar almacenes y ubicaciones válidas";
        mysqli_close($con);
        exit;
    }
    
    // 🔹 CONSTRUIR ARRAY DE MATERIALES (CORREGIDO)
    $materiales = [];
    
    // 🔹 MÉTODO 1: Si viene como JSON string (desde el nuevo código JS)
    if (isset($_REQUEST['items_salida']) && is_string($_REQUEST['items_salida'])) {
        error_log("📦 Items recibidos como JSON string");
        
        $items_json = json_decode($_REQUEST['items_salida'], true);
        
        if (json_last_error() === JSON_ERROR_NONE && is_array($items_json)) {
            error_log("✅ JSON decodificado correctamente: " . count($items_json) . " items");
            
            foreach ($items_json as $item) {
                if (empty($item['id_producto']) || empty($item['cantidad'])) {
                    continue;
                }
                
                // 🔥 CRÍTICO: Buscar id_salida_detalle en la BD
                $id_producto = intval($item['id_producto']);
                $id_pedido_detalle = isset($item['id_pedido_detalle']) && $item['id_pedido_detalle'] > 0
                                    ? intval($item['id_pedido_detalle']) 
                                    : 0;
                
                // 🔹 BUSCAR id_salida_detalle EXISTENTE
                $sql_buscar = "SELECT id_salida_detalle FROM salida_detalle 
                              WHERE id_salida = ? AND id_producto = ?";
                
                if ($id_pedido_detalle > 0) {
                    $sql_buscar .= " AND id_pedido_detalle = ?";
                }
                
                $stmt = $con->prepare($sql_buscar);
                
                if ($id_pedido_detalle > 0) {
                    $stmt->bind_param("iii", $id_salida, $id_producto, $id_pedido_detalle);
                } else {
                    $stmt->bind_param("ii", $id_salida, $id_producto);
                }
                
                $stmt->execute();
                $result = $stmt->get_result();
                
                $id_salida_detalle = 0;
                if ($row = $result->fetch_assoc()) {
                    $id_salida_detalle = intval($row['id_salida_detalle']);
                }
                $stmt->close();
                
                $materiales[] = [
                    'id_salida_detalle' => $id_salida_detalle,
                    'id_producto' => $id_producto,
                    'descripcion' => isset($item['descripcion']) ? trim($item['descripcion']) : '',
                    'id_pedido_detalle' => $id_pedido_detalle,
                    'cantidad' => floatval($item['cantidad']),
                    'es_nuevo' => ($id_salida_detalle > 0) ? '0' : '1'
                ];
                
                error_log("   ✅ Item procesado: id_salida_detalle={$id_salida_detalle}, Producto={$id_producto}, Cantidad={$item['cantidad']}");
            }
        } else {
            error_log("❌ Error al decodificar JSON: " . json_last_error_msg());
        }
    }
    // 🔹 MÉTODO 2: Si viene como array (desde el código PHP antiguo)
    elseif (isset($_REQUEST['items_salida']) && is_array($_REQUEST['items_salida'])) {
        error_log("📦 Items recibidos como array PHP");
        
        foreach ($_REQUEST['items_salida'] as $key => $item) {
            // 🔥 CRÍTICO: Obtener id_salida_detalle del input hidden
            $id_salida_detalle = 0;
            
            if (isset($item['id_salida_detalle']) && intval($item['id_salida_detalle']) > 0) {
                $id_salida_detalle = intval($item['id_salida_detalle']);
            } elseif (is_numeric($key)) {
                $id_salida_detalle = intval($key);
            }
            
            // Verificar si es nuevo
            $es_nuevo = isset($item['es_nuevo']) && $item['es_nuevo'] == '1';
            
            // Validar datos mínimos
            if (empty($item['id_producto']) || empty($item['cantidad'])) {
                continue;
            }
            
            $id_producto = intval($item['id_producto']);
            $cantidad = floatval($item['cantidad']);
            $descripcion = isset($item['descripcion']) ? trim($item['descripcion']) : '';
            $id_pedido_detalle = isset($item['id_pedido_detalle']) && $item['id_pedido_detalle'] > 0 
                                ? intval($item['id_pedido_detalle']) 
                                : 0;
            
            // Validar cantidad
            if ($cantidad <= 0) {
                echo "ERROR: Todas las cantidades deben ser mayores a 0";
                mysqli_close($con);
                exit;
            }
            
            // ✅ AGREGAR AL ARRAY
            $materiales[] = [
                'id_salida_detalle' => $id_salida_detalle,
                'id_producto' => $id_producto,
                'descripcion' => $descripcion,
                'id_pedido_detalle' => $id_pedido_detalle,
                'cantidad' => $cantidad,
                'es_nuevo' => $es_nuevo ? '1' : '0'
            ];
            
            error_log("✅ Material procesado - ID Detalle: $id_salida_detalle | Cantidad: $cantidad | Es nuevo: " . ($es_nuevo ? 'SÍ' : 'NO'));
        }
    }
    
    // VALIDAR QUE HAYA AL MENOS UN MATERIAL
    if (empty($materiales)) {
        error_log("❌ Array de materiales está vacío");
        echo "ERROR: Debe agregar al menos un material a la salida";
        mysqli_close($con);
        exit;
    }
    
    // LOG DE DEBUG
    error_log("📦 Actualizando salida ID: $id_salida");
    error_log("📋 Materiales a actualizar: " . count($materiales));
    error_log("📄 Materiales: " . print_r($materiales, true));
    
    // LLAMAR FUNCIÓN DEL MODELO
    $resultado = ActualizarSalida(
        $id_salida,
        $id_almacen_origen,
        $id_ubicacion_origen,
        $id_almacen_destino,
        $id_ubicacion_destino,
        $ndoc_salida,
        $fec_salida,
        $obs_salida,
        $id_personal,
        $id_personal,
        $materiales
    );
    
    // PROCESAR RESULTADO
    mysqli_close($con);
    
    if (strpos($resultado, 'OK') === 0 || strpos($resultado, 'SI') === 0) {
        echo "SI";
    } else {
        echo $resultado;
    }
    
    exit;
}




// ============================================================================
// OBTENER STOCK ACTUALIZADO (cuando cambia ubicación origen)
// ============================================================================
if (isset($_REQUEST['obtener_stock_actualizado'])) {
    
    $id_ubicacion_origen = intval($_REQUEST['id_ubicacion_origen']);
    $id_almacen_origen = intval($_REQUEST['id_almacen_origen']);
    $id_pedido = intval($_REQUEST['id_pedido']);
    
    // Obtener todos los productos del pedido con su stock en la nueva ubicación
    $sql_productos = "SELECT pd.id_pedido_detalle,
                             pd.id_producto,
                             pd.cant_pedido_detalle,
                             p.nom_producto,
                             p.unid_producto,
                             COALESCE(SUM(sd.cant_salida_detalle), 0) as cantidad_enviada
                      FROM pedido_detalle pd
                      INNER JOIN producto p ON pd.id_producto = p.id_producto
                      LEFT JOIN salida_detalle sd ON pd.id_pedido_detalle = sd.id_pedido_detalle
                      LEFT JOIN salida s ON sd.id_salida = s.id_salida AND s.est_salida = 1
                      WHERE pd.id_pedido = $id_pedido
                        AND pd.est_pedido_detalle = 1
                      GROUP BY pd.id_pedido_detalle";
    
    $res = mysqli_query($con, $sql_productos);
    $productos_stock = [];
    
    while ($row = mysqli_fetch_assoc($res)) {
        $id_producto = $row['id_producto'];
        
        // Obtener stock en la ubicación seleccionada
        $stock = ObtenerStockProducto($id_producto, $id_almacen_origen, $id_ubicacion_origen, $id_pedido);
        
        $cantidad_pedida = floatval($row['cant_pedido_detalle']);
        $cantidad_enviada = floatval($row['cantidad_enviada']);
        $cantidad_pendiente = $cantidad_pedida - $cantidad_enviada;
        
        $productos_stock[] = [
            'id_producto' => $id_producto,
            'id_pedido_detalle' => $row['id_pedido_detalle'],
            'nom_producto' => $row['nom_producto'],
            'unid_producto' => $row['unid_producto'],
            'cantidad_pedida' => $cantidad_pedida,
            'cantidad_enviada' => $cantidad_enviada,
            'cantidad_pendiente' => $cantidad_pendiente,
            'stock_fisico' => floatval($stock['stock_fisico']),
            'stock_disponible' => floatval($stock['stock_disponible'])
        ];
    }
    
    echo json_encode([
        'success' => true,
        'productos' => $productos_stock
    ]);
    
    mysqli_close($con);
    exit;
}

// MOSTRAR ALERTAS DE SUCCESS (después del redirect)
if (isset($_GET['success'])) {
    switch($_GET['success']) {
        case 'verificado':
            $alerta = [
                "icon" => "success",
                "title" => "¡Éxito!",
                "text" => "Item verificado correctamente",
                "timer" => 1500
            ];
            break;
        case 'creado':
            $alerta = [
                "icon" => "success",
                "title" => "¡Orden Creada!",
                "text" => "La orden de compra se ha creado exitosamente",
                "timer" => 2000
            ];
            break;
        case 'actualizado':
            $alerta = [
                "icon" => "success",
                "title" => "¡Orden Actualizada!",
                "text" => "La orden de compra se ha actualizado exitosamente",
                "timer" => 2000
            ];
            break;
        // ============================================
        // ALERTAS DE SALIDAS
        // ============================================
        case 'salida_creada':
            $alerta = [
                "icon" => "success",
                "title" => "¡Salida Generada!",
                "text" => "La salida de materiales se ha registrado correctamente",
                "timer" => 2000
            ];
            break;
        case 'salida_actualizada':
            $alerta = [
                "icon" => "success",
                "title" => "¡Salida Actualizada!",
                "text" => "Los datos de la salida se han actualizado correctamente",
                "timer" => 2000
            ];
            break;
        case 'salida_anulada':
            $alerta = [
                "icon" => "success",
                "title" => "¡Salida Anulada!",
                "text" => "La salida ha sido anulada correctamente",
                "timer" => 2000
            ];
            break;
    }
}

// ============================================================================
// CARGAR DATOS DEL PEDIDO
// ============================================================================

$tiene_salida_activa = false; // Inicializar variable

if ($id_pedido > 0) {
    $pedido_data = ConsultarPedido($id_pedido);
    
    if (empty($pedido_data)) {
        $pedido_data = ConsultarPedidoAnulado($id_pedido);
    }
    
    if (!empty($pedido_data)) {
        $pedido_detalle = ConsultarPedidoDetalle($id_pedido);
        $pedido_compra = ConsultarCompra($id_pedido);
        $pedido_salidas = ConsultarSalidasPorPedido($id_pedido); //nuevoo 
        $proveedor = MostrarProveedores();
        $moneda = MostrarMoneda();
        $obras = MostrarObras();
        
        $almacenes = MostrarAlmacenesActivos();
        $ubicaciones = MostrarUbicacionesActivas();
        
        $tiene_salida_activa = TieneSalidaActivaPedido($id_pedido);

        // ✅ CARGAR DATOS DE ORDEN SI ESTÁ EN MODO EDICIÓN
        $orden_data = null;
        $orden_detalle = null;
        if ($modo_editar) {
            $orden_data = ObtenerOrdenPorId($id_compra_editar);
            $orden_detalle = ObtenerDetalleOrden($id_compra_editar);
        }

        // ✅ CARGAR DATOS DE SALIDA SI ESTÁ EN MODO EDICIÓN
        $salida_data = null;
        $salida_detalle = null;
        if ($modo_editar_salida) {
            // Validar que la salida existe
            $validacion = ValidarSalidaExiste($id_salida_editar);
            
            if (!$validacion['existe']) {
                header("Location: pedido_verificar.php?id=$id_pedido&error=salida_no_encontrada");
                exit;
            }
            
            if (!$validacion['activa']) {
                header("Location: pedido_verificar.php?id=$id_pedido&error=salida_anulada");
                exit;
            }
            
            // Verificar que la salida pertenece al pedido actual
            $id_pedido_salida = ObtenerPedidoDeSalida($id_salida_editar);
            if ($id_pedido_salida != $id_pedido) {
                header("Location: pedido_verificar.php?id=$id_pedido&error=salida_no_pertenece");
                exit;
            }
            
            // Obtener datos de la salida
            $salida_data = ObtenerSalidaPorId($id_salida_editar);
            
            if (!$salida_data) {
                header("Location: pedido_verificar.php?id=$id_pedido&error=salida_no_encontrada");
                exit;
            }
            
            // Obtener detalle de la salida
            $salida_detalle = ObtenerDetalleSalida($id_salida_editar);
        }

        $pedido = $pedido_data[0];
    } else {
        header("Location: pedidos_mostrar.php?error=pedido_no_encontrado");
        exit;
    }
} else {
    header("Location: pedidos_mostrar.php?error=id_invalido");
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Verificar Pedido</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");
            // ========================================================================
            // Calcular y adjuntar stock disponible y en almacén a cada detalle
            // ========================================================================
            foreach ($pedido_detalle as &$detalle) {
                $id_producto  = intval($detalle['id_producto']);
                $id_almacen   = intval($pedido_data[0]['id_almacen']);
                $id_ubicacion = intval($pedido_data[0]['id_ubicacion']);

                // Llamamos a una función en el modelo (m_pedidos.php)
                $stock = ObtenerStockProducto($detalle['id_producto'], $pedido['id_almacen'], $pedido['id_ubicacion'], $id_pedido);
                $detalle['cantidad_disponible_almacen'] = $stock['stock_fisico'];    // lo que muestras como "/Almacén"
                $detalle['cantidad_disponible_real']   = $stock['stock_disponible']; // disponible real (físico - reservado)
            }
            unset($detalle);
            require_once("../_vista/v_pedido_verificar.php"); 
            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>

    <?php
    require_once("../_vista/v_script.php");
    require_once("../_vista/v_alertas.php");
    ?>

    <?php if (isset($alerta) && !empty($alerta) && !empty($alerta['text'])): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // 🔹 VALIDACIÓN: Solo mostrar si hay contenido real
        const alerta = <?php echo json_encode($alerta, JSON_UNESCAPED_UNICODE); ?>;
        
        if (alerta && alerta.text && alerta.text.trim() !== '') {
            Swal.fire({
                icon: alerta.icon || 'info',
                title: alerta.title || 'Aviso',
                text: alerta.text,
                showConfirmButton: !alerta.timer,
                timer: alerta.timer || null,
                allowOutsideClick: false
            });
        }
    });
    </script>
    <?php endif; ?>
</body>
</html>