<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_devolucion.php"); // cambiamos al modelo de devoluciones
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
    ?>
</body>
</html>