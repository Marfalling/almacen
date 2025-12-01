<?php
//-----------------------------------------------------------------------
function GrabarUsuario($id_personal, $usu, $pass, $est, $roles = array()) 
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe un usuario con el mismo nombre de usuario
    $sql_verificar = "SELECT COUNT(*) as total FROM usuario WHERE usu_usuario = ?";
    $stmt = mysqli_prepare($con, $sql_verificar);
    mysqli_stmt_bind_param($stmt, "s", $usu);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    

    // Verificar si el personal ya tiene un usuario asignado
    $sql_verificar_usuario = "SELECT COUNT(*) as total FROM usuario WHERE id_personal = ?";
    $stmt2 = mysqli_prepare($con, $sql_verificar_usuario);
    mysqli_stmt_bind_param($stmt2, "i", $id_personal);
    mysqli_stmt_execute($stmt2);
    $resultado_usuario = mysqli_stmt_get_result($stmt2);
    $fila_usuario = mysqli_fetch_assoc($resultado_usuario);
    
    if ($fila_usuario['total'] > 0) {
        mysqli_close($con);
        return "PERSONAL_YA_ASIGNADO";
    }
    
    // Insertar nuevo usuario
    $sql = "INSERT INTO usuario (id_personal, usu_usuario, con_usuario, est_usuario) 
            VALUES (?, ?, ?, ?)";
    $stmt3 = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt3, "issi", $id_personal, $usu, $pass, $est);
    
    if (mysqli_stmt_execute($stmt3)) {
        $id_usuario = mysqli_insert_id($con);
        
        // Asignar roles al usuario
        if (!empty($roles)) {
            foreach ($roles as $id_rol) {
                $sql_rol = "INSERT INTO usuario_rol (id_usuario, id_rol, est_usuario_rol) 
                           VALUES (?, ?, 1)";
                $stmt_rol = mysqli_prepare($con, $sql_rol);
                mysqli_stmt_bind_param($stmt_rol, "ii", $id_usuario, $id_rol);
                mysqli_stmt_execute($stmt_rol);
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
    include("../_conexion/conexion.php"); // conexión a la base principal

    // Verificar si ya existe otro usuario con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) AS total 
                      FROM usuario 
                      WHERE usu_usuario = ? AND id_usuario != ?";
    $stmt = mysqli_prepare($con, $sql_verificar);
    mysqli_stmt_bind_param($stmt, "si", $usu, $id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);

    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Usuario duplicado
    }

    // Verificar que el id_personal asociado exista en la base complementaria
    $sql_personal = "SELECT u.id_personal, 
                            p.nom_personal, 
                            p.dni_personal, 
                            a.nom_area, 
                            c.nom_cargo
                     FROM usuario u
                     LEFT JOIN {$bd_complemento}.personal p ON u.id_personal = p.id_personal
                     LEFT JOIN {$bd_complemento}.area a ON p.id_area = a.id_area
                     LEFT JOIN {$bd_complemento}.cargo c ON p.id_cargo = c.id_cargo
                     WHERE u.id_usuario = ?";
    $stmt_personal = mysqli_prepare($con, $sql_personal);
    mysqli_stmt_bind_param($stmt_personal, "i", $id);
    mysqli_stmt_execute($stmt_personal);
    $res_personal = mysqli_stmt_get_result($stmt_personal);

    if (!mysqli_fetch_assoc($res_personal)) {
        mysqli_close($con);
        return "PERSONAL_NO_ENCONTRADO";
    }

    // Actualizar datos del usuario
    if (!empty($pass)) {
        $sql_update = "UPDATE usuario 
                       SET usu_usuario = ?, 
                           con_usuario = ?, 
                           est_usuario = ?
                       WHERE id_usuario = ?";
        $stmt_update = mysqli_prepare($con, $sql_update);
        mysqli_stmt_bind_param($stmt_update, "ssii", $usu, $pass, $est, $id);
    } else {
        $sql_update = "UPDATE usuario 
                       SET usu_usuario = ?, 
                           est_usuario = ?
                       WHERE id_usuario = ?";
        $stmt_update = mysqli_prepare($con, $sql_update);
        mysqli_stmt_bind_param($stmt_update, "sii", $usu, $est, $id);
    }

    if (!mysqli_stmt_execute($stmt_update)) {
        mysqli_close($con);
        return "ERROR_UPDATE";
    }

    // Eliminar roles anteriores
    $sql_delete_roles = "DELETE FROM usuario_rol WHERE id_usuario = ?";
    $stmt_delete = mysqli_prepare($con, $sql_delete_roles);
    mysqli_stmt_bind_param($stmt_delete, "i", $id);
    mysqli_stmt_execute($stmt_delete);

    // Insertar nuevos roles
    if (!empty($roles)) {
        $sql_insert_rol = "INSERT INTO usuario_rol (id_usuario, id_rol, est_usuario_rol) VALUES (?, ?, 1)";
        $stmt_rol = mysqli_prepare($con, $sql_insert_rol);
        foreach ($roles as $id_rol) {
            mysqli_stmt_bind_param($stmt_rol, "ii", $id, $id_rol);
            mysqli_stmt_execute($stmt_rol);
        }
    }

    mysqli_close($con);
    return "SI";
}
//-----------------------------------------------------------------------
//-----------------------------------------------------------------------
function MostrarUsuario($excluir_superadmin = false)
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT u.*, 
                   p.nom_personal, 
                   p.dni_personal,
                   a.nom_area,
                   c.nom_cargo,
                   GROUP_CONCAT(DISTINCT r.nom_rol ORDER BY r.nom_rol SEPARATOR ', ') as roles
             FROM usuario u 
             INNER JOIN {$bd_complemento}.personal p ON u.id_personal = p.id_personal
             INNER JOIN {$bd_complemento}.area a ON p.id_area = a.id_area
             INNER JOIN {$bd_complemento}.cargo c ON p.id_cargo = c.id_cargo
             LEFT JOIN usuario_rol ur ON u.id_usuario = ur.id_usuario AND ur.est_usuario_rol = 1
             LEFT JOIN rol r ON ur.id_rol = r.id_rol AND r.est_rol = 1";
    
    // Si se debe excluir SUPER ADMINISTRADOR
    if ($excluir_superadmin) {
        $sqlc .= " WHERE u.id_usuario NOT IN (
                    SELECT DISTINCT ur2.id_usuario 
                    FROM usuario_rol ur2 
                    WHERE ur2.id_rol = 1 AND ur2.est_usuario_rol = 1
                  )";
    }
    
    $sqlc .= " GROUP BY u.id_usuario
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
                   p.nom_personal, p.dni_personal,
                   GROUP_CONCAT(r.nom_rol SEPARATOR ', ') as roles
             FROM usuario u 
             INNER JOIN {$bd_complemento}.personal p ON u.id_personal = p.id_personal
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
                   p.nom_personal, 
                   p.dni_personal,
                   a.nom_area,
                   c.nom_cargo
            FROM usuario u 
            INNER JOIN {$bd_complemento}.personal p ON u.id_personal = p.id_personal
            INNER JOIN {$bd_complemento}.area a ON p.id_area = a.id_area
            INNER JOIN {$bd_complemento}.cargo c ON p.id_cargo = c.id_cargo
            WHERE u.id_usuario = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $usuario = mysqli_fetch_assoc($result);
    
    // Obtener roles del usuario
    $sql_roles = "SELECT ur.id_rol, r.nom_rol 
                  FROM usuario_rol ur 
                  INNER JOIN rol r ON ur.id_rol = r.id_rol 
                  WHERE ur.id_usuario = ? AND ur.est_usuario_rol = 1 AND r.est_rol = 1";
    $stmt_roles = mysqli_prepare($con, $sql_roles);
    mysqli_stmt_bind_param($stmt_roles, "i", $id);
    mysqli_stmt_execute($stmt_roles);
    $result_roles = mysqli_stmt_get_result($stmt_roles);
    
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
                   p.nom_personal, 
                   p.dni_personal
            FROM usuario u 
            INNER JOIN {$bd_complemento}.personal p ON u.id_personal = p.id_personal
            WHERE u.usu_usuario = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $usuario);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $usuario_data = mysqli_fetch_assoc($result);
    
    mysqli_close($con);
    
    return $usuario_data;
}
//-----------------------------------------------------------------------
function ValidarCredenciales($usuario, $password)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT u.*, 
                   p.nom_personal
            FROM usuario u 
            INNER JOIN {$bd_complemento}.personal p ON u.id_personal = p.id_personal
            WHERE u.usu_usuario = ? AND u.est_usuario = 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $usuario);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $usuario_data = mysqli_fetch_assoc($result);
        
        // Verificar contraseña (en texto plano para desarrollo)
        if ($password === $usuario_data['con_usuario']) {
            // Obtener permisos completos del usuario
            $usuario_data['permisos'] = obtenerPermisosCompletos($usuario_data['id_usuario']);
            
            // Actualizar último acceso
            $update_sql = "UPDATE usuario SET fec_ultimo_acceso = NOW() WHERE id_usuario = ?";
            $stmt_update = mysqli_prepare($con, $update_sql);
            mysqli_stmt_bind_param($stmt_update, "i", $usuario_data['id_usuario']);
            mysqli_stmt_execute($stmt_update);
            
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

    $resultado = array();

    // Personal activo en la base complementaria sin usuario asociado en la principal
    $sql = "SELECT 
                p.id_personal, 
                p.nom_personal, 
                p.dni_personal,
                a.nom_area, 
                c.nom_cargo, 
                'Inspecciones' AS origen
            FROM {$bd_complemento}.personal p
            INNER JOIN {$bd_complemento}.area a ON p.id_area = a.id_area
            INNER JOIN {$bd_complemento}.cargo c ON p.id_cargo = c.id_cargo
            LEFT JOIN usuario u 
                   ON p.id_personal = u.id_personal
                      OR p.dni_personal = (
                          SELECT p2.dni_personal 
                          FROM {$bd_complemento}.personal p2
                          WHERE p2.id_personal = u.id_personal
                          LIMIT 1
                      )
            WHERE p.act_personal = 1 
              AND u.id_usuario IS NULL
            ORDER BY p.nom_personal ASC";

    $result = mysqli_query($con, $sql);

    if (!$result) {
        error_log("Error en ObtenerPersonalSinUsuario(): " . mysqli_error($con));
        mysqli_close($con);
        return [];
    }

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $resultado[] = $row;
    }

    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
