<?php 
function MostrarTurno()
{
	require_once("../_conexion/conexion.php");

	$sqlc = "SELECT * 
    FROM 
        turno tur
    WHERE 
        tur.est_turno = 1
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