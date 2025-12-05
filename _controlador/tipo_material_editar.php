<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

if (!verificarPermisoEspecifico('editar_tipo de material')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'TIPO_MATERIAL', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}
//=======================================================================
// CONTROLADOR: tipo_material_editar.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Editar Tipo de Material</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_tipo_material.php");

            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $id_material_tipo = $_REQUEST['id_material_tipo'];
                $nom = strtoupper($_REQUEST['nom']);
                $est = isset($_REQUEST['est']) ? 1 : 0;

                //  OBTENER DATOS ANTES DE EDITAR
                $material_actual = ObtenerMaterialTipo($id_material_tipo);
                $nom_anterior = $material_actual['nom_material_tipo'] ?? '';
                $est_anterior = $material_actual['est_material_tipo'] ?? 0;

                //  EJECUTAR ACTUALIZACIÓN
                $rpta = ActualizarMaterialTipo($id_material_tipo, $nom, $est);

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
                        $descripcion = "ID: $id_material_tipo | Sin cambios";
                    } else {
                        $descripcion = "ID: $id_material_tipo | " . implode(' | ', $cambios);
                    }
                    
                    //  AUDITORÍA: EDICIÓN EXITOSA
                    GrabarAuditoria($id, $usuario_sesion, 'EDITAR', 'TIPO_MATERIAL', $descripcion);
                ?>
                    <script Language="JavaScript">
                        location.href = 'tipo_material_mostrar.php?actualizado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                    //  AUDITORÍA: ERROR - YA EXISTE
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'TIPO_MATERIAL', "ID: $id_material_tipo | Nombre '$nom' ya existe");
                ?>
                    <script Language="JavaScript">
                        location.href = 'tipo_material_mostrar.php?existe=true';
                    </script>
                <?php
                } else {
                    //  AUDITORÍA: ERROR GENERAL
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'TIPO_MATERIAL', "ID: $id_material_tipo | Error del sistema");
                ?>
                    <script Language="JavaScript">
                        location.href = 'tipo_material_mostrar.php?error=true';
                    </script>
                <?php
                }
            }
            //-------------------------------------------

            // Obtener ID del tipo de material desde GET
            $id_material_tipo = isset($_GET['id_material_tipo']) ? $_GET['id_material_tipo'] : '';
            if ($id_material_tipo == "") {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos del tipo de material a editar
            $tipo_material_data = ObtenerMaterialTipo($id_material_tipo);
            if ($tipo_material_data) {
                $nom = $tipo_material_data['nom_material_tipo'];
                $est = ($tipo_material_data['est_material_tipo'] == 1) ? "checked" : "";
            } else {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            require_once("../_vista/v_tipo_material_editar.php");
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