<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

if (!verificarPermisoEspecifico('editar_moneda')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'MONEDA', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

//=======================================================================
// CONTROLADOR: moneda_editar.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Moneda</title>
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");
            require_once("../_modelo/m_moneda.php");

            //-------------------------------------------
            // OPERACIÓN DE EDICIÓN
            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $id_moneda = $_REQUEST['id_moneda'];
                
                //  OBTENER DATOS ANTES DE EDITAR
                $moneda_antes = ObtenerMoneda($id_moneda);
                $nom_anterior = $moneda_antes['nom_moneda'];
                $est_anterior = $moneda_antes['est_moneda'];
                
                // Obtener nuevos valores
                $nom_nuevo = strtoupper($_REQUEST['nom']);
                $est_nuevo = isset($_REQUEST['est']) ? 1 : 0;

                $rpta = EditarMoneda($id_moneda, $nom_nuevo, $est_nuevo);

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
                    
                    if (count($cambios) == 0) {
                        $descripcion = "ID: $id_moneda | Sin cambios";
                    } else {
                        $descripcion = "ID: $id_moneda | " . implode(' | ', $cambios);
                    }
                    
                    GrabarAuditoria($id, $usuario_sesion, 'EDITAR', 'MONEDA', $descripcion);
                ?>
                    <script Language="JavaScript">
                        location.href = 'moneda_mostrar.php?actualizado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'MONEDA', "ID: $id_moneda - Moneda ya existe");
                ?>
                    <script Language="JavaScript">
                        location.href = 'moneda_mostrar.php?error=true';
                    </script>
                <?php
                }
                exit;
            }
            //-------------------------------------------

            // Obtener ID de la moneda desde GET
            $id_moneda = isset($_GET['id_moneda']) ? $_GET['id_moneda'] : '';
            if ($id_moneda == "") {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos de la moneda a editar
            $moneda_data = ObtenerMoneda($id_moneda);
            if ($moneda_data) {
                $nom = $moneda_data['nom_moneda'];
                $est = ($moneda_data['est_moneda'] == 1) ? "checked" : "";
            } else {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            require_once("../_vista/v_moneda_editar.php");
            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>

    <?php require_once("../_vista/v_script.php"); ?>
</body>

</html>