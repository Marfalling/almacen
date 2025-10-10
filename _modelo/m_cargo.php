<?php
//=======================================================================
// MODELO: m_cargo.php
//=======================================================================
require_once("../_conexion/conexion_complemento.php"); // $con_comp global

//-----------------------------------------------------------------------
// REGISTRAR NUEVO CARGO
function GrabarCargo($nom, $est) 
{
    global $con_comp;

    // Verificar si ya existe un cargo con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) as total FROM cargo WHERE nom_cargo = '$nom'";
    $resultado_verificar = mysqli_query($con_comp, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);

    if ($fila['total'] > 0) {
        return "NO"; // Ya existe
    }

    // Insertar nuevo cargo
    $sql = "INSERT INTO cargo (nom_cargo, act_cargo) VALUES ('$nom', $est)";

    if (mysqli_query($con_comp, $sql)) {
        return "SI";
    } else {
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
// MOSTRAR TODOS LOS CARGOS
function MostrarCargos()
{
    global $con_comp;

    $sql = "SELECT * FROM cargo ORDER BY nom_cargo ASC";
    $resultado = mysqli_query($con_comp, $sql);

    $cargos = array();
    while ($row = mysqli_fetch_assoc($resultado)) {
        $cargos[] = $row;
    }

    return $cargos;
}

//-----------------------------------------------------------------------
// MOSTRAR CARGOS ACTIVOS
function MostrarCargosActivos() {
    global $con_comp;

    $sql = "SELECT id_cargo, nom_cargo FROM cargo WHERE act_cargo = 1 ORDER BY nom_cargo ASC";
    $res = mysqli_query($con_comp, $sql);

    $cargos = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $cargos[] = $row;
        }
    }
    return $cargos;
}

//-----------------------------------------------------------------------
// EDITAR CARGO
function EditarCargo($id, $nom, $est)
{
    global $con_comp;

    // Verificar si ya existe otro cargo con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) as total FROM cargo 
                      WHERE nom_cargo = '$nom' AND id_cargo != $id";
    $resultado_verificar = mysqli_query($con_comp, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);

    if ($fila['total'] > 0) {
        return "NO"; // Ya existe
    }

    // Actualizar cargo
    $sql = "UPDATE cargo 
            SET nom_cargo = '$nom', act_cargo = $est 
            WHERE id_cargo = $id";

    if (mysqli_query($con_comp, $sql)) {
        return "SI";
    } else {
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
// OBTENER CARGO POR ID
function ObtenerCargo($id)
{
    global $con_comp;

    $sql = "SELECT * FROM cargo WHERE id_cargo = $id";
    $result = mysqli_query($con_comp, $sql);

    $cargo = mysqli_fetch_assoc($result);

    return $cargo;
}
?>
