<?php
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('ver_pedidos')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'PEDIDOS', 'VER');
    header("location: bienvenido.php?permisos=true");
    exit;
}

require_once("../_modelo/m_pedidos.php");
require_once("../_modelo/m_compras.php");

// ========================================================================
// Filtro de fechas con valores por defecto dinámicos
// ========================================================================
if (isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin'])) {
    // Si el usuario envió fechas, se usan esas
    $fecha_inicio = $_GET['fecha_inicio'];
    $fecha_fin    = $_GET['fecha_fin'];
} else {
    // Si no se envían fechas, se calcula automáticamente el rango del mes actual
    $fecha_inicio = date('Y-m-01'); // Primer día del mes actual
    $fecha_fin    = date('Y-m-d');  // Fecha actual
}

// Obtener pedidos con filtro
$pedidos = MostrarPedidosFecha($fecha_inicio, $fecha_fin);
$pedidos_rechazados = ObtenerPedidosConComprasAnuladas();
$alerta = null;



if (isset($_GET['success']) && $_GET['success'] === 'completado_auto') {
    $alerta = [
        "icon" => "success",
        "title" => "¡Pedido Completado Automáticamente!",
        "text" => "El pedido se completó porque todos los items tienen stock disponible.",
        "timer" => 3000
    ];
}
if (isset($_GET['error']) && $_GET['error'] === 'completado_auto') {
    $alerta = [
        "icon" => "error",
        "title" => "Error al Completar Pedido",
        "text" => "No se pudo completar el pedido automáticamente.",
        "timer" => 3000
    ];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pedidos Mostrar</title>
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_vista/v_pedidos_mostrar.php");
            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>

    <?php
    require_once("../_vista/v_script.php");
    require_once("../_vista/v_alertas.php");
    ?>

    <?php if ($alerta): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const alerta = <?php echo json_encode($alerta, JSON_UNESCAPED_UNICODE); ?>;
        
        Swal.fire({
            icon: alerta.icon,
            title: alerta.title,
            text: alerta.text,
            showConfirmButton: !alerta.timer,
            timer: alerta.timer || null
        });
    });
    </script>
    <?php endif; ?>
</body>
</html>
