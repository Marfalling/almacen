<?php
require_once("../_conexion/sesion.php");

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

            $id_pedido = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

            if (isset($_REQUEST['verificar_item'])) {
                $id_pedido_detalle = $_REQUEST['id_pedido_detalle'];
                $new_cant_fin = $_REQUEST['fin_cant_pedido_detalle'];
                $rpta = verificarItem($id_pedido_detalle, $new_cant_fin);
                
                if ($rpta == "SI") {
            ?>
                    <script Language="JavaScript">
                        alert('Pedido verificado exitosamente');
                        location.href = 'pedido_verificar.php?id=<?php echo $id_pedido; ?>';
                    </script>
                <?php
                } else {
                ?>
                    <script Language="JavaScript">
                        alert('Error al finalizar la verificación del pedido: <?php echo $rpta; ?>');
                    </script>
            <?php
                }
            }

            if ($id_pedido > 0) {
                $pedido_data = ConsultarPedido($id_pedido);
                $pedido_detalle = ConsultarPedidoDetalle($id_pedido);
                $obras = MostrarObrasActivas();
                
                if (!empty($pedido_data)) {
                    $pedido = $pedido_data[0];
                    require_once("../_vista/v_pedido_verificar.php");
                } else {
                    echo "<script>alert('Pedido no encontrado'); location.href='pedidos_mostrar.php';</script>";
                }
            } else {
                echo "<script>alert('ID de pedido no válido'); location.href='pedidos_mostrar.php';</script>";
            }

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