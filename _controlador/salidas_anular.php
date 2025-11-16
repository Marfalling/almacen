<?php
require_once("../_conexion/sesion.php");

header('Content-Type: application/json');

/* Verificar permiso
if (!verificarPermisoEspecifico('anular_salidas')) {
    echo json_encode(['success' => false, 'message' => 'No tienes permiso para anular salidas.']);
    exit;
}*/
if (!verificarPermisoEspecifico('anular_salidas')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'SALIDAS', 'ANULAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de salida no recibido.']);
    exit;
}

$id_salida = intval($_POST['id']);

require_once("../_modelo/m_salidas.php");
require_once("../_modelo/m_auditoria.php");
require_once("../_modelo/m_pedidos.php");

$id_usuario = $_SESSION['id'] ?? 0;

// ============================================================
// 1️⃣ ANULAR LA SALIDA
// ============================================================
$result = AnularSalida($id_salida, $id_usuario);

// ============================================================
// 2️⃣ PROCESAR RESULTADO
// ============================================================
if (is_array($result) && isset($result['success'])) {
    
    if ($result['success']) {
        error_log("✅ Salida anulada correctamente");
        
        // ✅ REGISTRAR AUDITORÍA
        // GrabarAuditoria($id_usuario, $_SESSION['usuario_sesion'] ?? '', 'ANULACIÓN DE SALIDA', 'SALIDAS', "Anuló salida ID: $id_salida");
        
        echo json_encode([
            'success' => true, 
            'message' => $result['message']
        ]);
        
    } else {
        error_log("❌ Error al anular salida: " . $result['message']);
        // GrabarAuditoria($id_usuario, $_SESSION['usuario_sesion'] ?? '', 'ERROR ANULAR SALIDA', 'SALIDAS', "Error en salida ID: $id_salida - " . $result['message']);
        
        echo json_encode([
            'success' => false, 
            'message' => $result['message']
        ]);
    }
    
} else {
    // error
    $mensaje_error = is_string($result) ? $result : 'Error desconocido al anular salida';
    error_log("❌ Error al anular salida: $mensaje_error");
    
    echo json_encode([
        'success' => false, 
        'message' => $mensaje_error
    ]);
}
?>