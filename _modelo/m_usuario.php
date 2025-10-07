<?php

//-----------------------------------------------------------------------
//-----------------------------------------------------------------------
function GrabarUsuario($id_personal, $usu, $pass, $est, $roles = array()) 
{
    include("../_conexion/conexion.php");
    include("../_conexion/conexion_complemento.php");
    
    // Verificar si ya existe un usuario con el mismo nombre de usuario
    $sql_verificar = "SELECT COUNT(*) as total FROM usuario WHERE usu_usuario = ?";
    $stmt = mysqli_prepare($con, $sql_verificar);
    mysqli_stmt_bind_param($stmt, "s", $usu);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        mysqli_close($con_comp);
        return "NO"; // Ya existe
    }
    
    // Verificar si el personal existe en la base principal
    $sql_verificar_personal = "SELECT COUNT(*) as total FROM personal WHERE id_personal = ?";
    $stmt_personal = mysqli_prepare($con, $sql_verificar_personal);
    mysqli_stmt_bind_param($stmt_personal, "i", $id_personal);
    mysqli_stmt_execute($stmt_personal);
    $resultado_personal = mysqli_stmt_get_result($stmt_personal);
    $fila_personal = mysqli_fetch_assoc($resultado_personal);
    
    // Si el personal no existe en la base principal, buscarlo en la complementaria y sincronizarlo
    if ($fila_personal['total'] == 0) {
        // Buscar el personal en la base complementaria
        $sql_comp = "SELECT p.*, a.nom_area, c.nom_cargo 
                     FROM personal p 
                     INNER JOIN area a ON p.id_area = a.id_area 
                     INNER JOIN cargo c ON p.id_cargo = c.id_cargo 
                     WHERE p.id_personal = ?";
        $stmt_comp = mysqli_prepare($con_comp, $sql_comp);
        mysqli_stmt_bind_param($stmt_comp, "i", $id_personal);
        mysqli_stmt_execute($stmt_comp);
        $resultado_comp = mysqli_stmt_get_result($stmt_comp);
        
        if ($row_comp = mysqli_fetch_assoc($resultado_comp)) {
            // Verificar/crear área en la base principal
            $id_area_comp = $row_comp['id_area'];
            $nom_area = $row_comp['nom_area'];
            
            $sql_area = "SELECT id_area FROM area WHERE nom_area = ?";
            $stmt_area = mysqli_prepare($con, $sql_area);
            mysqli_stmt_bind_param($stmt_area, "s", $nom_area);
            mysqli_stmt_execute($stmt_area);
            $result_area = mysqli_stmt_get_result($stmt_area);
            
            if ($row_area = mysqli_fetch_assoc($result_area)) {
                $id_area_principal = $row_area['id_area'];
            } else {
                // Crear el área si no existe
                $sql_insert_area = "INSERT INTO area (nom_area, est_area) VALUES (?, 1)";
                $stmt_insert_area = mysqli_prepare($con, $sql_insert_area);
                mysqli_stmt_bind_param($stmt_insert_area, "s", $nom_area);
                mysqli_stmt_execute($stmt_insert_area);
                $id_area_principal = mysqli_insert_id($con);
            }
            
            // Verificar/crear cargo en la base principal
            $id_cargo_comp = $row_comp['id_cargo'];
            $nom_cargo = $row_comp['nom_cargo'];
            
            $sql_cargo = "SELECT id_cargo FROM cargo WHERE nom_cargo = ?";
            $stmt_cargo = mysqli_prepare($con, $sql_cargo);
            mysqli_stmt_bind_param($stmt_cargo, "s", $nom_cargo);
            mysqli_stmt_execute($stmt_cargo);
            $result_cargo = mysqli_stmt_get_result($stmt_cargo);
            
            if ($row_cargo = mysqli_fetch_assoc($result_cargo)) {
                $id_cargo_principal = $row_cargo['id_cargo'];
            } else {
                // Crear el cargo si no existe
                $sql_insert_cargo = "INSERT INTO cargo (nom_cargo, est_cargo) VALUES (?, 1)";
                $stmt_insert_cargo = mysqli_prepare($con, $sql_insert_cargo);
                mysqli_stmt_bind_param($stmt_insert_cargo, "s", $nom_cargo);
                mysqli_stmt_execute($stmt_insert_cargo);
                $id_cargo_principal = mysqli_insert_id($con);
            }
            
            // Ahora insertar el personal en la base principal
            $nom_personal = $row_comp['nom_personal'];
            $dni_personal = $row_comp['dni_personal'];
            $email_personal = $row_comp['email_personal'];
            $cel_personal = $row_comp['cel_personal'];
            $act_personal = $row_comp['act_personal'];
            
            $sql_insert_personal = "INSERT INTO personal (id_personal, id_area, id_cargo, nom_personal, ape_personal, dni_personal, email_personal, tel_personal, est_personal) 
                                   VALUES (?, ?, ?, ?, '', ?, ?, ?, ?)";
            $stmt_insert_personal = mysqli_prepare($con, $sql_insert_personal);
            mysqli_stmt_bind_param($stmt_insert_personal, "iiissssi", 
                $id_personal, 
                $id_area_principal, 
                $id_cargo_principal, 
                $nom_personal, 
                $dni_personal, 
                $email_personal, 
                $cel_personal, 
                $act_personal
            );
            
            if (!mysqli_stmt_execute($stmt_insert_personal)) {
                mysqli_close($con);
                mysqli_close($con_comp);
                return "ERROR_SINCRONIZAR";
            }
        } else {
            mysqli_close($con);
            mysqli_close($con_comp);
            return "PERSONAL_NO_ENCONTRADO";
        }
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
        mysqli_close($con_comp);
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
        mysqli_close($con_comp);
        return "SI";
    } else {
        mysqli_close($con);
        mysqli_close($con_comp);
        return "ERROR";
    }
}


