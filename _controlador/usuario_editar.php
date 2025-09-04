<?php

require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('editar_usuarios')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'USUARIOS', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
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

    <title>Editar Usuario</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $id_usuario = $_REQUEST['id_usuario'];
                $usu = trim($_REQUEST['user']);
                $pass = $_REQUEST['pass']; // Puede estar vacío si no se cambia
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

                // Validaciones adicionales del lado del servidor
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

                // Si se está cambiando la contraseña, validar longitud mínima
                if (!empty($pass) && strlen($pass) < 6) {
                ?>
                    <script Language="JavaScript">
                        alert('Error: La contraseña debe tener al menos 6 caracteres.');
                        history.back();
                    </script>
                <?php
                    exit;
                }

                // Convertir el rol único en un array para mantener compatibilidad con la función existente
                $roles = array($rol_seleccionado);

                $rpta = EditarUsuario($id_usuario, $usu, $pass, $est, $roles);

                if ($rpta == "SI") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'usuario_mostrar.php?actualizado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'usuario_mostrar.php?error=true';
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
            //-------------------------------------------

            // Obtener ID del usuario desde GET
            $id_usuario = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';
            if ($id_usuario == "") {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos del usuario a editar
            $usuario_data = ObtenerUsuario($id_usuario);
            if ($usuario_data) {
                $id_personal = $usuario_data['id_personal'];
                $nom_personal = $usuario_data['nom_personal'];
                $ape_personal = $usuario_data['ape_personal'];
                $dni_personal = $usuario_data['dni_personal'];
                $nom_area = $usuario_data['nom_area'];
                $nom_cargo = $usuario_data['nom_cargo'];
                $usu = $usuario_data['usu_usuario'];
                $est = ($usuario_data['est_usuario'] == 1) ? "checked" : "";
                
                // Obtener roles actuales del usuario
                $roles_usuario = ObtenerRolesUsuario($id_usuario);
            } else {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos para los selectores
            $roles_activos = MostrarRolesActivos();

            require_once("../_vista/v_usuario_editar.php");
            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>

    <?php require_once("../_vista/v_script.php"); ?>
</body>

</html>