<?php
//=======================================================================
// FUNCIONES PARA PRODUCTO TIPO
//=======================================================================

//-----------------------------------------------------------------------
function MostrarProductoTipo() {
    include("../_conexion/conexion.php");
    $sqlc = "SELECT * FROM producto_tipo ORDER BY nom_producto_tipo ASC";
    $resc = mysqli_query($con, $sqlc);
    $resultado = array();
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }
    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
function MostrarProductoTipoActivos() {
    include("../_conexion/conexion.php");
    $sqlc = "SELECT * FROM producto_tipo WHERE est_producto_tipo = 1 ORDER BY nom_producto_tipo ASC";
    $resc = mysqli_query($con, $sqlc);
    $resultado = array();
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }
    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
function GrabarProductoTipo($nom, $est) {
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe
    $sqlv = "SELECT * FROM producto_tipo WHERE nom_producto_tipo = '$nom'";
    $resv = mysqli_query($con, $sqlv);
    
    if (mysqli_num_rows($resv) > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Insertar nuevo registro
    $sqli = "INSERT INTO producto_tipo (nom_producto_tipo, est_producto_tipo) VALUES ('$nom', $est)";
    $resi = mysqli_query($con, $sqli);
    
    mysqli_close($con);
    
    if ($resi) {
        return "SI";
    } else {
        return "ERROR";
    }
}
?>
