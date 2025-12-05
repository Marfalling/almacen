<?php
require_once("../_conexion/sesion.php");
require_once("../_conexion/conexion.php");
require_once("../_modelo/m_auditoria.php"); 

if (!verificarPermisoEspecifico('editar_devoluciones')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'DEVOLUCION', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
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
    
    <title>Editar Devolución</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");
            require_once("../_modelo/m_producto.php");
            require_once("../_modelo/m_devolucion.php");
            require_once("../_modelo/m_uso_material.php");
            require_once("../_modelo/m_almacen.php");
            require_once("../_modelo/m_ubicacion.php");
            require_once("../_modelo/m_personal.php");
            require_once("../_modelo/m_tipo_material.php");
            require_once("../_modelo/m_clientes.php"); // <-- agregado para clientes

            // Variables para mostrar alertas
            $mostrar_alerta = false;
            $tipo_alerta = '';
            $titulo_alerta = '';
            $mensaje_alerta = '';

            // Verificar que se haya pasado el ID de la salida
            if (!isset($_GET['id']) || empty($_GET['id'])) {
                ?>
                <script Language="JavaScript">
                    setTimeout(function() {
                        window.location.href = 'devoluciones_mostrar.php';
                    }, 100);
                </script>
                <?php
                exit();
            }

            $id_devolucion = intval($_GET['id']);

            // Cargar datos de la devolucion
            $devolucion_datos = ConsultarDevolucion($id_devolucion);
            if (empty($devolucion_datos)) {
                $mostrar_alerta = true;
                $tipo_alerta = 'error';
                $titulo_alerta = 'Devolución no encontrada';
                $mensaje_alerta = 'La devolución especificada no existe o ha sido eliminada';
                
                // Redireccionar después de un tiempo
                ?>
                <script Language="JavaScript">
                    setTimeout(function() {
                        window.location.href = 'devoluciones_mostrar.php';
                    }, 3000);
                </script>
                <?php
            }

            // Cargar detalles de la devolucion
            $devolucion_detalles = ConsultarDevolucionDetalle($id_devolucion);

            // Cargar datos para el formulario
            $almacenes = MostrarAlmacenesActivos();
            $ubicaciones = MostrarUbicacionesActivas();
            $personal = MostrarPersonal();
            $material_tipos = MostrarMaterialTipoActivos();
            $id_almacen_actual = $devolucion_datos[0]['id_almacen'];
            $id_cliente_actual = $devolucion_datos[0]['id_cliente_destino'] ?? null;
            $nom_cliente_actual = $devolucion_datos[0]['nom_cliente_destino'] ?? null;

            //=======================================================================
            // OPERACIÓN DE EDICIÓN
            //=======================================================================
            if (isset($_REQUEST['actualizar'])) {
                //  OBTENER DATOS ANTES DE EDITAR
                $devolucion_antes = ConsultarDevolucion($id_devolucion);
                $id_almacen_anterior = $devolucion_antes[0]['id_almacen'];
                $nom_almacen_anterior = $devolucion_antes[0]['nom_almacen'] ?? '';
                $id_ubicacion_anterior = $devolucion_antes[0]['id_ubicacion'];
                $nom_ubicacion_anterior = $devolucion_antes[0]['nom_ubicacion'] ?? '';
                $id_cliente_anterior = $devolucion_antes[0]['id_cliente_destino'] ?? 0;
                $nom_cliente_anterior = $devolucion_antes[0]['nom_cliente_destino'] ?? '';
                $obs_anterior = $devolucion_antes[0]['obs_devolucion'] ?? '';
                $detalles_anteriores = ConsultarDevolucionDetalle($id_devolucion);
                
                // Obtener nuevos valores
                $id_almacen = intval($_REQUEST['id_almacen']);
                $id_ubicacion = intval($_REQUEST['id_ubicacion']);
                
                $cliente_destino = ObtenerClientePorAlmacen($id_almacen);
                $id_cliente_destino = $cliente_destino['id_cliente'] ?? 0;
                $nom_cliente_nuevo = $cliente_destino['nom_cliente'] ?? '';

                $obs_devolucion = mysqli_real_escape_string($con, $_REQUEST['obs_devolucion']);
                
                // Obtener nombres de almacén y ubicación nuevos
                $nom_almacen_nuevo = '';
                foreach ($almacenes as $alm) {
                    if ($alm['id_almacen'] == $id_almacen) {
                        $nom_almacen_nuevo = $alm['nom_almacen'];
                        break;
                    }
                }
                
                $nom_ubicacion_nuevo = '';
                foreach ($ubicaciones as $ubi) {
                    if ($ubi['id_ubicacion'] == $id_ubicacion) {
                        $nom_ubicacion_nuevo = $ubi['nom_ubicacion'];
                        break;
                    }
                }
                
                // Procesar materiales
                $materiales = array();
                if (isset($_REQUEST['id_producto']) && is_array($_REQUEST['id_producto'])) {
                    foreach ($_REQUEST['id_producto'] as $index => $id_producto) {
                        if (!empty($id_producto) && !empty($_REQUEST['cantidad'][$index])) {
                            $materiales[] = array(
                                'id_devolucion_detalle' => isset($_REQUEST['id_devolucion_detalle'][$index]) 
                                    ? intval($_REQUEST['id_devolucion_detalle'][$index]) 
                                : 0, // ✅ Si es 0, indica que es un registro NUEVO
                                'id_producto' => intval($id_producto),
                                'descripcion' => mysqli_real_escape_string($con, $_REQUEST['descripcion'][$index]),
                                'cantidad' => floatval($_REQUEST['cantidad'][$index])
                            );
                        }
                    }
                }
                
                // Validar cliente destino
                if (!$id_cliente_destino) {
                    $mostrar_alerta = true;
                    $tipo_alerta = 'warning';
                    $titulo_alerta = 'Datos incompletos';
                    $mensaje_alerta = 'Debe seleccionar un cliente destino.';
                } else {
                    // Validar que haya al menos un material
                    if (count($materiales) > 0) {
                        // Validar stocks antes de actualizar
                        $errores_stock = array();
                        
                        foreach ($materiales as $material) {
                            // Obtener stock actual del producto en la ubicación origen
                            $stock_actual = ObtenerStockDisponible($material['id_producto'], $id_almacen, $id_ubicacion);
                            
                            // Obtener cantidad previamente asignada en esta salida para este producto
                            $cantidad_previa = 0;
                            foreach ($devolucion_detalles as $detalle_previo) {
                                if ($detalle_previo['id_producto'] == $material['id_producto']) {
                                    $cantidad_previa = $detalle_previo['cant_devolucion_detalle'];
                                    break;
                                }
                            }
                            
                            // Stock disponible = stock actual + cantidad que se va a "devolver" por la edición
                            $stock_disponible = $stock_actual + $cantidad_previa;
                            
                            // Verificar si la nueva cantidad excede el stock disponible
                            if ($material['cantidad'] > $stock_disponible) {
                                $errores_stock[] = "El producto '{$material['descripcion']}' no tiene suficiente stock. Disponible: {$stock_disponible}, solicitado: {$material['cantidad']}";
                            }
                        }
                        
                        // Si hay errores de stock, no proceder
                        if (count($errores_stock) > 0) {
                            $mostrar_alerta = true;
                            $tipo_alerta = 'warning';
                            $titulo_alerta = 'Stock insuficiente';
                            $mensaje_alerta = implode('<br>', $errores_stock);
                        } else {
                            // Proceder con la actualización
                            // NOTA: ActualizarDevolucion ahora espera id_cliente_destino como parámetro adicional
                            $resultado = ActualizarDevolucion($id_devolucion, $id_almacen, $id_ubicacion, $id_cliente_destino,
                                    $obs_devolucion, $materiales);
                            
                            if ($resultado === "SI") {
                                //  CONSTRUIR DESCRIPCIÓN CON CAMBIOS
                                $cambios = [];

                                if ($id_almacen_anterior != $id_almacen) {
                                    $cambios[] = "Almacén: '$nom_almacen_anterior' → '$nom_almacen_nuevo'";
                                }

                                if ($id_ubicacion_anterior != $id_ubicacion) {
                                    $cambios[] = "Ubicación: '$nom_ubicacion_anterior' → '$nom_ubicacion_nuevo'";
                                }

                                if ($id_cliente_anterior != $id_cliente_destino) {
                                    $cambios[] = "Cliente: '$nom_cliente_anterior' → '$nom_cliente_nuevo'";
                                }

                                if (trim($obs_anterior) != trim($obs_devolucion)) {
                                    $cambios[] = "Observación modificada";
                                }

                                //  COMPARAR MATERIALES (DETALLADO)
                                $cambios_materiales = [];

                                // Crear índice de materiales anteriores
                                $materiales_antes_index = [];
                                foreach ($detalles_anteriores as $det) {
                                    $id_prod = intval($det['id_producto']);
                                    $materiales_antes_index[$id_prod] = [
                                        'descripcion' => $det['nom_producto'],
                                        'cantidad' => floatval($det['cant_devolucion_detalle'])
                                    ];
                                }

                                // Comparar con materiales nuevos
                                foreach ($materiales as $mat) {
                                    $id_prod = intval($mat['id_producto']);
                                    
                                    //  USAR FUNCIÓN DEL MODELO
                                    $producto_data = ObtenerProductoPorId($id_prod);
                                    $nom_producto = $producto_data ? $producto_data['nom_producto'] : "ID:$id_prod";
                                    
                                    $desc_corta = (strlen($nom_producto) > 30) 
                                        ? substr($nom_producto, 0, 30) . '...' 
                                        : $nom_producto;
                                    
                                    if (isset($materiales_antes_index[$id_prod])) {
                                        // Producto existente
                                        $cantidad_anterior = $materiales_antes_index[$id_prod]['cantidad'];
                                        $cantidad_nueva = floatval($mat['cantidad']);
                                        
                                        if ($cantidad_anterior != $cantidad_nueva) {
                                            $cambios_materiales[] = "$desc_corta: $cantidad_anterior → $cantidad_nueva";
                                        }
                                        
                                        unset($materiales_antes_index[$id_prod]);
                                    } else {
                                        // Producto nuevo
                                        $cambios_materiales[] = "NUEVO: $desc_corta (Cant: {$mat['cantidad']})";
                                    }
                                }

                                // Detectar productos eliminados
                                foreach ($materiales_antes_index as $id_prod => $datos) {
                                    $desc_corta = (strlen($datos['descripcion']) > 30) 
                                        ? substr($datos['descripcion'], 0, 30) . '...' 
                                        : $datos['descripcion'];
                                    $cambios_materiales[] = "ELIMINADO: $desc_corta (Cant: {$datos['cantidad']})";
                                }

                                // Agregar cambios de materiales
                                if (!empty($cambios_materiales)) {
                                    $cambios[] = "Materiales: " . implode(' | ', $cambios_materiales);
                                }

                                if (count($cambios) == 0) {
                                    $descripcion = "ID: $id_devolucion | Cliente: $nom_cliente_nuevo | Sin cambios";
                                } else {
                                    $descripcion = "ID: $id_devolucion | Cliente: $nom_cliente_nuevo | " . implode(' | ', $cambios);
                                }

                                GrabarAuditoria($id, $usuario_sesion, 'EDITAR', 'DEVOLUCION', $descripcion);
                                ?>
                                <script Language="JavaScript">
                                    setTimeout(function() {
                                        window.location.href = 'devoluciones_mostrar.php?actualizado=true';
                                    }, 100);
                                </script>
                                <?php
                                exit();
                            } else {
                                GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'DEVOLUCION', "ID: $id_devolucion - " . substr($resultado, 0, 100));
                                
                                $mostrar_alerta = true;
                                $tipo_alerta = 'error';
                                $titulo_alerta = 'Error al actualizar';
                                $mensaje_alerta = str_replace("'", "\'", $resultado);
                            }
                        }
                    } else {
                        $mostrar_alerta = true;
                        $tipo_alerta = 'warning';
                        $titulo_alerta = 'Datos incompletos';
                        $mensaje_alerta = 'Debe tener al menos un material en la salida';
                    }
                }
            }
            //-------------------------------------------
            
            // Solo mostrar la vista si hay datos válidos de devoluciones
            if (!empty($devolucion_datos)) {
                require_once("../_vista/v_devolucion_editar.php");
            }
            
            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>

    <?php
    require_once("../_vista/v_script.php");
    require_once("../_vista/v_alertas.php");
    
    // Mostrar alerta si es necesario
    if ($mostrar_alerta) {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: '<?php echo $tipo_alerta; ?>',
                    title: '<?php echo $titulo_alerta; ?>',
                    html: '<?php echo $mensaje_alerta; ?>',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '<?php echo ($tipo_alerta == "error") ? "#d33" : "#3085d6"; ?>'
                });
            } else {
                alert('<?php echo $titulo_alerta . ": " . strip_tags($mensaje_alerta); ?>');
            }
        });
        </script>
        <?php
    }
    ?>
</body>
</html>