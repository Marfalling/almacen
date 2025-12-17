<?php
//=======================================================================
// SALIDAS - EDITAR CON AUDITORÍA (salidas_editar.php)
//=======================================================================
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

if (!verificarPermisoEspecifico('editar_salidas')) {
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
            require_once("../_modelo/m_centro_costo.php");
            $mostrar_alerta = false;
            $tipo_alerta = '';
            $titulo_alerta = '';
            $mensaje_alerta = '';

            // Verificar ID de la salida
            if (!isset($_GET['id']) || empty($_GET['id'])) {
                GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'SALIDAS', 'ID no especificado');
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
                GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'SALIDAS', "ID: $id_salida - Salida no encontrada");
                
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
            
            // Calcular cantidad_disponible_origen para cada detalle
            foreach ($salida_detalles as &$detalle) {
                $id_producto = intval($detalle['id_producto']);
                $id_almacen_origen = intval($salida_datos[0]['id_almacen_origen']);
                $id_ubicacion_origen = intval($salida_datos[0]['id_ubicacion_origen']);
                
                $stock_actual = ObtenerStockDisponible($id_producto, $id_almacen_origen, $id_ubicacion_origen);
                $cantidad_en_salida = floatval($detalle['cant_salida_detalle']);
                
                $detalle['cantidad_disponible_origen'] = $stock_actual + $cantidad_en_salida;
            }
            unset($detalle); 

            // Cargar datos para el formulario
            $almacenes = MostrarAlmacenesActivosConArceBase();
            $ubicaciones = MostrarUbicacionesActivas();
            $personal = MostrarPersonalActivo();
            $material_tipos = MostrarMaterialTipoActivos();
            $centros_costo_personal = ObtenerCentrosCostoTodoPersonal();
            $centro_costo_usuario = ObtenerCentroCostoPersonal($id_personal);


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
            // CONTROLADOR CON AUDITORÍA
            //=======================================================================
            if (isset($_REQUEST['actualizar'])) {
                
                // Obtener datos antes de editar
                $salida_antes = $salida_datos[0];
                $detalles_antes = $salida_detalles;
                
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
                    
                    foreach ($_REQUEST['id_producto'] as $index => $id_producto) {
                        if (!empty($id_producto) && !empty($_REQUEST['cantidad'][$index])) {
                            
                            $cantidad = floatval($_REQUEST['cantidad'][$index]);
                            $descripcion = isset($_REQUEST['descripcion'][$index]) ? $_REQUEST['descripcion'][$index] : '';
                            
                            $id_salida_detalle = 0;
                            if (isset($salida_detalles[$index])) {
                                $id_salida_detalle = intval($salida_detalles[$index]['id_salida_detalle']);
                            }
                            
                            $id_pedido_detalle = 0;
                            if (isset($salida_detalles[$index]) && isset($salida_detalles[$index]['id_pedido_detalle'])) {
                                $id_pedido_detalle = intval($salida_detalles[$index]['id_pedido_detalle']);
                            }
                            
                            $es_nuevo = ($id_salida_detalle <= 0) ? '1' : '0';
                            
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

                // Validar que haya al menos un material
                if (count($materiales) > 0) {
                    
                    // Validación de stocks
                    $errores_stock = array();
                    
                    foreach ($materiales as $material) {
                        $stock_actual = ObtenerStockDisponible($material['id_producto'], $id_almacen_origen, $id_ubicacion_origen);
                        
                        $cantidad_previa = 0;
                        foreach ($salida_detalles as $detalle_previo) {
                            if ($detalle_previo['id_producto'] == $material['id_producto']) {
                                $cantidad_previa = $detalle_previo['cant_salida_detalle'];
                                break;
                            }
                        }
                        
                        $stock_disponible = $stock_actual + $cantidad_previa;
                        
                        if ($material['cantidad'] > $stock_disponible) {
                            $errores_stock[] = "El producto '{$material['descripcion']}' no tiene suficiente stock. Disponible: {$stock_disponible}, solicitado: {$material['cantidad']}";
                        }
                    }
                    
                    if (count($errores_stock) > 0) {
                        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'SALIDAS', 
                            "ID: $id_salida | Stock insuficiente");
                        
                        $mostrar_alerta = true;
                        $tipo_alerta = 'warning';
                        $titulo_alerta = 'Stock insuficiente';
                        $mensaje_alerta = implode('<br>', $errores_stock);
                        
                    } else {
                        // Actualizar salida
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
                        
                        if ($resultado === "SI") {
                            //  CONSTRUIR DESCRIPCIÓN CON CAMBIOS
                            $cambios = [];

                            // Comparar documento
                            if ($salida_antes['ndoc_salida'] != $ndoc_salida) {
                                $cambios[] = "Doc: '{$salida_antes['ndoc_salida']}' → '$ndoc_salida'";
                            }

                            // Comparar fecha
                            if ($salida_antes['fec_req_salida'] != $fec_req_salida) {
                                $cambios[] = "Fecha: '{$salida_antes['fec_req_salida']}' → '$fec_req_salida'";
                            }

                            // Comparar almacén origen
                            if ($salida_antes['id_almacen_origen'] != $id_almacen_origen) {
                                $alm_ant = '';
                                $alm_nue = '';
                                foreach ($almacenes as $alm) {
                                    if ($alm['id_almacen'] == $salida_antes['id_almacen_origen']) $alm_ant = $alm['nom_almacen'];
                                    if ($alm['id_almacen'] == $id_almacen_origen) $alm_nue = $alm['nom_almacen'];
                                }
                                $cambios[] = "Alm.Origen: '$alm_ant' → '$alm_nue'";
                            }

                            // Comparar ubicación origen
                            if ($salida_antes['id_ubicacion_origen'] != $id_ubicacion_origen) {
                                $ubi_ant = '';
                                $ubi_nue = '';
                                foreach ($ubicaciones as $ubi) {
                                    if ($ubi['id_ubicacion'] == $salida_antes['id_ubicacion_origen']) $ubi_ant = $ubi['nom_ubicacion'];
                                    if ($ubi['id_ubicacion'] == $id_ubicacion_origen) $ubi_nue = $ubi['nom_ubicacion'];
                                }
                                $cambios[] = "Ubi.Origen: '$ubi_ant' → '$ubi_nue'";
                            }

                            // Comparar almacén destino
                            if ($salida_antes['id_almacen_destino'] != $id_almacen_destino) {
                                $alm_ant = '';
                                $alm_nue = '';
                                foreach ($almacenes as $alm) {
                                    if ($alm['id_almacen'] == $salida_antes['id_almacen_destino']) $alm_ant = $alm['nom_almacen'];
                                    if ($alm['id_almacen'] == $id_almacen_destino) $alm_nue = $alm['nom_almacen'];
                                }
                                $cambios[] = "Alm.Destino: '$alm_ant' → '$alm_nue'";
                            }

                            // Comparar ubicación destino
                            if ($salida_antes['id_ubicacion_destino'] != $id_ubicacion_destino) {
                                $ubi_ant = '';
                                $ubi_nue = '';
                                foreach ($ubicaciones as $ubi) {
                                    if ($ubi['id_ubicacion'] == $salida_antes['id_ubicacion_destino']) $ubi_ant = $ubi['nom_ubicacion'];
                                    if ($ubi['id_ubicacion'] == $id_ubicacion_destino) $ubi_nue = $ubi['nom_ubicacion'];
                                }
                                $cambios[] = "Ubi.Destino: '$ubi_ant' → '$ubi_nue'";
                            }

                            // Comparar personal encargado
                            if ($salida_antes['id_personal_encargado'] != $id_personal_encargado) {
                                $per_ant = '';
                                $per_nue = '';
                                foreach ($personal as $per) {
                                    if ($per['id_personal'] == $salida_antes['id_personal_encargado']) {
                                        $per_ant = $per['nom_personal'];
                                    }
                                    if ($per['id_personal'] == $id_personal_encargado) {
                                        $per_nue = $per['nom_personal'];
                                    }
                                }
                                $cambios[] = "Encargado: '$per_ant' → '$per_nue'";
                            }

                            // Comparar personal que recibe
                            if ($salida_antes['id_personal_recibe'] != $id_personal_recibe) {
                                $per_ant = '';
                                $per_nue = '';
                                foreach ($personal as $per) {
                                    if ($per['id_personal'] == $salida_antes['id_personal_recibe']) {
                                        $per_ant = $per['nom_personal'];
                                    }
                                    if ($per['id_personal'] == $id_personal_recibe) {
                                        $per_nue = $per['nom_personal'];
                                    }
                                }
                                $cambios[] = "Recibe: '$per_ant' → '$per_nue'";
                            }

                            //  COMPARAR MATERIALES (DETALLADO)
                            $cambios_materiales = [];

                            // Crear índice de materiales anteriores por id_producto
                            $materiales_antes_index = [];
                            foreach ($detalles_antes as $det) {
                                $id_prod = intval($det['id_producto']);
                                $materiales_antes_index[$id_prod] = [
                                    'descripcion' => $det['prod_salida_detalle'],
                                    'cantidad' => floatval($det['cant_salida_detalle'])
                                ];
                            }

                            // Crear índice de materiales nuevos por id_producto
                            $materiales_nuevos_index = [];
                            foreach ($materiales as $mat) {
                                $id_prod = intval($mat['id_producto']);
                                $materiales_nuevos_index[$id_prod] = [
                                    'descripcion' => $mat['descripcion'],
                                    'cantidad' => floatval($mat['cantidad'])
                                ];
                            }

                            // Comparar cantidades
                            foreach ($materiales_nuevos_index as $id_prod => $datos_nuevos) {
                                $desc_corta = (strlen($datos_nuevos['descripcion']) > 30) 
                                    ? substr($datos_nuevos['descripcion'], 0, 30) . '...' 
                                    : $datos_nuevos['descripcion'];
                                
                                if (isset($materiales_antes_index[$id_prod])) {
                                    // Producto existente
                                    $cantidad_anterior = $materiales_antes_index[$id_prod]['cantidad'];
                                    $cantidad_nueva = $datos_nuevos['cantidad'];
                                    
                                    if ($cantidad_anterior != $cantidad_nueva) {
                                        $cambios_materiales[] = "$desc_corta: $cantidad_anterior → $cantidad_nueva";
                                    }
                                    
                                    // Remover para detectar eliminados
                                    unset($materiales_antes_index[$id_prod]);
                                } else {
                                    // Producto nuevo
                                    $cambios_materiales[] = "NUEVO: $desc_corta (Cant: {$datos_nuevos['cantidad']})";
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
                                $descripcion = "ID: $id_salida | Doc: $ndoc_salida | Sin cambios";
                            } else {
                                $descripcion = "ID: $id_salida | Doc: $ndoc_salida | " . implode(' | ', $cambios);
                            }

                            GrabarAuditoria($id, $usuario_sesion, 'EDITAR', 'SALIDAS', $descripcion);
                            
                            ?>
                            <script Language="JavaScript">
                                setTimeout(function() {
                                    window.location.href = 'salidas_mostrar.php?actualizado=true';
                                }, 100);
                            </script>
                            <?php
                            exit();
                        } else {
                            GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'SALIDAS', 
                                "ID: $id_salida | Error del sistema");
                            
                            $mostrar_alerta = true;
                            $tipo_alerta = 'error';
                            $titulo_alerta = 'Error al actualizar';
                            $mensaje_alerta = str_replace("'", "\'", $resultado);
                        }
                    }
                } else {
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'SALIDAS', 
                        "ID: $id_salida | Sin materiales");
                    
                    $mostrar_alerta = true;
                    $tipo_alerta = 'warning';
                    $titulo_alerta = 'Datos incompletos';
                    $mensaje_alerta = 'Debe tener al menos un material en la salida';
                }
            }
            
            // Solo mostrar la vista si hay datos válidos
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