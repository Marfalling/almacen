<?php

require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('crear_usuarios')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'USUARIO', 'CREAR');
    header("location: dashboard.php?permisos=true");
    exit;
}

require_once("../_modelo/m_usuario.php");
require_once("../_modelo/m_rol.php");

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

                // Obtener rol seleccionado (ahora es solo uno)
                $rol_seleccionado = isset($_REQUEST['rol_seleccionado']) ? $_REQUEST['rol_seleccionado'] : null;
                
                // Validar que se haya seleccionado un rol
                if (empty($rol_seleccionado)) {
            ?>
                    <script Language="JavaScript">
                        alert('Error: Debe seleccionar un rol para el usuario.');
                        history.back();
                    </script>
            <?php
                    exit;
                }

                // Convertir el rol único en un array para mantener compatibilidad con la función existente
                $roles = array($rol_seleccionado);

                // Validaciones adicionales del lado del servidor
                if (empty($id_personal)) {
            ?>
                    <script Language="JavaScript">
                        alert('Error: Debe seleccionar un personal.');
                        history.back();
                    </script>
            <?php
                    exit;
                }

                if (empty($usu)) {
            ?>
                    <script Language="JavaScript">
                        alert('Error: El nombre de usuario es obligatorio.');
                        history.back();
                    </script>
            <?php
                    exit;
                }

                // Validar que el usuario no contenga espacios
                if (preg_match('/\s/', $usu)) {
            ?>
                    <script Language="JavaScript">
                        alert('Error: El nombre de usuario no puede contener espacios.');
                        history.back();
                    </script>
            <?php
                    exit;
                }

                if (empty($pass)) {
            ?>
                    <script Language="JavaScript">
                        alert('Error: La contraseña es obligatoria.');
                        history.back();
                    </script>
            <?php
                    exit;
                }

                // Validar longitud mínima de contraseña
                if (strlen($pass) < 6) {
            ?>
                    <script Language="JavaScript">
                        alert('Error: La contraseña debe tener al menos 6 caracteres.');
                        history.back();
                    </script>
            <?php
                    exit;
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