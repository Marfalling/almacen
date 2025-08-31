<?php

//-----------------------------------------------------------------------
function GrabarMaterialTipo($nom, $est) 
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe un tipo de material con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) as total FROM material_tipo WHERE nom_material_tipo = '$nom'";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Insertar nuevo tipo de material
    $sql = "INSERT INTO material_tipo (nom_material_tipo, est_material_tipo) 
            VALUES ('$nom', $est)";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
function MostrarMaterialTipo()
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT * FROM material_tipo ORDER BY nom_material_tipo ASC";
    $resc = mysqli_query($con, $sqlc);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    
    return $resultado;
}

//-----------------------------------------------------------------------
function MostrarMaterialTipoActivos()
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT id_material_tipo, nom_material_tipo 
             FROM material_tipo 
             WHERE est_material_tipo = 1 
             ORDER BY nom_material_tipo ASC";
    $resc = mysqli_query($con, $sqlc);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    
    return $resultado;
}

//-----------------------------------------------------------------------
function ActualizarMaterialTipo($id, $nom, $est)
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe otro tipo de material con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) as total FROM material_tipo WHERE nom_material_tipo = '$nom' AND id_material_tipo != $id";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe otro con el mismo nombre
    }
    
    // Actualizar tipo de material
    $sql = "UPDATE material_tipo SET 
            nom_material_tipo = '$nom', 
            est_material_tipo = $est 
            WHERE id_material_tipo = $id";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
function ObtenerMaterialTipo($id)
{
    include("../_conexion/conexion.php");
    
    $sql = "SELECT * FROM material_tipo WHERE id_material_tipo = $id";
    $resultado = mysqli_query($con, $sql);
    
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $fila = mysqli_fetch_assoc($resultado);
        mysqli_close($con);
        return $fila;
    } else {
        mysqli_close($con);
        return false;
    }
}
?>