<?php 
function GrabarPedido($id_usuario, $id_vigilante, $num, $ot, $ubi, $fec, $det, $nota, $act)
{
	include("../_conexion/conexion.php");

	//Datos de la brigada del usuario en ese momento
	$usuario = ConsultarReporteUsuario($id_usuario);
	$id_brigada = $usuario[0]['id_brigada'];
	$nbri_usuario = $usuario[0]['nbri_usuario'];

	if (!empty($num) && !empty($ot) && !empty($ubi) && !empty($fec) && !empty($nota)) 
	{
		$sql = "INSERT INTO pedido() VALUES 
		(
		NULL,
		'$id_usuario',
		NULL, /*id_reporte*/
		1, /*id_pedido_tipo*/
		'$ot',
		'$id_usuario',
		NULL, /*vig_pedido*/
		'$id_vigilante',
		'$ubi',
		'$det',
		'$id_brigada',
		'$nbri_usuario',
		NULL, /*obs_pedido*/
		'$nota',
		'$fec',
		NOW(), /*freg_pedido*/
		'$act',
		'$num'
		)";
		$resc = mysqli_query($con, $sql);
	
		if ($resc === TRUE) {
			$resultado = mysqli_insert_id($con);
		} else {
			$resultado = -1;
		}
	} 
	else 
	{
		$resultado = 0;
	}
	
	mysqli_close($con);

	return $resultado;
}
//-----------------------------------------------------------------------
function ConsultarReporteUsuario($id_usuario)
{
	include("../_conexion/conexion.php");

	$sqlc = "SELECT * FROM usuario u WHERE u.id_usuario='$id_usuario'";
	$resc = mysqli_query($con, $sqlc);

	$resultado = array();

	while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
		$resultado[] = $rowc;
	}


	mysqli_close($con);

	return $resultado;
}
//-----------------------------------------------------------------------
function MostrarPedidoID($id_pedido)
{
	include("../_conexion/conexion.php");

	mysqli_query($con, "SET lc_time_names = 'es_ES';");
	
	$sqlc = "SELECT *,
	COALESCE(p.id_vigilante, 0) AS id_vigilante_, -- Si es NULL, devuelve 0
    DATE_FORMAT(p.fecha_pedido, '%d/%m/%Y') AS fecha_pedido -- Fecha en formato dd/mm/yyyy
	FROM
		pedido p
	WHERE 
		p.id_pedido = '$id_pedido'";

	$resc = mysqli_query($con, $sqlc);

	$resultado = array();

	while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
		$resultado[] = $rowc;
	}

	mysqli_close($con);

	return $resultado;
}
//-----------------------------------------------------------------------
function MostrarPedidoUsuario($id_usuario)
{
	include("../_conexion/conexion.php");

	mysqli_query($con, "SET lc_time_names = 'es_ES';");
	
	$sqlc = "SELECT 
			p.id_pedido as id_pedido,
			p.num_reporte_pedido as num_reporte_pedido,
			p.ot_pedido as ot_pedido,
			p.ubi_pedido as ubi_pedido,
			COALESCE(vig.nom_vigilante, p.vig_pedido) AS nom_vigilante,
            DATE_FORMAT(p.fecha_pedido, '%a %d %b %Y') AS fecha_pedido,
			p.est_pedido as est_pedido
         FROM 
            pedido p
		LEFT JOIN
			vigilante vig  ON p.id_vigilante = vig.id_vigilante
         WHERE 
            p.id_usuario_pedido='$id_usuario'
			AND p.est_pedido IN (1,2)
            AND DATE(p.freg_pedido) >= DATE(DATE_SUB(NOW(), INTERVAL 15 DAY)) -- Ignorar la hora
         ORDER BY 
            p.id_pedido DESC";

	$resc = mysqli_query($con, $sqlc);

	$resultado = array();

	while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
		$resultado[] = $rowc;
	}

	mysqli_close($con);

	return $resultado;
}
//-----------------------------------------------------------------------
function ActualizarPedido($id_pedido,$id_usuario, $id_vigilante, $num, $ot, $ubi, $fec, $det, $nota, $act)
{
	include("../_conexion/conexion.php");

	//Datos de la brigada del usuario en ese momento
	$usuario = ConsultarReporteUsuario($id_usuario);
	$id_brigada = $usuario[0]['id_brigada'];
	$nbri_usuario = $usuario[0]['nbri_usuario'];

	if (!empty($num) && !empty($ot) && !empty($ubi) && !empty($fec) && !empty($nota)) 
	{
		$sql = "UPDATE pedido 
		SET 
            ot_pedido = '$ot',"
            . ($id_vigilante != 0 ? " vig_pedido = NULL," : "") . // Solo actualiza con NULL si $id_vigilante ≠ 0
            ($id_vigilante != 0 ? " id_vigilante = '$id_vigilante'," : "") . // Solo actualiza si es diferente de 0
            "num_reporte_pedido = '$num',
            ubi_pedido = '$ubi',
			detalle_pedido = '$det',
			id_brigada = '$id_brigada',
            nbri_pedido = '$nbri_usuario',
            nota_pedido = '$nota',
			fecha_pedido = '$fec'
		WHERE 
            id_pedido = '$id_pedido'";

		$resc = mysqli_query($con, $sql);
	
		if ($resc === TRUE) {
			$resultado = $id_pedido;
		} else {
			$resultado = -1;
		}
	} 
	else 
	{
		$resultado = 0;
	}
	
	mysqli_close($con);

	return $resultado;
}
//-----------------------------------------------------------------------
function AnularPedido($id_pedido)
{
	include("../_conexion/conexion.php");

	$sql = "UPDATE pedido SET est_pedido= 0 WHERE id_pedido = '$id_pedido'";
	$resc = mysqli_query($con, $sql);

	if ($resc === TRUE) {
		$resultado = 1;
	} else {
		$resultado = -1;
	}

	mysqli_close($con);

	return $resultado;
}
?>