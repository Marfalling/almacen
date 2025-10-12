<?php
//=======================================================================
// MODELO: m_cargo.php
//=======================================================================

// REGISTRAR NUEVO CARGO
function GrabarCargo($nom, $est) 
{
    include("../_conexion/conexion.php");

    // Verificar si ya existe un cargo con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) as total FROM {$bd_complemento}.cargo WHERE nom_cargo = '$nom'";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);

    if ($fila['total'] > 0) {
        return "NO"; // Ya existe
    }

    // Insertar nuevo cargo
    $sql = "INSERT INTO {$bd_complemento}.cargo (nom_cargo, act_cargo) VALUES ('$nom', $est)";

    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
// MOSTRAR TODOS LOS CARGOS
function MostrarCargos()
{
    include("../_conexion/conexion.php");

    $sql = "SELECT * FROM {$bd_complemento}.cargo ORDER BY nom_cargo ASC";
    $resultado = mysqli_query($con, $sql);

    $cargos = array();
    while ($row = mysqli_fetch_assoc($resultado)) {
        $cargos[] = $row;
    }

    mysqli_close($con);
    return $cargos;
}

//-----------------------------------------------------------------------
// MOSTRAR CARGOS ACTIVOS
function MostrarCargosActivos() {

    include("../_conexion/conexion.php");

    $sql = "SELECT id_cargo, nom_cargo FROM {$bd_complemento}.cargo WHERE act_cargo = 1 ORDER BY nom_cargo ASC";
    $res = mysqli_query($con, $sql);

    $cargos = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $cargos[] = $row;
        }
    }

    mysqli_close($con);
    return $cargos;
}

//-----------------------------------------------------------------------
// EDITAR CARGO
function EditarCargo($id, $nom, $est)
{
    include("../_conexion/conexion.php");

    // Verificar si ya existe otro cargo con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) as total FROM {$bd_complemento}.cargo 
                      WHERE nom_cargo = '$nom' AND id_cargo != $id";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);

    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }

    // Actualizar cargo
    $sql = "UPDATE {$bd_complemento}.cargo 
            SET nom_cargo = '$nom', act_cargo = $est 
            WHERE id_cargo = $id";

    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
// OBTENER CARGO POR ID
function ObtenerCargo($id)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT * FROM {$bd_complemento}.cargo WHERE id_cargo = $id";
    $result = mysqli_query($con, $sql);

    $cargo = mysqli_fetch_assoc($result);

    mysqli_close($con);
    return $cargo;
}
?>
