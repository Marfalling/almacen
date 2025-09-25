<?php
require_once("../_conexion/sesion.php");


if (!verificarPermisoEspecifico('ver_pedidos')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'PEDIDOS', 'VERIFICAR');
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

    <title>Verificar Pedido</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_pedidos.php");
            require_once("../_modelo/m_obras.php");
            require_once("../_modelo/m_proveedor.php");
            require_once("../_modelo/m_moneda.php");

            $id_pedido = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

            // variable para pasar mensajes a JS
            $alerta = null;

            // VERIFICAR ITEM
            if (isset($_REQUEST['verificar_item'])) {
                $id_pedido_detalle = $_REQUEST['id_pedido_detalle'];
                $new_cant_fin = $_REQUEST['fin_cant_pedido_detalle'];
                $rpta = verificarItem($id_pedido_detalle, $new_cant_fin);

                if ($rpta == "SI") {
                    $alerta = [
                        "icon" => "success",
                        "title" => "¡Éxito!",
                        "text" => "Item verificado correctamente",
                        "redirect" => "pedido_verificar.php?id=$id_pedido",
                        "timer" => 1500
                    ];
                } else {
                    $alerta = [
                        "icon" => "error",
                        "title" => "Error",
                        "text" => "Error al verificar: $rpta"
                    ];
                }
            }

            // CREAR ORDEN
            if (isset($_REQUEST['crear_orden'])) {
                $id_pedido = $_REQUEST['id'];
                $proveedor = $_REQUEST['proveedor_orden'];
                $moneda = $_REQUEST['moneda_orden'];
                $id_personal = $_SESSION['id_personal'];
                $observacion = $_REQUEST['observaciones_orden'];
                $direccion = $_REQUEST['direccion_envio'];
                $plazo_entrega = $_REQUEST['plazo_entrega'];
                $porte = $_REQUEST['tipo_porte'];
                $fecha_orden = $_REQUEST['fecha_orden'];
                $items = $_REQUEST['items_orden'];

                $rpta = CrearOrdenCompra($id_pedido, $proveedor, $moneda, $id_personal, $observacion, $direccion, $plazo_entrega, $porte, $fecha_orden, $items);

                if ($rpta == "SI") {
                    $alerta = [
                        "icon" => "success",
                        "title" => "¡Orden Creada!",
                        "text" => "La orden de compra se ha creado exitosamente",
                        "redirect" => "pedido_verificar.php?id=$id_pedido",
                        "timer" => 2000
                    ];
                } else {
                    $alerta = [
                        "icon" => "error",
                        "title" => "Error",
                        "text" => "Error al crear orden: $rpta"
                    ];
                }
            }

            // CARGAR PEDIDO
            if ($id_pedido > 0) {
                $pedido_data = ConsultarPedido($id_pedido);
                $pedido_detalle = ConsultarPedidoDetalle($id_pedido);
                $pedido_compra = ConsultarCompra($id_pedido);
                $proveedor = MostrarProveedores();
                $moneda = MostrarMoneda();
                $obras = MostrarObrasActivas();

                if (!empty($pedido_data)) {
                    $pedido = $pedido_data[0];
                    require_once("../_vista/v_pedido_verificar.php");
                } else {
                    $alerta = [
                        "icon" => "error",
                        "title" => "Error",
                        "text" => "Pedido no encontrado",
                        "redirect" => "pedidos_mostrar.php",
                        "timer" => 2000
                    ];
                }
            } else {
                $alerta = [
                    "icon" => "error",
                    "title" => "Error",
                    "text" => "ID de pedido no válido",
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