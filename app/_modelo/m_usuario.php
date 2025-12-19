<?php 
function AutentificarUsuario($user, $pass)
{
    require_once("../_conexion/conexion.php");

    $sqlc = "SELECT 
                u.id_usuario,
                u.usu_usuario,
                u.id_personal,
                p.nom_personal,
                c.nom_cargo,
                a.nom_area
            FROM usuario u
            INNER JOIN {$bd_complemento}.personal p ON u.id_personal = p.id_personal
            INNER JOIN {$bd_complemento}.cargo c ON p.id_cargo = c.id_cargo
            INNER JOIN {$bd_complemento}.area a ON p.id_area = a.id_area
            WHERE u.usu_usuario = ? 
            AND u.con_usuario = ? 
            AND u.est_usuario = 1 
            AND p.act_personal = 1";

    $stmt = mysqli_prepare($con, $sqlc);
    mysqli_stmt_bind_param($stmt, "ss", $user, $pass);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    $datos_usuario = array();
    while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
        $datos_usuario[] = $row;        
    }
        
    mysqli_close($con);
    return $datos_usuario;
}

/**
 * Obtener permisos completos del usuario
 */
function obtenerPermisosUsuario($id_usuario) {
    // ✅ CREAR NUEVA CONEXIÓN PARA ESTA FUNCIÓN
    require("../_conexion/conexion.php"); // ✅ USAR require EN LUGAR DE require_once
    
    // ✅ VERIFICAR QUE LA CONEXIÓN EXISTA
    if (!isset($con) || !$con) {
        error_log("❌ Error: No se pudo establecer conexión a la base de datos");
        return array('acceso_sistema' => true);
    }
    
    $sql = "SELECT DISTINCT
                m.nom_modulo,
                a.nom_accion
            FROM usuario_rol ur
            INNER JOIN rol r ON ur.id_rol = r.id_rol
            INNER JOIN permiso p ON r.id_rol = p.id_rol
            INNER JOIN modulo_accion ma ON p.id_modulo_accion = ma.id_modulo_accion
            INNER JOIN modulo m ON ma.id_modulo = m.id_modulo
            INNER JOIN accion a ON ma.id_accion = a.id_accion
            WHERE ur.id_usuario = ? 
            AND ur.est_usuario_rol = 1 
            AND r.est_rol = 1 
            AND p.est_permiso = 1 
            AND ma.est_modulo_accion = 1 
            AND m.est_modulo = 1 
            AND a.est_accion = 1";
    
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        error_log("❌ Error al preparar consulta de permisos: " . mysqli_error($con));
        mysqli_close($con);
        return array('acceso_sistema' => true);
    }
    
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    
    // ✅ Inicializar permisos básicos
    $permisos_formateados = array(
        'acceso_sistema' => true
    );
    
    while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
        $modulo = strtolower(trim($row['nom_modulo']));
        $accion = strtolower(trim($row['nom_accion']));
        
        // ✅ LOG PARA DEBUG
        error_log("🔑 Permiso encontrado: $modulo -> $accion");
        
        // Crear permiso en formato: accion_modulo
        $key = $accion . '_' . str_replace(' ', '_', $modulo);
        $permisos_formateados[$key] = true;
        
        // ✅ MAPEO ESPECÍFICO PARA USO DE MATERIAL
        if ($modulo == 'uso de material') {
            switch($accion) {
                case 'crear':
                    $permisos_formateados['crear_uso_de_material'] = true;
                    $permisos_formateados['reg_uso_de_material'] = true; // Compatibilidad
                    break;
                case 'editar':
                    $permisos_formateados['editar_uso_de_material'] = true;
                    $permisos_formateados['edi_uso_de_material'] = true; // Compatibilidad
                    break;
                case 'ver':
                    $permisos_formateados['ver_uso_de_material'] = true;
                    break;
                case 'ver todo':
                    $permisos_formateados['ver_todo_uso_de_material'] = true;
                    break;
                case 'anular':
                    $permisos_formateados['anular_uso_de_material'] = true;
                    break;
            }
        }
    }
    
    // ✅ LOG FINAL PARA DEBUG
    error_log("🔐 Total permisos para usuario $id_usuario: " . count($permisos_formateados));
    error_log("🔐 Permisos: " . print_r($permisos_formateados, true));
    
    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return $permisos_formateados;
}

/**
 * Grabar auditoría desde app móvil
 */
function GrabarAuditoriaApp($id_usuario, $nom_usuario, $accion, $modulo, $descripcion)
{
    // ✅ CREAR NUEVA CONEXIÓN PARA ESTA FUNCIÓN
    require("../_conexion/conexion.php"); // ✅ USAR require EN LUGAR DE require_once

    // ✅ VERIFICAR QUE LA CONEXIÓN EXISTA
    if (!isset($con) || !$con) {
        error_log("❌ Error: No se pudo establecer conexión para auditoría");
        return "NO";
    }

    date_default_timezone_set('America/Lima');
    $fecha_actual = date("Y-m-d H:i:s");

    // MANEJAR CASOS DONDE ID_USUARIO ES NULL O 0
    $id_usuario_final = ($id_usuario === null || $id_usuario === 0) ? 0 : intval($id_usuario);
    $nom_usuario_final = $nom_usuario ?: 'Usuario desconocido';

    $sql = "INSERT INTO auditoria (id_usuario, nom_usuario, accion, modulo, descripcion, fecha) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        error_log("❌ Error al preparar auditoría: " . mysqli_error($con));
        mysqli_close($con);
        return "NO";
    }
    
    mysqli_stmt_bind_param($stmt, "isssss", $id_usuario_final, $nom_usuario_final, $accion, $modulo, $descripcion, $fecha_actual);
    
    if (mysqli_stmt_execute($stmt)) {
        $rpta = "SI";
        error_log("✅ Auditoría APP registrada: $accion - $descripcion");
    } else {
        $rpta = "NO";
        error_log("❌ Error en auditoría APP: " . mysqli_error($con));
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);
    
    return $rpta;
}

function obtenerIdPersonalPorUsuario($id_usuario) {
    require("../_conexion/conexion.php");
    
    if (!isset($con) || !$con) {
        return 0;
    }
    
    $sql = "SELECT id_personal FROM usuario WHERE id_usuario = ? AND est_usuario = 1";
    $stmt = mysqli_prepare($con, $sql);
    
    if (!$stmt) {
        mysqli_close($con);
        return 0;
    }
    
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    
    $id_personal = 0;
    if ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
        $id_personal = $row['id_personal'];
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($con);
    
    return $id_personal;
}

?>