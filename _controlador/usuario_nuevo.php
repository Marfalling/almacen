<?php

require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

if (!verificarPermisoEspecifico('crear_usuarios')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'USUARIO', 'CREAR');
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

    <title>Nuevo Usuario</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            if (isset($_REQUEST['registrar'])) {
                $id_personal = $_REQUEST['id_personal'];
                $usu = trim($_REQUEST['user']);
                $pass = $_REQUEST['pass'];
                $est = isset($_REQUEST['est']) ? 1 : 0;

                // Obtener rol seleccionado (ahora es solo uno)
                $rol_seleccionado = isset($_REQUEST['rol_seleccionado']) ? $_REQUEST['rol_seleccionado'] : null;
                
                // Validar que se haya seleccionado un rol
                if (empty($rol_seleccionado)) {
                    //  AUDITORÍA: ERROR - SIN ROL SELECCIONADO
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'USUARIO', "Usuario: '$usu' | Sin rol seleccionado");
            ?>
                    <script Language="JavaScript">
                        alert('Error: Debe seleccionar un rol para el usuario.');
                        history.back();
                    </script>
            <?php
                    exit;
                }

                // Convertir el rol único en un array para mantener compatibilidad con la función existente
                $roles = array($rol_seleccionado);

                // Validaciones adicionales del lado del servidor
                if (empty($id_personal)) {
                    //  AUDITORÍA: ERROR - SIN PERSONAL SELECCIONADO
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'USUARIO', "Usuario: '$usu' | Sin personal seleccionado");
            ?>
                    <script Language="JavaScript">
                        alert('Error: Debe seleccionar un personal.');
                        history.back();
                    </script>
            <?php
                    exit;
                }

                if (empty($usu)) {
                    //  AUDITORÍA: ERROR - NOMBRE DE USUARIO VACÍO
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'USUARIO', "Nombre de usuario vacío");
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
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'USUARIO', "Usuario: '$usu' | Contiene espacios");
            ?>
                    <script Language="JavaScript">
                        alert('Error: El nombre de usuario no puede contener espacios.');
                        history.back();
                    </script>
            <?php
                    exit;
                }

                if (empty($pass)) {
                    //  AUDITORÍA: ERROR - CONTRASEÑA VACÍA
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'USUARIO', "Usuario: '$usu' | Contraseña vacía");
            ?>
                    <script Language="JavaScript">
                        alert('Error: La contraseña es obligatoria.');
                        history.back();
                    </script>
            <?php
                    exit;
                }

                // Validar longitud mínima de contraseña
                if (strlen($pass) < 6) {
                    //  AUDITORÍA: ERROR - CONTRASEÑA CORTA
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'USUARIO', "Usuario: '$usu' | Contraseña menor a 6 caracteres");
            ?>
                    <script Language="JavaScript">
                        alert('Error: La contraseña debe tener al menos 6 caracteres.');
                        history.back();
                    </script>
            <?php
                    exit;
                }

                $rpta = GrabarUsuario($id_personal, $usu, $pass, $est, $roles);

                if ($rpta == "SI") {
                    //  OBTENER NOMBRES PARA AUDITORÍA
                    require_once("../_modelo/m_personal.php");
                    $personal_data = ObtenerPersonal($id_personal);
                    $rol_data = ObtenerRol($rol_seleccionado);
                    
                    $nom_personal = $personal_data ? $personal_data['nom_personal'] : '';
                    $nom_rol = $rol_data ? $rol_data['nom_rol'] : '';
                    $estado_texto = ($est == 1) ? 'Activo' : 'Inactivo';
                    
                    //  AUDITORÍA: REGISTRO EXITOSO
                    $descripcion = "Usuario: '$usu' | Personal: '$nom_personal' | Rol: '$nom_rol' | Estado: $estado_texto";
                    GrabarAuditoria($id, $usuario_sesion, 'REGISTRAR', 'USUARIO', $descripcion);
                ?>
                    <script Language="JavaScript">
                        location.href = 'usuario_mostrar.php?registrado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                    //  AUDITORÍA: ERROR - YA EXISTE
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'USUARIO', "Usuario: '$usu' - Ya existe");
                ?>
                    <script Language="JavaScript">
                        location.href = 'usuario_mostrar.php?existe=true';
                    </script>
                <?php
                } else if ($rpta == "PERSONAL_YA_ASIGNADO") {
                    //  AUDITORÍA: ERROR - PERSONAL YA TIENE USUARIO
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'USUARIO', "Usuario: '$usu' | Personal ya tiene usuario asignado");
                ?>
                    <script Language="JavaScript">
                        location.href = 'usuario_mostrar.php?personal_asignado=true';
                    </script>
                <?php
                } else if ($rpta == "ERROR_SINCRONIZAR") {
                    //  AUDITORÍA: ERROR - SINCRONIZACIÓN
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'USUARIO', "Usuario: '$usu' | Error al sincronizar");
                ?>
                    <script Language="JavaScript">
                        alert('Error al sincronizar el personal de la base de Inspecciones. Intente nuevamente.');
                        history.back();
                    </script>
                <?php
                } else if ($rpta == "PERSONAL_NO_ENCONTRADO") {
                    //  AUDITORÍA: ERROR - PERSONAL NO ENCONTRADO
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'USUARIO', "Usuario: '$usu' | Personal no encontrado");
                ?>
                    <script Language="JavaScript">
                        alert('Error: El personal seleccionado no fue encontrado en ninguna base de datos.');
                        history.back();
                    </script>
                <?php
                } else {
                    //  AUDITORÍA: ERROR GENERAL
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'USUARIO', "Usuario: '$usu' | Error del sistema");
                ?>
                    <script Language="JavaScript">
                        location.href = 'usuario_mostrar.php?error=true';
                    </script>
                <?php
                }
            }

            // Obtener datos para los selectores
            $personal_sin_usuario = ObtenerPersonalSinUsuario();
            
            // Obtener roles activos, filtrando SUPER ADMINISTRADOR si no es superadmin
            if ($es_superadmin) {
                $roles_activos = MostrarRolesActivos();
            } else {
                $roles_activos = MostrarRolesActivosFiltrados();
            }

            require_once("../_vista/v_usuario_nuevo.php");
            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>

    <?php
    require_once("../_vista/v_script.php");
    require_once("../_vista/v_alertas.php");
    ?>
</body>
</html>