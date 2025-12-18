<?php
//=======================================================================
// COMPRAS - VER (compras_mostrar.php) - CON VER TODO
//=======================================================================
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('ver_compras')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'COMPRAS', 'VER');
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

require_once("../_modelo/m_compras.php");
require_once("../_modelo/m_documentos.php");

// ========================================================================
// Filtro de fechas (rango automático por defecto)
// ========================================================================
$fecha_actual = date('Y-m-d');
$primer_dia_mes = date('Y-m-01');

$fecha_inicio = isset($_GET['fecha_inicio']) && $_GET['fecha_inicio'] !== ''
    ? $_GET['fecha_inicio']
    : $primer_dia_mes;

$fecha_fin = isset($_GET['fecha_fin']) && $_GET['fecha_fin'] !== ''
    ? $_GET['fecha_fin']
    : $fecha_actual;

// ========================================================================
// LÓGICA DE "VER TODO" vs "VER SOLO PROPIOS"
// ========================================================================
if (verificarPermisoEspecifico('ver todo_compras')) {
    // Tiene permiso "Ver Todo" - Ve TODAS las compras del sistema
    $id_personal_filtro = null;
} else {
    // Solo tiene "Ver" básico - Ve únicamente SUS compras
    $id_personal_filtro = $id_personal_actual;
}

// Obtener compras con el rango seleccionado y filtro de personal
$compras = MostrarComprasFecha($fecha_inicio, $fecha_fin, $id_personal_filtro);
foreach ($compras as $i => $compra) {
    $compras[$i]['id_producto_tipo'] = ObtenerTipoProductoPorCompra($compra['id_compra']);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Compras</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            // Vista principal
            require_once("../_vista/v_compras_mostrar.php");

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