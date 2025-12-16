<?php
/**
 * Autenticación de usuario para App Android
 * Versión mejorada con seguridad y auditoría
 */

header('Content-Type: application/json; charset=utf-8');

// Validar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Método no permitido'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Incluir archivos necesarios
require_once("../_conexion/conexion.php");
require_once("_modelo/m_usuario.php");
require_once("_modelo/m_auditoria.php");

try {
    // ✅ RECIBIR Y SANITIZAR DATOS
    $user = isset($_POST['user']) ? mysqli_real_escape_string($con, trim($_POST['user'])) : '';
    $pass = isset($_POST['pass']) ? mysqli_real_escape_string($con, trim($_POST['pass'])) : '';

    // Validar datos vacíos
    if (empty($user) || empty($pass)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Usuario y contraseña son requeridos'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ✅ AUTENTICAR USUARIO
    $usuario = AutentificarUsuario($user, $pass);

    if ($usuario == NULL || empty($usuario)) {
        // ❌ CREDENCIALES INCORRECTAS
        GrabarAuditoria(null, $user, 'INTENTO DE ACCESO FALLIDO - APP ANDROID', 'SESIÓN', 'LOGIN APP');
        
        echo json_encode([
            'status' => 'error',
            'message' => 'Credenciales incorrectas'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        // ✅ AUTENTICACIÓN EXITOSA
        $id_usuario = $usuario[0]['id_usuario'];
        $id_personal = $usuario[0]['id_personal'];
        $nom_personal = $usuario[0]['nom_personal'];
        $usu_usuario = $usuario[0]['usu_usuario'];
        $nom_area = $usuario[0]['nom_area'];

        // ✅ ACTUALIZAR ÚLTIMO ACCESO
        date_default_timezone_set('America/Lima');
        $fecha_actual = date('Y-m-d H:i:s');
        
        $sql_update = "UPDATE usuario SET fec_ultimo_acceso = ? WHERE id_usuario = ?";
        $stmt_update = mysqli_prepare($con, $sql_update);
        mysqli_stmt_bind_param($stmt_update, "si", $fecha_actual, $id_usuario);
        mysqli_stmt_execute($stmt_update);
        mysqli_stmt_close($stmt_update);

        // ✅ GRABAR AUDITORÍA
        GrabarAuditoria($id_usuario, $nom_personal, 'INICIO DE SESIÓN - APP ANDROID', 'SESIÓN', $nom_personal);

        // ✅ OBTENER PERMISOS
        $permisos = obtenerPermisosUsuario($id_usuario);

        // ✅ RESPUESTA EXITOSA
        echo json_encode([
            'status' => 'success',
            'id_usuario' => $id_usuario,
            'id_personal' => $id_personal,
            'nom_personal' => $nom_personal,
            'usuario' => $usu_usuario,
            'nom_area' => $nom_area,
            'permisos' => $permisos[0] ?? []
        ], JSON_UNESCAPED_UNICODE);
    }

    mysqli_close($con);

} catch (Exception $e) {
    error_log("❌ Error en autentificacion_usuario.php: " . $e->getMessage());
    
    echo json_encode([
        'status' => 'error',
        'message' => 'Error interno del servidor'
    ], JSON_UNESCAPED_UNICODE);
}
?>