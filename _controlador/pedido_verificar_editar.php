<?php
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('editar_pedidos')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'PEDIDOS', 'EDITAR');
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

    <title>Editar Orden de Compra</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_pedidos.php");
            require_once("../_modelo/m_proveedor.php");
            require_once("../_modelo/m_moneda.php");
            require_once("../_modelo/m_obras.php");

            $id_compra = isset($_REQUEST['id_compra']) ? intval($_REQUEST['id_compra']) : 0;

            // Variable para pasar mensajes a JS
            $alerta = null;

            // ACTUALIZAR ORDEN
            if (isset($_REQUEST['actualizar_orden'])) {
                $id_compra = $_REQUEST['id_compra'];
                $proveedor_sel = $_REQUEST['proveedor_orden'];
                $moneda_sel = $_REQUEST['moneda_orden'];
                $observacion = $_REQUEST['observaciones_orden'];
                $direccion = $_REQUEST['direccion_envio'];
                $plazo_entrega = $_REQUEST['plazo_entrega'];
                $porte = $_REQUEST['tipo_porte'];
                $fecha_orden = $_REQUEST['fecha_orden'];
                $items = $_REQUEST['items_orden'] ?? [];

                // Validaciones
                if (empty($proveedor_sel) || empty($moneda_sel) || empty($fecha_orden)) {
                    $alerta = [
                        "icon" => "error",
                        "title" => "Error",
                        "text" => "Complete todos los campos obligatorios (Fecha, Proveedor y Moneda)"
                    ];
                } elseif (empty($items)) {
                    $alerta = [
                        "icon" => "error",
                        "title" => "Error",
                        "text" => "Debe tener al menos un item en la orden"
                    ];
                } else {
                    $rpta = ActualizarOrdenCompra(
                        $id_compra,
                        $proveedor_sel,
                        $moneda_sel,
                        $observacion,
                        $direccion,
                        $plazo_entrega,
                        $porte,
                        $fecha_orden,
                        $items
                    );

                    if ($rpta == "SI") {
                        // Obtener el ID del pedido para redireccionar
                        $orden_temp = ObtenerOrdenPorId($id_compra);
                        $id_pedido = $orden_temp ? $orden_temp['id_pedido'] : 0;
                        
                        $alerta = [
                            "icon" => "success",
                            "title" => "¡Orden Actualizada!",
                            "text" => "La orden de compra se ha actualizado exitosamente",
                            "redirect" => "pedido_verificar.php?id=$id_pedido",
                            "timer" => 2000
                        ];
                    } else {
                        $alerta = [
                            "icon" => "error",
                            "title" => "Error",
                            "text" => "Error al actualizar orden: $rpta"
                        ];
                    }
                }
            }

            // CARGAR ORDEN
            if ($id_compra > 0) {
                try {
                    $orden_data = ObtenerOrdenPorId($id_compra);
                    
                    if (!empty($orden_data)) {
                        $orden_detalle = ObtenerDetalleOrden($id_compra);
                        $proveedor = MostrarProveedores();
                        $moneda = MostrarMoneda();
                        $obras = MostrarObrasActivas();
                        
                        require_once("../_vista/v_pedido_verificar_editar.php");
                    } else {
                        $alerta = [
                            "icon" => "error",
                            "title" => "Error",
                            "text" => "Orden no encontrada",
                            "redirect" => "pedidos_mostrar.php",
                            "timer" => 2000
                        ];
                    }
                } catch (Exception $e) {
                    $alerta = [
                        "icon" => "error",
                        "title" => "Error",
                        "text" => "Error al cargar datos: " . $e->getMessage(),
                        "redirect" => "pedidos_mostrar.php",
                        "timer" => 2000
                    ];
                }
            } else {
                $alerta = [
                    "icon" => "error",
                    "title" => "Error",
                    "text" => "ID de orden no válido",
                    "redirect" => "pedidos_mostrar.php",
                    "timer" => 2000
                ];
            }

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
            timer: alerta.timer || null,
            allowOutsideClick: false
        }).then(() => {
            if (alerta.redirect) {
                window.location.href = alerta.redirect;
            }
        });
    });
    </script>
    <?php endif; ?>
</body>
</html>