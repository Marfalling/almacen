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

// Función para obtener un cliente específico por ID
function ObtenerCliente($id_cliente)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT * FROM cliente WHERE id_cliente = '$id_cliente'";
    $resultado = mysqli_query($con, $sql);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $cliente = mysqli_fetch_assoc($resultado);
        mysqli_close($con);
        return $cliente;
    } else {
        mysqli_close($con);
        return false;
    }
}

//-----------------------------------------------------------------------
// Función para editar un cliente
function EditarCliente($id_cliente, $nom, $est) 
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe otro cliente con el mismo nombre (excluyendo el actual)
    $sql_verificar = "SELECT COUNT(*) as total FROM cliente WHERE nom_cliente = '$nom' AND id_cliente != '$id_cliente'";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe otro cliente con ese nombre
    }
    
    // Actualizar cliente
    $sql = "UPDATE cliente SET nom_cliente = '$nom', est_cliente = $est WHERE id_cliente = '$id_cliente'";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}

?>