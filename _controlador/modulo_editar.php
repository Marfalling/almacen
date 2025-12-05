<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

if (!verificarPermisoEspecifico('editar_modulos')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'MODULOS', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}
//=======================================================================
// CONTROLADOR: modulo_editar.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Editar Módulo</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_modulo.php");

            // Obtener las acciones disponibles para mostrar en el formulario
            $acciones_disponibles = MostrarAcciones();

            //-------------------------------------------
            // OPERACIÓN DE EDICIÓN
            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $id_modulo = $_REQUEST['id_modulo'];
                
                //  OBTENER DATOS ANTES DE EDITAR
                $modulo_antes = ObtenerModulo($id_modulo);
                $nom_anterior = $modulo_antes['nom_modulo'];
                $est_anterior = $modulo_antes['est_modulo'];
                $acciones_anteriores = ObtenerAccionesModulo($id_modulo);
                
                // Obtener nuevos valores
                $nom_nuevo = strtoupper(trim($_REQUEST['nom_modulo']));
                $est_nuevo = isset($_REQUEST['est']) ? 1 : 0;
                $acciones_nuevas = isset($_REQUEST['acciones']) ? $_REQUEST['acciones'] : array();

                // Validar que se hayan seleccionado acciones
                if (empty($acciones_nuevas)) {
                ?>
                    <script Language="JavaScript">
                        location.href = 'modulo_editar.php?id_modulo=<?php echo $id_modulo; ?>&sin_acciones=true';
                    </script>
                <?php
                } else {
                    $rpta = ActualizarModuloCompleto($id_modulo, $nom_nuevo, $acciones_nuevas, $est_nuevo);

                    if ($rpta == "SI") {
                        //  CONSTRUIR DESCRIPCIÓN CON CAMBIOS
                        $cambios = [];
                        
                        if ($nom_anterior != $nom_nuevo) {
                            $cambios[] = "Nombre: '$nom_anterior' → '$nom_nuevo'";
                        }
                        
                        if ($est_anterior != $est_nuevo) {
                            $estado_ant = ($est_anterior == 1) ? 'Activo' : 'Inactivo';
                            $estado_nue = ($est_nuevo == 1) ? 'Activo' : 'Inactivo';
                            $cambios[] = "Estado: $estado_ant → $estado_nue";
                        }
                        
                        // Comparar acciones
                        $ids_anteriores = array_column($acciones_anteriores, 'id_accion');
                        sort($ids_anteriores);
                        sort($acciones_nuevas);
                        
                        if ($ids_anteriores != $acciones_nuevas) {
                            $cambios[] = "Acciones: " . count($ids_anteriores) . " → " . count($acciones_nuevas) . " permisos";
                        }
                        
                        if (count($cambios) == 0) {
                            $descripcion = "ID: $id_modulo | Sin cambios";
                        } else {
                            $descripcion = "ID: $id_modulo | " . implode(' | ', $cambios);
                        }
                        
                        GrabarAuditoria($id, $usuario_sesion, 'EDITAR', 'MODULO', $descripcion);
                ?>
                        <script Language="JavaScript">
                            location.href = 'modulo_mostrar.php?actualizado=true';
                        </script>
                    <?php
                        exit;
                    } else if ($rpta == "NO") {
                        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'MODULO', "ID: $id_modulo - Módulo ya existe");
                    ?>
                        <script Language="JavaScript">
                            location.href = 'modulo_mostrar.php?existe=true';
                        </script>
                    <?php
                        exit;
                    } else {
                        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'MODULO', "ID: $id_modulo");
                    ?>
                        <script Language="JavaScript">
                            location.href = 'modulo_mostrar.php?error=true';
                        </script>
                <?php
                        exit;
                    }
                }
            }
            //-------------------------------------------

            // Obtener ID del módulo desde GET
            $id_modulo = isset($_GET['id_modulo']) ? $_GET['id_modulo'] : '';
            if ($id_modulo == "") {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos del módulo a editar
            $modulo_data = ObtenerModulo($id_modulo);
            if ($modulo_data) {
                $nom_modulo = $modulo_data['nom_modulo'];
                $est = ($modulo_data['est_modulo'] == 1) ? "checked" : "";
                
                // Obtener las acciones actualmente asignadas al módulo
                $acciones_modulo = ObtenerAccionesModulo($id_modulo);
            } else {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            require_once("../_vista/v_modulo_editar.php");
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