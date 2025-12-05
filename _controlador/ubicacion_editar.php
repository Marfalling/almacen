<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

if (!verificarPermisoEspecifico('editar_ubicacion')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'UBICACION', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

//=======================================================================
// CONTROLADOR: ubicacion_editar.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Editar Ubicación</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_ubicacion.php");

            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $id_ubicacion = $_REQUEST['id_ubicacion'];
                $nom = strtoupper($_REQUEST['nom']);
                $est = isset($_REQUEST['est']) ? 1 : 0;

                // OBTENER DATOS ANTES DE EDITAR
                $ubicacion_actual = ObtenerUbicacion($id_ubicacion);
                $nom_anterior = $ubicacion_actual['nom_ubicacion'] ?? '';
                $est_anterior = $ubicacion_actual['est_ubicacion'] ?? 0;

                //  EJECUTAR ACTUALIZACIÓN
                $rpta = ActualizarUbicacion($id_ubicacion, $nom, $est);

                if ($rpta == "SI") {
                    //  COMPARAR Y CONSTRUIR DESCRIPCIÓN
                    $cambios = [];
                    
                    if ($nom_anterior != $nom) {
                        $cambios[] = "Nombre: '$nom_anterior' → '$nom'";
                    }
                    
                    if ($est_anterior != $est) {
                        $estado_ant = ($est_anterior == 1) ? 'Activo' : 'Inactivo';
                        $estado_nvo = ($est == 1) ? 'Activo' : 'Inactivo';
                        $cambios[] = "Estado: $estado_ant → $estado_nvo";
                    }
                    
                    if (empty($cambios)) {
                        $descripcion = "ID: $id_ubicacion | Sin cambios";
                    } else {
                        $descripcion = "ID: $id_ubicacion | " . implode(' | ', $cambios);
                    }
                    
                    // AUDITORÍA: EDICIÓN EXITOSA
                    GrabarAuditoria($id, $usuario_sesion, 'EDITAR', 'UBICACION', $descripcion);
                ?>
                    <script Language="JavaScript">
                        location.href = 'ubicacion_mostrar.php?actualizado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                    //  AUDITORÍA: ERROR - YA EXISTE
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'UBICACION', "ID: $id_ubicacion | Nombre '$nom' ya existe");
                ?>
                    <script Language="JavaScript">
                        location.href = 'ubicacion_mostrar.php?existe=true';
                    </script>
                <?php
                } else {
                    // AUDITORÍA: ERROR GENERAL
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'UBICACION', "ID: $id_ubicacion | Error del sistema");
                ?>
                    <script Language="JavaScript">
                        location.href = 'ubicacion_mostrar.php?error=true';
                    </script>
                <?php
                }
            }
            //-------------------------------------------

            // Obtener ID de la ubicación desde GET
            $id_ubicacion = isset($_GET['id_ubicacion']) ? $_GET['id_ubicacion'] : '';
            if ($id_ubicacion == "") {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos de la ubicación a editar
            $ubicacion_data = ObtenerUbicacion($id_ubicacion);
            if ($ubicacion_data) {
                $nom = $ubicacion_data['nom_ubicacion'];
                $est = ($ubicacion_data['est_ubicacion'] == 1) ? "checked" : "";
            } else {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            require_once("../_vista/v_ubicacion_editar.php");
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