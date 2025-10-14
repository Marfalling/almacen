<?php

//-----------------------------------------------------------------------
function GrabarRol($nom_rol, $permisos, $est) 
{
    include("../_conexion/conexion.php");
    
    // Sanitizar el nombre del rol
    $nom_rol = mysqli_real_escape_string($con, $nom_rol);
    $est = (int)$est; // Convertir a entero
    
    // Verificar si ya existe un rol con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) as total FROM rol WHERE nom_rol = '$nom_rol'";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    
    if (!$resultado_verificar) {
        mysqli_close($con);
        return "ERROR";
    }
    
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Insertar nuevo rol
    $sql = "INSERT INTO rol (nom_rol, est_rol) VALUES ('$nom_rol', $est)";
    
    if (mysqli_query($con, $sql)) {
        $id_rol = mysqli_insert_id($con);
        
        // Asignar permisos al rol
        if (!empty($permisos) && is_array($permisos)) {
            foreach ($permisos as $id_modulo_accion) {
                $id_modulo_accion = (int)$id_modulo_accion; // Sanitizar
                $sql_permiso = "INSERT INTO permiso (id_rol, id_modulo_accion, est_permiso) 
                               VALUES ($id_rol, $id_modulo_accion, 1)";
                $resultado_permiso = mysqli_query($con, $sql_permiso);
                
                // Si falla algún permiso, log del error pero continúa
                if (!$resultado_permiso) {
                    error_log("Error insertando permiso: " . mysqli_error($con));
                }
            }
        }
        
        mysqli_close($con);
        return "SI";
    } else {
        error_log("Error insertando rol: " . mysqli_error($con));
        mysqli_close($con);
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
function MostrarRoles()
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT r.*, 
                   COUNT(p.id_permiso) as total_permisos,
                   COUNT(ur.id_usuario_rol) as total_usuarios
             FROM rol r 
             LEFT JOIN permiso p ON r.id_rol = p.id_rol AND p.est_permiso = 1
             LEFT JOIN usuario_rol ur ON r.id_rol = ur.id_rol AND ur.est_usuario_rol = 1
             GROUP BY r.id_rol
             ORDER BY r.nom_rol ASC";
    $resc = mysqli_query($con, $sqlc);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    
    return $resultado;
}

//-----------------------------------------------------------------------
function MostrarRolesActivos()
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT * FROM rol WHERE est_rol = 1 ORDER BY nom_rol ASC";
    $resc = mysqli_query($con, $sqlc);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    
    return $resultado;
}

//-----------------------------------------------------------------------
function EditarRol($id_rol, $nom_rol, $permisos, $est)
{
    include("../_conexion/conexion.php");
    
    // Sanitizar datos
    $id_rol = (int)$id_rol;
    $nom_rol = mysqli_real_escape_string($con, $nom_rol);
    $est = (int)$est;
    
    // Verificar si ya existe otro rol con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) as total FROM rol WHERE nom_rol = '$nom_rol' AND id_rol != $id_rol";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    
    if (!$resultado_verificar) {
        error_log("Error verificando rol existente: " . mysqli_error($con));
        mysqli_close($con);
        return "ERROR";
    }
    
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Actualizar rol
    $sql = "UPDATE rol SET nom_rol = '$nom_rol', est_rol = $est WHERE id_rol = $id_rol";
    
    if (mysqli_query($con, $sql)) {
        // Eliminar permisos anteriores
        $sql_delete_permisos = "DELETE FROM permiso WHERE id_rol = $id_rol";
        $resultado_delete = mysqli_query($con, $sql_delete_permisos);
        
        if (!$resultado_delete) {
            error_log("Error eliminando permisos anteriores: " . mysqli_error($con));
        }
        
        // Asignar nuevos permisos
        if (!empty($permisos) && is_array($permisos)) {
            foreach ($permisos as $id_modulo_accion) {
                $id_modulo_accion = (int)$id_modulo_accion; // Sanitizar
                $sql_permiso = "INSERT INTO permiso (id_rol, id_modulo_accion, est_permiso) 
                               VALUES ($id_rol, $id_modulo_accion, 1)";
                $resultado_permiso = mysqli_query($con, $sql_permiso);
                
                if (!$resultado_permiso) {
                    error_log("Error insertando permiso: " . mysqli_error($con));
                }
            }
        }
        
        mysqli_close($con);
        return "SI";
    } else {
        error_log("Error actualizando rol: " . mysqli_error($con));
        mysqli_close($con);
        return "ERROR";
    }
}
//-----------------------------------------------------------------------
function ObtenerRol($id_rol)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT * FROM rol WHERE id_rol = $id_rol";
    $result = mysqli_query($con, $sql);
    
    $rol = mysqli_fetch_assoc($result);
    
    // Obtener permisos del rol
    $sql_permisos = "SELECT p.id_modulo_accion, m.nom_modulo, a.nom_accion 
                     FROM permiso p 
                     INNER JOIN modulo_accion ma ON p.id_modulo_accion = ma.id_modulo_accion 
                     INNER JOIN modulo m ON ma.id_modulo = m.id_modulo
                     INNER JOIN accion a ON ma.id_accion = a.id_accion
                     WHERE p.id_rol = $id_rol AND p.est_permiso = 1";
    $result_permisos = mysqli_query($con, $sql_permisos);
    
    $permisos = array();
    while ($row_permiso = mysqli_fetch_assoc($result_permisos)) {
        $permisos[] = $row_permiso;
    }
    
    $rol['permisos'] = $permisos;
    
    mysqli_close($con);
    
    return $rol;
}

