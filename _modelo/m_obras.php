<?php
//=======================================================================
// MODELO: m_obras.php
//=======================================================================

//Mostrar todas las obras activas (desde ambas bases de datos)
function MostrarObrasActivas() {
    include("../_conexion/conexion.php");               // BD principal (almacen)
    include("../_conexion/conexion_complemento.php");   // BD adicional (arceperu)

    $obras = [];

    // ---- BD principal ----
    $sql_local = "SELECT id_obra, nom_obra, est_obra, 'local' AS fuente FROM obra WHERE est_obra = 1";
    $res_local = mysqli_query($con, $sql_local);
    if ($res_local) {
        while ($row = mysqli_fetch_assoc($res_local)) {
            $obras[] = $row;
        }
    }

    // ---- BD adicional ----
    $sql_comp = "SELECT id_obra, nom_obra, est_obra, 'externa' AS fuente FROM obra WHERE est_obra = 1";
    $res_comp = mysqli_query($con_comp, $sql_comp);
    if ($res_comp) {
        while ($row = mysqli_fetch_assoc($res_comp)) {
            // Evitar duplicados por nombre
            $existe = false;
            foreach ($obras as $o) {
                if (trim(strtoupper($o['nom_obra'])) === trim(strtoupper($row['nom_obra']))) {
                    $existe = true;
                    break;
                }
            }
            if (!$existe) $obras[] = $row;
        }
    }

    mysqli_close($con);
    mysqli_close($con_comp);
    return $obras;
}

//Mostrar todas las obras (sin filtro)
function MostrarObras() {
    include("../_conexion/conexion.php");
    include("../_conexion/conexion_complemento.php");

    $obras = [];

    // ---- BD principal ----
    $sql_local = "SELECT id_obra, nom_obra, est_obra, 'local' AS fuente FROM obra ORDER BY nom_obra ASC";
    $res_local = mysqli_query($con, $sql_local);
    if ($res_local) {
        while ($row = mysqli_fetch_assoc($res_local)) {
            $obras[] = $row;
        }
    }

    // ---- BD adicional ----
    $sql_comp = "SELECT id_obra, nom_obra, est_obra, 'externa' AS fuente FROM obra ORDER BY nom_obra ASC";
    $res_comp = mysqli_query($con_comp, $sql_comp);
    if ($res_comp) {
        while ($row = mysqli_fetch_assoc($res_comp)) {
            $existe = false;
            foreach ($obras as $o) {
                if (trim(strtoupper($o['nom_obra'])) === trim(strtoupper($row['nom_obra']))) {
                    $existe = true;
                    break;
                }
            }
            if (!$existe) $obras[] = $row;
        }
    }

    mysqli_close($con);
    mysqli_close($con_comp);
    return $obras;
}

//Consultar obra por ID
function ConsultarObra($id_obra, $fuente = 'local') {
    $obra = null;
    $id_obra = intval($id_obra);

    if ($fuente === 'externa') {
        include("../_conexion/conexion_complemento.php");
        $sql = "SELECT id_obra, nom_obra, est_obra, 'externa' AS fuente FROM obra WHERE id_obra = $id_obra LIMIT 1";
        $res = mysqli_query($con_comp, $sql);
        if ($res && mysqli_num_rows($res) > 0) {
            $obra = mysqli_fetch_assoc($res);
        }
        mysqli_close($con_comp);
    } else {
        include("../_conexion/conexion.php");
        $sql = "SELECT id_obra, nom_obra, est_obra, 'local' AS fuente FROM obra WHERE id_obra = $id_obra LIMIT 1";
        $res = mysqli_query($con, $sql);
        if ($res && mysqli_num_rows($res) > 0) {
            $obra = mysqli_fetch_assoc($res);
        }
        mysqli_close($con);
    }

    return $obra;
}

//Actualizar obra (solo BD principal)
function ActualizarObra($id_obra, $nom, $est) {
    include("../_conexion/conexion.php");

    $nom = strtoupper(trim($nom));
    $sql_verif = "SELECT * FROM obra WHERE nom_obra = '$nom' AND id_obra != $id_obra";
    $res_verif = mysqli_query($con, $sql_verif);
    if ($res_verif && mysqli_num_rows($res_verif) > 0) {
        mysqli_close($con);
        return "NO";
    }

    $sql = "UPDATE obra SET nom_obra = '$nom', est_obra = $est WHERE id_obra = $id_obra";
    $res = mysqli_query($con, $sql);
    mysqli_close($con);

    return $res ? "SI" : "ERROR";
}

//Registrar nueva obra (solo BD principal)
function RegistrarObra($nom, $est) {
    include("../_conexion/conexion.php");

    $nom = strtoupper(trim($nom));
    $sql_verif = "SELECT * FROM obra WHERE nom_obra = '$nom'";
    $res_verif = mysqli_query($con, $sql_verif);

    if ($res_verif && mysqli_num_rows($res_verif) > 0) {
        mysqli_close($con);
        return "NO";
    }

    $sql = "INSERT INTO obra (nom_obra, est_obra) VALUES ('$nom', $est)";
    $res = mysqli_query($con, $sql);
    mysqli_close($con);

    return $res ? "SI" : "ERROR";
}
?>


