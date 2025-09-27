<?php

require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('crear_proveedor')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'PROVEEDOR', 'CREAR');
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
            require_once("../_modelo/m_moneda.php");

            $monedas = MostrarMoneda();

            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $nom = strtoupper($_REQUEST['nom']);
                $ruc = strtoupper($_REQUEST['ruc']);
                $dir = strtoupper($_REQUEST['dir']);
                $tel = strtoupper($_REQUEST['tel']);
                $cont = strtoupper($_REQUEST['cont']);
                $est = isset($_REQUEST['est']) ? 1 : 0;

                // Nuevos campos
                $email = strtolower(trim($_REQUEST['email']));
                $item = isset($_REQUEST['item']) ? (int) $_REQUEST['item'] : null;
                $banco = strtoupper(trim($_REQUEST['banco']));
                $id_moneda = !empty($_REQUEST['id_moneda']) ? (int) $_REQUEST['id_moneda'] : null;
                $nro_cuenta_corriente = trim($_REQUEST['nro_cuenta_corriente']);
                $nro_cuenta_interbancaria= trim($_REQUEST['nro_cuenta_interbancaria']);

                $rpta = GrabarProveedor($nom, $ruc, $dir, $tel, $cont, $est, $email, $item, $banco, $id_moneda, $nro_cuenta_corriente, $nro_cuenta_interbancaria);

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