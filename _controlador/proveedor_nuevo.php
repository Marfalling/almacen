<?php

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Nuevo Proveedor</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_proveedor.php");

            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $nom = strtoupper($_REQUEST['nom']);
                $ruc = strtoupper($_REQUEST['ruc']);
                $dir = strtoupper($_REQUEST['dir']);
                $tel = strtoupper($_REQUEST['tel']);
                $cont = strtoupper($_REQUEST['cont']);
                $est = isset($_REQUEST['est']) ? 1 : 0;

                $rpta = GrabarProveedor($nom, $ruc, $dir, $tel, $cont, $est);

                if ($rpta == "SI") {
            ?>
                    <script Language="JavaScript">
                        location.href = 'proveedor_mostrar.php?registrado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'proveedor_mostrar.php?existe=true';
                    </script>
            <?php
                }
            }
            //-------------------------------------------

            require_once("../_vista/v_proveedor_nuevo.php");
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