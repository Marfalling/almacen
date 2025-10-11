<?php
//=======================================================================
// uso_material_editar.php - CONTROLADOR CORREGIDO
//=======================================================================
require_once("../_conexion/sesion.php");

// VERIFICACIÓN DE PERMISOS
if (!verificarPermisoEspecifico('editar_uso de material')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'USO DE MATERIAL', 'EDITAR');
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

    <title>Editar Uso de Material</title>

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
            
            // Cargar datos necesarios para el formulario
            $ubicaciones = MostrarUbicacionesActivas();
            $personal = MostrarPersonal();
            
            // Crear directorio de archivos si no existe
            if (!file_exists("../_archivos/uso_material/")) {
                mkdir("../_archivos/uso_material/", 0777, true);
            }

            $id_uso_material = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

            //=======================================================================
            // CONTROLADOR
            //=======================================================================
            if (isset($_REQUEST['actualizar'])) {
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
                                'observaciones' => $_REQUEST['observaciones'][$i],
                                'id_detalle' => isset($_REQUEST['id_detalle'][$i]) ? $_REQUEST['id_detalle'][$i] : 0
                            );
                        }
                    }
                }

                // Procesar archivos
                $archivos_subidos = array();
                foreach ($_FILES as $key => $file) {
                    if (strpos($key, 'archivos_') === 0 && !empty($file['name'][0])) {
                        $index = str_replace('archivos_', '', $key);
                        $archivos_subidos[$index] = $file;
                    }
                }

                // Validar que hay materiales
                if (empty($materiales)) {
                    ?>
                    <script Language="JavaScript">
                        alert('Debe agregar al menos un material.');
                    </script>
                    <?php
                } else {
                    $rpta = ActualizarUsoMaterial($id_uso_material, $id_ubicacion, $id_solicitante, 
                                                $materiales, $archivos_subidos);

                    if ($rpta == "SI") {
                        ?>
                        <script Language="JavaScript">
                            location.href = 'uso_material_mostrar.php?actualizado=true';
                        </script>
                        <?php
                    } else {
                        ?>
                        <script Language="JavaScript">
                            alert('Error al actualizar el uso de material: <?php echo $rpta; ?>');
                        </script>
                        <?php
                    }
                }
            }
            //-------------------------------------------

            if ($id_uso_material > 0) {
                // Cargar datos del uso de material
                $uso_material_data = ConsultarUsoMaterial($id_uso_material);
                $uso_material_detalle = ConsultarUsoMaterialDetalle($id_uso_material);
                
                if (!empty($uso_material_data)) {
                    require_once("../_vista/v_uso_material_editar.php");
                } else {
                    echo "<script>alert('Uso de material no encontrado'); location.href='uso_material_mostrar.php';</script>";
                }
            } else {
                echo "<script>alert('ID de uso de material no válido'); location.href='uso_material_mostrar.php';</script>";
            }

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