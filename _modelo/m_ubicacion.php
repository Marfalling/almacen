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

?>