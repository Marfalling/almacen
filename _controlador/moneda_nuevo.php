<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

if (!verificarPermisoEspecifico('crear_moneda')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'MONEDA', 'CREAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nueva Moneda</title>
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");
            require_once("../_modelo/m_moneda.php");

            //-------------------------------------------
            // OPERACIÓN DE REGISTRO
            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $nom = strtoupper($_REQUEST['nom']);
                $est = isset($_REQUEST['est']) ? 1 : 0;

                $rpta = GrabarMoneda($nom, $est);

                if ($rpta == "SI") {
                    //  AUDITORÍA: REGISTRO EXITOSO
                    $estado_texto = ($est == 1) ? 'Activo' : 'Inactivo';
                    $descripcion = "Nombre: '$nom' | Estado: $estado_texto";
                    GrabarAuditoria($id, $usuario_sesion, 'REGISTRAR', 'MONEDA', $descripcion);
                ?>
                    <script Language="JavaScript">
                        location.href = 'moneda_mostrar.php?registrado=true';
                    </script>
                <?php
                    exit;
                } else if ($rpta == "NO") {
                    //  AUDITORÍA: ERROR - YA EXISTE
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'MONEDA', "Nombre: '$nom' - Ya existe");
                ?>
                    <script Language="JavaScript">
                        location.href = 'moneda_mostrar.php?existe=true';
                    </script>
                <?php
                    exit;
                } else {
                    // AUDITORÍA: ERROR GENERAL
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'MONEDA', "Nombre: '$nom'");
                ?>
                    <script Language="JavaScript">
                        location.href = 'moneda_mostrar.php?error=true';
                    </script>
                <?php
                    exit;
                }
            }
            //-------------------------------------------

            require_once("../_vista/v_moneda_nuevo.php");
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