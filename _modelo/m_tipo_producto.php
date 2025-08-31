<?php
//=======================================================================
// FUNCIONES PARA PRODUCTO TIPO
//=======================================================================

//-----------------------------------------------------------------------
function MostrarProductoTipo() {
    include("../_conexion/conexion.php");
    $sqlc = "SELECT * FROM producto_tipo ORDER BY nom_producto_tipo ASC";
    $resc = mysqli_query($con, $sqlc);
    $resultado = array();
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }
    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
function MostrarProductoTipoActivos() {
    include("../_conexion/conexion.php");
    $sqlc = "SELECT * FROM producto_tipo WHERE est_producto_tipo = 1 ORDER BY nom_producto_tipo ASC";
    $resc = mysqli_query($con, $sqlc);
    $resultado = array();
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }
    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
function GrabarProductoTipo($nom, $est) {
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe
    $sqlv = "SELECT * FROM producto_tipo WHERE nom_producto_tipo = '$nom'";
    $resv = mysqli_query($con, $sqlv);
    
    if (mysqli_num_rows($resv) > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Insertar nuevo registro
    $sqli = "INSERT INTO producto_tipo (nom_producto_tipo, est_producto_tipo) VALUES ('$nom', $est)";
    $resi = mysqli_query($con, $sqli);
    
    mysqli_close($con);
    
    if ($resi) {
        return "SI";
    } else {
        return "ERROR";
    }
}

// Función para obtener un tipo de producto específico por ID
function ObtenerProductoTipo($id_producto_tipo)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT * FROM producto_tipo WHERE id_producto_tipo = '$id_producto_tipo'";
    $resultado = mysqli_query($con, $sql);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $tipo_producto = mysqli_fetch_assoc($resultado);
        mysqli_close($con);
        return $tipo_producto;
    } else {
        mysqli_close($con);
        return false;
    }
}

//-----------------------------------------------------------------------
// Función para editar un tipo de producto
function EditarProductoTipo($id_producto_tipo, $nom, $est) 
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe otro tipo de producto con el mismo nombre (excluyendo el actual)
    $sql_verificar = "SELECT COUNT(*) as total FROM producto_tipo WHERE nom_producto_tipo = '$nom' AND id_producto_tipo != '$id_producto_tipo'";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe otro tipo de producto con ese nombre
    }
    
    // Actualizar tipo de producto
    $sql = "UPDATE producto_tipo SET nom_producto_tipo = '$nom', est_producto_tipo = $est WHERE id_producto_tipo = '$id_producto_tipo'";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}
?>
