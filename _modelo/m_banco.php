<?php
//=======================================================================
// FUNCIONES PARA BANCO
//=======================================================================

//-----------------------------------------------------------------------
// Mostrar todos los bancos
function MostrarBanco() {
    include("../_conexion/conexion.php");
    $sqlc = "SELECT * FROM banco ORDER BY nom_banco ASC";
    $resc = mysqli_query($con, $sqlc);
    $resultado = array();
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }
    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
// Registrar un nuevo banco
function GrabarBanco($cod, $nom, $est) {
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe el código o el nombre
    $sqlv = "SELECT * FROM banco WHERE cod_banco = '$cod' OR nom_banco = '$nom'";
    $resv = mysqli_query($con, $sqlv);
    
    if (mysqli_num_rows($resv) > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Insertar nuevo registro
    $sqli = "INSERT INTO banco (cod_banco, nom_banco, est_banco) VALUES ('$cod', '$nom', $est)";
    $resi = mysqli_query($con, $sqli);
    
    mysqli_close($con);
    
    if ($resi) {
        return "SI";
    } else {
        return "NO";
    }
}

//-----------------------------------------------------------------------
// Obtener un banco específico por ID
function ObtenerBanco($id_banco)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT * FROM banco WHERE id_banco = '$id_banco'";
    $resultado = mysqli_query($con, $sql);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $banco = mysqli_fetch_assoc($resultado);
        mysqli_close($con);
        return $banco;
    } else {
        mysqli_close($con);
        return false;
    }
}

//-----------------------------------------------------------------------
// Editar un banco existente
function EditarBanco($id_banco, $cod, $nom, $est) 
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe otro banco con el mismo código o nombre (excluyendo el actual)
    $sql_verificar = "SELECT COUNT(*) as total FROM banco 
                      WHERE (cod_banco = '$cod' OR nom_banco = '$nom') 
                      AND id_banco != '$id_banco'";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe otro banco con ese código o nombre
    }
    
    // Actualizar registro
    $sql = "UPDATE banco 
            SET cod_banco = '$cod', nom_banco = '$nom', est_banco = $est 
            WHERE id_banco = '$id_banco'";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}
?>