<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php"); 

if (!verificarPermisoEspecifico('crear_cliente')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'CLIENTE', 'CREAR');
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

    <title>Nuevo Cliente</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_clientes.php");

            //-------------------------------------------
            // OPERACIÓN DE REGISTRO
            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $nom = strtoupper(trim($_REQUEST['nom'])); 
                $est = isset($_REQUEST['est']) ? 1 : 0;

                $rpta = GrabarClientes($nom, $est);

                if ($rpta == "SI") {
                    //  AUDITORÍA: REGISTRO EXITOSO
                    GrabarAuditoria($id, $usuario_sesion, 'REGISTRAR', 'CLIENTE', $nom);
            ?>
                    <script Language="JavaScript">
                        location.href = 'clientes_mostrar.php?registrado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                    //  AUDITORÍA: ERROR AL REGISTRAR
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'CLIENTE', "$nom YA EXISTE");
                ?>
                    <script Language="JavaScript">
                        location.href = 'clientes_mostrar.php?existe=true';
                    </script>
            <?php
                }
            }
            //-------------------------------------------

            require_once("../_vista/v_clientes_nuevo.php");
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