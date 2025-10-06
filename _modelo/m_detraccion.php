<?php
//=======================================================================
// MODELO: m_detraccion.php 
//=======================================================================

function ObtenerDetracciones() {
    include("../_conexion/conexion.php");
    $sql = "SELECT * FROM detraccion ORDER BY nombre_detraccion ASC";
    $res = mysqli_query($con, $sql);
    $data = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            if (!isset($row['est_detraccion'])) {
                $row['est_detraccion'] = 1;
            }
            $data[] = $row;
        }
    }
    mysqli_close($con);
    return $data;
}

function ObtenerDetraccion($id) {
    include("../_conexion/conexion.php");
    $id = intval($id);
    $sql = "SELECT * FROM detraccion WHERE id_detraccion = '$id' LIMIT 1";
    $res = mysqli_query($con, $sql);
    $row = ($res && mysqli_num_rows($res) > 0) ? mysqli_fetch_assoc($res) : null;
    if ($row && !isset($row['est_detraccion'])) {
        $row['est_detraccion'] = 1;
    }
    mysqli_close($con);
    return $row;
}

function ObtenerDetraccionPorId($id) {
    return ObtenerDetraccion($id);
}

function GrabarDetraccion($nom, $porcentaje) {
    include("../_conexion/conexion.php");
    $nom = mysqli_real_escape_string($con, $nom);
    $porcentaje = floatval($porcentaje);
    $estado = 1;

    $sqlv = "SELECT * FROM detraccion WHERE nombre_detraccion = '$nom'";
    $resv = mysqli_query($con, $sqlv);
    if (mysqli_num_rows($resv) > 0) {
        mysqli_close($con);
        return "NO";
    }

    $sqli = "INSERT INTO detraccion (nombre_detraccion, porcentaje, est_detraccion) 
             VALUES ('$nom', $porcentaje, $estado)";
    $resi = mysqli_query($con, $sqli);
    mysqli_close($con);
    return $resi ? "SI" : "ERROR";
}

// Editar detracción existente (nombre, porcentaje y estado)
function EditarDetraccion($id, $nom, $porcentaje, $estado) {
    include("../_conexion/conexion.php");
    $id = intval($id);
    $nom = mysqli_real_escape_string($con, $nom);
    $porcentaje = floatval($porcentaje);
    $estado = intval($estado);

    $sqlv = "SELECT COUNT(*) as total FROM detraccion WHERE nombre_detraccion = '$nom' AND id_detraccion != '$id'";
    $resv = mysqli_query($con, $sqlv);
    $fila = mysqli_fetch_assoc($resv);
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO";
    }

    $sql = "UPDATE detraccion 
            SET nombre_detraccion = '$nom', porcentaje = $porcentaje, est_detraccion = $estado
            WHERE id_detraccion = $id";
    $res = mysqli_query($con, $sql);
    mysqli_close($con);
    return $res ? "SI" : "ERROR";
}

function CambiarEstadoDetraccion($id, $nuevo_estado) {
    include("../_conexion/conexion.php");
    $id = intval($id);
    $nuevo_estado = intval($nuevo_estado);
    $sql = "UPDATE detraccion SET est_detraccion = $nuevo_estado WHERE id_detraccion = $id";
    $res = mysqli_query($con, $sql);
    mysqli_close($con);
    return $res ? "SI" : "ERROR";
}

function EliminarDetraccion($id) {
    include("../_conexion/conexion.php");
    $id = intval($id);
    $sql = "DELETE FROM detraccion WHERE id_detraccion = $id";
    $res = mysqli_query($con, $sql);
    mysqli_close($con);
    return $res ? "SI" : "ERROR";
}
?>

