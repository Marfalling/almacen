<?php
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('crear_ubicacion')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'UBICACION', 'CREAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

//=======================================================================
// CONTROLADOR: ubicacion_nuevo.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Nueva Ubicación</title>

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
                $nom = strtoupper($_REQUEST['nom']);
                $est = isset($_REQUEST['est']) ? 1 : 0;

                $rpta = GrabarUbicacion($nom, $est);

                if ($rpta == "SI") {
            ?>
                    <script Language="JavaScript">
                        location.href = 'ubicacion_mostrar.php?registrado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'ubicacion_mostrar.php?existe=true';
                    </script>
            <?php
                }
            }
            //-------------------------------------------

            require_once("../_vista/v_ubicacion_nuevo.php");
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
