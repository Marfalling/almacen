<?php
require_once("../_conexion/sesion.php");

//=======================================================================
// CONTROLADOR: detraccion_mostrar.php 
//=======================================================================
require_once("../_modelo/m_detraccion.php");

if (isset($_GET['id_detraccion']) && isset($_GET['estado'])) {
    $id = intval($_GET['id_detraccion']);
    $nuevo_estado = intval($_GET['estado']);

    $resultado = CambiarEstadoDetraccion($id, $nuevo_estado);

    if ($resultado == "SI") {
        header("Location: detraccion_mostrar.php?actualizado=true");
        exit;
    } else {
        header("Location: detraccion_mostrar.php?error=true");
        exit;
    }
}

// Verificar permisos
if (!verificarPermisoEspecifico('ver_detraccion')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'DETRACCION', 'VER');
    header("location: bienvenido.php?permisos=true");
    exit;
}

// Obtener lista de detracciones
$detraccion = ObtenerDetracciones();

// Registrar auditoría de ingreso
require_once("../_modelo/m_auditoria.php");
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