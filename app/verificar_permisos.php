<?php
/**
 * Archivo para verificar permisos específicos desde la app
 */
require_once("_modelo/m_usuario.php");

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Método no permitido'
        ]);
        exit;
    }

    $id_usuario = isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : 0;
    $permiso_solicitado = isset($_POST['permiso']) ? $_POST['permiso'] : '';
    $modulo = isset($_POST['modulo']) ? $_POST['modulo'] : 'SISTEMA';
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    if ($id_usuario <= 0 || empty($permiso_solicitado)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Parámetros inválidos'
        ]);
        exit;
    }

    // ✅ Obtener permisos del usuario
    $permisos = obtenerPermisosUsuario($id_usuario);
    
    // ✅ Verificar si tiene el permiso
    $tiene_permiso = isset($permisos[$permiso_solicitado]) && $permisos[$permiso_solicitado] === true;

    if (!$tiene_permiso) {
        // ✅ AUDITORÍA: Acceso denegado
        GrabarAuditoriaApp($id_usuario, '', 'ERROR DE ACCESO', $modulo, $accion . ' - APP MÓVIL');
    }

    echo json_encode([
        'status' => 'success',
        'tiene_permiso' => $tiene_permiso,
        'permiso_solicitado' => $permiso_solicitado
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error interno del servidor'
    ]);
}
?>