<?php
// ====================================================================
// CONTROLADORES DE SEGURIDAD PARA DEVOLUCIONES
// ====================================================================
require_once("../_conexion/sesion.php");
require_once("../_conexion/conexion.php");
require_once("../_modelo/m_auditoria.php"); 

if (!verificarPermisoEspecifico('crear_devoluciones')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'DEVOLUCION', 'CREAR');
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
            require_once("../_modelo/m_producto.php");
            require_once("../_modelo/m_devolucion.php");
            require_once("../_modelo/m_almacen.php");
            require_once("../_modelo/m_ubicacion.php");
            require_once("../_modelo/m_personal.php");
            require_once("../_modelo/m_producto.php");
            require_once("../_modelo/m_clientes.php"); // <--- Nuevo para clientes

            // Cargar datos para el formulario
            $almacenes = MostrarAlmacenesActivos();
            $ubicaciones = MostrarUbicacionesActivas();
            $personal = MostrarPersonal();
            $productos = MostrarMaterialesActivos();
            $clientes = MostrarClientesActivos(); // <--- Lista de clientes

            // Variables para alertas
            $mostrar_alerta = false;
            $tipo_alerta = '';
            $titulo_alerta = '';
            $mensaje_alerta = '';

            //=======================================================================
            // OPERACIÓN DE REGISTRO
            //=======================================================================
            if (isset($_REQUEST['registrar'])) {
                $id_almacen   = intval($_REQUEST['id_almacen']);
                $id_ubicacion = intval($_REQUEST['id_ubicacion']);
                $obs_devolucion = mysqli_real_escape_string($con, $_REQUEST['obs_devolucion']);
                
                // Usar el ID del personal en sesión
                $id_personal = $_SESSION['id_personal'];

                // Capturar cliente destino
                $cliente_destino = ObtenerClientePorAlmacen($id_almacen);
                $id_cliente_destino = $cliente_destino['id_cliente'] ?? 0;

                // Validación obligatoria de cliente
                if (!$id_cliente_destino) {
                    $mostrar_alerta = true;
                    $tipo_alerta = 'warning';
                    $titulo_alerta = 'Datos incompletos';
                    $mensaje_alerta = 'Debe seleccionar un cliente destino';
                } else {
                    // Procesar materiales
                    $materiales = array();
                    $errores_stock = array(); // NUEVO: Array para errores de stock
                    
                    if (isset($_REQUEST['id_producto']) && is_array($_REQUEST['id_producto'])) {
                        foreach ($_REQUEST['id_producto'] as $index => $id_producto) {
                            if (!empty($id_producto) && !empty($_REQUEST['cantidad'][$index])) {
                                $id_prod = intval($id_producto);
                                $cantidad = floatval($_REQUEST['cantidad'][$index]);

                                require_once("../_modelo/m_stock.php");
                                
                                // VALIDAR STOCK DISPONIBLE
                                $stock_actual = ObtenerStockActual($id_prod, $id_almacen, $id_ubicacion);
                                
                                if ($cantidad > $stock_actual) {
                                    // Obtener nombre del producto para el mensaje
                                    $sql_prod = "SELECT nom_producto FROM producto WHERE id_producto = $id_prod";
                                    $res_prod = mysqli_query($con, $sql_prod);
                                    $row_prod = mysqli_fetch_assoc($res_prod);
                                    $nombre_prod = $row_prod['nom_producto'] ?? "Producto ID: $id_prod";
                                    
                                    $errores_stock[] = "$nombre_prod: solicita $cantidad pero solo hay $stock_actual disponible";
                                } else {
                                    // Si hay stock suficiente, agregar al array
                                    $materiales[] = array(
                                        'id_producto' => $id_prod,
                                        'cantidad'    => $cantidad,
                                        'detalle'     => mysqli_real_escape_string($con, $_REQUEST['descripcion'][$index])
                                    );
                                }
                            }
                        }
                    }
                    
                    // ⭐ VERIFICAR SI HAY ERRORES DE STOCK
                    if (count($errores_stock) > 0) {
                        $mostrar_alerta = true;
                        $tipo_alerta = 'error';
                        $titulo_alerta = 'Stock insuficiente';
                        $mensaje_alerta = implode('<br>', $errores_stock);
                    }
                    // Validación materiales
                    elseif (count($materiales) > 0) {
                        $resultado = GrabarDevolucion($id_almacen, $id_ubicacion, $id_personal, $obs_devolucion, $materiales, $id_cliente_destino);
                        
                        if ($resultado === "SI") {
                            $nom_almacen = '';
                            foreach ($almacenes as $alm) {
                                if ($alm['id_almacen'] == $id_almacen) {
                                    $nom_almacen = $alm['nom_almacen'];
                                    break;
                                }
                            }

                            // Obtener nombre del cliente
                            $nom_cliente = $cliente_destino['nom_cliente'] ?? '';

                            //  CONSTRUIR DETALLE DE PRODUCTOS
                            $productos_detalle = [];
                            foreach ($materiales as $mat) {
                                //  USAR FUNCIÓN DEL MODELO
                                $producto_data = ObtenerProductoPorId($mat['id_producto']);
                                $nom_producto = $producto_data ? $producto_data['nom_producto'] : "ID:" . $mat['id_producto'];
                                
                                $desc_corta = (strlen($nom_producto) > 30) 
                                    ? substr($nom_producto, 0, 30) . '...' 
                                    : $nom_producto;
                                
                                $productos_detalle[] = "$desc_corta (Cant: {$mat['cantidad']})";
                            }
                            $desc_productos = implode(' | ', $productos_detalle);

                            $descripcion = "Almacén: '$nom_almacen' | Cliente: '$nom_cliente' | Productos: $desc_productos";
                            GrabarAuditoria($id, $usuario_sesion, 'REGISTRAR', 'DEVOLUCION', $descripcion);
                            ?>
                            <script Language="JavaScript">
                                setTimeout(function() {
                                    window.location.href = 'devoluciones_mostrar.php?registrado=true';
                                }, 100);
                            </script>
                            <?php
                            exit();
                        } else {
                            //  AUDITORÍA: ERROR AL REGISTRAR
                            GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'DEVOLUCION', "Almacén: $id_almacen");
                            
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
                alert('<?php echo $titulo_alerta . ": " . strip_tags($mensaje_alerta); ?>');
            }
        });
        </script>
        <?php
    }
    ?>
</body>
</html>