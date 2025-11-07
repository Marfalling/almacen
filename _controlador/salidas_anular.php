<?php
require_once("../_conexion/sesion.php");

header('Content-Type: application/json');

/* Verificar permiso
if (!verificarPermisoEspecifico('anular_salidas')) {
    echo json_encode(['success' => false, 'message' => 'No tienes permiso para anular salidas.']);
    exit;
}*/

if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de salida no recibido.']);
    exit;
}

$id_salida = intval($_POST['id']);

require_once("../_modelo/m_salidas.php");
require_once("../_modelo/m_auditoria.php");

$id_usuario = $_SESSION['id'] ?? 0;

// ============================================================
// 1️⃣ ANULAR LA SALIDA
// ============================================================
$result = AnularSalida($id_salida, $id_usuario);

// ============================================================
// 2️⃣ PROCESAR RESULTADO
// ============================================================
if (strpos($result, "SI|") === 0) {
    
    // Extraer ítems afectados
    $items_json = substr($result, 3);
    $items_afectados = json_decode($items_json, true);
    
    error_log("📊 Actualizando estados de ítems | Total: " . count($items_afectados));
    
    // ============================================================
    // 3️⃣ ACTUALIZAR ESTADO DE CADA ÍTEM (fuera de la transacción)
    // ============================================================
    if (!empty($items_afectados)) {
        
        require_once("../_modelo/m_pedidos.php");
        
        foreach ($items_afectados as $id_pedido_detalle) {
            error_log("   🔄 Actualizando ítem: $id_pedido_detalle");
            VerificarEstadoItemPorDetalle($id_pedido_detalle);
        }
        
        error_log("✅ Estados de ítems actualizados: " . count($items_afectados) . " ítems");
    }
    
    // ============================================================
    // 4️⃣ REGISTRAR AUDITORÍA
    // ============================================================
    //GrabarAuditoria($id_usuario, $_SESSION['usuario_sesion'] ?? '', 'ANULACIÓN DE SALIDA', 'SALIDAS', "Anuló salida ID: $id_salida");
    
    echo json_encode([
        'success' => true, 
        'message' => 'La salida fue anulada correctamente.'
    ]);
    
} else {
    // ============================================================
    // 5️⃣ MANEJAR ERROR
    // ============================================================
    error_log("❌ Error al anular salida: $result");
    //GrabarAuditoria($id_usuario, $_SESSION['usuario_sesion'] ?? '', 'ERROR ANULAR SALIDA', 'SALIDAS', "Error en salida ID: $id_salida - $result");
    
    echo json_encode([
        'success' => false, 
        'message' => $result
    ]);
}