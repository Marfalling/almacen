<?php
//=======================================================================
// CONTROLADOR MODIFICADO: salidas_nuevo.php
//=======================================================================
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('crear_salidas')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'SALIDAS', 'CREAR');
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
    
    <title>Nueva Salida</title>
    
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

            // Cargar datos para el formulario
            $almacenes = MostrarAlmacenesActivos();
            $ubicaciones = MostrarUbicacionesActivas();
            $personal = MostrarPersonal();
            $material_tipos = MostrarMaterialTipoActivos();

            $desde_pedido = isset($_GET['desde_pedido']) ? intval($_GET['desde_pedido']) : 0;
            $items_pedido = array();
            $pedido_origen = null;
            $id_material_tipo_pedido = 0;

            if ($desde_pedido > 0) {
                require_once("../_modelo/m_pedidos.php");
                
                // Obtener datos del pedido
                $pedido_data = ConsultarPedido($desde_pedido);
                if (!empty($pedido_data)) {
                    $pedido_origen = $pedido_data[0];
                    $items_pedido = ObtenerItemsParaSalida($desde_pedido);
                    
                    // Determinar el tipo de material predominante
                    if (!empty($items_pedido)) {
                        $primer_producto = $items_pedido[0]['id_producto'];
                        $id_material_tipo_pedido = ObtenerTipoMaterialProducto($primer_producto);
                    }
                }
            }

            // Variables para mostrar alertas
            $mostrar_alerta = false;
            $tipo_alerta = '';
            $titulo_alerta = '';
            $mensaje_alerta = '';
            $redirigir_a = '';

            //=======================================================================
            // CONTROLADOR CON VALIDACIONES MEJORADAS
            //=======================================================================
            if (isset($_REQUEST['registrar'])) {
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
                
                // Usar el ID del personal de la sesión
                $id_personal = $_SESSION['id_personal'];
                
                // VALIDACIÓN 1: No permitir material tipo "NA" (id = 1)
                if ($id_material_tipo == 1) {
                    $mostrar_alerta = true;
                    $tipo_alerta = 'error';
                    $titulo_alerta = 'Tipo de material no válido';
                    $mensaje_alerta = 'No se puede realizar salidas para materiales tipo "NA". Este tipo está reservado para servicios.';
                } 
                // VALIDACIÓN 2: No permitir misma ubicación origen = destino
                elseif ($id_almacen_origen == $id_almacen_destino && $id_ubicacion_origen == $id_ubicacion_destino) {
                    $mostrar_alerta = true;
                    $tipo_alerta = 'warning';
                    $titulo_alerta = 'Ubicaciones idénticas';
                    $mensaje_alerta = 'No puede realizar una salida hacia la misma ubicación de origen. Seleccione un destino diferente.';
                }
                else {
                    // Procesar materiales
                    $materiales = array();
                    if (isset($_REQUEST['id_producto']) && is_array($_REQUEST['id_producto'])) {
                        foreach ($_REQUEST['id_producto'] as $index => $id_producto) {
                            if (!empty($id_producto) && !empty($_REQUEST['cantidad'][$index])) {
                                $cantidad = floatval($_REQUEST['cantidad'][$index]);
                                
                                // VALIDACIÓN 3: Verificar stock antes de procesar
                                $stock_disponible = ObtenerStockDisponible($id_producto, $id_almacen_origen, $id_ubicacion_origen);
                                
                                if ($stock_disponible <= 0) {
                                    $mostrar_alerta = true;
                                    $tipo_alerta = 'error';
                                    $titulo_alerta = 'Stock insuficiente';
                                    $mensaje_alerta = "El producto '{$_REQUEST['descripcion'][$index]}' no tiene stock disponible en la ubicación origen.";
                                    break;
                                } elseif ($cantidad > $stock_disponible) {
                                    $mostrar_alerta = true;
                                    $tipo_alerta = 'warning';
                                    $titulo_alerta = 'Cantidad excede stock';
                                    $mensaje_alerta = "La cantidad solicitada para '{$_REQUEST['descripcion'][$index]}' ({$cantidad}) excede el stock disponible ({$stock_disponible}).";
                                    break;
                                } else {
                                    $materiales[] = array(
                                        'id_producto' => intval($id_producto),
                                        'descripcion' => $_REQUEST['descripcion'][$index],
                                        'cantidad' => $cantidad
                                    );
                                }
                            }
                        }
                    }
                    
                    // VALIDACIÓN 4: Verificar que haya al menos un material válido
                    if (!$mostrar_alerta && count($materiales) > 0) {
                        // Capturar el ID del pedido origen si existe
                        $id_pedido_para_salida = isset($_REQUEST['id_pedido_origen']) ? intval($_REQUEST['id_pedido_origen']) : null;
                        
                        $resultado = GrabarSalida(
                            $id_material_tipo, $id_almacen_origen, $id_ubicacion_origen,
                            $id_almacen_destino, $id_ubicacion_destino, $ndoc_salida,
                            $fec_req_salida, $obs_salida, $id_personal_encargado,
                            $id_personal_recibe, $id_personal, $materiales, $id_pedido_para_salida
                        );
                        
                        if ($resultado === "SI" || (is_array($resultado) && isset($resultado['success']) && $resultado['success'] === true)) {

                            // ======================================
                            // NUEVO: SUBIDA DE DOCUMENTOS
                            // ======================================
                            $id_salida = is_array($resultado) ? $resultado['id_salida'] : mysqli_insert_id($con ?? null);
                            
                            if (isset($_FILES['documento']) && count($_FILES['documento']['name']) > 0) {
                                include_once("../_modelo/m_documentos.php");

                                $entidad = "salidas";
                                $target_dir = __DIR__ . "/../uploads/" . $entidad . "/";
                                if (!is_dir($target_dir)) {
                                    mkdir($target_dir, 0777, true);
                                }

                                foreach ($_FILES['documento']['name'] as $i => $nombre_original) {
                                    if (!empty($nombre_original)) {
                                        $nombre_archivo = $entidad . "_" . $id_salida . "_" . time() . "_" . basename($nombre_original);
                                        $target_file = $target_dir . $nombre_archivo;

                                        if (move_uploaded_file($_FILES["documento"]["tmp_name"][$i], $target_file)) {
                                            GuardarDocumento($entidad, $id_salida, $nombre_archivo, $_SESSION['id_personal']);
                                        }
                                    }
                                }
                            }
                            // ======================================

                            $id_pedido_origen = isset($_REQUEST['id_pedido_origen']) ? intval($_REQUEST['id_pedido_origen']) : 0;
                            
                            if ($id_pedido_origen > 0) {
                                require_once("../_modelo/m_pedidos.php");
                                
                                // Finalizar el pedido 
                                $resultado_pedido = FinalizarPedido($id_pedido_origen);
                                
                                if ($resultado_pedido['success']) {
                                    $mensaje_base = 'La salida se ha creado correctamente.';
                                    
                                    if (isset($resultado_pedido['ya_completado']) && $resultado_pedido['ya_completado']) {
                                        $mensaje_completo = $mensaje_base . ' El pedido ya estaba finalizado.';
                                    } else {
                                        $mensaje_completo = $mensaje_base . ' El pedido ha sido marcado como finalizado.';
                                    }
                                    
                                    $mostrar_alerta = true;
                                    $tipo_alerta = 'success';
                                    $titulo_alerta = '¡Salida registrada!';
                                    $mensaje_alerta = $mensaje_completo;
                                    $redirigir_a = 'salidas_mostrar.php?registrado=true';
                                } else {
                                    $mostrar_alerta = true;
                                    $tipo_alerta = 'warning';
                                    $titulo_alerta = 'Salida registrada con advertencia';
                                    $mensaje_alerta = 'La salida se ha creado correctamente. Advertencia: ' . $resultado_pedido["mensaje"];
                                    $redirigir_a = 'salidas_mostrar.php?registrado=true';
                                }
                            } else {
                                $mostrar_alerta = true;
                                $tipo_alerta = 'success';
                                $titulo_alerta = '¡Salida registrada!';
                                $mensaje_alerta = 'La salida se ha creado correctamente.';
                                $redirigir_a = 'salidas_mostrar.php?registrado=true';
                            }
                        } else {
                            $mostrar_alerta = true;
                            $tipo_alerta = 'error';
                            $titulo_alerta = 'Error al registrar salida';
                            $mensaje_alerta = str_replace("'", "\'", $resultado);
                        }
                    } elseif (!$mostrar_alerta) {
                        $mostrar_alerta = true;
                        $tipo_alerta = 'warning';
                        $titulo_alerta = 'Datos incompletos';
                        $mensaje_alerta = 'Debe agregar al menos un material válido a la salida';
                    }
                }
            }
            //-------------------------------------------
            
            require_once("../_vista/v_salidas_nuevo.php");
            
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
                    text: '<?php echo $mensaje_alerta; ?>',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '<?php echo ($tipo_alerta == "error") ? "#d33" : "#3085d6"; ?>',
                    allowOutsideClick: false
                }).then((result) => {
                    <?php if (!empty($redirigir_a)) { ?>
                        if (result.isConfirmed) {
                            window.location.href = '<?php echo $redirigir_a; ?>';
                        }
                    <?php } ?>
                });
            } else {
                alert('<?php echo $titulo_alerta . ": " . $mensaje_alerta; ?>');
                <?php if (!empty($redirigir_a)) { ?>
                    window.location.href = '<?php echo $redirigir_a; ?>';
                <?php } ?>
            }
        });
        </script>
        <?php
    }
    ?>
</body>
</html>