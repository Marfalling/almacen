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
            require_once("../_modelo/m_obras.php");
            require_once("../_modelo/m_unidad_medida.php");

            // Cargar datos necesarios para el formulario
            $unidades_medida = MostrarUnidadMedidaActiva();

            // Crear directorio de archivos si no existe
            if (!file_exists("../_archivos/pedidos/")) {
                mkdir("../_archivos/pedidos/", 0777, true);
            }

            $id_pedido = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

            //-------------------------------------------
            if (isset($_REQUEST['actualizar'])) {
                $nom_pedido = strtoupper($_REQUEST['nom_pedido']);
                $fecha_necesidad = $_REQUEST['fecha_necesidad'];
                $num_ot = strtoupper($_REQUEST['num_ot']);
                $contacto = $_REQUEST['contacto'];
                $lugar_entrega = strtoupper($_REQUEST['lugar_entrega']);
                $aclaraciones = strtoupper($_REQUEST['aclaraciones']);

                // Procesar materiales - CORREGIDO para manejar el campo SST combinado
                $materiales = array();
                if (isset($_REQUEST['descripcion']) && is_array($_REQUEST['descripcion'])) {
                    for ($i = 0; $i < count($_REQUEST['descripcion']); $i++) {
                        // MANEJO CORRECTO del campo SST combinado (igual que en nuevo)
                        $sst_combinado = $_REQUEST['sst'][$i];
                        $sst = $ma = $ca = '';
                        
                        // Parsear SST/MA/CA del campo combinado
                        if (strpos($sst_combinado, 'SST:') !== false) {
                            if (preg_match('/SST:\s*([^|]*)\s*(\||$)/', $sst_combinado, $matches)) {
                                $sst = trim($matches[1]);
                            }
                        }
                        if (strpos($sst_combinado, 'MA:') !== false) {
                            if (preg_match('/MA:\s*([^|]*)\s*(\||$)/', $sst_combinado, $matches)) {
                                $ma = trim($matches[1]);
                            }
                        }
                        if (strpos($sst_combinado, 'CA:') !== false) {
                            if (preg_match('/CA:\s*(.*)$/', $sst_combinado, $matches)) {
                                $ca = trim($matches[1]);
                            }
                        }
                        
                        // Si no hay separadores, asumir que todo es SST
                        if (empty($sst) && empty($ma) && empty($ca)) {
                            $sst = $sst_combinado;
                        }
                        
                        $materiales[] = array(
                            'descripcion' => $_REQUEST['descripcion'][$i],
                            'cantidad' => $_REQUEST['cantidad'][$i],
                            'unidad' => $_REQUEST['unidad'][$i], // Este es el ID de la unidad
                            'observaciones' => $_REQUEST['observaciones'][$i],
                            'sst' => $sst,
                            'ma' => $ma,
                            'ca' => $ca
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

                $rpta = ActualizarPedido($id_pedido, $nom_pedido, $fecha_necesidad, 
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
                $pedido_detalle = ConsultarPedidoDetalle($id_pedido);
                
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