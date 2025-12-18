<?php
//=======================================================================
// SALIDAS - VER (salidas_mostrar.php) - CON VER TODO
//=======================================================================
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('ver_salidas')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'SALIDAS', 'VER');
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

require_once("../_modelo/m_salidas.php");
require_once("../_modelo/m_documentos.php");

// ========================================================================
// Filtro de fechas con valores por defecto automáticos
// ========================================================================
if (isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin'])) {
    $fecha_inicio = $_GET['fecha_inicio'];
    $fecha_fin    = $_GET['fecha_fin'];
} else {
    $fecha_inicio = date('Y-m-01');
    $fecha_fin    = date('Y-m-d');
}

// ========================================================================
// LÓGICA DE "VER TODO" vs "VER SOLO PROPIOS"
// ========================================================================
if (verificarPermisoEspecifico('ver todo_salidas')) {
    // Tiene permiso "Ver Todo" - Ve TODAS las salidas del sistema
    $id_personal_filtro = null;
} else {
    // Solo tiene "Ver" básico - Ve únicamente SUS salidas
    $id_personal_filtro = $id_personal_actual;
}

// Obtener salidas según el filtro de personal
$salidas = MostrarSalidasFecha($fecha_inicio, $fecha_fin, $id_personal_filtro);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Salidas Mostrar</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");
            require_once("../_vista/v_salidas_mostrar.php");
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