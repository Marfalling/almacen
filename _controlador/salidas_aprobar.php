<?php
//=======================================================================
// CONTROLADOR: salidas_aprobar.php
// Aprueba la salida y genera movimientos
//=======================================================================

header('Content-Type: application/json; charset=utf-8');
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");  
require_once("../_modelo/m_salidas.php");

if (!verificarPermisoEspecifico('aprobar_salidas')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'SALIDAS', 'APROBAR');
    
    echo json_encode([
        "success" => false,
        "message" => "No tienes permiso para aprobar salidas."
    ]);
    exit;
}

$id_salida = isset($_POST['id_salida']) ? intval($_POST['id_salida']) : null;

if (!$id_salida) {
    echo json_encode([
        "success" => false,
        "message" => "ID de salida no proporcionado."
    ]);
    exit;
}

$resultado = AprobarSalidaConMovimientos($id_salida, $id_personal);

if (is_array($resultado)) {
    
    // Caso: Salida anulada por falta de stock
    if (isset($resultado['anulada']) && $resultado['anulada'] === true) {
        
        //  AUDITORÍA: ANULACIÓN AUTOMÁTICA POR STOCK
        GrabarAuditoria($id, $usuario_sesion, 'ANULAR', 'SALIDAS', "ID: $id_salida (ANULADA AUTOMÁTICAMENTE - Falta de stock)");
        
        echo json_encode([
            "success" => false,
            "anulada" => true,
            "message" => $resultado['message']
        ]);
        exit;
    }
    
    // Caso: Aprobación exitosa
    if (isset($resultado['success']) && $resultado['success'] === true) {
        
        //  AUDITORÍA: APROBACIÓN EXITOSA (FORMATO MEJORADO)
        GrabarAuditoria($id, $usuario_sesion, 'APROBAR', 'SALIDAS', "ID: $id_salida (APROBACIÓN TÉCNICA)");
        
    } else {
        
        //  AUDITORÍA: ERROR AL APROBAR
        $mensaje_error = $resultado['message'] ?? 'Error desconocido';
        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL APROBAR', 'SALIDAS', "ID: $id_salida | $mensaje_error");
    }
    
    echo json_encode($resultado);
    
} else {
    
    //  AUDITORÍA: RESPUESTA INESPERADA
    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL APROBAR', 'SALIDAS', "ID: $id_salida | Respuesta inesperada");
    
    echo json_encode([
        "success" => false,
        "message" => "Error inesperado al procesar la aprobación."
    ]);
}

exit;
?>