/**
 * Función mejorada para obtener TODOS los permisos de un usuario
 * Mapea tanto los permisos dinámicos de la BD como los legacy
 */
function obtenerPermisosCompletos($id_usuario) {
    include("../_conexion/conexion.php");
    
    $sql = "SELECT DISTINCT
                m.nom_modulo,
                a.nom_accion,
                ma.id_modulo_accion,
                m.id_modulo,
                a.id_accion
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
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    
    // Inicializar permisos básicos
    $permisos_formateados = array();
    $permisos_formateados[0] = array(
        'ver_dashboard' => 1,
        'acceso_sistema' => 1
    );
    
    while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
        $modulo = strtolower(trim($row['nom_modulo']));
        $accion = strtolower(trim($row['nom_accion']));
        
        // Crear permiso en formato moderno: accion_modulo
        $key = $accion . '_' . $modulo;
        $permisos_formateados[0][$key] = 1;
        
        // MAPEO COMPLETO según tu estructura actual
        $mapeo_permisos = [
            'cliente' => [
                'crear' => ['reg_cliente', 'crear_cliente'],
                'editar' => ['ver_cliente', 'edi_cliente', 'editar_cliente'],
                'ver' => ['ver_cliente'],
                'eliminar' => ['eli_cliente', 'eliminar_cliente']
            ],
            'almacen' => [
                'crear' => ['reg_almacen', 'crear_almacen'],
                'editar' => ['ver_almacen', 'edi_almacen', 'editar_almacen'],
                'ver' => ['ver_almacen'],
                'eliminar' => ['eli_almacen', 'eliminar_almacen']
            ],
            'auditoria' => [
                'ver' => ['ver_auditoria'],
                'crear' => ['crear_auditoria'],
                'editar' => ['ver_auditoria', 'editar_auditoria']
            ],
            'procesos' => [
                'crear' => ['reg_procesos', 'crear_procesos', 
                           'crear_uso_material', 'crear_pedidos', 'crear_compras', 
                           'crear_ingresos', 'crear_salidas', 'crear_devoluciones'],
                'editar' => ['ver_procesos', 'edi_procesos', 'editar_procesos',
                           'editar_uso_material', 'editar_pedidos', 'editar_compras',
                           'editar_ingresos', 'editar_salidas', 'editar_devoluciones'],
                'ver' => ['ver_procesos', 'ver_uso_material', 'ver_pedidos', 
                         'ver_compras', 'ver_ingresos', 'ver_salidas', 'ver_devoluciones'],
                'eliminar' => ['eli_procesos', 'eliminar_procesos']
            ],
            'usuario' => [
                'crear' => ['reg_usuario', 'crear_usuario'],
                'editar' => ['ver_usuario', 'edi_usuario', 'editar_usuario'],
                'ver' => ['ver_usuario'],
                'eliminar' => ['eli_usuario', 'eliminar_usuario']
            ],
            // Módulos de mantenimiento
            'personal' => [
                'crear' => ['crear_personal'],
                'editar' => ['ver_personal', 'editar_personal'],
                'ver' => ['ver_personal'],
                'eliminar' => ['eliminar_personal']
            ],
            'rol' => [
                'crear' => ['crear_rol'],
                'editar' => ['ver_rol', 'editar_rol'],
                'ver' => ['ver_rol'],
                'eliminar' => ['eliminar_rol']
            ],
            'area' => [
                'crear' => ['crear_area'],
                'editar' => ['ver_area', 'editar_area'],
                'ver' => ['ver_area'],
                'eliminar' => ['eliminar_area']
            ],
            'cargo' => [
                'crear' => ['crear_cargo'],
                'editar' => ['ver_cargo', 'editar_cargo'],
                'ver' => ['ver_cargo'],
                'eliminar' => ['eliminar_cargo']
            ],
            'obra' => [
                'crear' => ['crear_obra'],
                'editar' => ['ver_obra', 'editar_obra'],
                'ver' => ['ver_obra'],
                'eliminar' => ['eliminar_obra']
            ],
            'ubicacion' => [
                'crear' => ['crear_ubicacion'],
                'editar' => ['ver_ubicacion', 'editar_ubicacion'],
                'ver' => ['ver_ubicacion'],
                'eliminar' => ['eliminar_ubicacion']
            ],
            'producto' => [
                'crear' => ['crear_producto'],
                'editar' => ['ver_producto', 'editar_producto'],
                'ver' => ['ver_producto'],
                'eliminar' => ['eliminar_producto']
            ],
            'producto_tipo' => [
                'crear' => ['crear_tipo_producto'],
                'editar' => ['ver_tipo_producto', 'editar_tipo_producto'],
                'ver' => ['ver_tipo_producto'],
                'eliminar' => ['eliminar_tipo_producto']
            ],
            'material_tipo' => [
                'crear' => ['crear_tipo_material'],
                'editar' => ['ver_tipo_material', 'editar_tipo_material'],
                'ver' => ['ver_tipo_material'],
                'eliminar' => ['eliminar_tipo_material']
            ],
            'unidad_medida' => [
                'crear' => ['crear_unidad_medida'],
                'editar' => ['ver_unidad_medida', 'editar_unidad_medida'],
                'ver' => ['ver_unidad_medida'],
                'eliminar' => ['eliminar_unidad_medida']
            ],
            'proveedor' => [
                'crear' => ['crear_proveedor'],
                'editar' => ['ver_proveedor', 'editar_proveedor'],
                'ver' => ['ver_proveedor'],
                'eliminar' => ['eliminar_proveedor']
            ],
            'moneda' => [
                'crear' => ['crear_moneda'],
                'editar' => ['ver_moneda', 'editar_moneda'],
                'ver' => ['ver_moneda'],
                'eliminar' => ['eliminar_moneda']
            ],
            'modulo' => [
                'crear' => ['crear_modulo'],
                'editar' => ['ver_modulo', 'editar_modulo'],
                'ver' => ['ver_modulo'],
                'eliminar' => ['eliminar_modulo']
            ]
        ];
        
        // Aplicar mapeo de permisos
        if (isset($mapeo_permisos[$modulo][$accion])) {
            foreach ($mapeo_permisos[$modulo][$accion] as $permiso_legacy) {
                $permisos_formateados[0][$permiso_legacy] = 1;
            }
        }
    }
    
    mysqli_close($con);
    return $permisos_formateados;
}

