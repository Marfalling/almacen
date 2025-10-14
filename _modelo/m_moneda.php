
<?php
//=======================================================================
// FUNCIONES PARA MONEDA
//=======================================================================

//-----------------------------------------------------------------------
function MostrarMoneda() {
    include("../_conexion/conexion.php");
    $sqlc = "SELECT * FROM moneda ORDER BY nom_moneda ASC";
    $resc = mysqli_query($con, $sqlc);
    $resultado = array();
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }
    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
function GrabarMoneda($nom, $est) {
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe
    $sqlv = "SELECT * FROM moneda WHERE nom_moneda = '$nom'";
    $resv = mysqli_query($con, $sqlv);
    
    if (mysqli_num_rows($resv) > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Insertar nuevo registro
    $sqli = "INSERT INTO moneda (nom_moneda, est_moneda) VALUES ('$nom', $est)";
    $resi = mysqli_query($con, $sqli);
    
    mysqli_close($con);
    
    if ($resi) {
        return "SI";
    } else {
        return "NO";
    }
}

// Función para obtener una moneda específica por ID
function ObtenerMoneda($id_moneda)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT * FROM moneda WHERE id_moneda = '$id_moneda'";
    $resultado = mysqli_query($con, $sql);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $moneda = mysqli_fetch_assoc($resultado);
        mysqli_close($con);
        return $moneda;
    } else {
        mysqli_close($con);
        return false;
    }
}

//-----------------------------------------------------------------------
// Función para editar una moneda
function EditarMoneda($id_moneda, $nom, $est) 
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe otra moneda con el mismo nombre (excluyendo la actual)
    $sql_verificar = "SELECT COUNT(*) as total FROM moneda WHERE nom_moneda = '$nom' AND id_moneda != '$id_moneda'";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe otra moneda con ese nombre
    }
    
    // Actualizar moneda
    $sql = "UPDATE moneda SET nom_moneda = '$nom', est_moneda = $est WHERE id_moneda = '$id_moneda'";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}