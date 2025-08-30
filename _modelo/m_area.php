<?php

//-----------------------------------------------------------------------
function GrabarArea($nom, $est) 
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe un área con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) as total FROM area WHERE nom_area = '$nom'";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Insertar nueva área
    $sql = "INSERT INTO area (nom_area, est_area) VALUES ('$nom', $est)";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
function MostrarAreas()
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT * FROM area ORDER BY nom_area ASC";
    $resc = mysqli_query($con, $sqlc);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    
    return $resultado;
}

//-----------------------------------------------------------------------
function MostrarAreasActivas()
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT id_area, nom_area FROM area WHERE est_area = 1 ORDER BY nom_area ASC";
    $resc = mysqli_query($con, $sqlc);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    
    return $resultado;
}

//-----------------------------------------------------------------------
function EditarArea($id, $nom, $est)
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe otra área con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) as total FROM area WHERE nom_area = '$nom' AND id_area != $id";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Actualizar área
    $sql = "UPDATE area SET nom_area = '$nom', est_area = $est WHERE id_area = $id";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
function ObtenerArea($id)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT * FROM area WHERE id_area = $id";
    $result = mysqli_query($con, $sql);
    
    $area = mysqli_fetch_assoc($result);
    
    mysqli_close($con);
    
    return $area;
}

?>