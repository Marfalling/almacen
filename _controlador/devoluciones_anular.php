<?php
//=======================================================================
// DEVOLUCIONES - ANULAR CON AUDITORÍA (devoluciones_anular.php)
//=======================================================================

require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

if (!verificarPermisoEspecifico('anular_devoluciones')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'DEVOLUCION', 'ANULAR');
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
    $id_devolucion = intval($_POST['id_devolucion']);
    
    //  VALIDAR QUE LA DEVOLUCIÓN EXISTE
    $devolucion_data = ConsultarDevolucion($id_devolucion);
    
    if (empty($devolucion_data)) {
        //  AUDITORÍA: DEVOLUCIÓN NO ENCONTRADA
        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL ANULAR', 'DEVOLUCION', "ID: $id_devolucion - Devolución no encontrada");
        
        header("location: devoluciones_mostrar.php?anulado=error&msg=" . urlencode("Devolución no encontrada"));
        exit;
    }
    
    //  VERIFICAR ESTADO ACTUAL
    $estado_actual = intval($devolucion_data[0]['est_devolucion']);
    
    if ($estado_actual == 0) {
        //  AUDITORÍA: YA ESTÁ ANULADA
        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL ANULAR', 'DEVOLUCION', "ID: $id_devolucion | Ya está anulada");
        
        header("location: devoluciones_mostrar.php?anulado=error&msg=" . urlencode("La devolución ya está anulada"));
        exit;
    }
    
    //  EJECUTAR ANULACIÓN
    $resultado = AnularDevolucion($id_devolucion);

    if ($resultado === "SI") {
        //  AUDITORÍA: ANULACIÓN EXITOSA
        GrabarAuditoria($id, $usuario_sesion, 'ANULAR', 'DEVOLUCION', "ID: $id_devolucion");
        
        header("location: devoluciones_mostrar.php?anulado=success");
        exit;
    } else {
        //  AUDITORÍA: ERROR AL ANULAR
        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL ANULAR', 'DEVOLUCION', "ID: $id_devolucion | " . substr($resultado, 0, 100));
        
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