<?php

//-----------------------------------------------------------------------
function GrabarProveedor($nom, $ruc, $dir, $tel, $cont, $est) 
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe un proveedor con el mismo nombre o RUC
    $sql_verificar = "SELECT COUNT(*) as total FROM proveedor WHERE nom_proveedor = '$nom' OR ruc_proveedor = '$ruc'";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Insertar nuevo proveedor
    $sql = "INSERT INTO proveedor (nom_proveedor, ruc_proveedor, dir_proveedor, tel_proveedor, cont_proveedor, est_proveedor) 
            VALUES ('$nom', '$ruc', '$dir', '$tel', '$cont', $est)";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
function MostrarProveedores()
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT * FROM proveedor ORDER BY nom_proveedor ASC";
    $resc = mysqli_query($con, $sqlc);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    
    return $resultado;
}

//-----------------------------------------------------------------------
function MostrarProveedoresActivos()
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT id_proveedor, nom_proveedor, ruc_proveedor FROM proveedor WHERE est_proveedor = 1 ORDER BY nom_proveedor ASC";
    $resc = mysqli_query($con, $sqlc);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    
    return $resultado;
}

?>