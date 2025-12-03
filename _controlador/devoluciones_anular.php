<?php
//=======================================================================
// DEVOLUCIONES - ANULAR (devoluciones_anular.php)
//=======================================================================

require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('anular_devoluciones')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'DEVOLUCIONES', 'ANULAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

require_once("../_modelo/m_devolucion.php");

// ========================================================================
// VARIABLES DE ALERTA
// ========================================================================
$mostrar_alerta = false;
$tipo_alerta = '';
$titulo_alerta = '';
$mensaje_alerta = '';

// ========================================================================
// CONTROLADOR DE ACCIÓN ANULAR
// ========================================================================
if (isset($_POST['anular']) && isset($_POST['id_devolucion'])) {
    $id = intval($_POST['id_devolucion']);
    $resultado = AnularDevolucion($id);

    if ($resultado === "SI") {
        // Redirigir con parámetros de éxito
        header("location: devoluciones_mostrar.php?anulado=success");
        exit;
    } else {
        // Redirigir con parámetros de error
        $mensaje_error = urlencode($resultado);
        header("location: devoluciones_mostrar.php?anulado=error&msg=" . $mensaje_error);
        exit;
    }
} else {
    // Si acceden sin POST, redirigir a mostrar
    header("location: devoluciones_mostrar.php");
    exit;
}

?>