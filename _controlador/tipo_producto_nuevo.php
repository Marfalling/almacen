<?php
require_once("../_conexion/sesion.php");

// Usa el nombre CORRECTO con ESPACIOS
if (!verificarPermisoEspecifico('crear_tipo de producto')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'TIPO DE PRODUCTO', 'CREAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

//=======================================================================
// CONTROLADOR: tipo_producto_nuevo.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Nuevo Tipo de Producto</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_tipo_producto.php");

            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $nom = strtoupper($_REQUEST['nom']);
                $est = isset($_REQUEST['est']) ? 1 : 0;

                $rpta = GrabarProductoTipo($nom, $est);

                if ($rpta == "SI") {
            ?>
                    <script Language="JavaScript">
                        location.href = 'tipo_producto_mostrar.php?registrado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'tipo_producto_mostrar.php?existe=true';
                    </script>
            <?php
                } else {
                ?>
                    <script Language="JavaScript">
                        location.href = 'tipo_producto_mostrar.php?error=true';
                    </script>
            <?php
                }
            }
            //-------------------------------------------

            require_once("../_vista/v_tipo_producto_nuevo.php");
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