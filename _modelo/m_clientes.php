<?php

//-----------------------------------------------------------------------
function GrabarClientes($nom, $est) 
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe un cliente con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) as total FROM cliente WHERE nom_cliente = '$nom'";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Insertar nuevo cliente
    $sql = "INSERT INTO cliente (nom_cliente, est_cliente) VALUES ('$nom', $est)";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
function MostrarClientes()
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT * FROM cliente ORDER BY nom_cliente ASC";
    $resc = mysqli_query($con, $sqlc);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    
    return $resultado;
}

//-----------------------------------------------------------------------
function MostrarClientesActivos()
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT id_cliente, nom_cliente FROM cliente WHERE est_cliente = 1 ORDER BY nom_cliente ASC";
    $resc = mysqli_query($con, $sqlc);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    
    return $resultado;
}

?>