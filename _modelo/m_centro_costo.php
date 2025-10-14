<?php
//=======================================================================
// MODELO: m_centro_costo.php
//=======================================================================

// Mostrar centros de costo activos
function MostrarCentrosCostoActivos()
{
    include("../_conexion/conexion.php");
    
    $sql = "SELECT id_area as id_centro_costo, 
                   nom_area as nom_centro_costo 
            FROM {$bd_complemento}.area 
            WHERE act_area = 1 
            ORDER BY nom_area ASC";
    
    $resultado = mysqli_query($con, $sql);
    if (!$resultado) {
        error_log("Error al obtener centros de costo: " . mysqli_error($con));
        mysqli_close($con);
        return array();
    }

    $centros = [];
    while ($row = mysqli_fetch_assoc($resultado)) {
        $centros[] = $row;
    }

    mysqli_close($con);
    return $centros;
}
//-------------------------------------------------------
// Mostrar todos los centros (activos e inactivos)
function ObtenerCentrosCosto()
{
    include("../_conexion/conexion.php");
    
    $sql = "SELECT id_area as id_centro_costo,
                   nom_area as nom_centro_costo,
                   act_area as est_centro_costo
            FROM {$bd_complemento}.area
            ORDER BY nom_area ASC";

    $res = mysqli_query($con, $sql);
    $data = [];

    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
    }

    mysqli_close($con);
    return $data;
}

// Obtener un centro específico
function ObtenerCentroCostoPorId($id)
{
    include("../_conexion/conexion.php");
    $id = intval($id);
    $sql = "SELECT * FROM {$bd_complemento}.area WHERE id_area = $id LIMIT 1";
    $res = mysqli_query($con, $sql);
    $row = ($res && mysqli_num_rows($res) > 0) ? mysqli_fetch_assoc($res) : null;
    mysqli_close($con);
    return $row;
}

// Registrar nuevo centro
function GrabarCentroCosto($nom)
{
    include("../_conexion/conexion.php");
    $nom = strtoupper(mysqli_real_escape_string($con, $nom));

    $sqlv = "SELECT * FROM {$bd_complemento}.area WHERE nom_area = '$nom'";
    $resv = mysqli_query($con, $sqlv);
    if (mysqli_num_rows($resv) > 0) {
        mysqli_close($con);
        return "NO";
    }

    $sql = "INSERT INTO {$bd_complemento}.area (nom_area, act_area) VALUES ('$nom', 1)";
    $res = mysqli_query($con, $sql);
    mysqli_close($con);
    return $res ? "SI" : "ERROR";
}

// Editar centro
function EditarCentroCosto($id, $nom)
{
    include("../_conexion/conexion.php");
    $id = intval($id);
    $nom = strtoupper(mysqli_real_escape_string($con, $nom));

    $sqlv = "SELECT COUNT(*) as total FROM {$bd_complemento}.area WHERE nom_area = '$nom' AND id_area != $id";
    $resv = mysqli_query($con, $sqlv);
    $fila = mysqli_fetch_assoc($resv);
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO";
    }

    $sql = "UPDATE {$bd_complemento}.area SET nom_area = '$nom' WHERE id_area = $id";
    $res = mysqli_query($con, $sql);
    mysqli_close($con);
    return $res ? "SI" : "ERROR";
}

// Cambiar estado
function CambiarEstadoCentroCosto($id, $estado)
{
    include("../_conexion/conexion.php");
    $id = intval($id);
    $estado = intval($estado);

    $sql = "UPDATE {$bd_complemento}.area SET act_area = $estado WHERE id_area = $id";
    $res = mysqli_query($con, $sql);
    mysqli_close($con);
    return $res ? "SI" : "ERROR";
}

// Obtener solo el nombre
function ObtenerNombreCentroCosto($id_centro_costo)
{
    include("../_conexion/conexion.php");
    $id_centro_costo = intval($id_centro_costo);

    $sql = "SELECT nom_area FROM {$bd_complemento}.area WHERE id_area = $id_centro_costo";
    $resultado = mysqli_query($con, $sql);
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $row = mysqli_fetch_assoc($resultado);
        mysqli_close($con);
        return $row['nom_area'];
    }

    mysqli_close($con);
    return 'N/A';
}