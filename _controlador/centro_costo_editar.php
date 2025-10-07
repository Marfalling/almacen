<?php
require_once("../_conexion/sesion.php");

//=======================================================================
// CONTROLADOR: centro_costo_editar.php 
//=======================================================================

require_once("../_modelo/m_centro_costo.php");

// Verificar permiso
if (!verificarPermisoEspecifico('editar_centro de costo')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'CENTRO DE COSTO', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

// Procesar actualización
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_centro = intval($_POST['id_centro_costo']);
    $nombre = trim($_POST['nom_centro_costo']);
    $estado = intval($_POST['est_centro_costo']);

    $rpta = EditarCentroCosto($id_centro, $nombre);
    if ($rpta == "SI") {
        CambiarEstadoCentroCosto($id_centro, $estado);
        require_once("../_modelo/m_auditoria.php");
        GrabarAuditoria($id, $usuario_sesion, 'ACTUALIZACIÓN', 'CENTRO DE COSTO', $nombre);
        header("Location: centro_costo_mostrar.php?actualizado=true");
    } else {
        require_once("../_modelo/m_auditoria.php");
        GrabarAuditoria($id, $usuario_sesion, 'ERROR', 'CENTRO DE COSTO', 'EDITAR');
        header("Location: centro_costo_mostrar.php?error=true");
    }
    exit;
}

// Obtener datos para editar
$id_centro = isset($_GET['id_centro_costo']) ? intval($_GET['id_centro_costo']) : 0;
$centro = ObtenerCentroCostoPorId($id_centro);

// Registrar auditoría
require_once("../_modelo/m_auditoria.php");
GrabarAuditoria($id, $usuario_sesion, 'INGRESO', 'CENTRO DE COSTO', 'EDITAR');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Editar Centro de Costo</title>
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
<div class="container body">
    <div class="main_container">

        <?php
        require_once("../_vista/v_menu.php");
        require_once("../_vista/v_menu_user.php");
        require_once("../_vista/v_centro_costo_editar.php");
        require_once("../_vista/v_footer.php");
        ?>

    </div>
</div>
<?php require_once("../_vista/v_script.php"); ?>
</body>
</html>
