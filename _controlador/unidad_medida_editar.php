<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

if (!verificarPermisoEspecifico('editar_unidad de medida')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'UNIDAD_MEDIDA', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}
//=======================================================================
// CONTROLADOR: unidad_medida_editar.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Editar Unidad de Medida</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_unidad_medida.php");

            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $id_unidad_medida = $_REQUEST['id_unidad_medida'];
                $nom = strtoupper($_REQUEST['nom']);
                $est = isset($_REQUEST['est']) ? 1 : 0;

                //  OBTENER DATOS ANTES DE EDITAR
                $unidad_medida_actual = ObtenerUnidadMedida($id_unidad_medida);
                $nom_anterior = $unidad_medida_actual['nom_unidad_medida'] ?? '';
                $est_anterior = $unidad_medida_actual['est_unidad_medida'] ?? 0;

                //  EJECUTAR ACTUALIZACIÓN
                $rpta = ActualizarUnidadMedida($id_unidad_medida, $nom, $est);

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
                        $descripcion = "ID: $id_unidad_medida | Sin cambios";
                    } else {
                        $descripcion = "ID: $id_unidad_medida | " . implode(' | ', $cambios);
                    }
                    
                    // AUDITORÍA: EDICIÓN EXITOSA
                    GrabarAuditoria($id, $usuario_sesion, 'EDITAR', 'UNIDAD_MEDIDA', $descripcion);
                ?>
                    <script Language="JavaScript">
                        location.href = 'unidad_medida_mostrar.php?actualizado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                    // AUDITORÍA: ERROR - YA EXISTE
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'UNIDAD_MEDIDA', "ID: $id_unidad_medida | Nombre '$nom' ya existe");
                ?>
                    <script Language="JavaScript">
                        location.href = 'unidad_medida_mostrar.php?existe=true';
                    </script>
                <?php
                } else {
                    // AUDITORÍA: ERROR GENERAL
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'UNIDAD_MEDIDA', "ID: $id_unidad_medida | Error del sistema");
                ?>
                    <script Language="JavaScript">
                        location.href = 'unidad_medida_mostrar.php?error=true';
                    </script>
                <?php
                }
            }
            //-------------------------------------------

            // Obtener ID de la unidad de medida desde GET
            $id_unidad_medida = isset($_GET['id_unidad_medida']) ? $_GET['id_unidad_medida'] : '';
            if ($id_unidad_medida == "") {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos de la unidad de medida a editar
            $unidad_medida_data = ObtenerUnidadMedida($id_unidad_medida);
            if ($unidad_medida_data) {
                $nom = $unidad_medida_data['nom_unidad_medida'];
                $est = ($unidad_medida_data['est_unidad_medida'] == 1) ? "checked" : "";
            } else {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            require_once("../_vista/v_unidad_medida_editar.php");
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