//-----------------------------------------------------------------------
/**
 * Función para sincronizar permisos cuando se detectan nuevos módulos
 */
function sincronizarPermisosModulos() {
    include("../_conexion/conexion.php");
    
    // Detectar módulos que no tienen todas las acciones básicas
    $modulos_existentes = array('Cliente', 'Almacen', 'AUDITORIA', 'PROCESOS', 'Usuario');
    $acciones_basicas = array('Crear', 'Editar', 'Ver', 'Eliminar');
    
    foreach ($modulos_existentes as $modulo) {
        // Verificar si el módulo existe
        $sql_modulo = "SELECT id_modulo FROM modulo WHERE nom_modulo = ? AND est_modulo = 1";
        $stmt = mysqli_prepare($con, $sql_modulo);
        mysqli_stmt_bind_param($stmt, "s", $modulo);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row_modulo = mysqli_fetch_assoc($result)) {
            $id_modulo = $row_modulo['id_modulo'];
            
            // Verificar cada acción básica
            foreach ($acciones_basicas as $accion) {
                $sql_accion = "SELECT id_accion FROM accion WHERE nom_accion = ? AND est_accion = 1";
                $stmt_accion = mysqli_prepare($con, $sql_accion);
                mysqli_stmt_bind_param($stmt_accion, "s", $accion);
                mysqli_stmt_execute($stmt_accion);
                $result_accion = mysqli_stmt_get_result($stmt_accion);
                
                if ($row_accion = mysqli_fetch_assoc($result_accion)) {
                    $id_accion = $row_accion['id_accion'];
                    
                    // Verificar si ya existe la combinación módulo-acción
                    $sql_existe = "SELECT COUNT(*) as total FROM modulo_accion 
                                  WHERE id_modulo = ? AND id_accion = ?";
                    $stmt_existe = mysqli_prepare($con, $sql_existe);
                    mysqli_stmt_bind_param($stmt_existe, "ii", $id_modulo, $id_accion);
                    mysqli_stmt_execute($stmt_existe);
                    $result_existe = mysqli_stmt_get_result($stmt_existe);
                    $row_existe = mysqli_fetch_assoc($result_existe);
                    
                    if ($row_existe['total'] == 0) {
                        // Crear la combinación módulo-acción
                        $sql_crear = "INSERT INTO modulo_accion (id_modulo, id_accion, est_modulo_accion) 
                                     VALUES (?, ?, 1)";
                        $stmt_crear = mysqli_prepare($con, $sql_crear);
                        mysqli_stmt_bind_param($stmt_crear, "ii", $id_modulo, $id_accion);
                        mysqli_stmt_execute($stmt_crear);
                    }
                }
            }
        }
    }
    
    mysqli_close($con);
}

