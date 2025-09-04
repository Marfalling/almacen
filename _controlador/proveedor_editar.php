<?php
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('editar_proveedor')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'PROVEEDOR', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

//=======================================================================
// CONTROLADOR: proveedor_editar.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Editar Proveedor</title>
    
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
                $id_proveedor = $_REQUEST['id_proveedor'];
                $nom = strtoupper($_REQUEST['nom']);
                $ruc = strtoupper($_REQUEST['ruc']);
                $dir = strtoupper($_REQUEST['dir']);
                $tel = strtoupper($_REQUEST['tel']);
                $cont = strtoupper($_REQUEST['cont']);
                $est = isset($_REQUEST['est']) ? 1 : 0;

                $rpta = ActualizarProveedor($id_proveedor, $nom, $ruc, $dir, $tel, $cont, $est);

                if ($rpta == "SI") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'proveedor_mostrar.php?actualizado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'proveedor_mostrar.php?existe=true';
                    </script>
                <?php
                } else {
                ?>
                    <script Language="JavaScript">
                        location.href = 'proveedor_mostrar.php?error=true';
                    </script>
                <?php
                }
            }
            //-------------------------------------------

            // Obtener ID del proveedor desde GET
            $id_proveedor = isset($_GET['id_proveedor']) ? $_GET['id_proveedor'] : '';
            if ($id_proveedor == "") {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos del proveedor a editar
            $proveedor_data = ObtenerProveedor($id_proveedor);
            if ($proveedor_data) {
                $nom = $proveedor_data['nom_proveedor'];
                $ruc = $proveedor_data['ruc_proveedor'];
                $dir = $proveedor_data['dir_proveedor'];
                $tel = $proveedor_data['tel_proveedor'];
                $cont = $proveedor_data['cont_proveedor'];
                $est = ($proveedor_data['est_proveedor'] == 1) ? "checked" : "";
            } else {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            require_once("../_vista/v_proveedor_editar.php");
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