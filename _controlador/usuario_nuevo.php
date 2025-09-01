<?php

require_once("../_modelo/m_usuario.php");
require_once("../_modelo/m_rol.php");
require_once("../_conexion/sesion.php");

// Verificar permiso para crear usuarios
if (!verificarPermisoEspecifico('reg_usuario')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'USUARIO', 'NUEVO');
    header("location: dashboard.php?permisos=true");
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

    <title>Nuevo Usuario</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            if (isset($_REQUEST['registrar'])) {
                $id_personal = $_REQUEST['id_personal'];
                $usu = trim($_REQUEST['user']);
                $pass = $_REQUEST['pass'];
                $est = isset($_REQUEST['est']) ? 1 : 0;
                
                // Obtener roles seleccionados
                $roles = array();
                if (isset($_REQUEST['roles']) && is_array($_REQUEST['roles'])) {
                    $roles = $_REQUEST['roles'];
                }

                $rpta = GrabarUsuario($id_personal, $usu, $pass, $est, $roles);

                if ($rpta == "SI") {
            ?>
                    <script Language="JavaScript">
                        location.href = 'usuario_mostrar.php?registrado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'usuario_mostrar.php?existe=true';
                    </script>
                <?php
                } else if ($rpta == "PERSONAL_YA_ASIGNADO") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'usuario_mostrar.php?personal_asignado=true';
                    </script>
                <?php
                } else {
                ?>
                    <script Language="JavaScript">
                        location.href = 'usuario_mostrar.php?error=true';
                    </script>
            <?php
                }
            }

            // Obtener datos para los selectores
            $personal_sin_usuario = ObtenerPersonalSinUsuario();
            $roles_activos = MostrarRolesActivos();

            require_once("../_vista/v_usuario_nuevo.php");
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