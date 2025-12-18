<?php
// ====================================================================
// CONTROLADORES DE SEGURIDAD PARA PEDIDOS
// ====================================================================

// PEDIDOS - CREAR (pedidos_nuevo.php)

require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

if (!verificarPermisoEspecifico('crear_pedidos')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'PEDIDOS', 'CREAR');
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
    <title>Nuevo Pedido</title>
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
            require_once("../_modelo/m_tipo_producto.php");
            require_once("../_modelo/m_unidad_medida.php");
            require_once("../_modelo/m_tipo_material.php");
            require_once("../_modelo/m_ubicacion.php");
            require_once("../_modelo/m_centro_costo.php");
            require_once("../_modelo/m_personal.php");

            // Cargar datos para el formulario
            $almacenes = MostrarAlmacenesArce(); // Solo almacenes de ARCE
            $producto_tipos = MostrarProductoTipoActivos();
            $unidades_medida = MostrarUnidadMedidaActiva();
            $material_tipos = MostrarMaterialTipoActivos();
            $ubicaciones = MostrarUbicacionesActivas(); 
            $centros_costo = MostrarCentrosCostoActivos();
            $personal_list = MostrarPersonalActivo(); //  AGREGADO - Lista de personal activo
            $centro_costo_usuario = ObtenerCentroCostoPersonal($id_personal);

            
            // Crear directorio de archivos si no existe
            if (!file_exists("../_archivos/pedidos/")) {
                mkdir("../_archivos/pedidos/", 0777, true);
            }
            
            //=======================================================================
            // CONTROLADOR ACTUALIZADO CON AUDITORÍA
            //=======================================================================
            if (isset($_REQUEST['registrar'])) {
                // Recibir datos del formulario
                $id_producto_tipo = intval($_REQUEST['tipo_pedido']);
                $id_almacen = intval($_REQUEST['id_obra']);
                $id_ubicacion = intval($_REQUEST['id_ubicacion']);
                $id_centro_costo = intval($_REQUEST['id_centro_costo']);
                
                // CAMBIO: Si nom_pedido está vacío o es solo espacios, guardarlo como cadena vacía
                $nom_pedido = isset($_REQUEST['nom_pedido']) ? trim($_REQUEST['nom_pedido']) : '';
                if (empty($nom_pedido)) {
                    $nom_pedido = '';
                } else {
                    $nom_pedido = strtoupper($nom_pedido);
                }
                
                $solicitante = strtoupper($_REQUEST['solicitante']);
                $fecha_necesidad = $_REQUEST['fecha_necesidad'];
                $num_ot = strtoupper($_REQUEST['num_ot']);
                $contacto = $_REQUEST['contacto'];
                $lugar_entrega = strtoupper($_REQUEST['lugar_entrega']);
                $aclaraciones = strtoupper($_REQUEST['aclaraciones']);
                
                // Procesar materiales
                $materiales = array();
                $errores_validacion = array();

                if (isset($_REQUEST['descripcion']) && is_array($_REQUEST['descripcion'])) {
                    for ($i = 0; $i < count($_REQUEST['descripcion']); $i++) {
                        $sst_descripcion = trim($_REQUEST['sst'][$i]);
                        $ot_detalle = isset($_REQUEST['ot_detalle'][$i]) ? trim($_REQUEST['ot_detalle'][$i]) : '';
                        
                        // Procesar centros de costo
                        $centros_costo_material = array();
                        if (isset($_REQUEST['centros_costo'])) {
                            if (is_array($_REQUEST['centros_costo'])) {
                                if (isset($_REQUEST['centros_costo'][$i])) {
                                    $centros_value = $_REQUEST['centros_costo'][$i];
                                    if (is_array($centros_value)) {
                                        $centros_costo_material = $centros_value;
                                    } else if (is_string($centros_value) && !empty($centros_value)) {
                                        $centros_costo_material = explode(',', $centros_value);
                                    }
                                }
                            }
                        }

                        $centros_costo_material = array_map('intval', $centros_costo_material);
                        $centros_costo_material = array_filter($centros_costo_material, function($id) {
                            return $id > 0;
                        });
                        $centros_costo_material = array_unique($centros_costo_material);
                        
                        // Procesar personal
                        $personal_material = array();
                        if (isset($_REQUEST['personal_ids'])) {
                            if (is_array($_REQUEST['personal_ids'])) {
                                if (isset($_REQUEST['personal_ids'][$i])) {
                                    $personal_value = $_REQUEST['personal_ids'][$i];
                                    if (is_array($personal_value)) {
                                        $personal_material = $personal_value;
                                    } else if (is_string($personal_value) && !empty($personal_value)) {
                                        $personal_material = explode(',', $personal_value);
                                    }
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
                            'centros_costo' => $centros_costo_material,
                            'personal_ids' => $personal_material  
                        );
                    }
                }

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
                    $mensaje_error = "No se puede registrar el pedido. Errores encontrados:\\n\\n";
                    $mensaje_error .= implode("\\n", $errores_validacion);
                    $mensaje_error .= "\\n\\nDebe buscar y seleccionar productos válidos para todos los materiales.";
                    
                    // Auditoría del error
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR VALIDACIÓN', 'PEDIDOS', 
                                "Productos inválidos: " . implode("; ", $errores_validacion));
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
                    if (strpos($key, 'archivos_') === 0) {
                        $index = str_replace('archivos_', '', $key);
                        $archivos_subidos[$index] = $file;
                    }
                }

                // LLAMADA a GrabarPedido
                $rpta = GrabarPedido($id_producto_tipo, $id_almacen, $id_ubicacion, $id_centro_costo,
                                $nom_pedido, $solicitante, $fecha_necesidad, $num_ot, 
                                $contacto, $lugar_entrega, $aclaraciones, $id_personal, 
                                $materiales, $archivos_subidos);

                if ($rpta == "SI") {
                    //  AUDITORÍA: REGISTRO EXITOSO
                    // Ya no usamos nom_pedido en auditoría
                    $descripcion = "Solicitante: '$solicitante' | OT: '$num_ot' | Fecha: '$fecha_necesidad' | " . count($materiales) . " materiales";
                    GrabarAuditoria($id, $usuario_sesion, 'REGISTRAR', 'PEDIDOS', $descripcion);
            ?>
                    <script Language="JavaScript">
                        location.href = 'pedidos_mostrar.php?registrado=true';
                    </script>
                <?php
                    exit;
                } else {
                    //  AUDITORÍA: ERROR
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'PEDIDOS', "Error: $rpta");
                ?>
                    <script Language="JavaScript">
                        alert('Error al registrar el pedido: <?php echo addslashes($rpta); ?>');
                        location.href = 'pedidos_mostrar.php?error=true';
                    </script>
            <?php
                    exit;
                }
            }
            //-------------------------------------------

            require_once("../_vista/v_pedidos_nuevo.php");
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