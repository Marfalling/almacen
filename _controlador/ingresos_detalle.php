<?php
require_once("../_conexion/sesion.php");



require_once("../_modelo/m_ingreso.php");

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Detalle de Ingresos</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            // Obtener el ID de la compra desde la URL
            $id_compra = isset($_GET['id_compra']) ? $_GET['id_compra'] : 0;
            
            // Obtener datos del ingreso
            $detalle_ingreso = ObtenerDetalleIngresoPorCompra($id_compra);
            
            // Verificar si el ingreso existe
            if ($detalle_ingreso && !empty($detalle_ingreso['compra'])) {
                require_once("../_vista/v_ingresos_detalle.php");
            } else {
                ?>
                <script Language="JavaScript">
                    alert('Orden de compra no encontrada o sin datos');
                    location.href = 'ingresos_mostrar.php';
                </script>
                <?php
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