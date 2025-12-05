<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php"); 

//=======================================================================
// CONTROLADOR: centro_costo_nuevo.php 
//=======================================================================

require_once("../_modelo/m_centro_costo.php");

// Verificar permiso
if (!verificarPermisoEspecifico('crear_centro de costo')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'CENTRO DE COSTO', 'CREAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

//-------------------------------------------
// OPERACIÓN DE REGISTRO
//-------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nom_centro_costo']);
    $rpta = GrabarCentroCosto($nombre);

    if ($rpta == "SI") {
        //  AUDITORÍA: REGISTRO EXITOSO
        GrabarAuditoria($id, $usuario_sesion, 'REGISTRAR', 'CENTRO DE COSTO', $nombre);
        header("Location: centro_costo_mostrar.php?registrado=true");
    } else {
        //  AUDITORÍA: ERROR AL REGISTRAR
        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'CENTRO DE COSTO', "$nombre (YA EXISTE)");
        header("Location: centro_costo_mostrar.php?error=true");
    }
    exit;
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
    
    <title>Nuevo Centro de Costo</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">

            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");
            require_once("../_vista/v_centro_costo_nuevo.php");
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