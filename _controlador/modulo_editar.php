<?php
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('editar_modulos')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'MODULOS', 'EDITAR');
    header("location: dashboard.php?permisos=true");
    exit;
}
//=======================================================================
// CONTROLADOR: modulo_editar.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Editar Módulo</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_modulo.php");

            // Obtener las acciones disponibles para mostrar en el formulario
            $acciones_disponibles = MostrarAcciones();

            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $id_modulo = $_REQUEST['id_modulo'];
                $nom_modulo = strtoupper(trim($_REQUEST['nom_modulo']));
                $est = isset($_REQUEST['est']) ? 1 : 0;
                $acciones = isset($_REQUEST['acciones']) ? $_REQUEST['acciones'] : array();

                // Validar que se hayan seleccionado acciones
                if (empty($acciones)) {
                ?>
                    <script Language="JavaScript">
                        location.href = 'modulo_editar.php?id_modulo=<?php echo $id_modulo; ?>&sin_acciones=true';
                    </script>
                <?php
                } else {
                    $rpta = ActualizarModuloCompleto($id_modulo, $nom_modulo, $acciones, $est);

                    if ($rpta == "SI") {
                ?>
                        <script Language="JavaScript">
                            location.href = 'modulo_mostrar.php?actualizado=true';
                        </script>
                    <?php
                    } else if ($rpta == "NO") {
                    ?>
                        <script Language="JavaScript">
                            location.href = 'modulo_mostrar.php?existe=true';
                        </script>
                    <?php
                    } else {
                    ?>
                        <script Language="JavaScript">
                            location.href = 'modulo_mostrar.php?error=true';
                        </script>
                <?php
                    }
                }
            }
            //-------------------------------------------

            // Obtener ID del módulo desde GET
            $id_modulo = isset($_GET['id_modulo']) ? $_GET['id_modulo'] : '';
            if ($id_modulo == "") {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos del módulo a editar
            $modulo_data = ObtenerModulo($id_modulo);
            if ($modulo_data) {
                $nom_modulo = $modulo_data['nom_modulo'];
                $est = ($modulo_data['est_modulo'] == 1) ? "checked" : "";
                
                // Obtener las acciones actualmente asignadas al módulo
                $acciones_modulo = ObtenerAccionesModulo($id_modulo);
            } else {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            require_once("../_vista/v_modulo_editar.php");
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