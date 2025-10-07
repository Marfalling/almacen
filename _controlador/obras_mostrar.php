<?php
require_once("../_conexion/sesion.php");

//Verificar permiso para VER OBRAS
if (!verificarPermisoEspecifico('ver_obras')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'OBRAS', 'VER');
    header("location: bienvenido.php?permisos=true");
    exit;
}

require_once("../_modelo/m_obras.php");
$obras = MostrarObras();

require_once("../_modelo/m_auditoria.php");
GrabarAuditoria($id, $usuario_sesion, 'INGRESO', 'OBRAS', 'MOSTRAR');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Listado de Obras</title>
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
<div class="container body">
    <div class="main_container">
        <?php
        require_once("../_vista/v_menu.php");
        require_once("../_vista/v_menu_user.php");
        require_once("../_vista/v_obras_mostrar.php");
        require_once("../_vista/v_footer.php");
        ?>
    </div>
</div>
<?php require_once("../_vista/v_script.php"); ?>
</body>
</html>


