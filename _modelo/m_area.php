<?php
//=======================================================================
// MODELO: m_area.php
//=======================================================================
require_once("../_conexion/conexion_complemento.php"); // $con_comp global

//-----------------------------------------------------------------------
// REGISTRAR NUEVA ÁREA
function GrabarArea($nom, $est)
{
    global $con_comp;

    // Verificar si ya existe un área con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) AS total FROM area WHERE nom_area = '$nom'";
    $resultado_verificar = mysqli_query($con_comp, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);

    if ($fila['total'] > 0) {
        return "NO"; // Ya existe
    }

    // Insertar nueva área
    $sql = "INSERT INTO area (nom_area, act_area) VALUES ('$nom', $est)";

    if (mysqli_query($con_comp, $sql)) {
        return "SI";
    } else {
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
// MOSTRAR TODAS LAS ÁREAS
function MostrarAreas()
{
    global $con_comp;

    $sql = "SELECT id_area, nom_area, act_area FROM area ORDER BY nom_area ASC";
    $res = mysqli_query($con_comp, $sql);

    $resultado = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $resultado[] = $row;
    }

    return $resultado;
}

//-----------------------------------------------------------------------
// MOSTRAR ÁREAS ACTIVAS
function MostrarAreasActivas() {
    global $con_comp;

    $query = "SELECT id_area, nom_area 
              FROM area 
              WHERE act_area = 1 
              ORDER BY nom_area ASC";
    $result = mysqli_query($con_comp, $query);

    $areas = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $areas[] = $row;
        }
    }
    return $areas;
}

//-----------------------------------------------------------------------
// EDITAR ÁREA
function EditarArea($id, $nom, $est)
{
    global $con_comp;

    // Verificar si ya existe otra con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) AS total FROM area WHERE nom_area = '$nom' AND id_area != $id";
    $resultado_verificar = mysqli_query($con_comp, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);

    if ($fila['total'] > 0) {
        return "NO";
    }

    // Actualizar
    $sql = "UPDATE area 
            SET nom_area = '$nom', act_area = $est 
            WHERE id_area = $id";

    if (mysqli_query($con_comp, $sql)) {
        return "SI";
    } else {
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
// OBTENER UNA ÁREA POR ID
function ObtenerArea($id)
{
    global $con_comp;

    $sql = "SELECT * FROM area WHERE id_area = $id";
    $res = mysqli_query($con_comp, $sql);
    $area = mysqli_fetch_assoc($res);

    return $area;
}
?>
