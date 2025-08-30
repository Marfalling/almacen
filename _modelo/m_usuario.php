<?php

//-----------------------------------------------------------------------
function GrabarUsuario($id_personal, $usu, $pass, $est, $roles = array()) 
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe un usuario con el mismo nombre de usuario
    $sql_verificar = "SELECT COUNT(*) as total FROM usuario WHERE usu_usuario = '$usu'";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Verificar si el personal ya tiene un usuario asignado
    $sql_verificar_personal = "SELECT COUNT(*) as total FROM usuario WHERE id_personal = $id_personal";
    $resultado_verificar_personal = mysqli_query($con, $sql_verificar_personal);
    $fila_personal = mysqli_fetch_assoc($resultado_verificar_personal);
    
    if ($fila_personal['total'] > 0) {
        mysqli_close($con);
        return "PERSONAL_YA_ASIGNADO";
    }
    
    // Insertar nuevo usuario
    $sql = "INSERT INTO usuario (id_personal, usu_usuario, con_usuario, est_usuario) 
            VALUES ($id_personal, '$usu', '$pass', $est)";
    
    if (mysqli_query($con, $sql)) {
        $id_usuario = mysqli_insert_id($con);
        
        // Asignar roles al usuario
        if (!empty($roles)) {
            foreach ($roles as $id_rol) {
                $sql_rol = "INSERT INTO usuario_rol (id_usuario, id_rol, est_usuario_rol) 
                           VALUES ($id_usuario, $id_rol, 1)";
                mysqli_query($con, $sql_rol);
            }
        }
        
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
function EditarUsuario($id, $usu, $pass, $est, $roles = array())
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe otro usuario con el mismo nombre de usuario
    $sql_verificar = "SELECT COUNT(*) as total FROM usuario WHERE usu_usuario = '$usu' AND id_usuario != $id";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Actualizar usuario
    if (!empty($pass)) {
        // Si se proporciona nueva contraseña
        $sql = "UPDATE usuario SET 
                usu_usuario = '$usu', 
                con_usuario = '$pass',
                est_usuario = $est 
                WHERE id_usuario = $id";
    } else {
        // Si no se cambia la contraseña
        $sql = "UPDATE usuario SET 
                usu_usuario = '$usu', 
                est_usuario = $est 
                WHERE id_usuario = $id";
    }
    
    if (mysqli_query($con, $sql)) {
        // Eliminar roles anteriores
        $sql_delete_roles = "DELETE FROM usuario_rol WHERE id_usuario = $id";
        mysqli_query($con, $sql_delete_roles);
        
        // Asignar nuevos roles
        if (!empty($roles)) {
            foreach ($roles as $id_rol) {
                $sql_rol = "INSERT INTO usuario_rol (id_usuario, id_rol, est_usuario_rol) 
                           VALUES ($id, $id_rol, 1)";
                mysqli_query($con, $sql_rol);
            }
        }
        
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
function MostrarUsuario()
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT u.*, 
                   p.nom_personal, p.ape_personal, p.dni_personal,
                   a.nom_area,
                   c.nom_cargo,
                   GROUP_CONCAT(r.nom_rol SEPARATOR ', ') as roles
             FROM usuario u 
             INNER JOIN personal p ON u.id_personal = p.id_personal
             INNER JOIN area a ON p.id_area = a.id_area
             INNER JOIN cargo c ON p.id_cargo = c.id_cargo
             LEFT JOIN usuario_rol ur ON u.id_usuario = ur.id_usuario AND ur.est_usuario_rol = 1
             LEFT JOIN rol r ON ur.id_rol = r.id_rol AND r.est_rol = 1
             GROUP BY u.id_usuario
             ORDER BY p.nom_personal ASC";
    $resc = mysqli_query($con, $sqlc);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    
    return $resultado;
}

//-----------------------------------------------------------------------
function MostrarUsuarioActivo()
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT u.*, 
                   p.nom_personal, p.ape_personal, p.dni_personal,
                   GROUP_CONCAT(r.nom_rol SEPARATOR ', ') as roles
             FROM usuario u 
             INNER JOIN personal p ON u.id_personal = p.id_personal
             LEFT JOIN usuario_rol ur ON u.id_usuario = ur.id_usuario AND ur.est_usuario_rol = 1
             LEFT JOIN rol r ON ur.id_rol = r.id_rol AND r.est_rol = 1
             WHERE u.est_usuario = 1 
             GROUP BY u.id_usuario
             ORDER BY p.nom_personal ASC";
    $resc = mysqli_query($con, $sqlc);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    
    return $resultado;
}



