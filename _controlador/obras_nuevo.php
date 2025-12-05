<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

// Verificar permisos
if (!verificarPermisoEspecifico('crear_obras')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'OBRAS', 'CREAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

require_once("../_modelo/m_obras.php");

//-------------------------------------------
// OPERACIÓN DE REGISTRO
//-------------------------------------------
if (isset($_POST['registrar'])) {
    $nom = strtoupper(trim($_POST['nom']));
    $est = isset($_POST['est']) ? 1 : 0;

    $rpta = RegistrarObra($nom, $est);

    if ($rpta == "SI") {
        //  AUDITORÍA: REGISTRO EXITOSO
        $estado_texto = ($est == 1) ? 'Activo' : 'Inactivo';
        $descripcion = "Nombre: '$nom' | Estado: $estado_texto";
        GrabarAuditoria($id, $usuario_sesion, 'REGISTRAR', 'OBRAS', $descripcion);
        header("Location: obras_mostrar.php?registrado=true");
        exit;
    } elseif ($rpta == "NO") {
        //  AUDITORÍA: ERROR - YA EXISTE
        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'OBRAS', "Nombre: '$nom' - Ya existe");
        header("Location: obras_mostrar.php?existe=true");
        exit;
    } else {
        //  AUDITORÍA: ERROR GENERAL
        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'OBRAS', "Nombre: '$nom'");
        header("Location: obras_mostrar.php?error=true");
        exit;
    }
}
//-------------------------------------------
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nueva Obra</title>
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
<div class="container body">
    <div class="main_container">
        <?php
        require_once("../_vista/v_menu.php");
        require_once("../_vista/v_menu_user.php");
        require_once("../_vista/v_obras_nuevo.php");
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