//-----------------------------------------------------------------------
/**
 * Función para obtener solo los IDs de roles de un usuario
 * (para mantener compatibilidad con código existente)
 */
function ObtenerRolesUsuarioIDs($id_usuario)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT r.id_rol 
            FROM usuario_rol ur 
            INNER JOIN rol r ON ur.id_rol = r.id_rol 
            WHERE ur.id_usuario = ? AND ur.est_usuario_rol = 1 AND r.est_rol = 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $roles = array();

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $roles[] = $row['id_rol'];
    }

    mysqli_close($con);
    
    return $roles;
}

//-----------------------------------------------------------------------
function MostrarModulosAcciones()
{
    include("../_conexion/conexion.php");

    $sql = "SELECT ma.id_modulo_accion, m.nom_modulo, a.nom_accion, a.id_accion
            FROM modulo_accion ma 
            INNER JOIN modulo m ON ma.id_modulo = m.id_modulo 
            INNER JOIN accion a ON ma.id_accion = a.id_accion 
            WHERE ma.est_modulo_accion = 1 
            AND m.est_modulo = 1 
            AND a.est_accion = 1
            ORDER BY m.nom_modulo ASC, a.nom_accion ASC";
    
    $result = mysqli_query($con, $sql);

    if (!$result) {
        // Log del error para debugging
        error_log("Error en MostrarModulosAcciones(): " . mysqli_error($con));
        mysqli_close($con);
        return array();
    }

    $resultado = array();
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $resultado[] = $row;
    }

    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
function MostrarModulos()
{
    include("../_conexion/conexion.php");

    $sql = "SELECT * FROM modulo WHERE est_modulo = 1 ORDER BY nom_modulo";
    $result = mysqli_query($con, $sql);

    $resultado = array();

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $resultado[] = $row;
    }

    mysqli_close($con);
    
    return $resultado;
}

//-----------------------------------------------------------------------
function MostrarAcciones()
{
    include("../_conexion/conexion.php");

    $sql = "SELECT * FROM accion WHERE est_accion = 1 ORDER BY nom_accion";
    $result = mysqli_query($con, $sql);

    $resultado = array();

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $resultado[] = $row;
    }

    mysqli_close($con);
    
    return $resultado;
}

//-----------------------------------------------------------------------
function VerificarPermisoUsuario($id_usuario, $modulo, $accion)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT COUNT(*) as total 
            FROM usuario_rol ur
            INNER JOIN permiso p ON ur.id_rol = p.id_rol
            INNER JOIN modulo_accion ma ON p.id_modulo_accion = ma.id_modulo_accion
            INNER JOIN modulo m ON ma.id_modulo = m.id_modulo
            INNER JOIN accion a ON ma.id_accion = a.id_accion
            WHERE ur.id_usuario = $id_usuario 
            AND m.nom_modulo = '$modulo' 
            AND a.nom_accion = '$accion'
            AND ur.est_usuario_rol = 1 
            AND p.est_permiso = 1 
            AND ma.est_modulo_accion = 1 
            AND m.est_modulo = 1 
            AND a.est_accion = 1";
    
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($result);
    
    mysqli_close($con);
    
    return $row['total'] > 0;
}

//-----------------------------------------------------------------------
function EliminarRol($id_rol)
{
    include("../_conexion/conexion.php");
    
    // Verificar si el rol tiene usuarios asignados
    $sql_verificar = "SELECT COUNT(*) as total FROM usuario_rol WHERE id_rol = $id_rol AND est_usuario_rol = 1";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "USUARIOS_ASIGNADOS";
    }
    
    // Eliminar permisos del rol
    $sql_permisos = "DELETE FROM permiso WHERE id_rol = $id_rol";
    mysqli_query($con, $sql_permisos);
    
    // Eliminar el rol
    $sql = "UPDATE rol SET est_rol = 0 WHERE id_rol = $id_rol";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}
?>