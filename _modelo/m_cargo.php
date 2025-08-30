<?php

//-----------------------------------------------------------------------
function GrabarCargo($nom, $est) 
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe un cargo con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) as total FROM cargo WHERE nom_cargo = '$nom'";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Insertar nuevo cargo
    $sql = "INSERT INTO cargo (nom_cargo, est_cargo) VALUES ('$nom', $est)";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
function MostrarCargos()
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT * FROM cargo ORDER BY nom_cargo ASC";
    $resc = mysqli_query($con, $sqlc);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    
    return $resultado;
}

//-----------------------------------------------------------------------
function MostrarCargosActivos()
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT id_cargo, nom_cargo FROM cargo WHERE est_cargo = 1 ORDER BY nom_cargo ASC";
    $resc = mysqli_query($con, $sqlc);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    
    return $resultado;
}

//-----------------------------------------------------------------------
function EditarCargo($id, $nom, $est)
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe otro cargo con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) as total FROM cargo WHERE nom_cargo = '$nom' AND id_cargo != $id";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Actualizar cargo
    $sql = "UPDATE cargo SET nom_cargo = '$nom', est_cargo = $est WHERE id_cargo = $id";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
function ObtenerCargo($id)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT * FROM cargo WHERE id_cargo = $id";
    $result = mysqli_query($con, $sql);
    
    $cargo = mysqli_fetch_assoc($result);
    
    mysqli_close($con);
    
    return $cargo;
}

?>