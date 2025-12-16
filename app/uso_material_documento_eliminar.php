<?php
require_once("_modelo/m_uso_material.php");

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Método no permitido'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $id_documento = isset($_POST['id_documento']) ? intval($_POST['id_documento']) : 0;

    if ($id_documento <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID de documento inválido'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resultado = EliminarDocumento($id_documento);
    echo json_encode($resultado, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    error_log("❌ Error en uso_material_documento_eliminar.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Error interno del servidor'
    ], JSON_UNESCAPED_UNICODE);
}
?>