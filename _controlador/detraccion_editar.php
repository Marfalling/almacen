<?php
require_once("../_conexion/sesion.php");

//=======================================================================
// CONTROLADOR: detraccion_editar.php 
//=======================================================================

if (!verificarPermisoEspecifico('editar_detraccion')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'DETRACCION', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

require_once("../_modelo/m_detraccion.php");

// Obtener tipos de detracción para el formulario
$tipos_detraccion = ObtenerTiposDetraccion();

if (isset($_POST['registrar'])) {
    $id_detraccion = intval($_POST['id_detraccion']);
    $nom = strtoupper(trim($_POST['nom']));
    $porcentaje = floatval($_POST['porcentaje']);
    $estado = isset($_POST['estado']) ? 1 : 0;
    $id_detraccion_tipo = intval($_POST['id_detraccion_tipo']);

    $rpta = EditarDetraccion($id_detraccion, $nom, $porcentaje, $estado, $id_detraccion_tipo);

    require_once("../_modelo/m_auditoria.php");

    if ($rpta === "SI") {
        GrabarAuditoria($id, $usuario_sesion, 'MODIFICACIÓN', 'DETRACCION', "Se editó la detracción '$nom' (ID: $id_detraccion)");
        header("Location: detraccion_mostrar.php?actualizado=true");
        exit;
    } elseif ($rpta === "NO") {
        GrabarAuditoria($id, $usuario_sesion, 'ERROR MODIFICACIÓN', 'DETRACCION', "Intento duplicado al editar '$nom'");
        header("Location: detraccion_mostrar.php?error=duplicado");
        exit;
    } else {
        GrabarAuditoria($id, $usuario_sesion, 'ERROR MODIFICACIÓN', 'DETRACCION', "Error al editar '$nom'");
        header("Location: detraccion_mostrar.php?error=true");
        exit;
    }
}

$id_detraccion = isset($_GET['id_detraccion']) ? intval($_GET['id_detraccion']) : 0;
if ($id_detraccion <= 0) {
    header("Location: detraccion_mostrar.php?error=true");
    exit;
}

$detraccion_data = ObtenerDetraccionPorId($id_detraccion);
if (!$detraccion_data) {
    header("Location: detraccion_mostrar.php?error=true");
    exit;
}

$nom = $detraccion_data['nombre_detraccion'];
$porcentaje = $detraccion_data['porcentaje'];
$estado = $detraccion_data['est_detraccion'];
$id_detraccion_tipo_actual = $detraccion_data['id_detraccion_tipo'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Editar Detracción</title>
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
<div class="container body">
    <div class="main_container">
        <?php
        require_once("../_vista/v_menu.php");
        require_once("../_vista/v_menu_user.php");
        require_once("../_vista/v_detraccion_editar.php");
        require_once("../_vista/v_footer.php");
        ?>
    </div>
</div>
<?php require_once("../_vista/v_script.php"); ?>
</body>
</html>