<?php
//=======================================================================
// FUNCIONES PARA TIPO DE DOCUMENTO
//=======================================================================

//-----------------------------------------------------------------------
// Mostrar todos los tipos de documento
function MostrarTipoDocumento() {
    include("../_conexion/conexion.php");
    $sqlc = "SELECT * FROM tipo_documento ORDER BY nom_tipo_documento ASC";
    $resc = mysqli_query($con, $sqlc);
    $resultado = array();
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }
    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
// Registrar un nuevo tipo de documento
function GrabarTipoDocumento($nom, $est) {
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe
    $sqlv = "SELECT * FROM tipo_documento WHERE nom_tipo_documento = '$nom'";
    $resv = mysqli_query($con, $sqlv);
    
    if (mysqli_num_rows($resv) > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Insertar nuevo registro
    $sqli = "INSERT INTO tipo_documento (nom_tipo_documento, est_tipo_documento) VALUES ('$nom', $est)";
    $resi = mysqli_query($con, $sqli);
    
    mysqli_close($con);
    
    if ($resi) {
        return "SI";
    } else {
        return "NO";
    }
}
//-----------------------------------------------------------------------
// Obtener un tipo de documento específico por ID
function ObtenerTipoDocumento($id_tipo_documento)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT * FROM tipo_documento WHERE id_tipo_documento = '$id_tipo_documento'";
    $resultado = mysqli_query($con, $sql);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $tipo_documento = mysqli_fetch_assoc($resultado);
        mysqli_close($con);
        return $tipo_documento;
    } else {
        mysqli_close($con);
        return false;
    }
}

//-----------------------------------------------------------------------
// Editar un tipo de documento existente
function EditarTipoDocumento($id_tipo_documento, $nom, $est) 
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe otro con el mismo nombre (excluyendo el actual)
    $sql_verificar = "SELECT COUNT(*) as total FROM tipo_documento WHERE nom_tipo_documento = '$nom' AND id_tipo_documento != '$id_tipo_documento'";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe otro tipo con ese nombre
    }
    
    // Actualizar registro
    $sql = "UPDATE tipo_documento 
            SET nom_tipo_documento = '$nom', est_tipo_documento = $est 
            WHERE id_tipo_documento = '$id_tipo_documento'";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}
?>