<?php
//=======================================================================
// uso_material_nuevo.php - CONTROLADOR CORREGIDO
//=======================================================================
require_once("../_conexion/sesion.php");

// VERIFICACIÓN DE PERMISOS
if (!verificarPermisoEspecifico('crear_uso de material')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'USO DE MATERIAL', 'CREAR');
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

    <title>Nuevo Uso de Material</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_uso_material.php");
            require_once("../_modelo/m_almacen.php");
            require_once("../_modelo/m_ubicacion.php");
            require_once("../_modelo/m_personal.php");

            // Cargar datos para el formulario
            $almacenes = MostrarAlmacenesActivos();
            $ubicaciones = MostrarUbicacionesActivas();
            $personal = MostrarPersonal();
            
            // Crear directorio de archivos si no existe
            if (!file_exists("../_archivos/uso_material/")) {
                mkdir("../_archivos/uso_material/", 0777, true);
            }
            
            // Variables para mostrar alertas
            $mostrar_alerta = false;
            $tipo_alerta = '';
            $titulo_alerta = '';
            $mensaje_alerta = '';
            
            //=======================================================================
            // CONTROLADOR
            //=======================================================================
            if (isset($_REQUEST['registrar'])) {
                // Recibir datos del formulario
                $id_almacen = intval($_REQUEST['id_almacen']);
                $id_ubicacion = intval($_REQUEST['id_ubicacion']);
                $id_solicitante = intval($_REQUEST['id_solicitante']);

                // Procesar materiales
                $materiales = array();
                if (isset($_REQUEST['id_producto']) && is_array($_REQUEST['id_producto'])) {
                    for ($i = 0; $i < count($_REQUEST['id_producto']); $i++) {
                        if (!empty($_REQUEST['id_producto'][$i])) {
                            $materiales[] = array(
                                'id_producto' => $_REQUEST['id_producto'][$i],
                                'cantidad' => $_REQUEST['cantidad'][$i],
                                'observaciones' => $_REQUEST['observaciones'][$i]
                            );
                        }
                    }
                }

                // Procesar archivos
                $archivos_subidos = array();
                foreach ($_FILES as $key => $file) {
                    if (strpos($key, 'archivos_') === 0) {
                        $index = str_replace('archivos_', '', $key);
                        $archivos_subidos[$index] = $file;
                    }
                }

                // Validar que hay materiales
                if (empty($materiales)) {
                    $mostrar_alerta = true;
                    $tipo_alerta = 'warning';
                    $titulo_alerta = 'Datos incompletos';
                    $mensaje_alerta = 'Debe agregar al menos un material para continuar.';
                } else {
                    $rpta = GrabarUsoMaterial($id_almacen, $id_ubicacion, $id_solicitante, 
                                           $id, $materiales, $archivos_subidos);

                    if ($rpta == "SI") {
                        ?>
                        <script Language="JavaScript">
                            // Usar setTimeout para asegurar que la página se carga completamente
                            setTimeout(function() {
                                window.location.href = 'uso_material_mostrar.php?registrado=true';
                            }, 100);
                        </script>
                        <?php
                        exit; // Detener ejecución después del éxito
                    } else {
                        $mostrar_alerta = true;
                        $tipo_alerta = 'error';
                        $titulo_alerta = 'Error al registrar';
                        $mensaje_alerta = str_replace("'", "\'", $rpta); // Escapar comillas simples
                        // NO continuar con el procesamiento cuando hay error
                    }
                }
            }
            //-------------------------------------------

            require_once("../_vista/v_uso_material_nuevo.php");
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
            // Asegurar que SweetAlert está disponible
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: '<?php echo $tipo_alerta; ?>',
                    title: '<?php echo $titulo_alerta; ?>',
                    text: '<?php echo $mensaje_alerta; ?>',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '<?php echo ($tipo_alerta == "error") ? "#d33" : "#3085d6"; ?>'
                });
            } else {
                // Fallback en caso de que SweetAlert no esté disponible
                alert('<?php echo $titulo_alerta . ": " . $mensaje_alerta; ?>');
            }
        });
        </script>
        <?php
    }
    ?>
</body>
</html>