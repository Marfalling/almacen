<?php
require_once("_modelo/m_uso_material.php");
require_once("_modelo/m_usuario.php");

header('Content-Type: application/json; charset=utf-8');

error_log("═══════════════════════════════════════════");
error_log("📝 uso_material_editar.php - Nueva petición");
error_log("📦 POST data: " . print_r($_POST, true));

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Método no permitido'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Obtener datos POST
    $id_uso_material = isset($_POST['id_uso_material']) ? intval($_POST['id_uso_material']) : 0;
    $id_almacen = isset($_POST['id_almacen']) ? intval($_POST['id_almacen']) : 0;
    $id_ubicacion = isset($_POST['id_ubicacion']) ? intval($_POST['id_ubicacion']) : 0;
    $id_solicitante = isset($_POST['id_solicitante']) ? intval($_POST['id_solicitante']) : 0;
    $id_personal = isset($_POST['id_personal']) ? intval($_POST['id_personal']) : 0;
    $materiales_json = isset($_POST['materiales']) ? $_POST['materiales'] : '';

    if ($id_personal <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Usuario no válido'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Obtener permisos del usuario
    $permisos = obtenerPermisosUsuario($id_personal);
    
    // SOLO VERIFICAR PERMISO DE EDITAR
    if (!isset($permisos['editar_uso_de_material']) || !$permisos['editar_uso_de_material']) {
        // Acceso denegado
        GrabarAuditoriaApp($id_personal, '', 'ERROR DE ACCESO', 'USO_MATERIAL', 'EDITAR - APP MÓVIL');
        
        echo json_encode([
            'status' => 'error',
            'message' => 'No tienes permisos para editar uso de material'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Validar parámetros obligatorios
    if ($id_uso_material <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID de uso de material inválido'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($id_almacen <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID de almacén inválido'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($id_ubicacion <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID de ubicación inválido'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($id_solicitante <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID de solicitante inválido'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($id_personal <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID de personal inválido'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (empty($materiales_json)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No se enviaron materiales'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Decodificar JSON de materiales
    $materiales = json_decode($materiales_json, true);
    
    if (!$materiales || !is_array($materiales) || empty($materiales)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Formato de materiales inválido'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Validar estructura de materiales
    foreach ($materiales as $material) {
        if (!isset($material['id_producto']) || !isset($material['cantidad'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Estructura de material inválida'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    // Llamar función para editar
    $resultado = ActualizarUsoMaterial($id_uso_material, $id_almacen, $id_ubicacion, $id_solicitante, $id_personal, $materiales);

    if (isset($resultado['status']) && $resultado['status'] === 'success') {
        $descripcion = "APP MÓVIL - ID: $id_uso_material | Almacén: $id_almacen | Ubicación: $id_ubicacion | Materiales: " . count($materiales);
        GrabarAuditoriaApp($id_personal, '', 'EDITAR', 'USO_MATERIAL', $descripcion);
    }
    
    // Devolver resultado
    echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    error_log("═══════════════════════════════════════════");

} catch (Exception $e) {
    error_log("❌ Error en uso_material_editar.php: " + $e->getMessage());
    error_log("═══════════════════════════════════════════");
    
    echo json_encode([
        'status' => 'error',
        'message' => 'Error interno del servidor'
    ], JSON_UNESCAPED_UNICODE);
}
?>