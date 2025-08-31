<?php
//=======================================================================
// FUNCIONES PARA UBICACION
//=======================================================================

//-----------------------------------------------------------------------
function MostrarUbicacion() {
    include("../_conexion/conexion.php");
    $sqlc = "SELECT * FROM ubicacion ORDER BY nom_ubicacion ASC";
    $resc = mysqli_query($con, $sqlc);
    $resultado = array();
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }
    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
function GrabarUbicacion($nom, $est) {
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe
    $sqlv = "SELECT * FROM ubicacion WHERE nom_ubicacion = '$nom'";
    $resv = mysqli_query($con, $sqlv);
    
    if (mysqli_num_rows($resv) > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Insertar nuevo registro
    $sqli = "INSERT INTO ubicacion (nom_ubicacion, est_ubicacion) VALUES ('$nom', $est)";
    $resi = mysqli_query($con, $sqli);
    
    mysqli_close($con);
    
    if ($resi) {
        return "SI";
    } else {
        return "NO";
    }
}

//-----------------------------------------------------------------------
function ObtenerUbicacion($id)
{
    include("../_conexion/conexion.php");
    
    $sql = "SELECT * FROM ubicacion WHERE id_ubicacion = $id";
    $resultado = mysqli_query($con, $sql);
    
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $fila = mysqli_fetch_assoc($resultado);
        mysqli_close($con);
        return $fila;
    } else {
        mysqli_close($con);
        return false;
    }
}

//-----------------------------------------------------------------------
function ActualizarUbicacion($id, $nom, $est)
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe otra ubicación con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) as total FROM ubicacion WHERE nom_ubicacion = '$nom' AND id_ubicacion != $id";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe otra con el mismo nombre
    }
    
    // Actualizar ubicación
    $sql = "UPDATE ubicacion SET 
            nom_ubicacion = '$nom', 
            est_ubicacion = $est 
            WHERE id_ubicacion = $id";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}
?>