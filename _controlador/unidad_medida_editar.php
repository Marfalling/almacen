<?php
require_once("../_conexion/sesion.php");

//=======================================================================
// CONTROLADOR: unidad_medida_editar.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Editar Unidad de Medida</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_unidad_medida.php");

            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $id_unidad_medida = $_REQUEST['id_unidad_medida'];
                $nom = strtoupper($_REQUEST['nom']);
                $est = isset($_REQUEST['est']) ? 1 : 0;

                $rpta = ActualizarUnidadMedida($id_unidad_medida, $nom, $est);

                if ($rpta == "SI") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'unidad_medida_mostrar.php?actualizado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'unidad_medida_mostrar.php?existe=true';
                    </script>
                <?php
                } else {
                ?>
                    <script Language="JavaScript">
                        location.href = 'unidad_medida_mostrar.php?error=true';
                    </script>
                <?php
                }
            }
            //-------------------------------------------

            // Obtener ID de la unidad de medida desde GET
            $id_unidad_medida = isset($_GET['id_unidad_medida']) ? $_GET['id_unidad_medida'] : '';
            if ($id_unidad_medida == "") {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos de la unidad de medida a editar
            $unidad_medida_data = ObtenerUnidadMedida($id_unidad_medida);
            if ($unidad_medida_data) {
                $nom = $unidad_medida_data['nom_unidad_medida'];
                $est = ($unidad_medida_data['est_unidad_medida'] == 1) ? "checked" : "";
            } else {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            require_once("../_vista/v_unidad_medida_editar.php");
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