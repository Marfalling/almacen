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

    $id_uso_material = isset($_POST['id_uso_material']) ? intval($_POST['id_uso_material']) : 0;

    if ($id_uso_material <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID de uso de material inválido'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resultado = ConsultarUsoMaterialCompleto($id_uso_material);
    
    if (empty($resultado)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Uso de material no encontrado'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'status' => 'success',
            'data' => $resultado
        ], JSON_UNESCAPED_UNICODE);
    }

} catch (Exception $e) {
    error_log("❌ Error en uso_material_consultar.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Error interno del servidor'
    ], JSON_UNESCAPED_UNICODE);
}
?>