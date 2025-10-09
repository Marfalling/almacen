<?php
//=======================================================================
// DEVOLUCIONES - VER (devoluciones_mostrar.php)
//=======================================================================

require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('ver_devoluciones')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'DEVOLUCIONES', 'VER');
    header("location: bienvenido.php?permisos=true");
    exit;
}

require_once("../_modelo/m_devolucion.php");

// ========================================================================
// VARIABLES DE ALERTA
// ========================================================================
$mostrar_alerta = false;
$tipo_alerta = '';
$titulo_alerta = '';
$mensaje_alerta = '';

// ========================================================================
// CONTROLADOR DE ACCIONES (REQUEST)
// ========================================================================
if (isset($_REQUEST['confirmar'])) {
    $id = intval($_REQUEST['id_devolucion']);
    $resultado = ConfirmarDevolucion($id);

    if ($resultado === "SI") {
        $mostrar_alerta = true;
        $tipo_alerta = 'success';
        $titulo_alerta = 'Confirmado';
        $mensaje_alerta = 'La devolución fue confirmada correctamente.';
    } else {
        $mostrar_alerta = true;
        $tipo_alerta = 'error';
        $titulo_alerta = 'Error';
        $mensaje_alerta = 'No se pudo confirmar la devolución.<br>' . $resultado;
    }
}

if (isset($_REQUEST['anular'])) {
    $id = intval($_REQUEST['id_devolucion']);
    $resultado = AnularDevolucion($id);

    if ($resultado === "SI") {
        $mostrar_alerta = true;
        $tipo_alerta = 'success';
        $titulo_alerta = 'Anulada';
        $mensaje_alerta = 'La devolución fue anulada correctamente.';
    } else {
        $mostrar_alerta = true;
        $tipo_alerta = 'error';
        $titulo_alerta = 'Error';
        $mensaje_alerta = 'No se pudo anular la devolución.<br>' . $resultado;
    }
}

// ========================================================================
// Filtro de fechas (por defecto: del primer día del mes hasta hoy)
// ========================================================================
$hoy = date('Y-m-d');
$primerDiaMes = date('Y-m-01');

$fecha_inicio = isset($_GET['fecha_inicio']) && !empty($_GET['fecha_inicio'])
    ? $_GET['fecha_inicio']
    : $primerDiaMes;

$fecha_fin = isset($_GET['fecha_fin']) && !empty($_GET['fecha_fin'])
    ? $_GET['fecha_fin']
    : $hoy;

// obtenemos devoluciones desde el modelo con filtro
$devoluciones = MostrarDevolucionesFecha($fecha_inicio, $fecha_fin);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Devoluciones Mostrar</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_vista/v_devolucion_mostrar.php");
            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>

    <?php
    require_once("../_vista/v_script.php");
    require_once("../_vista/v_alertas.php");

    if ($mostrar_alerta) {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: '<?php echo $tipo_alerta; ?>',
                    title: '<?php echo $titulo_alerta; ?>',
                    html: '<?php echo $mensaje_alerta; ?>',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '<?php echo ($tipo_alerta == "error") ? "#d33" : "#3085d6"; ?>'
                });
            } else {
                alert('<?php echo $titulo_alerta . ": " . strip_tags($mensaje_alerta); ?>');
            }
        });
        </script>
        <?php
    }
    ?>

    <script>
    // Unir botones de Confirmar y Anular con confirmarAccion()
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-confirmar').forEach(function(btn) {
            btn.addEventListener('click', function() {
                let form = this.closest('form');
                confirmarAccion(
                    "¿Está seguro de confirmar la devolución?",
                    "Ya no se podrá editar esta devolución.",
                    function() {
                        form.submit();
                    }
                );
            });
        });

        document.querySelectorAll('.btn-anular').forEach(function(btn) {
            btn.addEventListener('click', function() {
                let form = this.closest('form');
                confirmarAccion(
                    "¿Está seguro de anular la devolución?",
                    "El stock volverá al almacén.",
                    function() {
                        form.submit();
                    }
                );
            });
        });
    });
    </script>
</body>
</html>
