<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php"); 

if (!verificarPermisoEspecifico('crear_cargo')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'CARGO', 'CREAR');
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

    <title>Nuevo Cargo</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_cargo.php");

            //-------------------------------------------
            // OPERACIÓN DE REGISTRO
            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $nom = strtoupper($_REQUEST['nom']);
                $est = isset($_REQUEST['est']) ? 1 : 0;

                $rpta = GrabarCargo($nom, $est);

                if ($rpta == "SI") {
                    //  AUDITORÍA: REGISTRO EXITOSO
                    GrabarAuditoria($id, $usuario_sesion, 'REGISTRAR', 'CARGO', $nom);
            ?>
                    <script Language="JavaScript">
                        location.href = 'cargo_mostrar.php?registrado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                    //  AUDITORÍA: YA EXISTE
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'CARGO', "$nom YA EXISTE");
                ?>
                    <script Language="JavaScript">
                        location.href = 'cargo_mostrar.php?existe=true';
                    </script>
            <?php
                }
            }
            //-------------------------------------------

            require_once("../_vista/v_cargo_nuevo.php");
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