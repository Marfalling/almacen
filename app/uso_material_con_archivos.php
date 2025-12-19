<?php
require_once("_modelo/m_uso_material.php");
require_once("_modelo/m_usuario.php");

header('Content-Type: application/json; charset=utf-8');

// AGREGAR LOGS
error_log("═══════════════════════════════════════════");
error_log("📥 uso_material_con_archivos.php - Nueva petición");
error_log("📦 POST data: " . print_r($_POST, true));
error_log("📎 FILES data: " . print_r($_FILES, true));

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Método no permitido'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $id_usuario = isset($_POST['id_personal']) ? intval($_POST['id_personal']) : 0;
    
    if ($id_usuario <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Usuario no válido'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Obtener permisos del usuario
    $permisos = obtenerPermisosUsuario($id_usuario);
    
    // Verificar permiso específico
    if (!isset($permisos['crear_uso_de_material']) || !$permisos['crear_uso_de_material']) {
        // Acceso denegado
        GrabarAuditoriaApp($id_usuario, '', 'ERROR DE ACCESO', 'USO_MATERIAL', 'CREAR - APP MÓVIL');
        
        echo json_encode([
            'status' => 'error',
            'message' => 'No tienes permisos para crear uso de material'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Obtener datos POST
    $id_almacen = isset($_POST['id_almacen']) ? intval($_POST['id_almacen']) : 0;
    $id_ubicacion = isset($_POST['id_ubicacion']) ? intval($_POST['id_ubicacion']) : 0;
    $id_solicitante = isset($_POST['id_solicitante']) ? intval($_POST['id_solicitante']) : 0;
    $id_personal = isset($_POST['id_personal']) ? intval($_POST['id_personal']) : 0;
    $materiales_json = isset($_POST['materiales']) ? $_POST['materiales'] : '';

    // Validaciones
    if ($id_almacen <= 0 || $id_ubicacion <= 0 || $id_solicitante <= 0 || $id_personal <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Parámetros inválidos'
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

    $materiales = json_decode($materiales_json, true);
    
    if (!$materiales || !is_array($materiales) || empty($materiales)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Formato de materiales inválido'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Procesar archivos subidos
    $archivos_por_material = array();
    
    foreach ($_FILES as $key => $file) {
        
        if (preg_match('/file\d+_material(\d+)/', $key, $matches)) {
            $material_index = intval($matches[1]);
            
            if (!isset($archivos_por_material[$material_index])) {
                $archivos_por_material[$material_index] = array();
            }
            
            $archivos_por_material[$material_index][] = $file;
            error_log("📎 Archivo detectado: $key para material $material_index");
        }
    }

    error_log("📊 Total archivos por material: " . print_r(array_map('count', $archivos_por_material), true));

    // Llamar función para grabar 
    $resultado = GrabarUsoMaterialConArchivos($id_almacen, $id_ubicacion, $id_solicitante, $id_personal, $materiales, $archivos_por_material);

    if (isset($resultado['status']) && $resultado['status'] === 'success') {
        // Registro exitoso
        $descripcion = "APP MÓVIL - Almacén ID: $id_almacen | Ubicación ID: $id_ubicacion | Materiales: " . count($materiales);
        GrabarAuditoriaApp($id_usuario, '', 'REGISTRAR', 'USO_MATERIAL', $descripcion);
    }
    
    echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    error_log("═══════════════════════════════════════════");

} catch (Exception $e) {
    error_log("Error en uso_material_con_archivos.php: " . $e->getMessage());
    error_log("═══════════════════════════════════════════");
    
    echo json_encode([
        'status' => 'error',
        'message' => 'Error interno del servidor'
    ], JSON_UNESCAPED_UNICODE);
}
?>