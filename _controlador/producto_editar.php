<?php
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('editar_producto')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'PRODUCTO', 'EDITAR');
    header("location: dashboard.php?permisos=true");
    exit;
}
//=======================================================================
// CONTROLADOR: producto_editar.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Editar Producto</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_producto.php");
            require_once("../_modelo/m_tipo_producto.php");
            require_once("../_modelo/m_tipo_material.php");
            require_once("../_modelo/m_unidad_medida.php");

            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $id_producto = $_REQUEST['id_producto'];
                $id_producto_tipo = $_REQUEST['id_producto_tipo'];
                $id_material_tipo = $_REQUEST['id_material_tipo'];
                $id_unidad_medida = $_REQUEST['id_unidad_medida'];
                $cod_material = strtoupper($_REQUEST['cod_material']);
                $nom_producto = strtoupper($_REQUEST['nom_producto']);
                $nser_producto = strtoupper($_REQUEST['nser_producto']);
                $mod_producto = strtoupper($_REQUEST['mod_producto']);
                $mar_producto = strtoupper($_REQUEST['mar_producto']);
                $det_producto = $_REQUEST['det_producto'];
                $fuc_producto = $_REQUEST['fuc_producto'];
                $fpc_producto = $_REQUEST['fpc_producto'];
                $fuo_producto = $_REQUEST['fuo_producto'];
                $fpo_producto = $_REQUEST['fpo_producto'];
                $est = isset($_REQUEST['est']) ? 1 : 0;

                // Obtener datos actuales del producto para conservar archivos existentes
                $producto_actual = ObtenerProductoPorId($id_producto);
                $dcal_producto = $producto_actual['dcal_producto'];
                $dope_producto = $producto_actual['dope_producto'];

                // Función para subir archivos
                function subirArchivo($archivo, $prefijo) {
                    if ($archivo['error'] == 0) {
                        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
                        $extensiones_permitidas = array('pdf', 'jpg', 'jpeg');
                        
                        if (in_array($extension, $extensiones_permitidas)) {
                            // Crear directorio si no existe
                            $directorio = "../_uploads/documentos/";
                            if (!file_exists($directorio)) {
                                mkdir($directorio, 0777, true);
                            }
                            
                            // Generar nombre único
                            $nombre_archivo = $prefijo . '_' . date('YmdHis') . '_' . uniqid() . '.' . $extension;
                            $ruta_completa = $directorio . $nombre_archivo;
                            
                            if (move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
                                return $nombre_archivo;
                            }
                        }
                    }
                    return '';
                }

                // Procesar archivos subidos (solo si se subieron nuevos)
                if (isset($_FILES['dcal_archivo']) && $_FILES['dcal_archivo']['size'] > 0) {
                    $nuevo_dcal = subirArchivo($_FILES['dcal_archivo'], 'calibrado');
                    if (!empty($nuevo_dcal)) {
                        $dcal_producto = $nuevo_dcal;
                    }
                }

                if (isset($_FILES['dope_archivo']) && $_FILES['dope_archivo']['size'] > 0) {
                    $nuevo_dope = subirArchivo($_FILES['dope_archivo'], 'operatividad');
                    if (!empty($nuevo_dope)) {
                        $dope_producto = $nuevo_dope;
                    }
                }

                $rpta = ActualizarProducto($id_producto, $id_producto_tipo, $id_material_tipo, $id_unidad_medida, $cod_material, $nom_producto, 
                                          $nser_producto, $mod_producto, $mar_producto, $det_producto, $fuc_producto, 
                                          $fpc_producto, $dcal_producto, $fuo_producto, $fpo_producto, $dope_producto, $est);

                if ($rpta == "SI") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'producto_mostrar.php?actualizado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'producto_mostrar.php?error=true';
                    </script>
                <?php
                } else {
                ?>
                    <script Language="JavaScript">
                        location.href = 'producto_mostrar.php?error=true';
                    </script>
                <?php
                }
            }
            //-------------------------------------------

            // Obtener ID del producto desde GET
            $id_producto = isset($_GET['id_producto']) ? $_GET['id_producto'] : '';
            if ($id_producto == "") {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos del producto a editar
            $producto_data = ObtenerProductoPorId($id_producto);
            if ($producto_data) {
                $id_producto_tipo = $producto_data['id_producto_tipo'];
                $id_material_tipo = $producto_data['id_material_tipo'];
                $id_unidad_medida = $producto_data['id_unidad_medida'];
                $cod_material = $producto_data['cod_material'];
                $nom_producto = $producto_data['nom_producto'];
                $nser_producto = $producto_data['nser_producto'];
                $mod_producto = $producto_data['mod_producto'];
                $mar_producto = $producto_data['mar_producto'];
                $det_producto = $producto_data['det_producto'];
                $fuc_producto = $producto_data['fuc_producto'];
                $fpc_producto = $producto_data['fpc_producto'];
                $dcal_producto = $producto_data['dcal_producto'];
                $fuo_producto = $producto_data['fuo_producto'];
                $fpo_producto = $producto_data['fpo_producto'];
                $dope_producto = $producto_data['dope_producto'];
                $est = ($producto_data['est_producto'] == 1) ? "checked" : "";
            } else {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos para los select
            $producto_tipos = MostrarProductoTipoActivos();
            $material_tipos = MostrarMaterialTipoActivos(); 
            $unidades_medida = MostrarUnidadMedidaActiva();

            require_once("../_vista/v_producto_editar.php");
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