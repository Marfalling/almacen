<?php
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('editar_rol de usuario')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'ROL_USUARIO', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

// Verificar si el usuario actual es SUPERADMIN
$es_superadmin = esSuperAdmin($id);

//=======================================================================
// CONTROLADOR: rol_usuario_editar.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Editar Rol</title>
    
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
                $id_rol = $_REQUEST['id_rol'];
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
                        location.href = 'rol_usuario_editar.php?id_rol=<?php echo $id_rol; ?>&sin_permisos=true';
                    </script>
                <?php
                    exit();
                }

                $rpta = EditarRol($id_rol, $nom_rol, $permisos, $est);

                if ($rpta == "SI") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'rol_usuario_mostrar.php?actualizado=true';
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

            // Obtener ID del rol desde GET
            $id_rol = isset($_GET['id_rol']) ? $_GET['id_rol'] : '';
            if ($id_rol == "") {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos del rol a editar
            $rol_data = ObtenerRol($id_rol);
            if ($rol_data) {
                $nom_rol = $rol_data['nom_rol'];
                $est = ($rol_data['est_rol'] == 1) ? "checked" : "";
                $permisos_rol = $rol_data['permisos'];
                
                // Crear array de permisos para facilitar la verificación
                $permisos_asignados = array();
                foreach ($permisos_rol as $permiso) {
                    $permisos_asignados[] = $permiso['id_modulo_accion'];
                }
            } else {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }
            
            // Obtener módulos y acciones para los permisos - pasar $es_superadmin
            $modulos_acciones = MostrarModulosAcciones($es_superadmin);

            require_once("../_vista/v_rol_usuario_editar.php");
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