//-----------------------------------------------------------------------
function ObtenerUsuario($id)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT u.*, 
                   p.nom_personal, p.ape_personal, p.dni_personal,
                   a.nom_area,
                   c.nom_cargo
            FROM usuario u 
            INNER JOIN personal p ON u.id_personal = p.id_personal
            INNER JOIN area a ON p.id_area = a.id_area
            INNER JOIN cargo c ON p.id_cargo = c.id_cargo
            WHERE u.id_usuario = $id";
    $result = mysqli_query($con, $sql);
    
    $usuario = mysqli_fetch_assoc($result);
    
    // Obtener roles del usuario
    $sql_roles = "SELECT ur.id_rol, r.nom_rol 
                  FROM usuario_rol ur 
                  INNER JOIN rol r ON ur.id_rol = r.id_rol 
                  WHERE ur.id_usuario = $id AND ur.est_usuario_rol = 1 AND r.est_rol = 1";
    $result_roles = mysqli_query($con, $sql_roles);
    
    $roles = array();
    while ($row_rol = mysqli_fetch_assoc($result_roles)) {
        $roles[] = $row_rol;
    }
    
    $usuario['roles'] = $roles;
    
    mysqli_close($con);
    
    return $usuario;
}

//-----------------------------------------------------------------------
function BuscarUsuarioPorNombre($usuario)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT u.*, 
                   p.nom_personal, p.ape_personal, p.dni_personal
            FROM usuario u 
            INNER JOIN personal p ON u.id_personal = p.id_personal
            WHERE u.usu_usuario = '$usuario'";
    $result = mysqli_query($con, $sql);
    
    $usuario = mysqli_fetch_assoc($result);
    
    mysqli_close($con);
    
    return $usuario;
}

//-----------------------------------------------------------------------
function ValidarCredenciales($usuario, $password)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT u.*, 
                   p.nom_personal, p.ape_personal
            FROM usuario u 
            INNER JOIN personal p ON u.id_personal = p.id_personal
            WHERE u.usu_usuario = '$usuario' AND u.est_usuario = 1";
    $result = mysqli_query($con, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $usuario_data = mysqli_fetch_assoc($result);
        
        // Para texto plano (desarrollo)
        if ($password === $usuario_data['con_usuario']) {
            // Obtener roles y permisos del usuario
            $sql_permisos = "SELECT DISTINCT r.nom_rol, m.nom_modulo, a.nom_accion 
                            FROM usuario_rol ur
                            INNER JOIN rol r ON ur.id_rol = r.id_rol
                            INNER JOIN permiso p ON r.id_rol = p.id_rol
                            INNER JOIN modulo_accion ma ON p.id_modulo_accion = ma.id_modulo_accion
                            INNER JOIN modulo m ON ma.id_modulo = m.id_modulo
                            INNER JOIN accion a ON ma.id_accion = a.id_accion
                            WHERE ur.id_usuario = " . $usuario_data['id_usuario'] . " 
                            AND ur.est_usuario_rol = 1 
                            AND r.est_rol = 1 
                            AND p.est_permiso = 1 
                            AND ma.est_modulo_accion = 1 
                            AND m.est_modulo = 1 
                            AND a.est_accion = 1";
            $result_permisos = mysqli_query($con, $sql_permisos);
            
            $permisos = array();
            while ($row_permiso = mysqli_fetch_assoc($result_permisos)) {
                $permisos[] = $row_permiso;
            }
            
            $usuario_data['permisos'] = $permisos;
            
            // Actualizar último acceso
            $update_sql = "UPDATE usuario SET fec_ultimo_acceso = NOW() WHERE id_usuario = " . $usuario_data['id_usuario'];
            mysqli_query($con, $update_sql);
            
            mysqli_close($con);
            return $usuario_data;
        }
    }
    
    mysqli_close($con);
    return false;
}

//-----------------------------------------------------------------------
function ObtenerPersonalSinUsuario()
{
    include("../_conexion/conexion.php");

    $sql = "SELECT p.id_personal, p.nom_personal, p.ape_personal, p.dni_personal,
                   a.nom_area, c.nom_cargo
            FROM personal p 
            INNER JOIN area a ON p.id_area = a.id_area 
            INNER JOIN cargo c ON p.id_cargo = c.id_cargo 
            LEFT JOIN usuario u ON p.id_personal = u.id_personal
            WHERE p.est_personal = 1 AND u.id_usuario IS NULL
            ORDER BY p.nom_personal ASC";
    $result = mysqli_query($con, $sql);

    $resultado = array();

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $resultado[] = $row;
    }

    mysqli_close($con);
    
    return $resultado;
}



//-----------------------------------------------------------------------
function ObtenerRolesUsuario($id_usuario)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT r.id_rol 
            FROM usuario_rol ur 
            INNER JOIN rol r ON ur.id_rol = r.id_rol 
            WHERE ur.id_usuario = $id_usuario AND ur.est_usuario_rol = 1 AND r.est_rol = 1";
    $result = mysqli_query($con, $sql);

    $roles = array();

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $roles[] = $row['id_rol'];
    }

    mysqli_close($con);
    
    return $roles;
}

?>