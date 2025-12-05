<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

if (!verificarPermisoEspecifico('crear_modulos')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'MODULOS', 'CREAR');
    header("location: bienvenido.php?permisos=true");
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
            // OPERACIÓN DE REGISTRO
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
                    exit;
                } else {
                    $rpta = GrabarModulo($nom_modulo, $acciones, $est);

                    if ($rpta == "SI") {
                        //  AUDITORÍA: REGISTRO EXITOSO
                        $estado_texto = ($est == 1) ? 'Activo' : 'Inactivo';
                        $descripcion = "Nombre: '$nom_modulo' | Estado: $estado_texto | " . count($acciones) . " permisos asignados";
                        GrabarAuditoria($id, $usuario_sesion, 'REGISTRAR', 'MODULO', $descripcion);
                ?>
                        <script Language="JavaScript">
                            location.href = 'modulo_mostrar.php?registrado=true';
                        </script>
                    <?php
                        exit;
                    } else if ($rpta == "NO") {
                        //  AUDITORÍA: ERROR - YA EXISTE
                        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'MODULO', "Nombre: '$nom_modulo' - Ya existe");
                    ?>
                        <script Language="JavaScript">
                            location.href = 'modulo_mostrar.php?existe=true';
                        </script>
                    <?php
                        exit;
                    } else if ($rpta == "SIN_ACCIONES") {
                        //  AUDITORÍA: ERROR - SIN ACCIONES
                        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'MODULO', "Nombre: '$nom_modulo' - Sin acciones seleccionadas");
                    ?>
                        <script Language="JavaScript">
                            location.href = 'modulo_nuevo.php?sin_acciones=true';
                        </script>
                    <?php
                        exit;
                    } else {
                        //  AUDITORÍA: ERROR GENERAL
                        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'MODULO', "Nombre: '$nom_modulo'");
                    ?>
                        <script Language="JavaScript">
                            location.href = 'modulo_mostrar.php?error=true';
                        </script>
                <?php
                        exit;
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