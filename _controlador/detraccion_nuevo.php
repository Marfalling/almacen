<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php"); 

//=======================================================================
// CONTROLADOR: detraccion_nuevo.php 
//=======================================================================

// Verificar permisos
if (!verificarPermisoEspecifico('crear_detraccion')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'DETRACCION', 'CREAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

require_once("../_modelo/m_detraccion.php");

// Obtener tipos de detracción para el formulario
$tipos_detraccion = ObtenerTiposDetraccion();

//-------------------------------------------
// OPERACIÓN DE REGISTRO
//-------------------------------------------
if (isset($_REQUEST['registrar'])) {
    $nom                = strtoupper(trim($_REQUEST['nom'] ?? ''));
    $cod_detraccion     = strtoupper(trim($_REQUEST['cod_detraccion'] ?? ''));
    $porcentaje         = floatval($_REQUEST['porcentaje'] ?? 0);
    $id_detraccion_tipo = intval($_REQUEST['id_detraccion_tipo'] ?? 0);

    // Validaciones básicas (opcional - puedes mantenerlas o quitarlas)
    if ($nom === '' || $cod_detraccion === '' || $id_detraccion_tipo <= 0 || $porcentaje <= 0) {
        header("location: detraccion_nuevo.php?error=validacion");
        exit;
    }

    $rpta = GrabarDetraccion($nom, $cod_detraccion, $porcentaje, $id_detraccion_tipo);

    if ($rpta == "SI") {
        //  AUDITORÍA: REGISTRO EXITOSO
        GrabarAuditoria($id, $usuario_sesion, 'REGISTRAR', 'DETRACCION', "$cod_detraccion - $nom");
        header("location: detraccion_mostrar.php?registrado=true");
        exit;
    } elseif ($rpta == "NO") {
        //  AUDITORÍA: ERROR - YA EXISTE
        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'DETRACCION', "$cod_detraccion - $nom (YA EXISTE)");
        header("location: detraccion_mostrar.php?error=duplicado");
        exit;
    } else {
        //  AUDITORÍA: ERROR AL REGISTRAR
        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'DETRACCION', "$cod_detraccion - $nom");
        header("location: detraccion_mostrar.php?error=true");
        exit;
    }
}
//-------------------------------------------
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Nueva Detracción</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">

            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");
            require_once("../_vista/v_detraccion_nuevo.php");
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