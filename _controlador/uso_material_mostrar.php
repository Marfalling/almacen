<?php
//=======================================================================
// uso_material_mostrar.php 
//=======================================================================
require_once("../_conexion/sesion.php");

// VERIFICACIÓN DE PERMISOS
if (!verificarPermisoEspecifico('ver_uso de material')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'USO DE MATERIAL', 'VER');
    header("location: bienvenido.php?permisos=true");
    exit;
}

require_once("../_modelo/m_uso_material.php");

// ========================================================================
// Filtro de fechas
// ========================================================================
$fecha_hoy = date('Y-m-d');
$primer_dia_mes = date('Y-m-01');

$fecha_inicio = isset($_GET['fecha_inicio']) && !empty($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : $primer_dia_mes;
$fecha_fin    = isset($_GET['fecha_fin']) && !empty($_GET['fecha_fin']) ? $_GET['fecha_fin'] : $fecha_hoy;

// Obtener datos con filtro
$usos_material = MostrarUsoMaterial($fecha_inicio, $fecha_fin);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Listado de Uso de Material</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_vista/v_uso_material_mostrar.php");
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
