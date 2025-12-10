<?php 
function GrabarUbicacion($id_usuario, $lat, $lng)
{
    require_once("../_conexion/conexion.php");

    if (!empty($id_usuario) && !empty($lat) && !empty($lng)) 
    {
        // Verificar si ya existe una ubicación registrada
        $checkSql = "SELECT id_ubicacion FROM ubicacion WHERE id_usuario = '$id_usuario' LIMIT 1";
        $checkResult = mysqli_query($con, $checkSql);

        if (mysqli_num_rows($checkResult) > 0) {
            // Ya existe → ACTUALIZAR
            $sql = "UPDATE ubicacion 
                    SET lat_ubicacion = '$lat', lng_ubicacion = '$lng', fec_ubicacion = NOW() 
                    WHERE id_usuario = '$id_usuario'";
        } else {
            // No existe → INSERTAR
            $sql = "INSERT INTO ubicacion (id_usuario, lat_ubicacion, lng_ubicacion, fec_ubicacion)
                    VALUES ('$id_usuario', '$lat', '$lng', NOW())";
        }

        $resc = mysqli_query($con, $sql);

        if ($resc === TRUE) {
            $resultado = 1;
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
function ConsultarUbicacion()
{
    require_once("../_conexion/conexion.php");

    $resultado = array();

    // La lógica compara si la ubicación fue registrada en los últimos 10 segundos
    $sql = "SELECT 
                ubi.lat_ubicacion AS lat,
                ubi.lng_ubicacion AS lng,  
                ubi.id_usuario AS id_usuario,
                usu.nom_usuario AS nombre_usuario,
                ubi.fec_ubicacion AS fecha,
                CASE 
                    WHEN TIMESTAMPDIFF(SECOND, ubi.fec_ubicacion, NOW()) <= 20 THEN 1
                    ELSE 0
                END AS ubicacion_actual
            FROM ubicacion ubi
            INNER JOIN usuario usu ON ubi.id_usuario = usu.id_usuario";

    $stmt = $con->prepare($sql);

    if ($stmt->execute()) {
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $resultado[] = $row;
        }
    }

    $stmt->close();
    mysqli_close($con);

    return $resultado;
}

//-----------------------------------------------------------------------
function ConsultarUbicacionUsuario($id_usuario)
{
    require_once("../_conexion/conexion.php");

    $resultado = array();

    if (!empty($id_usuario)) {
        $sql = "SELECT * FROM ubicacion WHERE id_usuario = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $id_usuario);

        if ($stmt->execute()) {
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $resultado[] = $row;
            }
        }

        $stmt->close();
    }

    mysqli_close($con);
    return $resultado;
}

function MostrarUbicacionesActivas()
{
    include("../_conexion/conexion.php");

    $sql = "
        SELECT 
            id_ubicacion,
            nom_ubicacion,
            est_ubicacion
        FROM ubicacion
        WHERE est_ubicacion = 1
        ORDER BY nom_ubicacion ASC;
    ";

    $res = mysqli_query($con, $sql);
    $resultado = mysqli_fetch_all($res, MYSQLI_ASSOC);

    mysqli_close($con);
    return $resultado;
}


?>