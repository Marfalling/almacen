<?php

require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

if (!verificarPermisoEspecifico('editar_usuarios')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'USUARIO', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

require_once("../_modelo/m_usuario.php");
require_once("../_modelo/m_rol.php");

// Verificar si el usuario actual es SUPERADMIN
$es_superadmin = esSuperAdmin($id);

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Editar Usuario</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $id_usuario = $_REQUEST['id_usuario'];
                $usu = trim($_REQUEST['user']);
                $pass = $_REQUEST['pass']; // Puede estar vacío si no se cambia
                $est = isset($_REQUEST['est']) ? 1 : 0;
                
                //  OBTENER DATOS ANTES DE EDITAR
                $usuario_actual = ObtenerUsuario($id_usuario);
                $usu_anterior = $usuario_actual['usu_usuario'] ?? '';
                $est_anterior = $usuario_actual['est_usuario'] ?? 0;
                $roles_anteriores = ObtenerRolesUsuario($id_usuario);
                $rol_anterior_id = !empty($roles_anteriores) ? $roles_anteriores[0]['id_rol'] : 0;
                $rol_anterior_nom = !empty($roles_anteriores) ? $roles_anteriores[0]['nom_rol'] : '';
                
                // Obtener rol seleccionado (ahora es solo uno)
                $rol_seleccionado = isset($_REQUEST['rol_seleccionado']) ? $_REQUEST['rol_seleccionado'] : null;
                
                // Validar que se haya seleccionado un rol
                if (empty($rol_seleccionado)) {
                    //  AUDITORÍA: ERROR - SIN ROL SELECCIONADO
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'USUARIO', "ID: $id_usuario | Usuario: '$usu' | Sin rol seleccionado");
                ?>
                    <script Language="JavaScript">
                        alert('Error: Debe seleccionar un rol para el usuario.');
                        history.back();
                    </script>
                <?php
                    exit;
                }               

                // Validaciones adicionales del lado del servidor
                if (empty($usu)) {
                    //  AUDITORÍA: ERROR - NOMBRE DE USUARIO VACÍO
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'USUARIO', "ID: $id_usuario | Nombre de usuario vacío");
                ?>
                    <script Language="JavaScript">
                        alert('Error: El nombre de usuario es obligatorio.');
                        history.back();
                    </script>
                <?php
                    exit;
                }

                // Validar que el usuario no contenga espacios
                if (preg_match('/\s/', $usu)) {
                    //  AUDITORÍA: ERROR - USUARIO CON ESPACIOS
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'USUARIO', "ID: $id_usuario | Usuario: '$usu' | Contiene espacios");
                ?>
                    <script Language="JavaScript">
                        alert('Error: El nombre de usuario no puede contener espacios.');
                        history.back();
                    </script>
                <?php
                    exit;
                }

                // Si se está cambiando la contraseña, validar longitud mínima
                if (!empty($pass) && strlen($pass) < 6) {
                    //  AUDITORÍA: ERROR - CONTRASEÑA CORTA
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'USUARIO', "ID: $id_usuario | Usuario: '$usu' | Contraseña menor a 6 caracteres");
                ?>
                    <script Language="JavaScript">
                        alert('Error: La contraseña debe tener al menos 6 caracteres.');
                        history.back();
                    </script>
                <?php
                    exit;
                }

                // Convertir el rol único en un array para mantener compatibilidad con la función existente
                $roles = array($rol_seleccionado);

                //  EJECUTAR ACTUALIZACIÓN
                $rpta = EditarUsuario($id_usuario, $usu, $pass, $est, $roles);

                if ($rpta == "SI") {
                    //  COMPARAR Y CONSTRUIR DESCRIPCIÓN
                    $cambios = [];
                    
                    if ($usu_anterior != $usu) {
                        $cambios[] = "Usuario: '$usu_anterior' → '$usu'";
                    }
                    
                    if (!empty($pass)) {
                        $cambios[] = "Contraseña actualizada";
                    }
                    
                    if ($rol_anterior_id != $rol_seleccionado) {
                        $rol_nuevo_data = ObtenerRol($rol_seleccionado);
                        $rol_nuevo_nom = $rol_nuevo_data ? $rol_nuevo_data['nom_rol'] : '';
                        $cambios[] = "Rol: '$rol_anterior_nom' → '$rol_nuevo_nom'";
                    }
                    
                    if ($est_anterior != $est) {
                        $estado_ant = ($est_anterior == 1) ? 'Activo' : 'Inactivo';
                        $estado_nvo = ($est == 1) ? 'Activo' : 'Inactivo';
                        $cambios[] = "Estado: $estado_ant → $estado_nvo";
                    }
                    
                    if (empty($cambios)) {
                        $descripcion = "ID: $id_usuario | Usuario: '$usu' | Sin cambios";
                    } else {
                        $descripcion = "ID: $id_usuario | " . implode(' | ', $cambios);
                    }
                
                    //  AUDITORÍA: EDICIÓN EXITOSA
                    GrabarAuditoria($id, $usuario_sesion, 'EDITAR', 'USUARIO', $descripcion);
                ?>
                    <script Language="JavaScript">
                        location.href = 'usuario_mostrar.php?actualizado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                    //  AUDITORÍA: ERROR - YA EXISTE
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'USUARIO', "ID: $id_usuario | Usuario '$usu' ya existe");
                ?>
                    <script Language="JavaScript">
                        location.href = 'usuario_mostrar.php?error=true';
                    </script>
                <?php
                } else if ($rpta == "ERROR_SINCRONIZAR") {
                    // AUDITORÍA: ERROR - SINCRONIZACIÓN
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'USUARIO', "ID: $id_usuario | Error al sincronizar");
                ?>
                    <script Language="JavaScript">
                        alert('Error al sincronizar el personal de la base de Inspecciones. Intente nuevamente.');
                        history.back();
                    </script>
                <?php
                } else if ($rpta == "PERSONAL_NO_ENCONTRADO") {
                    //  AUDITORÍA: ERROR - PERSONAL NO ENCONTRADO
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'USUARIO', "ID: $id_usuario | Personal no encontrado");
                ?>
                    <script Language="JavaScript">
                        alert('Error: El personal asociado a este usuario no fue encontrado en ninguna base de datos.');
                        history.back();
                    </script>
                <?php
                } else {
                    //  AUDITORÍA: ERROR GENERAL
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'USUARIO', "ID: $id_usuario | Error del sistema");
                ?>
                    <script Language="JavaScript">
                        location.href = 'usuario_mostrar.php?error=true';
                    </script>
                <?php
                }
            }
            //-------------------------------------------

            // Obtener ID del usuario desde GET
            $id_usuario = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';
            if ($id_usuario == "") {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos del usuario a editar
            $usuario_data = ObtenerUsuario($id_usuario);
            if ($usuario_data) {
                $id_personal = $usuario_data['id_personal'];
                $nom_personal = $usuario_data['nom_personal'];
                $dni_personal = $usuario_data['dni_personal'];
                $nom_area = $usuario_data['nom_area'];
                $nom_cargo = $usuario_data['nom_cargo'];
                $usu = $usuario_data['usu_usuario'];
                $est = ($usuario_data['est_usuario'] == 1) ? "checked" : "";
                
                // Obtener roles actuales del usuario
                $roles_usuario = ObtenerRolesUsuario($id_usuario);
                
                // Verificar si el usuario a editar tiene rol SUPER ADMINISTRADOR
                $usuario_tiene_superadmin = false;
                foreach ($roles_usuario as $rol) {
                    if ($rol['id_rol'] == 1) {
                        $usuario_tiene_superadmin = true;
                        break;
                    }
                }
                
            } else {
                //  AUDITORÍA: USUARIO NO ENCONTRADO
                GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'USUARIO', "ID: $id_usuario | No encontrado");
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos para los selectores - filtrar SUPER ADMINISTRADOR si no es superadmin
            if ($es_superadmin) {
                $roles_activos = MostrarRolesActivos();
            } else {
                $roles_activos = MostrarRolesActivosFiltrados();
            }

            require_once("../_vista/v_usuario_editar.php");
            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>

    <?php require_once("../_vista/v_script.php"); ?>
</body>

</html>