<?php

require_once("../_modelo/m_producto.php");
require_once("../_conexion/sesion.php");

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Producto Detalle</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_producto.php");
            
            // Obtener el ID del producto desde la URL
            $id = isset($_GET['id']) ? $_GET['id'] : 0;
            $producto = ObtenerProductoPorId($id);
            
            // Verificar si el producto existe
            if ($producto) {
                require_once("../_vista/v_producto_detalle.php");
            } else {
                ?>
                <script Language="JavaScript">
                    alert('Producto no encontrado');
                    location.href = 'producto_mostrar.php';
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