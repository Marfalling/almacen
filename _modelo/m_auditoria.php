<?php
//-----------------------------------------------------------------------
function MostrarAuditoria($fecha_inicio, $fecha_fin)
{
	// Función temporal vacía - siempre retorna un array vacío
    return array();
}

//-----------------------------------------------------------------------
function GrabarAuditoria($id_usuario, $nom_usuario, $accion, $modulo, $descripcion)
{
	// Función temporal vacía - siempre retorna "SI"
    return "SI";
}
/*
//-----------------------------------------------------------------------
function MostrarAuditoria($fecha_inicio, $fecha_fin)
{
	include("../_conexion/conexion.php");

	$sqla = "SELECT * FROM auditoria WHERE DATE(fecha) BETWEEN '$fecha_inicio' AND '$fecha_fin' ORDER BY fecha DESC";
	$resa = mysqli_query($con, $sqla);

	$resultado = array();

	while ($rowa = mysqli_fetch_array($resa, MYSQLI_ASSOC)) {
		$resultado[] = $rowa;
	}

	return $resultado;

	mysqli_close($con);
}

//-----------------------------------------------------------------------
function GrabarAuditoria($id_usuario, $nom_usuario, $accion, $modulo, $descripcion)
{
	include("../_conexion/conexion.php");

    date_default_timezone_set('America/Lima');
    $fecha_actual = date("Y-m-d H:i:s");

    $sql = "INSERT INTO auditoria VALUES (NULL, '$id_usuario', '$nom_usuario', '$accion', '$modulo', '$descripcion', '$fecha_actual')";
    if (mysqli_query($con, $sql)) {
        $rpta = "SI";
    } else {
        $rpta = "NO";
    }

	mysqli_close($con);
    
	return $rpta;
}
*/
?>