<?php
//=======================================================================
// MODELO: m_centro_costo.php
//=======================================================================

// Mostrar centros de costo activos
function MostrarCentrosCostoActivos()
{
    include("../_conexion/conexion_complemento.php");
    
    $sql = "SELECT id_area as id_centro_costo, 
                   nom_area as nom_centro_costo 
            FROM area 
            WHERE act_area = 1 
            ORDER BY nom_area ASC";
    
    $resultado = mysqli_query($con_comp, $sql);
    if (!$resultado) {
        error_log("Error al obtener centros de costo: " . mysqli_error($con_comp));
        mysqli_close($con_comp);
        return array();
    }

    $centros = [];
    while ($row = mysqli_fetch_assoc($resultado)) {
        $centros[] = $row;
    }

    mysqli_close($con_comp);
    return $centros;
}

// Mostrar todos los centros (activos e inactivos)
function ObtenerCentrosCosto()
{
    include("../_conexion/conexion_complemento.php");
    
    $sql = "SELECT id_area as id_centro_costo,
                   nom_area as nom_centro_costo,
                   act_area as est_centro_costo
            FROM area
            ORDER BY nom_area ASC";

    $res = mysqli_query($con_comp, $sql);
    $data = [];

    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
    }

    mysqli_close($con_comp);
    return $data;
}

// Obtener un centro específico
function ObtenerCentroCostoPorId($id)
{
    include("../_conexion/conexion_complemento.php");
    $id = intval($id);
    $sql = "SELECT * FROM area WHERE id_area = $id LIMIT 1";
    $res = mysqli_query($con_comp, $sql);
    $row = ($res && mysqli_num_rows($res) > 0) ? mysqli_fetch_assoc($res) : null;
    mysqli_close($con_comp);
    return $row;
}

// Registrar nuevo centro
function GrabarCentroCosto($nom)
{
    include("../_conexion/conexion_complemento.php");
    $nom = strtoupper(mysqli_real_escape_string($con_comp, $nom));

    $sqlv = "SELECT * FROM area WHERE nom_area = '$nom'";
    $resv = mysqli_query($con_comp, $sqlv);
    if (mysqli_num_rows($resv) > 0) {
        mysqli_close($con_comp);
        return "NO";
    }

    $sql = "INSERT INTO area (nom_area, act_area) VALUES ('$nom', 1)";
    $res = mysqli_query($con_comp, $sql);
    mysqli_close($con_comp);
    return $res ? "SI" : "ERROR";
}

// Editar centro
function EditarCentroCosto($id, $nom)
{
    include("../_conexion/conexion_complemento.php");
    $id = intval($id);
    $nom = strtoupper(mysqli_real_escape_string($con_comp, $nom));

    $sqlv = "SELECT COUNT(*) as total FROM area WHERE nom_area = '$nom' AND id_area != $id";
    $resv = mysqli_query($con_comp, $sqlv);
    $fila = mysqli_fetch_assoc($resv);
    if ($fila['total'] > 0) {
        mysqli_close($con_comp);
        return "NO";
    }

    $sql = "UPDATE area SET nom_area = '$nom' WHERE id_area = $id";
    $res = mysqli_query($con_comp, $sql);
    mysqli_close($con_comp);
    return $res ? "SI" : "ERROR";
}

// Cambiar estado
function CambiarEstadoCentroCosto($id, $estado)
{
    include("../_conexion/conexion_complemento.php");
    $id = intval($id);
    $estado = intval($estado);

    $sql = "UPDATE area SET act_area = $estado WHERE id_area = $id";
    $res = mysqli_query($con_comp, $sql);
    mysqli_close($con_comp);
    return $res ? "SI" : "ERROR";
}

// Obtener solo el nombre
function ObtenerNombreCentroCosto($id_centro_costo)
{
    include("../_conexion/conexion_complemento.php");
    $id_centro_costo = intval($id_centro_costo);

    $sql = "SELECT nom_area FROM area WHERE id_area = $id_centro_costo";
    $resultado = mysqli_query($con_comp, $sql);
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $row = mysqli_fetch_assoc($resultado);
        mysqli_close($con_comp);
        return $row['nom_area'];
    }

    mysqli_close($con_comp);
    return 'N/A';
}
?>
