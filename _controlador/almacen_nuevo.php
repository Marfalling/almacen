<?php

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Nuevo Almacén</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_almacen.php");

            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $id_cliente = $_REQUEST['id_cliente'];
                $id_obra = $_REQUEST['id_obra'];
                $nom = strtoupper($_REQUEST['nom']);
                $est = isset($_REQUEST['est']) ? 1 : 0;

                $rpta = GrabarAlmacen($id_cliente, $id_obra, $nom, $est);

                if ($rpta == "SI") {
            ?>
                    <script Language="JavaScript">
                        location.href = 'almacen_mostrar.php?registrado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'almacen_mostrar.php?existe=true';
                    </script>
            <?php
                }
            }
            //-------------------------------------------

            require_once("../_vista/v_almacen_nuevo.php");
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