<?php
require_once("../_conexion/sesion.php");


?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Nuevo Pedido</title>

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
            require_once("../_modelo/m_tipo_producto.php");
            require_once("../_modelo/m_unidad_medida.php");

            // Cargar almacenes activos para el formulario
            $almacenes = MostrarAlmacenesActivos();
            $producto_tipos = MostrarProductoTipoActivos();
            $unidades_medida = MostrarUnidadMedidaActiva();

            // Crear directorio de archivos si no existe
            if (!file_exists("../_archivos/pedidos/")) {
                mkdir("../_archivos/pedidos/", 0777, true);
            }
            //=======================================================================
            // CONTROLADOR CORREGIDO
            //=======================================================================
            if (isset($_REQUEST['registrar'])) {
                // CORREGIDO: Recibir id_producto_tipo en lugar de tipo_pedido
                $id_producto_tipo = intval($_REQUEST['tipo_pedido']); // El select envía el ID
                // CORREGIDO: Recibir id_almacen en lugar de id_obra
                $id_almacen = intval($_REQUEST['id_obra']); // El select envía el ID del almacén
                $nom_pedido = strtoupper($_REQUEST['nom_pedido']);
                $solicitante = strtoupper($_REQUEST['solicitante']);
                $fecha_necesidad = $_REQUEST['fecha_necesidad'];
                $num_ot = strtoupper($_REQUEST['num_ot']);
                $contacto = $_REQUEST['contacto'];
                $lugar_entrega = strtoupper($_REQUEST['lugar_entrega']);
                $aclaraciones = strtoupper($_REQUEST['aclaraciones']);

                // Procesar materiales
                $materiales = array();
                if (isset($_REQUEST['descripcion']) && is_array($_REQUEST['descripcion'])) {
                    for ($i = 0; $i < count($_REQUEST['descripcion']); $i++) {
                        $materiales[] = array(
                            'descripcion' => $_REQUEST['descripcion'][$i],
                            'cantidad' => $_REQUEST['cantidad'][$i],
                            'unidad' => $_REQUEST['unidad'][$i], // Este es el ID de la unidad
                            'observaciones' => $_REQUEST['observaciones'][$i],
                            'sst' => $_REQUEST['sst'][$i],
                            'ma' => isset($_REQUEST['ma'][$i]) ? $_REQUEST['ma'][$i] : '',
                            'ca' => isset($_REQUEST['ca'][$i]) ? $_REQUEST['ca'][$i] : ''
                        );
                    }
                }

                // Procesar archivos
                $archivos_subidos = array();
                foreach ($_FILES as $key => $file) {
                    if (strpos($key, 'archivos_') === 0) {
                        $index = str_replace('archivos_', '', $key);
                        $archivos_subidos[$index] = $file;
                    }
                }

                // LLAMADA CORREGIDA con los parámetros correctos
                $rpta = GrabarPedido($id_producto_tipo, $id_almacen, $nom_pedido, $solicitante, 
                                $fecha_necesidad, $num_ot, $contacto, $lugar_entrega, 
                                $aclaraciones, $id, $materiales, $archivos_subidos);

                if ($rpta == "SI") {
            ?>
                    <script Language="JavaScript">
                        location.href = 'pedidos_mostrar.php?registrado=true';
                    </script>
                <?php
                } else {
                ?>
                    <script Language="JavaScript">
                        alert('Error al registrar el pedido: <?php echo $rpta; ?>');
                        location.href = 'pedidos_mostrar.php?error=true';
                    </script>
            <?php
                }
            }
            //-------------------------------------------

            
            require_once("../_vista/v_pedidos_nuevo.php");
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