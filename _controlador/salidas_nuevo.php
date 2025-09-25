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
            $personal = MostrarPersonalActivo();
            $material_tipos = MostrarMaterialTipoActivos();

            // Variables para mostrar alertas
            $mostrar_alerta = false;
            $tipo_alerta = '';
            $titulo_alerta = '';
            $mensaje_alerta = '';

            //=======================================================================
            // CONTROLADOR CON VALIDACIONES MEJORADAS
            //=======================================================================
            if (isset($_REQUEST['registrar'])) {
                $id_material_tipo = intval($_REQUEST['id_material_tipo']);
                $id_almacen_origen = intval($_REQUEST['id_almacen_origen']);
                $id_ubicacion_origen = intval($_REQUEST['id_ubicacion_origen']);
                $id_almacen_destino = intval($_REQUEST['id_almacen_destino']);
                $id_ubicacion_destino = intval($_REQUEST['id_ubicacion_destino']);
                $ndoc_salida = mysqli_real_escape_string($con, $_REQUEST['ndoc_salida']);
                $fec_req_salida = $_REQUEST['fec_req_salida'];
                $obs_salida = mysqli_real_escape_string($con, $_REQUEST['obs_salida']);
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
                                        'descripcion' => mysqli_real_escape_string($con, $_REQUEST['descripcion'][$index]),
                                        'cantidad' => $cantidad
                                    );
                                }
                            }
                        }
                    }
                    
                    // VALIDACIÓN 4: Verificar que haya al menos un material válido
                    if (!$mostrar_alerta && count($materiales) > 0) {
                        $resultado = GrabarSalida(
                            $id_material_tipo, $id_almacen_origen, $id_ubicacion_origen,
                            $id_almacen_destino, $id_ubicacion_destino, $ndoc_salida,
                            $fec_req_salida, $obs_salida, $id_personal_encargado,
                            $id_personal_recibe, $id_personal, $materiales
                        );
                        
                        if ($resultado === "SI") {
                            ?>
                            <script Language="JavaScript">
                                setTimeout(function() {
                                    window.location.href = 'salidas_mostrar.php?registrado=true';
                                }, 100);
                            </script>
                            <?php
                            exit();
                        } else {
                            $mostrar_alerta = true;
                            $tipo_alerta = 'error';
                            $titulo_alerta = 'Error al registrar';
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
                    confirmButtonColor: '<?php echo ($tipo_alerta == "error") ? "#d33" : "#3085d6"; ?>'
                });
            } else {
                alert('<?php echo $titulo_alerta . ": " . $mensaje_alerta; ?>');
            }
        });
        </script>
        <?php
    }
    ?>
</body>
</html>