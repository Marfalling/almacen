
<?php
//=======================================================================
// FUNCIONES PARA MONEDA
//=======================================================================

//-----------------------------------------------------------------------
function MostrarMoneda() {
    include("../_conexion/conexion.php");
    $sqlc = "SELECT * FROM moneda ORDER BY nom_moneda ASC";
    $resc = mysqli_query($con, $sqlc);
    $resultado = array();
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }
    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
function GrabarMoneda($nom, $est) {
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe
    $sqlv = "SELECT * FROM moneda WHERE nom_moneda = '$nom'";
    $resv = mysqli_query($con, $sqlv);
    
    if (mysqli_num_rows($resv) > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Insertar nuevo registro
    $sqli = "INSERT INTO moneda (nom_moneda, est_moneda) VALUES ('$nom', $est)";
    $resi = mysqli_query($con, $sqli);
    
    mysqli_close($con);
    
    if ($resi) {
        return "SI";
    } else {
        return "NO";
    }
}
?>