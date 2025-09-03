<?php
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('crear_personal')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'PERSONAL', 'CREAR');
    header("location: dashboard.php?permisos=true");
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

    <title>Nuevo Personal</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_personal.php");

            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $id_area = $_REQUEST['id_area'];
                $id_cargo = $_REQUEST['id_cargo'];
                $nom = strtoupper($_REQUEST['nom']);
                $ape = strtoupper($_REQUEST['ape']);
                $dni = $_REQUEST['dni'];
                $email = $_REQUEST['email'];
                $tel = $_REQUEST['tel'];
                $est = isset($_REQUEST['est']) ? 1 : 0;

                $rpta = GrabarPersonal($id_area, $id_cargo, $nom, $ape, $dni, $email, $tel, $est);

                if ($rpta == "SI") {
            ?>
                    <script Language="JavaScript">
                        location.href = 'personal_mostrar.php?registrado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'personal_mostrar.php?existe=true';
                    </script>
            <?php
                }
            }
            //-------------------------------------------

            require_once("../_vista/v_personal_nuevo.php");
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