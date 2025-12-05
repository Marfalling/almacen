<?php
// ====================================================================
// INGRESOS DIRECTO - CREAR (ingresos_directo_nuevo.php)
// ====================================================================

require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

if (!verificarPermisoEspecifico('crear_ingresos')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'INGRESOS', 'CREAR DIRECTO');
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

    <title>Nuevo Ingreso Directo</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");
            require_once("../_modelo/m_producto.php"); 
            require_once("../_modelo/m_ingreso.php");
            require_once("../_modelo/m_almacen.php");
            require_once("../_modelo/m_ubicacion.php");

            // Cargar datos para el formulario
            //$almacenes = MostrarAlmacenesActivos();
            $almacenes = MostrarAlmacenesActivosConArceBase();
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
                            $id_ingreso = $resultado['id_ingreso'];
                            
                            // 🔹 PROCESAR DOCUMENTOS ADJUNTOS (igual que en salidas)
                            if (isset($_FILES['documento']) && !empty($_FILES['documento']['name'][0])) {
                                include_once("../_modelo/m_documentos.php");

                                $entidad = "ingreso_directo";
                                $target_dir = __DIR__ . "/../uploads/" . $entidad . "/";
                                
                                if (!is_dir($target_dir)) {
                                    mkdir($target_dir, 0777, true);
                                }

                                foreach ($_FILES['documento']['name'] as $i => $nombre_original) {
                                    if (!empty($nombre_original) && $_FILES['documento']['error'][$i] == 0) {
                                        // Validar tamaño (5MB máximo)
                                        if ($_FILES['documento']['size'][$i] > 5242880) {
                                            continue; // Saltar archivos muy grandes
                                        }
                                        
                                        $nombre_limpio = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $nombre_original);
                                        if ($nombre_limpio === false || trim($nombre_limpio) === '') {
                                            $nombre_limpio = $nombre_original;
                                        }

                                        $nombre_limpio = preg_replace('/[^A-Za-z0-9._-]/', '_', $nombre_limpio);
                                        $nombre_limpio = trim($nombre_limpio, '_');

                                        $nombre_archivo = $entidad . "_" . $id_ingreso . "_" . time() . "_" . basename($nombre_limpio);
                                        $target_file = $target_dir . $nombre_archivo;

                                        if (move_uploaded_file($_FILES["documento"]["tmp_name"][$i], $target_file)) {
                                            GuardarDocumento($entidad, $id_ingreso, $nombre_archivo, $_SESSION['id_personal']);
                                        }
                                    }
                                }
                            }
                            
                            $nom_almacen = '';
                            foreach ($almacenes as $alm) {
                                if ($alm['id_almacen'] == $id_almacen) {
                                    $nom_almacen = $alm['nom_almacen'];
                                    break;
                                }
                            }

                            $nom_ubicacion = '';
                            foreach ($ubicaciones as $ubi) {
                                if ($ubi['id_ubicacion'] == $id_ubicacion) {
                                    $nom_ubicacion = $ubi['nom_ubicacion'];
                                    break;
                                }
                            }

                            //  CONSTRUIR DETALLE DE PRODUCTOS CON CANTIDADES
                            $productos_detalle = [];
                            foreach ($productos as $prod) {
                                //  USAR FUNCIÓN DEL MODELO
                                $producto_data = ObtenerProductoPorId($prod['id_producto']);
                                $nom_producto = $producto_data ? $producto_data['nom_producto'] : "ID:" . $prod['id_producto'];
                                
                                $desc_corta = (strlen($nom_producto) > 30) 
                                    ? substr($nom_producto, 0, 30) . '...' 
                                    : $nom_producto;
                                
                                $productos_detalle[] = "$desc_corta (Cant: {$prod['cantidad']})";
                            }
                            $desc_productos = implode(' | ', $productos_detalle);

                            $descripcion = "ID: $id_ingreso | Almacén: '$nom_almacen' | Ubicación: '$nom_ubicacion' | Productos: $desc_productos";
                            GrabarAuditoria($id, $usuario_sesion, 'REGISTRAR', 'INGRESO DIRECTO', $descripcion);
                            
                            ?>
                            <script Language="JavaScript">
                                location.href = 'ingresos_mostrar.php?tab=todos-ingresos&registrado=true';
                            </script>
                            <?php
                            exit();
                        } else {
                            //  AUDITORÍA: ERROR AL REGISTRAR
                            GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'INGRESO DIRECTO', "Almacén: $id_almacen - " . substr($resultado['message'], 0, 100));
                            
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