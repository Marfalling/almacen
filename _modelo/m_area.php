<?php
//=======================================================================
// MODELO: m_area.php
//=======================================================================
// REGISTRAR NUEVA ÁREA
function GrabarArea($nom, $est)
{
    include("../_conexion/conexion.php");

    // Verificar si ya existe un área con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) AS total FROM {$bd_complemento}.area WHERE nom_area = '$nom'";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);

    if ($fila['total'] > 0) {
        return "NO"; // Ya existe
    }

    // Insertar nueva área
    $sql = "INSERT INTO {$bd_complemento}.area (nom_area, act_area) VALUES ('$nom', $est)";

    if (mysqli_query($con, $sql)) {
        return "SI";
    } else {
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
// MOSTRAR TODAS LAS ÁREAS
function MostrarAreas()
{
    include("../_conexion/conexion.php");

    $sql = "SELECT id_area, nom_area, act_area FROM {$bd_complemento}.area ORDER BY nom_area ASC";
    $res = mysqli_query($con, $sql);

    $resultado = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $resultado[] = $row;
    }

    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
// MOSTRAR ÁREAS ACTIVAS
function MostrarAreasActivas() {

    include("../_conexion/conexion.php");

    $query = "SELECT id_area, nom_area 
              FROM {$bd_complemento}.area 
              WHERE act_area = 1 
              ORDER BY nom_area ASC";
    $result = mysqli_query($con, $query);

    $areas = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $areas[] = $row;
        }
    }

    mysqli_close($con);
    return $areas;
}

//-----------------------------------------------------------------------
// EDITAR ÁREA
function EditarArea($id, $nom, $est)
{
    include("../_conexion/conexion.php");

    // Verificar si ya existe otra con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) AS total FROM {$bd_complemento}.area WHERE nom_area = '$nom' AND id_area != $id";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);

    if ($fila['total'] > 0) {
        return "NO";
    }

    // Actualizar
    $sql = "UPDATE {$bd_complemento}.area 
            SET nom_area = '$nom', act_area = $est 
            WHERE id_area = $id";

    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
// OBTENER UNA ÁREA POR ID
function ObtenerArea($id)
{
    include("../_conexion/conexion.php");
    
    $sql = "SELECT * FROM {$bd_complemento}.area WHERE id_area = $id";
    $result = mysqli_query($con, $sql);

    $areas = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $areas[] = $row;
        }
    }
    mysqli_close($con);
    return $areas;
}
?>
