<?php

//-----------------------------------------------------------------------
function GrabarPersonal($id_area, $id_cargo, $nom, $ape, $dni, $email, $tel, $est) 
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe un personal con el mismo DNI en la base principal
    $sql_verificar = "SELECT COUNT(*) as total FROM personal WHERE dni_personal = '$dni'";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Insertar nuevo personal
    $sql = "INSERT INTO personal (id_area, id_cargo, nom_personal, ape_personal, dni_personal, email_personal, tel_personal, est_personal) 
            VALUES ($id_area, $id_cargo, '$nom', '$ape', '$dni', '$email', '$tel', $est)";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
function MostrarPersonal()
{
    include("../_conexion/conexion.php");
    include("../_conexion/conexion_complemento.php");

    $resultado = array();

    // Obtener personal de la base principal
    $sqlc = "SELECT p.id_personal, p.nom_personal, p.ape_personal, p.dni_personal, 
             p.email_personal, p.tel_personal, p.est_personal,
             a.nom_area, c.nom_cargo, 'Principal' as origen 
             FROM personal p 
             INNER JOIN area a ON p.id_area = a.id_area 
             INNER JOIN cargo c ON p.id_cargo = c.id_cargo 
             ORDER BY p.nom_personal ASC";
    $resc = mysqli_query($con, $sqlc);

    if (!$resc) {
        error_log("Error en MostrarPersonal() - Base principal: " . mysqli_error($con));
    } else {
        while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
            $resultado[] = $rowc;
        }
    }

    // Obtener personal de la base complementaria (Inspecciones)
    $sql_comp = "SELECT p.id_personal, p.nom_personal, '' as ape_personal, p.dni_personal, 
                 p.email_personal, p.cel_personal as tel_personal, p.act_personal as est_personal,
                 a.nom_area, c.nom_cargo, 'Inspecciones' as origen 
                 FROM personal p 
                 INNER JOIN area a ON p.id_area = a.id_area 
                 INNER JOIN cargo c ON p.id_cargo = c.id_cargo 
                 ORDER BY p.nom_personal ASC";
    $res_comp = mysqli_query($con_comp, $sql_comp);
    
    if (!$res_comp) {
        error_log("Error en MostrarPersonal() - Base Inspecciones: " . mysqli_error($con_comp));
    } else {
        while ($rowc = mysqli_fetch_array($res_comp, MYSQLI_ASSOC)) {
            $resultado[] = $rowc;
        }
    }

    // Ordenar por nombre
    usort($resultado, function($a, $b) {
        return strcmp($a['nom_personal'], $b['nom_personal']);
    });

    mysqli_close($con);
    mysqli_close($con_comp);
    
    return $resultado;
}

//-----------------------------------------------------------------------
function MostrarPersonalActivo()
{
    include("../_conexion/conexion.php");
    include("../_conexion/conexion_complemento.php");

    $resultado = array();

    // Personal activo de la base principal
    $sqlc = "SELECT p.id_personal, p.nom_personal, p.ape_personal, 
             a.nom_area, c.nom_cargo, 'Principal' as origen 
             FROM personal p 
             INNER JOIN area a ON p.id_area = a.id_area 
             INNER JOIN cargo c ON p.id_cargo = c.id_cargo 
             WHERE p.est_personal = 1 
             ORDER BY p.nom_personal ASC";
    $resc = mysqli_query($con, $sqlc);

    if (!$resc) {
        error_log("Error en MostrarPersonalActivo() - Base principal: " . mysqli_error($con));
    } else {
        while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
            $resultado[] = $rowc;
        }
    }

    // Personal activo de la base complementaria
    $sql_comp = "SELECT p.id_personal, p.nom_personal, '' as ape_personal, 
                 a.nom_area, c.nom_cargo, 'Inspecciones' as origen 
                 FROM personal p 
                 INNER JOIN area a ON p.id_area = a.id_area 
                 INNER JOIN cargo c ON p.id_cargo = c.id_cargo 
                 WHERE p.act_personal = 1 
                 ORDER BY p.nom_personal ASC";
    $res_comp = mysqli_query($con_comp, $sql_comp);
    
    if (!$res_comp) {
        error_log("Error en MostrarPersonalActivo() - Base Inspecciones: " . mysqli_error($con_comp));
    } else {
        while ($rowc = mysqli_fetch_array($res_comp, MYSQLI_ASSOC)) {
            $resultado[] = $rowc;
        }
    }

    // Ordenar por nombre
    usort($resultado, function($a, $b) {
        return strcmp($a['nom_personal'], $b['nom_personal']);
    });

    mysqli_close($con);
    mysqli_close($con_comp);
    
    return $resultado;
}

//-----------------------------------------------------------------------
function EditarPersonal($id, $id_area, $id_cargo, $nom, $ape, $dni, $email, $tel, $est)
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe otro personal con el mismo DNI
    $sql_verificar = "SELECT COUNT(*) as total FROM personal WHERE dni_personal = '$dni' AND id_personal != $id";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Actualizar personal
    $sql = "UPDATE personal SET 
            id_area = $id_area, 
            id_cargo = $id_cargo, 
            nom_personal = '$nom', 
            ape_personal = '$ape', 
            dni_personal = '$dni', 
            email_personal = '$email', 
            tel_personal = '$tel', 
            est_personal = $est 
            WHERE id_personal = $id";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
function ObtenerPersonal($id)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT p.*, a.nom_area, c.nom_cargo 
            FROM personal p 
            INNER JOIN area a ON p.id_area = a.id_area 
            INNER JOIN cargo c ON p.id_cargo = c.id_cargo 
            WHERE p.id_personal = $id";
    $result = mysqli_query($con, $sql);
    
    $personal = mysqli_fetch_assoc($result);
    
    mysqli_close($con);
    
    return $personal;
}

//-----------------------------------------------------------------------
function BuscarPersonalPorDNI($dni)
{
    include("../_conexion/conexion.php");
    include("../_conexion/conexion_complemento.php");

    // Buscar primero en la base principal
    $sql = "SELECT p.*, a.nom_area, c.nom_cargo, 'Principal' as origen 
            FROM personal p 
            INNER JOIN area a ON p.id_area = a.id_area 
            INNER JOIN cargo c ON p.id_cargo = c.id_cargo 
            WHERE p.dni_personal = '$dni'";
    $result = mysqli_query($con, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $personal = mysqli_fetch_assoc($result);
        mysqli_close($con);
        mysqli_close($con_comp);
        return $personal;
    }

    // Si no se encuentra en la principal, buscar en la complementaria
    $sql_comp = "SELECT p.id_personal, p.nom_personal, '' as ape_personal, 
                 p.dni_personal, p.email_personal, p.cel_personal as tel_personal,
                 a.nom_area, c.nom_cargo, 'Inspecciones' as origen 
                 FROM personal p 
                 INNER JOIN area a ON p.id_area = a.id_area 
                 INNER JOIN cargo c ON p.id_cargo = c.id_cargo 
                 WHERE p.dni_personal = '$dni'";
    $result_comp = mysqli_query($con_comp, $sql_comp);
    
    if ($result_comp && mysqli_num_rows($result_comp) > 0) {
        $personal = mysqli_fetch_assoc($result_comp);
        mysqli_close($con);
        mysqli_close($con_comp);
        return $personal;
    }

    mysqli_close($con);
    mysqli_close($con_comp);
    
    return false;
}

?>