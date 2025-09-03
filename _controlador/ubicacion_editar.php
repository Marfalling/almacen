<?php
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('editar_ubicacion')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'UBICACION', 'EDITAR');
    header("location: dashboard.php?permisos=true");
    exit;
}


//=======================================================================
// CONTROLADOR: ubicacion_editar.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Editar Ubicación</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_ubicacion.php");

            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $id_ubicacion = $_REQUEST['id_ubicacion'];
                $nom = strtoupper($_REQUEST['nom']);
                $est = isset($_REQUEST['est']) ? 1 : 0;

                $rpta = ActualizarUbicacion($id_ubicacion, $nom, $est);

                if ($rpta == "SI") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'ubicacion_mostrar.php?actualizado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'ubicacion_mostrar.php?existe=true';
                    </script>
                <?php
                } else {
                ?>
                    <script Language="JavaScript">
                        location.href = 'ubicacion_mostrar.php?error=true';
                    </script>
                <?php
                }
            }
            //-------------------------------------------

            // Obtener ID de la ubicación desde GET
            $id_ubicacion = isset($_GET['id_ubicacion']) ? $_GET['id_ubicacion'] : '';
            if ($id_ubicacion == "") {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos de la ubicación a editar
            $ubicacion_data = ObtenerUbicacion($id_ubicacion);
            if ($ubicacion_data) {
                $nom = $ubicacion_data['nom_ubicacion'];
                $est = ($ubicacion_data['est_ubicacion'] == 1) ? "checked" : "";
            } else {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            require_once("../_vista/v_ubicacion_editar.php");
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