//-----------------------------------------------------------------------
/**
 * Función para obtener estadísticas de permisos del usuario
 */
function obtenerEstadisticasPermisos($id_usuario) {
    include("../_conexion/conexion.php");
    
    $sql = "SELECT 
                COUNT(DISTINCT m.id_modulo) as total_modulos,
                COUNT(DISTINCT a.id_accion) as total_acciones,
                COUNT(DISTINCT p.id_permiso) as total_permisos
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
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $estadisticas = mysqli_fetch_assoc($result);
    
    mysqli_close($con);
    return $estadisticas;
}
//-----------------------------------------------------------------------
function ObtenerRolesUsuario($id_usuario)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT r.id_rol, r.nom_rol 
            FROM usuario_rol ur 
            INNER JOIN rol r ON ur.id_rol = r.id_rol 
            WHERE ur.id_usuario = ? AND ur.est_usuario_rol = 1 AND r.est_rol = 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $roles = array();

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $roles[] = $row;
    }

    mysqli_close($con);
    
    return $roles;
}

function CambiarPassword($id_usuario, $password_actual, $password_nueva)
{
    include("../_conexion/conexion.php");

    // Verificar contraseña actual
    $sql = "SELECT con_usuario FROM usuario WHERE id_usuario = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($result);

    if (!$fila || $fila['con_usuario'] !== $password_actual) {
        mysqli_close($con);
        return false; // contraseña actual incorrecta
    }

    // Actualizar la contraseña
    $sql_update = "UPDATE usuario SET con_usuario = ? WHERE id_usuario = ?";
    $stmt_update = mysqli_prepare($con, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "si", $password_nueva, $id_usuario);
    $res = mysqli_stmt_execute($stmt_update);

    mysqli_close($con);
    return $res;
}
// Función para filtrar rol SUPER ADMINISTRADOR
function MostrarRolesActivosFiltrados() {
    include("../_conexion/conexion.php");
    $sqlc = "SELECT * FROM rol WHERE est_rol = 1 AND id_rol != 1 ORDER BY nom_rol ASC";
    $resc = mysqli_query($con, $sqlc);
    $resultado = array();
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }
    mysqli_close($con);
    return $resultado;
}
?>