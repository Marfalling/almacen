<?php
require_once("../_conexion/sesion.php");

// Verificar permisos
if (!verificarPermisoEspecifico('ver_centro_costo')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'CENTRO DE COSTO', 'VER');
    header("location: bienvenido.php?permisos=true");
    exit;
}

//=======================================================================
// CONTROLADOR: centro_costo_mostrar.php
//=======================================================================

require_once("../_modelo/m_centro_costo.php");

// Verificar si se envió acción de cambio de estado
if (isset($_GET['accion']) && $_GET['accion'] === 'estado') {
    $id = intval($_GET['id_centro_costo']);
    $nuevo_estado = intval($_GET['estado']);

    if ($id > 0) {
        $rpta = CambiarEstadoCentroCosto($id, $nuevo_estado);
        if ($rpta == "SI") {
            header("location: centro_costo_mostrar.php?actualizado=true");
            exit;
        } else {
            header("location: centro_costo_mostrar.php?error=true");
            exit;
        }
    }
}

// Obtener lista de centros de costo
$centros = ObtenerCentrosCosto();

// Cargar vista
require_once("../_vista/v_centro_costo_mostrar.php");
?>
