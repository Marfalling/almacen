<?php
require_once("../_conexion/sesion.php");

//=======================================================================
// CONTROLADOR: centro_costo_mostrar.php 
//=======================================================================

require_once("../_modelo/m_centro_costo.php");

// Cambiar estado
if (isset($_GET['id_centro_costo']) && isset($_GET['estado'])) {
    $id = intval($_GET['id_centro_costo']);
    $nuevo_estado = intval($_GET['estado']);

    $resultado = CambiarEstadoCentroCosto($id, $nuevo_estado);

    if ($resultado == "SI") {
        header("Location: centro_costo_mostrar.php?actualizado=true");
        exit;
    } else {
        header("Location: centro_costo_mostrar.php?error=true");
        exit;
    }
}

// Verificar permisos
if (!verificarPermisoEspecifico('ver_centro de costo')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'CENTRO DE COSTO', 'VER');
    header("location: bienvenido.php?permisos=true");
    exit;
}

// Obtener lista de centros de costo
$centros = ObtenerCentrosCosto();

// Registrar auditoría
require_once("../_modelo/m_auditoria.php");
GrabarAuditoria($id, $usuario_sesion, 'INGRESO', 'CENTRO DE COSTO', 'MOSTRAR');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Listado de Centros de Costo</title>
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
<div class="container body">
    <div class="main_container">

        <?php
        require_once("../_vista/v_menu.php");
        require_once("../_vista/v_menu_user.php");
        require_once("../_vista/v_centro_costo_mostrar.php");
        require_once("../_vista/v_footer.php");
        ?>

    </div>
</div>
<?php require_once("../_vista/v_script.php"); ?>
</body>
</html>





