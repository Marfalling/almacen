<?php 
function MostrarVigilante()
{
	require_once("../_conexion/conexion.php");

	$sqlc = "SELECT vig.id_vigilante,vig.nom_vigilante
    FROM 
        vigilante vig
    WHERE 
        vig.est_vigilante = 1
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
function MostrarVigilante2($id_reporte)
{
    require_once("../_conexion/conexion.php");

    // Obtener vig_reporte del reporte específico
    $sql = "SELECT vig_reporte FROM reporte WHERE id_reporte = '$id_reporte'";
    $res = mysqli_query($con, $sql);
    $vig_reporte = NULL;
    
    if ($row = mysqli_fetch_assoc($res)) {
        $vig_reporte = $row['vig_reporte'];
    }

    // Obtener lista de vigilantes activos
    $sqlc = "SELECT vig.id_vigilante, vig.nom_vigilante FROM vigilante vig WHERE vig.est_vigilante = 1";
    $resc = mysqli_query($con, $sqlc);

    $resultado = array();

    if ($vig_reporte !== NULL) {
        // Agregar manualmente el elemento
        $resultado[] = array('id_vigilante' => "0", 'nom_vigilante' => $vig_reporte);
    }

    while ($rowc = mysqli_fetch_assoc($resc)) {
        $resultado[] = $rowc;
    }

 

    mysqli_close($con);

    return $resultado;
}
function MostrarVigilante3($id_pedido)
{
    require_once("../_conexion/conexion.php");

    // Obtener vig_pedido del pedido específico
    $sql = "SELECT vig_pedido FROM pedido WHERE id_pedido = '$id_pedido'";
    $res = mysqli_query($con, $sql);
    $vig_pedido = NULL;
    
    if ($row = mysqli_fetch_assoc($res)) {
        $vig_pedido = $row['vig_pedido'];
    }

    // Obtener lista de vigilantes activos
    $sqlc = "SELECT vig.id_vigilante, vig.nom_vigilante FROM vigilante vig WHERE vig.est_vigilante = 1";
    $resc = mysqli_query($con, $sqlc);

    $resultado = array();

    if ($vig_pedido !== NULL) {
        // Agregar manualmente el elemento
        $resultado[] = array('id_vigilante' => "0", 'nom_vigilante' => $vig_pedido);
    }

    while ($rowc = mysqli_fetch_assoc($resc)) {
        $resultado[] = $rowc;
    }

 

    mysqli_close($con);

    return $resultado;
}
?>