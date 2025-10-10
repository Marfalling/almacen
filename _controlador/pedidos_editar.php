<?php
//=======================================================================
// PEDIDOS - EDITAR (pedidos_editar.php)
//=======================================================================
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('editar_pedidos')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'PEDIDOS', 'EDITAR');
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

    <title>Editar Pedido</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_pedidos.php");
            require_once("../_modelo/m_almacen.php");
            require_once("../_modelo/m_unidad_medida.php");
            require_once("../_modelo/m_tipo_producto.php");
            require_once("../_modelo/m_tipo_material.php");
            require_once("../_modelo/m_ubicacion.php");
            require_once("../_modelo/m_centro_costo.php"); // AGREGADO: Modelo de centro de costo
            
            // Cargar datos necesarios para el formulario
            $unidades_medida = MostrarUnidadMedidaActiva();
            $producto_tipos = MostrarProductoTipoActivos();
            $material_tipos = MostrarMaterialTipoActivos();
            $ubicaciones = MostrarUbicacionesActivas(); 
            $centros_costo = MostrarCentrosCostoActivos(); // AGREGADO: Cargar centros de costo
            
            // Crear directorio de archivos si no existe
            if (!file_exists("../_archivos/pedidos/")) {
                mkdir("../_archivos/pedidos/", 0777, true);
            }

            $id_pedido = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

            //=======================================================================
            // CONTROLADOR ACTUALIZADO - SST COMO CAMPO ÚNICO
            //=======================================================================
            if (isset($_REQUEST['actualizar'])) {
                $id_ubicacion = intval($_REQUEST['id_ubicacion']); // AGREGADO: Recibir ubicación
                $id_centro_costo = intval($_REQUEST['id_centro_costo']); 
                $nom_pedido = strtoupper($_REQUEST['nom_pedido']);
                $fecha_necesidad = $_REQUEST['fecha_necesidad'];
                $num_ot = strtoupper($_REQUEST['num_ot']);
                $contacto = $_REQUEST['contacto'];
                $lugar_entrega = strtoupper($_REQUEST['lugar_entrega']);
                $aclaraciones = strtoupper($_REQUEST['aclaraciones']);
                
                // Procesar materiales - CORREGIDO: SST como campo único
                $materiales = array();
                if (isset($_REQUEST['descripcion']) && is_array($_REQUEST['descripcion'])) {
                    for ($i = 0; $i < count($_REQUEST['descripcion']); $i++) {
                        
                        $sst_descripcion = trim($_REQUEST['sst'][$i]);

                        $materiales[] = array(
                            'id_producto' => $_REQUEST['id_material'][$i],
                            'descripcion' => $_REQUEST['descripcion'][$i],
                            'cantidad' => $_REQUEST['cantidad'][$i],
                            'unidad' => $_REQUEST['unidad'][$i],
                            'observaciones' => $_REQUEST['observaciones'][$i],
                            'sst_descripcion' => $sst_descripcion,  
                            'id_detalle' => $_REQUEST['id_detalle'][$i]
                        );
                    }
                }

                // Procesar archivos
                $archivos_subidos = array();
                foreach ($_FILES as $key => $file) {
                    if (strpos($key, 'archivos_') === 0 && !empty($file['name'][0])) {
                        $index = str_replace('archivos_', '', $key);
                        $archivos_subidos[$index] = $file;
                    }
                }

                // LLAMADA ACTUALIZADA con ubicación
                $rpta = ActualizarPedido($id_pedido, $id_ubicacion, $id_centro_costo, $nom_pedido, $fecha_necesidad, 
                           $num_ot, $contacto, $lugar_entrega, 
                           $aclaraciones, $materiales, $archivos_subidos);

                if ($rpta == "SI") {
            ?>
                    <script Language="JavaScript">
                        location.href = 'pedidos_mostrar.php?actualizado=true';
                    </script>
                <?php
                } else {
                ?>
                    <script Language="JavaScript">
                        alert('Error al actualizar el pedido: <?php echo $rpta; ?>');
                    </script>
            <?php
                }
            }
            //-------------------------------------------

            if ($id_pedido > 0) {
                // Cargar datos del pedido
                $pedido_data = ConsultarPedido($id_pedido);
                foreach ($pedido_detalle as &$detalle) {
                    $id_producto = intval($detalle['id_producto']);
                    $id_almacen  = intval($pedido_data[0]['id_almacen']);
                    $id_ubicacion = intval($pedido_data[0]['id_ubicacion']);

                    // Consultar stock disponible real y en almacén
                    $stock = ObtenerStockProducto($id_producto, $id_almacen, $id_ubicacion);

                    $detalle['cantidad_disponible_real'] = $stock['stock_disponible'] ?? 0;
                    $detalle['cantidad_disponible_almacen'] = $stock['stock_almacen'] ?? 0;
                }
                unset($detalle);
                
                if (!empty($pedido_data)) {
                    require_once("../_vista/v_pedidos_editar.php");
                } else {
                    echo "<script>alert('Pedido no encontrado'); location.href='pedidos_mostrar.php';</script>";
                }
            } else {
                echo "<script>alert('ID de pedido no válido'); location.href='pedidos_mostrar.php';</script>";
            }

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