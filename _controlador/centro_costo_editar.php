<?php
require_once("../_conexion/sesion.php");

// Verificar permiso
if (!verificarPermisoEspecifico('editar_centro_costo')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'CENTRO DE COSTO', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

//=======================================================================
// CONTROLADOR: centro_costo_editar.php
//=======================================================================

require_once("../_modelo/m_centro_costo.php");

if (isset($_POST['actualizar'])) {
    $id_centro_costo = intval($_POST['id_centro_costo']);
    $nom = strtoupper(trim($_POST['nom']));
    $rpta = EditarCentroCosto($id_centro_costo, $nom);

    if ($rpta == "SI") {
        header("location: centro_costo_mostrar.php?actualizado=true");
    } elseif ($rpta == "NO") {
        header("location: centro_costo_mostrar.php?error=duplicado");
    } else {
        header("location: centro_costo_mostrar.php?error=true");
    }
    exit;
}

// Obtener datos
$id_centro_costo = isset($_GET['id_centro_costo']) ? intval($_GET['id_centro_costo']) : 0;
if ($id_centro_costo <= 0) {
    header("location: centro_costo_mostrar.php?error=true");
    exit;
}

$centro = ObtenerCentroCostoPorId($id_centro_costo);
if (!$centro) {
    header("location: centro_costo_mostrar.php?error=true");
    exit;
}

require_once("../_vista/v_centro_costo_editar.php");
?>
