<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

if (!verificarPermisoEspecifico('editar_rol de usuario')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'ROL_USUARIO', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

// Verificar si el usuario actual es SUPERADMIN
$es_superadmin = esSuperAdmin($id);

//=======================================================================
// CONTROLADOR: rol_usuario_editar.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Editar Rol</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_rol.php");

            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $id_rol = $_REQUEST['id_rol'];
                $nom_rol = strtoupper(trim($_REQUEST['nom_rol']));
                $est = isset($_REQUEST['est']) ? 1 : 0;
                
                // Obtener permisos seleccionados
                $permisos = array();
                if (isset($_REQUEST['permisos']) && is_array($_REQUEST['permisos'])) {
                    $permisos = $_REQUEST['permisos'];
                }
                
                // Validar que se hayan seleccionado permisos
                if (empty($permisos)) {
                    //  AUDITORÍA: ERROR - SIN PERMISOS
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'ROL_USUARIO', "ID: $id_rol | Nombre: '$nom_rol' - Sin permisos seleccionados");
                ?>
                    <script Language="JavaScript">
                        location.href = 'rol_usuario_editar.php?id_rol=<?php echo $id_rol; ?>&sin_permisos=true';
                    </script>
                <?php
                    exit();
                }

                //  OBTENER DATOS ANTES DE EDITAR
                $datos_antes = ObtenerRol($id_rol);
                $nom_anterior = $datos_antes['nom_rol'] ?? '';
                $est_anterior = $datos_antes['est_rol'] ?? 0;
                
                // Contar permisos antes
                $permisos_antes = $datos_antes['permisos'] ?? [];
                $cantidad_permisos_antes = count($permisos_antes);

                //  EJECUTAR EDICIÓN
                $rpta = EditarRol($id_rol, $nom_rol, $permisos, $est);

                if ($rpta == "SI") {
                    //  COMPARAR Y CONSTRUIR DESCRIPCIÓN
                    $cambios = [];
                    
                    if ($nom_anterior != $nom_rol) {
                        $cambios[] = "Nombre: '$nom_anterior' → '$nom_rol'";
                    }
                    if ($est_anterior != $est) {
                        $estado_ant = ($est_anterior == 1) ? 'Activo' : 'Inactivo';
                        $estado_nvo = ($est == 1) ? 'Activo' : 'Inactivo';
                        $cambios[] = "Estado: $estado_ant → $estado_nvo";
                    }
                    
                    $cantidad_permisos_nuevos = count($permisos);
                    if ($cantidad_permisos_antes != $cantidad_permisos_nuevos) {
                        $cambios[] = "Permisos: $cantidad_permisos_antes → $cantidad_permisos_nuevos";
                    }
                    
                    if (empty($cambios)) {
                        $descripcion = "ID: $id_rol | Sin cambios";
                    } else {
                        $descripcion = "ID: $id_rol | " . implode(' | ', $cambios);
                    }
                    
                    //  AUDITORÍA: EDICIÓN EXITOSA
                    GrabarAuditoria($id, $usuario_sesion, 'EDITAR', 'ROL_USUARIO', $descripcion);
                ?>
                    <script Language="JavaScript">
                        location.href = 'rol_usuario_mostrar.php?actualizado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                    //  AUDITORÍA: ERROR - YA EXISTE
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'ROL_USUARIO', "ID: $id_rol | Nombre '$nom_rol' ya existe");
                ?>
                    <script Language="JavaScript">
                        location.href = 'rol_usuario_mostrar.php?existe=true';
                    </script>
                <?php
                } else {
                    //  AUDITORÍA: ERROR GENERAL
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'ROL_USUARIO', "ID: $id_rol | Error del sistema");
                ?>
                    <script Language="JavaScript">
                        location.href = 'rol_usuario_mostrar.php?error=true';
                    </script>
                <?php
                }
            }
            //-------------------------------------------

            // Obtener ID del rol desde GET
            $id_rol = isset($_GET['id_rol']) ? $_GET['id_rol'] : '';
            if ($id_rol == "") {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos del rol a editar
            $rol_data = ObtenerRol($id_rol);
            if ($rol_data) {
                $nom_rol = $rol_data['nom_rol'];
                $est = ($rol_data['est_rol'] == 1) ? "checked" : "";
                $permisos_rol = $rol_data['permisos'];
                
                // Crear array de permisos para facilitar la verificación
                $permisos_asignados = array();
                foreach ($permisos_rol as $permiso) {
                    $permisos_asignados[] = $permiso['id_modulo_accion'];
                }
            } else {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }
            
            // Obtener módulos y acciones para los permisos - pasar $es_superadmin
            $modulos_acciones = MostrarModulosAcciones($es_superadmin);

            require_once("../_vista/v_rol_usuario_editar.php");
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