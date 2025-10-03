<?php
require_once("../_conexion/sesion.php");

// Verificar permiso
if (!verificarPermisoEspecifico('editar_detraccion')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'DETRACCION', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

//=======================================================================
// CONTROLADOR: detraccion_editar.php
//=======================================================================

require_once("../_modelo/m_detraccion.php");

// Procesar envío
if (isset($_POST['registrar'])) {
    $id_detraccion = intval($_POST['id_detraccion']);
    $nom = strtoupper(trim($_POST['nom']));
    $porcentaje = floatval($_POST['porcentaje']);

    $rpta = EditarDetraccion($id_detraccion, $nom, $porcentaje);

    if ($rpta === "SI") {
        header("Location: detraccion_mostrar.php?actualizado=true");
        exit;
    } elseif ($rpta === "NO") {
        header("Location: detraccion_mostrar.php?error=duplicado");
        exit;
    } else {
        header("Location: detraccion_mostrar.php?error=true");
        exit;
    }
}

// Obtener ID desde GET
$id_detraccion = isset($_GET['id_detraccion']) ? intval($_GET['id_detraccion']) : 0;
if ($id_detraccion <= 0) {
    header("Location: detraccion_mostrar.php?error=true");
    exit;
}

// Obtener datos de la detracción
$detraccion_data = ObtenerDetraccionPorId($id_detraccion);
if (!$detraccion_data) {
    header("Location: detraccion_mostrar.php?error=true");
    exit;
}

// Variables para la vista
$nom = $detraccion_data['nombre_detraccion'];
$porcentaje = $detraccion_data['porcentaje'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Editar Detracción</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            // Vista principal
            require_once("../_vista/v_detraccion_editar.php");

            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>

    <?php require_once("../_vista/v_script.php"); ?>
</body>

</html>
