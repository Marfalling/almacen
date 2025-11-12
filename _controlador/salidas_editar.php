<?php
//=======================================================================
// SALIDAS - EDITAR (salidas_editar.php)
//=======================================================================
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('editar_salidas')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'SALIDAS', 'EDITAR');
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
    
    <title>Editar Salida</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");
            
            require_once("../_conexion/conexion.php");
            require_once("../_modelo/m_salidas.php");
            require_once("../_modelo/m_uso_material.php");
            require_once("../_modelo/m_almacen.php");
            require_once("../_modelo/m_ubicacion.php");
            require_once("../_modelo/m_personal.php");
            require_once("../_modelo/m_tipo_material.php");
            require_once("../_modelo/m_documentos.php");

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
                        window.location.href = 'salidas_mostrar.php';
                    }, 100);
                </script>
                <?php
                exit();
            }

            $id_salida = intval($_GET['id']);

            // Cargar datos de la salida
            $salida_datos = ConsultarSalida($id_salida);
            if (empty($salida_datos)) {
                $mostrar_alerta = true;
                $tipo_alerta = 'error';
                $titulo_alerta = 'Salida no encontrada';
                $mensaje_alerta = 'La salida especificada no existe o ha sido eliminada';
                
                ?>
                <script Language="JavaScript">
                    setTimeout(function() {
                        window.location.href = 'salidas_mostrar.php';
                    }, 3000);
                </script>
                <?php
            }

            // Cargar detalles de la salida
            $salida_detalles = ConsultarSalidaDetalle($id_salida);

            // Cargar datos para el formulario
            $almacenes = MostrarAlmacenesActivos();
            $ubicaciones = MostrarUbicacionesActivas();
            $personal = MostrarPersonal();
            $material_tipos = MostrarMaterialTipoActivos();

            // Cargar documentos asociados a la salida
            $documentos = MostrarDocumentos('salidas', $id_salida);

            // ============================================================
            // SUBIR NUEVOS DOCUMENTOS
            // ============================================================
            if (isset($_FILES['documento']) && count($_FILES['documento']['name']) > 0) {
                $entidad = "salidas";
                $target_dir = __DIR__ . "/../uploads/" . $entidad . "/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                foreach ($_FILES['documento']['name'] as $i => $nombre_original) {
                    if (!empty($nombre_original)) {
                        $nombre_limpio = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $nombre_original);
                        $nombre_archivo = $entidad . "_" . $id_salida . "_" . time() . "_" . $nombre_limpio;
                        $target_file = $target_dir . $nombre_archivo;

                        if (move_uploaded_file($_FILES["documento"]["tmp_name"][$i], $target_file)) {
                            GuardarDocumento($entidad, $id_salida, $nombre_archivo, $_SESSION['id_personal']);
                        }
                    }
                }
                $documentos = MostrarDocumentos('salidas', $id_salida);
            }

            // ============================================================
            // ELIMINAR DOCUMENTO (AJAX)
            // ============================================================
            if (isset($_POST['eliminar_doc']) && isset($_POST['id_doc'])) {
                $id_doc = intval($_POST['id_doc']);
                $res = EliminarDocumento($id_doc);
                echo json_encode(['success' => $res ? true : false]);
                exit;
            }

            //=======================================================================
            // 🔥 CONTROLADOR - VERSIÓN CORREGIDA
            //=======================================================================
            if (isset($_REQUEST['actualizar'])) {
                error_log("========================================");
                error_log("🔧 INICIO ACTUALIZACIÓN SALIDA");
                error_log("========================================");
                
                $id_material_tipo = intval($_REQUEST['id_material_tipo']);
                $id_almacen_origen = intval($_REQUEST['id_almacen_origen']);
                $id_ubicacion_origen = intval($_REQUEST['id_ubicacion_origen']);
                $id_almacen_destino = intval($_REQUEST['id_almacen_destino']);
                $id_ubicacion_destino = intval($_REQUEST['id_ubicacion_destino']);
                $ndoc_salida = $_REQUEST['ndoc_salida'];
                $fec_req_salida = $_REQUEST['fec_req_salida'];
                $obs_salida = $_REQUEST['obs_salida'];
                $id_personal_encargado = intval($_REQUEST['id_personal_encargado']);
                $id_personal_recibe = intval($_REQUEST['id_personal_recibe']);
                
                // ============================================================
                // 🔥 CONSTRUCCIÓN CORRECTA DEL ARRAY DE MATERIALES
                // ============================================================
                $materiales = array();
                
                if (isset($_REQUEST['id_producto']) && is_array($_REQUEST['id_producto'])) {
                    
                    error_log("📦 Total de productos recibidos: " . count($_REQUEST['id_producto']));
                    
                    foreach ($_REQUEST['id_producto'] as $index => $id_producto) {
                        if (!empty($id_producto) && !empty($_REQUEST['cantidad'][$index])) {
                            
                            $cantidad = floatval($_REQUEST['cantidad'][$index]);
                            $descripcion = isset($_REQUEST['descripcion'][$index]) ? $_REQUEST['descripcion'][$index] : '';
                            
                            // 🔹 OBTENER id_salida_detalle DESDE salida_detalles
                            $id_salida_detalle = 0;
                            if (isset($salida_detalles[$index])) {
                                $id_salida_detalle = intval($salida_detalles[$index]['id_salida_detalle']);
                            }
                            
                            // 🔹 OBTENER id_pedido_detalle si existe
                            $id_pedido_detalle = 0;
                            if (isset($salida_detalles[$index]) && isset($salida_detalles[$index]['id_pedido_detalle'])) {
                                $id_pedido_detalle = intval($salida_detalles[$index]['id_pedido_detalle']);
                            }
                            
                            // 🔹 DETERMINAR SI ES NUEVO (si no tiene id_salida_detalle)
                            $es_nuevo = ($id_salida_detalle <= 0) ? '1' : '0';
                            
                            error_log("   Item $index: id_salida_detalle=$id_salida_detalle | id_pedido_detalle=$id_pedido_detalle | id_producto=$id_producto | cantidad=$cantidad | es_nuevo=$es_nuevo");
                            
                            //  CONSTRUIR EL ARRAY CON LA ESTRUCTURA CORRECTA
                            $materiales[] = array(
                                'id_salida_detalle' => $id_salida_detalle,
                                'id_producto' => intval($id_producto),
                                'id_pedido_detalle' => $id_pedido_detalle,
                                'descripcion' => $descripcion,
                                'cantidad' => $cantidad,
                                'es_nuevo' => $es_nuevo
                            );
                        }
                    }
                }

                error_log("📋 Total de materiales procesados: " . count($materiales));

                // Validar que haya al menos un material
                if (count($materiales) > 0) {
                    
                    // ============================================================
                    // 🔥 VALIDACIÓN DE STOCKS (OPCIONAL - Puedes quitarlo si no es necesario)
                    // ============================================================
                    $errores_stock = array();
                    
                    foreach ($materiales as $material) {
                        // Obtener stock actual del producto en la ubicación origen
                        $stock_actual = ObtenerStockDisponible($material['id_producto'], $id_almacen_origen, $id_ubicacion_origen);
                        
                        // Obtener cantidad previamente asignada en esta salida para este producto
                        $cantidad_previa = 0;
                        foreach ($salida_detalles as $detalle_previo) {
                            if ($detalle_previo['id_producto'] == $material['id_producto']) {
                                $cantidad_previa = $detalle_previo['cant_salida_detalle'];
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
                        
                        error_log("❌ ERRORES DE STOCK: " . implode(" | ", $errores_stock));
                    } else {
                        // ============================================================
                        // ✅ LLAMAR A LA FUNCIÓN ACTUALIZAR
                        // ============================================================
                        error_log("🚀 Llamando a ActualizarSalida...");
                        
                        $resultado = ActualizarSalida(
                            $id_salida, 
                            $id_almacen_origen, 
                            $id_ubicacion_origen,
                            $id_almacen_destino, 
                            $id_ubicacion_destino, 
                            $ndoc_salida,
                            $fec_req_salida, 
                            $obs_salida, 
                            $id_personal_encargado,
                            $id_personal_recibe, 
                            $materiales
                        );
                        
                        error_log("📤 Resultado: $resultado");
                        
                        if ($resultado === "SI") {
                            error_log("✅ ACTUALIZACIÓN EXITOSA");
                            ?>
                            <script Language="JavaScript">
                                setTimeout(function() {
                                    window.location.href = 'salidas_mostrar.php?actualizado=true';
                                }, 100);
                            </script>
                            <?php
                            exit();
                        } else {
                            error_log("❌ ERROR EN ACTUALIZACIÓN: $resultado");
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
                
                error_log("========================================");
                error_log("🔧 FIN ACTUALIZACIÓN SALIDA");
                error_log("========================================");
            }
            
            // Solo mostrar la vista si hay datos válidos de salida
            if (!empty($salida_datos)) {
                require_once("../_vista/v_salidas_editar.php");
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