<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_movimientos.php");

// ========================================================================
// filtro de fechas
// ========================================================================
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
$fecha_fin    = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;

// Obtener movimientos (con filtro o fecha actual)
$movimientos = MostrarMovimientos($fecha_inicio, $fecha_fin);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Movimientos Mostrar</title>
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php 
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_vista/v_movimientos.php");

            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>
    <?php require_once("../_vista/v_script.php"); ?>
</body>
</html>