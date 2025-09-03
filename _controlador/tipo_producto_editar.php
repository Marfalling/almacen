<?php
require_once("../_conexion/sesion.php");

// Usa el nombre CORRECTO con ESPACIOS
if (!verificarPermisoEspecifico('editar_tipo de producto')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'TIPO DE PRODUCTO', 'EDITAR');
    header("location: dashboard.php?permisos=true");
    exit;
}

//=======================================================================
// CONTROLADOR: tipo_producto_editar.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Editar Tipo de Producto</title>

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
                $id_producto_tipo = $_REQUEST['id_producto_tipo'];
                $nom = strtoupper($_REQUEST['nom']);
                $est = isset($_REQUEST['est']) ? 1 : 0;

                $rpta = EditarProductoTipo($id_producto_tipo, $nom, $est);

                if ($rpta == "SI") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'tipo_producto_mostrar.php?actualizado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'tipo_producto_mostrar.php?error=true';
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

            // Obtener ID del tipo de producto desde GET
            $id_producto_tipo = isset($_GET['id_producto_tipo']) ? $_GET['id_producto_tipo'] : '';
            if ($id_producto_tipo == "") {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos del tipo de producto a editar
            $tipo_producto_data = ObtenerProductoTipo($id_producto_tipo);
            if ($tipo_producto_data) {
                $nom = $tipo_producto_data['nom_producto_tipo'];
                $est = ($tipo_producto_data['est_producto_tipo'] == 1) ? "checked" : "";
            } else {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            require_once("../_vista/v_tipo_producto_editar.php");
            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>

    <?php require_once("../_vista/v_script.php"); ?>
</body>

</html>