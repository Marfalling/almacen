<?php
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('editar_banco')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'MONEDA', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

//=======================================================================
// CONTROLADOR: banco_editar.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Editar Banco</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_banco.php");

            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $id_banco = $_REQUEST['id_banco'];
                $cod = strtoupper(trim($_REQUEST['cod']));
                $nom = strtoupper($_REQUEST['nom']);
                $est = isset($_REQUEST['est']) ? 1 : 0;

                 $rpta = EditarBanco($id_banco, $cod, $nom, $est);

                if ($rpta == "SI") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'banco_mostrar.php?actualizado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'banco_mostrar.php?error=true';
                    </script>
                <?php
                }
            }
            //-------------------------------------------

            // Obtener ID de la banco desde GET
            $id_banco = isset($_GET['id_banco']) ? $_GET['id_banco'] : '';
            if ($id_banco == "") {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos de la banco a editar
            $banco_data = ObtenerBanco($id_banco);
            if ($banco_data) {
                $cod = $banco_data['cod_banco'];
                $nom = $banco_data['nom_banco'];
                $est = ($banco_data['est_banco'] == 1) ? "checked" : "";
            } else {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            require_once("../_vista/v_banco_editar.php");
            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>

    <?php require_once("../_vista/v_script.php"); ?>
</body>

</html>