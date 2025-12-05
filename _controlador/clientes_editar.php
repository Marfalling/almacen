<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

if (!verificarPermisoEspecifico('editar_cliente')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'CLIENTE', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Cliente</title>
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");
            require_once("../_modelo/m_clientes.php");

            //-------------------------------------------
            // OPERACIÓN DE EDICIÓN
            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $id_cliente = $_REQUEST['id_cliente'];
                
                //  OBTENER DATOS ANTES DE EDITAR
                $cliente_antes = ObtenerCliente($id_cliente);
                $nom_anterior = $cliente_antes['nom_cliente'];
                $est_anterior = $cliente_antes['act_cliente'];
                
                // Obtener nuevos valores
                $nom_nuevo = strtoupper(trim($_REQUEST['nom']));
                $est_nuevo = isset($_REQUEST['est']) ? 1 : 0;

                $rpta = EditarCliente($id_cliente, $nom_nuevo, $est_nuevo);

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
                        $descripcion = "ID: $id_cliente | Sin cambios";
                    } else {
                        $descripcion = "ID: $id_cliente | " . implode(' | ', $cambios);
                    }
                    
                    GrabarAuditoria($id, $usuario_sesion, 'EDITAR', 'CLIENTE', $descripcion);
                ?>
                    <script Language="JavaScript">
                        location.href = 'clientes_mostrar.php?actualizado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'CLIENTE', "ID: $id_cliente - Cliente ya existe");
                ?>
                    <script Language="JavaScript">
                        location.href = 'clientes_mostrar.php?error=true';
                    </script>
                <?php
                }
                exit;
            }
            //-------------------------------------------

            // Obtener ID del cliente desde GET
            $id_cliente = isset($_GET['id_cliente']) ? $_GET['id_cliente'] : '';
            if ($id_cliente == "") {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos del cliente a editar
            $cliente_data = ObtenerCliente($id_cliente);
            if ($cliente_data) {
                $nom = $cliente_data['nom_cliente'];
                $est = ($cliente_data['act_cliente'] == 1) ? "checked" : "";
            } else {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            require_once("../_vista/v_clientes_editar.php");
            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>
    <?php require_once("../_vista/v_script.php"); ?>
</body>
</html>