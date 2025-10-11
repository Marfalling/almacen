<?php
require_once("../_conexion/sesion.php");

// Verificar permisos
if (!verificarPermisoEspecifico('crear_obras')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'OBRAS', 'NUEVO');
    header("location: bienvenido.php?permisos=true");
    exit;
}

require_once("../_modelo/m_obras.php");

// Procesar registro
if (isset($_POST['registrar'])) {
    $nom = trim($_POST['nom']);
    $est = isset($_POST['est']) ? 1 : 0;

    $rpta = RegistrarObra($nom, $est);

    if ($rpta == "SI") {
        header("Location: obras_mostrar.php?registrado=true");
        exit;
    } elseif ($rpta == "NO") {
        header("Location: obras_mostrar.php?existe=true");
        exit;
    } else {
        header("Location: obras_mostrar.php?error=true");
        exit;
    }
}

//require_once("../_modelo/m_auditoria.php");
//GrabarAuditoria($id, $usuario_sesion, 'INGRESO', 'OBRAS', 'NUEVO');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Nueva Obra</title>
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
<div class="container body">
    <div class="main_container">
        <?php
        require_once("../_vista/v_menu.php");
        require_once("../_vista/v_menu_user.php");
        require_once("../_vista/v_obras_nuevo.php");
        require_once("../_vista/v_footer.php"); ?>
    </div>
</div>
<?php require_once("../_vista/v_script.php"); ?>
</body>
</html>
