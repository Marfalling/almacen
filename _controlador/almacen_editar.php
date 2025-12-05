<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

if (!verificarPermisoEspecifico('editar_almacen')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'ALMACEN', 'EDITAR');
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
    <title>Editar Almacén</title>
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");
            require_once("../_modelo/m_almacen.php");
            require_once("../_modelo/m_clientes.php");
            require_once("../_modelo/m_obras.php");

            //-------------------------------------------
            // OPERACIÓN DE EDICIÓN
            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $id_almacen = $_REQUEST['id_almacen'];
                
                //  OBTENER DATOS ANTES DE EDITAR
                $almacen_antes = ConsultarAlmacen($id_almacen);
                if (count($almacen_antes) > 0) {
                    $datos_antes = $almacen_antes[0];
                    $id_cliente_anterior = $datos_antes['id_cliente'];
                    $id_obra_anterior = $datos_antes['id_obra'];
                    $nom_anterior = $datos_antes['nom_almacen'];
                    $est_anterior = $datos_antes['est_almacen'];
                    
                    // Obtener nombres para la descripción
                    $cliente_anterior_data = ObtenerCliente($id_cliente_anterior);
                    $obra_anterior_data = ObtenerObra($id_obra_anterior);
                    $nom_cliente_anterior = $cliente_anterior_data ? $cliente_anterior_data['nom_cliente'] : 'N/A';
                    $nom_obra_anterior = $obra_anterior_data ? $obra_anterior_data['nom_obra'] : 'N/A';
                }
                
                // Obtener nuevos valores
                $id_cliente_nuevo = $_REQUEST['id_cliente'];
                $id_obra_nuevo = $_REQUEST['id_obra'];
                $nom_nuevo = strtoupper($_REQUEST['nom']);
                $est_nuevo = isset($_REQUEST['est']) ? 1 : 0;
                
                // Obtener nombres nuevos para la descripción
                $cliente_nuevo_data = ObtenerCliente($id_cliente_nuevo);
                $obra_nuevo_data = ObtenerObra($id_obra_nuevo);
                $nom_cliente_nuevo = $cliente_nuevo_data ? $cliente_nuevo_data['nom_cliente'] : 'N/A';
                $nom_obra_nuevo = $obra_nuevo_data ? $obra_nuevo_data['nom_obra'] : 'N/A';

                $rpta = ActualizarAlmacen($id_almacen, $id_cliente_nuevo, $id_obra_nuevo, $nom_nuevo, $est_nuevo);

                if ($rpta == "SI") {
                    //  CONSTRUIR DESCRIPCIÓN CON CAMBIOS
                    $cambios = [];
                    
                    if ($nom_anterior != $nom_nuevo) {
                        $cambios[] = "Nombre: '$nom_anterior' → '$nom_nuevo'";
                    }
                    
                    if ($id_cliente_anterior != $id_cliente_nuevo) {
                        $cambios[] = "Cliente: '$nom_cliente_anterior' → '$nom_cliente_nuevo'";
                    }
                    
                    if ($id_obra_anterior != $id_obra_nuevo) {
                        $cambios[] = "Obra: '$nom_obra_anterior' → '$nom_obra_nuevo'";
                    }
                    
                    if ($est_anterior != $est_nuevo) {
                        $estado_ant = ($est_anterior == 1) ? 'Activo' : 'Inactivo';
                        $estado_nue = ($est_nuevo == 1) ? 'Activo' : 'Inactivo';
                        $cambios[] = "Estado: $estado_ant → $estado_nue";
                    }
                    
                    if (count($cambios) == 0) {
                        $descripcion = "ID: $id_almacen | Sin cambios";
                    } else {
                        $descripcion = "ID: $id_almacen | " . implode(' | ', $cambios);
                    }
                    
                    GrabarAuditoria($id, $usuario_sesion, 'EDITAR', 'ALMACEN', $descripcion);
                ?>
                    <script Language="JavaScript">
                        location.href = 'almacen_mostrar.php?actualizado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'ALMACEN', "ID: $id_almacen - Almacén ya existe");
                ?>
                    <script Language="JavaScript">
                        location.href = 'almacen_mostrar.php?error=true';
                    </script>
                <?php
                }
                exit;
            }
            //-------------------------------------------

            // Obtener ID del almacén desde GET
            $id_almacen = isset($_GET['id_almacen']) ? $_GET['id_almacen'] : '';
            if ($id_almacen == "") {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos del almacén a editar
            $almacen = ConsultarAlmacen($id_almacen);
            if (count($almacen) > 0) {
                foreach ($almacen as $value) {
                    $id_cliente = $value['id_cliente'];
                    $id_obra = $value['id_obra'];
                    $nom = $value['nom_almacen'];
                    $est = ($value['est_almacen'] == 1) ? "checked" : "";
                }
            } else {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener listas para los selects (solo activos)
            $listaClientes = MostrarClientesActivos();
            $listaObras = MostrarObras();

            require_once("../_vista/v_almacen_editar.php");
            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>
    <?php require_once("../_vista/v_script.php"); ?>
</body>
</html>