<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_devolucion.php"); // cambiamos al modelo de devoluciones

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
            // menús reutilizables
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            // obtenemos devoluciones desde el modelo
            $devoluciones = MostrarDevoluciones();

            // cargamos la vista
            require_once("../_vista/v_devolucion_mostrar.php");

            // pie de página
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
    // Enganchar botones de Confirmar y Anular con confirmarAccion()
    document.addEventListener('DOMContentLoaded', function() {
        // Confirmar devolución
        document.querySelectorAll('.btn-confirmar').forEach(function(btn) {
            btn.addEventListener('click', function() {
                let form = this.closest('form');
                confirmarAccion(
                    "¿Está seguro de confirmar la devolución?",
                    "El stock ya no estará disponible en almacén.",
                    function() {
                        form.submit();
                    }
                );
            });
        });

        // Anular devolución
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