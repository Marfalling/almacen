<?php
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('editar_tipo de material')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'TIPO_MATERIAL', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}
//=======================================================================
// CONTROLADOR: tipo_material_editar.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Editar Tipo de Material</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_tipo_material.php");

            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $id_material_tipo = $_REQUEST['id_material_tipo'];
                $nom = strtoupper($_REQUEST['nom']);
                $est = isset($_REQUEST['est']) ? 1 : 0;

                $rpta = ActualizarMaterialTipo($id_material_tipo, $nom, $est);

                if ($rpta == "SI") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'tipo_material_mostrar.php?actualizado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'tipo_material_mostrar.php?existe=true';
                    </script>
                <?php
                } else {
                ?>
                    <script Language="JavaScript">
                        location.href = 'tipo_material_mostrar.php?error=true';
                    </script>
                <?php
                }
            }
            //-------------------------------------------

            // Obtener ID del tipo de material desde GET
            $id_material_tipo = isset($_GET['id_material_tipo']) ? $_GET['id_material_tipo'] : '';
            if ($id_material_tipo == "") {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos del tipo de material a editar
            $tipo_material_data = ObtenerMaterialTipo($id_material_tipo);
            if ($tipo_material_data) {
                $nom = $tipo_material_data['nom_material_tipo'];
                $est = ($tipo_material_data['est_material_tipo'] == 1) ? "checked" : "";
            } else {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            require_once("../_vista/v_tipo_material_editar.php");
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