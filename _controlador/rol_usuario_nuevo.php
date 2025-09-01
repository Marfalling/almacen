<?php 
require_once("../_conexion/sesion.php");

//=======================================================================
// CONTROLADOR: rol_usuario_nuevo.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Nuevo Rol</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");
            
            require_once("../_modelo/m_rol.php");

            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $nom_rol = strtoupper(trim($_REQUEST['nom_rol']));
                $est = isset($_REQUEST['est']) ? 1 : 0;
                
                // Obtener permisos seleccionados
                $permisos = array();
                if (isset($_REQUEST['permisos']) && is_array($_REQUEST['permisos'])) {
                    $permisos = $_REQUEST['permisos'];
                }
                
                // Validar que se hayan seleccionado permisos
                if (empty($permisos)) {
                ?>
                    <script Language="JavaScript">
                        location.href = 'rol_usuario_nuevo.php?sin_permisos=true';
                    </script>
                <?php
                    exit();
                }
                
                $rpta = GrabarRol($nom_rol, $permisos, $est);
                
                if ($rpta == "SI") {
            ?>
                    <script Language="JavaScript">
                        location.href = 'rol_usuario_mostrar.php?registrado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'rol_usuario_mostrar.php?existe=true';
                    </script>
            <?php
                } else {
                ?>
                    <script Language="JavaScript">
                        location.href = 'rol_usuario_mostrar.php?error=true';
                    </script>
            <?php
                }
            }
            //-------------------------------------------
            
            // Obtener módulos y acciones para los permisos
            $modulos_acciones = MostrarModulosAcciones();
            
            require_once("../_vista/v_rol_usuario_nuevo.php");
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