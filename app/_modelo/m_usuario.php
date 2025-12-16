<?php 
function AutentificarUsuario($user, $pass)
{
    require_once("../_conexion/conexion.php");

    // ✅ CONSULTA CON PREPARED STATEMENTS (seguridad contra SQL Injection)
    $sql = "SELECT 
                u.id_usuario,
                u.usu_usuario,
                u.id_personal,
                p.nom_personal,
                a.nom_area
            FROM usuario u
            INNER JOIN {$bd_complemento}.personal p ON u.id_personal = p.id_personal
            INNER JOIN {$bd_complemento}.area a ON p.id_area = a.id_area
            WHERE u.usu_usuario = ? 
            AND u.con_usuario = ? 
            AND u.est_usuario = 1 
            AND p.act_personal = 1";

    $stmt = mysqli_prepare($con, $sql);
    
    if (!$stmt) {
        error_log("❌ Error al preparar consulta: " . mysqli_error($con));
        mysqli_close($con);
        return NULL;
    }

    mysqli_stmt_bind_param($stmt, "ss", $user, $pass);
    mysqli_stmt_execute($stmt);
    $resultado_query = mysqli_stmt_get_result($stmt);

    $resultado = array();
    
    while ($row = mysqli_fetch_array($resultado_query, MYSQLI_ASSOC)) {
        $resultado[] = $row;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);

    return empty($resultado) ? NULL : $resultado;
}

/**
 * Obtener permisos del usuario
 * Retorna array con permisos formateados para la app
 */
function obtenerPermisosUsuario($id_usuario) {
    require_once("../_conexion/conexion.php");
    
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
        return [['acceso_sistema' => 1]]; // Permisos básicos por defecto
    }

    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    
    // ✅ INICIALIZAR CON PERMISOS BÁSICOS
    $permisos_formateados[0] = array(
        'acceso_sistema' => 1
    );
    
    while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
        $modulo = strtolower(trim($row['nom_modulo']));
        $accion = strtolower(trim($row['nom_accion']));
        
        // ✅ FORMATO MODERNO: accion_modulo
        $key = $accion . '_' . $modulo;
        $permisos_formateados[0][$key] = 1;
        
        // ✅ MAPEO PARA APP ANDROID (solo módulos relevantes para móvil)
        switch($modulo) {
            case 'dashboard':
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_dashboard'] = 1;
                }
                break;

            case 'uso de material':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_uso_material'] = 1;
                    $permisos_formateados[0]['reg_uso_material'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_uso_material'] = 1;
                    $permisos_formateados[0]['edi_uso_material'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_uso_material'] = 1;
                }
                if($accion == 'anular') {
                    $permisos_formateados[0]['anular_uso_material'] = 1;
                }
                break;

            case 'pedidos':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_pedidos'] = 1;
                    $permisos_formateados[0]['reg_pedidos'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_pedidos'] = 1;
                    $permisos_formateados[0]['edi_pedidos'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_pedidos'] = 1;
                }
                if($accion == 'aprobar') {
                    $permisos_formateados[0]['aprobar_pedidos'] = 1;
                }
                break;

            case 'salidas':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_salidas'] = 1;
                    $permisos_formateados[0]['reg_salidas'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_salidas'] = 1;
                    $permisos_formateados[0]['edi_salidas'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_salidas'] = 1;
                }
                if($accion == 'recepcionar') {
                    $permisos_formateados[0]['recepcionar_salidas'] = 1;
                }
                break;

            case 'movimientos':
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_movimientos'] = 1;
                }
                break;

            // ✅ AGREGAR MÁS MÓDULOS SEGÚN NECESITES EN LA APP
        }
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($con);
    
    return $permisos_formateados;
}
?>
