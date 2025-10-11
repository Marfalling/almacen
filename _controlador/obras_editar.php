</div>
<?php
//=======================================================================
// CONTROLADOR: obras_editar.php 
//=======================================================================
require_once("../_conexion/sesion.php");

//Verificar permisos
if (!verificarPermisoEspecifico('editar_obras')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'OBRAS', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

require_once("../_modelo/m_obras.php");

//Si se envía el formulario
if (isset($_POST['registrar'])) 
{
    $id_obra = intval($_POST['id_obra']);
    $nom = trim($_POST['nom']);
    $est = isset($_POST['est']) ? 1 : 0;

    $rpta = ActualizarObra($id_obra, $nom, $est);

    if ($rpta == "SI") {
        header("Location: obras_mostrar.php?actualizado=true");
        exit;
    } elseif ($rpta == "NO") {
        header("Location: obras_mostrar.php?existe=true");
        exit;
    } else {
        header("Location: obras_mostrar.php?error=true");
        exit;
    }
}

//Obtener obra a editar
$id_obra = isset($_GET['id_obra']) ? intval($_GET['id_obra']) : 0;

// Consultar obra/subestacion por ID
$obra = ConsultarObra($id_obra);
if (!$obra) {
    header("Location: obras_mostrar.php?error=true");
    exit;
}

$nom = $obra['nom_subestacion'];
$est = ($obra['act_subestacion'] == 1) ? "checked" : "";

//require_once("../_modelo/m_auditoria.php");
//GrabarAuditoria($id, $usuario_sesion, 'INGRESO', 'OBRAS', 'EDITAR');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Editar Obra</title>
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
<div class="container body">
    <div class="main_container">
        <?php
        require_once("../_vista/v_menu.php");
        require_once("../_vista/v_menu_user.php");
        require_once("../_vista/v_obras_editar.php");
        require_once("../_vista/v_footer.php"); 
        ?>
    </div>
</div>
<?php require_once("../_vista/v_script.php"); ?>
</body>
</html>
