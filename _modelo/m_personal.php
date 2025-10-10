<?php
//=======================================================================
// MODELO: m_personal.php 
//=======================================================================

// Mostrar todo el personal
function MostrarPersonal() {
    include("../_conexion/conexion_complemento.php");

    $personal = [];
    $sql = "SELECT 
                p.id_personal,
                p.nom_personal,
                p.dni_personal,
                p.cel_personal,
                p.email_personal,
                p.act_personal,
                a.nom_area,
                c.nom_cargo
            FROM personal p
            LEFT JOIN area a ON p.id_area = a.id_area
            LEFT JOIN cargo c ON p.id_cargo = c.id_cargo
            ORDER BY p.nom_personal ASC";

    $res = mysqli_query($con_comp, $sql);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $personal[] = $row;
        }
    }

    mysqli_close($con_comp);
    return $personal;
}

// Consultar personal por ID
function ConsultarPersonal($id_personal) {
    include("../_conexion/conexion_complemento.php");
    $id_personal = intval($id_personal);
    $personal = null;

    $sql = "SELECT 
                p.id_personal,
                p.id_cargo,
                p.id_area,
                p.id_tipo,
                p.nom_personal,
                p.dni_personal,
                p.cel_personal,
                p.email_personal,
                p.act_personal
            FROM personal p
            WHERE p.id_personal = $id_personal
            LIMIT 1";

    $res = mysqli_query($con_comp, $sql);
    if ($res && mysqli_num_rows($res) > 0) {
        $personal = mysqli_fetch_assoc($res);
    }

    mysqli_close($con_comp);
    return $personal;
}

// Registrar nuevo personal
function GrabarPersonal($id_area, $id_cargo, $nom, $dni, $email, $cel, $est)
{
    include("../_conexion/conexion_complemento.php");

    $id_area  = intval($id_area);
    $id_cargo = intval($id_cargo);
    $id_tipo  = 2; // por defecto según tu estructura
    $nom_completo = trim($nom);
    $dni   = trim($dni);
    $email = trim($email);
    $cel   = trim($cel);
    $est   = intval($est);

    // Verificar duplicado por DNI
    $sql_check = "SELECT id_personal FROM personal WHERE dni_personal = ? LIMIT 1";
    $stmt_check = mysqli_prepare($con_comp, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "s", $dni);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);
    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        mysqli_stmt_close($stmt_check);
        mysqli_close($con_comp);
        return "NO";
    }
    mysqli_stmt_close($stmt_check);

    // Insertar
    $sql = "INSERT INTO personal
                (id_cargo, id_area, id_tipo, nom_personal, dni_personal, cel_personal, email_personal, pass_personal, act_personal)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con_comp, $sql);
    if (!$stmt) {
        mysqli_close($con_comp);
        return "ERROR";
    }

    // por convención ponemos pass_personal = dni (como estaba en tus datos)
    $pass = $dni;
    mysqli_stmt_bind_param($stmt, "iiisssssi",
        $id_cargo, $id_area, $id_tipo,
        $nom_completo, $dni, $cel, $email, $pass, $est
    );

    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con_comp);

    return $ok ? "SI" : "ERROR";
}

// Actualizar personal existente
function ActualizarPersonal($id_personal, $id_area, $id_cargo, $nom, $dni, $email, $cel, $est) {
    include("../_conexion/conexion_complemento.php");

    $id_personal = intval($id_personal);
    $nom = strtoupper(trim($nom));
    $email = trim($email);
    $dni = trim($dni);
    $cel = trim($cel);

    // Verificar duplicado (excepto el mismo registro)
    $sql_verif = "SELECT * FROM personal 
                  WHERE (dni_personal = '$dni' OR email_personal = '$email') 
                  AND id_personal != $id_personal";
    $res_verif = mysqli_query($con_comp, $sql_verif);
    if ($res_verif && mysqli_num_rows($res_verif) > 0) {
        mysqli_close($con_comp);
        return "NO"; // Ya existe otro con mismo DNI o correo
    }

    $sql = "UPDATE personal SET
                id_area = $id_area,
                id_cargo = $id_cargo,
                nom_personal = '$nom',
                dni_personal = '$dni',
                email_personal = '$email',
                cel_personal = '$cel',
                act_personal = $est
            WHERE id_personal = $id_personal";

    $res = mysqli_query($con_comp, $sql);
    mysqli_close($con_comp);

    return $res ? "SI" : "ERROR";
}

