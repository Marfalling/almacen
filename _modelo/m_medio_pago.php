<?php
//=======================================================================
// FUNCIONES PARA MEDIO DE PAGO
//=======================================================================

//-----------------------------------------------------------------------
// Mostrar todos los medios de pago
function MostrarMedioPago() {
    include("../_conexion/conexion.php");
    $sqlc = "SELECT * FROM medio_pago ORDER BY nom_medio_pago ASC";
    $resc = mysqli_query($con, $sqlc);
    $resultado = array();
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }
    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
// Registrar un nuevo medio de pago
function GrabarMedioPago($nom, $est) {
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe
    $sqlv = "SELECT * FROM medio_pago WHERE nom_medio_pago = '$nom'";
    $resv = mysqli_query($con, $sqlv);
    
    if (mysqli_num_rows($resv) > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Insertar nuevo registro
    $sqli = "INSERT INTO medio_pago (nom_medio_pago, est_medio_pago) VALUES ('$nom', $est)";
    $resi = mysqli_query($con, $sqli);
    
    mysqli_close($con);
    
    if ($resi) {
        return "SI";
    } else {
        return "NO";
    }
}
//-----------------------------------------------------------------------
// Obtener un medio de pago específico por ID
function ObtenerMedioPago($id_medio_pago)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT * FROM medio_pago WHERE id_medio_pago = '$id_medio_pago'";
    $resultado = mysqli_query($con, $sql);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $medio_pago = mysqli_fetch_assoc($resultado);
        mysqli_close($con);
        return $medio_pago;
    } else {
        mysqli_close($con);
        return false;
    }
}

//-----------------------------------------------------------------------
// Editar un medio de pago existente
function EditarMedioPago($id_medio_pago, $nom, $est) 
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe otro con el mismo nombre (excluyendo el actual)
    $sql_verificar = "SELECT COUNT(*) as total FROM medio_pago WHERE nom_medio_pago = '$nom' AND id_medio_pago != '$id_medio_pago'";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe otro medio con ese nombre
    }
    
    // Actualizar registro
    $sql = "UPDATE medio_pago 
            SET nom_medio_pago = '$nom', est_medio_pago = $est 
            WHERE id_medio_pago = '$id_medio_pago'";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}
?>