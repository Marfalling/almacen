<?php
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('ver_unidad de medida')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'UNIDAD DE MEDIDA', 'VER');
    header("location: bienvenido.php?permisos=true");
    exit;
}
require_once("../_modelo/m_unidad_medida.php");



?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Unidad Medida Mostrar</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_unidad_medida.php");
            $unidad_medida = MostrarUnidadMedida();
            require_once("../_vista/v_unidad_medida_mostrar.php");

            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>

    <?php
    require_once("../_vista/v_script.php");
    require_once("../_vista/v_alertas.php");
    ?>
</body>

</html>