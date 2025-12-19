<?php
require_once("_modelo/m_uso_material.php"); // 
require_once("_modelo/m_usuario.php");

header('Content-Type: application/json; charset=utf-8');


error_log("Recibiendo petición POST");
error_log("POST data: " . print_r($_POST, true));

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        error_log("❌ Método no permitido: " . $_SERVER['REQUEST_METHOD']);
        echo json_encode([
            'status' => 'error',
            'message' => 'Método no permitido'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $id_uso_material = isset($_POST['id_uso_material']) ? intval($_POST['id_uso_material']) : 0;
    $id_usuario = isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : 0;
    
    error_log("🔍 Consultando ID: " . $id_uso_material);

    if ($id_uso_material <= 0) {
        error_log("❌ ID inválido: " . $id_uso_material);
        echo json_encode([
            'status' => 'error',
            'message' => 'ID de uso de material inválido: ' . $id_uso_material
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($id_usuario > 0) {
        $permisos = obtenerPermisosUsuario($id_usuario);
        
        // VERIFICAR PERMISO DE EDITAR
        if (!isset($permisos['editar_uso_de_material']) || !$permisos['editar_uso_de_material']) {
            echo json_encode([
                'status' => 'error',
                'message' => 'No tienes permisos para consultar este registro'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    // Consultar datos completos
    error_log("Llamando a ConsultarUsoMaterialCompleto()");
    $datos = ConsultarUsoMaterialCompleto($id_uso_material);
    
    error_log("Datos obtenidos: " . print_r($datos, true));

    if (empty($datos)) {
        error_log("No se encontraron datos para ID: " . $id_uso_material);
        echo json_encode([
            'status' => 'error',
            'message' => 'No se encontraron datos para el ID: ' . $id_uso_material
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    error_log("Enviando respuesta exitosa");
    echo json_encode([
        'status' => 'success',
        'data' => $datos
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    error_log("❌ Error en uso_material_consultar.php: " . $e->getMessage());
    error_log("❌ Stack trace: " . $e->getTraceAsString());
    echo json_encode([
        'status' => 'error',
        'message' => 'Error interno del servidor: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>