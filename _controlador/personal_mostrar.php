<?php
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('ver_personal')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'PERSONAL', 'VER');
    header("location: bienvenido.php?permisos=true");
    exit;
}

require_once("../_conexion/conexion.php");
require_once("../_modelo/m_personal.php");
$personal = MostrarPersonal();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Personal Mostrar</title>
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
<div class="container body">
    <div class="main_container">
        <?php
        require_once("../_vista/v_menu.php");
        require_once("../_vista/v_menu_user.php");
        require_once("../_vista/v_personal_mostrar.php");
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
