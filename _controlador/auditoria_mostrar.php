<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

// Verificar permiso
if (!verificarPermisoEspecifico('ver_auditoria')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'AUDITORIA', 'VER');
    header("location: bienvenido.php?permisos=true");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Auditoría | Almacen Arce</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            date_default_timezone_set('America/Lima');
            $fecha_actual = date("Y-m-d");

            if (isset($_REQUEST['filtrar'])) {
                $fecha_inicio = ($_REQUEST['fecha_inicio'] != '') ? $_REQUEST['fecha_inicio'] : $fecha_actual;
                $fecha_fin = ($_REQUEST['fecha_fin'] != '') ? $_REQUEST['fecha_fin'] : $fecha_actual;
            } else {
                $fecha_inicio = $fecha_actual;
                $fecha_fin = $fecha_actual;
            }

            $auditoria = MostrarAuditoria($fecha_inicio, $fecha_fin);

            require_once("../_vista/v_auditoria_mostrar.php");
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