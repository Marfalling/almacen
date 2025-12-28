<?php 
//=======================================================================
// VISTA: v_pedidos_verificar.php
//=======================================================================
$pedido = $pedido_data[0];
// Verificar si ya tiene verificación técnica
$verificacion_tecnica = TieneVerificacionTecnica($id_pedido);
$tiene_verificacion_tecnica = $verificacion_tecnica['verificado'];
$pedido_anulado = ($pedido['est_pedido'] == 0);
$pedido['tiene_verificados'] = PedidoTieneVerificaciones($id_pedido);

// Inicializar variables
$modo_editar_salida = isset($modo_editar_salida) ? $modo_editar_salida : false;
$id_salida_editar = isset($id_salida_editar) ? $id_salida_editar : 0;
$pedido_salidas = isset($pedido_salidas) ? $pedido_salidas : array();
$almacenes = isset($almacenes) ? $almacenes : array();
$ubicaciones = isset($ubicaciones) ? $ubicaciones : array();

//=======================================================================
// Cargar bancos y monedas activas para los select del modal
//=======================================================================
require_once("../_modelo/m_banco.php");
require_once("../_modelo/m_moneda.php");

$bancos = MostrarBanco(); 
$monedas = MostrarMoneda();

?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Verificar Pedido <?php 
                    echo $modo_editar ? '- Editando Orden' : ''; 
                    echo $modo_editar_salida ? '- Editando Salida' : '';
                    echo $pedido_anulado ? ' - PEDIDO ANULADO' : '';

                    if ($pedido['id_producto_tipo'] == 2) {
                        echo ' <span class="badge badge-primary">ORDEN DE SERVICIO</span>';
                    }
                ?></h3>
            </div>
        </div>
        <div class="clearfix"></div>
        
        <!-- Información básica del pedido -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title" style="padding: 10px 15px; background-color: #f8f9fa;">
                    <h2 style="margin: 0; font-size: 18px;">
                        <i class="fa fa-info-circle text-primary"></i> 
                        Información General - Pedido <?php echo $pedido['cod_pedido']; ?>
                    </h2>
                    <div class="clearfix"></div>
                </div>
                    <div class="x_content" style="padding: 15px;">
                        <table class="table table-bordered" style="font-size: 13px; margin-bottom: 0;">
                            <tbody>
                                <tr>
                                    <td style="width: 20%; background-color: #f8f9fa;"><strong>Código del Pedido:</strong></td>
                                    <td style="width: 30%;"><?php echo $pedido['cod_pedido']; ?></td>
                                    <td style="width: 20%; background-color: #f8f9fa;"><strong>Tipo de Pedido:</strong></td>
                                    <td style="width: 30%;"><?php echo $pedido['nom_producto_tipo']; ?></td>
                                </tr>
                                <tr>
                                    <!--<td style="background-color: #f8f9fa;"><strong>Nombre del Pedido:</strong></td>
                                    <td><?php echo $pedido['nom_pedido']; ?></td>-->
                                    <td style="background-color: #f8f9fa;"><strong>Fecha del Pedido:</strong></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($pedido['fec_pedido'])); ?></td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f8f9fa;"><strong>Almacén:</strong></td>
                                    <td><?php echo $pedido['nom_almacen']; ?></td>
                                    <td style="background-color: #f8f9fa;"><strong>Ubicación:</strong></td>
                                    <td><?php echo $pedido['nom_ubicacion']; ?></td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f8f9fa;"><strong>Cliente:</strong></td>
                                    <td><?php echo $pedido['nom_cliente']; ?></td>
                                    <td style="background-color: #f8f9fa;"><strong>Obra:</strong></td>
                                    <td><?php echo $pedido['nom_obra']; ?></td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f8f9fa;"><strong>Solicitante:</strong></td>
                                    <td><?php echo $pedido['nom_personal']; ?></td>
                                    <td style="background-color: #f8f9fa;"><strong>Centro de Costo:</strong></td>
                                    <td>
                                        <span class="badge badge-primary badge_size">
                                            <?php echo !empty($pedido['nom_centro_costo']) ? $pedido['nom_centro_costo'] : 'Sin asignar'; ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f8f9fa;"><strong>Fecha de Necesidad:</strong></td>
                                    <td><?php echo date('d/m/Y', strtotime($pedido['fec_req_pedido'])); ?></td>
                                    <td style="background-color: #f8f9fa;"><strong>Contacto:</strong></td>
                                    <td><?php echo $pedido['cel_pedido']; ?></td>
                                </tr>
                                <tr>

                                    <td style="background-color: #f8f9fa;"><strong>Lugar de Entrega:</strong></td>
                                    <td><?php echo $pedido['lug_pedido']; ?></td>
                                </tr>
                                <?php if (!empty($pedido['acl_pedido'])) { ?>
                                <tr>
                                    <td style="background-color: #f8f9fa;"><strong>Aclaraciones:</strong></td>
                                    <td colspan="3"><?php echo nl2br($pedido['acl_pedido']); ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- COLUMNA IZQUIERDA - Items del Pedido -->
            <div class="col-md-6">
                <div class="x_panel">
                    <div class="x_title" style="padding: 8px 15px; display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 10px; flex-grow: 1;">
                            <h2 style="margin: 0; font-size: 16px;">
                                Productos <small id="contador-pendientes">(<?php echo "Cantidad: " . count($pedido_detalle); ?>)</small>
                            </h2>
                            <?php if ($pedido_anulado): ?>
                                <span class="badge badge-danger" style="font-size: 11px; padding: 4px 8px;">PEDIDO ANULADO</span>
                            <?php endif; ?>
                            
                            <?php if ($tiene_verificacion_tecnica): ?>
                                <span class="badge badge-success" style="
                                    font-size: 10px; 
                                    padding: 5px 10px;
                                    background-color: #28a745 !important;
                                    color: white !important;
                                    font-weight: 500;
                                    border: 1px solid #218838;
                                    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
                                ">
                                    <i class="fa fa-check-circle"></i> 
                                    <strong>VERIF. TÉCNICAMENTE</strong> 
                                    por <?php echo htmlspecialchars($verificacion_tecnica['nom_personal']); ?> 
                                    el <?php echo date('d/m/Y H:i:s', strtotime($verificacion_tecnica['fec_verificacion'])); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div style="margin-left: auto; padding-right: 5px;">
                            <?php 
                            // Verificar si hay items pendientes de OC/Servicio
                            $hay_oc_pendiente = false;
                            
                            foreach ($pedido_detalle as $det_check) {
                                if ($pedido['id_producto_tipo'] == 2) {
                                    // SERVICIOS: Revisar si hay pendiente
                                    $cant_ordenada = isset($det_check['cantidad_ya_ordenada']) ? floatval($det_check['cantidad_ya_ordenada']) : 0;
                                    $cant_pedida = floatval($det_check['cant_pedido_detalle']);
                                    if ($cant_ordenada < $cant_pedida) {
                                        $hay_oc_pendiente = true;
                                        break;
                                    }
                                } else {
                                    // MATERIALES: Solo si tiene cant_oc_pedido_detalle > 0
                                    $cant_oc = isset($det_check['cant_oc_pedido_detalle']) ? floatval($det_check['cant_oc_pedido_detalle']) : 0;
                                    
                                    // O si es BASE ARCE y no está todo ordenado
                                    if ($es_pedido_base_arce) {
                                        $cant_ordenada_oc = ObtenerCantidadYaOrdenadaOCPorDetalle($det_check['id_pedido_detalle']);
                                        $cant_pedida = floatval($det_check['cant_pedido_detalle']);
                                        if ($cant_ordenada_oc < $cant_pedida) {
                                            $hay_oc_pendiente = true;
                                            break;
                                        }
                                    } elseif ($cant_oc > 0) {
                                        $hay_oc_pendiente = true;
                                        break;
                                    }
                                }
                            }
                            
                            // Mostrar botón con est_pedido (del pedido general), NO est_pedido_detalle
                            if (!$pedido_anulado && 
                                !$tiene_verificacion_tecnica && 
                                !$modo_editar && 
                                !$modo_editar_salida && 
                                empty($pedido_compra) &&
                                $pedido['est_pedido'] != 2 &&
                                $hay_oc_pendiente): 
                            ?>
                                <button type="button" 
                                        class="btn btn-warning btn-sm" 
                                        id="btn-verificar-tecnicamente"
                                        data-id-pedido="<?php echo $id_pedido; ?>"
                                        style="padding: 6px 12px; font-size: 12px; margin-left: 10px;">
                                    <i class="fa fa-check-square"></i> Verificar Técnicamente
                                </button>
                            <?php endif; ?>
                        </div>
                        
                        <div class="clearfix"></div>
                    </div>
                        
                    <div class="x_content" style="max-height: 650px; overflow-y: auto; padding: 5px;">
                        <div id="contenedor-pendientes">
                            <?php 
                            $contador_detalle = 1;
                            foreach ($pedido_detalle as $detalle) {                             

                                //Variables base iniciales
                                $stock_destino = 0;
                                $stock_otras_ubicaciones = 0;
                                $cantidad_para_oc = 0;
                                $cantidad_para_os = 0;
                                
                                //Obtener datos de stock por ubicaciones
                                if ($pedido['id_producto_tipo'] == 2) {
                                    // SERVICIOS
                                    $cantidad_original = floatval($detalle['cant_pedido_detalle']);
                                    $detalle['cantidad_ya_ordenada'] = ObtenerCantidadYaOrdenadaServicioPorDetalle($detalle['id_pedido_detalle']); 
                                    $detalle['cantidad_pendiente'] = $cantidad_original - $detalle['cantidad_ya_ordenada'];
                                } else {
                                    // MATERIALES
                                    //$detalle['cantidad_ya_ordenada'] = ObtenerCantidadYaOrdenadaPorDetalle($detalle['id_pedido_detalle']);
                                    //$detalle['cantidad_pendiente'] = floatval($detalle['cant_oc_pedido_detalle']) - $detalle['cantidad_ya_ordenada'];

                                    // Obtener cantidades verificadas
                                    $cantidad_verificada_total = floatval($detalle['cant_oc_pedido_detalle']) + floatval($detalle['cant_os_pedido_detalle']);
                                    
                                    // Obtener cantidades ya ordenadas (separadas por tipo)
                                    $detalle['cantidad_ya_ordenada_oc'] = ObtenerCantidadYaOrdenadaOCPorDetalle($detalle['id_pedido_detalle']);
                                    $detalle['cantidad_ya_ordenada_os'] = ObtenerCantidadYaOrdenadaOSPorDetalle($detalle['id_pedido_detalle']);
                                    $detalle['cantidad_ya_ordenada'] = $detalle['cantidad_ya_ordenada_oc'] + $detalle['cantidad_ya_ordenada_os'];
                                    
                                    // Calcular cantidades pendientes (separadas por tipo)
                                    $detalle['cantidad_pendiente_oc'] = floatval($detalle['cant_oc_pedido_detalle']) - $detalle['cantidad_ya_ordenada_oc'];
                                    $detalle['cantidad_pendiente_os'] = floatval($detalle['cant_os_pedido_detalle']) - $detalle['cantidad_ya_ordenada_os'];
                                    //$detalle['cantidad_pendiente'] = $cantidad_verificada_total - $detalle['cantidad_ya_ordenada'];

                                    // NUEVO: Obtener stock por ubicaciones
                                    $detalle['stock_ubicacion_destino'] = ObtenerStockEnUbicacion(
                                        $detalle['id_producto'], 
                                        $pedido['id_almacen'], 
                                        $pedido['id_ubicacion']
                                    );
                                    
                                    $detalle['otras_ubicaciones_con_stock'] = ObtenerOtrasUbicacionesConStock(
                                        $detalle['id_producto'],
                                        $pedido['id_almacen'],
                                        $pedido['id_ubicacion']
                                    );
                               
                                    $detalle['stock_total_almacen'] = ObtenerStockTotalAlmacen(
                                        $detalle['id_producto'],
                                        $pedido['id_almacen']
                                    );
                                }
                                
                                //$todo_ordenado = ($detalle['cantidad_pendiente'] <= 0);

                                // Variables de estado

                                // Calcular cantidad_pendiente total
                                if ($pedido['id_producto_tipo'] == 2) {
                                    // SERVICIOS: usar cantidad_pendiente original
                                    $cantidad_pendiente = $detalle['cantidad_pendiente'];
                                } else {
                                    // MATERIALES: sumar pendientes de OC + OS
                                    $cantidad_pendiente = $detalle['cantidad_pendiente_oc'] + $detalle['cantidad_pendiente_os'];
                                }
                                
                                $todo_ordenado = ($cantidad_pendiente <= 0);
                                $cantidad_pendiente_editar = $cantidad_pendiente;
                                $todo_ordenado_editar = $todo_ordenado;

                                $comentario = $detalle['com_pedido_detalle'];
                                $unidad = '';
                                $observaciones = '';
                                    
                                if (preg_match('/Unidad:\s*([^|]*)\s*\|/', $comentario, $matches)) {
                                    $unidad = trim($matches[1]);
                                }
                                if (preg_match('/Obs:\s*(.*)$/', $comentario, $matches)) {
                                    $observaciones = trim($matches[1]);
                                }
                                    
                                // Obtener directamente la descripción SST/MA/CA
                                $descripcion_sst_completa = !empty($detalle['req_pedido']) ? $detalle['req_pedido'] : '';
                                
                                // Variables básicas
                                $cantidad_pedida = floatval($detalle['cant_pedido_detalle']);
                                $esVerificado = (!is_null($detalle['cant_oc_pedido_detalle']) || !is_null($detalle['cant_os_pedido_detalle']));
                                $pedidoAnulado = ($pedido['est_pedido'] == 0);
                                $esAutoOrden = ($pedido['id_producto_tipo'] == 2);
                                
                                // Determinar estado según ubicaciones
                                if ($esAutoOrden) {
                                    // SERVICIOS 
                                    if ($detalle['est_pedido_detalle'] == 2) {
                                        $colorBorde = '#dc3545';
                                        $claseTexto = 'text-danger';
                                        $icono = 'fa-times-circle';
                                        $estadoTexto = 'Servicio - Cerrado';
                                    } else {
                                        $colorBorde = '#2196f3';
                                        $claseTexto = 'text-primary';
                                        $icono = 'fa-wrench';
                                        $estadoTexto = 'Servicio';
                                    }
                                } else {
                                    //  MATERIALES
                                    if ($es_pedido_base_arce && !$esVerificado) {
                                    // ═══════════════════════════════════════════════════════
                                    // LÓGICA ESPECIAL PARA BASE ARCE
                                    // ═══════════════════════════════════════════════════════
                                    
                                    // Calcular si está todo ordenado
                                    $cant_total_pedida = floatval($detalle['cant_pedido_detalle']);
                                    $cant_total_ordenada = ObtenerCantidadYaOrdenadaOCPorDetalle($detalle['id_pedido_detalle']);
                                    
                                    if ($cant_total_ordenada >= $cant_total_pedida) {
                                        // TODO ORDENADO → FINALIZADO
                                        $colorBorde = '#1abc9c';
                                        $claseTexto = 'text-success';
                                        $icono = 'fa-check-circle';
                                        $estadoTexto = 'Finalizado';
                                    } else {
                                        //  PENDIENTE → BASE ARCE
                                        $colorBorde = '#17a2b8';
                                        $claseTexto = 'text-info';
                                        $icono = 'fa-warehouse';
                                        $estadoTexto = 'BASE ARCE';
                                    }
                                    //  PRIORIDAD 1: Verificar si está CERRADO MANUALMENTE
                                    } elseif ($detalle['est_pedido_detalle'] == 2) {
                                        $colorBorde = '#dc3545';
                                        $claseTexto = 'text-danger';
                                        $icono = 'fa-times-circle';
                                        $estadoTexto = 'Cerrado Manualmente';
                                        
                                    } else {
                                        //  PRIORIDAD 2: Verificar si está FINALIZADO (ordenó todo lo verificado)
                                        $cantidad_verificada_total = floatval($detalle['cant_oc_pedido_detalle']) + floatval($detalle['cant_os_pedido_detalle']);
                                        $cantidad_ya_ordenada_total = $detalle['cantidad_ya_ordenada_oc'] + $detalle['cantidad_ya_ordenada_os'];
                                        
                                        //  Si ya ordenó todo lo que se verificó → FINALIZADO
                                        if ($esVerificado && $cantidad_ya_ordenada_total >= $cantidad_verificada_total) {
                                            $colorBorde = '#1abc9c';
                                            $claseTexto = 'text-success';
                                            $icono = 'fa-check-circle';
                                            $estadoTexto = 'Finalizado';
                                            
                                        } else {
                                            //  PRIORIDAD 3: Evaluar por stock y verificación
                                            $stock_destino = floatval($detalle['stock_ubicacion_destino']);
                                            $stock_otras_ubicaciones = floatval($detalle['stock_total_almacen']) - $stock_destino;
                                            $falta_total = max(0, $cantidad_pedida - $detalle['stock_total_almacen']);
                                            
                                            $cantidad_para_oc = $falta_total;
                                            $cantidad_para_os = 0;
                                            
                                            if ($stock_destino >= $cantidad_pedida) {
                                                // CASO 1: Stock completo en destino → FINALIZADO
                                                $colorBorde = '#1abc9c';
                                                $claseTexto = 'text-success';
                                                $icono = 'fa-check-circle';
                                                $estadoTexto = 'Finalizado';
                                                
                                            } else if ($esVerificado) {
                                                // CASO 2: ITEM VERIFICADO - Evaluar estado según órdenes
                                                $cantidad_para_os = floatval($detalle['cant_os_pedido_detalle']);
                                                $cantidad_para_oc = floatval($detalle['cant_oc_pedido_detalle']);
                                                
                                                $tiene_oc = $cantidad_para_oc > 0;
                                                $tiene_os = $cantidad_para_os > 0;
                                                
                                                if ($tiene_oc && $tiene_os) {
                                                    // SUBCASO 2.1: Ambos (OC + OS)
                                                    $pendiente_oc = $detalle['cantidad_pendiente_oc'];
                                                    $pendiente_os = $detalle['cantidad_pendiente_os'];
                                                    
                                                    if ($pendiente_oc > 0 && $pendiente_os > 0) {
                                                        $colorBorde = '#ffc107';
                                                        $claseTexto = 'text-warning';
                                                        $icono = 'fa-exchange-alt';
                                                        $estadoTexto = 'Verificado OS/OC';
                                                    } else if ($pendiente_oc <= 0 && $pendiente_os > 0) {
                                                        $colorBorde = '#28a745';
                                                        $claseTexto = 'text-success';
                                                        $icono = 'fa-truck';
                                                        $estadoTexto = 'Pendiente OS';
                                                    } else if ($pendiente_os <= 0 && $pendiente_oc > 0) {
                                                        $colorBorde = '#2196f3';
                                                        $claseTexto = 'text-primary';
                                                        $icono = 'fa-shopping-cart';
                                                        $estadoTexto = 'Pendiente OC';
                                                    } else {
                                                        // Ambos completados
                                                        $colorBorde = '#1abc9c';
                                                        $claseTexto = 'text-success';
                                                        $icono = 'fa-check-circle';
                                                        $estadoTexto = 'Finalizado';
                                                    }
                                                    
                                                } else if ($tiene_os) {
                                                    // SUBCASO 2.2: Solo OS
                                                    $colorBorde = '#28a745';
                                                    $claseTexto = 'text-success';
                                                    $icono = 'fa-truck';
                                                    $estadoTexto = 'Verificado OS';
                                                    
                                                } else if ($tiene_oc) {
                                                    // SUBCASO 2.3: Solo OC
                                                    $colorBorde = '#2196f3';
                                                    $claseTexto = 'text-primary';
                                                    $icono = 'fa-shopping-cart';
                                                    $estadoTexto = 'Verificado OC';
                                                    
                                                } else {
                                                    // Sin cantidades asignadas (caso raro)
                                                    $colorBorde = '#6c757d';
                                                    $claseTexto = 'text-secondary';
                                                    $icono = 'fa-question-circle';
                                                    $estadoTexto = 'Sin asignar';
                                                }
                                                
                                            } else {
                                                // CASO 3: No verificado → PENDIENTE VERIFICAR
                                                $colorBorde = '#ffc107';
                                                $claseTexto = 'text-warning';
                                                $icono = 'fa-clock-o';
                                                $estadoTexto = 'Pendiente Verificar';
                                            }
                                        }
                                    }
                                }

                                $descripcion_producto = $detalle['prod_pedido_detalle'];

                                $enOrdenActual = false;
                                if ($modo_editar && !empty($orden_detalle)) {
                                    foreach ($orden_detalle as $item_orden) {
                                        if ($item_orden['id_producto'] == $detalle['id_producto']) {
                                            $enOrdenActual = true;
                                            break;
                                        }
                                    }
                                }

                                // ========================================
                                //  BLOQUE ÚNICO DE CÁLCULO DE PENDIENTES
                                // ========================================
                                $id_detalle = $detalle['id_pedido_detalle'];
                                
                                if (!$esAutoOrden) {
                                    //  Variables básicas del detalle (MATERIALES)
                                    $cant_os_verificada = isset($detalle['cant_os_pedido_detalle']) ? floatval($detalle['cant_os_pedido_detalle']) : 0;
                                    $cant_oc_verificada = isset($detalle['cant_oc_pedido_detalle']) ? floatval($detalle['cant_oc_pedido_detalle']) : 0;

                                    //  Obtener cantidad ya ordenada en salidas activas (excluyendo anuladas)
                                    $cant_os_ordenada_total = ObtenerCantidadYaOrdenadaOSPorDetalle($id_detalle);
                                    $cant_anulada = ObtenerCantidadEnSalidasAnuladasPorDetalle($id_detalle);
                                    $cant_os_ordenada_actual = max(0, $cant_os_ordenada_total - $cant_anulada);

                                    //  CORRECCIÓN: Calcular pendiente OS correctamente
                                    // Lo que falta en destino
                                    $falta_en_destino = max(0, $cantidad_pedida - $stock_destino);

                                    // OS pendiente = lo que se puede trasladar (limitado por OS verificada y ya ordenado)
                                    $pendiente_os = max(0, min($falta_en_destino, $cant_os_verificada) - $cant_os_ordenada_actual);

                                    //  OC pendiente (sin cambios)
                                    $pendiente_oc = isset($detalle['cantidad_pendiente_oc']) ? floatval($detalle['cantidad_pendiente_oc']) : 0;

                                    //  Log para debugging
                                    error_log("🔍 Item {$id_detalle}: Pedido=$cantidad_pedida | Stock destino=$stock_destino | Falta=$falta_en_destino | OS verificada=$cant_os_verificada | OS ordenada=$cant_os_ordenada_actual | Pendiente OS=$pendiente_os");

                                    //  Determinar si están completadas
                                    $os_completada = ($cant_os_verificada > 0 && $pendiente_os <= 0);
                                    $oc_completada = ($cant_oc_verificada > 0 && $pendiente_oc <= 0);

                                    //  Determinar si se verificó algo
                                    $se_verifico_os = ($cant_os_verificada > 0);
                                    $se_verifico_oc = ($cant_oc_verificada > 0);

                                // --Verificar si el item tiene stock completo para salida
                                //$tieneStockCompleto = ($detalle['cantidad_disponible_real'] >= $detalle['cant_pedido_detalle']);
                                }
                            ?>
                                
                            <div class="item-pendiente border mb-2"
                                    style="border-left: 4px solid <?php echo $colorBorde; ?> !important; padding: 8px 12px; border-radius: 4px;"
                                    data-item="<?php echo $contador_detalle; ?>"
                                    data-id-detalle="<?php echo $detalle['id_pedido_detalle']; ?>"
                                    data-id-producto="<?php echo $detalle['id_producto']; ?>"
                                    data-cant-pedido="<?php echo number_format($detalle['cant_pedido_detalle'], 2, '.', ''); ?>"
                                    data-cant-disponible="<?php echo number_format($detalle['cantidad_disponible_real'], 2, '.', ''); ?>">
                                
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="<?php echo $claseTexto; ?>" style="font-weight: 600; font-size: 14px;">
                                        <i class="fa <?php echo $icono; ?>"></i> 
                                        <?php echo $esAutoOrden ? 'Servicio' : 'Producto'; ?> <?php echo $contador_detalle; ?> - <?php echo $estadoTexto; ?>
                                    </span>

                                    <!-- a evaluar si se muestra 
                                    <?php 
                                    // 🔹 MOSTRAR INFO DE STOCK (solo materiales)
                                    if ($pedido['id_producto_tipo'] != 2 && isset($stock_destino)): 
                                    ?>
                                    <div style="font-size: 10px; text-align: right;">
                                        <div><strong>Destino:</strong> <?php echo number_format($stock_destino, 2); ?></div>
                                        <div><strong>Otras ubicaciones:</strong> <?php echo number_format($stock_otras_ubicaciones, 2); ?></div>
                                    </div>
                                    <?php endif; ?>
                                    -->
                                </div>

                                <!-- ***************** EVALUAR DESDE AQUI LO QUE DEBERIA MOSTRARSE EN LABEL VERIFICADO ORDENADO PENDIENTE *********************-->

                                <?php if (!$esAutoOrden && $esVerificado && isset($detalle['otras_ubicaciones_con_stock'])): ?>
                                <div class="mt-2 p-2" style="background-color: #f8f9fa; border-radius: 4px; font-size: 11px; border-left: 3px solid <?php echo $colorBorde; ?>;">
                                    <strong class="d-block mb-2">
                                        <i class="fa fa-map-marker"></i> Stock por ubicaciones:
                                    </strong>
                                    
                                    <!-- Ubicación destino -->
                                    <div class="mb-2 p-1" style="background-color: #fff; border-radius: 3px;">
                                        <div style="font-size: 10px;">
                                            <span style="font-size: 9px;">
                                                <?php echo htmlspecialchars($pedido['nom_cliente']); ?> - 
                                                <?php echo htmlspecialchars($pedido['nom_obra']); ?>
                                            </span>
                                            <span class="ml-1"><?php echo htmlspecialchars($pedido['nom_ubicacion']); ?> (Destino)</span>
                                        </div>
                                        <div class="mt-1">
                                            <strong><?php echo number_format($detalle['stock_ubicacion_destino'], 2); ?></strong>
                                            <span class="text-muted">/ Necesita: <?php echo number_format($cantidad_pedida, 2); ?></span>
                                        </div>
                                    </div>

                                    <?php
                                    // Obtener ubicaciones BLOQUEADAS para este producto
                                    $ubicaciones_bloqueadas_info = ObtenerUbicacionesBloqueadasPorDenegacion(
                                        $detalle['id_producto'],
                                        $detalle['id_pedido_detalle']
                                    );
                                    
                                    // Crear array de keys bloqueadas para búsqueda rápida
                                    $ubicaciones_bloqueadas_keys = [];
                                    foreach ($ubicaciones_bloqueadas_info as $key => $info) {
                                        $ubicaciones_bloqueadas_keys[$key] = true;
                                    }
                                    ?>

                                    <!-- Otras ubicaciones -->
                                    <?php if (!empty($detalle['otras_ubicaciones_con_stock'])): ?>
                                        <?php
                                        // Ordenar por stock de mayor a menor
                                        usort($detalle['otras_ubicaciones_con_stock'], function($a, $b) {
                                            return $b['stock'] - $a['stock'];
                                        });
                                        ?>
                                        <div class="mt-2">
                                            <small class="text-muted d-block mb-1">
                                                <i class="fa fa-list"></i> Otras ubicaciones disponibles:
                                            </small>
                                            <?php foreach ($detalle['otras_ubicaciones_con_stock'] as $ub): ?>
                                                <?php
                                                // Verificar si esta ubicación está bloqueada
                                                $key_ub = $ub['id_almacen'] . '_' . $ub['id_ubicacion'];
                                                $estaBloqueada = isset($ubicaciones_bloqueadas_keys[$key_ub]);
                                                ?>
                                                <div class="ml-3 mb-1 p-1" style="font-size: 10px; background-color: #fff; border-radius: 3px;">
                                                    <div>
                                                        <span style="font-size: 8px;">
                                                            <?php echo htmlspecialchars($ub['nom_cliente'] ?? 'Sin cliente'); ?>
                                                            <?php echo htmlspecialchars($ub['nom_obra'] ?? ''); ?>
                                                        </span>
                                                        <span class="ml-1" style="font-size: 9px;">
                                                            <?php echo htmlspecialchars($ub['nom_ubicacion']); ?>
                                                        </span>
                                                        <?php if ($estaBloqueada): ?>
                                                            <i class="fa fa-ban text-danger ml-1" title="Ubicación bloqueada"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="mt-1">
                                                        <strong><?php echo number_format($ub['stock'], 2); ?></strong> unidades
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    
                                    <!-- Resumen de cantidades -->
                                    <?php if (isset($cantidad_para_os) && isset($cantidad_para_oc)): ?>
                                    <!--<div class="mt-2 pt-2" style="border-top: 1px solid #dee2e6;">
                                        <?php if ($cantidad_para_os > 0): ?>
                                        <div class="mb-1" style="font-size: 10px;">
                                            <i class="fa fa-truck text-success"></i>
                                            <strong>Para OS:</strong> <span class="badge badge-success"><?php echo number_format($cantidad_para_os, 2); ?></span>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($cantidad_para_oc > 0): ?>
                                        <div style="font-size: 10px;">
                                            <i class="fa fa-shopping-cart text-danger"></i>
                                            <strong>Para OC:</strong> <span class="badge badge-danger"><?php echo number_format($cantidad_para_oc, 2); ?></span>
                                        </div>
                                        <?php endif; ?>
                                    </div>-->
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>

                                <!-- ********************** HASTA AQUI ******************* -->

                                <!-- ======================================= -->
                                <!--  SECCIÓN DE VERIFICADO / ORDENADO / PENDIENTE -->
                                <!-- ======================================= -->

                                <?php
                                    // ======================================
                                    // 🔹 VARIABLES DE CANTIDADES UNIFICADAS
                                    // ======================================
                                    $id_detalle = $detalle['id_pedido_detalle'];

                                    // Cantidad PEDIDA (original)
                                    $cant_pedida_item = floatval($detalle['cant_pedido_detalle']);

                                    // Cantidad VERIFICADA y ORDENADA
                                    if ($es_pedido_base_arce) {
                                        // BASE ARCE: Auto-calculada
                                        $cant_oc_db = floatval($detalle['cant_oc_pedido_detalle']);
                                        
                                        // 🔹 CORRECCIÓN: Si no tiene OC verificada, usar cantidad pedida
                                        if ($cant_oc_db > 0) {
                                            $cant_verificada_item = $cant_oc_db;
                                        } else {
                                            // Fallback: usar cantidad pedida si no se ha reverificado
                                            $cant_verificada_item = $cant_pedida_item;
                                        }
                                        
                                        $cant_ya_ordenada_item = floatval(ObtenerCantidadYaOrdenadaOCPorDetalle($id_detalle));
                                    } else {
                                        // NORMAL: Manual
                                        $cant_verificada_item = floatval($detalle['cant_oc_pedido_detalle']);
                                        $cant_ya_ordenada_item = isset($detalle['cantidad_ya_ordenada_oc'])
                                            ? floatval($detalle['cantidad_ya_ordenada_oc'])
                                            : 0;
                                    }

                                    // Cantidad PENDIENTE real
                                    $cant_pendiente_item = max(0, $cant_verificada_item - $cant_ya_ordenada_item);
                                ?>

                                <!--div style="font-size: 11px; background: #f9fafc; border-radius: 4px; padding: 6px 10px; border-left: 3px solid <?php echo $colorBorde; ?>; margin-bottom: 8px;">
                                    <strong><i class="fa fa-info-circle"></i> Detalle de cantidades</strong>
                                    <div class="mt-1" style="line-height: 1.5;">
                                        <div><strong>Verificado:</strong> <?php echo number_format($cant_pedida_item, 2); ?></div>
                                        <div><strong>Ordenado:</strong> <?php echo number_format($cant_ya_ordenada_item, 2); ?></div>
                                        <div><strong>Pendiente:</strong> <span class="text-warning"><?php echo number_format($cant_pendiente_item, 2); ?></span></div>

                                        <?php if ($es_pedido_base_arce): ?>
                                            <span class="badge badge-info mt-2"><i class="fa fa-warehouse"></i> BASE ARCE</span>
                                        <?php endif; ?>
                                    </div>
                                </div-->

                                <!-- BOTONES DE ACCIÓN -->
                                <div class="mt-2">
                                    <?php
                                    if ($esAutoOrden) { 
                                        // SERVICIOS 
                                        $cantidad_para_ordenar = isset($detalle['cant_pedido_detalle']) ? floatval($detalle['cant_pedido_detalle']) : 0;
                                        $cantidad_ya_ordenada_real = isset($detalle['cantidad_ya_ordenada']) ? floatval($detalle['cantidad_ya_ordenada']) : 0;
                                        $cantidad_pendiente_real = $cantidad_para_ordenar - $cantidad_ya_ordenada_real;
                                        
                                        //  VERIFICAR SI ESTÁ CERRADO MANUALMENTE
                                        $esta_cerrado = ($detalle['est_pedido_detalle'] == 2);
                                        
                                        if ($esta_cerrado) {
                                            // ============================================
                                            //  CASO 1: SERVICIO CERRADO MANUALMENTE
                                            // ============================================
                                    ?>
                                            <span class="badge badge-danger" style="font-size: 11px; padding: 4px 8px;">
                                                <i class="fa fa-times-circle"></i> Cerrado
                                            </span>
                                    <?php
                                        } elseif ($cantidad_pendiente_real <= 0) {
                                            // ============================================
                                            //  CASO 2: TODO ORDENADO (COMPLETADO)
                                            // ============================================
                                    ?>
                                            <span class="badge badge-success" style="font-size: 11px; padding: 4px 8px;">
                                                <i class="fa fa-check-circle"></i> Todo Ordenado
                                            </span>
                                    <?php
                                        } elseif ($enOrdenActual) {
                                            // ============================================
                                            //  CASO 3: EN ORDEN ACTUAL
                                            // ============================================
                                    ?>
                                            <span class="badge badge-info" style="font-size: 10px; padding: 2px 6px;">
                                                <i class="fa fa-check"></i> En Orden
                                            </span>
                                    <?php
                                        } elseif (!$modo_editar && !$pedidoAnulado && $cantidad_pendiente_real > 0) {
                                            // ============================================
                                            //  CASO 4: PUEDE AGREGAR (MODO NORMAL)
                                            // ============================================
                                    ?>
                                            <button type="button" 
                                                    class="btn btn-primary btn-xs btn-agregarOrden" 
                                                    <?php echo !$tiene_verificacion_tecnica ? 'disabled' : ''; ?>
                                                    data-id-detalle="<?php echo $detalle['id_pedido_detalle']; ?>"
                                                    data-id-producto="<?php echo $detalle['id_producto']; ?>"
                                                    data-descripcion="<?php echo htmlspecialchars($detalle['prod_pedido_detalle']); ?>"
                                                    data-cantidad-verificada="<?php echo $cantidad_para_ordenar; ?>"
                                                    data-cantidad-ordenada="<?php echo $cantidad_ya_ordenada_real; ?>"
                                                    data-cantidad-pendiente="<?php echo $cantidad_pendiente_real; ?>"
                                                    data-requisitos="<?php echo htmlspecialchars($detalle['req_pedido'] ?? ''); ?>"
                                                    title="Agregar a Orden (Pendiente: <?php echo $cantidad_pendiente_real; ?>)" 
                                                    style="padding: 2px 8px; font-size: 11px;">
                                                <i class="fa fa-check"></i> Agregar a Orden
                                            </button>
                                    <?php
                                        } elseif ($modo_editar && !$enOrdenActual && !$pedidoAnulado && $cantidad_pendiente_real > 0) {
                                            // ============================================
                                            //  CASO 5: PUEDE AGREGAR (MODO EDICIÓN)
                                            // ============================================
                                    ?>
                                            <button type="button" 
                                                class="btn btn-primary btn-xs btn-agregarOrden" 
                                                data-id-detalle="<?php echo $detalle['id_pedido_detalle']; ?>"
                                                data-id-producto="<?php echo $detalle['id_producto']; ?>"
                                                data-descripcion="<?php echo htmlspecialchars($detalle['nom_producto']); ?>"
                                                data-cantidad-verificada="<?php echo $detalle['cant_oc_pedido_detalle']; ?>"
                                                data-cantidad-ordenada="<?php echo $detalle['cantidad_ya_ordenada']; ?>"
                                                data-cantidad-pendiente="<?php echo $detalle['cantidad_pendiente_oc']; ?>"
                                                data-es-base-arce="0"
                                                data-requisitos="<?php echo htmlspecialchars($detalle['req_pedido'] ?? ''); ?>">
                                                <i class="fa fa-plus"></i> Agregar
                                            </button>
                                    <?php
                                        } elseif ($enOrdenActual) { 
                                    ?>
                                            <span class="badge badge-info" style="font-size: 10px; padding: 2px 6px;">
                                                <i class="fa fa-check"></i> En Orden
                                            </span>
                                    <?php 
                                        } elseif ($cantidad_pendiente_real <= 0) { 
                                    ?>
                                            <span class="badge badge-success" style="font-size: 10px; padding: 2px 6px;">
                                                <i class="fa fa-check-circle"></i> Todo Ordenado
                                            </span>
                                    <?php 
                                        }
                                    } else {
                                        // ========================================
                                        // MATERIALES - LÓGICA HÍBRIDA (OS Nueva + OC Antigua)
                                        // ========================================
                                        $cantidad_ya_ordenada_oc = ObtenerCantidadYaOrdenadaOCPorDetalle($id_detalle);
                                        $cantidad_ya_ordenada_os = ObtenerCantidadRecepcionadaOSPorDetalle($id_detalle);
                                        //---------------------------------------------
                                        // CASO 0: Reglas base
                                        //---------------------------------------------
                                        
                                        // PARA OS: Usar lógica NUEVA (con funciones)
                                        $total_os_verificado = floatval($detalle['cant_os_pedido_detalle']);
                                        $os_trasladada = ObtenerCantidadYaOrdenadaOSPorDetalle($id_detalle);
                                        $os_verificada_total = $total_os_verificado + $os_trasladada;
                                        $pendiente_os = max(0, $total_os_verificado - $os_trasladada); // Pendiente real después de restar ordenado
                                        
                                        // PARA OC: Usar lógica ANTIGUA (desde $detalle)
                                        $cant_oc_verificada = isset($detalle['cant_oc_pedido_detalle']) ? floatval($detalle['cant_oc_pedido_detalle']) : 0;
                                        $ya_ordenado_oc = isset($detalle['cantidad_ya_ordenada_oc']) ? floatval($detalle['cantidad_ya_ordenada_oc']) : 0;
                                        $pendiente_oc = isset($detalle['cantidad_pendiente_oc']) ? floatval($detalle['cantidad_pendiente_oc']) : 0;
                                        
                                        //  IMPORTANTE: Para BASE ARCE, calcular todo desde la BD
                                        if ($es_pedido_base_arce) {
                                            $total_ordenado_oc = ObtenerCantidadYaOrdenadaOCPorDetalle($id_detalle);
                                            $pendiente_oc = max(0, $cantidad_pedida - $total_ordenado_oc);

                                            error_log(" BASE ARCE - Calculando pendiente: Pedida=$cantidad_pedida | Ordenada=$total_ordenado_oc | Pendiente=$pendiente_oc");
                                        } else {
                                            $total_ordenado_oc = $ya_ordenado_oc;
                                            $pendiente_oc = isset($detalle['cantidad_pendiente_oc']) ? floatval($detalle['cantidad_pendiente_oc']) : 0;
                                        }

                                        // Estados finales
                                        $os_completada = ($os_verificada_total > 0 && $pendiente_os <= 0);
                                        $oc_completada = ($cant_oc_verificada > 0 && $pendiente_oc <= 0);

                                        $se_verifico_os = ($os_verificada_total > 0);
                                        $se_verifico_oc = ($cant_oc_verificada > 0);

                                        //---------------------------------------------
                                        // CASO 1: Stock completo en destino → FINALIZADO
                                        //---------------------------------------------
                                        if ($stock_destino >= $cantidad_pedida) { ?>
                                            <span class="badge badge-success" style="font-size: 11px;">
                                                <i class="fa fa-check-circle"></i> Stock disponible - Pedido Completado
                                            </span>
                                        <?php 
                                        }

                                        //---------------------------------------------
                                        // CASO 2: Ambos completados (OS + OC)
                                        //---------------------------------------------
                                        elseif ($os_completada && $oc_completada) { ?>
                                            <span class="badge badge-success" style="font-size: 11px;">
                                                <i class="fa fa-check-double"></i> Pedido Completado (OS + OC)
                                            </span>
                                        <?php 
                                        }

                                        //---------------------------------------------
                                        // CASO 3: Solo OC verificada y pendiente
                                        //---------------------------------------------
                                        elseif (($se_verifico_oc || $es_pedido_base_arce) && !$se_verifico_os && $pendiente_oc > 0 && !$pedidoAnulado) { ?>
                                            <!-- BOTÓN FUNCIONAL INTEGRADO -->
                                            <button type="button" 
                                                    class="btn btn-primary btn-sm btn-agregarOrden"
                                                    <?php echo !$tiene_verificacion_tecnica ? 'disabled' : ''; ?>
                                                    data-id-detalle="<?= $id_detalle ?>"
                                                    data-id-producto="<?= $detalle['id_producto'] ?>"
                                                    data-descripcion="<?= htmlspecialchars($descripcion_producto) ?>"
                                                    data-cantidad-verificada="<?= $cant_pedida_item ?>"
                                                    data-cantidad-ordenada="<?= $cant_ya_ordenada_item ?>"
                                                    data-cantidad-pendiente="<?= $cant_pendiente_item ?>"
                                                    data-es-base-arce="<?= $es_pedido_base_arce ? 1 : 0 ?>"
                                                    data-requisitos="<?php echo htmlspecialchars($detalle['req_pedido'] ?? ''); ?>"
                                                    style="padding:2px 8px;font-size:11px;">
                                                <i class="fa fa-shopping-cart"></i> Agregar a OC (<?= number_format($cant_pendiente_item,2) ?>)
                                            </button>
                                        <?php 
                                        }

                                        //---------------------------------------------
                                        // CASO 4: Solo OC completada
                                        //---------------------------------------------
                                        elseif ($se_verifico_oc && !$se_verifico_os && $oc_completada) { ?>
                                            <span class="badge badge-success" style="font-size: 11px;">
                                                <i class="fa fa-check-circle"></i> OC Completada
                                            </span>
                                        <?php 
                                        }

                                        //---------------------------------------------
                                        // CASO 5: Solo OS pendiente
                                        //---------------------------------------------
                                        elseif ($se_verifico_os && !$se_verifico_oc && $pendiente_os > 0 && !$pedidoAnulado) { ?>
                                            <button type="button" 
                                                    class="btn btn-success btn-sm btn-agregarSalida"
                                                    data-id-detalle="<?= $id_detalle ?>"
                                                    data-id-producto="<?= $detalle['id_producto'] ?>"
                                                    data-codigo-producto="<?= htmlspecialchars($detalle['cod_material'] ?? '') ?>"  
                                                    data-descripcion="<?= htmlspecialchars($descripcion_producto) ?>"
                                                    data-cantidad-disponible="<?= $pendiente_os ?>"
                                                    data-almacen-destino="<?= $pedido['id_almacen'] ?>"
                                                    data-ubicacion-destino="<?= $pedido['id_ubicacion'] ?>"
                                                    data-otras-ubicaciones='<?= json_encode($detalle['otras_ubicaciones_con_stock']) ?>'
                                                    style="padding:2px 8px;font-size:11px;">
                                                <i class="fa fa-truck"></i> Agregar a OS (<?= number_format($pendiente_os, 2) ?>)
                                            </button>
                                        <?php 
                                        }

                                        //---------------------------------------------
                                        // CASO 6: Solo OS completada
                                        //---------------------------------------------
                                        elseif ($se_verifico_os && !$se_verifico_oc && $os_completada) { ?>
                                            <span class="badge badge-success" style="font-size: 11px;">
                                                <i class="fa fa-check-circle"></i> OS Completada
                                            </span>
                                        <?php 
                                        }

                                        //---------------------------------------------
                                        // CASO 7: Ambos verificados, OS pendiente, OC completada
                                        //---------------------------------------------
                                        elseif ($se_verifico_oc && $se_verifico_os && $pendiente_os > 0 && $oc_completada && !$pedidoAnulado) { ?>
                                            <button type="button" 
                                                    class="btn btn-success btn-sm btn-agregarSalida"
                                                    data-id-detalle="<?= $id_detalle ?>"
                                                    data-id-producto="<?= $detalle['id_producto'] ?>"
                                                    data-codigo-producto="<?= htmlspecialchars($detalle['cod_material'] ?? '') ?>"  
                                                    data-descripcion="<?= htmlspecialchars($descripcion_producto) ?>"
                                                    data-cantidad-disponible="<?= $pendiente_os ?>"
                                                    data-almacen-destino="<?= $pedido['id_almacen'] ?>"
                                                    data-ubicacion-destino="<?= $pedido['id_ubicacion'] ?>"
                                                    data-otras-ubicaciones='<?= json_encode($detalle['otras_ubicaciones_con_stock']) ?>'
                                                    style="padding:2px 8px;font-size:11px;margin-right:4px;">
                                                <i class="fa fa-truck"></i> Agregar a OS (<?= number_format($pendiente_os, 2) ?>)
                                            </button>

                                            <span class="badge badge-success" style="font-size: 10px;">
                                                <i class="fa fa-check-circle"></i> OC Completada
                                            </span>
                                        <?php 
                                        }

                                        //---------------------------------------------
                                        // CASO 8: Ambos verificados, OC pendiente, OS completada
                                        //---------------------------------------------
                                        elseif ($se_verifico_oc && $se_verifico_os && $pendiente_oc > 0 && $os_completada && !$pedidoAnulado) { ?>
                                                
                                            <span class="badge badge-success" style="font-size: 10px;margin-right:4px;">
                                                <i class="fa fa-check-circle"></i> OS Completada
                                            </span>

                                            <!-- BOTÓN FUNCIONAL INTEGRADO -->
                                            <button type="button" 
                                                    class="btn btn-primary btn-sm btn-agregarOrden"
                                                    <?php echo !$tiene_verificacion_tecnica ? 'disabled' : ''; ?>
                                                    data-id-detalle="<?= $id_detalle ?>"
                                                    data-id-producto="<?= $detalle['id_producto'] ?>"
                                                    data-descripcion="<?= htmlspecialchars($descripcion_producto) ?>"
                                                    data-cantidad-verificada="<?= $cant_pedida_item ?>"
                                                    data-cantidad-ordenada="<?= $cant_ya_ordenada_item ?>"
                                                    data-cantidad-pendiente="<?= $cant_pendiente_item ?>"
                                                    data-es-base-arce="<?= $es_pedido_base_arce ? 1 : 0 ?>"
                                                    data-requisitos="<?php echo htmlspecialchars($detalle['req_pedido'] ?? ''); ?>"
                                                    style="padding:2px 8px;font-size:11px;">
                                                <i class="fa fa-shopping-cart"></i> Agregar a OC (<?= number_format($cant_pendiente_item, 2) ?>)
                                            </button>
                                        <?php 
                                        }

                                        //---------------------------------------------
                                        // CASO 9: Ambos verificados y ambos pendientes
                                        //---------------------------------------------
                                        elseif ($se_verifico_os && $se_verifico_oc && $pendiente_os > 0 && $pendiente_oc > 0 && !$pedidoAnulado) { ?>
                                            <button type="button" 
                                                    class="btn btn-success btn-sm btn-agregarSalida" 
                                                    data-id-detalle="<?= $id_detalle ?>"
                                                    data-id-producto="<?= $detalle['id_producto'] ?>"
                                                    data-codigo-producto="<?= htmlspecialchars($detalle['cod_material'] ?? '') ?>"  
                                                    data-descripcion="<?= htmlspecialchars($descripcion_producto) ?>"
                                                    data-cantidad-disponible="<?= $pendiente_os ?>"
                                                    data-almacen-destino="<?= $pedido['id_almacen'] ?>"
                                                    data-ubicacion-destino="<?= $pedido['id_ubicacion'] ?>"
                                                    data-otras-ubicaciones='<?= json_encode($detalle['otras_ubicaciones_con_stock']) ?>'
                                                    style="padding: 2px 8px; font-size: 11px; margin-right: 4px;">
                                                <i class="fa fa-truck"></i> OS (<?= number_format($pendiente_os, 2) ?>)
                                            </button>
                                            
                                            <!-- BOTÓN FUNCIONAL INTEGRADO -->
                                            <button type="button" 
                                                    class="btn btn-primary btn-sm btn-agregarOrden" 
                                                    <?php echo !$tiene_verificacion_tecnica ? 'disabled' : ''; ?>
                                                    data-id-detalle="<?= $id_detalle ?>"
                                                    data-id-producto="<?= $detalle['id_producto'] ?>"
                                                    data-descripcion="<?= htmlspecialchars($descripcion_producto) ?>"
                                                    data-cantidad-verificada="<?= $cant_pedida_item ?>"
                                                    data-cantidad-ordenada="<?= $cant_ya_ordenada_item ?>"
                                                    data-cantidad-pendiente="<?= $cant_pendiente_item ?>"
                                                    data-es-base-arce="<?= $es_pedido_base_arce ? 1 : 0 ?>"
                                                    data-requisitos="<?php echo htmlspecialchars($detalle['req_pedido'] ?? ''); ?>"
                                                    style="padding: 2px 8px; font-size: 11px;">
                                                <i class="fa fa-shopping-cart"></i> OC (<?= number_format($cant_pendiente_item, 2) ?>)
                                            </button>
                                        <?php
                                        }

                                        //---------------------------------------------
                                        // CASO 10: Pendiente verificar o BASE ARCE
                                        //---------------------------------------------
                                        elseif (!$esVerificado && !$pedidoAnulado) { 
                                            if ($es_pedido_base_arce) { 
                                                // BASE ARCE: Solo mostrar botón de OC si hay pendiente
                                                // 🔹 Calcular pendiente para BASE ARCE
                                                $cantidad_pendiente_base_arce = max(0, $cantidad_pedida - $cantidad_ya_ordenada_oc);
                                                
                                                if ($cantidad_pendiente_base_arce > 0) {
                                                    // ✅ HAY PENDIENTE: Mostrar botón
                                                    ?>
                                                    <button type="button" 
                                                            class="btn btn-primary btn-sm btn-agregarOrden"
                                                            <?php echo !$tiene_verificacion_tecnica ? 'disabled' : ''; ?>
                                                            data-id-detalle="<?= $id_detalle ?>"
                                                            data-id-producto="<?= $detalle['id_producto'] ?>"
                                                            data-descripcion="<?= htmlspecialchars($descripcion_producto) ?>"
                                                            data-cantidad-verificada="<?= $cantidad_pedida ?>"
                                                            data-cantidad-ordenada="<?= $cantidad_ya_ordenada_oc ?>"
                                                            data-cantidad-pendiente="<?= $cantidad_pendiente_base_arce ?>"
                                                            data-es-base-arce="1"
                                                            data-requisitos="<?php echo htmlspecialchars($detalle['req_pedido'] ?? ''); ?>"
                                                            style="padding: 2px 8px; font-size: 11px;">
                                                        <i class="fa fa-shopping-cart"></i> Agregar a OC (<?= number_format($cantidad_pendiente_base_arce, 2) ?>)
                                                    </button>
                                                <?php 
                                                } else {
                                                    // ✅ TODO ORDENADO: Mostrar badge
                                                    ?>
                                                    <span class="badge badge-success" style="padding: 4px 8px; font-size: 11px;">
                                                        <i class="fa fa-check-circle"></i> TODO ORDENADO
                                                    </span>
                                                <?php 
                                                }
                                            }
                                        }
                                    
                                    }
                                    ?>

                                </div>

                                <!-- Descripción del producto -->
                                <div style="font-size: 11px; color: #333; line-height: 1.6; margin-top: 8px;">
                                    <!-- PRIMERA LÍNEA: Descripción y datos básicos -->
                                    <div style="margin-bottom: 4px;">
                                        <?php if (!empty($detalle['cod_material'])): ?>
                                        <strong>Código:</strong> 
                                        <span style="color: #666;"><?php echo htmlspecialchars($detalle['cod_material']); ?></span>
                                        <span style="margin: 0 8px;">|</span>
                                        <?php endif; ?>
                                        
                                        <strong>Producto:</strong> 
                                        <span style="color: #666;">
                                            <?php 
                                            $nombre_producto = !empty($detalle['nom_producto']) ? $detalle['nom_producto'] : $detalle['prod_pedido_detalle'];
                                            echo strlen($nombre_producto) > 80 ? substr($nombre_producto, 0, 80) . '...' : $nombre_producto; 
                                            ?>
                                        </span>
                                        
                                        <span style="margin: 0 8px;">|</span>
                                        <strong>Cantidad:</strong> <?php echo number_format($detalle['cant_pedido_detalle'], 2); ?>
                                        
                                        <span style="margin: 0 8px;">|</span>
                                        <strong>Unidad:</strong> <?php echo $unidad; ?>
                                    </div>

                                    <!-- SEGUNDA LÍNEA: OT y SST -->
                                    <?php if (!empty($detalle['ot_pedido_detalle']) || (!$esAutoOrden && !empty($descripcion_sst_completa))): ?>
                                    <div style="font-size: 10px; color: #777; margin-top: 4px;">
                                        <?php if (!empty($detalle['ot_pedido_detalle'])): ?>
                                        <strong>OT:</strong> <span><?php echo htmlspecialchars($detalle['ot_pedido_detalle']); ?></span>
                                        <?php endif; ?>
                                        
                                        <?php if (!$esAutoOrden && !empty($descripcion_sst_completa)): ?>
                                            <?php if (!empty($detalle['ot_pedido_detalle'])): ?>
                                            <span style="margin: 0 8px;">|</span>
                                            <?php endif; ?>
                                        <strong>SST/MA/CA:</strong> <?php echo htmlspecialchars($descripcion_sst_completa); ?>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>

                                    <!-- TERCERA LÍNEA: Estado de verificación y ordenamiento -->
                                    <?php 
                                    // MOSTRAR INFORMACIÓN DE ORDENAMIENTO
                                    if ($esAutoOrden) {
                                        // Para SERVICIOS 
                                        $cant_original_item = floatval($detalle['cant_pedido_detalle']);
                                        $cant_ordenada_item = isset($detalle['cantidad_ya_ordenada']) ? floatval($detalle['cantidad_ya_ordenada']) : 0;
                                        $cant_pendiente_item = $cant_original_item - $cant_ordenada_item;
                                        
                                        //  SOLO MOSTRAR SI HAY ALGO ORDENADO
                                        if ($cant_ordenada_item > 0 || $cant_pendiente_item < $cant_original_item) { ?>
                                            <div style="padding: 3px 0; border-top: 1px solid #e0e0e0;">
                                                <?php if ($cant_ordenada_item > 0) { ?>
                                                    <strong style="color: #333;">Ordenado:</strong> 
                                                    <span style="color: #333;"><?php echo number_format($cant_ordenada_item, 2); ?></span>
                                                <?php }
                                                
                                                if ($cant_pendiente_item > 0) { ?>
                                                    <span style="margin: 0 8px;">|</span>
                                                    <strong style="color: #333;">Pendiente:</strong> 
                                                    <span style="color: #333;"><?php echo number_format($cant_pendiente_item, 2); ?></span>
                                                <?php } ?>
                                            </div>
                                        <?php }
                                        
                                    } else if ($esVerificado) {
                                        // Para MATERIALES verificados
                                        $cant_oc_verificada = floatval($detalle['cant_oc_pedido_detalle']);
                                        $cant_os_verificada = floatval($detalle['cant_os_pedido_detalle']);
                                        
                                        // 🔹 DEFINIR CANTIDADES ORDENADAS AQUÍ (ANTES DE USARLAS)
                                        $cant_oc_ordenada = isset($detalle['cantidad_ya_ordenada_oc']) ? floatval($detalle['cantidad_ya_ordenada_oc']) : 0;
                                        $cant_os_ordenada = isset($detalle['cantidad_ya_ordenada_os']) ? floatval($detalle['cantidad_ya_ordenada_os']) : 0;
                                        
                                    // Obtener salidas históricas reales
                                        $cant_os_ordenada_historica = ObtenerCantidadYaOrdenadaOSPorDetalle($detalle['id_pedido_detalle']);
                                        
                                        // Si no hay OS verificada PERO hay salidas históricas, mostrarlas
                                        if ($cant_os_verificada == 0 && $cant_os_ordenada_historica > 0) {
                                            $cant_os_verificada = $cant_os_ordenada_historica;
                                        }
                                        
                                        // Usar ordenada histórica si no hay ordenada calculada
                                        if ($cant_os_ordenada == 0 && $cant_os_ordenada_historica > 0) {
                                            $cant_os_ordenada = $cant_os_ordenada_historica;
                                        }
                                        
                                        // ✅ CORRECCIÓN: Usar OS verificada, no cantidad pedida
                                        $pendiente_os = $cant_os_verificada - $cant_os_ordenada;
                                        
                                        // ✅ Solo validar que no sea negativo
                                        $pendiente_os = max(0, $pendiente_os);
                                        
                                        $pendiente_oc = $cant_oc_verificada - $cant_oc_ordenada;
                                        
                                        // DETERMINAR SI ESTÁN COMPLETADAS
                                        $os_completada = ($cant_os_verificada > 0 && $pendiente_os <= 0);
                                        $oc_completada = ($cant_oc_verificada > 0 && $pendiente_oc <= 0);
                                        
                                        if ($cant_os_verificada > 0 || $cant_oc_verificada > 0) { ?>
                                        <div style="padding: 4px 0; border-top: 1px solid #e0e0e0;">
                                            
                                            <?php if ($se_verifico_os && $cant_os_verificada > 0): ?>
                                                <div style="background: #ffffff; padding: 3px 8px; border-radius: 3px; margin-bottom: 4px; color: #333;">
                                                    <strong>📦 OS:</strong>
                                                    <span><strong>Verificado:</strong> <?php echo number_format($cant_os_verificada, 2); ?></span>
                                                    
                                                    <?php if ($cant_os_ordenada > 0): ?>
                                                        <span style="margin: 0 6px;">•</span>
                                                        <span><strong>Trasladado:</strong> <?php echo number_format($cant_os_ordenada, 2); ?></span>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($pendiente_os > 0): ?>
                                                        <span style="margin: 0 6px;">•</span>
                                                        <span><strong>Pendiente:</strong> <?php echo number_format($pendiente_os, 2); ?></span>
                                                    <?php elseif ($os_completada): ?>
                                                        <span style="margin: 0 6px;">•</span>
                                                        <span style="font-weight: bold; color: #28a745;">✓ Completada</span>
                                                    <?php endif; ?>

                                                    <!--
                                                    <span style="margin: 0 6px;">•</span>
                                                    <span class="text-info"><strong>Stock destino:</strong> <?php echo number_format($stock_destino, 2); ?>/<?php echo number_format($cantidad_pedida, 2); ?></span> !-->

                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if ($se_verifico_oc && $cant_oc_verificada > 0): ?>
                                                <div style="background: #ffffff; padding: 3px 8px; border-radius: 3px; color: #333;">
                                                    <strong>🛒 OC:</strong>
                                                    <span><strong>Verificado:</strong> <?php echo number_format($cant_oc_verificada, 2); ?></span>
                                                    
                                                    <?php if ($cant_oc_ordenada > 0): ?>
                                                        <span style="margin: 0 6px;">•</span>
                                                        <span><strong>Ordenado:</strong> <?php echo number_format($cant_oc_ordenada, 2); ?></span>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($pendiente_oc > 0): ?>
                                                        <span style="margin: 0 6px;">•</span>
                                                        <span><strong>Pendiente:</strong> <?php echo number_format($pendiente_oc, 2); ?></span>
                                                    <?php elseif ($oc_completada): ?>
                                                        <span style="margin: 0 6px;">•</span>
                                                        <span style="font-weight: bold; color: #28a745;">✓ Completada</span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if ($os_completada && $oc_completada && $cant_os_verificada > 0 && $cant_oc_verificada > 0): ?>
                                                <div style="background: #d4edda; padding: 5px 8px; border-radius: 4px; margin-top: 5px; border-left: 3px solid #28a745;">
                                                    <strong style="color: #155724;">
                                                        <i class="fa fa-check-double"></i> OS + OC Completadas
                                                    </strong>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php }
                                    }
                                    ?>
                    
                                </div>
                            </div>
                            <?php 
                                $contador_detalle++;
                            } 
                            ?>
                        </div>
                        
                        <div id="mensaje-sin-pendientes" style="display: none;" class="text-center p-3">
                            <i class="fa fa-check-circle fa--2x text-success mb-2"></i>
                            <h5 class="text-success">¡Todos verificados!</h5>
                            <p class="text-muted" style="font-size: 12px;">No hay items pendientes.</p>
                        </div>
                    </div>
                </div>
            </div>

                    <!-- COLUMNA DERECHA - Órdenes y Salidas -->
                    <div class="col-md-6">
                        <div class="x_panel">
                            <div class="x_title" style="padding: 8px 15px;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h2 style="margin: 0; font-size: 16px;">
                                        <?php 
                                        if ($modo_editar) {
                                            echo 'Editando Orden';
                                        } elseif ($modo_editar_salida) {
                                            echo 'Editando Salida';
                                        } else {
                                            echo 'Gestión de Pedido';
                                        }
                                        ?>
                                        <small id="contador-verificados"></small>
                                    </h2>
                                    
                                    <?php if (!$modo_editar && !$modo_editar_salida && !$pedido_anulado): ?>
                                    <div class="btn-group" role="group">
                                        <?php if (!$modo_editar && !$pedido_anulado): ?>
                                            <?php
                                            // Verificar si hay items disponibles para agregar a orden
                                            $tiene_items_disponibles = false;
                                            foreach ($pedido_detalle as $detalle) {
                                                $esVerificado = !is_null($detalle['cant_oc_pedido_detalle']);

                                                $detalle['cantidad_ya_ordenada_oc'] = ObtenerCantidadYaOrdenadaOCPorDetalle($detalle['id_pedido_detalle']);
                                                $detalle['cantidad_pendiente_oc'] = floatval($detalle['cant_oc_pedido_detalle']) - $detalle['cantidad_ya_ordenada_oc'];

                                                $pendiente_oc = isset($detalle['cantidad_pendiente_oc']) ? floatval($detalle['cantidad_pendiente_oc']) : 0;

                                                $stockInsuficiente = $detalle['cantidad_disponible_almacen'] < $detalle['cant_pedido_detalle'];
                                                $esAutoOrden = ($pedido['id_producto_tipo'] == 2);
                                                $estaCerrado = ($detalle['est_pedido_detalle'] == 2);
                                                
                                                // Si es auto-orden, verificado con stock insuficiente, y no está cerrado
                                                if (($esAutoOrden || ($esVerificado && $pendiente_oc>0)) && !$estaCerrado) {
                                                    $tiene_items_disponibles = true;
                                                    break;
                                                }
                                            }
                                            ?>
                                            
                                            <?php if ($tiene_items_disponibles): ?>
                                                <button type="button" data-toggle="tooltip" class="btn btn-primary btn-sm" id="btn-nueva-orden" style="padding: 4px 8px; font-size: 12px;" 
                                                        title="<?php echo $pedido['id_producto_tipo'] == 2 ? 'Crear una nueva orden de servicio' : 'Crear una nueva orden de compra con los items verificados'; ?>">
                                                    <i class="fa fa-shopping-cart"></i> Nueva Orden
                                                </button>
                                            <?php else: ?>
                                                <button type="button" data-toggle="tooltip" class="btn btn-secondary btn-sm" disabled 
                                                        title="<?php echo $pedido['id_producto_tipo'] == 2 ? 'No hay servicios disponibles para agregar' : 'No hay items disponibles para agregar'; ?>" 
                                                        style="padding: 4px 8px; font-size: 12px;">
                                                    <i class="fa fa-ban"></i> Nueva Orden
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        
                                        
                                        <!-- Botón Nueva Salida - Solo para MATERIALES -->
                                        <?php if ($pedido['id_producto_tipo'] != 2 && !$modo_editar_salida && !$pedido_anulado): ?>
                                        <?php
                                        // VALIDAR ITEMS DISPONIBLES PARA SALIDA
                                        $tiene_items_para_salida = false;
                                        
                                        foreach ($pedido_detalle as $detalle_validacion) {
                                            // Solo evaluar MATERIALES
                                            if ($pedido['id_producto_tipo'] != 2) {
                                                $cant_os_verificada = floatval($detalle_validacion['cant_os_pedido_detalle']);
                                                
                                                if ($cant_os_verificada > 0) {
                                                    $detalle_validacion['cantidad_ya_ordenada_os'] = ObtenerCantidadYaOrdenadaOSPorDetalle($detalle_validacion['id_pedido_detalle']);
                                                    $pendiente_os = $cant_os_verificada - $detalle_validacion['cantidad_ya_ordenada_os'];
                                                    
                                                    if ($pendiente_os > 0) {
                                                        $tiene_items_para_salida = true;
                                                        break; 
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                        
                                        <!--  SIEMPRE RENDERIZAR EL BOTÓN (solo cambiar estado) -->
                                        <button type="button" data-toggle="tooltip"
                                                class="btn btn-<?php echo $tiene_items_para_salida ? 'success' : 'secondary'; ?> btn-sm" 
                                                id="btn-nueva-salida" 
                                                <?php echo !$tiene_items_para_salida ? 'disabled' : ''; ?>
                                                title="<?php 
                                                    if ($tiene_items_para_salida) {
                                                        echo 'Crear una nueva orden de salida ';
                                                    } else {
                                                        echo 'No hay items disponibles para generar salida';
                                                    }
                                                ?>"
                                                style="padding: 4px 8px; font-size: 12px;">
                                            <i class="fa fa-<?php echo $tiene_items_para_salida ? 'truck' : 'ban'; ?>"></i> Nueva Salida
                                        </button>
                                    <?php endif; ?>

                                        <!-- Fin Modificado -->

                                        <!-- Botón Nueva Salida Anterior-->
                                        <!--<button type="button" class="btn btn-success btn-sm" id="btn-nueva-salida" style="padding: 4px 8px; font-size: 12px;">
                                            <i class="fa fa-truck"></i> Nueva Salida
                                        </button>-->
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            
                            <div class="x_content" style="max-height: 650px; overflow-y: auto; padding: 5px;">
                                <!-- TABS PARA ÓRDENES Y SALIDAS -->
                                <ul class="nav nav-tabs" id="myTab" role="tablist" <?php echo ($modo_editar || $modo_editar_salida) ? 'style="display: none;"' : 'style="display: flex;"'; ?>>
                                    <li class="nav-item">
                                        <a class="nav-link active" id="ordenes-tab" data-toggle="tab" href="#ordenes" role="tab">
                                            <i class="fa fa-<?php echo $pedido['id_producto_tipo'] == 2 ? 'wrench' : 'shopping-cart'; ?>"></i> 
                                            <?php echo $pedido['id_producto_tipo'] == 2 ? 'Órdenes de Servicio' : 'Órdenes de Compra'; ?> 
                                            <span><?php echo count($pedido_compra); ?></span>
                                        </a>
                                    </li>
                                    
                                    <!--  SOLO MOSTRAR TAB DE SALIDAS SI NO ES SERVICIO -->
                                    <?php if ($pedido['id_producto_tipo'] != 2): ?>
                                    <li class="nav-item">
                                        <a class="nav-link" id="salidas-tab" data-toggle="tab" href="#salidas" role="tab">
                                            <i class="fa fa-truck"></i> Salidas 
                                            <span><?php echo count($pedido_salidas); ?></span>
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                </ul>


                                <div class="tab-content" id="myTabContent" <?php echo ($modo_editar || $modo_editar_salida) ? 'style="display: none;"' : 'style="display: block;"'; ?>>
                                    <!-- TAB: ÓRDENES DE COMPRA -->
                                    <div class="tab-pane fade show active" id="ordenes" role="tabpanel">
                                        <div class="table-responsive mt-2">
                                            <table class="table table-striped table-bordered" style="font-size: 12px;">
                                                <thead style="background-color: #007bff; color: white;">
                                                    <tr>
                                                        <th style="width: 15%;">N° Orden</th>
                                                        <th style="width: 20%;">Proveedor</th>
                                                        <th style="width: 15%;">Fecha</th>
                                                        <th style="width: 15%;">Estado</th>
                                                        <th style="width: 20%;">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbody-ordenes">
                                                    <?php if (!empty($pedido_compra)) { ?>
                                                        <?php foreach ($pedido_compra as $compra) {
                                                            $estado_texto = '';
                                                            $estado_clase = '';

                                                            /*switch ($compra['est_compra']) {
                                                                case 0:
                                                                    $estado_texto = 'Anulada';
                                                                    $estado_clase = 'danger';
                                                                    break;
                                                                case 1:
                                                                    $estado_texto = 'Pendiente';
                                                                    $estado_clase = 'warning';
                                                                    break;
                                                                case 2:
                                                                    $estado_texto = 'Aprobada';
                                                                    $estado_clase = 'info';
                                                                    $puede_agregar_pago = true;
                                                                    break;
                                                                case 3:
                                                                    $estado_texto = 'Ingresada';
                                                                    $estado_clase = 'success';
                                                                    $puede_agregar_pago = true;
                                                                    break;
                                                                case 4:
                                                                    $estado_texto = 'Pagada';
                                                                    $estado_clase = 'primary';
                                                                    $puede_agregar_pago = false;
                                                                    break;
                                                                default:
                                                                    $estado_texto = 'Desconocido';
                                                                    $estado_clase = 'secondary';
                                                            }*/

                                                            if ($compra['est_compra'] == 2 && $compra['pagado'] == 1) {
                                                                $estado_texto = 'PAGADO';
                                                                $estado_clase = 'badge-primary';

                                                            } elseif ($compra['est_compra'] == 3 && $compra['pagado'] == 1) {
                                                                $estado_texto = 'CERRADO';
                                                                $estado_clase = 'badge-dark';

                                                            } elseif ($compra['est_compra'] == 2) {
                                                                $estado_texto = 'APROBADO';
                                                                $estado_clase = 'badge-info';

                                                            } elseif ($compra['est_compra'] == 3) {
                                                                $estado_texto = 'INGRESADO';
                                                                $estado_clase = 'badge-success';

                                                            } elseif ($compra['est_compra'] == 1) {
                                                                $estado_texto = 'PENDIENTE';
                                                                $estado_clase = 'badge-warning';
                                                            } else {
                                                                $estado_texto = 'ANULADO';
                                                                $estado_clase = 'badge-danger';
                                                            }

                                                            $fecha_formateada = date('d/m/Y', strtotime($compra['fec_compra']));
                                                        ?>
                                                            <tr>
                                                                <td><strong>C00<?php echo $compra['id_compra']; ?></strong></td>
                                                                <td><?php echo htmlspecialchars($compra['nom_proveedor']); ?></td>
                                                                <td><?php echo $fecha_formateada; ?></td>
                                                                <td>
                                                                    <span class="badge <?php echo $estado_clase; ?> badge_size">
                                                                        <?php echo $estado_texto; ?>
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <!-- Botón Ver Detalles -->
                                                                    <button class="btn btn-info btn-xs btn-ver-detalle" data-toggle="tooltip"
                                                                            title="Ver detalles"
                                                                            data-id-compra="<?php echo $compra['id_compra']; ?>">
                                                                        <i class="fa fa-eye"></i>
                                                                    </button>

                                                                    <?php
                                                                    // Verificar si tiene aprobaciones
                                                                    $tiene_aprobacion_tecnica = !empty($compra['id_personal_aprueba_tecnica']);
                                                                    $tiene_aprobacion_financiera = !empty($compra['id_personal_aprueba_financiera']);
                                                                    $tiene_alguna_aprobacion = $tiene_aprobacion_tecnica || $tiene_aprobacion_financiera;

                                                                    // Solo se puede editar si está PENDIENTE y SIN aprobaciones
                                                                    $puede_editar = ($compra['est_compra'] == 1 && !$tiene_alguna_aprobacion);

                                                                    if ($puede_editar) { ?>
                                                                        <!-- Botón Editar HABILITADO -->
                                                                        <button class="btn btn-warning btn-xs ml-1 btn-editar-orden" data-toggle="tooltip"
                                                                                title="<?php echo $pedido['id_producto_tipo'] == 2 ? 'Editar esta orden de servicio' : 'Editar esta orden de compra'; ?>"
                                                                                data-id-compra="<?php echo $compra['id_compra']; ?>">
                                                                            <i class="fa fa-edit"></i>
                                                                        </button>
                                                                    <?php } else {
                                                                        if ($compra['est_compra'] == 0) {
                                                                            $mensaje = $pedido['id_producto_tipo'] == 2 ? "No se puede editar - Orden de servicio anulada" : "No se puede editar - Orden anulada";
                                                                        } elseif ($compra['est_compra'] == 2) {
                                                                            $mensaje = $pedido['id_producto_tipo'] == 2 ? "No se puede editar - Orden de servicio aprobada completamente" : "No se puede editar - Orden aprobada completamente";
                                                                        } elseif ($compra['est_compra'] == 3) {
                                                                            $mensaje = $pedido['id_producto_tipo'] == 2 ? "No se puede editar - Orden de servicio cerrada" : "No se puede editar - Orden cerrada";
                                                                        } elseif ($compra['est_compra'] == 4) {
                                                                            $mensaje = $pedido['id_producto_tipo'] == 2 ? "No se puede editar - Orden de servicio pagada" : "No se puede editar - Orden pagada";
                                                                        } elseif ($tiene_alguna_aprobacion) {
                                                                            $mensaje = $pedido['id_producto_tipo'] == 2 ? "No se puede editar - Orden de servicio con aprobación iniciada" : "No se puede editar - Orden con aprobación iniciada";
                                                                        } else {
                                                                            $mensaje = "No se puede editar";
                                                                        }
                                                                    ?>
                                                                        <!-- Botón Editar DESHABILITADO -->
                                                                        <button class="btn btn-outline-secondary btn-xs ml-1"
                                                                                title="<?php echo $mensaje; ?>"
                                                                                disabled>
                                                                            <i class="fa fa-edit"></i>
                                                                        </button>
                                                                    <?php } ?>

                                                                    <!-- Botón Anular -->
                                                                    <?php
                                                                    $puede_anular = ($compra['est_compra'] != 0 && !$tiene_alguna_aprobacion);

                                                                    if ($puede_anular) { ?>
                                                                        <button class="btn btn-danger btn-xs ml-1 btn-anular-orden"
                                                                                title="Anular Orden"
                                                                                data-toggle="tooltip"
                                                                                data-id-compra="<?php echo $compra['id_compra']; ?>"
                                                                                data-id-pedido="<?php echo $id_pedido; ?>">
                                                                            <i class="fa fa-times"></i>
                                                                        </button>
                                                                    <?php } else {
                                                                        if ($compra['est_compra'] == 0) {
                                                                            $mensaje_anular = "Orden anulada";
                                                                        } elseif ($compra['est_compra'] == 2) {
                                                                            $mensaje_anular = "No se puede anular - Orden aprobada completamente";
                                                                        } elseif ($compra['est_compra'] == 3) {
                                                                            $mensaje_anular = "No se puede anular - Orden cerrada";
                                                                        } elseif ($compra['est_compra'] == 4) {
                                                                            $mensaje_anular = "No se puede anular - Orden pagada";
                                                                        } elseif ($tiene_alguna_aprobacion) {
                                                                            $mensaje_anular = "No se puede anular - Orden con aprobación iniciada";
                                                                        } else {
                                                                            $mensaje_anular = "No se puede anular";
                                                                        }
                                                                    ?>
                                                                        <button class="btn btn-outline-secondary btn-xs ml-1"
                                                                                title="<?php echo $mensaje_anular; ?>"
                                                                                disabled>
                                                                            <i class="fa fa-times"></i>
                                                                        </button>
                                                                    <?php } ?>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <tr>
                                                            <td colspan="5" class="text-center p-3">
                                                                <i class="fa fa-<?php echo $pedido['id_producto_tipo'] == 2 ? 'wrench' : 'file-text-o'; ?> fa-2x text-info mb-2"></i>
                                                                <h5 class="text-info">
                                                                    <?php echo $pedido['id_producto_tipo'] == 2 ? 'Sin órdenes de servicio' : 'Sin órdenes de compra'; ?>
                                                                </h5>
                                                                <p class="text-muted" style="font-size: 12px;">
                                                                    <?php echo $pedido['id_producto_tipo'] == 2 ? 'Las órdenes de servicio aparecerán aquí.' : 'Las órdenes de compra aparecerán aquí.'; ?>
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- TAB: SALIDAS -->
                                    <div class="tab-pane fade" id="salidas" role="tabpanel">
                                        <div class="table-responsive mt-2">
                                            <table class="table table-striped table-bordered" style="font-size: 12px;">
                                                <thead style="background-color: #28a745; color: white;">
                                                    <tr>
                                                        <th style="width: 12%;">N° Salida</th>
                                                        <th style="width: 18%;">Destino</th>
                                                        <th style="width: 12%;">Fecha Requerida</th>
                                                        <th style="width: 18%;">Recepcionado Por</th>
                                                        <th style="width: 12%;">Estado</th>
                                                        <th style="width: 18%;">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbody-salidas">
                                                <?php if (!empty($pedido_salidas)) { ?>
                                                    <?php foreach ($pedido_salidas as $salida) {

                                                        // Estado
                                                        if ($salida['est_salida'] == 0) {
                                                            $estado_salida_texto = 'ANULADA';
                                                            $estado_salida_clase = 'badge-danger';
                                                        } elseif ($salida['est_salida'] == 1) {
                                                            $estado_salida_texto = 'PENDIENTE';
                                                            $estado_salida_clase = 'badge-warning';
                                                        } elseif ($salida['est_salida'] == 2) {
                                                            $estado_salida_texto = 'RECEPCIONADA';
                                                            $estado_salida_clase = 'badge-primary';
                                                        } elseif ($salida['est_salida'] == 3) {
                                                            $estado_salida_texto = 'APROBADA';
                                                            $estado_salida_clase = 'badge-success';
                                                        } elseif ($salida['est_salida'] == 4) {
                                                            $estado_salida_texto = 'DENEGADA';
                                                            $estado_salida_clase = 'badge-dark';
                                                        } else {
                                                            $estado_salida_texto = 'DESCONOCIDO';
                                                            $estado_salida_clase = 'badge-secondary';
                                                        }

                                                        $fecha_salida = date('d/m/Y', strtotime($salida['fec_req_salida']));
                                                    ?>
                                                        <tr>
                                                            <td><strong>S00<?php echo $salida['id_salida']; ?></strong></td>
                                                            <td><?php echo htmlspecialchars($salida['nom_ubicacion_destino']); ?></td>
                                                            <td><?php echo $fecha_salida; ?></td>

                                                            <td>
                                                                <?php if ($salida['est_salida'] == 2) { ?>
                                                                    <?php echo htmlspecialchars($salida['nom_personal_recepciona']); ?>
                                                                    <?php if (!empty($salida['fec_aprueba_salida'])) { ?>
                                                                        <br><small class="text-muted">
                                                                            <?php echo date('d/m/Y H:i', strtotime($salida['fec_aprueba_salida'])); ?>
                                                                        </small>
                                                                    <?php } ?>
                                                                <?php } else { ?>
                                                                    <span class="text-muted">-</span>
                                                                <?php } ?>
                                                            </td>

                                                            <td>
                                                                <span class="badge <?php echo $estado_salida_clase; ?> badge_size">
                                                                    <?php echo $estado_salida_texto; ?>
                                                                </span>
                                                            </td>

                                                            <td>
                                                                <!-- Botón Ver Detalles - SIEMPRE VISIBLE -->
                                                                <button class="btn btn-info btn-xs btn-ver-salida"
                                                                        data-toggle="tooltip"
                                                                        title="Ver detalles completos de esta salida"
                                                                        data-id-salida="<?php echo $salida['id_salida']; ?>">
                                                                    <i class="fa fa-eye"></i>
                                                                </button>

                                                                <?php if ($salida['est_salida'] == 1) { ?>
                                                                    <!-- Solo en estado PENDIENTE: mostrar Editar y Anular -->
                                                                    <button class="btn btn-warning btn-xs ml-1 btn-editar-salida"
                                                                            data-toggle="tooltip"
                                                                            title="Editar esta orden de salida"
                                                                            data-id-salida="<?php echo $salida['id_salida']; ?>">
                                                                        <i class="fa fa-edit"></i>
                                                                    </button>

                                                                    <button class="btn btn-danger btn-xs ml-1 btn-anular-salida"
                                                                            data-toggle="tooltip"
                                                                            title="Anular Salida"
                                                                            data-id-salida="<?php echo $salida['id_salida']; ?>">
                                                                        <i class="fa fa-times"></i>
                                                                    </button>
                                                                <?php } ?>
                                                                
                                                                <?php if ($salida['est_salida'] == 0 || $salida['est_salida'] == 4) { ?>
                                                                    <!-- Estados ANULADA (0) o DENEGADA (4): botones deshabilitados -->
                                                                    <button class="btn btn-outline-secondary btn-xs ml-1 disabled" disabled>
                                                                        <i class="fa fa-edit"></i>
                                                                    </button>
                                                                    <button class="btn btn-outline-secondary btn-xs ml-1 disabled" disabled>
                                                                        <i class="fa fa-times"></i>
                                                                    </button>
                                                                <?php } ?>
                                                                
                                                                <!-- Estados 2 (RECEPCIONADA) y 3 (APROBADA): solo mostrar el ojo, sin más botones -->
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center p-3">
                                                            <i class="fa fa-truck fa-2x text-success mb-2"></i>
                                                            <h5 class="text-success">Sin salidas registradas</h5>
                                                            <p class="text-muted" style="font-size: 12px;">
                                                                Las salidas de almacén aparecerán aquí.
                                                            </p>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- FORMULARIO PARA CREAR/EDITAR ORDEN DE COMPRA  -->
                                <div id="contenedor-nueva-orden" <?php echo $modo_editar ? 'style="display: block;"' : 'style="display: none;"'; ?>>
                                    <form id="form-nueva-orden" method="POST" action="" enctype="multipart/form-data">
                                        <?php if ($modo_editar): ?>
                                        <input type="hidden" name="actualizar_orden" value="1">
                                        <input type="hidden" name="id_compra" value="<?php echo $id_compra_editar; ?>">
                                        <?php else: ?>
                                        <input type="hidden" name="crear_orden" value="1">
                                        <?php endif; ?>
                                        <input type="hidden" name="id" value="<?php echo $id_pedido; ?>">
                                        
                                        <div class="card">
                                            <div class="card-header" style="padding: 8px 12px; background-color: <?php echo $modo_editar ? '#fff3cd' : '#e3f2fd'; ?>;">
                                                <h6 class="mb-0">
                                                    <i class="fa <?php echo $modo_editar ? 'fa-edit text-warning' : 'fa-plus-circle text-primary'; ?>"></i>
                                                    <?php 
                                                    if ($modo_editar) {
                                                        echo 'Editar Orden';
                                                    } else {
                                                        echo ($pedido['id_producto_tipo'] == 2) ? 'Nueva Orden de Servicio' : 'Nueva Orden de Compra';
                                                    }
                                                    echo $modo_editar ? ' C00' . $id_compra_editar : ''; 
                                                    ?>
                                                </h6>
                                            </div>
                                            <div class="card-body" style="padding: 12px;">
                                                <div class="row mb-2">
                                                    <div class="col-md-6">
                                                        <label style="font-size: 11px; font-weight: bold;">Fecha: <span class="text-danger">*</span></label>
                                                        <input type="date" class="form-control form-control-sm" id="fecha_orden" name="fecha_orden" 
                                                            value="<?php echo $modo_editar && $orden_data ? date('Y-m-d', strtotime($orden_data['fec_compra'])) : date('Y-m-d'); ?>" 
                                                            min="<?php echo date('Y-m-d'); ?>"
                                                            style="font-size: 12px;" required>
                                                    </div>

                                                    <!-- Proveedor -->
                                                    <div class="col-md-5">
                                                        <div class="form-group mb-3">
                                                            <label class="mb-1" style="font-size:11px;font-weight:bold;">
                                                                Proveedor: <span class="text-danger">*</span>
                                                            </label>

                                                            <div class="proveedor-row d-flex align-items-center">
                                                                <select id="proveedor_orden" name="proveedor_orden"
                                                                        class="form-control form-control-sm flex-grow-1"
                                                                        style="font-size:12px;" required>
                                                                    <option value="">Seleccionar proveedor...</option>
                                                                    <?php foreach ($proveedor as $prov) {
                                                                        $sel = ($modo_editar && $orden_data && $orden_data['id_proveedor']==$prov['id_proveedor']) ? 'selected' : '';
                                                                        echo '<option value="'.htmlspecialchars($prov['id_proveedor']).'" '.$sel.'>'.
                                                                                htmlspecialchars($prov['nom_proveedor']).'</option>';
                                                                    } ?>
                                                                </select>

                                                                <button type="button"
                                                                        class="btn btn-info btn-sm btn-plus"
                                                                        id="btn-agregar-proveedor"
                                                                        data-toggle="tooltip"
                                                                        title="Agregar Proveedor">
                                                                    <i class="fa fa-plus"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-6">
                                                        <label style="font-size: 11px; font-weight: bold;">Moneda: <span class="text-danger">*</span></label>
                                                        <select class="form-control form-control-sm" id="moneda_orden" name="moneda_orden" 
                                                                style="font-size: 12px;" required>
                                                            <option value="">Seleccionar moneda...</option>
                                                            <?php
                                                            foreach ($moneda as $mon) {
                                                                $selected = ($modo_editar && $orden_data && $orden_data['id_moneda'] == $mon['id_moneda']) ? 'selected' : '';
                                                                echo '<option value="' . htmlspecialchars($mon['id_moneda']) . '" ' . $selected . '>' . htmlspecialchars($mon['nom_moneda']) . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <!-- Condición de Pago -->
                                                            <label style="font-size: 11px; font-weight: bold;">Condición de Pago (días):</label>
                                                            <input type="number" 
                                                                class="form-control form-control-sm" 
                                                                id="plazo_pago" 
                                                                name="plazo_pago"
                                                                value="<?php echo $modo_editar && $orden_data ? htmlspecialchars($orden_data['plaz_compra']) : ''; ?>"
                                                                placeholder="Dejar vacío o 0 para pago al contado"
                                                                min="0"
                                                                step="1"
                                                                style="font-size: 12px;">
                                                            <small class="form-text text-muted">
                                                                Si no ingresa Condición de Pago, se considera pago al contado (sin alertas)
                                                            </small>
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <!-- NUEVO: Plazo de Entrega -->
                                                    <div class="col-md-12">
                                                        <label style="font-size: 11px; font-weight: bold;">Plazo de Entrega:</label>
                                                        <input type="text" 
                                                            class="form-control form-control-sm" 
                                                            id="plazo_entrega" 
                                                            name="plazo_entrega"
                                                            value="<?php echo $modo_editar && $orden_data ? htmlspecialchars($orden_data['plaz_entrega'] ?? '') : ''; ?>"
                                                            placeholder="Plazo de Entrega..."
                                                            maxlength="255"
                                                            style="font-size: 12px;">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-12">
                                                        <label style="font-size: 11px; font-weight: bold;">Dirección de Envío:</label>
                                                        <textarea class="form-control form-control-sm" id="direccion_envio" name="direccion_envio"
                                                                rows="2" placeholder="Ingrese la dirección de envío..." 
                                                                style="font-size: 12px; resize: none;"><?php echo $modo_editar && $orden_data ? htmlspecialchars($orden_data['denv_compra']) : ''; ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-12">
                                                        <label style="font-size: 11px; font-weight: bold;">Observaciones:</label>
                                                        <textarea class="form-control form-control-sm" id="observaciones_orden" name="observaciones_orden"
                                                                rows="2" placeholder="Observaciones adicionales..." 
                                                                style="font-size: 12px; resize: none;"><?php echo $modo_editar && $orden_data ? htmlspecialchars($orden_data['obs_compra']) : ''; ?></textarea>
                                                    </div>
                                                </div>
                                                <!--
                                                <div class="row mb-2">
                                                    <div class="col-md-12">
                                                        <label style="font-size: 11px; font-weight: bold;">Tipo de Porte:</label>
                                                        <input type="text" class="form-control form-control-sm" id="tipo_porte" name="tipo_porte"
                                                            value="<?php echo $modo_editar && $orden_data ? htmlspecialchars($orden_data['port_compra']) : ''; ?>"
                                                            placeholder="Ej. Marítimo, Terrestre, Aéreo" style="font-size: 12px;">
                                                    </div>
                                                </div>
                                                -->
                                                <!-- SECCIÓN DE DETRACCIÓN, RETENCIÓN Y PERCEPCIÓN -->
                                                <div class="row mb-2">
                                                    <div class="col-md-12">
                                                        <div class="card" style="border: 1px solid #dee2e6;">
                                                            <div class="card-header" style="background-color: #f8f9fa; padding: 8px 12px; cursor: pointer;" 
                                                                data-toggle="collapse" 
                                                                data-target="#afectacionesCollapse"
                                                                aria-expanded="false"
                                                                aria-controls="afectacionesCollapse">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <h6 class="mb-0" style="font-size: 13px;">
                                                                        <i class="fa fa-percent text-info"></i> 
                                                                        Detracción, Retención y Percepción
                                                                    </h6>
                                                                    <i class="fa fa-chevron-down" id="icon-toggle-afectaciones"></i>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="collapse" id="afectacionesCollapse">
                                                                <div class="card-body" style="padding: 12px;">
                                                                    <!-- DETRACCIÓN -->
                                                                    <div class="mb-3">
                                                                        <label style="font-size: 11px; font-weight: bold;">Detracción:</label>
                                                                        <div id="contenedor-detracciones" style="padding: 8px; background-color: #fff3cd; border-radius: 4px; border: 1px solid #ffc107;">
                                                                            <?php
                                                                            $detracciones = ObtenerDetraccionesPorTipo('DETRACCION');
                                                                            $detraccion_seleccionada = ($modo_editar && isset($orden_data['id_detraccion'])) ? $orden_data['id_detraccion'] : null;
                                                                            
                                                                            if (!empty($detracciones)) {
                                                                                foreach ($detracciones as $detraccion) {
                                                                                    $checked = ($detraccion_seleccionada == $detraccion['id_detraccion']) ? 'checked' : '';
                                                                                    ?>
                                                                                    <div class="form-check" style="margin-bottom: 5px;">
                                                                                        <input class="form-check-input detraccion-checkbox" 
                                                                                            type="checkbox" 
                                                                                            name="id_detraccion" 
                                                                                            value="<?php echo $detraccion['id_detraccion']; ?>" 
                                                                                            data-porcentaje="<?php echo $detraccion['porcentaje']; ?>" 
                                                                                            data-nombre="<?php echo htmlspecialchars($detraccion['nombre_detraccion']); ?>"
                                                                                            id="detraccion_<?php echo $detraccion['id_detraccion']; ?>" 
                                                                                            <?php echo $checked; ?>>
                                                                                        <label class="form-check-label" 
                                                                                            for="detraccion_<?php echo $detraccion['id_detraccion']; ?>" 
                                                                                            style="font-size: 12px; cursor: pointer;">
                                                                                            <?php echo htmlspecialchars($detraccion['nombre_detraccion']); ?> 
                                                                                            <strong>(<?php echo $detraccion['porcentaje']; ?>%)</strong>
                                                                                        </label>
                                                                                    </div>
                                                                                    <?php
                                                                                }
                                                                            } else {
                                                                                echo '<p class="text-muted" style="font-size: 11px; margin: 0;"><i class="fa fa-info-circle"></i> No hay detracciones configuradas</p>';
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                        <small class="form-text text-muted">Se aplica sobre el total después de IGV</small>
                                                                    </div>

                                                                    <!-- RETENCIÓN -->
                                                                    <div class="mb-3">
                                                                        <label style="font-size: 11px; font-weight: bold;">Retención:</label>
                                                                        <div id="contenedor-retenciones" style="padding: 8px; background-color: #e7f3ff; border-radius: 4px; border: 1px solid #2196f3;">
                                                                            <?php
                                                                            $retenciones = ObtenerDetraccionesPorTipo('RETENCION');
                                                                            $retencion_seleccionada = ($modo_editar && isset($orden_data['id_retencion'])) ? $orden_data['id_retencion'] : null;
                                                                            
                                                                            if (!empty($retenciones)) {
                                                                                foreach ($retenciones as $retencion) {
                                                                                    $checked = ($retencion_seleccionada == $retencion['id_detraccion']) ? 'checked' : '';
                                                                                    ?>
                                                                                    <div class="form-check" style="margin-bottom: 5px;">
                                                                                        <input class="form-check-input retencion-checkbox" 
                                                                                            type="checkbox" 
                                                                                            name="id_retencion" 
                                                                                            value="<?php echo $retencion['id_detraccion']; ?>" 
                                                                                            data-porcentaje="<?php echo $retencion['porcentaje']; ?>" 
                                                                                            data-nombre="<?php echo htmlspecialchars($retencion['nombre_detraccion']); ?>"
                                                                                            id="retencion_<?php echo $retencion['id_detraccion']; ?>" 
                                                                                            <?php echo $checked; ?>>
                                                                                        <label class="form-check-label" 
                                                                                            for="retencion_<?php echo $retencion['id_detraccion']; ?>" 
                                                                                            style="font-size: 12px; cursor: pointer;">
                                                                                            <?php echo htmlspecialchars($retencion['nombre_detraccion']); ?> 
                                                                                            <strong>(<?php echo $retencion['porcentaje']; ?>%)</strong>
                                                                                        </label>
                                                                                    </div>
                                                                                    <?php
                                                                                }
                                                                            } else {
                                                                                echo '<p class="text-muted" style="font-size: 11px; margin: 0;"><i class="fa fa-info-circle"></i> No hay retenciones configuradas</p>';
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                        <small class="form-text text-muted">Se aplica sobre el total después de IGV</small>
                                                                    </div>

                                                                    <!-- PERCEPCIÓN -->
                                                                    <div class="mb-2">
                                                                        <label style="font-size: 11px; font-weight: bold;">Percepción:</label>
                                                                        <div id="contenedor-percepciones" style="padding: 8px; background-color: #e8f5e9; border-radius: 4px; border: 1px solid #4caf50;">
                                                                            <?php
                                                                            $percepciones = ObtenerDetraccionesPorTipo('PERCEPCION');
                                                                            $percepcion_seleccionada = ($modo_editar && isset($orden_data['id_percepcion'])) ? $orden_data['id_percepcion'] : null;
                                                                            
                                                                            if (!empty($percepciones)) {
                                                                                foreach ($percepciones as $percepcion) {
                                                                                    $checked = ($percepcion_seleccionada == $percepcion['id_detraccion']) ? 'checked' : '';
                                                                                    ?>
                                                                                    <div class="form-check" style="margin-bottom: 5px;">
                                                                                        <input class="form-check-input percepcion-checkbox" 
                                                                                            type="checkbox" 
                                                                                            name="id_percepcion" 
                                                                                            value="<?php echo $percepcion['id_detraccion']; ?>" 
                                                                                            data-porcentaje="<?php echo $percepcion['porcentaje']; ?>" 
                                                                                            data-nombre="<?php echo htmlspecialchars($percepcion['nombre_detraccion']); ?>"
                                                                                            id="percepcion_<?php echo $percepcion['id_detraccion']; ?>" 
                                                                                            <?php echo $checked; ?>>
                                                                                        <label class="form-check-label" 
                                                                                            for="percepcion_<?php echo $percepcion['id_detraccion']; ?>" 
                                                                                            style="font-size: 12px; cursor: pointer;">
                                                                                            <?php echo htmlspecialchars($percepcion['nombre_detraccion']); ?> 
                                                                                            <strong>(<?php echo $percepcion['porcentaje']; ?>%)</strong>
                                                                                        </label>
                                                                                    </div>
                                                                                    <?php
                                                                                }
                                                                            } else {
                                                                                echo '<p class="text-muted" style="font-size: 11px; margin: 0;"><i class="fa fa-info-circle"></i> No hay percepciones configuradas</p>';
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                        <small class="form-text text-muted">Se aplica sobre el total después de IGV</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div id="contenedor-items-orden" class="mb-3">
                                            <?php if ($modo_editar && !empty($orden_detalle)): ?>
                                                <?php foreach ($orden_detalle as $item): 
                                                    // CORRECCIÓN: Obtener id_pedido_detalle del item
                                                    $id_pedido_detalle = isset($item['id_pedido_detalle']) ? $item['id_pedido_detalle'] : 0;
                                                    
                                                    // OBTENER DATOS DE VALIDACIÓN PARA ESTE DETALLE ESPECÍFICO
                                                    $cantidad_verificada_item = 0;
                                                    $cantidad_ordenada_item = 0;

                                                    // Buscar por id_pedido_detalle
                                                    foreach ($pedido_detalle as $detalle) {
                                                        if ($detalle['id_pedido_detalle'] == $id_pedido_detalle) {
                                                            //  PARA BASE ARCE: usar cantidad pedida, NO verificada
                                                            if ($es_pedido_base_arce) {
                                                                $cantidad_verificada_item = isset($detalle['cant_pedido_detalle']) ? floatval($detalle['cant_pedido_detalle']) : 0;
                                                            } else {
                                                                $cantidad_verificada_item = isset($detalle['cant_oc_pedido_detalle']) ? floatval($detalle['cant_oc_pedido_detalle']) : 0;
                                                            }
                                                            $cantidad_ordenada_item = isset($detalle['cantidad_ya_ordenada']) ? floatval($detalle['cantidad_ya_ordenada']) : 0;
                                                            break;
                                                        }
                                                    }
                                                ?>
                                                <div class="alert alert-light p-2 mb-2" id="item-orden-<?php echo $item['id_compra_detalle']; ?>">
                                                    <!-- CRÍTICO: Guardar id_pedido_detalle -->
                                                    <input type="hidden" name="items_orden[<?php echo $item['id_compra_detalle']; ?>][id_compra_detalle]" value="<?php echo $item['id_compra_detalle']; ?>">
                                                    <input type="hidden" name="items_orden[<?php echo $item['id_compra_detalle']; ?>][id_pedido_detalle]" value="<?php echo $id_pedido_detalle; ?>">
                                                    <input type="hidden" name="items_orden[<?php echo $item['id_compra_detalle']; ?>][id_producto]" value="<?php echo $item['id_producto']; ?>">
                                                    <input type="hidden" name="items_orden[<?php echo $item['id_compra_detalle']; ?>][es_nuevo]" value="0">
                                                    <input type="hidden" name="items_orden[<?php echo $item['id_compra_detalle']; ?>][es_base_arce]" value="<?php echo $es_pedido_base_arce ? '1' : '0'; ?>">
                                                    <!-- Descripción del producto -->
                                                    <div class="row align-items-center mb-2">
                                                        <div class="col-md-11">
                                                            <div style="font-size: 12px;">
                                                                <strong>Descripción:</strong> <?php echo htmlspecialchars($item['nom_producto']); ?>
                                                                <?php if ($modo_editar): ?>
                                                                <span class="badge badge-info badge-sm ml-1">EDITANDO</span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1 text-right">
                                                            <button type="button" class="btn btn-danger btn-sm btn-remover-item" 
                                                                    data-id-detalle="<?php echo $item['id_compra_detalle']; ?>"
                                                                    data-id-compra-detalle="<?php echo $item['id_compra_detalle']; ?>">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <!-- Requisitos SST/MA/CA -->
                                                    <div class="row mt-2 mb-2">
                                                        <div class="col-md-12">
                                                            <label style="font-size: 11px; font-weight: bold; margin-bottom: 4px; display: block; color: #495057;">
                                                                <i></i> Requisitos SST/MA/CA:
                                                            </label>
                                                            <?php 
                                                            // 🔹 CORRECCIÓN: Mostrar req_compra_detalle si existe, sino req_pedido
                                                            $req_texto = '';
                                                            
                                                            // Primero intentar obtener de la orden (si ya fue guardado)
                                                            if (!empty($item['req_compra_detalle'])) {
                                                                $req_texto = $item['req_compra_detalle'];
                                                            } 
                                                            // Si no existe en la orden, buscar en el pedido original
                                                            else {
                                                                foreach ($pedido_detalle as $det) {
                                                                    if ($det['id_pedido_detalle'] == $id_pedido_detalle) {
                                                                        $req_texto = !empty($det['req_pedido']) ? $det['req_pedido'] : '';
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                            <input type="text"
                                                            class="form-control form-control-sm" 
                                                            name="req_compra[<?php echo $id_pedido_detalle; ?>]"
                                                            value="<?php echo htmlspecialchars($req_texto); ?>"
                                                            style="font-size: 11px;"
                                                            placeholder="Requisitos de seguridad, medio ambiente y calidad...">
                                                        </div>
                                                    </div>
                                                    <div style="border-top: 1px dashed #dee2e6; margin: 10px 0;"></div>
                                                    <!-- Campos en una sola línea: Cantidad, Precio, IGV, Homologación -->
                                                    <div class="row">
                                                        <!-- Cantidad -->
                                                        <div class="col-md-2">
                                                            <label style="font-size: 11px; font-weight: bold; margin-bottom: 4px; display: block;">Cantidad:</label>
                                                            <input type="number" 
                                                                class="form-control form-control-sm cantidad-item" 
                                                                name="items_orden[<?php echo $item['id_compra_detalle']; ?>][cantidad]"
                                                                data-id-detalle="<?php echo $item['id_compra_detalle']; ?>"
                                                                data-id-producto="<?php echo $item['id_producto']; ?>"
                                                                data-cantidad-verificada="<?php echo $cantidad_verificada_item; ?>"
                                                                data-cantidad-ordenada="<?php echo $cantidad_ordenada_item; ?>"
                                                                data-es-base-arce="<?php echo $es_pedido_base_arce ? '1' : '0'; ?>"
                                                                value="<?php echo $item['cant_compra_detalle']; ?>"
                                                                min="0.01" 
                                                                step="0.01"
                                                                style="font-size: 12px;"
                                                                required>
                                                        </div>
                                                        
                                                        <!-- Precio Unitario -->
                                                        <div class="col-md-2">
                                                            <label style="font-size: 11px; font-weight: bold; margin-bottom: 4px; display: block;">Precio Unit.:</label>
                                                            <div class="input-group input-group-sm">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" style="font-size: 11px; background-color: #f8f9fa; border: 1px solid #ced4da;">
                                                                        <?php echo ($orden_data['id_moneda'] == 1) ? 'S/.' : 'US$'; ?>
                                                                    </span>
                                                                </div>
                                                                <input type="number" 
                                                                    class="form-control form-control-sm precio-item" 
                                                                    name="items_orden[<?php echo $item['id_compra_detalle']; ?>][precio_unitario]"
                                                                    data-id-detalle="<?php echo $item['id_compra_detalle']; ?>"
                                                                    value="<?php echo $item['prec_compra_detalle']; ?>"
                                                                    step="0.01" 
                                                                    min="0"
                                                                    style="font-size: 11px;"
                                                                    required>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- IGV (%) -->
                                                        <div class="col-md-2">
                                                            <label style="font-size: 11px; font-weight: bold; margin-bottom: 4px; display: block;">IGV (%):</label>
                                                            <input type="number" 
                                                                class="form-control form-control-sm igv-item" 
                                                                name="items_orden[<?php echo $item['id_compra_detalle']; ?>][igv]"
                                                                data-id-detalle="<?php echo $item['id_compra_detalle']; ?>"
                                                                value="<?php echo $item['igv_compra_detalle'] ?? 18; ?>"
                                                                min="0" 
                                                                max="100"
                                                                step="0.01"
                                                                style="font-size: 12px;"
                                                                required>
                                                        </div>
            
                                                        
                                                        <!-- Homologación -->
                                                        <div class="col-md-3">
                                                            <label style="font-size: 11px; font-weight: bold; margin-bottom: 4px; display: block;">Homologación:</label>
                                                            
                                                            <?php if (!empty($item['hom_compra_detalle'])): ?>
                                                                <div style="margin-bottom: 5px; padding: 5px; background-color: #d4edda; border-radius: 4px; border: 1px solid #c3e6cb;">
                                                                    <a href="../_archivos/homologaciones/<?php echo htmlspecialchars($item['hom_compra_detalle']); ?>" 
                                                                    target="_blank" 
                                                                    class="text-success" 
                                                                    style="font-size: 11px; display: block; text-decoration: none;">
                                                                        <i class="fa fa-file-pdf-o"></i> 
                                                                        <strong>Archivo actual:</strong>
                                                                        <br>
                                                                        <small class="text-muted"><?php echo htmlspecialchars($item['hom_compra_detalle']); ?></small>
                                                                    </a>
                                                                </div>
                                                                <small class="text-info d-block mb-2" style="font-size: 10px;">
                                                                    <i class="fa fa-info-circle"></i> Subir nuevo archivo para reemplazar
                                                                </small>
                                                            <?php endif; ?>
                                                            
                                                            <input type="file" 
                                                                class="form-control-file" 
                                                                name="homologacion[<?php echo $item['id_compra_detalle']; ?>]"
                                                                accept=".pdf,.jpg,.jpeg,.png"
                                                                style="font-size: 11px;">
                                                            
                                                            <?php if (empty($item['hom_compra_detalle'])): ?>
                                                                <small class="text-muted" style="font-size: 10px;">PDF, JPG, PNG (máx. 5MB)</small>
                                                            <?php endif; ?>
                                                        </div>
                                                        
                                                        <!-- Cálculos (Subtotal, IGV, Total) -->
                                                        <div class="col-md-3 text-right">
                                                            <label style="font-size: 11px; font-weight: bold; margin-bottom: 4px; display: block; visibility: hidden;">-</label>
                                                            <div class="calculo-item" id="calculo-<?php echo $item['id_compra_detalle']; ?>" 
                                                                style="font-size: 11px; line-height: 1.4;">
                                                                <div class="subtotal-text">Subtotal: --</div>
                                                                <div class="igv-text">IGV: --</div>
                                                                <div class="total-text" style="font-weight: bold; color: #28a745;">Total: --</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="text-center mt-2" style="padding: 8px;">
                                            <a href="pedido_verificar.php?id=<?php echo $id_pedido; ?>" class="btn btn-secondary btn-sm mr-2">
                                                <i class="fa fa-times"></i> Cancelar
                                            </a>
                                            <button type="submit" data-toggle="tooltip" class="btn btn-<?php echo $modo_editar ? 'warning' : 'primary'; ?> btn-sm">
                                                <i class="fa fa-save"></i> <?php echo $modo_editar ? 'Actualizar Orden' : 'Guardar Orden'; ?>
                                            </button>
                                        </div>
                                    </form>
                                </div>

                        <!-- FORMULARIO PARA CREAR/EDITAR SALIDA -->
                        <div id="contenedor-nueva-salida" <?php echo $modo_editar_salida ? 'style="display: block;"' : 'style="display: none;"'; ?>>
                            <form id="form-nueva-salida" method="POST" action="" enctype="multipart/form-data">
                                <?php if ($modo_editar_salida): ?>
                                <input type="hidden" name="actualizar_salida" value="1">
                                <input type="hidden" name="id_salida" value="<?php echo $id_salida_editar; ?>">
                                <?php else: ?>
                                <input type="hidden" name="crear_salida" value="1">
                                <?php endif; ?>
                                <input type="hidden" name="id_pedido" value="<?php echo $id_pedido; ?>">
                                
                                <div class="card">
                                    <div class="card-header" style="padding: 8px 12px; background-color: <?php echo $modo_editar_salida ? '#fff3cd' : '#d4edda'; ?>;">
                                        <h6 class="mb-0">
                                            <i class="fa <?php echo $modo_editar_salida ? 'fa-edit text-warning' : 'fa-truck text-success'; ?>"></i>
                                            <?php echo $modo_editar_salida ? 'Editar Salida S00' . $id_salida_editar : 'Nueva Salida'; ?>
                                        </h6>
                                    </div>
                                    <div class="card-body" style="padding: 12px;">
                                        <!-- Fecha de la salida -->
                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <label style="font-size: 11px; font-weight: bold;">Fecha Requerida de Salida: <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control form-control-sm" id="fecha_salida" name="fecha_salida" 
                                                            value="<?php 
                                                                if ($modo_editar_salida && isset($salida_data)) {
                                                                    echo date('Y-m-d', strtotime($salida_data['fec_req_salida']));
                                                                } else {
                                                                    //echo date('Y-m-d', strtotime($pedido['fec_req_pedido']));
                                                                    echo date('Y-m-d');
                                                                }
                                                            ?>" 
                                                    style="font-size: 12px;" required>
                                            </div>
                                            
                                            <!--  Centro de Costo del Solicitante -->
                                            <div class="col-md-6">
                                                <label style="font-size: 11px; font-weight: bold;">Centro de Costo: <span class="text-danger">*</span></label>
                                                <?php if ($centro_costo_usuario): ?>
                                                    <input type="text" class="form-control form-control-sm" 
                                                        value="<?php echo htmlspecialchars($centro_costo_usuario['nom_centro_costo']); ?>" 
                                                        readonly 
                                                        style="font-size: 12px; background-color: #e9ecef; font-weight: 500;">
                                                    <input type="hidden" name="id_centro_costo" 
                                                        value="<?php echo $centro_costo_usuario['id_centro_costo']; ?>">
                                                <?php else: ?>
                                                    <input type="text" class="form-control form-control-sm" 
                                                        value="Sin centro de costo asignado" 
                                                        readonly 
                                                        style="font-size: 12px; background-color: #f8d7da; color: #721c24;">
                                                    <input type="hidden" name="id_centro_costo" value="">
                                                    <small class="text-danger" style="font-size: 10px;">No tienes un área asignada. Contacta con el administrador.</small>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- ALMACÉN Y UBICACIÓN ORIGEN -->
                                        <div class="row mb-2">
                                            <!-- Almacén Origen -->
                                            <div class="col-md-6">
                                                <label style="font-size: 11px; font-weight: bold;">Almacén Origen: <span class="text-danger">*</span></label>
                                                <select class="form-control form-control-sm" 
                                                    id="almacen_origen_salida" 
                                                    name="almacen_origen_salida" 
                                                    style="font-size: 12px; background-color: #e9ecef; pointer-events: none;" 
                                                    required>
                                                    <option value="">Seleccionar almacén...</option>
                                                    <?php foreach ($almacenes as $alm) { 
                                                        $selected_origen = '';
                                                        if ($modo_editar_salida && isset($salida_data) && $salida_data['id_almacen_origen'] == $alm['id_almacen']) {
                                                            $selected_origen = 'selected';
                                                        } elseif (!$modo_editar_salida && $alm['id_almacen'] == $pedido['id_almacen']) {
                                                            $selected_origen = 'selected';
                                                        }
                                                    ?>
                                                        <option value="<?php echo $alm['id_almacen']; ?>" <?php echo $selected_origen; ?>>
                                                            <?php echo htmlspecialchars($alm['nom_almacen']); ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <!-- Ubicación Origen -->
                                            <div class="col-md-6">
                                                <label style="font-size: 11px; font-weight: bold;">Ubicación Origen: <span class="text-danger">*</span></label>
                                                <select class="form-control form-control-sm" 
                                                        id="ubicacion_origen_salida" 
                                                        name="ubicacion_origen_salida" 
                                                        style="font-size: 12px;" 
                                                        required>
                                                    <option value="">Seleccionar ubicación...</option>
                                                    <?php 
                                                    if (!empty($pedido_detalle)) {
                                                        $primer_detalle = $pedido_detalle[0];
                                                        
                                                        // Obtener ubicaciones bloqueadas
                                                        $ubicaciones_bloqueadas_form = ObtenerUbicacionesBloqueadasPorDenegacion(
                                                            $primer_detalle['id_producto'],
                                                            $primer_detalle['id_pedido_detalle']
                                                        );
                                                        
                                                        // Crear array de keys bloqueadas para búsqueda rápida
                                                        $bloqueadas_keys = [];
                                                        foreach ($ubicaciones_bloqueadas_form as $key => $info) {
                                                            $bloqueadas_keys[$key] = true;
                                                        }
                                                        
                                                        // Obtener todas las ubicaciones con stock
                                                        $ubicaciones_disponibles = ObtenerOtrasUbicacionesConStock(
                                                            $primer_detalle['id_producto'],
                                                            $pedido['id_almacen'],
                                                            $pedido['id_ubicacion']
                                                        );
                                                        
                                                        // Validar que sea un array
                                                        if (!is_array($ubicaciones_disponibles)) {
                                                            $ubicaciones_disponibles = [];
                                                        }
                                                        
                                                        // Ordenar por stock descendente (mayor a menor)
                                                        if (!empty($ubicaciones_disponibles)) {
                                                            usort($ubicaciones_disponibles, function($a, $b) {
                                                                return floatval($b['stock']) <=> floatval($a['stock']);
                                                            });
                                                        }
                                                        
                                                        $id_ubicacion_origen_preseleccionada = null;
                                                        if ($modo_editar_salida && isset($salida_data)) {
                                                            $id_ubicacion_origen_preseleccionada = $salida_data['id_ubicacion_origen'];
                                                        } elseif (!empty($ubicaciones_disponibles)) {
                                                            // Preseleccionar la primera ubicación no bloqueada (ya está ordenada por mayor stock)
                                                            foreach ($ubicaciones_disponibles as $ub) {
                                                                $key_ub = $ub['id_almacen'] . '_' . $ub['id_ubicacion'];
                                                                if (!isset($bloqueadas_keys[$key_ub])) {
                                                                    $id_ubicacion_origen_preseleccionada = $ub['id_ubicacion'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        
                                                        foreach ($ubicaciones_disponibles as $ub) {
                                                            // Verificar si está bloqueada
                                                            $key_ub = $ub['id_almacen'] . '_' . $ub['id_ubicacion'];
                                                            $estaBloqueada = isset($bloqueadas_keys[$key_ub]);
                                                            
                                                            $selected = '';
                                                            if ($id_ubicacion_origen_preseleccionada && $id_ubicacion_origen_preseleccionada == $ub['id_ubicacion']) {
                                                                $selected = 'selected';
                                                            }
                                                            
                                                            // Solo mostrar nombre de ubicación
                                                            $icono = $estaBloqueada ? '🚫 ' : '';
                                                            $texto_opcion = sprintf(
                                                                "%s%s",  
                                                                $icono,
                                                                $ub['nom_ubicacion']
                                                            );
                                                            
                                                            // Si está bloqueada, deshabilitar la opción
                                                            $disabled = $estaBloqueada ? 'disabled' : '';
                                                            $estilo = $estaBloqueada ? 'style="color: #dc3545; font-style: italic;"' : '';
                                                            
                                                            echo sprintf(
                                                                '<option value="%d" data-id-almacen="%d" data-stock="%s" %s %s %s>%s</option>',
                                                                $ub['id_ubicacion'],
                                                                $ub['id_almacen'],
                                                                number_format($ub['stock'], 2, '.', ''),  
                                                                $selected,
                                                                $disabled,
                                                                $estilo,
                                                                htmlspecialchars($texto_opcion)
                                                            );
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- ALMACÉN Y UBICACIÓN DESTINO -->
                                        <div class="row mb-2">
                                            <!-- Almacén Destino -->
                                            <div class="col-md-6">
                                                <label style="font-size: 11px; font-weight: bold;">Almacén Destino: <span class="text-danger">*</span></label>
                                                <select class="form-control form-control-sm" 
                                                        id="almacen_destino_salida" 
                                                        name="almacen_destino_salida" 
                                                        style="font-size: 12px; background-color: #e9ecef; pointer-events: none;" 
                                                        required>
                                                    <option value="">Seleccionar almacén...</option>
                                                    <?php foreach ($almacenes as $alm) { 
                                                        $selected_destino = '';
                                                        if ($modo_editar_salida && isset($salida_data) && $salida_data['id_almacen_destino'] == $alm['id_almacen']) {
                                                            $selected_destino = 'selected';
                                                        } elseif (!$modo_editar_salida && $alm['id_almacen'] == $pedido['id_almacen']) {
                                                            $selected_destino = 'selected';
                                                        }
                                                    ?>
                                                        <option value="<?php echo $alm['id_almacen']; ?>" <?php echo $selected_destino; ?>>
                                                            <?php echo htmlspecialchars($alm['nom_almacen']); ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <!-- Ubicación Destino -->
                                            <div class="col-md-6">
                                                <label style="font-size: 11px; font-weight: bold;">Ubicación Destino: <span class="text-danger">*</span></label>
                                                <select class="form-control form-control-sm" 
                                                        id="ubicacion_destino_salida" 
                                                        name="ubicacion_destino_salida" 
                                                        style="font-size: 12px; background-color: #e9ecef; pointer-events: none;" 
                                                        required>
                                                    <option value="">Seleccionar ubicación...</option>
                                                    <?php foreach ($ubicaciones as $ubi) { 
                                                        $selected_ubi_destino = '';
                                                        if ($modo_editar_salida && isset($salida_data) && $salida_data['id_ubicacion_destino'] == $ubi['id_ubicacion']) {
                                                            $selected_ubi_destino = 'selected';
                                                        } elseif (!$modo_editar_salida && $ubi['id_ubicacion'] == $pedido['id_ubicacion']) {
                                                            $selected_ubi_destino = 'selected';
                                                        }
                                                    ?>
                                                        <option value="<?php echo $ubi['id_ubicacion']; ?>" <?php echo $selected_ubi_destino; ?>>
                                                            <?php echo htmlspecialchars($ubi['nom_ubicacion']); ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- PERSONAL ENCARGADO Y RECEPTOR CON CENTRO DE COSTO -->
                                        <div class="row mb-2">
                                            <!-- ✅ Personal Encargado CON Centro de Costo -->
                                            <div class="col-md-6">
                                                <label style="font-size: 11px; font-weight: bold;">
                                                    Personal Encargado: <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-control form-control-sm" 
                                                        id="personal_encargado_salida" 
                                                        name="personal_encargado_salida" 
                                                        style="font-size: 12px;" 
                                                        required>
                                                    <option value="">Seleccionar personal...</option>
                                                    <?php 
                                                    if (isset($personal_lista) && !empty($personal_lista)) {
                                                        foreach ($personal_lista as $pers) {
                                                            $selected = '';
                                                            
                                                            // Preseleccionar usuario de la sesión en modo creación
                                                            if (!$modo_editar_salida && $pers['id_personal'] == $id_personal) {
                                                                $selected = 'selected';
                                                            }
                                                            
                                                            // En modo edición, respetar valor guardado
                                                            if ($modo_editar_salida && isset($salida_data)) {
                                                                if ($salida_data['id_personal_encargado'] == $pers['id_personal']) {
                                                                    $selected = 'selected';
                                                                }
                                                            }
                                                            ?>
                                                            <option value="<?php echo $pers['id_personal']; ?>" 
                                                                    data-centro-costo="<?php 
                                                                        if (isset($centros_costo_personal[$pers['id_personal']])) {
                                                                            echo htmlspecialchars($centros_costo_personal[$pers['id_personal']]['nom_centro_costo']);
                                                                        } else {
                                                                            echo '';
                                                                        }
                                                                    ?>"
                                                                    data-email="<?php echo htmlspecialchars($pers['email_personal']); ?>" 
                                                                    <?php echo $selected; ?>>
                                                                <?php echo htmlspecialchars($pers['nom_personal']); ?>
                                                            </option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                
                                                <!-- ✅ Info de centro de costo del encargado -->
                                                <small class="form-text text-muted" id="info-centro-costo-encargado" style="display: none; font-size: 10px;">
                                                    <strong>Centro de Costo:</strong> <span id="texto-centro-costo-encargado"></span>
                                                </small>
                                            </div>

                                            <!-- ✅ Personal que Recibe CON Centro de Costo -->
                                            <div class="col-md-6">
                                                <label style="font-size: 11px; font-weight: bold;">
                                                    Personal que Recibe: <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-control form-control-sm" 
                                                        id="personal_recibe_salida" 
                                                        name="personal_recibe_salida" 
                                                        style="font-size: 12px;"
                                                        required>
                                                    <option value="">Seleccionar personal...</option>
                                                    <?php 
                                                    if (isset($personal_lista) && !empty($personal_lista)) {
                                                        foreach ($personal_lista as $pers) {
                                                            $selected = '';
                                                            
                                                            // En modo edición, respetar valor guardado
                                                            if ($modo_editar_salida && isset($salida_data)) {
                                                                if ($salida_data['id_personal_recibe'] == $pers['id_personal']) {
                                                                    $selected = 'selected';
                                                                }
                                                            }
                                                            ?>
                                                            <option value="<?php echo $pers['id_personal']; ?>" 
                                                                    data-centro-costo="<?php 
                                                                        if (isset($centros_costo_personal[$pers['id_personal']])) {
                                                                            echo htmlspecialchars($centros_costo_personal[$pers['id_personal']]['nom_centro_costo']);
                                                                        } else {
                                                                            echo '';
                                                                        }
                                                                    ?>"
                                                                    <?php echo $selected; ?>>
                                                                <?php echo htmlspecialchars($pers['nom_personal']); ?>
                                                            </option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                
                                                <!-- ✅ Info de centro de costo del receptor -->
                                                <small class="form-text text-muted" id="info-centro-costo-recibe" style="display: none; font-size: 10px;">
                                                    <strong>Centro de Costo:</strong> <span id="texto-centro-costo-recibe"></span>
                                                </small>
                                            </div>
                                        </div>

                                        <!-- Observaciones -->
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <label style="font-size: 11px; font-weight: bold;">Observaciones:</label>
                                                <textarea class="form-control form-control-sm" id="observaciones_salida" name="observaciones_salida"
                                                        rows="2" placeholder="Observaciones adicionales..." 
                                                        style="font-size: 12px; resize: none;"><?php echo ($modo_editar_salida && isset($salida_data)) ? htmlspecialchars($salida_data['obs_salida']) : ''; ?></textarea>
                                            </div>
                                        </div>

                                        <!-- Subir Documentos -->
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <label style="font-size: 11px; font-weight: bold;">
                                                    Subir Documentos:
                                                </label>
                                                <input type="file" name="documentos_salida[]" id="documentos_salida" 
                                                    class="form-control form-control-sm" 
                                                    multiple 
                                                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                                    style="font-size: 12px;">
                                                <small class="text-muted" style="font-size: 10px;">
                                                    <i class="fa fa-info-circle"></i> 
                                                    <strong>Opcional.</strong> 
                                                    Formatos permitidos: PDF, JPG, PNG, DOC, DOCX. Máximo 5MB por archivo.
                                                </small>
                                                
                                                <?php if ($modo_editar_salida && isset($documentos_salida) && !empty($documentos_salida)): ?>
                                                <!-- MOSTRAR DOCUMENTOS EXISTENTES -->
                                                <div class="mt-2">
                                                    <?php foreach ($documentos_salida as $doc): 
                                                        $ruta_archivo = "../uploads/salidas/" . $doc['documento'];
                                                        $extension = strtolower(pathinfo($doc['documento'], PATHINFO_EXTENSION));
                                                        
                                                        // Determinar icono según extensión
                                                        $icono = 'fa-file';
                                                        if ($extension == 'pdf') {
                                                            $icono = 'fa-file-pdf-o';
                                                        } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                                            $icono = 'fa-file-image-o';
                                                        } elseif (in_array($extension, ['doc', 'docx'])) {
                                                            $icono = 'fa-file-word-o';
                                                        } elseif (in_array($extension, ['xls', 'xlsx'])) {
                                                            $icono = 'fa-file-excel-o';
                                                        }
                                                        
                                                        // Nombre amigable (sin prefijo de timestamp)
                                                        $nombre_mostrar = preg_replace('/^salidas_\d+_\d+_\d+_/', '', $doc['documento']);
                                                    ?>
                                                    <div style="margin-bottom: 5px; padding: 5px; background-color: #d4edda; border-radius: 4px; border: 1px solid #c3e6cb;">
                                                        <a href="<?php echo $ruta_archivo; ?>" 
                                                        target="_blank" 
                                                        class="text-success" 
                                                        style="font-size: 11px; display: block; text-decoration: none;">
                                                            <i class="fa <?php echo $icono; ?>"></i> 
                                                            <strong>Archivo actual:</strong>
                                                            <br>
                                                            <small class="text-muted"><?php echo htmlspecialchars($nombre_mostrar); ?></small>
                                                        </a>
                                                    </div>
                                                    <?php endforeach; ?>
                                                    <small class="text-info d-block mb-2" style="font-size: 10px;">
                                                        <i class="fa fa-info-circle"></i> Subir nuevos archivos para agregar más documentos
                                                    </small>
                                                </div>
                                                <?php elseif ($modo_editar_salida): ?>
                                                <div class="alert alert-warning mt-2" style="padding: 8px; font-size: 11px; margin-bottom: 0;">
                                                    <i class="fa fa-exclamation-triangle"></i> 
                                                    <strong>Sin documentos:</strong> Esta salida no tiene archivos adjuntos. Puedes agregar nuevos arriba.
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                                
                                        <!-- Contenedor de items de salida -->
                                        <div id="contenedor-items-salida" class="mb-3">
                                            <?php if ($modo_editar_salida && !empty($salida_detalle)): ?>
                                                <?php foreach ($salida_detalle as $item): 
                                                    $cantidad_maxima = isset($item['cantidad_maxima']) ? floatval($item['cantidad_maxima']) : 0;
                                                    $cant_actual_en_salida = floatval($item['cant_salida_detalle']);
                                                    
                                                    // Obtener cantidad OS original (sin límite de stock)
                                                    $cant_os_original = 0;
                                                    foreach ($pedido_detalle as $det) {
                                                        if ($det['id_pedido_detalle'] == $item['id_pedido_detalle']) {
                                                            $cant_os_original = floatval($det['cant_os_pedido_detalle']);
                                                            break;
                                                        }
                                                    }
                                                    error_log("📝 HTML: Item '{$item['nom_producto']}' | Max: $cantidad_maxima | Actual: $cant_actual_en_salida");
                                                ?>
                                                <div class="alert alert-light p-2 mb-2" id="item-salida-<?php echo $item['id_salida_detalle']; ?>">
                                                    <input type="hidden" name="items_salida[<?php echo $item['id_salida_detalle']; ?>][id_salida_detalle]" value="<?php echo $item['id_salida_detalle']; ?>">
                                                    <input type="hidden" name="items_salida[<?php echo $item['id_salida_detalle']; ?>][id_pedido_detalle]" value="<?php echo $item['id_pedido_detalle']; ?>">
                                                    <input type="hidden" name="items_salida[<?php echo $item['id_salida_detalle']; ?>][id_producto]" value="<?php echo $item['id_producto']; ?>">
                                                    <input type="hidden" name="items_salida[<?php echo $item['id_salida_detalle']; ?>][es_nuevo]" value="0">
                                                    <input type="hidden" name="items_salida[<?php echo $item['id_salida_detalle']; ?>][descripcion]" value="<?php echo htmlspecialchars($item['nom_producto']); ?>">
                                                    
                                                    <div class="row align-items-center mb-2">
                                                        <div class="col-md-11">
                                                            <div style="font-size: 12px;">
                                                                <strong>Descripción:</strong> <?php echo htmlspecialchars($item['nom_producto']); ?>
                                                                <span class="badge badge-warning badge-sm ml-1">EDITANDO</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1 text-right">
                                                            <button type="button" class="btn btn-danger btn-sm btn-remover-item-salida" data-toggle="tooltip"
                                                                    data-id-detalle="<?php echo $item['id_salida_detalle']; ?>">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label style="font-size: 11px; font-weight: bold;">Cantidad a Trasladar:</label>
                                                            <input type="number" 
                                                                class="form-control form-control-sm cantidad-salida" 
                                                                name="items_salida[<?php echo $item['id_salida_detalle']; ?>][cantidad]"
                                                                value="<?php echo number_format($cant_actual_en_salida, 2, '.', ''); ?>" 
                                                                min="0.01" 
                                                                max="<?php echo number_format($cantidad_maxima, 2, '.', ''); ?>" 
                                                                step="0.01"
                                                                data-cantidad-maxima="<?php echo number_format($cantidad_maxima, 2, '.', ''); ?>"
                                                                data-cantidad-maxima-os="<?php echo number_format($cantidad_maxima, 2, '.', ''); ?>"
                                                                style="font-size: 12px;" 
                                                                required>
                                                            <small class="text-info" style="font-size: 10px;">
                                                                <i class="fa fa-arrow-up"></i> Máx: <?php echo number_format($cantidad_maxima, 2); ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Botones de acción -->
                                        <div class="text-center mt-2" style="padding: 8px;">
                                            <a href="pedido_verificar.php?id=<?php echo $id_pedido; ?>" class="btn btn-secondary btn-sm mr-2">
                                                <i class="fa fa-times"></i> Cancelar
                                            </a>
                                            <button type="submit" data-toggle="tooltip" class="btn btn-<?php echo $modo_editar_salida ? 'warning' : 'success'; ?> btn-sm">
                                                <i class="fa fa-save"></i> <?php echo $modo_editar_salida ? 'Actualizar Salida' : 'Guardar Salida'; ?>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_content text-center" style="padding: 15px;">
                        <div class="row">
                            <div class="col-md-6 offset-md-3">
                                <a href="pedidos_mostrar.php" class="btn btn-outline-secondary btn-sm btn-block">
                                    <i class="fa fa-arrow-left"></i> Volver
                                </a>
                            </div>
                        </div>
                        
                        <?php if ($pedido['id_producto_tipo'] != 2): ?>
                        <div class="col-md-12 mt-2">
                            <p class="text-muted" style="font-size: 12px;">
                                <i class="fa fa-info-circle"></i> 
                                Recuerda verificar todos los items antes de generar órdenes o salidas
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
<!-- ============================================ -->
<!-- MODALES -->
<!-- ============================================ -->

<!-- MODAL DE VERIFICACIÓN SIMPLIFICADO -->
<div class="modal fade" id="verificarModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="verificarForm" action="pedido_verificar.php" method="POST">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">
                        <i class="fa fa-check-circle"></i> Verificar Item
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?php echo $id_pedido; ?>">
                    <input type="hidden" name="verificar_item" value="true">
                    <input type="hidden" id="id_pedido_detalle_input" name="id_pedido_detalle">
                    <input type="hidden" id="cantidad_pedida_hidden" value="0">
                    
                    <!-- Cantidad para OS -->
                    <div class="form-group">
                        <label class="font-weight-bold">
                            <i class="fa fa-truck text-success"></i> 
                            Cantidad para OS:
                        </label>
                        <input type="number" 
                               class="form-control" 
                               id="cantidad_para_os" 
                               name="cantidad_para_os"
                               step="0.01" 
                               min="0"
                               value="0">
                        <small id="detalle-ubicaciones-os" class="text-muted"></small>
                    </div>
                    
                    <!-- Cantidad para OC -->
                    <div class="form-group">
                        <label class="font-weight-bold">
                            <i class="fa fa-shopping-cart text-danger"></i> 
                            Cantidad para OC:
                        </label>
                        <input type="number" 
                               class="form-control" 
                               id="fin_cant_pedido_detalle" 
                               name="fin_cant_pedido_detalle"
                               step="0.01" 
                               min="0"
                               value="0">
                        <small class="text-muted">Cantidad a comprar</small>
                    </div>
                    
                    <!-- Total verificado -->
                    <div class="alert alert-info mb-0">
                        <div class="d-flex justify-content-between">
                            <span><strong>Cantidad Pedida:</strong></span>
                            <span id="cantidad-pedida-display">0.00</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span><strong>Total Verificado:</strong></span>
                            <span id="total-verificado" class="font-weight-bold">0.00</span>
                        </div>
                    </div>
                    <div id="alerta-exceso" class="alert alert-danger mt-2" style="display: none;">
                        ⚠️ El total verificado supera la cantidad pedida
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success" id="btn-verificar">
                        <i class="fa fa-check"></i> Verificar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para ver detalles de SALIDA -->
<div class="modal fade" id="modalDetalleSalida" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #d4edda; padding: 15px;">
                <h5 class="modal-title">
                    <i class="fa fa-truck text-success"></i> 
                    Detalles de Salida
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                <div id="loading-spinner-salida" class="text-center" style="padding: 40px;">
                    <i class="fa fa-spinner fa-spin fa-3x text-success"></i>
                    <p class="mt-2">Cargando detalles...</p>
                </div>
                
                <div id="contenido-detalle-salida" style="display: none;">
                    <!-- Contenido se carga dinámicamente -->
                </div>
                
                <div id="error-detalle-salida" style="display: none;" class="text-center">
                    <i class="fa fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h5 class="text-warning">Error al cargar detalles</h5>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA VER DETALLES DE ORDEN -->
<div class="modal fade" id="modalDetalleCompra" tabindex="-1" role="dialog" aria-labelledby="modalDetalleCompraLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #f8f9fa; padding: 15px;">
                <h5 class="modal-title" id="modalDetalleCompraLabel">
                    <i class="fa fa-file-text-o text-primary"></i> 
                    Detalles de Orden de Compra
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                <div id="loading-spinner" class="text-center" style="padding: 40px;">
                    <i class="fa fa-spinner fa-spin fa-3x text-primary"></i>
                    <p class="mt-2">Cargando detalles...</p>
                </div>
                
                <div id="contenido-detalle-compra" style="display: none;">
                    <!-- Contenido del detalle -->
                </div>
                
                <div id="error-detalle-compra" style="display: none;" class="text-center">
                    <i class="fa fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h5 class="text-warning">Error al cargar detalles</h5>
                    <p class="text-muted">No se pudieron cargar los detalles de la orden.</p>
                </div>
            </div>
            <div class="modal-footer" style="padding: 15px;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA AGREGAR PROVEEDOR -->
<div class="modal fade" id="modalNuevoProveedor" tabindex="-1" role="dialog" aria-labelledby="modalNuevoProveedorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #2a3f54; padding: 15px;">
                <h5 class="modal-title" id="modalNuevoProveedorLabel" style="color: white;">
                    <i class="fa fa-user-plus"></i> 
                    Agregar Nuevo Proveedor
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 600px; overflow-y: auto; padding: 20px;">
                <form id="form-nuevo-proveedor-modal" class="form-horizontal form-label-left">
                    
                    <!-- Nombre -->
                    <div class="form-group row">
                        <label class="control-label col-md-3 col-sm-3">Nombre <span class="text-danger">*</span>:</label>
                        <div class="col-md-9 col-sm-9">
                            <input type="text" name="nom_proveedor" class="form-control" placeholder="Nombre del proveedor" required>
                        </div>
                    </div>

                    <!-- RUC -->
                    <div class="form-group row">
                        <label class="control-label col-md-3 col-sm-3">RUC <span class="text-danger">*</span>:</label>
                        <div class="col-md-9 col-sm-9">
                            <input type="text" name="ruc_proveedor" class="form-control" placeholder="RUC del proveedor" maxlength="11" pattern="[0-9]{11}" title="Ingrese exactamente 11 dígitos numéricos" required>
                        </div>
                    </div>

                    <!-- Dirección -->
                    <div class="form-group row">
                        <label class="control-label col-md-3 col-sm-3">Dirección <span class="text-danger">*</span>:</label>
                        <div class="col-md-9 col-sm-9">
                            <textarea name="dir_proveedor" class="form-control" rows="3" placeholder="Dirección del proveedor" required></textarea>
                        </div>
                    </div>

                    <!-- Teléfono -->
                    <div class="form-group row">
                        <label class="control-label col-md-3 col-sm-3">Teléfono <span class="text-danger">*</span>:</label>
                        <div class="col-md-9 col-sm-9">
                            <input type="text" name="tel_proveedor" class="form-control" placeholder="Teléfono del proveedor" required>
                        </div>
                    </div>

                    <!-- Contacto -->
                    <div class="form-group row">
                        <label class="control-label col-md-3 col-sm-3">Contacto <span class="text-danger">*</span>:</label>
                        <div class="col-md-9 col-sm-9">
                            <input type="text" name="cont_proveedor" class="form-control" placeholder="Persona de contacto" required>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-group row">
                        <label class="control-label col-md-3 col-sm-3">Email:</label>
                        <div class="col-md-9 col-sm-9">
                            <input type="email" name="email_proveedor" class="form-control" placeholder="Correo electrónico">
                        </div>
                    </div>

                    <!-- Cuentas Bancarias -->
                    <div class="x_panel" style="margin-top: 20px;">
                        <div class="x_title">
                            <h2 style="font-size: 16px;">Cuentas Bancarias (Opcional)</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <table class="table table-bordered" style="font-size: 12px;">
                                <thead style="background-color: #f8f9fa;">
                                    <tr>
                                        <th>Banco</th>
                                        <th>Moneda</th>
                                        <th>Cuenta Corriente</th>
                                        <th>Cuenta Interbancaria</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla-cuentas-modal">
                                    <tr>
                                        <td>
                                            <select name="id_banco[]" class="form-control select2_banco" required>
                                                <option value="">Seleccione un banco</option>
                                                <?php foreach ($bancos as $b) { ?>
                                                    <?php if ($b['est_banco'] == 1) { // Solo bancos activos ?>
                                                        <option value="<?php echo $b['id_banco']; ?>">
                                                            <?php echo $b['cod_banco']; ?>
                                                        </option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="id_moneda[]" class="form-control select2_moneda" required>
                                                <option value="">Seleccione una moneda</option>
                                                <?php foreach ($monedas as $m) { ?>
                                                    <option value="<?php echo $m['id_moneda']; ?>"><?php echo $m['nom_moneda']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td><input type="text" name="cta_corriente[]" class="form-control" required></td>
                                        <td><input type="text" name="cta_interbancaria[]" class="form-control" required></td>
                                        <td><button type="button" class="btn btn-danger btn-sm eliminar-fila-modal">X</button></td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-success btn-sm" id="agregarCuentaModal">
                                <i class="fa fa-plus"></i> Agregar Cuenta
                            </button>
                        </div>
                    </div>

                    <div class="ln_solid"></div>

                    <div class="form-group">
                        <div class="col-md-12 col-sm-12">
                            <p class="text-muted" style="font-size: 12px;">
                                <span class="text-danger">*</span> Los campos con (<span class="text-danger">*</span>) son obligatorios.
                            </p>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer" style="padding: 15px; background-color: #f8f9fa;">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    <i></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btn-guardar-proveedor-modal">
                    <i></i> Registrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- JAVASCRIPT -->
<!-- ============================================ -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlActual = window.location.pathname;
    const esVistaVerificar = urlActual.includes('pedido_verificar.php');
    
    if (!esVistaVerificar) {
        console.log('Script de verificación omitido');
        return;
    }
    
    //  DETECTAR SI VIENE DE GUARDAR
    const urlParams = new URLSearchParams(window.location.search);
    const debeValidar = urlParams.get('validate') === '1';
    
    if (debeValidar) {
        console.log(' Recarga después de guardar - Activando validación forzada');
    }
    
    // Variables globales
    const esOrdenServicio = <?php echo ($pedido['id_producto_tipo'] == 2) ? 'true' : 'false'; ?>;
    const pedidoAnulado = <?php echo ($pedido['est_pedido'] == 0) ? 'true' : 'false'; ?>;
    const modoEditar = <?php echo $modo_editar ? 'true' : 'false'; ?>;
    const modoEditarSalida = <?php echo isset($modo_editar_salida) && $modo_editar_salida ? 'true' : 'false'; ?>;
    let itemsAgregadosOrden = new Set();
    let btnNuevaSalida = null;
    let itemsEliminadosOrden = []; // Array para trackear IDs eliminados

    

    // ============================================
    //  VERIFICAR TÉCNICAMENTE
    // ============================================
    const btnVerificarTecnicamente = document.getElementById('btn-verificar-tecnicamente');
    if (btnVerificarTecnicamente) {
        btnVerificarTecnicamente.addEventListener('click', function() {
            const idPedido = this.getAttribute('data-id-pedido');
            
            Swal.fire({
                title: '¿Verificar técnicamente este pedido?',
                html: `
                    <div style="text-align: left; padding: 15px;">
                        <p><strong>Esta acción:</strong></p>
                        <ul style="margin-left: 20px;">
                            <li>Habilitará los botones para crear órdenes de compra/servicio</li>
                            <li>Registrará tu nombre y la fecha/hora de verificación</li>
                        </ul>
                        <p style="color: #856404; margin-top: 15px;">
                            <i class="fa fa-exclamation-triangle"></i> 
                            <strong>Esta acción no se puede deshacer</strong>
                        </p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fa fa-check"></i> Sí, verificar',
                cancelButtonText: '<i class="fa fa-times"></i> Cancelar',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar loading
                    Swal.fire({
                        title: 'Procesando...',
                        text: 'Verificando pedido',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Enviar petición
                    fetch('pedido_verificar.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `verificar_tecnicamente=1&id_pedido=${idPedido}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Verificado!',
                                text: data.message,
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                // Recargar la página
                                window.location.href = `pedido_verificar.php?id=${idPedido}&success=verificado_tecnica`;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message,
                                confirmButtonColor: '#d33'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de conexión',
                            text: 'No se pudo conectar con el servidor',
                            confirmButtonColor: '#d33'
                        });
                    });
                }
            });
        });
    }
    // ============================================
    // MODAL DE VERIFICACIÓN MEJORADO
    // ============================================
    
    // Handler para botones de verificar - VERSIÓN SIMPLIFICADA
    document.querySelectorAll('.verificar-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const idDetalle = this.getAttribute('data-id-detalle');
            const cantidadPedida = parseFloat(this.getAttribute('data-cantidad-pedida')) || 0;
            const stockDestino = parseFloat(this.getAttribute('data-stock-destino')) || 0;
            const stockOtrasUbicaciones = parseFloat(this.getAttribute('data-stock-otras-ubicaciones')) || 0;
            const otrasUbicaciones = JSON.parse(this.getAttribute('data-otras-ubicaciones') || '[]');
            
            // Llenar campos ocultos
            document.getElementById('id_pedido_detalle_input').value = idDetalle;
            document.getElementById('cantidad_pedida_hidden').value = cantidadPedida;
            document.getElementById('cantidad-pedida-display').textContent = cantidadPedida.toFixed(2);
            
            // Calcular cantidades iniciales
            const stockTotalDisponible = stockDestino + stockOtrasUbicaciones;
            const faltante = Math.max(0, cantidadPedida - stockDestino);
            const cantidadInicialOS = Math.min(stockOtrasUbicaciones, faltante);
            const cantidadInicialOC = Math.max(0, faltante - cantidadInicialOS);
            
            // Llenar campo OS
            const inputOS = document.getElementById('cantidad_para_os');
            inputOS.value = cantidadInicialOS.toFixed(2);
            
            // Mostrar detalle de ubicaciones para OS
            const detalleOS = document.getElementById('detalle-ubicaciones-os');
            if (stockOtrasUbicaciones > 0) {
                let htmlUbicaciones = '<strong>Disponible en:</strong> ';
                otrasUbicaciones.forEach((ub, idx) => {
                    if (idx > 0) htmlUbicaciones += ', ';
                    htmlUbicaciones += `
                        <div class="mb-1">
                            <span class="badge badge-${ub.es_otro_cliente == 1 ? 'warning' : 'info'}" style="font-size: 9px;">
                                ${ub.nom_cliente} - ${ub.nom_obra}
                            </span>
                            <span class="ml-1">${ub.nom_ubicacion}</span>
                            <strong class="ml-2 text-primary">${parseFloat(ub.stock).toFixed(2)}</strong> unidades
                        </div>
                    `;
                });
                detalleOS.innerHTML = htmlUbicaciones;
            } else {
                detalleOS.innerHTML = '<em>Sin stock en otras ubicaciones</em>';
            }
            
            // Llenar campo OC
            const inputOC = document.getElementById('fin_cant_pedido_detalle');
            inputOC.value = cantidadInicialOC.toFixed(2);
            
            // Actualizar total verificado
            const total = cantidadInicialOS + cantidadInicialOC;
            document.getElementById('total-verificado').textContent = total.toFixed(2);
            
            // Mostrar modal
            $('#verificarModal').modal('show');
        });
    });

    // Validación en tiempo real
    $('#cantidad_para_os, #fin_cant_pedido_detalle').on('input', function() {
        let cantOS = parseFloat($('#cantidad_para_os').val()) || 0;
        let cantOC = parseFloat($('#fin_cant_pedido_detalle').val()) || 0;
        let cantPedida = parseFloat($('#cantidad_pedida_hidden').val()) || 0;
        
        let total = cantOS + cantOC;
        $('#total-verificado').text(total.toFixed(2));
        
        // Validar que no supere la cantidad pedida
        if (total > cantPedida) {
            $('#alerta-exceso').show();
            $('#btn-verificar').prop('disabled', true);
            $('#total-verificado').addClass('text-danger');
        } else {
            $('#alerta-exceso').hide();
            $('#btn-verificar').prop('disabled', false);
            $('#total-verificado').removeClass('text-danger');
        }
        
        // Validar que al menos una cantidad sea mayor a 0
        if (cantOS <= 0 && cantOC <= 0) {
            $('#btn-verificar').prop('disabled', true);
        }
    });

    // Función para actualizar total verificado
    function actualizarTotalVerificado() {
        const cantidadOS = parseFloat(document.getElementById('cantidad_para_os').value) || 0;
        const cantidadOC = parseFloat(document.getElementById('fin_cant_pedido_detalle').value) || 0;
        const total = cantidadOS + cantidadOC;
        
        document.getElementById('total-verificado').textContent = total.toFixed(2);
    }

    // Escuchar cambios en cantidad OC
    const inputFinCant = document.getElementById('fin_cant_pedido_detalle');
    if (inputFinCant) {
        inputFinCant.addEventListener('input', actualizarTotalVerificado);
    } 
    // ============================================
    // INICIALIZACIÓN
    // ============================================
    if (!esOrdenServicio && !pedidoAnulado && !modoEditar) {
        setTimeout(verificarSiGenerarSalida, 1000);
    }
    
    if (modoEditar) {
        configurarEventosEdicion();
    }

    // Cargar salida si está en edición
    if (modoEditarSalida) {
        setTimeout(cargarSalidaEdicion, 300);
    }
    
    configurarEventListeners();
    configurarModalProveedor();
    configurarValidacionTiempoReal();
    configurarValidacionTiempoRealSalidas();
    configurarExclusividadCheckboxes();
    
    // ============================================
    // FUNCIÓN PARA RECALCULAR ESTADO DE ITEMS
    // ============================================
    function recalcularEstadoItems() {
        const itemsPendientes = document.querySelectorAll('.item-pendiente');
        let tieneItemsDisponibles = false;
        itemsPendientes.forEach(function(item) {
            const estaCerrado = item.querySelector('.badge-danger') !== null && 
                            item.querySelector('.badge-danger').textContent.includes('Cerrado');
            
            const badgeTodoOrdenado = item.querySelector('.badge-success');
            const tieneTodoOrdenado = badgeTodoOrdenado && 
                                    badgeTodoOrdenado.textContent.includes('Todo Ordenado');
            
            if (!tieneTodoOrdenado && !estaCerrado) {
                tieneItemsDisponibles = true;
            }
        });
                
        // ============================================
        // BOTÓN NUEVA ORDEN (ACTUALIZADO)
        // ============================================
        const btnNuevaOrden = document.getElementById('btn-nueva-orden');
        if (btnNuevaOrden) {
            if (tieneItemsDisponibles) {
                btnNuevaOrden.disabled = false;
                btnNuevaOrden.classList.remove('btn-secondary');
                btnNuevaOrden.classList.add('btn-primary');
                btnNuevaOrden.title = 'Crear una nueva orden de compra con los items verificados';
                btnNuevaOrden.innerHTML = '<i class="fa fa-shopping-cart"></i> Nueva Orden';
            } else {
                btnNuevaOrden.disabled = true;
                btnNuevaOrden.classList.remove('btn-primary');
                btnNuevaOrden.classList.add('btn-secondary');
                btnNuevaOrden.title = 'No hay items disponibles para agregar';
                btnNuevaOrden.innerHTML = '<i class="fa fa-ban"></i> Nueva Orden';
            }
        }
    }

    if (!modoEditar) {
        setTimeout(recalcularEstadoItems, 500);
    }
    
    // ============================================
    // FUNCIONES DE SALIDA - DECLARAR PRIMERO
    // ============================================
    function validarItemsDisponiblesParaSalida() {
        const itemsPendientes = document.querySelectorAll('.item-pendiente');
        let hayDisponibles = false;
        let itemsDisponiblesDetalle = [];
        
        //  OBTENER IDS DE ITEMS YA AGREGADOS AL FORMULARIO (NO GUARDADOS)
        const itemsEnFormulario = new Set();
        const itemsFormularioSalida = document.querySelectorAll('#contenedor-items-salida [id^="item-salida-"]');
        
        itemsFormularioSalida.forEach(item => {
            const idProductoInput = item.querySelector('input[name*="[id_producto]"]');
            if (idProductoInput) {
                itemsEnFormulario.add(idProductoInput.value);
            }
        });
                
        itemsPendientes.forEach(function(item) {
            // 🔹 BUSCAR BOTÓN DE SALIDA DENTRO DEL ITEM
            const btnAgregarSalida = item.querySelector('.btn-agregarSalida');
            
            if (btnAgregarSalida) {
                const idProducto = btnAgregarSalida.dataset.idProducto;
                const idDetalle = btnAgregarSalida.dataset.idDetalle;
                const cantidadDisponible = parseFloat(btnAgregarSalida.dataset.cantidadDisponible) || 0;
                
                // 🔹 ITEM DISPONIBLE SI:
                // 1. No está deshabilitado Y tiene cantidad > 0
                // 2. Está deshabilitado PERO está en formulario temporal
                
                const estaEnFormulario = itemsEnFormulario.has(idProducto);
                const estaHabilitado = !btnAgregarSalida.disabled;
                
                if ((estaHabilitado && cantidadDisponible > 0) || (estaEnFormulario && !estaHabilitado)) {
                    hayDisponibles = true;
                    itemsDisponiblesDetalle.push({
                        idProducto: idProducto,
                        idDetalle: idDetalle,
                        cantidad: cantidadDisponible,
                        estado: estaEnFormulario ? 'EN_FORMULARIO' : 'DISPONIBLE'
                    });
                }
            }
        });
        
        console.log(' Resultado validación salidas:', {
            hayDisponibles: hayDisponibles,
            totalDisponibles: itemsDisponiblesDetalle.length,
            detalle: itemsDisponiblesDetalle
        });
        
        return hayDisponibles;
    }
    
    function mostrarFormularioNuevaSalida() {
        const myTab = document.getElementById('myTab');
        const myTabContent = document.getElementById('myTabContent');
        const contenedorNuevaSalida = document.getElementById('contenedor-nueva-salida');
        const contenedorNuevaOrden = document.getElementById('contenedor-nueva-orden');
        
        //  Ocultar tabs
        if (myTab) myTab.style.display = 'none';
        if (myTabContent) myTabContent.style.display = 'none';
        
        //  Ocultar formulario de orden si está visible
        if (contenedorNuevaOrden) contenedorNuevaOrden.style.display = 'none';
        
        //  Mostrar formulario de salida
        if (contenedorNuevaSalida) contenedorNuevaSalida.style.display = 'block';
        
        btnNuevaSalida = document.getElementById('btn-nueva-salida');
        if (btnNuevaSalida) {
            //  VALIDAR SI AÚN HAY ITEMS DISPONIBLES
            const hayItemsDisponibles = validarItemsDisponiblesParaSalida();
            
            
            //  SOLO CAMBIAR A "VER SALIDAS" SIN DESHABILITAR
            btnNuevaSalida.innerHTML = '<i class="fa fa-list"></i> Ver Salidas';
            btnNuevaSalida.classList.remove('btn-success');
            btnNuevaSalida.classList.add('btn-secondary');
            
            //  MANTENER HABILITADO SI HAY ITEMS
            if (hayItemsDisponibles) {
                btnNuevaSalida.disabled = false;
                btnNuevaSalida.title = 'Ver lista de salidas';
            } else {
                btnNuevaSalida.disabled = true;
                btnNuevaSalida.title = 'No hay más items disponibles';
            }
        }
        
        validarUbicacionesSalida();
    }
    
    function mostrarListaSalidas() {
        const myTab = document.getElementById('myTab');
        const myTabContent = document.getElementById('myTabContent');
        const contenedorNuevaSalida = document.getElementById('contenedor-nueva-salida');
        const contenedorNuevaOrden = document.getElementById('contenedor-nueva-orden');
        
        // ✅ Mostrar tabs
        if (myTab) myTab.style.display = 'flex';
        if (myTabContent) myTabContent.style.display = 'block';
        
        // ✅ Ocultar ambos formularios
        if (contenedorNuevaSalida) contenedorNuevaSalida.style.display = 'none';
        if (contenedorNuevaOrden) contenedorNuevaOrden.style.display = 'none';
        
        btnNuevaSalida = document.getElementById('btn-nueva-salida');
        if (btnNuevaSalida) {
            //  RE-VALIDAR DISPONIBILIDAD CADA VEZ
            const hayItemsDisponibles = validarItemsDisponiblesParaSalida();
            
            console.log(' Actualizando botón Nueva Salida:', {
                hayDisponibles: hayItemsDisponibles
            });
            
            if (hayItemsDisponibles) {
                btnNuevaSalida.innerHTML = '<i class="fa fa-truck"></i> Nueva Salida';
                btnNuevaSalida.classList.remove('btn-secondary');
                btnNuevaSalida.classList.add('btn-success');
                btnNuevaSalida.disabled = false;
                btnNuevaSalida.title = 'Crear una nueva orden de salida';
            } else {
                btnNuevaSalida.innerHTML = '<i class="fa fa-ban"></i> Nueva Salida';
                btnNuevaSalida.classList.remove('btn-success');
                btnNuevaSalida.classList.add('btn-secondary');
                btnNuevaSalida.disabled = true;
                btnNuevaSalida.title = 'No hay items disponibles para generar salida';
            }
        }
        
        const salidasTab = document.getElementById('salidas-tab');
        if (salidasTab) {
            $('#salidas-tab').tab('show');
        }
    }
    
    function agregarItemASalida(item) {
        console.log('🚚 agregarItemASalida INICIADO');
        console.log('📋 Item recibido:', item);
        console.log('🔑 Propiedades del item:', Object.keys(item));
        console.log('🔑 Todas las propiedades:', item);

        
        // ✅ VALIDACIÓN SIMPLE: Solo verificar que haya cantidad OS
        const cantidadOS = parseFloat(item.cantidadDisponible) || 0;
        
        if (cantidadOS <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Sin Cantidad Disponible',
                text: 'No hay cantidad pendiente para generar salida de este item.',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
        // ✅ OBTENER UBICACIÓN ORIGEN SELECCIONADA
        const ubicacionOrigenSelect = document.getElementById('ubicacion_origen_salida');
        
        if (!ubicacionOrigenSelect || !ubicacionOrigenSelect.value) {
            Swal.fire({
                icon: 'warning',
                title: 'Seleccione Ubicación Origen',
                text: 'Debe seleccionar la ubicación de origen antes de agregar items.',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
        const optionSeleccionada = ubicacionOrigenSelect.options[ubicacionOrigenSelect.selectedIndex];
        const stockEnUbicacion = parseFloat(optionSeleccionada.getAttribute('data-stock')) || 0;
        const nombreUbicacion = optionSeleccionada.text;
        
        console.log('📦 Stock en ubicación seleccionada:', {
            ubicacion: nombreUbicacion,
            stock: stockEnUbicacion
        });
        
        if (stockEnUbicacion <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Sin Stock en Ubicación',
                text: `La ubicación "${nombreUbicacion}" no tiene stock disponible.`,
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
        // ✅ CANTIDAD MÁXIMA = MÍNIMO ENTRE CANT_OS Y STOCK_UBICACIÓN
        const cantidadMaxima = Math.min(cantidadOS, stockEnUbicacion);
        
        console.log('🎯 Cantidad máxima calculada:', {
            cantOS: cantidadOS,
            stockUbicacion: stockEnUbicacion,
            maxFinal: cantidadMaxima
        });
        
        const itemId = 'salida-' + Date.now();
        const idPedidoDetalle = parseInt(item.idDetalle) || 0;
        
        // 🔹 DETECTAR MODO EDICIÓN
        const modoEditar = document.querySelector('input[name="actualizar_salida"]') !== null;
        const badgeTipo = modoEditar ? '<span class="badge badge-success badge-sm ml-1" style="font-size: 9px;">NUEVO</span>' : '';
        
        const itemElement = document.createElement('div');
        itemElement.id = `item-salida-${itemId}`;
        itemElement.classList.add('alert', 'alert-light', 'p-2', 'mb-2');
        itemElement.style.border = '1px solid #dee2e6';
        itemElement.style.borderRadius = '4px';
        
        // ✅ HTML COMPACTO TIPO TABLA - FORMATO: CÓDIGO | DESCRIPCIÓN | CANTIDAD | ELIMINAR
        itemElement.innerHTML = `
            <!-- Inputs ocultos -->
            <input type="hidden" name="items_salida[${itemId}][id_detalle]" value="${item.idDetalle}">
            <input type="hidden" name="items_salida[${itemId}][id_producto]" value="${item.idProducto}">
            <input type="hidden" name="items_salida[${itemId}][id_pedido_detalle]" value="${idPedidoDetalle}">
            <input type="hidden" name="items_salida[${itemId}][es_nuevo]" value="1">
            
            <!-- DISEÑO COMPACTO EN UNA SOLA FILA -->
            <div class="row align-items-center" style="margin: 0;">
                <!-- CÓDIGO -->
                <div class="col-md-2" style="padding: 4px 8px;">
                    <label style="font-size: 10px; font-weight: bold; margin-bottom: 2px; display: block; color: #6c757d;">Código:</label>
                    <input type="text" 
                        class="form-control form-control-sm" 
                        value="${item.codigoProducto || 'N/A'}"
                        readonly
                        style="font-size: 11px; background-color: #f8f9fa; border: 1px solid #e0e0e0; padding: 4px 8px;">
                </div>
                
                <!-- DESCRIPCIÓN -->
                <div class="col-md-6" style="padding: 4px 8px;">
                    <label style="font-size: 10px; font-weight: bold; margin-bottom: 2px; display: block; color: #6c757d;">Descripción:</label>
                    <div style="font-size: 11px; padding: 4px 8px; background-color: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 4px; line-height: 1.3;">
                        ${item.descripcion}
                        ${badgeTipo}
                    </div>
                </div>
                
                <!-- CANTIDAD -->
                <div class="col-md-3" style="padding: 4px 8px;">
                    <label style="font-size: 10px; font-weight: bold; margin-bottom: 2px; display: block; color: #6c757d;">Cantidad:</label>
                    <input type="number" 
                        class="form-control form-control-sm cantidad-salida" 
                        name="items_salida[${itemId}][cantidad]"
                        value="${cantidadMaxima.toFixed(2)}" 
                        min="0.01" 
                        max="${cantidadMaxima.toFixed(2)}" 
                        step="0.01"
                        data-cantidad-maxima="${cantidadMaxima}"
                        data-cantidad-maxima-os="${cantidadOS}"
                        style="font-size: 12px; font-weight: bold; padding: 4px 8px; text-align: center;" 
                        required>
                    <small class="text-info" style="font-size: 9px; display: block; text-align: center; margin-top: 1px;">
                        <i class="fa fa-arrow-up"></i> Máx: ${cantidadMaxima.toFixed(2)}
                    </small>
                </div>
                
                <!-- BOTÓN ELIMINAR -->
                <div class="col-md-1 text-center" style="padding: 4px;">
                    <button type="button" 
                        class="btn btn-danger btn-sm btn-remover-item-salida" 
                        data-id-detalle="${itemId}"
                        title="Eliminar item"
                        style="padding: 4px 8px; margin-top: 18px;">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        
        // ✅ AGREGAR AL CONTENEDOR
        const contenedorItemsSalida = document.getElementById('contenedor-items-salida');
        contenedorItemsSalida.appendChild(itemElement);
        
        console.log('✅ Item agregado correctamente');
        
        // ✅ DESHABILITAR BOTÓN ORIGINAL
        if (item.botonOriginal) {
            item.botonOriginal.disabled = true;
            item.botonOriginal.innerHTML = '<i class="fa fa-check-circle"></i> Agregado';
            item.botonOriginal.classList.remove('btn-success');
            item.botonOriginal.classList.add('btn-secondary');
        }
        
        // ✅ EVENT LISTENER PARA REMOVER
        const btnRemover = itemElement.querySelector('.btn-remover-item-salida');
        if (btnRemover) {
            btnRemover.addEventListener('click', function() {
                removerItemDeSalida(itemId, item.botonOriginal);
            });
        }

        // 🔹 RE-VALIDAR DISPONIBILIDAD DESPUÉS DE AGREGAR
        setTimeout(() => {
            const hayMasDisponibles = validarItemsDisponiblesParaSalida();
            const btnNuevaSalida = document.getElementById('btn-nueva-salida');
            
            console.log('🔍 Re-validación después de agregar:', {
                hayMasDisponibles: hayMasDisponibles,
                estadoBoton: btnNuevaSalida ? btnNuevaSalida.disabled : 'no existe'
            });
            
            if (btnNuevaSalida) {
                if (hayMasDisponibles) {
                    btnNuevaSalida.disabled = false;
                    btnNuevaSalida.title = 'Ver lista de salidas';
                    console.log('✅ Botón Nueva Salida mantiene habilitado');
                } else {
                    btnNuevaSalida.disabled = true;
                    btnNuevaSalida.title = 'No hay más items disponibles';
                    console.log('🔒 Botón Nueva Salida deshabilitado - sin items');
                }
            }
        }, 100);
        
        console.log('✅ agregarItemASalida COMPLETADO');
    }
    
    function removerItemDeSalida(idDetalle, botonOriginal) {
        const itemElement = document.getElementById(`item-salida-${idDetalle}`);
        if (itemElement) {
            itemElement.remove();
        }
        
        if (botonOriginal) {
            botonOriginal.disabled = false;
            botonOriginal.innerHTML = '<i class="fa fa-truck"></i> Agregar a Salida';
            botonOriginal.classList.remove('btn-secondary');
            botonOriginal.classList.add('btn-success');
        }
        
        // 🔹 RE-VALIDAR SI EL BOTÓN NUEVA SALIDA DEBE ESTAR HABILITADO
        const btnNuevaSalida = document.getElementById('btn-nueva-salida');
        if (btnNuevaSalida && btnNuevaSalida.classList.contains('btn-secondary')) {
            const hayItemsDisponibles = validarItemsDisponiblesParaSalida();
            
            if (hayItemsDisponibles) {
                btnNuevaSalida.innerHTML = '<i class="fa fa-truck"></i> Nueva Salida';
                btnNuevaSalida.classList.remove('btn-secondary');
                btnNuevaSalida.classList.add('btn-success');
                btnNuevaSalida.disabled = false;
                btnNuevaSalida.title = '';
            }
        }
    }
    
    function validarUbicacionesSalida() {
        const almacenOrigen = document.getElementById('almacen_origen_salida');
        const ubicacionOrigen = document.getElementById('ubicacion_origen_salida');
        const almacenDestino = document.getElementById('almacen_destino_salida');
        const ubicacionDestino = document.getElementById('ubicacion_destino_salida');
        
            // Filtrar ubicaciones según almacén origen
            almacenOrigen.addEventListener('change', function() {
                const idAlmacenOrigen = this.value;
                const opcionesUbicacionOrigen = ubicacionOrigen.querySelectorAll('option');
                
                opcionesUbicacionOrigen.forEach(option => {
                    if (option.value === '') {
                        option.style.display = 'block';
                        return;
                    }
                    
                    const almacenOption = option.getAttribute('data-almacen');
                    if (almacenOption === idAlmacenOrigen) {
                        option.style.display = 'block';
                    } else {
                        option.style.display = 'none';
                    }
                });
                
                ubicacionOrigen.value = '';
            });
            
            // Filtrar ubicaciones según almacén destino
            almacenDestino.addEventListener('change', function() {
                const idAlmacenDestino = this.value;
                const opcionesUbicacionDestino = ubicacionDestino.querySelectorAll('option');
                
                opcionesUbicacionDestino.forEach(option => {
                    if (option.value === '') {
                        option.style.display = 'block';
                        return;
                    }
                    
                    const almacenOption = option.getAttribute('data-almacen');
                    if (almacenOption === idAlmacenDestino) {
                        option.style.display = 'block';
                    } else {
                        option.style.display = 'none';
                    }
                });
                
                ubicacionDestino.value = '';
            });
            
            // Validar que origen y destino sean diferentes
            [ubicacionOrigen, ubicacionDestino].forEach(elemento => {
                elemento.addEventListener('change', function() {
                    if (almacenOrigen.value && ubicacionOrigen.value && 
                        almacenDestino.value && ubicacionDestino.value) {
                        
                        if (almacenOrigen.value === almacenDestino.value && 
                            ubicacionOrigen.value === ubicacionDestino.value) {
                            
                            Swal.fire({
                                icon: 'warning',
                                title: 'Ubicaciones idénticas',
                                text: 'El origen y destino no pueden ser la misma ubicación.',
                                confirmButtonText: 'Entendido'
                            });
                            
                            ubicacionOrigen.value = '';
                        }
                    }
            });
        });
    }
    
    function mostrarDetalleSalida(idSalida) {
        $('#modalDetalleSalida').modal('show');
        
        document.getElementById('loading-spinner-salida').style.display = 'block';
        document.getElementById('contenido-detalle-salida').style.display = 'none';
        document.getElementById('error-detalle-salida').style.display = 'none';
        
        fetch('salida_detalles.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `accion=obtener_detalle&id_salida=${idSalida}`
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loading-spinner-salida').style.display = 'none';
            if (data.success) {
                mostrarContenidoDetalleSalida(data.salida, data.detalles);
            } else {
                document.getElementById('error-detalle-salida').style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('loading-spinner-salida').style.display = 'none';
            document.getElementById('error-detalle-salida').style.display = 'block';
        });
    }
    
    function mostrarContenidoDetalleSalida(salida, detalles) {
        const contenido = document.getElementById('contenido-detalle-salida');
        
        // Determinar clase de estado
        let estadoClase = 'secondary';
        switch(parseInt(salida.est_salida)) {
            case 0: estadoClase = 'danger'; break;
            case 1: estadoClase = 'warning'; break;
            case 2: estadoClase = 'info'; break;
            case 3: estadoClase = 'success'; break;
            case 4: estadoClase = 'dark'; break;
        }
        
        // Formatear fecha
        const fechaFormateada = salida.fec_req_salida ? 
            (() => {
                const fecha = salida.fec_req_salida.split(' ')[0]; // Obtiene solo "2025-11-09"
                const [anio, mes, dia] = fecha.split('-');
                return `${dia}/${mes}/${anio}`;
            })() : 
            'No especificada';
        
        let html = `
            <div class="card mb-3">
                <div class="card-header" style="background-color: #d4edda; padding: 10px 15px;">
                    <h6 class="mb-0">
                        <i class="fa fa-info-circle text-success"></i> 
                        Información de Salida - S00${salida.id_salida}
                    </h6>
                </div>
                <div class="card-body" style="padding: 15px;">
                    <div class="row">
                        <div class="col-md-6">
                            <p style="margin: 5px 0; font-size: 13px;">
                                <strong>N° Salida:</strong> S00${salida.id_salida}
                            </p>
                            <p style="margin: 5px 0; font-size: 13px;">
                                <strong>Fecha:</strong> ${fechaFormateada}
                            </p>
                            
                            <p style="margin: 5px 0; font-size: 13px;">
                                <strong>Estado:</strong> 
                                <span class="badge badge-${estadoClase}">${salida.estado_texto}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p style="margin: 5px 0; font-size: 13px;">
                                <strong>Origen:</strong><br>
                                <span class="text-muted">${salida.nom_almacen_origen} - ${salida.nom_ubicacion_origen}</span>
                            </p>
                            <p style="margin: 5px 0; font-size: 13px;">
                                <strong>Destino:</strong><br>
                                <span class="text-muted">${salida.nom_almacen_destino} - ${salida.nom_ubicacion_destino}</span>
                            </p>
                            <p style="margin: 5px 0; font-size: 13px;">
                                <strong>Responsable:</strong> ${salida.nom_personal || 'No especificado'}
                            </p>
                        </div>
                    </div>
                    
                    ${salida.obs_salida ? `
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="border-top pt-2">
                                <p style="margin: 5px 0; font-size: 13px;">
                                    <strong>Observaciones:</strong><br>
                                    <span class="text-muted">${salida.obs_salida}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    ` : ''}
                </div>
            </div>
            
            <div class="card">
                <div class="card-header" style="background-color: #e8f5e8; padding: 10px 15px;">
                    <h6 class="mb-0">
                        <i class="fa fa-list-alt text-success"></i> 
                        Productos (${detalles.length})
                    </h6>
                </div>
                <div class="card-body" style="padding: 15px;">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm" style="font-size: 12px;">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th style="width: 10%;">#</th>
                                    <th style="width: 15%;">Código</th>
                                    <th style="width: 55%;">Producto</th>
                                    <th style="width: 20%; text-align: center;">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>`;
        
        if (detalles && detalles.length > 0) {
            detalles.forEach((detalle, index) => {
                const cantidad = parseFloat(detalle.cant_salida_detalle).toFixed(2);
                html += `
                    <tr>
                        <td style="font-weight: bold;">${index + 1}</td>
                        <td>${detalle.cod_material || 'N/A'}</td>
                        <td>${detalle.nom_producto}</td>
                        <td style="text-align: center; font-weight: bold;">
                            <span class="badge badge-success">${cantidad}</span>
                        </td>
                    </tr>`;
            });
        } else {
            html += `
                <tr>
                    <td colspan="4" class="text-center text-muted" style="padding: 20px;">
                        <i class="fa fa-inbox fa-2x mb-2"></i>
                        <p>No hay productos en esta salida</p>
                    </td>
                </tr>`;
        }
        
        html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>`;
        
        contenido.innerHTML = html;
        contenido.style.display = 'block';
    }
    
    // ============================================
    // FUNCIÓN ANULAR SALIDA (CORREGIDA)
    // ============================================
    function anularSalida(idSalida) {
        Swal.fire({
            title: '¿Anular Salida?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, anular',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar loading
                Swal.fire({
                    title: 'Anulando...',
                    text: 'Por favor espere',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                fetch('salidas_anular.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${idSalida}`
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();
                    
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Anulada!',
                            text: data.message || 'La salida fue anulada correctamente',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // 🔹 RECARGAR LA PÁGINA PARA ACTUALIZAR CANTIDADES
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'No se pudo anular la salida'
                        });
                    }
                })
                .catch(error => {
                    Swal.close();
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor.'
                    });
                });
            }
        });
    }
    
    // ============================================
    // FUNCIONES DE VERIFICACIÓN
    // ============================================
    function verificarSiGenerarSalida() {
        const itemsPendientes = document.querySelectorAll('.item-pendiente');
        let tieneItems = false;
        let todosConStockCompleto = true;
        
        itemsPendientes.forEach(function(item) {
            tieneItems = true;
            const cantPedido = parseFloat(item.getAttribute('data-cant-pedido')) || 0;
            const cantDisponible = parseFloat(item.getAttribute('data-cant-disponible')) || 0;

            if (cantDisponible < cantPedido) {
                todosConStockCompleto = false;
            }
        });
        
        const estadoPedido = <?php echo $pedido['est_pedido']; ?>;
        const tieneSalidaActiva = <?php echo isset($tiene_salida_activa) && $tiene_salida_activa ? 'true' : 'false'; ?>;

        if (estadoPedido === 4) {
            console.log('⏭ Pedido en estado INGRESADO - No verificar automáticamente');
            return;
        }

        if (tieneSalidaActiva) {
            console.log(' Este pedido tiene salidas registradas');
            return; // ← SOLO RETORNAR, NO OCULTAR NADA
        }

        /*if (tieneItems && todosConStockCompleto) {
            if (estadoPedido === 5) {
                mostrarAlertaPedidoFinalizado();
            } else if (estadoPedido === 3 || estadoPedido === 4) {
                mostrarAlertaPedidoAprobado(estadoPedido);
            } else if (estadoPedido === 2) {
                mostrarAlertaPedidoCompletado();
            } else if (estadoPedido === 1) {
                completarPedidoAutomaticamente();
            }
        }*/
    }
    
    function mostrarAlertaPedidoFinalizado() {
        Swal.fire({
            title: '¡Pedido Finalizado!',
            html: '<div style="text-align: left; padding: 10px;">' +
                '<p style="margin-bottom: 10px;">Este pedido ya está marcado como <strong style="color: #28a745;">FINALIZADO</strong>.</p>' +
                '<p style="margin-bottom: 0;"><strong>¿Deseas ver las salidas relacionadas?</strong></p></div>',
            icon: 'success',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa fa-eye"></i> Ver Salidas',
            cancelButtonText: '<i class="fa fa-arrow-left"></i> Volver a pedidos',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'salidas_mostrar.php?pedido=<?php echo $id_pedido; ?>';
            } else {
                window.location.href = 'pedidos_mostrar.php';
            }
        });
    }
    
    function mostrarAlertaPedidoAprobado(estadoPedido) {
        const estadoTexto = estadoPedido === 3 ? 'aprobado' : 'ingresado a almacén';
        Swal.fire({
            title: '¡Pedido con stock disponible!',
            html: `<div style="text-align: left; padding: 10px;">
                <p style="margin-bottom: 10px;">Este pedido está ${estadoTexto} y todos los items tienen stock en almacén.</p>
                <p style="margin-bottom: 0;"><strong>¿Deseas generar una salida de almacén ahora?</strong></p></div>`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa fa-truck"></i> Sí, generar salida',
            cancelButtonText: '<i class="fa fa-arrow-left"></i> Volver a pedidos',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'salidas_nuevo.php?desde_pedido=<?php echo $id_pedido; ?>';
            } else {
                window.location.href = 'pedidos_mostrar.php';
            }
        });
    }
    
    function mostrarAlertaPedidoCompletado() {
        Swal.fire({
            title: '¡Pedido Completado!',
            html: '<div style="text-align: left; padding: 10px;">' +
                '<p style="margin-bottom: 10px;">Este pedido fue completado automáticamente porque tiene todo el stock disponible.</p>' +
                '<p style="margin-bottom: 0;"><strong>¿Deseas generar una salida de almacén ahora?</strong></p></div>',
            icon: 'success',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa fa-truck"></i> Sí, generar salida',
            cancelButtonText: '<i class="fa fa-arrow-left"></i> Volver a pedidos',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'salidas_nuevo.php?desde_pedido=<?php echo $id_pedido; ?>';
            } else {
                window.location.href = 'pedidos_mostrar.php';
            }
        });
    }
    
    function completarPedidoAutomaticamente() {
        Swal.fire({
            title: 'Verificando disponibilidad...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });
        
        fetch('pedido_actualizar_estado.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id_pedido=<?php echo $id_pedido; ?>&accion=completar_automatico'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: '¡Stock Completo - Pedido Completado!',
                    html: '<div style="text-align: left; padding: 10px;">' +
                        '<p style="margin-bottom: 10px;">Todos los items tienen stock disponible en el almacén.</p>' +
                        '<p style="margin-bottom: 10px;">El pedido se ha marcado como <strong style="color: #17a2b8;">COMPLETADO</strong> automáticamente.</p>' +
                        '<p style="margin-bottom: 0;"><strong>¿Desea generar una salida de almacén ahora?</strong></p></div>',
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fa fa-truck"></i> Sí, generar salida',
                    cancelButtonText: '<i class="fa fa-arrow-left"></i> Volver a pedidos',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'salidas_nuevo.php?desde_pedido=<?php echo $id_pedido; ?>';
                    } else {
                        window.location.href = 'pedidos_mostrar.php';
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo actualizar el estado del pedido. Intente nuevamente.'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor. Intente nuevamente.'
            });
        });
    }
    
    document.addEventListener('click', function(event) {
        const btnSalida = event.target.closest('.btn-generar-salida-interna');
        if (!btnSalida) return;
        
        event.preventDefault();
        event.stopPropagation();
        
        const idPedido = btnSalida.dataset.idPedido;
        const idProducto = btnSalida.dataset.idProducto;
        const descripcion = btnSalida.dataset.descripcion;
        const cantidadFaltante = parseFloat(btnSalida.dataset.cantidadFaltante);
        const ubicacionDestinoId = btnSalida.dataset.ubicacionDestino;
        const ubicacionDestinoNombre = btnSalida.dataset.ubicacionDestinoNombre;
        const almacenId = btnSalida.dataset.almacen;
        const almacenNombre = btnSalida.dataset.almacenNombre;
        
        Swal.fire({
            title: '🔄 Generar Orden de Salida',
            html: `
                <div style="text-align: left; padding: 10px;">
                    <p><strong>Producto:</strong><br>${descripcion}</p>
                    <p><strong>Cantidad a trasladar:</strong> <span class="badge badge-warning">${cantidadFaltante.toFixed(2)}</span> unidades</p>
                    <p><strong>Desde:</strong> Otras ubicaciones de <em>${almacenNombre}</em></p>
                    <p><strong>Hacia:</strong> <em>${ubicacionDestinoNombre}</em></p>
                    <hr>
                    <p class="text-info"><i class="fa fa-info-circle"></i> Se generará una salida</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa fa-exchange"></i> Sí, generar salida',
            cancelButtonText: '<i class="fa fa-times"></i> Cancelar',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirigir a orden de salida

                //window.location.href = `salidas_nuevo.php?id_pedido=${idPedido}&id_producto=${idProducto}&cantidad=${cantidadFaltante}&id_ubicacion_destino=${ubicacionDestinoId}`;
                window.location.href = `salidas_nuevo.php?desde_pedido=<?php echo $pedido['id_pedido']; ?>`;
            }
        });
    });
    
    // ============================================
    // VALIDACIÓN DE FORMULARIO
    // ============================================
    
    function validarFormularioOrden(e) {
        e.preventDefault();

        const fecha = document.getElementById('fecha_orden').value;
        const proveedor = document.getElementById('proveedor_orden').value;
        const moneda = document.getElementById('moneda_orden').value;
        
        if (!fecha || !proveedor || !moneda) {
            Swal.fire({
                icon: 'warning',
                title: 'Campos Obligatorios',
                text: 'Por favor complete los campos obligatorios (Fecha, Proveedor y Moneda).',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Entendido'
            });
            return false;
        }
        
        const contenedorItemsOrden = document.getElementById('contenedor-items-orden');
        const itemsOrden = contenedorItemsOrden.querySelectorAll('[id^="item-orden-"]');
        
        if (itemsOrden.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Sin Items',
                text: 'Debe agregar al menos un ítem a la orden antes de guardar.',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Entendido'
            });
            return false;
        }

        let erroresValidacion = [];
        
        if (esOrdenServicio) {
            erroresValidacion = validarCantidadesServicio(itemsOrden);
        } else {
            erroresValidacion = validarCantidadesCliente(itemsOrden);
        }
        
        if (erroresValidacion.length > 0) {
            const tipoOrden = esOrdenServicio ? 'servicio' : 'material';
            
            let mensajeHTML = '<div style="text-align: left; padding: 10px;">' +
                            `<p style="margin-bottom: 10px;"><strong>No se puede guardar la orden de ${tipoOrden}:</strong></p>` +
                            '<ul style="color: #dc3545; font-size: 13px; margin-left: 20px;">';
            
            erroresValidacion.forEach(error => {
                mensajeHTML += `<li style="margin-bottom: 8px;">${error}</li>`;
            });
            
            mensajeHTML += '</ul></div>';
            
            Swal.fire({
                icon: 'error',
                title: 'Cantidad No Permitida',
                html: mensajeHTML,
                confirmButtonColor: '#d33',
                confirmButtonText: '<i class="fa fa-times"></i> Entendido',
                allowOutsideClick: false
            });
            
            return false;
        }

        // Si pasa la validación frontend, enviar el formulario via AJAX
        const form = document.getElementById('form-nueva-orden');
        const formData = new FormData(form);

        // AGREGAR ITEMS ELIMINADOS AL FORMDATA
        if (modoEditar && itemsEliminadosOrden.length > 0) {
            formData.append('items_eliminados', JSON.stringify(itemsEliminadosOrden));
        }

        Swal.fire({
            title: 'Guardando...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            Swal.close();

            if (data.startsWith('ERROR:')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al Guardar',
                    html: `<div style="text-align: left;">${data.replace('ERROR:', '')}</div>`,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Entendido'
                });
            } else {
                const tipo = esOrdenServicio ? 'servicio' : 'compra';
                const successParam = `success=${modoEditar ? 'actualizado' : 'creado'}&tipo=${tipo}`;

                // 1️⃣ Primero re-verificar
                fetch('pedido_verificar.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `reverificar_items=1&id_pedido=<?php echo $id_pedido; ?>`
                })
                .then(r => r.json())
                .then(reverificacion => {
                    console.log('✅ Items re-verificados:', reverificacion.items_reverificados);
                    
                    // 2️⃣ Ahora sí recargar la página
                    window.location.href = `pedido_verificar.php?id=<?php echo $id_pedido; ?>&${successParam}`;
                })
                .catch(error => {
                    console.error('Error en re-verificación:', error);
                    // Recargar de todas formas
                    window.location.href = `pedido_verificar.php?id=<?php echo $id_pedido; ?>&${successParam}`;
                });
            }
        })
    }

    function validarCantidadesCliente(itemsOrden) {
    const errores = [];

    // Obtener ID de compra actual
    const inputIdCompra = document.querySelector('input[name="id_compra"]');
    const idCompraActual = inputIdCompra ? parseInt(inputIdCompra.value) : null;

    console.log('🔍 validarCantidadesCliente - ID Compra actual:', idCompraActual);

    itemsOrden.forEach(itemElement => {

        // Inputs necesarios por item
        const idProductoInput = itemElement.querySelector('input[name*="[id_producto]"]');
        const cantidadInput = itemElement.querySelector('.cantidad-item');
        const esNuevoInput = itemElement.querySelector('input[name*="[es_nuevo]"]');
        const idDetalleInput = itemElement.querySelector('input[name*="[id_pedido_detalle]"]');
        const esBaseArceInput = itemElement.querySelector('input[name*="[es_base_arce]"]');

        // Validar elementos obligatorios
        if (!idProductoInput || !cantidadInput) {
            console.warn('⚠️ Faltan inputs necesarios en item:', itemElement);
            return;
        }

        // Valores principales del item
        const idProducto = parseInt(idProductoInput.value);
        const cantidadNueva = parseFloat(cantidadInput.value) || 0;
        const esNuevo = esNuevoInput && esNuevoInput.value === '1';
        const idDetalleActual = idDetalleInput ? parseInt(idDetalleInput.value) : null;

        //  Detectar BASE ARCE por data-attribute + input oculto
        const esBaseArce =
            cantidadInput.getAttribute('data-es-base-arce') === '1' ||
            (esBaseArceInput && esBaseArceInput.value === '1');

        console.log(' Validando item:', {
            id_pedido_detalle: idDetalleActual,
            esBaseArce: esBaseArce,
            esNuevo: esNuevo,
            modoEditar: modoEditar
        });

        //  Obtener cantidades verificada/ordenada
        let cantidadVerificada = parseFloat(cantidadInput.getAttribute('data-cantidad-verificada')) || 0;
        let cantidadOrdenada = parseFloat(cantidadInput.getAttribute('data-cantidad-ordenada')) || 0;

        
        let cantidadDisponible = 0;
        
        if (modoEditar && idCompraActual) {
            // ═══════════════════════════════════════════════════════
            // MODO EDICIÓN: Permitir usar TODA la cantidad verificada
            // ═══════════════════════════════════════════════════════
            if (esBaseArce) {
                // BASE ARCE: Máximo = cantidad pedida (verificada)
                cantidadDisponible = cantidadVerificada;
                console.log(' BASE ARCE EDICIÓN: Máximo permitido =', cantidadVerificada);
            } else {
                // NORMAL: Máximo = cantidad verificada
                cantidadDisponible = cantidadVerificada;
                console.log(' NORMAL EDICIÓN: Máximo permitido =', cantidadVerificada);
            }
        } else {
            // ═══════════════════════════════════════════════════════
            // MODO CREAR: Validar contra pendiente
            // ═══════════════════════════════════════════════════════
            cantidadDisponible = cantidadVerificada - cantidadOrdenada;
            
            if (esBaseArce) {
                console.log(' BASE ARCE CREAR: Pendiente =', cantidadDisponible);
            } else {
                console.log(' NORMAL CREAR: Pendiente =', cantidadDisponible);
            }
        }

        console.log(' Cálculo final:', {
            esBaseArce: esBaseArce,
            modoEditar: modoEditar,
            verificada: cantidadVerificada,
            ordenada: cantidadOrdenada,
            disponible: cantidadDisponible,
            intentaOrdenar: cantidadNueva
        });

        // Obtener descripción del producto (para el pop-up de errores)
        let descripcionProducto = '';
        const rowElement = cantidadInput.closest('[id^="item-orden-"]');
        if (rowElement) {
            const descripcionElement = rowElement.querySelector('strong');
            if (descripcionElement && descripcionElement.nextSibling) {
                descripcionProducto = descripcionElement.nextSibling.textContent.trim();
            }
        }

        //  Validación final: excede disponible
        if (cantidadNueva > cantidadDisponible) {
            const descripcionCorta = descripcionProducto.length > 50
                ? descripcionProducto.substring(0, 50) + '...'
                : descripcionProducto;

            const tipoItem = esNuevo ? '[NUEVO]' : '[EDITANDO]';
            
            let mensaje = '';
            
            if (modoEditar) {
                // Mensaje para modo edición
                mensaje = `
                    <strong>${tipoItem} ${descripcionCorta}:</strong><br>
                    Cantidad ingresada: <strong>${cantidadNueva}</strong><br>
                    Máximo permitido: <strong style="color: #28a745;">${cantidadDisponible.toFixed(2)}</strong><br>
                    <small class="text-muted">(En modo edición, puedes ordenar hasta la cantidad total ${esBaseArce ? 'pedida' : 'verificada'})</small>
                `;
            } else {
                // Mensaje para modo crear
                mensaje = `
                    <strong>${tipoItem} ${descripcionCorta}:</strong><br>
                    Cantidad ingresada: <strong>${cantidadNueva}</strong><br>
                    ${esBaseArce ? 'Pedida' : 'Verificada'}: ${cantidadVerificada} |
                    Ya ordenado: ${cantidadOrdenada} |
                    <strong style="color: #28a745;">Disponible: ${cantidadDisponible.toFixed(2)}</strong>
                `;
            }

            errores.push(mensaje);
        }
    });

    return errores;
}


    function obtenerCantidadActualEnOrden(idProducto, idCompraActual) {
        let cantidadActual = 0;
        
        const itemsOrden = document.querySelectorAll('[id^="item-orden-"]');
        itemsOrden.forEach(item => {
            const idProductoItem = item.querySelector('input[name*="[id_producto]"]');
            if (idProductoItem && parseInt(idProductoItem.value) === idProducto) {
                const cantidadInput = item.querySelector('.cantidad-item');
                if (cantidadInput) {
                    cantidadActual = parseFloat(cantidadInput.value) || 0;
                }
            }
        });
        
        return cantidadActual;
    }

    function validarCantidadesServicio(itemsOrden) {
        const errores = [];
        const inputIdCompra = document.querySelector('input[name="id_compra"]');
        const idCompraActual = inputIdCompra ? parseInt(inputIdCompra.value) : null;
        
        itemsOrden.forEach(itemElement => {
            const idProductoInput = itemElement.querySelector('input[name*="[id_producto]"]');
            const cantidadInput = itemElement.querySelector('.cantidad-item');
            const esNuevoInput = itemElement.querySelector('input[name*="[es_nuevo]"]');
            
            if (!idProductoInput || !cantidadInput) return;
            
            const idProducto = parseInt(idProductoInput.value);
            const cantidadNueva = parseFloat(cantidadInput.value) || 0;
            const esNuevo = esNuevoInput && esNuevoInput.value === '1';
            
            //  OBTENER id_pedido_detalle
            const idDetalleInput = itemElement.querySelector('input[name*="[id_pedido_detalle]"]');
            const idDetalleActual = idDetalleInput ? parseInt(idDetalleInput.value) : null;

            let cantidadOriginal = 0;
            let cantidadOrdenada = 0;
            let descripcionProducto = '';
            
            //  Usar data-attributes que tienen el id_pedido_detalle correcto
            if (cantidadInput.hasAttribute('data-cantidad-verificada') && cantidadInput.hasAttribute('data-cantidad-ordenada')) {
                cantidadOriginal = parseFloat(cantidadInput.getAttribute('data-cantidad-verificada')) || 0;
                cantidadOrdenada = parseFloat(cantidadInput.getAttribute('data-cantidad-ordenada')) || 0;
                
                const rowElement = cantidadInput.closest('[id^="item-orden-"]');
                if (rowElement) {
                    const descripcionElement = rowElement.querySelector('strong');
                    if (descripcionElement && descripcionElement.nextSibling) {
                        descripcionProducto = descripcionElement.nextSibling.textContent.trim();
                    }
                }
            } else if (idDetalleActual) {
                //  Buscar en items pendientes por id_pedido_detalle
                const itemsPendientes = document.querySelectorAll('.item-pendiente');
                itemsPendientes.forEach(item => {
                    const idDetalleItem = parseInt(item.getAttribute('data-id-detalle'));
                    if (idDetalleItem === idDetalleActual) {
                        const btnAgregar = item.querySelector('.btn-agregarOrden');
                        if (btnAgregar) {
                            cantidadOriginal = parseFloat(btnAgregar.dataset.cantidadVerificada) || 0;
                            cantidadOrdenada = parseFloat(btnAgregar.dataset.cantidadOrdenada) || 0;
                            descripcionProducto = btnAgregar.dataset.descripcion || `Detalle ID ${idDetalleActual}`;
                        }
                    }
                });
            }
            
            let cantidadDisponible = 0;

            if (esNuevo) {
                cantidadDisponible = cantidadOriginal - cantidadOrdenada;
            } else if (modoEditar && idCompraActual) {
                const cantidadActualEnOrden = obtenerCantidadActualEnOrden(idProducto, idCompraActual);
                cantidadDisponible = (cantidadOriginal - cantidadOrdenada) + cantidadActualEnOrden;
            } else {
                cantidadDisponible = cantidadOriginal;
            }
            
            if (cantidadNueva > cantidadDisponible) {
                const descripcionCorta = descripcionProducto.length > 50 
                    ? descripcionProducto.substring(0, 50) + '...' 
                    : descripcionProducto;
                
                const tipoItem = esNuevo ? '[NUEVO]' : '[EDITANDO]';
                
                const error = `<strong>${tipoItem} ${descripcionCorta}:</strong><br>` +
                    `Cantidad ingresada: <strong>${cantidadNueva}</strong><br>` +
                    `Original: ${cantidadOriginal.toFixed(2)} | ` +
                    `Ya ordenado (otras órdenes): ${cantidadOrdenada.toFixed(2)} | ` +
                    `<strong style="color: #28a745;">Disponible: ${cantidadDisponible.toFixed(2)}</strong>`;
                
                errores.push(error);
            }
        });
        
        return errores;
    }

    function configurarValidacionTiempoReal() {
        document.addEventListener('input', function(event) {
            if (event.target.classList.contains('cantidad-item')) {
                const cantidadInput = event.target;
                const itemElement = cantidadInput.closest('[id^="item-orden-"]');
                
                if (!itemElement) return;
                
                const idProductoInput = itemElement.querySelector('input[name*="[id_producto]"]');
                const esNuevoInput = itemElement.querySelector('input[name*="[es_nuevo]"]');
                
                if (!idProductoInput) return;
                
                const idProducto = parseInt(idProductoInput.value);
                const cantidadIngresada = parseFloat(cantidadInput.value) || 0;
                const esNuevo = esNuevoInput && esNuevoInput.value === '1';
                
                const inputIdCompra = document.querySelector('input[name="id_compra"]');
                const idCompraActual = inputIdCompra ? parseInt(inputIdCompra.value) : null;
                
                let cantidadVerificada = 0;
                let cantidadOrdenada = 0;
                let cantidadOriginal = 0;
                
                if (cantidadInput.hasAttribute('data-cantidad-verificada') && cantidadInput.hasAttribute('data-cantidad-ordenada')) {
                    if (esOrdenServicio) {
                        cantidadOriginal = parseFloat(cantidadInput.getAttribute('data-cantidad-verificada')) || 0;
                        cantidadOrdenada = parseFloat(cantidadInput.getAttribute('data-cantidad-ordenada')) || 0;
                    } else {
                        cantidadVerificada = parseFloat(cantidadInput.getAttribute('data-cantidad-verificada')) || 0;
                        cantidadOrdenada = parseFloat(cantidadInput.getAttribute('data-cantidad-ordenada')) || 0;
                    }
                }
                
                let cantidadMaxima = 0;
                
                if (esOrdenServicio) {
                    if (esNuevo) {
                        cantidadMaxima = cantidadOriginal - cantidadOrdenada;
                    } else if (modoEditar && idCompraActual) {
                        const cantidadActualEnOrden = obtenerCantidadActualEnOrden(idProducto, idCompraActual);
                        cantidadMaxima = (cantidadOriginal - cantidadOrdenada) + cantidadActualEnOrden;
                    } else {
                        cantidadMaxima = cantidadOriginal;
                    }
                } else {
                    if (esNuevo) {
                        cantidadMaxima = cantidadVerificada - cantidadOrdenada;
                    } else if (modoEditar && idCompraActual) {
                        cantidadMaxima = cantidadVerificada;
                    } else {
                        cantidadMaxima = cantidadVerificada;
                    }
                }
                
                if (cantidadIngresada > cantidadMaxima) {
                    cantidadInput.style.borderColor = '#dc3545';
                    cantidadInput.style.backgroundColor = '#f8d7da';
                    
                    let tooltip = itemElement.querySelector('.tooltip-error-cantidad');
                    if (!tooltip) {
                        tooltip = document.createElement('small');
                        tooltip.className = 'tooltip-error-cantidad text-danger';
                        tooltip.style.display = 'block';
                        tooltip.style.fontSize = '11px';
                        tooltip.style.marginTop = '2px';
                        
                        if (cantidadInput.parentElement.classList.contains('input-group')) {
                            cantidadInput.parentElement.parentElement.appendChild(tooltip);
                        } else {
                            cantidadInput.parentElement.appendChild(tooltip);
                        }
                    }
                    tooltip.textContent = `⚠ Excede máximo: ${cantidadMaxima.toFixed(2)}`;
                } else {
                    cantidadInput.style.borderColor = '#28a745';
                    cantidadInput.style.backgroundColor = '#d4edda';
                    
                    const tooltip = itemElement.querySelector('.tooltip-error-cantidad');
                    if (tooltip) tooltip.remove();
                }
            }
        });
    }

    function configurarValidacionTiempoRealSalidas() {
        document.addEventListener('input', function(event) {
            if (event.target.classList.contains('cantidad-salida')) {
                const cantidadInput = event.target;
                const itemElement = cantidadInput.closest('[id^="item-salida-"]');
                
                if (!itemElement) return;
                
                const cantidadIngresada = parseFloat(cantidadInput.value) || 0;
                
                // ✅ USAR EL ATRIBUTO 'max' DEL INPUT
                const cantidadMaxima = parseFloat(cantidadInput.getAttribute('max')) || 0;
                
                // Remover tooltip anterior
                const tooltipExistente = itemElement.querySelector('.tooltip-error-cantidad-salida');
                if (tooltipExistente) tooltipExistente.remove();
                
                if (cantidadIngresada > cantidadMaxima) {
                    cantidadInput.style.borderColor = '#dc3545';
                    cantidadInput.style.backgroundColor = '#f8d7da';
                    
                    const tooltip = document.createElement('small');
                    tooltip.className = 'tooltip-error-cantidad-salida text-danger';
                    tooltip.style.display = 'block';
                    tooltip.style.fontSize = '11px';
                    tooltip.style.marginTop = '2px';
                    tooltip.textContent = `⚠ Excede máximo: ${cantidadMaxima.toFixed(2)}`;
                    cantidadInput.parentElement.appendChild(tooltip);
                    
                } else if (cantidadIngresada <= 0) {
                    cantidadInput.style.borderColor = '#dc3545';
                    cantidadInput.style.backgroundColor = '#f8d7da';
                    
                    const tooltip = document.createElement('small');
                    tooltip.className = 'tooltip-error-cantidad-salida text-danger';
                    tooltip.style.display = 'block';
                    tooltip.style.fontSize = '11px';
                    tooltip.style.marginTop = '2px';
                    tooltip.textContent = `⚠ La cantidad debe ser mayor a 0`;
                    cantidadInput.parentElement.appendChild(tooltip);
                    
                } else {
                    cantidadInput.style.borderColor = '#28a745';
                    cantidadInput.style.backgroundColor = '#d4edda';
                }
            }
        });
    }

    // ============================================
    // VALIDACIÓN DE FORMULARIO SALIDA (CORREGIDO)
    // ============================================

    function validarFormularioSalida(e) {
    e.preventDefault();
    
    console.log('🚚 Validando formulario de salida...');
    
    const form = document.getElementById('form-nueva-salida');
    
    // Determinar si es creación o edición
    const modoEditarSalida = document.querySelector('input[name="actualizar_salida"]') !== null;
    
    console.log('📋 Modo:', modoEditarSalida ? 'EDICIÓN' : 'CREACIÓN');
    
    // Validar campos obligatorios
    const ndocInput = document.querySelector('input[name="ndoc_salida"]');
    const ndoc = ndocInput ? ndocInput.value.trim() : '';
    const fec = document.getElementById('fecha_salida').value;
    const almOrigen = document.getElementById('almacen_origen_salida').value;
    const ubicOrigen = document.getElementById('ubicacion_origen_salida').value;
    const almDestino = document.getElementById('almacen_destino_salida').value;
    const ubicDestino = document.getElementById('ubicacion_destino_salida').value;
    const inputArchivos = document.getElementById('documentos_salida');

    console.log('📋 Datos capturados:', {
        ndoc, fec, almOrigen, ubicOrigen, almDestino, ubicDestino
    });
    
    // Validaciones básicas (igual que antes)
    /*
    if (!ndoc) {
        Swal.fire({
            icon: 'warning',
            title: 'Campo Requerido',
            text: 'Debe ingresar el número de documento',
            confirmButtonColor: '#3085d6'
        });
        return;
    }
    */
    
    if (!fec) {
        Swal.fire({
            icon: 'warning',
            title: 'Campo Requerido',
            text: 'Debe seleccionar la fecha de salida',
            confirmButtonColor: '#3085d6'
        });
        return;
    }
    
    if (!almOrigen || almOrigen == '0') {
        Swal.fire({
            icon: 'warning',
            title: 'Campo Requerido',
            text: 'Debe seleccionar el almacén de origen',
            confirmButtonColor: '#3085d6'
        });
        return;
    }
    
    if (!ubicOrigen || ubicOrigen == '0') {
        Swal.fire({
            icon: 'warning',
            title: 'Campo Requerido',
            text: 'Debe seleccionar la ubicación de origen',
            confirmButtonColor: '#3085d6'
        });
        return;
    }
    
    if (!almDestino || almDestino == '0') {
        Swal.fire({
            icon: 'warning',
            title: 'Campo Requerido',
            text: 'Debe seleccionar el almacén de destino',
            confirmButtonColor: '#3085d6'
        });
        return;
    }
    
    if (!ubicDestino || ubicDestino == '0') {
        Swal.fire({
            icon: 'warning',
            title: 'Campo Requerido',
            text: 'Debe seleccionar la ubicación de destino',
            confirmButtonColor: '#3085d6'
        });
        return;
    }

     /*
    if (!modoEditarSalida) {
        if (!inputArchivos || !inputArchivos.files || inputArchivos.files.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Documentos Requeridos',
                text: 'Debe subir al menos un documento para generar la salida',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
    }
    */

    if (inputArchivos && inputArchivos.files && inputArchivos.files.length > 0) {
        const maxSize = 5 * 1024 * 1024;
        let archivosPesados = [];

        for (let i = 0; i < inputArchivos.files.length; i++) {
            if (inputArchivos.files[i].size > maxSize) {
                archivosPesados.push(inputArchivos.files[i].name);
            }
        }

        if (archivosPesados.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Archivos muy pesados',
                html: `
                    <div style="text-align: left;">
                        <p>Los siguientes archivos exceden el tamaño máximo de 5MB:</p>
                        <ul style="color: #d33;">
                            ${archivosPesados.map(f => `<li><strong>${f}</strong></li>`).join('')}
                        </ul>
                    </div>
                `,
                confirmButtonColor: '#d33'
            });
            return;
        }
        
        console.log(`📎 Archivos nuevos a subir: ${inputArchivos.files.length}`);
    }

    
    // ✅ RECOLECTAR ITEMS (CORREGIDO)
    const contenedorItems = document.getElementById('contenedor-items-salida');
    const itemsElements = contenedorItems.querySelectorAll('div[id^="item-salida-"]');
    
    console.log('📦 Items encontrados en DOM:', itemsElements.length);
    
    if (itemsElements.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Sin Items',
            text: 'Debe agregar al menos un material a la salida',
            confirmButtonColor: '#3085d6'
        });
        return;
    }
    
    // ✅ CONSTRUIR ARRAY DE ITEMS (CORREGIDO)
    const itemsSalida = [];
    let errorCantidad = false;
    let mensajeError = '';
    
    itemsElements.forEach((item, index) => {
        console.log(`🔍 Procesando item ${index}:`, item.id);
        
        // 🔹 BUSCAR TODOS LOS INPUTS (INCLUYENDO id_salida_detalle)
        const idSalidaDetalleInput = item.querySelector('input[name*="[id_salida_detalle]"]');
        const idProductoInput = item.querySelector('input[name*="[id_producto]"]');
        const idPedidoDetalleInput = item.querySelector('input[name*="[id_pedido_detalle]"]');
        const cantidadInput = item.querySelector('input[name*="[cantidad]"]');
        const descripcionInput = item.querySelector('input[name*="[descripcion]"]');
        
        console.log(`   Inputs encontrados:`, {
            idSalidaDetalle: idSalidaDetalleInput ? 'SÍ' : 'NO',
            idProducto: idProductoInput ? 'SÍ' : 'NO',
            idPedidoDetalle: idPedidoDetalleInput ? 'SÍ' : 'NO',
            cantidad: cantidadInput ? 'SÍ' : 'NO',
            descripcion: descripcionInput ? 'SÍ' : 'NO'
        });
        
        if (!idProductoInput || !cantidadInput) {
            console.warn(`⚠️ Item ${index} sin inputs necesarios`);
            return;
        }
        
        // 🔹 CAPTURAR VALORES
        const idSalidaDetalle = idSalidaDetalleInput ? parseInt(idSalidaDetalleInput.value) : 0;
        const idProducto = parseInt(idProductoInput.value);
        const idPedidoDetalle = idPedidoDetalleInput ? parseInt(idPedidoDetalleInput.value) : 0;
        const cantidad = parseFloat(cantidadInput.value) || 0;
        
        // 🔹 OBTENER DESCRIPCIÓN
        let descripcion = '';
        
        if (descripcionInput) {
            descripcion = descripcionInput.value;
            if (descripcion.startsWith('Descripción:')) {
                descripcion = descripcion.replace('Descripción:', '').trim();
            }
        }
        
        if (!descripcion) {
            const descripcionDiv = item.querySelector('div[style*="font-size: 12px"]');
            if (descripcionDiv) {
                const textoCompleto = descripcionDiv.textContent || '';
                const match = textoCompleto.match(/Descripción:\s*(.+?)(?:\s*EDITANDO|\s*SALIDA|$)/);
                descripcion = match ? match[1].trim() : `Producto ${idProducto}`;
            } else {
                descripcion = `Producto ${idProducto}`;
            }
        }
        
        const cantidadMaxima = parseFloat(cantidadInput.getAttribute('max')) || 0;
        
        console.log(`📦 Item ${index} procesado:`, {
            idSalidaDetalle,  // 🔥 AHORA SÍ SE CAPTURA
            idProducto,
            idPedidoDetalle,
            cantidad,
            descripcion: descripcion.substring(0, 50),
            max: cantidadMaxima
        });
        
        // Validar cantidad
        if (cantidad <= 0) {
            errorCantidad = true;
            mensajeError = `La cantidad para "${descripcion}" debe ser mayor a 0`;
            return;
        }
        
        if (cantidadMaxima > 0 && cantidad > cantidadMaxima) {
            errorCantidad = true;
            mensajeError = `La cantidad para "${descripcion}" (${cantidad}) excede el máximo disponible (${cantidadMaxima})`;
            return;
        }
        
        // 🔥 AGREGAR AL ARRAY CON id_salida_detalle
        itemsSalida.push({
            id_salida_detalle: idSalidaDetalle,  // ← CRÍTICO
            id_producto: idProducto,
            id_pedido_detalle: idPedidoDetalle,
            cantidad: cantidad,
            descripcion: descripcion.replace(/[^\x00-\x7F]/g, ''),
            es_nuevo: idSalidaDetalle > 0 ? '0' : '1'  // ← NUEVO
        });
    });
    
    if (errorCantidad) {
        Swal.fire({
            icon: 'error',
            title: 'Cantidad Inválida',
            text: mensajeError,
            confirmButtonColor: '#d33'
        });
        return;
    }
    
    if (itemsSalida.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Sin Items Válidos',
            text: 'No se encontraron items válidos para la salida',
            confirmButtonColor: '#3085d6'
        });
        return;
    }
    
    console.log('✅ Items válidos para enviar:', itemsSalida);
    
    // ✅ CONSTRUIR FORMDATA
    const formData = new FormData(form);
    
    // ✅ ENVIAR ITEMS COMO JSON STRING
    formData.append('items_salida', JSON.stringify(itemsSalida));
    
    console.log('📤 Enviando datos al servidor...');
    
    // Confirmar acción
    const textoConfirmacion = modoEditarSalida 
        ? '¿Está seguro de actualizar esta salida?' 
        : '¿Está seguro de generar esta salida?';
    
    const textoBoton = modoEditarSalida ? 'Sí, actualizar' : 'Sí, generar';
    
    Swal.fire({
        title: textoConfirmacion,
        text: "Esta acción afectará el inventario",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: textoBoton,
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: modoEditarSalida ? 'Actualizando...' : 'Generando...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Enviar formulario
            fetch('pedido_verificar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                console.log('📥 Respuesta del servidor:', data);
                
                // Manejo de errores de stock
                if (data.tipo === 'error_stock_reverificado' && data.accion === 'recargar_pagina') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Ajuste Automático Realizado',
                        html: `
                            <div style="text-align: left; padding: 15px;">
                                <p style="margin-bottom: 15px; color: #856404;">
                                    <i class="fa fa-exclamation-triangle"></i> 
                                    <strong>Stock Insuficiente Detectado:</strong>
                                </p>
                                <div style="background-color: #fff3cd; padding: 10px; border-radius: 5px; margin-bottom: 15px; border-left: 4px solid #ffc107;">
                                    ${data.message}
                                </div>
                                <hr>
                            </div>
                        `,
                        confirmButtonText: '<i class="fa fa-sync"></i> Ver Cambios',
                        confirmButtonColor: '#28a745',
                        allowOutsideClick: false,
                        width: '600px'
                    }).then(() => {
                        window.location.reload();
                    });
                    return;
                }
                
                //  ÉXITO
                if (data.success) {
                    const idPedido = document.querySelector('input[name="id_pedido"]').value;
                    const successParam = `success=${modoEditarSalida ? 'salida_actualizada' : 'salida_creada'}`;
                    window.location.href = `pedido_verificar.php?id=${idPedido}&${successParam}&validate=1`;
                }else {
                    // ❌ OTRO ERROR
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: `<div style="text-align: left;">${data.message || 'Ocurrió un error al procesar la solicitud'}</div>`,
                        confirmButtonColor: '#d33'
                    });
                }
            })
            .catch(error => {
                console.error('❌ Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Conexión',
                    text: 'No se pudo conectar con el servidor.',
                    confirmButtonColor: '#d33'
                });
            });
        }
    });
}


        
    // ============================================
    // CONFIGURACIÓN DE EVENTOS EDICIÓN
    // ============================================
    
    function configurarEventosEdicion() {
        document.querySelectorAll('[id^="item-orden-"]').forEach(function(item) {
            const cantidadInput = item.querySelector('.cantidad-item');
            const precioInput = item.querySelector('.precio-item');
            const igvInput = item.querySelector('.igv-item');
            const idDetalle = item.id.replace('item-orden-', '');
            
            if (cantidadInput && !cantidadInput.dataset.cantidadOriginal) {
                cantidadInput.dataset.cantidadOriginal = cantidadInput.value;
            }
        
            function calcularTotales() {
                const cantidad = parseFloat(cantidadInput.value) || 0;
                const precio = parseFloat(precioInput.value) || 0;
                const igvPorcentaje = parseFloat(igvInput.value) || 0;
                
                const subtotal = cantidad * precio;
                const montoIgv = subtotal * (igvPorcentaje / 100);
                const total = subtotal + montoIgv;
                
                const simboloMoneda = obtenerSimboloMoneda();
                const calculoDiv = document.getElementById(`calculo-${idDetalle}`);
                if (calculoDiv) {
                    calculoDiv.querySelector('.subtotal-text').textContent = `Subtotal: ${simboloMoneda} ${subtotal.toFixed(2)}`;
                    calculoDiv.querySelector('.igv-text').textContent = `IGV: ${simboloMoneda} ${montoIgv.toFixed(2)}`;
                    calculoDiv.querySelector('.total-text').textContent = `Total: ${simboloMoneda} ${total.toFixed(2)}`;
                }
                actualizarTotalGeneral();
            }
            
            if (cantidadInput) cantidadInput.addEventListener('input', calcularTotales);
            if (precioInput) precioInput.addEventListener('input', calcularTotales);
            if (igvInput) igvInput.addEventListener('input', calcularTotales);
            
            calcularTotales();
        });
        
        setTimeout(function() {
            actualizarTotalGeneral();
        }, 100);
    }

    // Actualizar la función cargarSalidaEdicion - Línea ~730
    function cargarSalidaEdicion() {
        if (!modoEditarSalida) return;
        
        console.log('📝 Cargando salida en modo edición...');
        
        // Llenar campos básicos
        const ndocInput = document.querySelector('input[name="ndoc_salida"]');
        const fechaInput = document.getElementById('fecha_salida');
        const obsInput = document.getElementById('observaciones_salida');
        const almacenOrigenSelect = document.getElementById('almacen_origen_salida');
        const ubicacionOrigenSelect = document.getElementById('ubicacion_origen_salida');
        const almacenDestinoSelect = document.getElementById('almacen_destino_salida');
        const ubicacionDestinoSelect = document.getElementById('ubicacion_destino_salida');
        
        if (ndocInput) ndocInput.value = salidaEditar.ndoc || '';
        if (fechaInput) fechaInput.value = salidaEditar.fecha ? salidaEditar.fecha.split(' ')[0] : '';
        if (obsInput) obsInput.value = salidaEditar.obs || '';
        if (almacenOrigenSelect) almacenOrigenSelect.value = salidaEditar.almacen_origen || '';
        if (almacenDestinoSelect) almacenDestinoSelect.value = salidaEditar.almacen_destino || '';
        
        setTimeout(() => {
            if (ubicacionOrigenSelect) ubicacionOrigenSelect.value = salidaEditar.ubicacion_origen || '';
            if (ubicacionDestinoSelect) ubicacionDestinoSelect.value = salidaEditar.ubicacion_destino || '';
        }, 200);
        
        const contenedor = document.getElementById('contenedor-items-salida');
        if (!contenedor) return;
        
        // Limpiar el contenedor
        contenedor.innerHTML = '';
        
        console.log('🗑️ Contenedor limpiado');
        
        // Recorrer cada item de la salida a editar
        itemsSalidaEditar.forEach(item => {
            console.log('📦 Procesando item edición:', item);
            
            const idSalidaDetalle = item.id_salida_detalle;
            const idPedidoDetalle = parseInt(item.id_pedido_detalle) || 0;
            const cantActualEnSalida = parseFloat(item.cant_salida_detalle) || 0;
            const cantidadMaxima = parseFloat(item.cantidad_maxima) || cantActualEnSalida;
            
            // Crear el item con el mismo diseño que agregarItemASalida
            const itemElement = document.createElement('div');
            itemElement.id = `item-salida-${idSalidaDetalle}`;
            itemElement.classList.add('alert', 'alert-light', 'p-2', 'mb-2');
            itemElement.style.border = '1px solid #dee2e6';
            itemElement.style.borderRadius = '4px';
            
            // 🔹 Obtener código del producto si está disponible
            const codigoProducto = item.cod_material || 'N/A';
            
            // 🔹 HTML unificado - mismo diseño que agregarItemASalida
            itemElement.innerHTML = `
                <!-- Inputs ocultos -->
                <input type="hidden" name="items_salida[${idSalidaDetalle}][id_salida_detalle]" value="${idSalidaDetalle}">
                <input type="hidden" name="items_salida[${idSalidaDetalle}][id_producto]" value="${item.id_producto}">
                <input type="hidden" name="items_salida[${idSalidaDetalle}][id_pedido_detalle]" value="${idPedidoDetalle}">
                <input type="hidden" name="items_salida[${idSalidaDetalle}][es_nuevo]" value="0">
                
                <!-- DISEÑO COMPACTO EN UNA SOLA FILA -->
                <div class="row align-items-center" style="margin: 0;">
                    <!-- CÓDIGO -->
                    <div class="col-md-2" style="padding: 4px 8px;">
                        <label style="font-size: 10px; font-weight: bold; margin-bottom: 2px; display: block; color: #6c757d;">Código:</label>
                        <input type="text" 
                            class="form-control form-control-sm" 
                            value="${codigoProducto}"
                            readonly
                            style="font-size: 11px; background-color: #f8f9fa; border: 1px solid #e0e0e0; padding: 4px 8px;">
                    </div>
                    
                    <!-- DESCRIPCIÓN -->
                    <div class="col-md-5" style="padding: 4px 8px;">
                        <label style="font-size: 10px; font-weight: bold; margin-bottom: 2px; display: block; color: #6c757d;">Descripción:</label>
                        <div style="font-size: 11px; padding: 4px 8px; background-color: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 4px; line-height: 1.3;">
                            ${item.nom_producto}
                            <span class="badge badge-warning badge-sm ml-1" style="font-size: 9px;">EDITANDO</span>
                        </div>
                    </div>
                    
                    <!-- CANTIDAD -->
                    <div class="col-md-3" style="padding: 4px 8px;">
                        <label style="font-size: 10px; font-weight: bold; margin-bottom: 2px; display: block; color: #6c757d;">Cantidad:</label>
                        <input type="number" 
                            class="form-control form-control-sm cantidad-salida" 
                            name="items_salida[${idSalidaDetalle}][cantidad]"
                            value="${cantActualEnSalida.toFixed(2)}" 
                            min="0.01" 
                            max="${cantidadMaxima.toFixed(2)}" 
                            step="0.01"
                            data-cantidad-maxima="${cantidadMaxima}"
                            data-cantidad-maxima-os="${cantidadMaxima}"
                            style="font-size: 12px; font-weight: bold; padding: 4px 8px; text-align: center;" 
                            required>
                        <small class="text-info" style="font-size: 9px; display: block; text-align: center; margin-top: 1px;">
                            <i class="fa fa-arrow-up"></i> Máx: ${cantidadMaxima.toFixed(2)}
                        </small>
                    </div>
                    
                    <!-- BOTÓN ELIMINAR -->
                    <div class="col-md-2 text-center" style="padding: 4px;">
                        <button type="button" 
                            class="btn btn-danger btn-sm btn-remover-item-salida" 
                            data-id-detalle="${idSalidaDetalle}"
                            data-id-producto="${item.id_producto}"
                            title="Eliminar item"
                            style="padding: 4px 8px; margin-top: 18px;">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            
            contenedor.appendChild(itemElement);
            
            // 🔹 Agregar evento al botón de eliminar
            const btnRemover = itemElement.querySelector('.btn-remover-item-salida');
            if (btnRemover) {
                btnRemover.addEventListener('click', function() {
                    removerItemDeSalida(idSalidaDetalle, null);
                });
            }
        });
        
        console.log('✅ Items de edición cargados con diseño unificado');
    }
        
    // ============================================
    // CONFIGURACIÓN DE EVENT LISTENERS
    // ============================================
    
    function configurarEventListeners() {
        // ============================================
        // 1. BOTÓN NUEVA SALIDA
        // ============================================
        const btnNuevaSalidaElement = document.getElementById('btn-nueva-salida');
        if (btnNuevaSalidaElement) {
            btnNuevaSalidaElement.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('🔘 Botón Nueva Salida clickeado');
                
                // 🔹 NO PERMITIR EN SERVICIOS
                if (esOrdenServicio) {
                    Swal.fire({
                        icon: 'info',
                        title: 'No disponible',
                        text: 'Las salidas no aplican para órdenes de servicio.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                
                const contenedorNuevaSalida = document.getElementById('contenedor-nueva-salida');
                
                // 🔹 TOGGLE: Si está mostrando formulario, volver a lista
                if (contenedorNuevaSalida && contenedorNuevaSalida.style.display === 'block') {
                    console.log('🔙 Volviendo a lista de salidas');
                    mostrarListaSalidas();
                    return;
                }
                
                // 🔹 VALIDAR DISPONIBILIDAD ANTES DE MOSTRAR FORMULARIO
                const hayItemsDisponibles = validarItemsDisponiblesParaSalida();
                
                console.log(' Validación antes de mostrar formulario:', {
                    hayDisponibles: hayItemsDisponibles,
                    botonDisabled: this.disabled
                });
                
                if (!hayItemsDisponibles) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Sin items disponibles',
                        text: 'No hay items disponibles para generar una salida.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                
                console.log(' Mostrando formulario de nueva salida');
                mostrarFormularioNuevaSalida();
                
                // ✅ NUEVO: Ocultar el formulario de Nueva Orden si está visible
                const contenedorNuevaOrden = document.getElementById('contenedor-nueva-orden');
                if (contenedorNuevaOrden) {
                    contenedorNuevaOrden.style.display = 'none';
                }
                
                // ✅ NUEVO: Restaurar el botón Nueva Orden a su estado original
                const btnNuevaOrden = document.getElementById('btn-nueva-orden');
                if (btnNuevaOrden) {
                    btnNuevaOrden.innerHTML = '<i class="fa fa-shopping-cart"></i> Nueva Orden';
                    btnNuevaOrden.classList.remove('btn-secondary');
                    btnNuevaOrden.classList.add('btn-primary');
                }
            });
        }
        
        // ============================================
        // 2. BOTÓN NUEVA ORDEN
        // ============================================
        const btnNuevaOrden = document.getElementById('btn-nueva-orden');
        if (btnNuevaOrden) {
            btnNuevaOrden.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('🔘 Botón Nueva Orden clickeado');
                
                const contenedorNuevaOrden = document.getElementById('contenedor-nueva-orden');
                const myTab = document.getElementById('myTab');
                const myTabContent = document.getElementById('myTabContent');
                
                console.log('📋 Estado actual:', {
                    displayNuevaOrden: contenedorNuevaOrden ? contenedorNuevaOrden.style.display : 'N/A',
                    displayTabs: myTab ? myTab.style.display : 'N/A'
                });
                
                // ✅ TOGGLE: Si el formulario está visible, volver a los tabs
                if (contenedorNuevaOrden && contenedorNuevaOrden.style.display === 'block') {
                    console.log('🔙 Ocultando formulario, mostrando tabs');
                    
                    // Ocultar formulario
                    contenedorNuevaOrden.style.display = 'none';
                    
                    // Mostrar tabs
                    if (myTab) myTab.style.display = 'flex';
                    if (myTabContent) myTabContent.style.display = 'block';
                    
                    // Cambiar botón a estado "Nueva Orden"
                    this.innerHTML = '<i class="fa fa-shopping-cart"></i> Nueva Orden';
                    this.classList.remove('btn-secondary');
                    this.classList.add('btn-primary');
                    
                    // Activar tab de órdenes
                    const ordenesTab = document.getElementById('ordenes-tab');
                    if (ordenesTab) {
                        $('#ordenes-tab').tab('show');
                    }
                    
                    console.log('✅ Tabs mostrados correctamente');
                } else {
                    // ✅ Mostrar formulario, ocultar tabs
                    console.log('➡️ Mostrando formulario, ocultando tabs');
                    
                    // Ocultar tabs
                    if (myTab) myTab.style.display = 'none';
                    if (myTabContent) myTabContent.style.display = 'none';
                    
                    //  NUEVO: Ocultar el formulario de Nueva Salida si está visible
                    const contenedorNuevaSalida = document.getElementById('contenedor-nueva-salida');
                    if (contenedorNuevaSalida) {
                        contenedorNuevaSalida.style.display = 'none';
                    }
                    
                    //  NUEVO: Restaurar el botón Nueva Salida a su estado original
                    const btnNuevaSalida = document.getElementById('btn-nueva-salida');
                    if (btnNuevaSalida) {
                        const hayItemsDisponibles = validarItemsDisponiblesParaSalida();
                        if (hayItemsDisponibles) {
                            btnNuevaSalida.innerHTML = '<i class="fa fa-truck"></i> Nueva Salida';
                            btnNuevaSalida.classList.remove('btn-secondary');
                            btnNuevaSalida.classList.add('btn-success');
                            btnNuevaSalida.disabled = false;
                        }
                    }
                    
                    // Mostrar formulario
                    if (contenedorNuevaOrden) {
                        contenedorNuevaOrden.style.display = 'block';
                    }
                    
                    // Cambiar botón a estado "Ver Órdenes"
                    this.innerHTML = '<i class="fa fa-table"></i> Ver Órdenes';
                    this.classList.remove('btn-primary');
                    this.classList.add('btn-secondary');
                    
                    // Establecer fecha actual
                    const fechaOrden = document.getElementById('fecha_orden');
                    if (fechaOrden && !fechaOrden.value) {
                        fechaOrden.value = '<?php echo date('Y-m-d'); ?>';
                    }
                    
                    console.log('✅ Formulario mostrado correctamente');
                }
            });
        }
        
        // ============================================
        // 🔹 3. AGREGAR ITEM A ORDEN (ÚNICO LISTENER)
        // ============================================
        document.addEventListener('click', function(event) {
            const btnAgregar = event.target.closest('.btn-agregarOrden');
            if (btnAgregar) {
                event.preventDefault();
                event.stopPropagation();
                
                console.log('🔵 Botón Agregar clickeado:', {
                    idProducto: btnAgregar.dataset.idProducto,
                    descripcion: btnAgregar.dataset.descripcion,
                    verificada: btnAgregar.dataset.cantidadVerificada,
                    ordenada: btnAgregar.dataset.cantidadOrdenada,
                    pendiente: btnAgregar.dataset.cantidadPendiente
                });
                
                const contenedorNuevaOrden = document.getElementById('contenedor-nueva-orden');
                const myTab = document.getElementById('myTab');
                const myTabContent = document.getElementById('myTabContent');
                
                console.log('🔍 Estado ANTES de mostrar formulario:', {
                    modoEditar: modoEditar,
                    contenedorNuevaOrden: contenedorNuevaOrden ? 'existe' : 'NO EXISTE',
                    displayNuevaOrden: contenedorNuevaOrden ? contenedorNuevaOrden.style.display : 'N/A',
                    myTab: myTab ? 'existe' : 'NO EXISTE',
                    myTabContent: myTabContent ? 'existe' : 'NO EXISTE'
                });
                
                if (modoEditar) {
                    console.log('🟢 MODO EDITAR - Mostrando contenedor-nueva-orden');
                    if (contenedorNuevaOrden && contenedorNuevaOrden.style.display === 'none') {
                        contenedorNuevaOrden.style.display = 'block';
                        console.log('✅ contenedor-nueva-orden ahora es visible');
                    }
                } else {
                    console.log('🟢 MODO NORMAL - Mostrando formulario nueva orden');
                    if (myTab) myTab.style.display = 'none';
                    if (myTabContent) myTabContent.style.display = 'none';
                    if (contenedorNuevaOrden) {
                        contenedorNuevaOrden.style.display = 'block';
                        console.log('✅ Formulario de nueva orden mostrado');
                    }
                    
                    const btnNuevaOrden = document.getElementById('btn-nueva-orden');
                    if (btnNuevaOrden) {
                        btnNuevaOrden.innerHTML = '<i class="fa fa-table"></i> Ver Órdenes';
                        btnNuevaOrden.classList.remove('btn-primary');
                        btnNuevaOrden.classList.add('btn-secondary');
                    }
                    
                    const fechaOrden = document.getElementById('fecha_orden');
                    if (fechaOrden) {
                        fechaOrden.value = new Date().toISOString().split('T')[0];
                    }
                }
                
                console.log('🔍 Estado DESPUÉS de mostrar formulario:', {
                    displayNuevaOrden: contenedorNuevaOrden ? contenedorNuevaOrden.style.display : 'N/A'
                });
                
                const cantidadVerificada = parseFloat(btnAgregar.dataset.cantidadVerificada) || 0;
                const cantidadOrdenada = parseFloat(btnAgregar.dataset.cantidadOrdenada) || 0;
                
                console.log('📦 Datos para agregar:', {
                    verificada: cantidadVerificada,
                    ordenada: cantidadOrdenada,
                    pendiente: (cantidadVerificada - cantidadOrdenada).toFixed(2)
                });
                                
                agregarItemAOrden({
                    idDetalle: btnAgregar.dataset.idDetalle,
                    idProducto: btnAgregar.dataset.idProducto,
                    descripcion: btnAgregar.dataset.descripcion,
                    cantidadVerificada: parseFloat(btnAgregar.dataset.cantidadVerificada) || 0,
                    cantidadOrdenada: parseFloat(btnAgregar.dataset.cantidadOrdenada) || 0,
                    cantidadPendiente: parseFloat(btnAgregar.dataset.cantidadPendiente) || 0,
                    es_base_arce: btnAgregar.dataset.esBaseArce,
                    requisitos: btnAgregar.dataset.requisitos || '',  // ⬅️ AGREGAR ESTA LÍNEA
                    botonOriginal: btnAgregar
                });
            }
        });
        
        // ============================================
        // 4. AGREGAR ITEM A SALIDA
        // ============================================
        document.addEventListener('click', function(event) {
            const btnAgregarSalida = event.target.closest('.btn-agregarSalida');
            if (btnAgregarSalida) {
                event.preventDefault();
                event.stopPropagation();
                
                console.log('🚚 Botón Agregar a Salida clickeado');
                
                const contenedorNuevaSalida = document.getElementById('contenedor-nueva-salida');
                if (contenedorNuevaSalida.style.display === 'none') {
                    mostrarFormularioNuevaSalida();
                }
                
                // Parsear otras ubicaciones
                let otrasUbicaciones = [];
                try {
                    const otrasUbicacionesStr = btnAgregarSalida.dataset.otrasUbicaciones;
                    otrasUbicaciones = otrasUbicacionesStr ? JSON.parse(otrasUbicacionesStr) : [];
                } catch (e) {
                    console.error('Error parseando otras_ubicaciones:', e);
                }
                
                console.log('📍 Otras ubicaciones:', otrasUbicaciones);
                
                agregarItemASalida({
                    idDetalle: btnAgregarSalida.dataset.idDetalle,
                    idProducto: btnAgregarSalida.dataset.idProducto,
                    codigoProducto: btnAgregarSalida.dataset.codigoProducto,
                    descripcion: btnAgregarSalida.dataset.descripcion,
                    cantidadDisponible: btnAgregarSalida.dataset.cantidadDisponible,
                    almacenDestino: btnAgregarSalida.dataset.almacenDestino,
                    ubicacionDestino: btnAgregarSalida.dataset.ubicacionDestino,
                    otrasUbicaciones: btnAgregarSalida.dataset.otrasUbicaciones,
                    botonOriginal: btnAgregarSalida
                });
            }
        });
        
        // Ver detalle de salida
        document.addEventListener('click', function(event) {
            const btnVerSalida = event.target.closest('.btn-ver-salida');
            if (btnVerSalida) {
                event.preventDefault();
                const idSalida = btnVerSalida.getAttribute('data-id-salida');
                mostrarDetalleSalida(idSalida);
            }
        });
        
        // Editar salida
        document.addEventListener('click', function(event) {
            const btnEditarSalida = event.target.closest('.btn-editar-salida');
            if (btnEditarSalida) {
                event.preventDefault();
                const idSalida = btnEditarSalida.getAttribute('data-id-salida');
                window.location.href = `pedido_verificar.php?id=<?php echo $id_pedido; ?>&id_salida=${idSalida}`;
            }
        });
        
        // Anular salida
        document.addEventListener('click', function(event) {
            const btnAnularSalida = event.target.closest('.btn-anular-salida');
            if (btnAnularSalida) {
                event.preventDefault();
                const idSalida = btnAnularSalida.getAttribute('data-id-salida');
                anularSalida(idSalida);
            }
        });
        
        // Anular orden
        document.addEventListener('click', function(event) {
            const btnAnular = event.target.closest('.btn-anular-orden');
            if (btnAnular) {
                event.preventDefault();
                event.stopPropagation();
                const idCompra = btnAnular.getAttribute('data-id-compra');
                const idPedido = btnAnular.getAttribute('data-id-pedido');
                AnularCompra(idCompra, idPedido);
            }
        });
        
        // Editar orden
        document.addEventListener('click', function(event) {
            const btnEditar = event.target.closest('.btn-editar-orden');
            if (btnEditar) {
                event.preventDefault();
                const idCompra = btnEditar.getAttribute('data-id-compra');
                window.location.href = `pedido_verificar.php?id=<?php echo $id_pedido; ?>&id_compra=${idCompra}`;
            }
        });
        
        // Cambio de moneda
        const selectMoneda = document.getElementById('moneda_orden');
        if (selectMoneda) {
            selectMoneda.addEventListener('change', function() {
                actualizarEtiquetasMoneda(this.value);
            });
        }
        
        // Verificar modal
        /*document.querySelectorAll('.verificar-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const idDetalle = this.getAttribute('data-id-detalle');
                const cantidadActual = this.getAttribute('data-cantidad-actual');
                const cantidadDisponible = parseFloat(this.getAttribute('data-cantidad-disponible'));
                const diferencia = cantidadActual - cantidadDisponible;
                document.getElementById('id_pedido_detalle_input').value = idDetalle;
                document.getElementById('fin_cant_pedido_detalle').value = diferencia;
                $('#verificarModal').modal('show');
            }); 
        });*/
        
        // Ver detalle orden
        document.addEventListener('click', function(event) {
            const btnVerDetalle = event.target.closest('.btn-ver-detalle');
            if (btnVerDetalle) {
                event.preventDefault();
                event.stopPropagation();
                const idCompra = btnVerDetalle.getAttribute('data-id-compra');
                mostrarDetalleCompra(idCompra);
            }
        });
        
        // Validar formulario orden
        const formNuevaOrden = document.getElementById('form-nueva-orden');
        if (formNuevaOrden) {
            formNuevaOrden.addEventListener('submit', validarFormularioOrden);
        }

        // Validar formulario salida
        const formNuevaSalida = document.getElementById('form-nueva-salida');
        if (formNuevaSalida) {
            formNuevaSalida.addEventListener('submit', validarFormularioSalida);
        }
        
        // LISTENER PARA REMOVER ITEMS (CON TRACKING DE ELIMINADOS)
        document.addEventListener('click', function(event) {
            const btnRemover = event.target.closest('.btn-remover-item');
            if (btnRemover) {
                const idDetalle = btnRemover.getAttribute('data-id-detalle');
                const idCompraDetalle = btnRemover.getAttribute('data-id-compra-detalle');
                const itemElement = document.getElementById(`item-orden-${idDetalle}`);
                
                if (itemElement) {
                    // Si es un item existente en modo editar, agregarlo a eliminados
                    if (modoEditar && idCompraDetalle && parseInt(idCompraDetalle) > 0) {
                        itemsEliminadosOrden.push(parseInt(idCompraDetalle));
                    }
                    
                    // Buscar el producto para reactivar el botón
                    const idProductoInput = itemElement.querySelector('input[name*="[id_producto]"]');
                    if (idProductoInput) {
                        const idProducto = idProductoInput.value;
                        
                        const botonesAgregar = document.querySelectorAll('.btn-agregarOrden');
                        botonesAgregar.forEach(btn => {
                            if (btn.dataset.idProducto === idProducto) {
                                btn.disabled = false;
                                btn.innerHTML = '<i class="fa fa-check"></i> Agregar a Orden';
                                btn.classList.remove('btn-success');
                                btn.classList.add('btn-primary');
                            }
                        });
                    }
                    
                    // Remover del DOM
                    itemElement.remove();
                    itemsAgregadosOrden.delete(idDetalle);
                    actualizarTotalGeneral();
                }
            }
        });

        // Remover item de salida (SIMPLIFICADO - IGUAL QUE ORDEN)
        document.addEventListener('click', function(event) {
            const btnRemoverSalida = event.target.closest('.btn-remover-item-salida');
            if (btnRemoverSalida) {
                const idDetalle = btnRemoverSalida.getAttribute('data-id-detalle');
                const itemElement = document.getElementById(`item-salida-${idDetalle}`);
                
                if (itemElement) {
                    // Buscar el botón original para habilitarlo nuevamente
                    const idProductoInput = itemElement.querySelector('input[name*="[id_producto]"]');
                    if (idProductoInput) {
                        const idProducto = idProductoInput.value;
                        
                        const botonesAgregarSalida = document.querySelectorAll('.btn-agregarSalida');
                        botonesAgregarSalida.forEach(btn => {
                            if (btn.dataset.idProducto === idProducto) {
                                btn.disabled = false;
                                btn.innerHTML = '<i class="fa fa-truck"></i> Agregar a Salida';
                                btn.classList.remove('btn-secondary');
                                btn.classList.add('btn-success');
                            }
                        });
                    }
                    
                    // Eliminar el item directamente
                    itemElement.remove();
                }
            }
        });
        
        // 🔹 LISTENER: Cambio de ubicación origen - actualiza máx de todos los items
        const ubicacionOrigenSelect = document.getElementById('ubicacion_origen_salida');
        if (ubicacionOrigenSelect) {
            ubicacionOrigenSelect.addEventListener('change', function() {
                console.log('🔄 Cambio de ubicación origen detectado');
                
                const optionSeleccionada = this.options[this.selectedIndex];
                const stockNuevo = parseFloat(optionSeleccionada.getAttribute('data-stock')) || 0;
                
                console.log(`📦 Nuevo stock en ubicación: ${stockNuevo}`);
                
                // Actualizar todos los items del formulario
                document.querySelectorAll('.cantidad-salida').forEach(input => {
                    const cantidadOS = parseFloat(input.getAttribute('data-cantidad-maxima-os')) || 0;
                    const nuevoMax = Math.min(cantidadOS, stockNuevo);
                    
                    console.log(`  ↳ Item actualizado: OS=${cantidadOS}, Stock=${stockNuevo}, Nuevo Max=${nuevoMax}`);
                    
                    input.setAttribute('max', nuevoMax.toFixed(2));
                    input.setAttribute('data-cantidad-maxima', nuevoMax);
                    
                    // Si la cantidad actual excede el nuevo max, ajustarla
                    const valorActual = parseFloat(input.value) || 0;
                    if (valorActual > nuevoMax) {
                        input.value = nuevoMax.toFixed(2);
                        console.log(`  ⚠️ Cantidad ajustada de ${valorActual} a ${nuevoMax}`);
                    }
                    
                    // Actualizar texto de ayuda
                    const small = input.parentElement.querySelector('small.text-info');
                    if (small) {
                        small.innerHTML = `<i class="fa fa-arrow-up"></i> Máx: ${nuevoMax.toFixed(2)}`;
                    }
                });
            });
        }
    }
    
    function configurarExclusividadCheckboxes() {
        document.addEventListener('change', function(event) {
            if (event.target.classList.contains('detraccion-checkbox')) {
                if (event.target.checked) {
                    document.querySelectorAll('.retencion-checkbox').forEach(cb => cb.checked = false);
                    document.querySelectorAll('.percepcion-checkbox').forEach(cb => cb.checked = false);
                }
                document.querySelectorAll('.detraccion-checkbox').forEach(cb => {
                    if (cb !== event.target) cb.checked = false;
                });
                actualizarTotalGeneral();
            }
            
            if (event.target.classList.contains('retencion-checkbox')) {
                if (event.target.checked) {
                    document.querySelectorAll('.detraccion-checkbox').forEach(cb => cb.checked = false);
                    document.querySelectorAll('.percepcion-checkbox').forEach(cb => cb.checked = false);
                }
                document.querySelectorAll('.retencion-checkbox').forEach(cb => {
                    if (cb !== event.target) cb.checked = false;
                });
                actualizarTotalGeneral();
            }
            
            if (event.target.classList.contains('percepcion-checkbox')) {
                if (event.target.checked) {
                    document.querySelectorAll('.detraccion-checkbox').forEach(cb => cb.checked = false);
                    document.querySelectorAll('.retencion-checkbox').forEach(cb => cb.checked = false);
                }
                document.querySelectorAll('.percepcion-checkbox').forEach(cb => {
                    if (cb !== event.target) cb.checked = false;
                });
                actualizarTotalGeneral();
            }
        });
    }
    
    // ============================================
    // CONFIGURACIÓN MODAL PROVEEDOR
    // ============================================
    
    function configurarModalProveedor() {
        const tablaCuentasModal = document.getElementById("tabla-cuentas-modal");
        //const btnAgregarModal = document.getElementById("agregarCuentaModal");
        
        /*
        if (btnAgregarModal) {
            btnAgregarModal.addEventListener("click", function() {
                const nuevaFila = document.createElement("tr");
                nuevaFila.innerHTML = `
                    <td><input type="text" name="banco[]" class="form-control form-control-sm"></td>
                    <td>
                        <select name="id_moneda[]" class="form-control form-control-sm">
                            <option value="">-- Moneda --</option>
                            <?php /*foreach ($moneda as $m) { ?>
                                <option value="<?php echo $m['id_moneda']; ?>"><?php echo $m['nom_moneda']; ?></option>
                            <?php } */?>
                        </select>
                    </td>
                    <td><input type="text" name="cta_corriente[]" class="form-control form-control-sm"></td>
                    <td><input type="text" name="cta_interbancaria[]" class="form-control form-control-sm"></td>
                    <td><button type="button" class="btn btn-danger btn-sm eliminar-fila-modal">X</button></td>
                `;
                tablaCuentasModal.appendChild(nuevaFila);
            });
        } */
        
        if (tablaCuentasModal) {
            tablaCuentasModal.addEventListener("click", function(e) {
                if (e.target.classList.contains("eliminar-fila-modal")) {
                    const filas = tablaCuentasModal.querySelectorAll("tr");
                    if (filas.length > 1) {
                        e.target.closest("tr").remove();
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Advertencia',
                            text: 'Debe mantener al menos una fila'
                        });
                    }
                }
            });
        }
        
        const btnAgregarProveedor = document.getElementById('btn-agregar-proveedor');
        if (btnAgregarProveedor) {
            btnAgregarProveedor.addEventListener('click', () => $('#modalNuevoProveedor').modal('show'));
        }
        
        const btnGuardarProveedorModal = document.getElementById('btn-guardar-proveedor-modal');
        if (btnGuardarProveedorModal) {
            btnGuardarProveedorModal.addEventListener('click', guardarProveedorModal);
        }
        
        $('#modalNuevoProveedor').on('hidden.bs.modal', limpiarFormularioProveedor);
    }
    
    function guardarProveedorModal() {
        const form = document.getElementById('form-nuevo-proveedor-modal');
        
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        const rucInput = form.querySelector('input[name="ruc_proveedor"]');
        const ruc = rucInput.value.trim();
        
        if (ruc.length !== 11 || !/^\d+$/.test(ruc)) {
            Swal.fire({
                icon: 'error',
                title: 'RUC inválido',
                text: 'El RUC debe contener exactamente 11 dígitos numéricos'
            });
            rucInput.focus();
            return;
        }
        
        const formData = new FormData(form);
        formData.append('registrar_ajax', '1');
        
        const btnGuardar = document.getElementById('btn-guardar-proveedor-modal');
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Guardando...';
        
        fetch('proveedor_nuevo_directo.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const selectProveedor = document.getElementById('proveedor_orden');
                const newOption = new Option(data.nombre_proveedor, data.id_proveedor, true, true);
                selectProveedor.add(newOption);
                
                $('#modalNuevoProveedor').modal('hide');
                form.reset();
                
                Swal.fire({
                    icon: 'success',
                    title: '¡Proveedor agregado!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor.'
            });
        })
        .finally(() => {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = '<i class="fa fa-save"></i> Registrar';
        });
    }
    
    function limpiarFormularioProveedor() {
        const form = document.getElementById('form-nuevo-proveedor-modal');
        if (form) {
            form.reset();
            const tablaCuentas = document.getElementById('tabla-cuentas-modal');
            if (tablaCuentas) {
                const filas = tablaCuentas.querySelectorAll('tr');
                for (let i = filas.length - 1; i > 0; i--) {
                    filas[i].remove();
                }
            }
        }
    }
    
function agregarItemAOrden(item) {
    console.log('🎯 agregarItemAOrden INICIADO');
    console.log('📋 Item recibido:', item);
    
    try {
        const contenedorItemsOrden = document.getElementById('contenedor-items-orden');
        
        if (!contenedorItemsOrden) {
            console.error('❌ CRÍTICO: No existe el elemento con id="contenedor-items-orden"');
            Swal.fire({
                icon: 'error',
                title: 'Error del Sistema',
                text: 'No se encontró el contenedor de items. Recarga la página.',
            });
            return;
        }
        
        const selectMoneda = document.getElementById('moneda_orden');
        let simboloMoneda = 'S/.';
        
        if (selectMoneda && selectMoneda.value) {
            const idMonedaSeleccionada = selectMoneda.value;
            simboloMoneda = idMonedaSeleccionada == '1' ? 'S/.' : (idMonedaSeleccionada == '2' ? 'US$' : 'S/.');
        }
        
        const itemId = 'nuevo-' + Date.now();
        
        // 🔹 DETECTAR SI ES BASE ARCE desde el botón original
        const esBaseArce = 
            item.es_base_arce === '1' || 
            item.es_base_arce === 1 ||
            (item.botonOriginal && item.botonOriginal.dataset.esBaseArce === '1');
        
        console.log('🏢 ¿Es BASE ARCE?', esBaseArce);
        
        // 🔹 USAR cantidadPendiente DIRECTAMENTE
        let cantidadVerificada = parseFloat(item.cantidadVerificada) || 0;
        let cantidadOrdenada = parseFloat(item.cantidadOrdenada) || 0;
        let cantidadPendiente = 0;
        
        if (item.cantidadPendiente !== undefined && item.cantidadPendiente !== null) {
            cantidadPendiente = parseFloat(item.cantidadPendiente) || 0;
        } else {
            cantidadPendiente = cantidadVerificada - cantidadOrdenada;
        }
        
        console.log('📊 Cantidades FINALES:', {
            esBaseArce: esBaseArce,
            verificada: cantidadVerificada,
            ordenada: cantidadOrdenada,
            pendiente: cantidadPendiente
        });
        
        if (cantidadPendiente <= 0) {
            console.warn('⚠️ No hay cantidad pendiente');
            Swal.fire({
                icon: 'info',
                title: 'Sin cantidad disponible',
                text: 'Este item ya fue completamente ordenado.',
            });
            return;
        }
        
        const itemElement = document.createElement('div');
        itemElement.id = `item-orden-${itemId}`;
        itemElement.classList.add('alert', 'alert-light', 'p-2', 'mb-2');
        
        const badgeTipo = esOrdenServicio 
            ? '<span class="badge badge-primary badge-sm ml-1">SERVICIO</span>'
            : (modoEditar ? '<span class="badge badge-info badge-sm ml-1">NUEVO</span>' : '');
        
        //  Badge especial para BASE ARCE
        const badgeBaseArce = esBaseArce ? '<span class="badge badge-info badge-sm ml-1">BASE ARCE</span>' : '';
        
        const etiquetaCantidad = (esOrdenServicio || esBaseArce) ? 'Cantidad Original' : 'Cantidad Verificada';
        
        // 🔹 CRÍTICO: value="${cantidadPendiente.toFixed(2)}"
        itemElement.innerHTML = `
            <input type="hidden" name="items_orden[${itemId}][id_detalle]" value="${item.idDetalle}">
            <input type="hidden" name="items_orden[${itemId}][id_pedido_detalle]" value="${item.idDetalle}">
            <input type="hidden" name="items_orden[${itemId}][id_producto]" value="${item.idProducto}">
            <input type="hidden" name="items_orden[${itemId}][es_nuevo]" value="1">
            <input type="hidden" name="items_orden[${itemId}][es_base_arce]" value="${esBaseArce ? '1' : '0'}">
            
            <div class="row align-items-center mb-2">
                <div class="col-md-11">
                    <div style="font-size: 12px;">
                        <strong>Descripción:</strong> ${item.descripcion}
                        ${badgeTipo}${badgeBaseArce}
                    </div>
                <small class="text-muted" style="font-size: 11px;">
                    ${etiquetaCantidad}: ${cantidadVerificada.toFixed(2)} | 
                    Ya ordenado: ${cantidadOrdenada.toFixed(2)} | 
                    <strong class="text-warning">Pendiente: ${cantidadPendiente.toFixed(2)}</strong>
                </small>
                </div>
                <div class="col-md-1 text-right">
                    <button type="button" class="btn btn-danger btn-sm btn-remover-item" data-id-detalle="${itemId}">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
            
            <!-- REQUISITOS SST/MA/CA -->
            <div class="row mt-2 mb-2">
                <div class="col-md-12">
                    <label style="font-size: 11px; font-weight: bold; margin-bottom: 4px; display: block; color: #495057;">
                        <i></i> Requisitos SST/MA/CA:
                    </label>
                    <input type="text" 
                        class="form-control form-control-sm" 
                        name="req_compra[${item.idDetalle}]"
                        value="${item.requisitos || ''}"
                        style="font-size: 11px;"
                        placeholder="Requisitos de seguridad, medio ambiente y calidad...">
                </div>
            </div>
            <div style="border-top: 1px dashed #dee2e6; margin: 10px 0;"></div>

            <div class="row">
                <div class="col-md-2">
                    <label style="font-size: 11px; font-weight: bold; margin-bottom: 4px; display: block;">Cantidad:</label>
                    <input type="number" class="form-control form-control-sm cantidad-item" 
                        name="items_orden[${itemId}][cantidad]" 
                        data-id-detalle="${itemId}"
                        data-id-producto="${item.idProducto}"
                        data-cantidad-verificada="${cantidadVerificada}"
                        data-cantidad-ordenada="${cantidadOrdenada}"
                        data-es-base-arce="${esBaseArce ? '1' : '0'}"
                        value="${cantidadPendiente.toFixed(2)}" 
                        min="0.01" 
                        max="${cantidadPendiente.toFixed(2)}" 
                        step="0.01"
                        style="font-size: 12px;" required>
                    <small class="text-info" style="font-size: 10px;">Máx: ${cantidadPendiente.toFixed(2)}</small>
                </div>
                    
                    <div class="col-md-2">
                        <label style="font-size: 11px; font-weight: bold; margin-bottom: 4px; display: block;">Precio Unit.:</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="font-size: 11px; background-color: #f8f9fa; border: 1px solid #ced4da;">${simboloMoneda}</span>
                            </div>
                            <input type="number" class="form-control form-control-sm precio-item" 
                                name="items_orden[${itemId}][precio_unitario]" 
                                data-id-detalle="${itemId}"
                                step="0.01" min="0" placeholder="0.00" style="font-size: 11px;" required>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <label style="font-size: 11px; font-weight: bold; margin-bottom: 4px; display: block;">IGV (%):</label>
                        <input type="number" class="form-control form-control-sm igv-item" 
                            name="items_orden[${itemId}][igv]" 
                            data-id-detalle="${itemId}"
                            value="18" min="0" max="100" step="0.01" style="font-size: 12px;" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label style="font-size: 11px; font-weight: bold; margin-bottom: 4px; display: block;">Homologación:</label>
                        <input type="file" class="form-control-file" name="homologacion[${item.idDetalle}]"
                            accept=".pdf,.jpg,.jpeg,.png" style="font-size: 11px; padding-top: 4px;">
                        <small class="text-muted" style="font-size: 10px;">PDF, JPG, PNG</small>
                    </div>
                    
                    <div class="col-md-3 text-right">
                        <label style="font-size: 11px; font-weight: bold; margin-bottom: 4px; display: block; visibility: hidden;">-</label>
                        <div class="calculo-item" id="calculo-${itemId}" style="font-size: 11px; line-height: 1.4;">
                            <div class="subtotal-text">Subtotal: ${simboloMoneda} 0.00</div>
                            <div class="igv-text">IGV: ${simboloMoneda} 0.00</div>
                            <div class="total-text" style="font-weight: bold; color: #28a745;">Total: ${simboloMoneda} 0.00</div>
                        </div>
                    </div>
                </div>
            `;
            
            const resumenElement = document.getElementById('resumen-total-orden');
            if (resumenElement) {
                contenedorItemsOrden.insertBefore(itemElement, resumenElement);
            } else {
                contenedorItemsOrden.appendChild(itemElement);
            }

            console.log('✅ Item agregado al DOM correctamente con cantidad pendiente:', cantidadPendiente);
            
            itemsAgregadosOrden.add(itemId);
            
            if (item.botonOriginal) {
                item.botonOriginal.disabled = true;
                item.botonOriginal.innerHTML = '<i class="fa fa-check-circle"></i> Agregado';
                item.botonOriginal.classList.remove('btn-primary');
                item.botonOriginal.classList.add('btn-success');
            }
            
            const cantidadInput = itemElement.querySelector('.cantidad-item');
            const precioInput = itemElement.querySelector('.precio-item');
            const igvInput = itemElement.querySelector('.igv-item');
            
            function calcularTotalesItem() {
                const cantidad = parseFloat(cantidadInput.value) || 0;
                const precio = parseFloat(precioInput.value) || 0;
                const igvPorcentaje = parseFloat(igvInput.value) || 0;
                
                const subtotal = cantidad * precio;
                const montoIgv = subtotal * (igvPorcentaje / 100);
                const total = subtotal + montoIgv;
                
                const simboloMoneda = obtenerSimboloMoneda();
                const calculoDiv = document.getElementById(`calculo-${itemId}`);
                if (calculoDiv) {
                    calculoDiv.querySelector('.subtotal-text').textContent = `Subtotal: ${simboloMoneda} ${subtotal.toFixed(2)}`;
                    calculoDiv.querySelector('.igv-text').textContent = `IGV: ${simboloMoneda} ${montoIgv.toFixed(2)}`;
                    calculoDiv.querySelector('.total-text').textContent = `Total: ${simboloMoneda} ${total.toFixed(2)}`;
                }
                actualizarTotalGeneral();
            }
            
            cantidadInput.addEventListener('input', calcularTotalesItem);
            precioInput.addEventListener('input', calcularTotalesItem);
            igvInput.addEventListener('input', calcularTotalesItem);
            
            console.log('✅ agregarItemAOrden COMPLETADO EXITOSAMENTE');
            
        } catch (error) {
            console.error('❌ ERROR EN agregarItemAOrden:', error);
            console.error('Stack trace:', error.stack);
            Swal.fire({
                icon: 'error',
                title: 'Error al Agregar Item',
                text: 'Ocurrió un error: ' + error.message,
            });
        }
    }
    
    function removerItemDeOrden(idDetalle, botonOriginal) {
        console.log('🗑️ Removiendo item:', idDetalle);
        const itemElement = document.getElementById(`item-orden-${idDetalle}`);
        
        if (itemElement) {
            itemElement.remove();
            itemsAgregadosOrden.delete(idDetalle);
            
            if (botonOriginal) {
                botonOriginal.disabled = false;
                botonOriginal.innerHTML = '<i class="fa fa-plus"></i> Agregar';
                botonOriginal.classList.remove('btn-success');
                botonOriginal.classList.add('btn-primary');
            }
            
            actualizarTotalGeneral();
        }
    }
    
    // ============================================
    // FUNCIONES DE CÁLCULO
    // ============================================
    
    function actualizarTotalGeneral() {
        const items = document.querySelectorAll('[id^="item-orden-"]');
        let subtotalGeneral = 0;
        let totalIgv = 0;
        
        items.forEach(item => {
            const cantidadInput = item.querySelector('.cantidad-item');
            const precioInput = item.querySelector('.precio-item');
            const igvInput = item.querySelector('.igv-item');
            
            if (cantidadInput && precioInput && igvInput) {
                const cantidad = parseFloat(cantidadInput.value) || 0;
                const precio = parseFloat(precioInput.value) || 0;
                const igvPorcentaje = parseFloat(igvInput.value) || 0;
                
                const subtotal = cantidad * precio;
                const montoIgv = subtotal * (igvPorcentaje / 100);
                
                subtotalGeneral += subtotal;
                totalIgv += montoIgv;
            }
        });
        
        const totalConIgv = subtotalGeneral + totalIgv;
        
        let tipoDescuentoCargo = null;
        let porcentaje = 0;
        let nombreConcepto = '';
        let montoAfectacion = 0;
        
        const checkboxDetraccion = document.querySelector('.detraccion-checkbox:checked');
        const checkboxRetencion = document.querySelector('.retencion-checkbox:checked');
        const checkboxPercepcion = document.querySelector('.percepcion-checkbox:checked');
        
        if (checkboxDetraccion) {
            tipoDescuentoCargo = 'DETRACCION';
            porcentaje = parseFloat(checkboxDetraccion.getAttribute('data-porcentaje')) || 0;
            nombreConcepto = checkboxDetraccion.getAttribute('data-nombre') || '';
            montoAfectacion = (totalConIgv * porcentaje) / 100;
        } else if (checkboxRetencion) {
            tipoDescuentoCargo = 'RETENCION';
            porcentaje = parseFloat(checkboxRetencion.getAttribute('data-porcentaje')) || 0;
            nombreConcepto = checkboxRetencion.getAttribute('data-nombre') || '';
            montoAfectacion = (totalConIgv * porcentaje) / 100;
        } else if (checkboxPercepcion) {
            tipoDescuentoCargo = 'PERCEPCION';
            porcentaje = parseFloat(checkboxPercepcion.getAttribute('data-porcentaje')) || 0;
            nombreConcepto = checkboxPercepcion.getAttribute('data-nombre') || '';
            montoAfectacion = (totalConIgv * porcentaje) / 100;
        }
        
        let totalFinal = 0;
        
        if (tipoDescuentoCargo === 'DETRACCION') {
            totalFinal = totalConIgv - montoAfectacion;
        } else if (tipoDescuentoCargo === 'RETENCION') {
            totalFinal = totalConIgv - montoAfectacion;
        } else if (tipoDescuentoCargo === 'PERCEPCION') {
            totalFinal = totalConIgv + montoAfectacion;
        } else {
            totalFinal = totalConIgv;
        }
        
        let resumenDiv = document.getElementById('resumen-total-orden');
        if (!resumenDiv && items.length > 0) {
            resumenDiv = document.createElement('div');
            resumenDiv.id = 'resumen-total-orden';
            resumenDiv.className = '';
            document.getElementById('contenedor-items-orden').appendChild(resumenDiv);
        }
        
        if (resumenDiv && items.length > 0) {
            const simboloMoneda = obtenerSimboloMoneda();
            let html = `
                <div style="font-size: 15px; padding: 10px 15px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px;">
                    <div class="mb-2">
                        <i class="fa fa-calculator text-secondary"></i>
                        <strong class="text-secondary"> Subtotal:</strong>
                        <span class="text-dark">${simboloMoneda} ${subtotalGeneral.toFixed(2)}</span>
                    </div>
                    
                    <div class="mb-2">
                        <i class="fa fa-percent text-secondary"></i>
                        <strong class="text-secondary"> IGV Total:</strong>
                        <span class="text-dark">${simboloMoneda} ${totalIgv.toFixed(2)}</span>
                    </div>
                    
                    <div class="mb-2" style="font-weight: bold; font-size: 16px; padding: 5px; background-color: #e3f2fd; border-radius: 4px;">
                        <i class="fa fa-calculator text-primary"></i>
                        <strong class="text-primary"> Total con IGV:</strong>
                        <span class="text-primary">${simboloMoneda} ${totalConIgv.toFixed(2)}</span>
                    </div>`;
            
            if (tipoDescuentoCargo === 'DETRACCION') {
                html += `
                    <div class="mb-2">
                        <i class="fa fa-minus-circle text-warning"></i>
                        <strong class="text-warning"> Detracción ${nombreConcepto} (${porcentaje}%):</strong>
                        <span class="text-warning">-${simboloMoneda} ${montoAfectacion.toFixed(2)}</span>
                    </div>`;
            }
            
            if (tipoDescuentoCargo === 'RETENCION') {
                html += `
                    <div class="mb-2">
                        <i class="fa fa-minus-circle text-info"></i>
                        <strong class="text-info"> Retención ${nombreConcepto} (${porcentaje}%):</strong>
                        <span class="text-info">-${simboloMoneda} ${montoAfectacion.toFixed(2)}</span>
                    </div>`;
            }
            
            if (tipoDescuentoCargo === 'PERCEPCION') {
                html += `
                    <div class="mb-2">
                        <i class="fa fa-plus-circle text-success"></i>
                        <strong class="text-success"> Percepción ${nombreConcepto} (${porcentaje}%):</strong>
                        <span class="text-success">+${simboloMoneda} ${montoAfectacion.toFixed(2)}</span>
                    </div>`;
            }
            
            html += `
                    <div style="font-size: 18px; font-weight: bold; padding: 10px; background-color: #28a745; color: white; border-radius: 6px; text-align: center; margin-top: 10px;">
                        <i class="fa fa-money"></i> 
                        TOTAL A PAGAR: ${simboloMoneda} ${totalFinal.toFixed(2)}
                    </div>
                </div>`;
            
            resumenDiv.innerHTML = html;
        } else if (resumenDiv && items.length === 0) {
            resumenDiv.remove();
        }
    }
    
    function obtenerSimboloMoneda() {
        const selectMoneda = document.getElementById('moneda_orden');
        if (!selectMoneda || !selectMoneda.value) return 'S/.';
        const idMonedaSeleccionada = selectMoneda.value;
        return idMonedaSeleccionada == '1' ? 'S/.' : (idMonedaSeleccionada == '2' ? 'US$' : 'S/.');
    }
    
    function actualizarEtiquetasMoneda(idMoneda) {
        const simboloMoneda = idMoneda == '1' ? 'S/.' : (idMoneda == '2' ? 'US$' : 'S/.');
        
        document.querySelectorAll('.input-group-text').forEach(etiqueta => {
            if (etiqueta.textContent === 'S/.' || etiqueta.textContent === 'US$') {
                etiqueta.textContent = simboloMoneda;
            }
        });
        
        actualizarTotalGeneral();
    }
    
    // ============================================
    // FUNCIÓN ANULAR COMPRA
    // ============================================
    
    /*function AnularCompra(id_compra, id_pedido) {
        Swal.fire({
            title: '¿Qué deseas anular?',
            text: "Selecciona una opción:",
            icon: 'warning',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonColor: '#d33',
            denyButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Solo O/C',
            denyButtonText: 'O/C y Pedido',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'compras_anular.php',
                    type: 'POST',
                    data: { id_compra: id_compra },
                    dataType: 'json',
                    success: function(response) {
                        if (response.tipo_mensaje === 'success') {
                            Swal.fire('¡Anulado!', response.mensaje, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.mensaje, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                    }
                });
            } else if (result.isDenied) {
                $.ajax({
                    url: 'compras_pedido_anular.php',
                    type: 'POST',
                    data: { id_compra: id_compra, id_pedido: id_pedido },
                    dataType: 'json',
                    success: function(response) {
                        if (response.tipo_mensaje === 'success') {
                            Swal.fire('¡Anulado!', response.mensaje, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.mensaje, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                    }
                });
            }
        });
    }*/

    // ============================================
    // FUNCIÓN ANULAR COMPRA
    // ============================================
    function AnularCompra(id_compra, id_pedido) {
        // Validar si el pedido tiene otras OC o salidas
        $.ajax({
            url: 'compras_validar_anulacion.php',
            type: 'POST',
            data: { 
                id_compra: id_compra, 
                id_pedido: id_pedido 
            },
            dataType: 'json',
            success: function(validacion) {
                
                if (validacion.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: validacion.mensaje,
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                // CASO 1: NO se puede anular pedido (tiene otras OCs aprobadas/cerradas o salidas)
                if (!validacion.puede_anular_pedido) {
                    
                    let mensaje_restriccion = "No se puede anular el pedido completo porque contiene:\n\n";
                    
                    // Construir mensaje de restricciones
                    if (validacion.mensaje_restriccion && validacion.mensaje_restriccion.length > 0) {
                        validacion.mensaje_restriccion.forEach(function(restriccion) {
                            mensaje_restriccion += `• ${restriccion}\n`;
                        });
                    }
                    
                    mensaje_restriccion += "\n¿Deseas anular solo esta Orden de Compra?";

                    Swal.fire({
                        title: '¿Seguro que deseas anular esta O/C?',
                        html: mensaje_restriccion.replace(/\n/g, '<br>'),
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, anular O/C',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            anularSoloOC(id_compra);
                        }
                    });

                } 
                // CASO 2: SÍ se puede anular pedido (solo tiene esta OC y sin salidas)
                else {
                    
                    Swal.fire({
                        title: '¿Qué deseas anular?',
                        html: '<p style="margin-bottom: 15px;">Selecciona una opción:</p>' +
                            '<ul style="text-align: left; padding-left: 20px;">' +
                            '<li><strong>Solo O/C:</strong> Anula únicamente esta orden</li>' +
                            '<li><strong>O/C y Pedido:</strong> Anula la orden y el pedido completo</li>' +
                            '</ul>',
                        icon: 'warning',
                        showCancelButton: true,
                        showDenyButton: true,
                        confirmButtonColor: '#d33',
                        denyButtonColor: '#3085d6',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Solo O/C',
                        denyButtonText: 'O/C y Pedido',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            anularSoloOC(id_compra);
                        } else if (result.isDenied) {
                            anularOCyPedido(id_compra, id_pedido);
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en validación:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Conexión',
                    text: 'No se pudo validar la anulación. Intente nuevamente.',
                    confirmButtonColor: '#3085d6'
                });
            }
        });
    }

    // ============================================
    // FUNCIÓN AUXILIAR: ANULAR SOLO OC
    // ============================================
    function anularSoloOC(id_compra) {
        $.ajax({
            url: 'compras_anular.php',
            type: 'POST',
            data: { id_compra: id_compra },
            dataType: 'json',
            beforeSend: function() {
                Swal.fire({
                    title: 'Procesando...',
                    text: 'Anulando orden de compra',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                if (response.tipo_mensaje === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Anulado!',
                        text: response.mensaje,
                        confirmButtonColor: '#3085d6'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.mensaje,
                        confirmButtonColor: '#3085d6'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al anular OC:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Conexión',
                    text: 'No se pudo conectar con el servidor.',
                    confirmButtonColor: '#3085d6'
                });
            }
        });
    }

    // ============================================
    // FUNCIÓN AUXILIAR: ANULAR OC Y PEDIDO
    // ============================================
    function anularOCyPedido(id_compra, id_pedido) {
        $.ajax({
            url: 'compras_pedido_anular.php',
            type: 'POST',
            data: { 
                id_compra: id_compra, 
                id_pedido: id_pedido 
            },
            dataType: 'json',
            beforeSend: function() {
                Swal.fire({
                    title: 'Procesando...',
                    text: 'Anulando orden de compra y pedido',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                if (response.tipo_mensaje === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Anulado!',
                        text: response.mensaje,
                        confirmButtonColor: '#3085d6'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.mensaje,
                        confirmButtonColor: '#3085d6'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al anular OC y Pedido:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Conexión',
                    text: 'No se pudo conectar con el servidor.',
                    confirmButtonColor: '#3085d6'
                });
            }
        });
    }
    
    // ============================================
    // MODAL DETALLE COMPRA
    // ============================================
    
    function mostrarDetalleCompra(idCompra) {
        $('#modalDetalleCompra').modal('show');
        
        document.getElementById('loading-spinner').style.display = 'block';
        document.getElementById('contenido-detalle-compra').style.display = 'none';
        document.getElementById('error-detalle-compra').style.display = 'none';
        
        const formData = new FormData();
        formData.append('accion', 'obtener_detalle');
        formData.append('id_compra', idCompra);
        
        fetch('compra_detalles.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loading-spinner').style.display = 'none';
            if (data.success) {
                mostrarContenidoDetalle(data.compra, data.detalles);
            } else {
                mostrarErrorDetalle(data.message || 'Error desconocido');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('loading-spinner').style.display = 'none';
            mostrarErrorDetalle('Error de conexión');
        });
    }
    
    function mostrarContenidoDetalle(compra, detalles) {
        const titulo = document.getElementById('modalDetalleCompraLabel');
        titulo.innerHTML = `<i class="fa fa-file-text-o text-primary"></i> Orden de Compra - C00${compra.id_compra}`;
        
        const contenido = document.getElementById('contenido-detalle-compra');
        const fechaFormateada = new Date(compra.fec_compra).toLocaleDateString('es-PE');
        const estadoCompra = parseInt(compra.est_compra);
        
        let estadoTexto = 'Desconocido';
        let estadoClase = 'secondary';
        
        switch(estadoCompra) {
            case 0: estadoTexto = 'Anulada'; estadoClase = 'danger'; break;
            case 1: estadoTexto = 'Pendiente'; estadoClase = 'warning'; break;
            case 2: estadoTexto = 'Aprobada'; estadoClase = 'success'; break;
            case 3: estadoTexto = 'Cerrada'; estadoClase = 'info'; break;
            case 4: estadoTexto = 'Pagada'; estadoClase = 'primary'; break;
        }
        
        let html = `
        <div class="card mb-3">
            <div class="card-header" style="background-color: #e3f2fd; padding: 10px 15px;">
                <h6 class="mb-0"><i class="fa fa-info-circle text-primary"></i> Información General</h6>
            </div>
            <div class="card-body" style="padding: 15px;">
                <div class="row">
                    <div class="col-md-6">
                        <p style="margin: 5px 0; font-size: 13px;"><strong>N° Orden:</strong> C00${compra.id_compra}</p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>Proveedor:</strong> ${compra.nom_proveedor || 'No especificado'}</p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>RUC:</strong> ${compra.ruc_proveedor || 'No especificado'}</p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>Moneda:</strong> ${compra.nom_moneda || 'No especificada'}</p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>Condición de Pago:</strong> ${(() => {
                            if (!compra.plaz_compra) return 'No especificado';
                            const plazo = parseInt(compra.plaz_compra);
                            if (isNaN(plazo)) return compra.plaz_compra;
                            return plazo === 0 ? 'Contado' : `Crédito ${plazo} días`;
                        })()}</p>
                    </div>
                    <div class="col-md-6">
                        <p style="margin: 5px 0; font-size: 13px;"><strong>Fecha:</strong> ${fechaFormateada}</p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>Estado:</strong> <span class="badge badge-${estadoClase}">${estadoTexto}</span></p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>Creado por:</strong> ${compra.nom_personal || 'No especificado'}</p>
                        ${compra.nom_centro_costo ? `
                        <p style="margin: 5px 0; font-size: 13px;">
                            <strong>Centro de Costo:</strong> 
                            <span class="badge badge-primary" style="font-size: 11px;">
                                ${compra.nom_centro_costo}
                            </span>
                        </p>
                        ` : ''}
                    </div>
                </div>`;
        
        let tieneAfectacion = false;
        
        if (compra.nombre_detraccion && compra.porcentaje_detraccion) {
            tieneAfectacion = true;
            html += `
                <div class="alert alert-warning" style="margin-top: 15px; padding: 10px;">
                    <i class="fa fa-exclamation-triangle"></i> 
                    <strong>Detracción Aplicada:</strong> ${compra.nombre_detraccion} 
                    <span class="badge badge-warning">${compra.porcentaje_detraccion}%</span>
                </div>`;
        }
        
        if (compra.nombre_retencion && compra.porcentaje_retencion) {
            tieneAfectacion = true;
            html += `
                <div class="alert alert-info" style="margin-top: 15px; padding: 10px;">
                    <i class="fa fa-info-circle"></i> 
                    <strong>Retención Aplicada:</strong> ${compra.nombre_retencion} 
                    <span class="badge badge-info">${compra.porcentaje_retencion}%</span>
                </div>`;
        }
        
        if (compra.nombre_percepcion && compra.porcentaje_percepcion) {
            tieneAfectacion = true;
            html += `
                <div class="alert alert-success" style="margin-top: 15px; padding: 10px;">
                    <i class="fa fa-plus-circle"></i> 
                    <strong>Percepción Aplicada:</strong> ${compra.nombre_percepcion} 
                    <span class="badge badge-success">${compra.porcentaje_percepcion}%</span>
                </div>`;
        }
        
        if (!tieneAfectacion) {
            html += `
                <div class="alert alert-secondary" style="margin-top: 15px; padding: 10px;">
                    <i class="fa fa-info-circle"></i> 
                    <strong>Sin afectaciones:</strong> Esta orden no tiene detracción, retención ni percepción aplicada.
                </div>`;
        }
        
        if (compra.denv_compra || compra.obs_compra || compra.port_compra) {
            html += `<div class="row mt-3"><div class="col-md-12"><div class="border-top pt-2">`;
            if (compra.denv_compra) html += `<p style="margin: 5px 0; font-size: 13px;"><strong>Dirección de Envío:</strong> ${compra.denv_compra}</p>`;
            if (compra.obs_compra) html += `<p style="margin: 5px 0; font-size: 13px;"><strong>Observaciones:</strong> ${compra.obs_compra}</p>`;
            //if (compra.port_compra) html += `<p style="margin: 5px 0; font-size: 13px;"><strong>Tipo de Porte:</strong> ${compra.port_compra}</p>`;
            html += `</div></div></div>`;
        }
        
        html += `</div></div>`;
        
        html += `
            <div class="card">
                <div class="card-header" style="background-color: #e8f5e8; padding: 10px 15px;">
                    <h6 class="mb-0"><i class="fa fa-list-alt text-success"></i> Productos de la Orden</h6>
                </div>
                <div class="card-body" style="padding: 15px;">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm" style="font-size: 12px;">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 10%;">Código</th>
                                    <th style="width: 25%;">Descripción</th>
                                    <th style="width: 8%;">Cantidad</th>
                                    <th style="width: 10%;">Precio Unit.</th>
                                    <th style="width: 8%;">IGV (%)</th>
                                    <th style="width: 10%;">Subtotal</th>
                                    <th style="width: 24%;">Centro(s) de Costo</th>
                                </tr>
                            </thead>
                            <tbody>`;
        
        let subtotalGeneral = 0;
        let totalIgv = 0;
        const simboloMoneda = compra.sim_moneda || (compra.id_moneda == 1 ? 'S/.' : 'US$');
        
        detalles.forEach((detalle, index) => {
            const cantidad = parseFloat(detalle.cant_compra_detalle);
            const precioUnit = parseFloat(detalle.prec_compra_detalle);
            const igvPorcentaje = parseFloat(detalle.igv_compra_detalle || 18);
            
            const subtotal = cantidad * precioUnit;
            const montoIgv = subtotal * (igvPorcentaje / 100);
            
            subtotalGeneral += subtotal;
            totalIgv += montoIgv;
            
            // 🔹 CONSTRUIR HTML DE CENTROS DE COSTO CON MODAL
            let centrosCostoHtml = '<small class="text-muted">Sin asignar</small>';
            
            if (detalle.centros_costo && Array.isArray(detalle.centros_costo) && detalle.centros_costo.length > 0) {
                const totalCentros = detalle.centros_costo.length;
                const modalId = `modalCentrosCostoDetalle${index}`;
                
                if (totalCentros === 1) {
                    // Si solo hay 1 centro, mostrarlo directamente
                    centrosCostoHtml = `
                        <span class="badge badge-info badge_size" style="font-size: 11px;">
                            ${detalle.centros_costo[0].nom_centro_costo}
                        </span>`;
                } else {
                    // Si hay múltiples centros, usar modal
                    const listaCentros = detalle.centros_costo.map((cc, idx) => 
                        `<div style="padding: 8px; margin-bottom: 6px; background-color: #f8f9fa; border-left: 3px solid #17a2b8; border-radius: 4px;">
                            <strong style="color: #17a2b8;">${idx + 1}.</strong> ${cc.nom_centro_costo}
                        </div>`
                    ).join('');
                    
                    centrosCostoHtml = `
                        <button class="btn btn-sm btn-info btn-ver-centros-costo-detalle" 
                                type="button" 
                                data-modal-id="${modalId}"
                                style="font-size: 11px; padding: 3px 10px;">
                            <i class="fa fa-eye"></i> Ver ${totalCentros} centros
                        </button>
                        
                        <!-- Modal para centros de costo -->
                        <div class="modal fade" id="${modalId}" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header" style="background-color: #17a2b8; color: white; padding: 12px 20px;">
                                        <h6 class="modal-title mb-0">
                                            <i class="fa fa-building"></i> 
                                            Centros de Costo Asignados
                                        </h6>
                                        <button type="button" class="close close-centros-modal-detalle" data-modal-id="${modalId}" aria-label="Close" style="color: white; opacity: 0.8;">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body" style="padding: 20px;">
                                        <div style="margin-bottom: 15px; padding: 10px; background-color: #e7f3ff; border-radius: 4px; border-left: 4px solid #17a2b8;">
                                            <strong>Producto:</strong> ${detalle.nom_producto}
                                        </div>
                                        <div style="max-height: 400px; overflow-y: auto;">
                                            ${listaCentros}
                                        </div>
                                        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6; text-align: center;">
                                            <span class="badge badge-info" style="font-size: 12px; padding: 6px 12px;">
                                                Total: ${totalCentros} centro(s) de costo
                                            </span>
                                        </div>
                                    </div>
                                    <div class="modal-footer" style="padding: 10px 20px;">
                                        <button type="button" class="btn btn-secondary btn-sm close-centros-modal-detalle" data-modal-id="${modalId}">
                                            <i class="fa fa-times"></i> Cerrar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                }
            }
            
            html += `<tr>
                        <td style="font-weight: bold;">${index + 1}</td>
                        <td>${detalle.cod_material || 'N/A'}</td>
                        <td>${detalle.nom_producto}</td>
                        <td class="text-center">${cantidad.toFixed(2)}</td>
                        <td class="text-right">${simboloMoneda} ${precioUnit.toFixed(2)}</td>
                        <td class="text-center">${igvPorcentaje}%</td>
                        <td class="text-right" style="font-weight: bold;">${simboloMoneda} ${subtotal.toFixed(2)}</td>
                        <td>${centrosCostoHtml}</td>
                    </tr>`;
        });
        
        html += `</tbody></table></div><div class="row mt-3"><div class="col-md-12">`;
        
        const totalConIgv = subtotalGeneral + totalIgv;
        
        let tipoAfectacion = null;
        let porcentaje = 0;
        let nombreConcepto = '';
        let montoAfectacion = 0;
        
        if (compra.porcentaje_detraccion && parseFloat(compra.porcentaje_detraccion) > 0) {
            tipoAfectacion = 'DETRACCION';
            porcentaje = parseFloat(compra.porcentaje_detraccion);
            nombreConcepto = compra.nombre_detraccion;
            montoAfectacion = (totalConIgv * porcentaje) / 100;
        } else if (compra.porcentaje_retencion && parseFloat(compra.porcentaje_retencion) > 0) {
            tipoAfectacion = 'RETENCION';
            porcentaje = parseFloat(compra.porcentaje_retencion);
            nombreConcepto = compra.nombre_retencion;
            montoAfectacion = (totalConIgv * porcentaje) / 100;
        } else if (compra.porcentaje_percepcion && parseFloat(compra.porcentaje_percepcion) > 0) {
            tipoAfectacion = 'PERCEPCION';
            porcentaje = parseFloat(compra.porcentaje_percepcion);
            nombreConcepto = compra.nombre_percepcion;
            montoAfectacion = (totalConIgv * porcentaje) / 100;
        }
        
        let totalFinal = 0;
        
        if (tipoAfectacion === 'DETRACCION') {
            totalFinal = totalConIgv - montoAfectacion;
        } else if (tipoAfectacion === 'RETENCION') {
            totalFinal = totalConIgv - montoAfectacion;
        } else if (tipoAfectacion === 'PERCEPCION') {
            totalFinal = totalConIgv + montoAfectacion;
        } else {
            totalFinal = totalConIgv;
        }
        
        html += `<div class="alert alert-light" style="margin-bottom: 10px; padding: 10px;">
                    <div style="font-size: 14px; text-align: center; margin-bottom: 5px;">
                        <i class="fa fa-calculator text-secondary"></i> <strong>SUBTOTAL:</strong> ${simboloMoneda} ${subtotalGeneral.toFixed(2)}
                    </div>
                    <div style="font-size: 13px; text-align: center; margin-bottom: 5px;">
                        <i class="fa fa-percent text-secondary"></i> <strong>IGV TOTAL:</strong> ${simboloMoneda} ${totalIgv.toFixed(2)}
                    </div>
                    <div style="font-size: 14px; text-align: center; font-weight: bold; padding: 5px; background-color: #e3f2fd; border-radius: 4px; margin-bottom: 5px;">
                        <i class="fa fa-calculator text-primary"></i> <strong>TOTAL CON IGV:</strong> ${simboloMoneda} ${totalConIgv.toFixed(2)}
                    </div>`;
        
        if (tipoAfectacion === 'DETRACCION') {
            html += `<div style="font-size: 13px; text-align: center; color: #ffc107; margin-bottom: 5px;">
                        <i class="fa fa-minus-circle"></i> <strong>Detracción ${nombreConcepto} (${porcentaje}%):</strong> -${simboloMoneda} ${montoAfectacion.toFixed(2)}
                    </div>`;
        }
        
        if (tipoAfectacion === 'RETENCION') {
            html += `<div style="font-size: 13px; text-align: center; color: #2196f3; margin-bottom: 5px;">
                        <i class="fa fa-minus-circle"></i> <strong>Retención ${nombreConcepto} (${porcentaje}%):</strong> -${simboloMoneda} ${montoAfectacion.toFixed(2)}
                    </div>`;
        }
        
        if (tipoAfectacion === 'PERCEPCION') {
            html += `<div style="font-size: 13px; text-align: center; color: #4caf50; margin-bottom: 5px;">
                        <i class="fa fa-plus-circle"></i> <strong>Percepción ${nombreConcepto} (${porcentaje}%):</strong> +${simboloMoneda} ${montoAfectacion.toFixed(2)}
                    </div>`;
        }
        
        html += `</div>
                <div class="alert alert-success text-center" style="font-size: 18px; font-weight: bold; margin: 0; padding: 15px;">
                    <i class="fa fa-money"></i> TOTAL A PAGAR: ${simboloMoneda} ${totalFinal.toFixed(2)}
                </div></div></div></div></div>`;
        
        contenido.innerHTML = html;
        contenido.style.display = 'block';
        
        //  CONFIGURAR EVENTOS PARA MODALES DE CENTROS DE COSTO
        configurarEventosCentrosCostoDetalle();
    }

    //  FUNCIÓN PARA MANEJAR MODALES DE CENTROS DE COSTO EN ESTA VISTA
    function configurarEventosCentrosCostoDetalle() {
        // Abrir modal de centros de costo
        document.querySelectorAll('.btn-ver-centros-costo-detalle').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const modalId = this.getAttribute('data-modal-id');
                const modalElement = document.getElementById(modalId);
                
                if (modalElement) {
                    $(modalElement).modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    $(modalElement).modal('show');
                }
            });
        });
        
        // Cerrar modal de centros de costo
        document.querySelectorAll('.close-centros-modal-detalle').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const modalId = this.getAttribute('data-modal-id');
                const modalElement = document.getElementById(modalId);
                
                if (modalElement) {
                    $(modalElement).modal('hide');
                    
                    // Asegurar que el modal padre siga abierto
                    setTimeout(() => {
                        if (!$('#modalDetalleCompra').hasClass('show')) {
                            $('#modalDetalleCompra').modal('show');
                        }
                    }, 300);
                }
            });
        });
        
        // Prevenir que el cierre de modal hijo cierre el padre
        $('[id^="modalCentrosCostoDetalle"]').on('hidden.bs.modal', function (e) {
            e.stopPropagation();
            // Reabrir el modal padre si se cerró accidentalmente
            if (!$('#modalDetalleCompra').hasClass('show')) {
                $('#modalDetalleCompra').modal('show');
            }
        });
    }

    function mostrarErrorDetalle(mensaje) {
        const errorDiv = document.getElementById('error-detalle-compra');
        errorDiv.querySelector('p').textContent = mensaje;
        errorDiv.style.display = 'block';
    }
    
    // ============================================
    // INICIALIZACIÓN FINAL
    // ============================================
    
    // ============================================
    // HANDLERS PARA BOTONES DE OPCIONES
    // ============================================
    
    // Handler: Opción Salida Directa
    /* 1****************************************************************************** */
// BOTÓN DE RE-VERIFICACIÓN MANUAL
const btnReverificar = document.getElementById('btn-reverificar-manual');
if (btnReverificar) {
    btnReverificar.addEventListener('click', function() {
        Swal.fire({
            title: 'Actualizando...',
            text: 'Re-calculando cantidades según stock actual',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        
        window.location.href = 'pedido_verificar.php?id=<?php echo $id_pedido; ?>&force_reload=1';
    });
}
    // ============================================
    // 🆕 HANDLERS PARA BOTONES DE OPCIONES DE STOCK
    // ============================================
    
    // Handler: Opción Salida Directa (Stock suficiente en destino)
    document.addEventListener('click', function(event) {
        const btnSalida = event.target.closest('.btn-opcion-salida');
        if (!btnSalida) return;
        
        event.preventDefault();
        event.stopPropagation();
        
        const idPedido = btnSalida.dataset.idPedido;
        const idProducto = btnSalida.dataset.idProducto;
        const descripcion = btnSalida.dataset.descripcion;
        const cantidad = parseFloat(btnSalida.dataset.cantidad);
        
        Swal.fire({
            title: '✅ Stock Disponible',
            html: `
                <div style="text-align: left; padding: 10px;">
                    <p><strong>Producto:</strong><br>${descripcion}</p>
                    <hr>
                    <p class="text-success"><i class="fa fa-check-circle"></i> Hay stock suficiente en la ubicación destino</p>
                    <p><strong>Cantidad a generar salida:</strong> <span class="badge badge-success">${cantidad.toFixed(2)}</span> unidades</p>
                </div>
            `,
            icon: 'success',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa fa-truck"></i> Sí, generar salida',
            cancelButtonText: '<i class="fa fa-times"></i> Cancelar',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirigir a salidas_nuevo.php con el pedido
                window.location.href = `salidas_nuevo.php?desde_pedido=${idPedido}`;
            }
        });
    });
    
    // Handler: Opción Traslado (Stock en otras ubicaciones)
    document.addEventListener('click', function(event) {
        const btnTraslado = event.target.closest('.btn-opcion-traslado');
        if (!btnTraslado) return;
        
        event.preventDefault();
        event.stopPropagation();
        
        const idPedido = btnTraslado.dataset.idPedido;
        const idProducto = btnTraslado.dataset.idProducto;
        const descripcion = btnTraslado.dataset.descripcion;
        const cantidad = parseFloat(btnTraslado.dataset.cantidad);
        
        Swal.fire({
            title: '⚠️ Traslado Interno',
            html: `
                <div style="text-align: left; padding: 10px;">
                    <p><strong>Producto:</strong><br>${descripcion}</p>
                    <hr>
                    <p class="text-warning"><i class="fa fa-exclamation-triangle"></i> Stock disponible en otras ubicaciones del mismo almacén</p>
                    <p><strong>Cantidad a trasladar:</strong> <span class="badge badge-warning">${cantidad.toFixed(2)}</span> unidades</p>
                    <p class="text-muted">Se generará una salida interna desde otras ubicaciones</p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa fa-exchange-alt"></i> Sí, generar traslado',
            cancelButtonText: '<i class="fa fa-times"></i> Cancelar',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirigir a salidas_nuevo.php con el pedido
                window.location.href = `salidas_nuevo.php?desde_pedido=${idPedido}`;
            }
        });
    });
    
    // Handler: Opción Compra (Sin stock)
    document.addEventListener('click', function(event) {
        const btnOC = event.target.closest('.btn-opcion-compra');
        if (!btnOC) return;
        
        event.preventDefault();
        event.stopPropagation();
        
        const idPedido = btnOC.dataset.idPedido;
        const idProducto = btnOC.dataset.idProducto;
        const descripcion = btnOC.dataset.descripcion;
        const cantidad = parseFloat(btnOC.dataset.cantidad);
        
        Swal.fire({
            title: '❌ Sin Stock',
            html: `
                <div style="text-align: left; padding: 10px;">
                    <p><strong>Producto:</strong><br>${descripcion}</p>
                    <hr>
                    <p class="text-warning"><i class="fa fa-exclamation-triangle"></i> No hay suficiente stock en el almacén</p>
                    <p><strong>Cantidad necesaria:</strong> <span class="badge badge-danger">${cantidad.toFixed(2)}</span> unidades</p>
                    <p class="text-muted">Se agregará a una nueva orden de compra</p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa fa-shopping-cart"></i> Sí, generar OC',
            cancelButtonText: '<i class="fa fa-times"></i> Cancelar',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                // 🔹 BUSCAR EL BOTÓN ORIGINAL "Agregar a Orden"
                const itemsPendientes = document.querySelectorAll('.item-pendiente');
                let btnAgregarOrden = null;
                
                itemsPendientes.forEach(item => {
                    const itemIdProducto = item.getAttribute('data-id-producto');
                    if (itemIdProducto === idProducto) {
                        btnAgregarOrden = item.querySelector('.btn-agregarOrden');
                    }
                });
                
                if (btnAgregarOrden) {
                    // Mostrar formulario de nueva orden
                    const contenedorTabla = document.getElementById('contenedor-tabla-ordenes');
                    const contenedorNuevaOrden = document.getElementById('contenedor-nueva-orden');
                    
                    if (contenedorTabla) contenedorTabla.style.display = 'none';
                    if (contenedorNuevaOrden) contenedorNuevaOrden.style.display = 'block';
                    
                    const btnNuevaOrden = document.getElementById('btn-nueva-orden');
                    if (btnNuevaOrden) {
                        btnNuevaOrden.innerHTML = '<i class="fa fa-table"></i> Ver Órdenes';
                        btnNuevaOrden.classList.remove('btn-primary');
                        btnNuevaOrden.classList.add('btn-secondary');
                    }
                    
                    // Establecer fecha actual
                    const fechaOrden = document.getElementById('fecha_orden');
                    if (fechaOrden) {
                        fechaOrden.value = new Date().toISOString().split('T')[0];
                    }
                    
                    // SIMULAR CLIC EN EL BOTÓN "Agregar a Orden"
                    setTimeout(() => {
                        btnAgregarOrden.click();
                        
                        Swal.fire({
                            icon: 'success',
                            title: ' Item Agregado',
                            text: 'El producto se agregó a la nueva orden de compra',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }, 300);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se encontró el botón para agregar a orden',
                        confirmButtonText: 'Entendido'
                    });
                }
            }
        });
    });
    
    // Handler: Opción Mixta (Traslado + Compra)
    document.addEventListener('click', function(event) {
        const btnMixto = event.target.closest('.btn-opcion-mixta');
        if (!btnMixto) return;
        
        event.preventDefault();
        event.stopPropagation();
        
        const idPedido = btnMixto.dataset.idPedido;
        const idProducto = btnMixto.dataset.idProducto;
        const descripcion = btnMixto.dataset.descripcion;
        const cantidadTraslado = parseFloat(btnMixto.dataset.cantidadTraslado);
        const cantidadCompra = parseFloat(btnMixto.dataset.cantidadCompra);
        
        Swal.fire({
            title: ' Opción Mixta',
            html: `
                <div style="text-align: left; padding: 10px;">
                    <p><strong>Producto:</strong><br>${descripcion}</p>
                    <hr>
                    <div class="mb-3">
                        <p><strong> Traslado Interno:</strong></p>
                        <p class="ml-3">
                            <span class="badge badge-warning">${cantidadTraslado.toFixed(2)}</span> unidades
                            <br><small class="text-muted">Desde otras ubicaciones del almacén</small>
                        </p>
                    </div>
                    <div class="mb-3">
                        <p><strong> Orden de Compra:</strong></p>
                        <p class="ml-3">
                            <span class="badge badge-danger">${cantidadCompra.toFixed(2)}</span> unidades
                            <br><small class="text-muted">Lo que falta después del traslado</small>
                        </p>
                    </div>
                    <hr>
                    <p class="text-info"><i class="fa fa-info-circle"></i> Primero se generará el traslado, luego la orden de compra</p>
                </div>
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#17a2b8',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa fa-random"></i> Sí, proceder con ambas',
            cancelButtonText: '<i class="fa fa-times"></i> Cancelar',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Paso 1: Ir a generar salida (traslado)
                Swal.fire({
                    icon: 'info',
                    title: 'Paso 1: Generar Traslado',
                    html: `<p>Se redirigirá para generar la salida interna de <strong>${cantidadTraslado.toFixed(2)}</strong> unidades</p>`,
                    confirmButtonText: 'Continuar',
                    allowOutsideClick: false
                }).then(() => {
                    // Redirigir a salidas para el traslado
                    window.location.href = `salidas_nuevo.php?desde_pedido=${idPedido}&mixto=1&cantidad_compra=${cantidadCompra}&id_producto=${idProducto}`;
                });
            }
        });
    });
    
    // ============================================    
    if (modoEditar) {
        setTimeout(function() {
            document.querySelectorAll('[id^="item-orden-"]').forEach(function(item) {
                const cantidadInput = item.querySelector('.cantidad-item');
                const precioInput = item.querySelector('.precio-item');
                const igvInput = item.querySelector('.igv-item');
                
                if (cantidadInput && precioInput && igvInput) {
                    cantidadInput.dispatchEvent(new Event('input'));
                }
            });
            
            actualizarTotalGeneral();
        }, 300);
    }

    // Llamar después de operaciones críticas
    function reverificarItemsPedido() {
        fetch('pedido_verificar.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `reverificar_items=1&id_pedido=<?php echo $id_pedido; ?>`
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                console.log(`✅ ${data.items_reverificados} items re-verificados`);
                window.location.reload();
            }
        });
    }


   // Datos para edición de salida
    <?php if ($modo_editar_salida && $salida_data): ?>
    const salidaEditar = {
        id: <?php echo $salida_data['id_salida']; ?>,
        ndoc: '<?php echo $salida_data['ndoc_salida']; ?>',
        fecha: '<?php echo $salida_data['fec_req_salida']; ?>',
        almacen_origen: <?php echo $salida_data['id_almacen_origen']; ?>,
        ubicacion_origen: <?php echo $salida_data['id_ubicacion_origen']; ?>,
        almacen_destino: <?php echo $salida_data['id_almacen_destino']; ?>,
        ubicacion_destino: <?php echo $salida_data['id_ubicacion_destino']; ?>,
        obs: '<?php echo $salida_data['obs_salida']; ?>'
    };
    const itemsSalidaEditar = <?php echo json_encode($salida_detalle); ?>;
    <?php endif; ?>
    
    //  VALIDACIÓN INICIAL - SOLO UNA VEZ
    if (!esOrdenServicio && !pedidoAnulado && !modoEditar && !modoEditarSalida) {
        const tiempoEspera = debeValidar ? 1500 : 1000;
        
        setTimeout(() => {
            console.log('🔄 Validando estado inicial de botones (ejecución única)...');
            
            const btnNuevaSalida = document.getElementById('btn-nueva-salida');
            if (btnNuevaSalida) {
                const hayItemsDisponibles = validarItemsDisponiblesParaSalida();
                
                console.log('📊 Validación inicial salidas:', {
                    hayDisponibles: hayItemsDisponibles,
                    estadoActualBoton: btnNuevaSalida.disabled,
                    vieneDeGuardar: debeValidar
                });
                
                if (hayItemsDisponibles && btnNuevaSalida.disabled) {
                    console.log('⚠️ CORRECCIÓN: Habilitando botón');
                    btnNuevaSalida.disabled = false;
                    btnNuevaSalida.classList.remove('btn-secondary');
                    btnNuevaSalida.classList.add('btn-success');
                    btnNuevaSalida.innerHTML = '<i class="fa fa-truck"></i> Nueva Salida';
                    btnNuevaSalida.title = '';
                } else if (!hayItemsDisponibles && !btnNuevaSalida.disabled) {
                    console.log('⚠️ CORRECCIÓN: Deshabilitando botón');
                    btnNuevaSalida.disabled = true;
                    btnNuevaSalida.classList.remove('btn-success');
                    btnNuevaSalida.classList.add('btn-secondary');
                    btnNuevaSalida.innerHTML = '<i class="fa fa-ban"></i> Nueva Salida';
                    btnNuevaSalida.title = 'No hay items disponibles';
                } else {
                    console.log('✅ Estado del botón correcto');
                }
            } else {
                console.warn('⚠️ No se encontró el botón Nueva Salida (verificar renderizado PHP)');
            }
            
            recalcularEstadoItems();
        }, tiempoEspera);
    }



   function configurarControlOrigenDinamico() {
    const ubicacionOrigenSelect = document.getElementById('ubicacion_origen_salida');
    const almacenOrigenSelect = document.getElementById('almacen_origen_salida');
    
    if (!ubicacionOrigenSelect || !almacenOrigenSelect) {
        console.warn('⚠️ No se encontraron los selectores de origen');
        return;
    }
    
    console.log('🔧 Configurando control de origen dinámico...');
    
    ubicacionOrigenSelect.addEventListener('change', function() {
        const ubicacionSeleccionada = parseInt(this.value);
        
        console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        console.log('🔄 Cambio de Ubicación Origen');
        console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        console.log('📍 ID Ubicación:', ubicacionSeleccionada);
        
        if (!ubicacionSeleccionada) {
            console.log('⚠️ No hay ubicación seleccionada');
            return;
        }
        
        const optionSeleccionada = this.options[this.selectedIndex];
        const idAlmacen = parseInt(optionSeleccionada.getAttribute('data-id-almacen'));
        const stockNuevo = parseFloat(optionSeleccionada.getAttribute('data-stock')) || 0;
        
        console.log('📊 Datos de la ubicación:', {
            ubicacion: ubicacionSeleccionada,
            almacen: idAlmacen,
            stock: stockNuevo
        });
        
        //  ACTUALIZAR ALMACÉN ORIGEN
        if (idAlmacen && almacenOrigenSelect) {
            almacenOrigenSelect.value = idAlmacen;
            console.log(`🏢 Almacén actualizado a ID: ${idAlmacen}`);
        } else {
            console.error('❌ No se pudo actualizar el almacén:', { idAlmacen, almacenOrigenSelect });
        }
        
        //  ACTUALIZAR CANTIDADES MÁXIMAS DE TODOS LOS ITEMS AGREGADOS
        const itemsAgregados = document.querySelectorAll('[id^="item-salida-"]');
        
        console.log(' DEBUG - Items encontrados:', itemsAgregados.length);
        
        if (itemsAgregados.length > 0) {
            console.log(` Actualizando ${itemsAgregados.length} items con nuevo stock de ubicación...`);
            
            itemsAgregados.forEach((itemElement, index) => {
                console.log(`\n Procesando item ${index + 1}:`, itemElement.id);
                
                const cantidadInput = itemElement.querySelector('.cantidad-salida');
                
                if (!cantidadInput) {
                    console.error(` No se encontró input de cantidad para item ${index + 1}`);
                    return;
                }
                
                console.log('✓ Input encontrado');
                
                // DEBUG: Verificar atributos
                console.log('🔍 DEBUG - Atributos del input:', {
                    'data-cantidad-maxima': cantidadInput.getAttribute('data-cantidad-maxima'),
                    'data-cantidad-maxima-os': cantidadInput.getAttribute('data-cantidad-maxima-os'),
                    'value': cantidadInput.value,
                    'max': cantidadInput.getAttribute('max')
                });
                
                // Obtener la cantidad máxima de OS del data attribute
                const cantidadMaximaOS = parseFloat(cantidadInput.getAttribute('data-cantidad-maxima-os')) || 0;
                
                console.log('📊 Cantidad OS extraída:', cantidadMaximaOS);
                
                if (cantidadMaximaOS === 0) {
                    console.error('❌ PROBLEMA: data-cantidad-maxima-os es 0 o no existe');
                    console.error('   Verifica que el atributo se esté agregando al crear el item');
                    return;
                }
                
                // Calcular nueva cantidad máxima (el menor entre stock y cantidad OS)
                const nuevaCantidadMaxima = Math.min(cantidadMaximaOS, stockNuevo);
                
                console.log('📊 Cálculo:', {
                    cantidadOS: cantidadMaximaOS,
                    stockNuevo: stockNuevo,
                    resultado: nuevaCantidadMaxima
                });
                
                // Actualizar el max del input
                cantidadInput.setAttribute('max', nuevaCantidadMaxima.toFixed(2));
                cantidadInput.setAttribute('data-cantidad-maxima', nuevaCantidadMaxima.toFixed(2));
                
                // Si el valor actual excede el nuevo máximo, ajustarlo
                const valorActual = parseFloat(cantidadInput.value) || 0;
                if (valorActual > nuevaCantidadMaxima) {
                    cantidadInput.value = nuevaCantidadMaxima.toFixed(2);
                    console.log(` Valor ajustado de ${valorActual} a ${nuevaCantidadMaxima.toFixed(2)}`);
                } else {
                    console.log(` Valor actual ${valorActual} es válido (≤ ${nuevaCantidadMaxima.toFixed(2)})`);
                }
                
                // Actualizar el texto del máximo
                const smallMaximo = itemElement.querySelector('small.text-info');
                if (smallMaximo) {
                    smallMaximo.innerHTML = `<i class="fa fa-arrow-up"></i> Máx: ${nuevaCantidadMaxima.toFixed(2)}`;
                    console.log('✓ Texto de máximo actualizado');
                } else {
                    console.warn(' No se encontró elemento small.text-info');
                }
                
                console.log(` Item ${index + 1} actualizado correctamente`);
            });
            
            console.log('\n Todos los items procesados');
        } else {
            console.log(' No hay items agregados para actualizar');
        }
        
        console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n');
    });
    
    // Trigger inicial
    setTimeout(() => {
        const valorInicial = ubicacionOrigenSelect.value;
        console.log('🔍 Verificando valor inicial:', valorInicial);
        
        if (valorInicial) {
            console.log('🔄 Disparando evento change inicial...');
            ubicacionOrigenSelect.dispatchEvent(new Event('change'));
        }
    }, 300);
}

// Inicializar
setTimeout(configurarControlOrigenDinamico, 500);
}); // ← FIN DOMContentLoaded - ASEGÚRATE QUE SOLO HAY UNO
</script>