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
    
    <title>Detalle de Ingreso Directo</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            // Obtener el ID del ingreso desde la URL
            $id_ingreso = isset($_GET['id_ingreso']) ? intval($_GET['id_ingreso']) : 0;
            
            // Obtener datos del ingreso directo
            $detalle_ingreso_directo = ObtenerDetalleIngresoDirecto($id_ingreso);
            
            // Verificar si el ingreso existe
            if ($detalle_ingreso_directo && !empty($detalle_ingreso_directo['ingreso'])) {
                require_once("../_vista/v_ingresos_detalle_directo.php");
            } else {
                ?>
                <script Language="JavaScript">
                    alert('Ingreso directo no encontrado o sin datos');
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