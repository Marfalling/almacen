<?php

//-----------------------------------------------------------------------
function GrabarAlmacen($id_cliente, $id_obra, $nom, $est) 
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe un almacén con el mismo nombre para el mismo cliente y obra
    $sql_verificar = "SELECT COUNT(*) as total FROM almacen WHERE nom_almacen = '$nom' AND id_cliente = $id_cliente AND id_obra = $id_obra";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Insertar nuevo almacén
    $sql = "INSERT INTO almacen (id_cliente, id_obra, nom_almacen, est_almacen) 
            VALUES ($id_cliente, $id_obra, '$nom', $est)";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
function MostrarAlmacenes()
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT a.*, c.nom_cliente, o.nom_obra 
             FROM almacen a 
             INNER JOIN cliente c ON a.id_cliente = c.id_cliente 
             INNER JOIN obra o ON a.id_obra = o.id_obra 
             ORDER BY a.nom_almacen ASC";
    $resc = mysqli_query($con, $sqlc);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    
    return $resultado;
}

//-----------------------------------------------------------------------
function MostrarAlmacenesActivos()
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT a.id_almacen, a.nom_almacen, c.nom_cliente, o.nom_obra 
             FROM almacen a 
             INNER JOIN cliente c ON a.id_cliente = c.id_cliente 
             INNER JOIN obra o ON a.id_obra = o.id_obra 
             WHERE a.est_almacen = 1 
             ORDER BY a.nom_almacen ASC";
    $resc = mysqli_query($con, $sqlc);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    
    return $resultado;
}

?>