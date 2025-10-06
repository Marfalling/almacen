<?php
require_once("../_conexion/sesion.php");

// Verificar permiso
if (!verificarPermisoEspecifico('crear_centro_costo')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'CENTRO DE COSTO', 'CREAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

//=======================================================================
// CONTROLADOR: centro_costo_nuevo.php
//=======================================================================

require_once("../_modelo/m_centro_costo.php");

if (isset($_POST['registrar'])) {
    $nom = strtoupper(trim($_POST['nom']));
    $rpta = GrabarCentroCosto($nom);

    if ($rpta == "SI") {
        header("location: centro_costo_mostrar.php?registrado=true");
    } elseif ($rpta == "NO") {
        header("location: centro_costo_mostrar.php?error=duplicado");
    } else {
        header("location: centro_costo_mostrar.php?error=true");
    }
    exit;
}

require_once("../_vista/v_centro_costo_nuevo.php");
?>
