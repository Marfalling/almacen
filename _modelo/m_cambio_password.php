<?php
//=======================================================================
// MODELO: m_usuario.php
//=======================================================================

require_once("../_conexion/conexion.php");

function ObtenerDatosUsuario($id_usuario) {
    global $con;
    $sql = "SELECT u.usu_usuario, CONCAT(p.nom_personal, ' ', p.ape_personal) AS nombre_completo
            FROM usuario u
            INNER JOIN personal p ON u.id_personal = p.id_personal
            WHERE u.id_usuario = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function ActualizarPassword($id_usuario, $nueva_password) {
    global $con;
    $sql = "UPDATE usuario SET con_usuario = ? WHERE id_usuario = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $nueva_password, $id_usuario);
    return mysqli_stmt_execute($stmt);
}

function CambiarPassword($id_usuario, $password_actual, $password_nueva) {
    global $con;

    //Obtener contraseña actual
    $sql = "SELECT con_usuario FROM usuario WHERE id_usuario = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $usuario = mysqli_fetch_assoc($result);

    if (!$usuario) {
        return false; // No existe el usuario
    }

    //Verificar contraseña actual 
    if ($usuario['con_usuario'] !== $password_actual) {
        return false;
    }

    //Actualizar la contraseña nueva
    $sql_update = "UPDATE usuario SET con_usuario = ? WHERE id_usuario = ?";
    $stmt_update = mysqli_prepare($con, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "si", $password_nueva, $id_usuario);

    return mysqli_stmt_execute($stmt_update);
}
?>
