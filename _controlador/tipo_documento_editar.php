<?php
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('editar_tipo de documento')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'MONEDA', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

//=======================================================================
// CONTROLADOR: tipo_documento_editar.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Editar Tipo Documento</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_tipo_documento.php");

            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $id_tipo_documento = $_REQUEST['id_tipo_documento'];
                $nom = strtoupper($_REQUEST['nom']);
                $est = isset($_REQUEST['est']) ? 1 : 0;

                $rpta = EditarTipoDocumento($id_tipo_documento, $nom, $est);

                if ($rpta == "SI") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'tipo_documento_mostrar.php?actualizado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'tipo_documento_mostrar.php?error=true';
                    </script>
                <?php
                }
            }
            //-------------------------------------------

            // Obtener ID de la tipo_documento desde GET
            $id_tipo_documento = isset($_GET['id_tipo_documento']) ? $_GET['id_tipo_documento'] : '';
            if ($id_tipo_documento == "") {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos de la tipo_documento a editar
            $tipo_documento_data = ObtenerTipoDocumento($id_tipo_documento);
            if ($tipo_documento_data) {
                $nom = $tipo_documento_data['nom_tipo_documento'];
                $est = ($tipo_documento_data['est_tipo_documento'] == 1) ? "checked" : "";
            } else {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            require_once("../_vista/v_tipo_documento_editar.php");
            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>

    <?php require_once("../_vista/v_script.php"); ?>
</body>

</html>