//-----------------------------------------------------------------------
//-----------------------------------------------------------------------
function EditarUsuario($id, $usu, $pass, $est, $roles = array())
{
    include("../_conexion/conexion.php");
    include("../_conexion/conexion_complemento.php");
    
    // Verificar si ya existe otro usuario con el mismo nombre de usuario
    $sql_verificar = "SELECT COUNT(*) as total FROM usuario WHERE usu_usuario = ? AND id_usuario != ?";
    $stmt = mysqli_prepare($con, $sql_verificar);
    mysqli_stmt_bind_param($stmt, "si", $usu, $id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        mysqli_close($con_comp);
        return "NO"; // Ya existe
    }
    
    // Obtener el id_personal del usuario actual para verificar si existe en la base principal
    $sql_personal_actual = "SELECT id_personal FROM usuario WHERE id_usuario = ?";
    $stmt_personal = mysqli_prepare($con, $sql_personal_actual);
    mysqli_stmt_bind_param($stmt_personal, "i", $id);
    mysqli_stmt_execute($stmt_personal);
    $result_personal = mysqli_stmt_get_result($stmt_personal);
    
    if ($row_personal = mysqli_fetch_assoc($result_personal)) {
        $id_personal = $row_personal['id_personal'];
        
        // Verificar si el personal existe en la base principal
        $sql_verificar_personal = "SELECT COUNT(*) as total FROM personal WHERE id_personal = ?";
        $stmt_verif = mysqli_prepare($con, $sql_verificar_personal);
        mysqli_stmt_bind_param($stmt_verif, "i", $id_personal);
        mysqli_stmt_execute($stmt_verif);
        $resultado_personal = mysqli_stmt_get_result($stmt_verif);
        $fila_personal = mysqli_fetch_assoc($resultado_personal);
        
        // Si el personal no existe en la base principal, buscarlo en la complementaria y sincronizarlo
        if ($fila_personal['total'] == 0) {
            // Buscar el personal en la base complementaria
            $sql_comp = "SELECT p.*, a.nom_area, c.nom_cargo 
                         FROM personal p 
                         INNER JOIN area a ON p.id_area = a.id_area 
                         INNER JOIN cargo c ON p.id_cargo = c.id_cargo 
                         WHERE p.id_personal = ?";
            $stmt_comp = mysqli_prepare($con_comp, $sql_comp);
            mysqli_stmt_bind_param($stmt_comp, "i", $id_personal);
            mysqli_stmt_execute($stmt_comp);
            $resultado_comp = mysqli_stmt_get_result($stmt_comp);
            
            if ($row_comp = mysqli_fetch_assoc($resultado_comp)) {
                // Verificar/crear área en la base principal
                $id_area_comp = $row_comp['id_area'];
                $nom_area = $row_comp['nom_area'];
                
                $sql_area = "SELECT id_area FROM area WHERE nom_area = ?";
                $stmt_area = mysqli_prepare($con, $sql_area);
                mysqli_stmt_bind_param($stmt_area, "s", $nom_area);
                mysqli_stmt_execute($stmt_area);
                $result_area = mysqli_stmt_get_result($stmt_area);
                
                if ($row_area = mysqli_fetch_assoc($result_area)) {
                    $id_area_principal = $row_area['id_area'];
                } else {
                    // Crear el área si no existe
                    $sql_insert_area = "INSERT INTO area (nom_area, est_area) VALUES (?, 1)";
                    $stmt_insert_area = mysqli_prepare($con, $sql_insert_area);
                    mysqli_stmt_bind_param($stmt_insert_area, "s", $nom_area);
                    mysqli_stmt_execute($stmt_insert_area);
                    $id_area_principal = mysqli_insert_id($con);
                }
                
                // Verificar/crear cargo en la base principal
                $id_cargo_comp = $row_comp['id_cargo'];
                $nom_cargo = $row_comp['nom_cargo'];
                
                $sql_cargo = "SELECT id_cargo FROM cargo WHERE nom_cargo = ?";
                $stmt_cargo = mysqli_prepare($con, $sql_cargo);
                mysqli_stmt_bind_param($stmt_cargo, "s", $nom_cargo);
                mysqli_stmt_execute($stmt_cargo);
                $result_cargo = mysqli_stmt_get_result($stmt_cargo);
                
                if ($row_cargo = mysqli_fetch_assoc($result_cargo)) {
                    $id_cargo_principal = $row_cargo['id_cargo'];
                } else {
                    // Crear el cargo si no existe
                    $sql_insert_cargo = "INSERT INTO cargo (nom_cargo, est_cargo) VALUES (?, 1)";
                    $stmt_insert_cargo = mysqli_prepare($con, $sql_insert_cargo);
                    mysqli_stmt_bind_param($stmt_insert_cargo, "s", $nom_cargo);
                    mysqli_stmt_execute($stmt_insert_cargo);
                    $id_cargo_principal = mysqli_insert_id($con);
                }
                
                // Ahora insertar el personal en la base principal
                $nom_personal = $row_comp['nom_personal'];
                $dni_personal = $row_comp['dni_personal'];
                $email_personal = $row_comp['email_personal'];
                $cel_personal = $row_comp['cel_personal'];
                $act_personal = $row_comp['act_personal'];
                
                $sql_insert_personal = "INSERT INTO personal (id_personal, id_area, id_cargo, nom_personal, ape_personal, dni_personal, email_personal, tel_personal, est_personal) 
                                       VALUES (?, ?, ?, ?, '', ?, ?, ?, ?)";
                $stmt_insert_personal = mysqli_prepare($con, $sql_insert_personal);
                mysqli_stmt_bind_param($stmt_insert_personal, "iiissssi", 
                    $id_personal, 
                    $id_area_principal, 
                    $id_cargo_principal, 
                    $nom_personal, 
                    $dni_personal, 
                    $email_personal, 
                    $cel_personal, 
                    $act_personal
                );
                
                if (!mysqli_stmt_execute($stmt_insert_personal)) {
                    mysqli_close($con);
                    mysqli_close($con_comp);
                    return "ERROR_SINCRONIZAR";
                }
            } else {
                mysqli_close($con);
                mysqli_close($con_comp);
                return "PERSONAL_NO_ENCONTRADO";
            }
        }
    }
    
    // Actualizar usuario
    if (!empty($pass)) {
        // Si se proporciona nueva contraseña
        $sql = "UPDATE usuario SET 
                usu_usuario = ?, 
                con_usuario = ?,
                est_usuario = ? 
                WHERE id_usuario = ?";
        $stmt_update = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt_update, "ssii", $usu, $pass, $est, $id);
    } else {
        // Si no se cambia la contraseña
        $sql = "UPDATE usuario SET 
                usu_usuario = ?, 
                est_usuario = ? 
                WHERE id_usuario = ?";
        $stmt_update = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt_update, "sii", $usu, $est, $id);
    }
    
    if (mysqli_stmt_execute($stmt_update)) {
        // Eliminar roles anteriores
        $sql_delete_roles = "DELETE FROM usuario_rol WHERE id_usuario = ?";
        $stmt_delete = mysqli_prepare($con, $sql_delete_roles);
        mysqli_stmt_bind_param($stmt_delete, "i", $id);
        mysqli_stmt_execute($stmt_delete);
        
        // Asignar nuevos roles
        if (!empty($roles)) {
            foreach ($roles as $id_rol) {
                $sql_rol = "INSERT INTO usuario_rol (id_usuario, id_rol, est_usuario_rol) 
                           VALUES (?, ?, 1)";
                $stmt_rol = mysqli_prepare($con, $sql_rol);
                mysqli_stmt_bind_param($stmt_rol, "ii", $id, $id_rol);
                mysqli_stmt_execute($stmt_rol);
            }
        }
        
        mysqli_close($con);
        mysqli_close($con_comp);
        return "SI";
    } else {
        mysqli_close($con);
        mysqli_close($con_comp);
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
                   p.nom_personal, p.ape_personal, p.dni_personal
            FROM usuario u 
            INNER JOIN personal p ON u.id_personal = p.id_personal
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
                   p.nom_personal, p.ape_personal
            FROM usuario u 
            INNER JOIN personal p ON u.id_personal = p.id_personal
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
//-----------------------------------------------------------------------
function ObtenerPersonalSinUsuario()
{
    include("../_conexion/conexion.php");
    include("../_conexion/conexion_complemento.php");

    $resultado = array();

    // Personal sin usuario de la base principal
    $sql = "SELECT p.id_personal, p.nom_personal, p.ape_personal, p.dni_personal,
                   a.nom_area, c.nom_cargo, 'Principal' as origen
            FROM personal p 
            INNER JOIN area a ON p.id_area = a.id_area 
            INNER JOIN cargo c ON p.id_cargo = c.id_cargo 
            LEFT JOIN usuario u ON p.id_personal = u.id_personal
            WHERE p.est_personal = 1 AND u.id_usuario IS NULL
            ORDER BY p.nom_personal ASC";
    $result = mysqli_query($con, $sql);

    if (!$result) {
        error_log("Error en ObtenerPersonalSinUsuario() - Base principal: " . mysqli_error($con));
    } else {
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $resultado[] = $row;
        }
    }

    // Personal activo sin usuario de la base complementaria (Inspecciones)
    // Nota: La base complementaria no tiene tabla de usuarios, así que traemos todos los activos
    $sql_comp = "SELECT p.id_personal, p.nom_personal, '' as ape_personal, p.dni_personal,
                        a.nom_area, c.nom_cargo, 'Inspecciones' as origen
                 FROM personal p 
                 INNER JOIN area a ON p.id_area = a.id_area 
                 INNER JOIN cargo c ON p.id_cargo = c.id_cargo 
                 WHERE p.act_personal = 1
                 ORDER BY p.nom_personal ASC";
    $result_comp = mysqli_query($con_comp, $sql_comp);
    
    if (!$result_comp) {
        error_log("Error en ObtenerPersonalSinUsuario() - Base Inspecciones: " . mysqli_error($con_comp));
    } else {
        while ($row = mysqli_fetch_array($result_comp, MYSQLI_ASSOC)) {
            // Verificar que este personal de inspecciones no exista ya como usuario en la base principal
            // usando el DNI como referencia
            $dni_check = $row['dni_personal'];
            $sql_check = "SELECT COUNT(*) as total FROM usuario u 
                         INNER JOIN personal p ON u.id_personal = p.id_personal 
                         WHERE p.dni_personal = '$dni_check'";
            $result_check = mysqli_query($con, $sql_check);
            $row_check = mysqli_fetch_assoc($result_check);
            
            // Solo agregar si no existe un usuario con ese DNI en la base principal
            if ($row_check['total'] == 0) {
                $resultado[] = $row;
            }
        }
    }

    // Ordenar todo por nombre
    usort($resultado, function($a, $b) {
        return strcmp($a['nom_personal'], $b['nom_personal']);
    });

    mysqli_close($con);
    mysqli_close($con_comp);
    
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
?>