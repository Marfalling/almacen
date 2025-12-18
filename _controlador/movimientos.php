<?php
//=======================================================================
// MOVIMIENTOS - VER (movimientos_mostrar.php) - CON VER TODO
//=======================================================================
require_once("../_conexion/sesion.php");

// VALIDACIÓN DE PERMISOS 
if (!verificarPermisoEspecifico('ver_movimientos')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'MOVIMIENTOS', 'VER');
    header("location: bienvenido.php?permisos=true");
    exit;
}

// Validar sesión de personal
$id_personal_actual = isset($_SESSION['id_personal']) && !empty($_SESSION['id_personal']) 
    ? intval($_SESSION['id_personal']) 
    : 0;

if ($id_personal_actual === 0) {
    header("location: cerrar_sesion.php");
    exit;
}

require_once("../_modelo/m_movimientos.php");

// ========================================================================
// Filtro de fechas (por defecto desde el 1° del mes hasta hoy)
// ========================================================================
$fecha_actual = date("Y-m-d");
$primer_dia_mes = date("Y-m-01");

$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : $primer_dia_mes;
$fecha_fin    = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : $fecha_actual;

// ========================================================================
// LÓGICA DE "VER TODO" vs "VER SOLO PROPIOS"
// ========================================================================
if (verificarPermisoEspecifico('ver todo_movimientos')) {
    // Tiene permiso "Ver Todo" - Ve TODOS los movimientos del sistema
    $id_personal_filtro = null;
} else {
    // Solo tiene "Ver" básico - Ve únicamente SUS movimientos
    $id_personal_filtro = $id_personal_actual;
}

// Obtener movimientos filtrados
$movimientos = MostrarMovimientos($fecha_inicio, $fecha_fin, $id_personal_filtro);

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