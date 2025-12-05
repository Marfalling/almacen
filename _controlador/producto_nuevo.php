<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

if (!verificarPermisoEspecifico('crear_producto')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'PRODUCTO', 'CREAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}
//=======================================================================
// CONTROLADOR: producto_nuevo.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Nuevo Producto</title>

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
            
            // Obtener datos para los select
            $producto_tipos = MostrarProductoTipoActivos();
            $material_tipos = MostrarMaterialTipoActivos(); 
            $unidades_medida = MostrarUnidadMedidaActiva();

            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
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

                // Función para subir archivos
                function subirArchivo($archivo, $prefijo) {
                    if ($archivo['error'] == 0) {
                        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
                        $extensiones_permitidas = array('pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx');
                        
                        if (in_array($extension, $extensiones_permitidas)) {
                            $directorio = "../_uploads/documentos/";
                            if (!file_exists($directorio)) {
                                mkdir($directorio, 0755, true);
                            }
                            
                            $prefijo_corto = '';
                            if ($prefijo === 'calibrado') {
                                $prefijo_corto = 'cal';
                            } elseif ($prefijo === 'operatividad') {
                                $prefijo_corto = 'ope';
                            } elseif ($prefijo === 'homologacion') {
                                $prefijo_corto = 'hom';
                            } else {
                                $prefijo_corto = substr($prefijo, 0, 3);
                            }

                            $timestamp = date('YmdHis');
                            $random = substr(md5(uniqid(rand(), true)), 0, 4);
                            
                            $nombre_archivo = $prefijo_corto . '_' . $timestamp . '_' . $random . '.' . $extension;
                            $ruta_completa = $directorio . $nombre_archivo;
                            
                            $contador = 1;
                            while (file_exists($ruta_completa) && $contador < 10) {
                                $nombre_sin_ext = pathinfo($nombre_archivo, PATHINFO_FILENAME);
                                $nombre_archivo = $nombre_sin_ext . $contador . '.' . $extension;
                                $ruta_completa = $directorio . $nombre_archivo;
                                $contador++;
                            }
                            
                            if (move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
                                return $nombre_archivo;
                            }
                        }
                    }
                    return '';
                }
                
                // Procesar archivos subidos
                $hom_producto = '';
                $dcal_producto = '';
                $dope_producto = '';

                if (isset($_FILES['hom_archivo']) && $_FILES['hom_archivo']['size'] > 0) {
                    $hom_producto = subirArchivo($_FILES['hom_archivo'], 'homologacion');
                }

                if (isset($_FILES['dcal_archivo']) && $_FILES['dcal_archivo']['size'] > 0) {
                    $dcal_producto = subirArchivo($_FILES['dcal_archivo'], 'calibrado');
                }

                if (isset($_FILES['dope_archivo']) && $_FILES['dope_archivo']['size'] > 0) {
                    $dope_producto = subirArchivo($_FILES['dope_archivo'], 'operatividad');
                }

                $rpta = GrabarProducto($id_producto_tipo, $id_material_tipo, $id_unidad_medida, $cod_material, $nom_producto, 
                                      $nser_producto, $mod_producto, $mar_producto, $det_producto, $hom_producto,
                                      $fuc_producto, $fpc_producto, $dcal_producto, $fuo_producto, $fpo_producto, 
                                      $dope_producto, $est);

                if ($rpta == "SI") {
                    //  AUDITORÍA: REGISTRO EXITOSO
                    $estado_texto = ($est == 1) ? 'Activo' : 'Inactivo';
                    
                    //  OBTENER NOMBRES
                    $tipo_prod_data = ObtenerProductoTipo($id_producto_tipo);
                    $tipo_mat_data = ObtenerMaterialTipo($id_material_tipo);
                    $unid_med_data = ObtenerUnidadMedida($id_unidad_medida);
                    
                    $nom_tipo_producto = $tipo_prod_data ? $tipo_prod_data['nom_producto_tipo'] : '';
                    $nom_tipo_material = $tipo_mat_data ? $tipo_mat_data['nom_material_tipo'] : '';
                    $nom_unidad = $unid_med_data ? $unid_med_data['nom_unidad_medida'] : '';
                    
                    $descripcion = "Código: '$cod_material' | Nombre: '$nom_producto' | Tipo: '$nom_tipo_producto' | Material: '$nom_tipo_material' | Unidad: '$nom_unidad' | Estado: $estado_texto";
                    GrabarAuditoria($id, $usuario_sesion, 'REGISTRAR', 'PRODUCTO', $descripcion);
            ?>
                    <script Language="JavaScript">
                        location.href = 'producto_mostrar.php?registrado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                    //  AUDITORÍA: ERROR - YA EXISTE
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'PRODUCTO', "Código: '$cod_material' | Nombre: '$nom_producto' - Ya existe");
                ?>
                    <script Language="JavaScript">
                        location.href = 'producto_mostrar.php?existe=true';
                    </script>
            <?php
                } else {
                    //  AUDITORÍA: ERROR GENERAL
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'PRODUCTO', "Código: '$cod_material' | Nombre: '$nom_producto' - Error del sistema");
                ?>
                    <script Language="JavaScript">
                        location.href = 'producto_mostrar.php?error=true';
                    </script>
            <?php
                }
            }
            //-------------------------------------------

            require_once("../_vista/v_producto_nuevo.php");
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