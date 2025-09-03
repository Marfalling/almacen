<?php
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('crear_modulos')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'MODULOS', 'CREAR');
    header("location: dashboard.php?permisos=true");
    exit;
}

//=======================================================================
// CONTROLADOR: modulo_nuevo.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Nuevo Módulo</title>

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
                $nom_modulo = strtoupper(trim($_REQUEST['nom_modulo']));
                $est = isset($_REQUEST['est']) ? 1 : 0;
                $acciones = isset($_REQUEST['acciones']) ? $_REQUEST['acciones'] : array();

                // Validar que se hayan seleccionado acciones
                if (empty($acciones)) {
            ?>
                    <script Language="JavaScript">
                        location.href = 'modulo_nuevo.php?sin_acciones=true';
                    </script>
                <?php
                } else {
                    $rpta = GrabarModulo($nom_modulo, $acciones, $est);

                    if ($rpta == "SI") {
                ?>
                        <script Language="JavaScript">
                            location.href = 'modulo_mostrar.php?registrado=true';
                        </script>
                    <?php
                    } else if ($rpta == "NO") {
                    ?>
                        <script Language="JavaScript">
                            location.href = 'modulo_mostrar.php?existe=true';
                        </script>
                    <?php
                    } else if ($rpta == "SIN_ACCIONES") {
                    ?>
                        <script Language="JavaScript">
                            location.href = 'modulo_nuevo.php?sin_acciones=true';
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

            require_once("../_vista/v_modulo_nuevo.php");
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