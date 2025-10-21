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
require_once("../_modelo/m_movimientos.php"); // 🔹 nuevo: para registrar movimientos

$id_pedido = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
$id_compra_editar = isset($_REQUEST['id_compra']) ? intval($_REQUEST['id_compra']) : 0;
$modo_editar = ($id_compra_editar > 0);

$alerta = null;

// ============================================================================
// PROCESAR FORMULARIOS (ANTES DE CUALQUIER HTML)
// ============================================================================

// VERIFICAR ITEM (con validación de estado del pedido)
if (isset($_REQUEST['verificar_item'])) {
    $id_pedido_detalle = intval($_REQUEST['id_pedido_detalle']);
    $new_cant_fin = floatval($_REQUEST['fin_cant_pedido_detalle']);
    $id_personal = $_SESSION['id_personal'] ?? 0;

    //  VALIDACIÓN: La cantidad debe ser mayor a 0
    if ($new_cant_fin <= 0) {
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
                    $rpta = verificarItem($id_pedido_detalle, $new_cant_fin);

                    if ($rpta == "SI") {
                        // ===============================================================
                        //  REGISTRO DE MOVIMIENTO tipo_orden = 5 (pedido / stock comprometido)
                        // ===============================================================
                        $id_producto   = intval($detalle['id_producto']);
                        $id_almacen    = intval($pedido_row['id_almacen']);
                        $id_ubicacion  = intval($pedido_row['id_ubicacion']);
                        $cantidad_pedida = floatval($new_cant_fin);

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
                        if ($cantidad_reservar > 0) {
                            RegistrarMovimientoPedido(
                                $id_pedido_real,
                                $id_producto,
                                $id_almacen,
                                $id_ubicacion,
                                $cantidad_reservar
                            );
                        }
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
// ACTUALIZAR ORDEN (Detectar si es Material o Servicio)
// ============================================================================
if (isset($_REQUEST['actualizar_orden'])) {
    $id_compra = $_REQUEST['id_compra'];
    $id_pedido = $_REQUEST['id'];
    
    //  DETECTAR TIPO DE PEDIDO
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
    
    //  VALIDACIONES BÁSICAS
    if (empty($proveedor_sel) || empty($moneda_sel) || empty($fecha_orden)) {
        echo "ERROR: Complete todos los campos obligatorios (Proveedor, Moneda y Fecha)";
        exit;
    } elseif (empty($items)) {
        echo "ERROR: Debe tener al menos un item en la orden";
        exit;
    }
    
    //  SEPARAR ITEMS EXISTENTES Y NUEVOS
    include("../_conexion/conexion.php");
    
    $items_existentes = [];
    $items_nuevos = [];
    
    foreach ($items as $key => $item) {
        $es_nuevo = isset($item['es_nuevo']) && $item['es_nuevo'] == '1';
        
        if ($es_nuevo) {
            $items_nuevos[] = [
                'id_pedido_detalle' => intval($item['id_pedido_detalle']),
                'id_producto' => intval($item['id_producto']),
                'cantidad' => floatval($item['cantidad']),
                'precio_unitario' => floatval($item['precio_unitario']),
                'igv' => floatval($item['igv'])
            ];
        } else {
            $items_existentes[$key] = $item;
        }
    }
    
    //  ACTUALIZAR SEGÚN TIPO
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
        exit;
    }
    
   
    foreach ($items_nuevos as $nuevo_item) {
        $id_producto = $nuevo_item['id_producto'];
        $cantidad = $nuevo_item['cantidad'];
        $precio = $nuevo_item['precio_unitario'];
        $igv = $nuevo_item['igv'];
        $id_pedido_detalle = $nuevo_item['id_pedido_detalle'];
        
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
                          id_compra, id_producto, cant_compra_detalle, prec_compra_detalle, igv_compra_detalle, hom_compra_detalle, est_compra_detalle
                       ) VALUES (?, ?, ?, ?, ?, $hom_sql, 1)";
        $stmt = $con->prepare($sql_insert);
        $stmt->bind_param("iiddd", $id_compra, $id_producto, $cantidad, $precio, $igv);
        $stmt->execute();
        $stmt->close();
        
        // Cerrar item del pedido
        $sql_cerrar = "UPDATE pedido_detalle 
                      SET est_pedido_detalle = 2 
                      WHERE id_pedido_detalle = ?";
        $stmt_cerrar = $con->prepare($sql_cerrar);
        $stmt_cerrar->bind_param("i", $id_pedido_detalle);
        $stmt_cerrar->execute();
        $stmt_cerrar->close();
    }
    
    mysqli_close($con);
    
    $mensaje_tipo = $es_orden_servicio ? "servicio" : "compra";
    echo "SI"; // El frontend se encargará de redirigir
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
        $proveedor = MostrarProveedores();
        $moneda = MostrarMoneda();
        $obras = MostrarObras();

        // NUEVO: Verificar si ya tiene salida activa
        require_once("../_modelo/m_salidas.php");
        $tiene_salida_activa = TieneSalidaActivaPedido($id_pedido);

        //  CARGAR DATOS DE LA ORDEN SI ESTÁ EN MODO EDICIÓN
        $orden_data = null;
        $orden_detalle = null;
        if ($modo_editar) {
            $orden_data = ObtenerOrdenPorId($id_compra_editar);
            $orden_detalle = ObtenerDetalleOrden($id_compra_editar);
            
            //  DEBUG: Verificar qué trae la consulta
            error_log("ORDEN DATA: " . print_r($orden_data, true));
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
                $stock = ObtenerStockProducto($detalle['id_producto'], $pedido['id_almacen'], $pedido['id_ubicacion']);
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