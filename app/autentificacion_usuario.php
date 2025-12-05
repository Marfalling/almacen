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
    $rpta = -1;
}
else 
{
    $rpta = $usuario[0]['id_usuario'];
}
echo $rpta;
?>