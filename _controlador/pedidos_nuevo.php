<?php
// ====================================================================
// CONTROLADORES DE SEGURIDAD PARA PEDIDOS
// ====================================================================

// PEDIDOS - CREAR (pedidos_nuevo.php)

require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('crear_pedidos')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'PEDIDOS', 'CREAR');
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
            require_once("../_modelo/m_tipo_material.php");
            require_once("../_modelo/m_ubicacion.php");
            require_once("../_modelo/m_centro_costo.php"); // AGREGADO

            // Cargar datos para el formulario
            $almacenes = MostrarAlmacenesActivos();
            $producto_tipos = MostrarProductoTipoActivos();
            $unidades_medida = MostrarUnidadMedidaActiva();
            $material_tipos = MostrarMaterialTipoActivos();
            $ubicaciones = MostrarUbicacionesActivas(); 
            $centros_costo = MostrarCentrosCostoActivos(); // AGREGADO
            
            // Crear directorio de archivos si no existe
            if (!file_exists("../_archivos/pedidos/")) {
                mkdir("../_archivos/pedidos/", 0777, true);
            }
            //=======================================================================
            // CONTROLADOR CORREGIDO CON CENTROS DE COSTO MULTIPLES
            //=======================================================================
            if (isset($_REQUEST['registrar'])) {
                // Recibir datos del formulario
                $id_producto_tipo = intval($_REQUEST['tipo_pedido']);
                $id_almacen = intval($_REQUEST['id_obra']);
                $id_ubicacion = intval($_REQUEST['id_ubicacion']);
                $id_centro_costo = intval($_REQUEST['id_centro_costo']); // Centro de costo de cabecera
                $nom_pedido = strtoupper($_REQUEST['nom_pedido']);
                $solicitante = strtoupper($_REQUEST['solicitante']);
                $fecha_necesidad = $_REQUEST['fecha_necesidad'];
                $num_ot = strtoupper($_REQUEST['num_ot']);
                $contacto = $_REQUEST['contacto'];
                $lugar_entrega = strtoupper($_REQUEST['lugar_entrega']);
                $aclaraciones = strtoupper($_REQUEST['aclaraciones']);
                
                //  Procesar materiales con centros de costo multiples independientes
                $materiales = array();
                $errores_validacion = array();
                
                if (isset($_REQUEST['descripcion']) && is_array($_REQUEST['descripcion'])) {
                    for ($i = 0; $i < count($_REQUEST['descripcion']); $i++) {
                        
                        $sst_descripcion = trim($_REQUEST['sst'][$i]);
                        $ot_detalle = isset($_REQUEST['ot_detalle'][$i]) ? trim($_REQUEST['ot_detalle'][$i]) : '';
                        
                        //  Procesar centros de costo múltiples para este material
                        $centros_costo_material = array();
                        
                        // Select2 múltiple envía los datos de diferentes formas según el navegador
                        if (isset($_REQUEST['centros_costo'])) {
                            // Puede venir como array si es PHP >= 7.4 o como string separado por comas
                            if (is_array($_REQUEST['centros_costo'])) {
                                // Si viene como array directo
                                if (isset($_REQUEST['centros_costo'][$i])) {
                                    $centros_value = $_REQUEST['centros_costo'][$i];
                                    
                                    if (is_array($centros_value)) {
                                        // Ya es array
                                        $centros_costo_material = $centros_value;
                                    } else if (is_string($centros_value) && !empty($centros_value)) {
                                        // String separado por comas
                                        $centros_costo_material = explode(',', $centros_value);
                                    }
                                }
                            }
                        }
                        
                        // Limpiar y validar IDs
                        $centros_costo_material = array_map('intval', $centros_costo_material);
                        $centros_costo_material = array_filter($centros_costo_material, function($id) {
                            return $id > 0;
                        });
                        $centros_costo_material = array_unique($centros_costo_material);
                        
                        //  Validación: Cada material DEBE tener al menos un centro de costo
                        if (empty($centros_costo_material)) {
                            $descripcion_corta = substr($_REQUEST['descripcion'][$i], 0, 50);
                            $errores_validacion[] = "Material " . ($i + 1) . " ({$descripcion_corta}): Debe seleccionar al menos un centro de costo";
                        }

                        $materiales[] = array(
                            'id_producto' => $_REQUEST['id_material'][$i],
                            'descripcion' => $_REQUEST['descripcion'][$i],
                            'cantidad' => $_REQUEST['cantidad'][$i],
                            'unidad' => $_REQUEST['unidad'][$i],
                            'observaciones' => $_REQUEST['observaciones'][$i],
                            'sst_descripcion' => $sst_descripcion,
                            'ot_detalle' => $ot_detalle,
                            'centros_costo' => $centros_costo_material  // 🔴 Array de IDs
                        );
                    }
                }
                
                if (!empty($errores_validacion)) {
                    $mensaje_error = "Errores en el formulario:\\n\\n" . implode("\\n", $errores_validacion);
                    ?>
                    <script Language="JavaScript">
                        alert('<?php echo addslashes($mensaje_error); ?>');
                        history.back();
                    </script>
                    <?php
                    exit;
                }

                // Procesar archivos
                $archivos_subidos = array();
                foreach ($_FILES as $key => $file) {
                    if (strpos($key, 'archivos_') === 0) {
                        $index = str_replace('archivos_', '', $key);
                        $archivos_subidos[$index] = $file;
                    }
                }

                // LLAMADA a GrabarPedido con centros de costo
                $rpta = GrabarPedido($id_producto_tipo, $id_almacen, $id_ubicacion, $id_centro_costo,
                                $nom_pedido, $solicitante, $fecha_necesidad, $num_ot, 
                                $contacto, $lugar_entrega, $aclaraciones, $id, 
                                $materiales, $archivos_subidos);

                if ($rpta == "SI") {
            ?>
                    <script Language="JavaScript">
                        location.href = 'pedidos_mostrar.php?registrado=true';
                    </script>
                <?php
                } else {
                ?>
                    <script Language="JavaScript">
                        alert('Error al registrar el pedido: <?php echo addslashes($rpta); ?>');
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