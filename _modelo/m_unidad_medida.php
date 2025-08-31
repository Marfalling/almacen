<?php
//=======================================================================
// FUNCIONES PARA UNIDAD DE MEDIDA
//=======================================================================

//-----------------------------------------------------------------------
function MostrarUnidadMedida() {
    include("../_conexion/conexion.php");
    $sqlc = "SELECT * FROM unidad_medida ORDER BY nom_unidad_medida ASC";
    $resc = mysqli_query($con, $sqlc);
    $resultado = array();
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }
    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
function GrabarUnidadMedida($nom, $est) {
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe
    $sqlv = "SELECT * FROM unidad_medida WHERE nom_unidad_medida = '$nom'";
    $resv = mysqli_query($con, $sqlv);
    
    if (mysqli_num_rows($resv) > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Insertar nuevo registro
    $sqli = "INSERT INTO unidad_medida (nom_unidad_medida, est_unidad_medida) VALUES ('$nom', $est)";
    $resi = mysqli_query($con, $sqli);
    
    mysqli_close($con);
    
    if ($resi) {
        return "SI";
    } else {
        return "NO";
    }
}

function MostrarUnidadMedidaActiva() {
    include("../_conexion/conexion.php");
    $sqlc = "SELECT * FROM unidad_medida WHERE est_unidad_medida = 1 ORDER BY nom_unidad_medida ASC";
    $resc = mysqli_query($con, $sqlc);
    $resultado = array();
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }
    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
function ObtenerUnidadMedida($id)
{
    include("../_conexion/conexion.php");
    
    $sql = "SELECT * FROM unidad_medida WHERE id_unidad_medida = $id";
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

//-----------------------------------------------------------------------
function ActualizarUnidadMedida($id, $nom, $est)
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe otra unidad de medida con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) as total FROM unidad_medida WHERE nom_unidad_medida = '$nom' AND id_unidad_medida != $id";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe otra con el mismo nombre
    }
    
    // Actualizar unidad de medida
    $sql = "UPDATE unidad_medida SET 
            nom_unidad_medida = '$nom', 
            est_unidad_medida = $est 
            WHERE id_unidad_medida = $id";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}
?>