<?php
//=======================================================================
// PEDIDOS - EDITAR (pedidos_editar.php)
//=======================================================================
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

if (!verificarPermisoEspecifico('editar_pedidos')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'PEDIDOS', 'EDITAR');
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
    <title>Editar Pedido</title>
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");
            require_once("../_modelo/m_pedidos.php");
            require_once("../_modelo/m_almacen.php");
            require_once("../_modelo/m_unidad_medida.php");
            require_once("../_modelo/m_tipo_producto.php");
            require_once("../_modelo/m_tipo_material.php");
            require_once("../_modelo/m_ubicacion.php");
            require_once("../_modelo/m_centro_costo.php"); 
            require_once("../_modelo/m_personal.php");

            // Cargar datos necesarios para el formulario
            $unidades_medida = MostrarUnidadMedidaActiva();
            $producto_tipos = MostrarProductoTipoActivos();
            $material_tipos = MostrarMaterialTipoActivos();
            $ubicaciones = MostrarUbicacionesActivas(); 
            $centros_costo = MostrarCentrosCostoActivos(); 
            $personal_list = MostrarPersonalActivo();
            $centro_costo_usuario = ObtenerCentroCostoPersonal($id_personal);
            
            // Crear directorio de archivos si no existe
            if (!file_exists("../_archivos/pedidos/")) {
                mkdir("../_archivos/pedidos/", 0777, true);
            }

            $id_pedido = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
            $pedido_detalle = ConsultarPedidoDetalle($id_pedido);

            //=======================================================================
            // CONTROLADOR ACTUALIZADO - CON AUDITORÍA
            //=======================================================================
            if (isset($_REQUEST['actualizar'])) {
                //  OBTENER DATOS ANTES DE EDITAR
                $pedido_antes = ConsultarPedido($id_pedido);
                if (!empty($pedido_antes)) {
                    $pedido_antes = $pedido_antes[0];
                    $nom_pedido_anterior = $pedido_antes['nom_pedido'];
                    $fecha_necesidad_anterior = $pedido_antes['fec_req_pedido'];
                    $num_ot_anterior = $pedido_antes['ot_pedido'];
                    $lugar_entrega_anterior = $pedido_antes['lug_pedido'];
                    
                    // El centro de costo NO se modifica, viene del pedido original
                    $id_centro_costo = intval($pedido_antes['id_centro_costo']);
                } else {
                    echo "<script>alert('Error: No se pudo obtener los datos del pedido'); history.back();</script>";
                    exit;
                }
                
                $id_ubicacion = intval($_REQUEST['id_ubicacion']);                
                $nom_pedido = strtoupper($_REQUEST['nom_pedido']);
                $fecha_necesidad = $_REQUEST['fecha_necesidad'];
                $num_ot = strtoupper($_REQUEST['num_ot']);
                $contacto = $_REQUEST['contacto'];
                $lugar_entrega = strtoupper($_REQUEST['lugar_entrega']);
                $aclaraciones = strtoupper($_REQUEST['aclaraciones']);
                
                // Procesar materiales con centros de costo multiples
                $materiales = array();
                $errores_validacion = array();
                
                if (isset($_REQUEST['descripcion']) && is_array($_REQUEST['descripcion'])) {
                    for ($i = 0; $i < count($_REQUEST['descripcion']); $i++) {
                        $sst_descripcion = trim($_REQUEST['sst'][$i]);
                        $ot_detalle = isset($_REQUEST['ot_detalle'][$i]) ? trim($_REQUEST['ot_detalle'][$i]) : '';

                        // Procesar centros de costo múltiples
                        $centros_costo_material = array();
                        if (isset($_REQUEST['centros_costo']) && is_array($_REQUEST['centros_costo'])) {
                            if (isset($_REQUEST['centros_costo'][$i])) {
                                $centros_value = $_REQUEST['centros_costo'][$i];
                                if (is_array($centros_value)) {
                                    $centros_costo_material = $centros_value;
                                } else if (is_string($centros_value) && !empty($centros_value)) {
                                    $centros_costo_material = explode(',', $centros_value);
                                }
                            }
                        }
                        
                        $centros_costo_material = array_map('intval', $centros_costo_material);
                        $centros_costo_material = array_filter($centros_costo_material, function($id) {
                            return $id > 0;
                        });
                        $centros_costo_material = array_unique($centros_costo_material);
                        
                        $personal_material = array();
                        if (isset($_REQUEST['personal_ids']) && is_array($_REQUEST['personal_ids'])) {
                            if (isset($_REQUEST['personal_ids'][$i])) {
                                $personal_value = $_REQUEST['personal_ids'][$i];
                                if (is_array($personal_value)) {
                                    $personal_material = $personal_value;
                                } else if (is_string($personal_value) && !empty($personal_value)) {
                                    $personal_material = explode(',', $personal_value);
                                }
                            }
                        }
                        
                        $personal_material = array_map('intval', $personal_material);
                        $personal_material = array_filter($personal_material, function($id) {
                            return $id > 0;
                        });
                        $personal_material = array_unique($personal_material);

                        $materiales[] = array(
                            'id_producto' => $_REQUEST['id_material'][$i],
                            'descripcion' => $_REQUEST['descripcion'][$i],
                            'cantidad' => $_REQUEST['cantidad'][$i],
                            'unidad' => $_REQUEST['unidad'][$i],
                            'observaciones' => $_REQUEST['observaciones'][$i],
                            'sst_descripcion' => $sst_descripcion,
                            'ot_detalle' => $ot_detalle,
                            'id_detalle' => $_REQUEST['id_detalle'][$i],
                            'centros_costo' => $centros_costo_material,
                            'personal_ids' => $personal_material 
                        );
                    }
                }

                // ═══════════════════════════════════════════════════════════════
                //  VALIDACIÓN DE PRODUCTOS 
                // ═══════════════════════════════════════════════════════════════

                // Validar que todos los materiales tengan un ID de producto válido
                if (!empty($materiales)) {
                    foreach ($materiales as $index => $material) {
                        $id_producto = isset($material['id_producto']) ? trim($material['id_producto']) : '';
                        $descripcion = isset($material['descripcion']) ? trim($material['descripcion']) : '';
                        
                        // Validar que tenga ID de producto
                        if (empty($id_producto) || $id_producto === '0' || $id_producto === '') {
                            if (!empty($descripcion)) {
                                $errores_validacion[] = "Material " . ($index + 1) . ": '{$descripcion}' - No tiene un producto seleccionado válido";
                            } else {
                                $errores_validacion[] = "Material " . ($index + 1) . " - Sin producto seleccionado";
                            }
                        }
                        
                        // Validar que el ID sea numérico y mayor a 0
                        if (!empty($id_producto) && (!is_numeric($id_producto) || intval($id_producto) <= 0)) {
                            $errores_validacion[] = "Material " . ($index + 1) . ": ID de producto inválido ({$id_producto})";
                        }
                    }
                }

                // Si hay errores, detener el proceso
                if (!empty($errores_validacion)) {
                    $mensaje_error = "No se puede actualizar el pedido. Errores encontrados:\\n\\n";
                    $mensaje_error .= implode("\\n", $errores_validacion);
                    $mensaje_error .= "\\n\\nDebe buscar y seleccionar productos válidos para todos los materiales.";
                    
                    // Auditoría del error
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR VALIDACIÓN EDITAR', 'PEDIDOS', 
                                   "ID: $id_pedido - Productos inválidos: " . implode("; ", $errores_validacion));
                    ?>
                    <script Language="JavaScript">
                        alert('<?php echo addslashes($mensaje_error); ?>');
                        history.back();
                    </script>
                    <?php
                    exit;
                }

                // ═══════════════════════════════════════════════════════════════
                // FIN DE LA VALIDACIÓN
                // ═══════════════════════════════════════════════════════════════

                // Procesar archivos
                $archivos_subidos = array();
                foreach ($_FILES as $key => $file) {
                    if (strpos($key, 'archivos_') === 0 && !empty($file['name'][0])) {
                        $index = str_replace('archivos_', '', $key);
                        $archivos_subidos[$index] = $file;
                    }
                }

                $rpta = ActualizarPedido($id_pedido, $id_ubicacion, $id_centro_costo, $nom_pedido, $fecha_necesidad, 
                        $num_ot, $contacto, $lugar_entrega, 
                        $aclaraciones, $materiales, $archivos_subidos);

                if ($rpta == "SI") {
                    //  CONSTRUIR DESCRIPCIÓN CON CAMBIOS DEL PEDIDO
                    $cambios = [];
                    
                    if ($nom_pedido_anterior != $nom_pedido) {
                        $cambios[] = "Nombre: '$nom_pedido_anterior' → '$nom_pedido'";
                    }
                    
                    if ($fecha_necesidad_anterior != $fecha_necesidad) {
                        $cambios[] = "Fecha: '$fecha_necesidad_anterior' → '$fecha_necesidad'";
                    }
                    
                    if ($num_ot_anterior != $num_ot) {
                        $cambios[] = "OT: '$num_ot_anterior' → '$num_ot'";
                    }
                    
                    if ($lugar_entrega_anterior != $lugar_entrega) {
                        $cambios[] = "Lugar entrega: '$lugar_entrega_anterior' → '$lugar_entrega'";
                    }
                    
                    //  AUDITORÍA DETALLADA DE MATERIALES
                    $cambios_materiales = [];
                    
                    // Obtener datos ANTES de la edición
                    $materiales_antes = [];
                    foreach ($pedido_detalle as $det) {
                        $id_det = intval($det['id_pedido_detalle']);
                        $materiales_antes[$id_det] = [
                            'descripcion' => $det['prod_pedido_detalle'],
                            'cantidad' => floatval($det['cant_pedido_detalle'])
                        ];
                    }
                    
                    // Comparar con los nuevos datos
                    foreach ($materiales as $material) {
                        $id_det = intval($material['id_detalle']);
                        $nueva_cantidad = floatval($material['cantidad']);
                        $nueva_descripcion = $material['descripcion'];
                        
                        if (isset($materiales_antes[$id_det])) {
                            // Material existente - verificar cambios
                            $cantidad_anterior = $materiales_antes[$id_det]['cantidad'];
                            $descripcion_anterior = $materiales_antes[$id_det]['descripcion'];
                            
                            // Acortar descripciones largas
                            $desc_corta = (strlen($nueva_descripcion) > 40) 
                                ? substr($nueva_descripcion, 0, 40) . '...' 
                                : $nueva_descripcion;
                            
                            if ($cantidad_anterior != $nueva_cantidad) {
                                $cambios_materiales[] = "$desc_corta: $cantidad_anterior → $nueva_cantidad";
                            }
                            
                            // Remover de la lista para detectar eliminados
                            unset($materiales_antes[$id_det]);
                        } else {
                            // Material nuevo
                            $desc_corta = (strlen($nueva_descripcion) > 40) 
                                ? substr($nueva_descripcion, 0, 40) . '...' 
                                : $nueva_descripcion;
                            $cambios_materiales[] = "NUEVO: $desc_corta (Cant: $nueva_cantidad)";
                        }
                    }
                    
                    // Detectar materiales eliminados
                    foreach ($materiales_antes as $id_det => $datos) {
                        $desc_corta = (strlen($datos['descripcion']) > 40) 
                            ? substr($datos['descripcion'], 0, 40) . '...' 
                            : $datos['descripcion'];
                        $cambios_materiales[] = "ELIMINADO: $desc_corta (Cant: {$datos['cantidad']})";
                    }
                    
                    //  CONSTRUIR DESCRIPCIÓN FINAL
                    if (!empty($cambios_materiales)) {
                        $cambios[] = "Materiales: " . implode(' | ', $cambios_materiales);
                    } else {
                        $cambios[] = count($materiales) . " materiales (sin cambios)";
                    }
                    
                    if (count($cambios) == 0) {
                        $descripcion = "ID: $id_pedido | Sin cambios";
                    } else {
                        $descripcion = "ID: $id_pedido | " . implode(' | ', $cambios);
                    }
                    
                    GrabarAuditoria($id, $usuario_sesion, 'EDITAR', 'PEDIDOS', $descripcion);
                ?>
                    <script Language="JavaScript">
                        location.href = 'pedidos_mostrar.php?actualizado=true';
                    </script>
                <?php
                } else {
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'PEDIDOS', "ID: $id_pedido - Error: $rpta");
                ?>
                    <script Language="JavaScript">
                        alert('Error al actualizar el pedido: <?php echo $rpta; ?>');
                    </script>
                <?php

                }
            }
            //-------------------------------------------

            if ($id_pedido > 0) {
                // Cargar datos del pedido
                $pedido_data = ConsultarPedido($id_pedido);
                foreach ($pedido_detalle as &$detalle) {
                    $id_producto  = intval($detalle['id_producto']);
                    $id_almacen   = intval($pedido_data[0]['id_almacen']);
                    $id_ubicacion = intval($pedido_data[0]['id_ubicacion']);

                    // Consultar stock disponible real y en almacén
                    $stock = ObtenerStockProducto($id_producto, $id_almacen, $id_ubicacion);

                    // Ajusta nombres según lo que devuelve tu función
                    $detalle['cantidad_disponible_almacen'] = $stock['stock_fisico'];
                    $detalle['cantidad_disponible_real']    = $stock['stock_disponible'];

                     // Cargar centros de costo para este detalle
                    $detalle['centros_costo'] = ObtenerCentrosCostoPorDetalle($detalle['id_pedido_detalle']);
                     // Cargar personal asignado para este detalle                   
                    $detalle['personal_ids'] = ObtenerPersonalDetalle($detalle['id_pedido_detalle']);

                }
                unset($detalle);
                
                if (!empty($pedido_data)) {
                    require_once("../_vista/v_pedidos_editar.php");
                } else {
                    echo "<script>alert('Pedido no encontrado'); location.href='pedidos_mostrar.php';</script>";
                }
            } else {
                echo "<script>alert('ID de pedido no válido'); location.href='pedidos_mostrar.php';</script>";
            }

            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>

    <?php
    require_once("../_vista/v_script.php");
    require_once("../_vista/v_alertas.php");
    ?>
</body>
</html>