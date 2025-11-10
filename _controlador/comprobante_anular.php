<?php
// ====================================================================
// CONTROLADOR: Anular Comprobante
// Procesa solicitudes AJAX para anular comprobantes de pago
// ====================================================================

header('Content-Type: application/json; charset=utf-8');

// Iniciar sesión
session_start();

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['id_personal'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No tiene permisos para realizar esta acción. Debe iniciar sesión.'
    ]);
    exit;
}

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método de solicitud no permitido.'
    ]);
    exit;
}

// Verificar que se envió el ID del comprobante
if (!isset($_POST['id_comprobante']) || empty($_POST['id_comprobante'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No se especificó el comprobante a anular.'
    ]);
    exit;
}

// Obtener ID del comprobante
$id_comprobante = intval($_POST['id_comprobante']);

if ($id_comprobante <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de comprobante inválido.'
    ]);
    exit;
}

// Incluir el modelo de comprobantes
require_once("../_modelo/m_comprobante.php");

// ====================================================================
// EJECUTAR ANULACIÓN
// ====================================================================
try {
    $resultado = AnularComprobante($id_comprobante);
    
    // La función AnularComprobante retorna un array con 'success' y 'message'
    if ($resultado['success']) {
        // ====================================================================
        // REGISTRAR EN AUDITORÍA (opcional)
        // ====================================================================
        /*
        require_once("../_modelo/m_auditoria.php");
        GrabarAuditoria(
            0, // id_registro (puede ser 0 o el id del comprobante)
            $_SESSION['id_personal'],
            'ANULAR COMPROBANTE',
            'COMPROBANTES',
            'Comprobante ID: ' . $id_comprobante
        );
        */
        
        // Retornar respuesta exitosa
        echo json_encode([
            'success' => true,
            'message' => $resultado['message'],
            'id_comprobante' => $id_comprobante,
            'id_compra' => isset($resultado['id_compra']) ? $resultado['id_compra'] : null
        ]);
    } else {
        // Error en la anulación
        echo json_encode([
            'success' => false,
            'message' => $resultado['message']
        ]);
    }
    
} catch (Exception $e) {
    // Capturar cualquier excepción no controlada
    echo json_encode([
        'success' => false,
        'message' => 'Error inesperado: ' . $e->getMessage()
    ]);
    
    // Log del error
    error_log('ERROR AL ANULAR COMPROBANTE: ' . $e->getMessage());
}
?>
