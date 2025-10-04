<?php
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
    
    $centros = array();
    while ($row = mysqli_fetch_assoc($resultado)) {
        $centros[] = $row;
    }
    
    mysqli_close($con_comp);
    return $centros;
}

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