function ObtenerPersonal($id_personal) {
    include("../_conexion/conexion_complemento.php");
    $id_personal = intval($id_personal);

    $sql = "SELECT * FROM personal WHERE id_personal = $id_personal LIMIT 1";
    $res = mysqli_query($con_comp, $sql);
    if ($res && mysqli_num_rows($res) > 0) {
        $personal = mysqli_fetch_assoc($res);
        mysqli_close($con_comp);
        return $personal;
    }

    mysqli_close($con_comp);
    return null;
}


function EditarPersonal($id_personal, $id_area, $id_cargo, $nom, $dni, $email, $cel, $est)
{
    include("../_conexion/conexion_complemento.php");

    $id_personal = intval($id_personal);
    $id_area     = intval($id_area);
    $id_cargo    = intval($id_cargo);
    $nom_completo = trim($nom);
    $dni   = trim($dni);
    $email = trim($email);
    $cel   = trim($cel);
    $est   = intval($est);

    // Verificar duplicado de DNI en otro registro
    $sql_check = "SELECT id_personal FROM personal WHERE dni_personal = ? AND id_personal != ? LIMIT 1";
    $stmt_check = mysqli_prepare($con_comp, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "si", $dni, $id_personal);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);
    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        mysqli_stmt_close($stmt_check);
        mysqli_close($con_comp);
        return "NO";
    }
    mysqli_stmt_close($stmt_check);

    // Actualizar
    $sql = "UPDATE personal SET
                id_area = ?, id_cargo = ?, nom_personal = ?, dni_personal = ?, cel_personal = ?, email_personal = ?, act_personal = ?
            WHERE id_personal = ?";
    $stmt = mysqli_prepare($con_comp, $sql);
    if (!$stmt) {
        mysqli_close($con_comp);
        return "ERROR";
    }
    mysqli_stmt_bind_param($stmt, "iissssii",
        $id_area, $id_cargo,
        $nom_completo, $dni, $cel, $email, $est,
        $id_personal
    );

    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con_comp);

    return $ok ? "SI" : "ERROR";
}

function BuscarPersonalPorDNI($dni)
{
    include("../_conexion/conexion_complemento.php");

    $dni = trim($dni);
    $sql = "SELECT 
                p.id_personal,
                p.id_cargo,
                p.id_area,
                p.id_tipo,
                p.nom_personal,
                p.dni_personal,
                p.cel_personal,
                p.email_personal,
                p.pass_personal,
                p.act_personal,
                a.nom_area,
                c.nom_cargo
            FROM personal p
            LEFT JOIN area a ON p.id_area = a.id_area
            LEFT JOIN cargo c ON p.id_cargo = c.id_cargo
            WHERE p.dni_personal = ?
            LIMIT 1";

    $stmt = mysqli_prepare($con_comp, $sql);
    if (!$stmt) {
        mysqli_close($con_comp);
        return false;
    }
    mysqli_stmt_bind_param($stmt, "s", $dni);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = $res ? mysqli_fetch_assoc($res) : false;

    if ($row) {
        $row['est_personal'] = isset($row['act_personal']) ? (int)$row['act_personal'] : 0;
        $row['cel_personal'] = $row['cel_personal'] ?? '';
        $row['origen'] = 'Principal';
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con_comp);
    return $row;
}
?>

