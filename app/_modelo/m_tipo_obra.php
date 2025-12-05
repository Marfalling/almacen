<?php 
function MostrarTipoObra()
{
	require_once("../_conexion/conexion.php");

	$sqlc = "SELECT * 
    FROM 
        tipo_obra tob
    WHERE 
        tob.est_tipo_obra = 1
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