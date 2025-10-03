<?php
// ====================================================================
// CONTROLADORES DE SEGURIDAD PARA DEVOLUCIONES
// ====================================================================
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('crear_devoluciones')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'DEVOLUCIONES', 'CREAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Nueva Devolución</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");
            
            require_once("../_conexion/conexion.php");
            require_once("../_modelo/m_devolucion.php");
            require_once("../_modelo/m_almacen.php");
            require_once("../_modelo/m_ubicacion.php");
            require_once("../_modelo/m_personal.php");
            require_once("../_modelo/m_producto.php");
            require_once("../_modelo/m_clientes.php"); // <--- Nuevo para clientes

            // Cargar datos para el formulario
            $almacenes = MostrarAlmacenesActivos();
            $ubicaciones = MostrarUbicacionesActivas();
            $personal = MostrarPersonalActivo();
            $productos = MostrarMaterialesActivos();
            $clientes = MostrarClientesActivos(); // <--- Lista de clientes

            // Variables para alertas
            $mostrar_alerta = false;
            $tipo_alerta = '';
            $titulo_alerta = '';
            $mensaje_alerta = '';

            //=======================================================================
            // CONTROLADOR
            //=======================================================================
            if (isset($_REQUEST['registrar'])) {
                $id_almacen   = intval($_REQUEST['id_almacen']);
                $id_ubicacion = intval($_REQUEST['id_ubicacion']);
                $obs_devolucion = mysqli_real_escape_string($con, $_REQUEST['obs_devolucion']);
                
                // Usar el ID del personal en sesión
                $id_personal = $_SESSION['id_personal'];

                // Capturar cliente destino
                $id_cliente_destino = isset($_REQUEST['id_cliente_destino']) ? intval($_REQUEST['id_cliente_destino']) : null;

                // Validación obligatoria de cliente
                if (!$id_cliente_destino) {
                    $mostrar_alerta = true;
                    $tipo_alerta = 'warning';
                    $titulo_alerta = 'Datos incompletos';
                    $mensaje_alerta = 'Debe seleccionar un cliente destino';
                } else {
                    // Procesar materiales
                    $materiales = array();
                    if (isset($_REQUEST['id_producto']) && is_array($_REQUEST['id_producto'])) {
                        foreach ($_REQUEST['id_producto'] as $index => $id_producto) {
                            if (!empty($id_producto) && !empty($_REQUEST['cantidad'][$index])) {
                                $materiales[] = array(
                                    'id_producto' => intval($id_producto),
                                    'cantidad'    => floatval($_REQUEST['cantidad'][$index]),
                                    'detalle'     => mysqli_real_escape_string($con, $_REQUEST['descripcion'][$index])
                                );
                            }
                        }
                    }
                    
                    // Validación materiales
                    if (count($materiales) > 0) {
                        // <--- Se agrega $id_cliente_destino como nuevo parámetro
                        $resultado = GrabarDevolucion($id_almacen, $id_ubicacion, $id_personal, $id_cliente_destino, $obs_devolucion, $materiales);
                        
                        if ($resultado === "SI") {
                            ?>
                            <script Language="JavaScript">
                                setTimeout(function() {
                                    window.location.href = 'devoluciones_mostrar.php?registrado=true';
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
                    } else {
                        $mostrar_alerta = true;
                        $tipo_alerta = 'warning';
                        $titulo_alerta = 'Datos incompletos';
                        $mensaje_alerta = 'Debe agregar al menos un material a la devolución';
                    }
                }
            }
            //-------------------------------------------
            
            require_once("../_vista/v_devolucion_nuevo.php");
            
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