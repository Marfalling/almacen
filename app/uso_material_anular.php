<?php
require_once("_modelo/m_uso_material.php");
require_once("_modelo/m_usuario.php");

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
    $id_usuario = isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : 0;

    if ($id_uso_material <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID de uso de material inválido'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($id_usuario <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID de usuario inválido'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // VERIFICAR PERMISOS DEL USUARIO
    $permisos = obtenerPermisosUsuario($id_usuario);
    
    if (!isset($permisos['anular_uso_de_material']) || !$permisos['anular_uso_de_material']) {
        // AUDITORÍA: Intento sin permisos
        GrabarAuditoriaApp($id_usuario, 'Usuario ID: ' . $id_usuario, 'INTENTO ANULAR SIN PERMISOS', 'USO MATERIAL', 'APP MÓVIL - ID Uso: ' . $id_uso_material);
        
        echo json_encode([
            'status' => 'error',
            'message' => 'No tiene permisos para anular uso de material'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // VERIFICAR QUE EL USO DE MATERIAL EXISTE
    $uso_material = ConsultarUsoMaterial($id_uso_material);
    
    if (!$uso_material || empty($uso_material)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Uso de material no encontrado'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // VERIFICAR ESTADO (no anular si ya está anulado)
    if ($uso_material[0]['est_uso_material'] == 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Este uso de material ya está anulado'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // PROCEDER CON LA ANULACIÓN
    $resultado = AnularUsoMaterial($id_uso_material);
    
    if ($resultado === "SI") {
        // AUDITORÍA: Anulación exitosa
        $num_uso = isset($uso_material[0]['num_uso_material']) ? $uso_material[0]['num_uso_material'] : 'N/A';
        GrabarAuditoriaApp($id_usuario, 'Usuario ID: ' . $id_usuario, 'ANULAR USO MATERIAL', 'USO MATERIAL', 'APP MÓVIL - ID: ' . $id_uso_material . ' - Uso: ' . $num_uso);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Uso de material anulado correctamente'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        // AUDITORÍA: Error en anulación
        GrabarAuditoriaApp($id_usuario, 'Usuario ID: ' . $id_usuario, 'ERROR ANULAR USO MATERIAL', 'USO MATERIAL', 'APP MÓVIL - Error: ' . $resultado);
        
        echo json_encode([
            'status' => 'error',
            'message' => $resultado
        ], JSON_UNESCAPED_UNICODE);
    }

} catch (Exception $e) {
    error_log("❌ Error en uso_material_anular.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Error interno del servidor'
    ], JSON_UNESCAPED_UNICODE);
}
?>