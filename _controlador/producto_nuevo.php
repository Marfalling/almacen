<?php
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('crear_producto')) {
    require_once("../_modelo/m_auditoria.php");
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
                        // Crear directorio si no existe
                        $directorio = "../_uploads/documentos/";
                        if (!file_exists($directorio)) {
                            mkdir($directorio, 0755, true);
                        }
                        
                        // Generar nombre optimizado (máximo 35 caracteres)
                        $prefijo_corto = ($prefijo === 'calibrado') ? 'cal' : 'ope';
                        $timestamp = date('YmdHis'); // 14 caracteres
                        $random = substr(md5(uniqid(rand(), true)), 0, 4); // 4 caracteres
                        
                        // Estructura: prefijo(3) + _ + timestamp(14) + _ + random(4) + .ext = ~25-30 caracteres
                        $nombre_archivo = $prefijo_corto . '_' . $timestamp . '_' . $random . '.' . $extension;
                        $ruta_completa = $directorio . $nombre_archivo;
                        
                        // Verificar que no existe (muy improbable)
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
                $dcal_producto = '';
                $dope_producto = '';

                if (isset($_FILES['dcal_archivo']) && $_FILES['dcal_archivo']['size'] > 0) {
                    $dcal_producto = subirArchivo($_FILES['dcal_archivo'], 'calibrado');
                }

                if (isset($_FILES['dope_archivo']) && $_FILES['dope_archivo']['size'] > 0) {
                    $dope_producto = subirArchivo($_FILES['dope_archivo'], 'operatividad');
                }

                $rpta = GrabarProducto($id_producto_tipo, $id_material_tipo, $id_unidad_medida, $cod_material, $nom_producto, 
                                      $nser_producto, $mod_producto, $mar_producto, $det_producto, $fuc_producto, 
                                      $fpc_producto, $dcal_producto, $fuo_producto, $fpo_producto, $dope_producto, $est);

                if ($rpta == "SI") {
            ?>
                    <script Language="JavaScript">
                        location.href = 'producto_mostrar.php?registrado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'producto_mostrar.php?existe=true';
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