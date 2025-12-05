<?php 
function GrabarReporte($id_usuario, $id_reporte_tipo, $id_turno, $num, $ot, $ubit, $vig, $fec, $hi, $hf, $det, $hext, $lat, $lon, $act)
{
	include("../_conexion/conexion.php");

	//Datos de la brigada del usuario en ese momento
	$usuario = ConsultarReporteUsuario($id_usuario);
	$id_brigada = $usuario[0]['id_brigada'];
	$nbri_usuario = $usuario[0]['nbri_usuario'];

	if (!empty($num) && !empty($ot) && !empty($ot) && !empty($ubit) && !empty($vig) && !empty($fec) && !empty($hi) && !empty($det) && $hext >= 0) 
	{
		//if($hf==""){$hf=NULL;}

		$sql = "INSERT INTO reporte VALUES (NULL,'$id_usuario','$id_reporte_tipo','$id_turno',2,'$num','$ot','$ubit','$vig',NULL,'$fec','$hi',". ($hf === "" ? "NULL" : "'$hf'") . ",'$det','$hext',NOW(),'$id_brigada','$nbri_usuario','$lat','$lon','$act')";
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
function GrabarReporte1($id_usuario, $id_reporte_tipo, $id_tipo_obra, $id_vigilante, $id_turno, $num, $ot, $ubit, $fec, $hi, $hf, $det, $hext, $lat, $lon, $act)
{
	include("../_conexion/conexion.php");

	//Datos de la brigada del usuario en ese momento
	$usuario = ConsultarReporteUsuario($id_usuario);
	$id_brigada = $usuario[0]['id_brigada'];
	$nbri_usuario = $usuario[0]['nbri_usuario'];

	if (!empty($num) && !empty($ot) && !empty($ubit) && !empty($fec) && !empty($hi) && !empty($det) && $hext >= 0) 
	{
		//if($hf==""){$hf=NULL;}

		$sql = "INSERT INTO reporte VALUES (NULL,'$id_usuario','$id_reporte_tipo','$id_turno','$id_tipo_obra','$num','$ot','$ubit',NULL,'$id_vigilante','$fec','$hi',". ($hf === "" ? "NULL" : "'$hf'") . ",'$det','$hext',NOW(),'$id_brigada','$nbri_usuario','$lat','$lon','$act')";
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
//con validación de que no se repita uno igual en 15 segundos
function GrabarReporte_($id_usuario, $id_reporte_tipo, $id_turno, $num, $ot, $ubit, $vig, $fec, $hi, $hf, $det, $hext, $lat, $lon, $act)
{
    include("../_conexion/conexion.php");

    // Datos de la brigada del usuario en ese momento
    $usuario = ConsultarReporteUsuario($id_usuario);
    $id_brigada = $usuario[0]['id_brigada'];
    $nbri_usuario = $usuario[0]['nbri_usuario'];

    if (!empty($num) && !empty($ot) && !empty($ubit) && !empty($vig) && !empty($fec) && !empty($hi) && !empty($det) && $hext >= 0) 
    {
        // Comprobar si ya existe un registro similar en los últimos 20 segundos
        $checkQuery = "SELECT COUNT(*) as count FROM reporte 
                       WHERE id_usuario = '$id_usuario'
                       AND id_reporte_tipo = '$id_reporte_tipo'
                       AND id_turno = '$id_turno'
                       AND num_reporte = '$num'
                       AND ot_reporte = '$ot'
                       AND ubit_reporte = '$ubit'
					   AND vig_reporte = '$vig'
					   AND hi_reporte = '$hi'
					   AND hf_reporte = '$hf'
					   AND det_reporte = '$det'
					   AND hext_reporte = '$hext'
					   AND id_brigada = '$id_brigada'
					   AND nbri_reporte = '$nbri_usuario'";
					   //AND ABS(TIMESTAMPDIFF(SECOND, NOW(), freg_reporte)) <= 20"

        $checkResult = mysqli_query($con, $checkQuery);
        $checkRow = mysqli_fetch_assoc($checkResult);

        if ($checkRow['count'] > 0) {
            // Si existe un registro idéntico reciente
            $resultado = -2; // Código para "registro duplicado detectado"
        } else {
            // Realizar la inserción
            $sql = "INSERT INTO reporte VALUES 
                    (NULL, '$id_usuario', '$id_reporte_tipo', '$id_turno',2, '$num', '$ot', '$ubit', '$vig',NULL, '$fec', '$hi', " . 
                    ($hf === "" ? "NULL" : "'$hf'") . ", '$det', '$hext', NOW(), '$id_brigada', '$nbri_usuario', '$lat', '$lon', '$act')";
            $resc = mysqli_query($con, $sql);

            if ($resc === TRUE) {
                $resultado = mysqli_insert_id($con);
            } else {
                $resultado = -1; // Código para "error al insertar"
            }
        }
    } 
    else 
    {
        $resultado = 0; // Código para "datos incompletos o no válidos"
    }

    mysqli_close($con);

    return $resultado;
}
//-----------------------------------------------------------------------
function GrabarReporteEvidencia($id_reporte, $doc)
{
	include("../_conexion/conexion.php");

	$sql = "INSERT INTO reporte_evidencia VALUES (NULL,'$id_reporte','$doc',1)";
	mysqli_query($con, $sql);

	$rpta = "SI";

	mysqli_close($con);

	return $rpta;
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
function CantidadReporteUsuario($id_usuario)
{
	include("../_conexion/conexion.php");

	$sqlc = "SELECT IFNULL(COUNT(*),0) cantidad 
	FROM 
		reporte rep 
	WHERE 
		rep.id_usuario='$id_usuario' AND
		DATE(rep.freg_reporte) >= DATE(DATE_SUB(NOW(), INTERVAL 15 DAY)) AND
		rep.est_reporte =  1";
	$resc = mysqli_query($con, $sqlc);
	$rowc = mysqli_fetch_array($resc);
	$cantidad = $rowc['cantidad'];

	mysqli_close($con);

	return $cantidad;
}
//-----------------------------------------------------------------------
function MostrarReporteUsuario($id_usuario)
{
	include("../_conexion/conexion.php");

	mysqli_query($con, "SET lc_time_names = 'es_ES';");
	
	$sqlc = "SELECT 
            r.*, 
            u.*, 
            rt.*, 
            t.*, 
            DATE_FORMAT(r.fec_reporte, '%a %d %b %Y') AS fec_reporte,
            DATE_FORMAT(r.hi_reporte, '%h:%i %p') AS hi_reporte,
            COALESCE(
                DATE_FORMAT(r.hf_reporte, '%h:%i %p'), 
                '-'
            ) AS hf_reporte
         FROM 
            reporte r
         INNER JOIN 
            usuario u ON r.id_usuario = u.id_usuario
         INNER JOIN 
            reporte_tipo rt ON r.id_reporte_tipo = rt.id_reporte_tipo
         INNER JOIN 
            turno t ON r.id_turno = t.id_turno
         WHERE 
            r.id_usuario='$id_usuario'
			AND r.est_reporte = 1
            AND DATE(r.freg_reporte) >= DATE(DATE_SUB(NOW(), INTERVAL 15 DAY)) -- Ignorar la hora
         ORDER BY 
            r.id_reporte DESC";

	$resc = mysqli_query($con, $sqlc);

	$resultado = array();

	while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
		$resultado[] = $rowc;
	}

	mysqli_close($con);

	return $resultado;
}
//-----------------------------------------------------------------------
function FiltrarReporteUsuario($id_usuario, $filtro)
{
	include("../_conexion/conexion.php");

	mysqli_query($con, "SET lc_time_names = 'es_ES';");
	
	$sqlc = "SELECT 
            r.*, 
            u.*, 
            rt.*, 
            t.*, 
            DATE_FORMAT(r.fec_reporte, '%a %d %b %Y') AS fec_reporte,
            DATE_FORMAT(r.hi_reporte, '%h:%i %p') AS hi_reporte,
            COALESCE(
                DATE_FORMAT(r.hf_reporte, '%h:%i %p'), 
                '-'
            ) AS hf_reporte
         FROM 
            reporte r
         INNER JOIN 
            usuario u ON r.id_usuario = u.id_usuario
         INNER JOIN 
            reporte_tipo rt ON r.id_reporte_tipo = rt.id_reporte_tipo
         INNER JOIN 
            turno t ON r.id_turno = t.id_turno
         WHERE 
            r.id_usuario='$id_usuario'
			AND r.est_reporte = 1
			AND (
				r.num_reporte LIKE '$filtro%'
			)
            AND DATE(r.freg_reporte) >= DATE(DATE_SUB(NOW(), INTERVAL 15 DAY)) -- Ignorar la hora
			
         ORDER BY 
            r.id_reporte DESC";

	$resc = mysqli_query($con, $sqlc);

	$resultado = array();

	while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
		$resultado[] = $rowc;
	}

	mysqli_close($con);

	return $resultado;
}
//-----------------------------------------------------------------------
function MostrarReporteMapa($id_usuario)
{
	include("../_conexion/conexion.php");

	mysqli_query($con, "SET lc_time_names = 'es_ES';");
	
	$sqlc = "SELECT *,
		 CONCAT(b.nom_brigada,r.nbri_reporte) as brig
         FROM 
            reporte r
		INNER JOIN brigada b ON r.id_brigada = b.id_brigada
        WHERE 
            r.id_usuario = '$id_usuario'
            AND TRIM(r.lat_reporte) != '' 
            AND TRIM(r.lon_reporte) != ''
			AND r.est_reporte =  1
			AND DATE(r.freg_reporte) >= DATE(DATE_SUB(NOW(), INTERVAL 15 DAY))
        ORDER BY 
            r.id_reporte DESC";

	$resc = mysqli_query($con, $sqlc);

	$resultado = array();

	while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
		$resultado[] = $rowc;
	}

	mysqli_close($con);

	return $resultado;
}
//-----------------------------------------------------------------------
function MostrarReporteID($id_reporte)
{
	include("../_conexion/conexion.php");
	require_once("m_retiro.php");

	mysqli_query($con, "SET lc_time_names = 'es_ES';");
	
	$sqlc = "SELECT 
    r.*, 
    u.*, 
    rt.*, 
    t.*, 
    vig.*, 
    tob.*,
    COALESCE(r.id_vigilante, 0) AS id_vigilante_, -- Si es NULL, devuelve 0
    DATE_FORMAT(r.fec_reporte, '%d/%m/%Y') AS fec_reporte, -- Fecha en formato dd/mm/yyyy
    DATE_FORMAT(r.hi_reporte, '%h:%i %p') AS hi_reporte,  -- Hora inicio en formato hh:mm AM/PM
    COALESCE(DATE_FORMAT(r.hf_reporte, '%h:%i %p'), '') AS hf_reporte, -- Si es NULL, devuelve ''
    (SELECT COUNT(*) FROM reporte_evidencia re WHERE re.id_reporte = '$id_reporte') AS cantidad
	FROM 
		reporte r
	INNER JOIN 
		usuario u ON r.id_usuario = u.id_usuario
	INNER JOIN 
		reporte_tipo rt ON r.id_reporte_tipo = rt.id_reporte_tipo
	INNER JOIN 
		turno t ON r.id_turno = t.id_turno
	INNER JOIN 
		tipo_obra tob ON r.id_tipo_obra = tob.id_tipo_obra
	LEFT JOIN 
		vigilante vig ON r.id_vigilante = vig.id_vigilante
	WHERE 
		r.id_reporte = '$id_reporte'
	ORDER BY 
		r.id_reporte DESC;
	";

	$materiales = ConsultarRetiroMaterial($id_reporte);

	$resc = mysqli_query($con, $sqlc);

	$resultado = array();

	while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
		$rowc['materiales'] = $materiales; // ✅ Agregas los materiales como un nuevo campo
		$resultado[] = $rowc;
	}

	mysqli_close($con);

	return $resultado;
}
//-----------------------------------------------------------------------
function MostrarReporteEvidenciaID($id_reporte)
{
	include("../_conexion/conexion.php");

	$sqlc = "SELECT 
    CONCAT('https://arcereportes.com/_controlador/', re.doc_reporte_evidencia) AS ruta_archivo,
    -- Extraer el nombre del archivo después del primer guion
    SUBSTRING_INDEX(
        SUBSTRING_INDEX(re.doc_reporte_evidencia, '/', -1), 
        '-', -1
    ) AS nombre_archivo,
    -- Extraer la extensión del archivo
    SUBSTRING_INDEX(SUBSTRING_INDEX(re.doc_reporte_evidencia, '/', -1), '.', -1) AS tipo_archivo
	FROM 
		reporte_evidencia re
	WHERE 
		re.id_reporte = '$id_reporte'
	";

	$resc = mysqli_query($con, $sqlc);

	$resultado = array();

	while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
		$resultado[] = $rowc;
	}

	mysqli_close($con);

	return $resultado;
}
//-----------------------------------------------------------------------
function ActualizarReporte($id_reporte, $id_usuario, $id_reporte_tipo, $id_turno, $num, $ot, $ubit, $vig, $fec, $hi, $hf, $det, $hext, $lat, $lon, $act)
{
	include("../_conexion/conexion.php");

	//Datos de la brigada del usuario en ese momento
	$usuario = ConsultarReporteUsuario($id_usuario);
	$id_brigada = $usuario[0]['id_brigada'];
	$nbri_usuario = $usuario[0]['nbri_usuario'];

	if (!empty($num) && !empty($ot) && !empty($ot) && !empty($ubit) && !empty($vig) && !empty($fec) && !empty($hi) && !empty($det) && $hext >= 0) 
	{

		$sql = "UPDATE reporte 
        SET 
            id_usuario = '$id_usuario',
            id_reporte_tipo = '$id_reporte_tipo',
            id_turno = '$id_turno',
            num_reporte = '$num',
            ot_reporte = '$ot',
            ubit_reporte = '$ubit',
            vig_reporte = '$vig',
            fec_reporte = '$fec',
            hi_reporte = '$hi',
            hf_reporte = ".($hf === "" ? "NULL" : "'$hf'").",
            det_reporte = '$det',
            hext_reporte = '$hext',
            id_brigada = '$id_brigada',
            nbri_reporte = '$nbri_usuario',
            est_reporte = '$act'
        WHERE 
            id_reporte = '$id_reporte'";  // Condición para identificar el registro a actualizar
		$resc = mysqli_query($con, $sql);
	
		if ($resc === TRUE) {
			$resultado = $id_reporte;
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
function ActualizarReporte1($id_reporte,$id_usuario, $id_reporte_tipo, $id_tipo_obra, $id_vigilante, $id_turno, $num, $ot, $ubit, $fec, $hi, $hf, $det, $hext, $lat, $lon, $act)
{
	include("../_conexion/conexion.php");

	//Datos de la brigada del usuario en ese momento
	$usuario = ConsultarReporteUsuario($id_usuario);
	$id_brigada = $usuario[0]['id_brigada'];
	$nbri_usuario = $usuario[0]['nbri_usuario'];

	if (!empty($num) && !empty($ot) && !empty($ot) && !empty($ubit) && !empty($fec) && !empty($hi) && !empty($det) && $hext >= 0) 
	{

		$sql = "UPDATE reporte SET 
            id_usuario = '$id_usuario',
            id_reporte_tipo = '$id_reporte_tipo',
            id_tipo_obra = '$id_tipo_obra',"
            . ($id_vigilante != 0 ? " vig_reporte = NULL," : "") . // Solo actualiza con NULL si $id_vigilante ≠ 0
            ($id_vigilante != 0 ? " id_vigilante = '$id_vigilante'," : "") . // Solo actualiza si es diferente de 0
            "id_turno = '$id_turno',
            num_reporte = '$num',
            ot_reporte = '$ot',
            ubit_reporte = '$ubit',
            fec_reporte = '$fec',
            hi_reporte = '$hi',
            hf_reporte = " . ($hf === "" ? "NULL" : "'$hf'") . ",
            det_reporte = '$det',
            hext_reporte = '$hext',
            id_brigada = '$id_brigada',
            nbri_reporte = '$nbri_usuario',
            est_reporte = '$act'
        WHERE 
            id_reporte = '$id_reporte'";

		$resc = mysqli_query($con, $sql);
	
		if ($resc === TRUE) {
			$resultado = $id_reporte;
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
function AnularReporte($id_reporte)
{
	include("../_conexion/conexion.php");

	// Verificar si tiene asociaciones
	if (tieneAsociaciones($id_reporte)) 
	{
		$resultado = 0;
	}
	else
	{
		$sql = "UPDATE reporte SET est_reporte = 0 WHERE id_reporte = '$id_reporte'";
		$resc = mysqli_query($con, $sql);

		if ($resc === TRUE) {
			$resultado = 1;
		} else {
			$resultado = -1;
		}
	}

	mysqli_close($con);

	return $resultado;
}
//-----------------------------------------------------------------------
// Función para actualizar el estado de un reporte
function tieneAsociaciones($id_reporte)
{
	include("../_conexion/conexion.php");

	// Verificar pedidos asociados
	$sql_pedidos = "SELECT COUNT(*) as total FROM pedido WHERE id_reporte = '$id_reporte'";
	$result_pedidos = mysqli_query($con, $sql_pedidos);
	$row_pedidos = mysqli_fetch_assoc($result_pedidos);

	// Verificar valoraciones asociadas
	$sql_valoraciones = "SELECT COUNT(*) as total FROM valoracion WHERE id_reporte = '$id_reporte'";
	$result_valoraciones = mysqli_query($con, $sql_valoraciones);
	$row_valoraciones = mysqli_fetch_assoc($result_valoraciones);

	mysqli_close($con);

	// Retorna true si hay asociaciones, false si no hay
	return ($row_pedidos['total'] > 0 || $row_valoraciones['total'] > 0);
}

function GenerarCodigo($id_tipo_obra)
{
    include("../_conexion/conexion.php");

	//Obtener la letra
	$sql="SELECT SUBSTRING(nom_tipo_obra, 1, 1) AS letra,
	gen_tipo_obra
	FROM tipo_obra
	WHERE id_tipo_obra = '$id_tipo_obra'";
	$res = mysqli_query($con, $sql);
	$row =  mysqli_fetch_assoc($res);
	$gen_tipo_obra =  $row['gen_tipo_obra'];
	$letra =  $row['letra'];
	
	if($gen_tipo_obra==1)
	{
		//Obtner número de reporte
		$query = "SELECT num_reporte
		FROM reporte
		WHERE num_reporte LIKE '{$letra}%'
		ORDER BY CAST(SUBSTRING(num_reporte, 2) AS UNSIGNED) DESC
		LIMIT 1";
		$result = mysqli_query($con, $query);

		if ($row = mysqli_fetch_assoc($result)) {
			// Extraer el número después de la letra y sumarle 1
			$ultimoNumero = intval(substr($row['num_reporte'], 1));
			return $letra . ($ultimoNumero + 1);
		} else {
			// Si no hay códigos previos, comenzar desde 1
			return $letra . "1";
		}
	}
	else
	{
		return "";
	}	
}


function VerificarArchivado($num,$id_brigada)
{
	include("../_conexion/conexion.php");

	$in_clause = ($id_brigada == 3) ? "3,4" : $id_brigada;

	$sqlc = "SELECT COUNT(*) as cantidad
	FROM 
		valoracion v
	INNER JOIN 
		reporte r ON v.id_reporte = r.id_reporte
	WHERE 
		r.id_brigada IN ($in_clause) AND 
		r.num_reporte = '$num' AND 
		v.est_valoracion = 3";
	$resc = mysqli_query($con, $sqlc);

	$resultado = array();

	while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
		$resultado[] = $rowc;
	}


	mysqli_close($con);

	return $resultado;
}
?>