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

            //=======================================================================
            // CONTROLADOR PARA INGRESO DIRECTO - CORREGIDO SIN OBSERVACIONES
            //=======================================================================
            if (isset($_REQUEST['registrar'])) {
                // Recibir datos del formulario
                $id_almacen = intval($_REQUEST['id_almacen']);
                $id_ubicacion = intval($_REQUEST['id_ubicacion']);
                $id_personal_ingreso = $id; 
                
                // Validar datos básicos
                if (empty($id_almacen) || empty($id_ubicacion)) {
                    ?>
                    <script Language="JavaScript">
                        alert('Error: Debe seleccionar almacén y ubicación.');
                        history.back();
                    </script>
                    <?php
                    exit;
                }
                
                // Procesar productos
                $productos = array();
                $errores = array();
                
                if (isset($_REQUEST['id_producto']) && is_array($_REQUEST['id_producto'])) {
                    for ($i = 0; $i < count($_REQUEST['id_producto']); $i++) {
                        $id_producto = intval($_REQUEST['id_producto'][$i]);
                        $cantidad = floatval($_REQUEST['cantidad'][$i]);
                        
                        // Validaciones
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
                
                // Validar que hay al menos un producto
                if (empty($productos)) {
                    $errores[] = "Debe agregar al menos un producto";
                }
                
                // Si hay errores, mostrarlos
                if (!empty($errores)) {
                    $mensaje_error = implode("\\n", $errores);
                    ?>
                    <script Language="JavaScript">
                        alert('Errores encontrados:\n<?php echo $mensaje_error; ?>');
                        history.back();
                    </script>
                    <?php
                    exit;
                }
                
                // Procesar el ingreso directo sin observaciones
                $resultado = ProcesarIngresoDirecto($id_almacen, $id_ubicacion, $id_personal_ingreso, $productos);
                
                if ($resultado['success']) {
                    // Mostrar mensaje de éxito con detalles
                    ?>
                    <script Language="JavaScript">
                        <?php if (isset($resultado['id_ingreso'])) { ?>
                            alert('Ingreso directo registrado correctamente.\nID de Ingreso: ING-<?php echo $resultado["id_ingreso"]; ?>\nProductos: <?php echo count($productos); ?>');
                        <?php } else { ?>
                            alert('<?php echo addslashes($resultado["message"]); ?>');
                        <?php } ?>
                        location.href = 'ingresos_mostrar.php?registrado_directo=true';
                    </script>
                    <?php
                } else {
                    ?>
                    <script Language="JavaScript">
                        alert('Error al registrar el ingreso directo:\n<?php echo addslashes($resultado["message"]); ?>');
                        history.back();
                    </script>
                    <?php
                }
                exit;
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
    ?>
</body>
</html>