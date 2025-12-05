<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

if (!verificarPermisoEspecifico('crear_unidad de medida')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'UNIDAD_MEDIDA', 'CREAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}
//=======================================================================
// CONTROLADOR: unidad_medida_nuevo.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Nueva Unidad de Medida</title>

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
                $nom = strtoupper($_REQUEST['nom']);
                $est = isset($_REQUEST['est']) ? 1 : 0;

                $rpta = GrabarUnidadMedida($nom, $est);

                if ($rpta == "SI") {
                    //  AUDITORÍA: REGISTRO EXITOSO
                    $estado_texto = ($est == 1) ? 'Activo' : 'Inactivo';
                    $descripcion = "Nombre: '$nom' | Estado: $estado_texto";
                    GrabarAuditoria($id, $usuario_sesion, 'REGISTRAR', 'UNIDAD_MEDIDA', $descripcion);
            ?>
                    <script Language="JavaScript">
                        location.href = 'unidad_medida_mostrar.php?registrado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                    //  AUDITORÍA: ERROR - YA EXISTE
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'UNIDAD_MEDIDA', "Nombre: '$nom' - Ya existe");
                ?>
                    <script Language="JavaScript">
                        location.href = 'unidad_medida_mostrar.php?existe=true';
                    </script>
            <?php
                } else {
                    //  AUDITORÍA: ERROR GENERAL
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'UNIDAD_MEDIDA', "Nombre: '$nom' - Error del sistema");
                ?>
                    <script Language="JavaScript">
                        location.href = 'unidad_medida_mostrar.php?error=true';
                    </script>
            <?php
                }
            }
            //-------------------------------------------

            require_once("../_vista/v_unidad_medida_nuevo.php");
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