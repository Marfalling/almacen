<?php
//=======================================================================
// MODELO: m_obras.php (adaptado para usar solo conexion_complemento.php)
//=======================================================================

// Mostrar todas las obras / subestaciones
function MostrarObras() {
    include("../_conexion/conexion_complemento.php");

    $obras = [];
    $sql = "SELECT id_subestacion, nom_subestacion, act_subestacion 
            FROM subestacion 
            ORDER BY nom_subestacion ASC";
    $res = mysqli_query($con_comp, $sql);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $obras[] = $row;
        }
    }

    mysqli_close($con_comp);
    return $obras;
}

// Consultar obra / subestacion por ID
function ConsultarObra($id_obra) {
    include("../_conexion/conexion_complemento.php");
    $id_obra = intval($id_obra);
    $obra = null;

    $sql = "SELECT id_subestacion, nom_subestacion, act_subestacion 
            FROM subestacion 
            WHERE id_subestacion = $id_obra LIMIT 1";
    $res = mysqli_query($con_comp, $sql);
    if ($res && mysqli_num_rows($res) > 0) {
        $obra = mysqli_fetch_assoc($res);
    }

    mysqli_close($con_comp);
    return $obra;
}

// Registrar nueva obra / subestacion
function RegistrarObra($nom, $est) {
    include("../_conexion/conexion_complemento.php");
    $nom = strtoupper(trim($nom));

    // Verificar duplicados
    $sql_verif = "SELECT * FROM subestacion WHERE nom_subestacion = '$nom'";
    $res_verif = mysqli_query($con_comp, $sql_verif);
    if ($res_verif && mysqli_num_rows($res_verif) > 0) {
        mysqli_close($con_comp);
        return "NO";
    }

    $sql = "INSERT INTO subestacion (nom_subestacion, act_subestacion) VALUES ('$nom', $est)";
    $res = mysqli_query($con_comp, $sql);
    mysqli_close($con_comp);

    return $res ? "SI" : "ERROR";
}

// Actualizar obra / subestacion
function ActualizarObra($id_obra, $nom, $est) {
    include("../_conexion/conexion_complemento.php");
    $id_obra = intval($id_obra);
    $nom = strtoupper(trim($nom));

    // Verificar duplicados
    $sql_verif = "SELECT * FROM subestacion WHERE nom_subestacion = '$nom' AND id_subestacion != $id_obra";
    $res_verif = mysqli_query($con_comp, $sql_verif);
    if ($res_verif && mysqli_num_rows($res_verif) > 0) {
        mysqli_close($con_comp);
        return "NO";
    }

    $sql = "UPDATE subestacion 
            SET nom_subestacion = '$nom', act_subestacion = $est 
            WHERE id_subestacion = $id_obra";
    $res = mysqli_query($con_comp, $sql);
    mysqli_close($con_comp);

    return $res ? "SI" : "ERROR";
}
?>



