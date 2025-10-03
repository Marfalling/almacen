<?php
require_once("../_conexion/sesion.php");

// Verificar permisos
if (!verificarPermisoEspecifico('eliminar_detraccion')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'DETRACCION', 'ELIMINAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

//=======================================================================
// CONTROLADOR: detraccion_eliminar.php
//=======================================================================

require_once("../_modelo/m_detraccion.php");

$id_detraccion = isset($_GET['id_detraccion']) ? $_GET['id_detraccion'] : '';
if ($id_detraccion == '') {
    header("location: detraccion_mostrar.php?error=true");
    exit;
}

$rpta = EliminarDetraccion($id_detraccion);

if ($rpta == "SI") {
    header("location: detraccion_mostrar.php?eliminado=true");
    exit;
} else {
    header("location: detraccion_mostrar.php?error=true");
    exit;
}










