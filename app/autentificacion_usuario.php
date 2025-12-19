<?php 
//recibir datos del app
$user = $_REQUEST['user'];
$pass = $_REQUEST['pass'];

//llamada a funciones
require_once("_modelo/m_usuario.php");
$usuario = AutentificarUsuario($user, $pass);

//respuesta al app
if($usuario == NULL || empty($usuario)) {
    // ✅ AUDITORÍA: Intento fallido
    GrabarAuditoriaApp(null, $user, 'INTENTO DE ACCESO FALLIDO', 'SESIÓN APP', 'LOGIN MÓVIL');
    
    $rpta = [
        'status' => 'error',
        'message' => 'Credenciales incorrectas'
    ];
} else {
    $id_usuario = $usuario[0]['id_usuario'];
    $nom_usuario = $usuario[0]['nom_personal'];
    
    // ✅ OBTENER PERMISOS COMPLETOS
    $permisos = obtenerPermisosUsuario($id_usuario);
    
    // ✅ AUDITORÍA: Login exitoso
    GrabarAuditoriaApp($id_usuario, $nom_usuario, 'INICIO DE SESIÓN', 'SESIÓN APP', 'LOGIN MÓVIL');
    
    $rpta = [
        'status' => 'success',
        'id_usuario' => $usuario[0]['id_usuario'],
        'id_personal' => $usuario[0]['id_personal'],
        'nom_personal' => $usuario[0]['nom_personal'],
        'usuario' => $usuario[0]['usu_usuario'],
        'cargo' => $usuario[0]['nom_cargo'],
        'area' => $usuario[0]['nom_area'],
        'permisos' => $permisos  // ✅ INCLUIR PERMISOS
    ];
}

// Devolver JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($rpta, JSON_UNESCAPED_UNICODE);
?>