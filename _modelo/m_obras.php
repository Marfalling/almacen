<?php
//=======================================================================
// MODELO: m_obras.php (adaptado para usar solo conexion_complemento.php)
//=======================================================================

// Mostrar todas las obras / subestaciones
function MostrarObras() {
    include("../_conexion/conexion.php");

    $obras = [];
    $sql = "SELECT id_subestacion, nom_subestacion, act_subestacion 
            FROM {$bd_complemento}.subestacion 
            ORDER BY nom_subestacion ASC";
    $res = mysqli_query($con, $sql);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $obras[] = $row;
        }
    }

    mysqli_close($con);
    return $obras;
}

function MostrarObrasActivas() 
{
    include("../_conexion/conexion.php");

    $obras = [];
    $sql = "SELECT id_subestacion, nom_subestacion, act_subestacion 
            FROM {$bd_complemento}.subestacion 
            WHERE act_subestacion = 1
            ORDER BY nom_subestacion ASC";
    $res = mysqli_query($con, $sql);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $obras[] = $row;
        }
    }

    mysqli_close($con);
    return $obras;
}

// Consultar obra / subestacion por ID
function ConsultarObra($id_obra) {
    include("../_conexion/conexion.php");
    $id_obra = intval($id_obra);
    $obra = null;

    $sql = "SELECT id_subestacion, nom_subestacion, act_subestacion 
            FROM {$bd_complemento}.subestacion 
            WHERE id_subestacion = $id_obra LIMIT 1";
    $res = mysqli_query($con, $sql);
    if ($res && mysqli_num_rows($res) > 0) {
        $obra = mysqli_fetch_assoc($res);
    }

    mysqli_close($con);
    return $obra;
}

// Registrar nueva obra / subestacion
function RegistrarObra($nom, $est) {
    include("../_conexion/conexion.php");
    $nom = strtoupper(trim($nom));

    // Verificar duplicados
    $sql_verif = "SELECT * FROM {$bd_complemento}.subestacion WHERE nom_subestacion = '$nom'";
    $res_verif = mysqli_query($con, $sql_verif);
    if ($res_verif && mysqli_num_rows($res_verif) > 0) {
        mysqli_close($con);
        return "NO";
    }

    $sql = "INSERT INTO {$bd_complemento}.subestacion (nom_subestacion, act_subestacion) VALUES ('$nom', $est)";
    $res = mysqli_query($con, $sql);
    mysqli_close($con);

    return $res ? "SI" : "ERROR";
}

// Actualizar obra / subestacion
function ActualizarObra($id_obra, $nom, $est) {
    include("../_conexion/conexion.php");
    $id_obra = intval($id_obra);
    $nom = strtoupper(trim($nom));

    // Verificar duplicados
    $sql_verif = "SELECT * FROM {$bd_complemento}.subestacion WHERE nom_subestacion = '$nom' AND id_subestacion != $id_obra";
    $res_verif = mysqli_query($con, $sql_verif);
    if ($res_verif && mysqli_num_rows($res_verif) > 0) {
        mysqli_close($con);
        return "NO";
    }

    $sql = "UPDATE {$bd_complemento}.subestacion 
            SET nom_subestacion = '$nom', act_subestacion = $est 
            WHERE id_subestacion = $id_obra";
    $res = mysqli_query($con, $sql);
    mysqli_close($con);

    return $res ? "SI" : "ERROR";
}