<?php 
function AutentificarUsuario($user,$pass)
{
	require_once("../_conexion/conexion.php");

	/*$sqlc = "SELECT * 
    FROM 
        usuario u
    INNER JOIN 
        usuario_tipo ut on u.id_usuario_tipo = ut.id_usuario_tipo
    INNER JOIN 
        brigada bri on u.id_brigada = bri.id_brigada
    WHERE 
        BINARY u.user_usuario = '$user' AND
        BINARY u.pass_usuario = '$pass' AND
        u.est_usuario = 1
    ";
	$resc=mysqli_query($con,$sqlc);

	$resultado = array();
	
	while($rowc=mysqli_fetch_array($resc, MYSQLI_ASSOC))
	{
		$resultado[] = $rowc;		
	}
		
	mysqli_close($con);

	return $resultado;*/

    $sqlc = "SELECT * 
    FROM 
        usuario u
    WHERE 
        BINARY u.usu_usuario = '$user' AND
        BINARY u.con_usuario = '$pass' AND
        u.est_usuario = 1
    ";
	$resc=mysqli_query($con,$sqlc);

	$resultado = array();
	
	while($rowc=mysqli_fetch_array($resc, MYSQLI_ASSOC))
	{
		$resultado[] = $rowc;		
	}
		
	mysqli_close($con);

	return $resultado;
}
?>