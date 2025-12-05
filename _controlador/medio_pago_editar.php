<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

if (!verificarPermisoEspecifico('editar_medio de pago')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'MEDIO DE PAGO', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

//=======================================================================
// CONTROLADOR: medio_pago_editar.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Editar Medio Pago</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_medio_pago.php");

            //-------------------------------------------
            // OPERACIÓN DE EDICIÓN
            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $id_medio_pago = $_REQUEST['id_medio_pago'];
                
                //  OBTENER DATOS ANTES DE EDITAR
                $medio_pago_antes = ObtenerMedioPago($id_medio_pago);
                $nom_anterior = $medio_pago_antes['nom_medio_pago'];
                $est_anterior = $medio_pago_antes['est_medio_pago'];
                
                // Obtener nuevos valores
                $nom_nuevo = strtoupper($_REQUEST['nom']);
                $est_nuevo = isset($_REQUEST['est']) ? 1 : 0;

                $rpta = EditarMedioPago($id_medio_pago, $nom_nuevo, $est_nuevo);

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
                        $descripcion = "ID: $id_medio_pago | Sin cambios";
                    } else {
                        $descripcion = "ID: $id_medio_pago | " . implode(' | ', $cambios);
                    }
                    
                    GrabarAuditoria($id, $usuario_sesion, 'EDITAR', 'MEDIO DE PAGO', $descripcion);
                ?>
                    <script Language="JavaScript">
                        location.href = 'medio_pago_mostrar.php?actualizado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'MEDIO DE PAGO', "ID: $id_medio_pago - Medio de pago ya existe");
                ?>
                    <script Language="JavaScript">
                        location.href = 'medio_pago_mostrar.php?error=true';
                    </script>
                <?php
                }
                exit;
            }
            //-------------------------------------------

            // Obtener ID del medio de pago desde GET
            $id_medio_pago = isset($_GET['id_medio_pago']) ? $_GET['id_medio_pago'] : '';
            if ($id_medio_pago == "") {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos del medio de pago a editar
            $medio_pago_data = ObtenerMedioPago($id_medio_pago);
            if ($medio_pago_data) {
                $nom = $medio_pago_data['nom_medio_pago'];
                $est = ($medio_pago_data['est_medio_pago'] == 1) ? "checked" : "";
            } else {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            require_once("../_vista/v_medio_pago_editar.php");
            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>

    <?php require_once("../_vista/v_script.php"); ?>
</body>

</html>