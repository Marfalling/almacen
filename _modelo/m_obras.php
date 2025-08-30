<?php
//-----------------------------------------------------------------------
function GrabarObra($nom, $est) 
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe una obra con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) as total FROM obra WHERE nom_obra = '$nom'";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Insertar nueva obra
    $sql = "INSERT INTO obra (nom_obra, est_obra) VALUES ('$nom', $est)";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
function MostrarObras()
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT * FROM obra ORDER BY nom_obra ASC";
    $resc = mysqli_query($con, $sqlc);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    
    return $resultado;
}

//-----------------------------------------------------------------------
function MostrarObrasActivas()
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT id_obra, nom_obra FROM obra WHERE est_obra = 1 ORDER BY nom_obra ASC";
    $resc = mysqli_query($con, $sqlc);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    
    return $resultado;
}

?>