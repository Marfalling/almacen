<?php
//=======================================================================
// ingresos_mostrar.php
//=======================================================================
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('ver_ingresos')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'INGRESOS', 'VER');
    header("location: bienvenido.php?permisos=true");
    exit;
}

require_once("../_modelo/m_ingreso.php");
require_once("../_modelo/m_compras.php");

// ========================================================================
// Filtro de fechas 
// ========================================================================
$fecha_actual = date('Y-m-d');
$primer_dia_mes = date('Y-m-01');

$fecha_inicio = isset($_GET['fecha_inicio']) && $_GET['fecha_inicio'] !== ''
    ? $_GET['fecha_inicio']
    : $primer_dia_mes;

$fecha_fin = isset($_GET['fecha_fin']) && $_GET['fecha_fin'] !== ''
    ? $_GET['fecha_fin']
    : $fecha_actual;

// Obtener ingresos (compras + directos) con el filtro seleccionado
$ingresos = MostrarIngresosFecha($fecha_inicio, $fecha_fin);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Gestión de Ingresos</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            // Vista principal
            require_once("../_vista/v_ingresos_mostrar.php");
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
