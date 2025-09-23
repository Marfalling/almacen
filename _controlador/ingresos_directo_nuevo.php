<?php
require_once("../_conexion/sesion.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Nuevo Ingreso Directo</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_ingreso.php");
            require_once("../_modelo/m_almacen.php");
            require_once("../_modelo/m_ubicacion.php");

            // Cargar datos para el formulario
            $almacenes = MostrarAlmacenesActivos();
            $ubicaciones = MostrarUbicacionesActivas();

            // Variables para alertas
            $mostrar_alerta = false;
            $tipo_alerta = '';
            $titulo_alerta = '';
            $mensaje_alerta = '';

            //=======================================================================
            // CONTROLADOR PARA INGRESO DIRECTO
            //=======================================================================
            if (isset($_REQUEST['registrar'])) {
                // Recibir datos del formulario
                $id_almacen = intval($_REQUEST['id_almacen']);
                $id_ubicacion = intval($_REQUEST['id_ubicacion']);
                $id_personal_ingreso = $id; 
                
                // Validar datos básicos
                if (empty($id_almacen) || empty($id_ubicacion)) {
                    $mostrar_alerta = true;
                    $tipo_alerta = 'error';
                    $titulo_alerta = 'Error de Validación';
                    $mensaje_alerta = 'Debe seleccionar almacén y ubicación.';
                } else {
                    // Procesar productos
                    $productos = array();
                    $errores = array();
                    
                    if (isset($_REQUEST['id_producto']) && is_array($_REQUEST['id_producto'])) {
                        for ($i = 0; $i < count($_REQUEST['id_producto']); $i++) {
                            $id_producto = intval($_REQUEST['id_producto'][$i]);
                            $cantidad = floatval($_REQUEST['cantidad'][$i]);
                            
                            if (empty($id_producto)) {
                                $errores[] = "Producto en posición " . ($i + 1) . " no seleccionado";
                                continue;
                            }
                            
                            if ($cantidad <= 0) {
                                $errores[] = "Cantidad inválida en producto " . ($i + 1);
                                continue;
                            }
                            
                            $productos[] = array(
                                'id_producto' => $id_producto,
                                'cantidad' => $cantidad
                            );
                        }
                    }
                    
                    if (empty($productos)) {
                        $errores[] = "Debe agregar al menos un producto";
                    }
                    
                    if (!empty($errores)) {
                        $mostrar_alerta = true;
                        $tipo_alerta = 'error';
                        $titulo_alerta = 'Errores de Validación';
                        $mensaje_alerta = implode("\\n• ", $errores);
                    } else {
                        // Procesar el ingreso directo
                        $resultado = ProcesarIngresoDirecto($id_almacen, $id_ubicacion, $id_personal_ingreso, $productos);
                        
                        if ($resultado['success']) {
                            // Redirección exitosa con JavaScript inmediato
                            ?>
                            <script Language="JavaScript">
                                setTimeout(function() {
                                    window.location.href = 'ingresos_mostrar.php?tab=todos-ingresos&registrado_directo=true&id_ingreso=<?php echo $resultado["id_ingreso"]; ?>';
                                }, 100);
                            </script>
                            <?php
                            exit();
                        } else {
                            $mostrar_alerta = true;
                            $tipo_alerta = 'error';
                            $titulo_alerta = 'Error al Registrar Ingreso';
                            $mensaje_alerta = str_replace("'", "\'", $resultado['message']);
                        }
                    }
                }
            }
            //-------------------------------------------

            require_once("../_vista/v_ingresos_nuevo_directo.php");
            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>

    <?php
    require_once("../_vista/v_script.php");
    require_once("../_vista/v_alertas.php");

    if ($mostrar_alerta) {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof mostrarAlerta === 'function') {
                mostrarAlerta('<?php echo $tipo_alerta; ?>', '<?php echo $titulo_alerta; ?>', '<?php echo $mensaje_alerta; ?>');
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: '<?php echo $tipo_alerta; ?>',
                    title: '<?php echo $titulo_alerta; ?>',
                    text: '<?php echo str_replace("\\n• ", "\n• ", $mensaje_alerta); ?>',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '<?php echo ($tipo_alerta == "error") ? "#d33" : "#3085d6"; ?>'
                });
            } else {
                alert('<?php echo $titulo_alerta . ": " . str_replace("\\n• ", "\n• ", $mensaje_alerta); ?>');
            }
        });
        </script>
        <?php
    }
    ?>
</body>
</html>