<?php
require_once("../_conexion/sesion.php");

//=======================================================================
// CONTROLADOR: detraccion_nuevo.php 
//=======================================================================

// Verificar permisos
if (!verificarPermisoEspecifico('crear_detraccion')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'DETRACCION', 'CREAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

require_once("../_modelo/m_detraccion.php");

// Obtener tipos de detracción para el formulario
$tipos_detraccion = ObtenerTiposDetraccion();

// Procesar formulario
if (isset($_REQUEST['registrar'])) {
    $nom = strtoupper(trim($_REQUEST['nom']));
    $porcentaje = floatval($_REQUEST['porcentaje']);
    $id_detraccion_tipo = intval($_REQUEST['id_detraccion_tipo']);

    $rpta = GrabarDetraccion($nom, $porcentaje, $id_detraccion_tipo);

    require_once("../_modelo/m_auditoria.php");
    if ($rpta == "SI") {
        GrabarAuditoria($id, $usuario_sesion, 'CREACIÓN', 'DETRACCION', "Se creó la detracción '$nom'");
        header("location: detraccion_mostrar.php?registrado=true");
        exit;
    } elseif ($rpta == "NO") {
        GrabarAuditoria($id, $usuario_sesion, 'ERROR CREACIÓN', 'DETRACCION', "Intento duplicado al crear '$nom'");
        header("location: detraccion_mostrar.php?error=duplicado");
        exit;
    } else {
        GrabarAuditoria($id, $usuario_sesion, 'ERROR CREACIÓN', 'DETRACCION', "Error al crear '$nom'");
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