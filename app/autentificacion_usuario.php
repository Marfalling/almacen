<?php 
//recibir datos del app
$user = $_REQUEST['user'];
$pass = $_REQUEST['pass'];

//llamada a funciones
require_once("_modelo/m_usuario.php");
$usuario = AutentificarUsuario($user,$pass);

//respuesta al app
if($usuario==NULL)
{
    $rpta = [
        'status' => 'error',
        'message' => 'Credenciales incorrectas'
    ];
}
else 
{
    $rpta = [
        'status' => 'success',
        'id_usuario' => $usuario[0]['id_usuario'],
        'id_personal' => $usuario[0]['id_personal'],
        'nom_personal' => $usuario[0]['nom_personal'],
        'usuario' => $usuario[0]['usu_usuario']
    ];
}

// Devolver JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($rpta, JSON_UNESCAPED_UNICODE);
?>