<?php
require_once("../_conexion/sesion.php");

// Verificar permisos
if (!verificarPermisoEspecifico('ver_detraccion')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'DETRACCION', 'VER');
    header("location: bienvenido.php?permisos=true");
    exit;
}

//=======================================================================
// CONTROLADOR: detraccion_mostrar.php
//=======================================================================

require_once("../_modelo/m_detraccion.php");

// Obtener lista de detracciones
$detraccion = ObtenerDetracciones();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Listado de Detracciones</title>
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
<div class="container body">
    <div class="main_container">

        <?php
        require_once("../_vista/v_menu.php");
        require_once("../_vista/v_menu_user.php");
        require_once("../_vista/v_detraccion_mostrar.php");
        require_once("../_vista/v_footer.php");
        ?>

    </div>
</div>
<?php require_once("../_vista/v_script.php"); ?>
</body>
</html>











