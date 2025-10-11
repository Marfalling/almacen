<?php

//-----------------------------------------------------------------------
function GrabarClientes($nom, $est) 
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe un cliente con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) as total FROM cliente WHERE nom_cliente = ?";
    $stmt = mysqli_prepare($con, $sql_verificar);
    mysqli_stmt_bind_param($stmt, "s", $nom);
    mysqli_stmt_execute($stmt);
    $resultado_verificar = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO";
    }
    
    // Insertar nuevo cliente
    $sql = "INSERT INTO cliente (nom_cliente, est_cliente) VALUES (?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $nom, $est);
    
    if (mysqli_stmt_execute($stmt)) {
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
    include("../_conexion/conexion_complemento.php");

    $resultado = array();

    // Obtener clientes de la base complementaria (Inspecciones)
    $sql_comp = "SELECT id_cliente, nom_cliente, act_cliente as est_cliente, 'Inspecciones' as origen 
                 FROM cliente 
                 ORDER BY nom_cliente ASC";
    $res_comp = mysqli_query($con_comp, $sql_comp);
    
    if (!$res_comp) {
        error_log("Error en MostrarClientes() - Base Inspecciones: " . mysqli_error($con_comp));
    } else {
        while ($rowc = mysqli_fetch_array($res_comp, MYSQLI_ASSOC)) {
            $resultado[] = $rowc;
        }
    }

    mysqli_close($con_comp);
    
    return $resultado;
}

//-----------------------------------------------------------------------
function MostrarClientesActivos()
{
    include("../_conexion/conexion_complemento.php");

    $resultado = array();

    // Clientes activos de la base complementaria
    $sql_comp = "SELECT id_cliente, nom_cliente, 'Inspecciones' as origen 
                 FROM cliente 
                 WHERE act_cliente = 1 
                 ORDER BY nom_cliente ASC";
    $res_comp = mysqli_query($con_comp, $sql_comp);
    
    if (!$res_comp) {
        error_log("Error en MostrarClientesActivos() - Base Inspecciones: " . mysqli_error($con_comp));
    } else {
        while ($rowc = mysqli_fetch_array($res_comp, MYSQLI_ASSOC)) {
            $resultado[] = $rowc;
        }
    }

    mysqli_close($con_comp);
    
    return $resultado;
}

//-----------------------------------------------------------------------
function ObtenerCliente($id_cliente)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT * FROM cliente WHERE id_cliente = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_cliente);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

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
function EditarCliente($id_cliente, $nom, $est) 
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe otro cliente con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) as total FROM cliente WHERE nom_cliente = ? AND id_cliente != ?";
    $stmt = mysqli_prepare($con, $sql_verificar);
    mysqli_stmt_bind_param($stmt, "si", $nom, $id_cliente);
    mysqli_stmt_execute($stmt);
    $resultado_verificar = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO";
    }
    
    // Actualizar cliente
    $sql = "UPDATE cliente SET nom_cliente = ?, est_cliente = ? WHERE id_cliente = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sii", $nom, $est, $id_cliente);
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}

?>