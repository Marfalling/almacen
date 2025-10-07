<?php
require_once("../_conexion/sesion.php");

//=======================================================================
// CONTROLADOR: centro_costo_nuevo.php 
//=======================================================================

require_once("../_modelo/m_centro_costo.php");

// Verificar permiso
if (!verificarPermisoEspecifico('crear_centro de costo')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'CENTRO DE COSTO', 'CREAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nom_centro_costo']);
    $rpta = GrabarCentroCosto($nombre);

    require_once("../_modelo/m_auditoria.php");
    if ($rpta == "SI") {
        GrabarAuditoria($id, $usuario_sesion, 'REGISTRO', 'CENTRO DE COSTO', $nombre);
        header("Location: centro_costo_mostrar.php?registrado=true");
    } else {
        GrabarAuditoria($id, $usuario_sesion, 'ERROR', 'CENTRO DE COSTO', 'REGISTRAR');
        header("Location: centro_costo_mostrar.php?error=true");
    }
    exit;
}

// Registrar auditoría de ingreso a la vista de creación
require_once("../_modelo/m_auditoria.php");
GrabarAuditoria($id, $usuario_sesion, 'INGRESO', 'CENTRO DE COSTO', 'NUEVO');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Nuevo Centro de Costo</title>
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
<div class="container body">
    <div class="main_container">

        <?php
        require_once("../_vista/v_menu.php");
        require_once("../_vista/v_menu_user.php");
        require_once("../_vista/v_centro_costo_nuevo.php");
        require_once("../_vista/v_footer.php");
        ?>

    </div>
</div>
<?php require_once("../_vista/v_script.php"); ?>
</body>
</html>




