<?php
require_once("../_conexion/sesion.php");

// Verificar permisos
if (!verificarPermisoEspecifico('crear_detraccion')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'DETRACCION', 'CREAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

//=======================================================================
// CONTROLADOR: detraccion_nuevo.php
//=======================================================================

require_once("../_modelo/m_detraccion.php");

if (isset($_REQUEST['registrar'])) {
    $nom = strtoupper(trim($_REQUEST['nom']));
    $porcentaje = floatval($_REQUEST['porcentaje']);

    $rpta = GrabarDetraccion($nom, $porcentaje);

    if ($rpta == "SI") {
        header("location: detraccion_mostrar.php?registrado=true");
        exit;
    } else {
        header("location: detraccion_mostrar.php?error=true");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Nueva Detracción</title>
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
<div class="container body">
    <div class="main_container">

        <?php
        require_once("../_vista/v_menu.php");
        require_once("../_vista/v_menu_user.php");
        require_once("../_vista/v_detraccion_nuevo.php");
        require_once("../_vista/v_footer.php");
        ?>

    </div>
</div>
<?php require_once("../_vista/v_script.php"); ?>
</body>
</html>







