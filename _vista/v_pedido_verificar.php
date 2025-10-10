<?php 
//=======================================================================
// VISTA: v_pedidos_verificar.php -  CON EDICIÓN INTEGRADA
//=======================================================================
$pedido = $pedido_data[0];
$pedido_anulado = ($pedido['est_pedido'] == 0);
$pedido['tiene_verificados'] = PedidoTieneVerificaciones($id_pedido);

?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Verificar Pedido <?php 
                    echo $modo_editar ? '- Editando Orden' : ''; 
                    echo $pedido_anulado ? ' - PEDIDO ANULADO' : '';
                ?></h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <!-- Información básica del pedido -->
        <div class="row mb-2">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title" style="padding: 10px 15px;">
                        <h2 style="margin: 0; font-size: 18px;">Pedido <small><?php echo $pedido['cod_pedido']; ?></small></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" style="padding: 10px 15px;">
                        <div class="row" style="font-size: 13px;">
                            <div class="col-md-3">
                                <strong>Código:</strong> <?php echo $pedido['cod_pedido']; ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Nombre:</strong> <?php echo $pedido['nom_pedido']; ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($pedido['fec_req_pedido'])); ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Lugar:</strong> <?php echo $pedido['lug_pedido']; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="x_panel">
                    <div class="x_title" style="padding: 8px 15px;">
                        <h2 style="margin: 0; font-size: 16px;">Items <small id="contador-pendientes">(<?php echo "Cantidad: " . count($pedido_detalle); ?>)</small></h2>
                        <?php if ($pedido_anulado): ?>
                                <span class="badge badge-danger ml-2">PEDIDO ANULADO</span>
                            <?php endif; ?>
                        </h2>
                        <div class="clearfix"></div>
                    </div>
                        
                    <div class="x_content" style="max-height: 650px; overflow-y: auto; padding: 5px;">
                        <div id="contenedor-pendientes">
                            <?php 
                                $contador_detalle = 1;
                                foreach ($pedido_detalle as $detalle) { 
                                    $comentario = $detalle['com_pedido_detalle'];
                                    $unidad = '';
                                    $observaciones = '';
                                    
                                    if (preg_match('/Unidad:\s*([^|]*)\s*\|/', $comentario, $matches)) {
                                        $unidad = trim($matches[1]);
                                    }
                                    if (preg_match('/Obs:\s*(.*)$/', $comentario, $matches)) {
                                        $observaciones = trim($matches[1]);
                                    }
                                    
                                    // Parsear requisitos
                                    $requisitos = $detalle['req_pedido'];
                                    $sst = $ma = $ca = '';
                                    
                                    if (preg_match('/SST:\s*([^|]*)\s*\|/', $requisitos, $matches)) {
                                        $sst = trim($matches[1]);
                                    }
                                    if (preg_match('/MA:\s*([^|]*)\s*\|/', $requisitos, $matches)) {
                                        $ma = trim($matches[1]);
                                    }
                                    if (preg_match('/CA:\s*(.*)$/', $requisitos, $matches)) {
                                        $ca = trim($matches[1]);
                                    }

                                    $esVerificado = !is_null($detalle['cant_fin_pedido_detalle']);
                                    $stockInsuficiente = $detalle['cantidad_disponible_almacen'] < $detalle['cant_pedido_detalle'];
                                    $pedidoAnulado = ($pedido['est_pedido'] == 0);
                                    $esAutoOrden = ($pedido['id_producto_tipo'] == 2);
                                    
                                    // CORRECCIÓN DE LÓGICA: Determinar correctamente el estado del item
                                    if ($esAutoOrden) {
                                        $colorFondo = '#e3f2fd';
                                        $colorBorde = '#2196f3';
                                        $claseTexto = 'text-primary';
                                        $icono = 'fa-cog';
                                        $estadoTexto = 'Auto-Orden';
                                    } else if ($detalle['est_pedido_detalle'] == 2) {
                                        // Item cerrado manualmente
                                        $colorFondo = '#f8d7da';
                                        $colorBorde = '#dc3545';
                                        $claseTexto = 'text-danger';
                                        $icono = 'fa-times-circle';
                                        $estadoTexto = 'Cerrado';
                                    } else if ($esVerificado && $stockInsuficiente) {
                                        // Item verificado y con stock insuficiente (listo para orden)
                                        $colorFondo = '#d4edda';
                                        $colorBorde = '#28a745';
                                        $claseTexto = 'text-success';
                                        $icono = 'fa-check-circle';
                                        $estadoTexto = 'Verificado';
                                    } else if (!$esVerificado && $stockInsuficiente) {
                                        // Item pendiente de verificación (stock insuficiente, no verificado)
                                        $colorFondo = '#fff3cd';
                                        $colorBorde = '#ffc107';
                                        $claseTexto = 'text-warning';
                                        $icono = 'fa-clock-o';
                                        $estadoTexto = 'Pendiente';
                                    } else {
                                        // Item con stock suficiente (completo)
                                        $colorFondo = '#d4edda';
                                        $colorBorde = '#28a745';
                                        $claseTexto = 'text-success';
                                        $icono = 'fa-check-circle';
                                        $estadoTexto = 'Completo';
                                    }

                                    // Verificar si este item ya está en la orden que estamos editando
                                    $enOrdenActual = false;
                                    if ($modo_editar && !empty($orden_detalle)) {
                                        foreach ($orden_detalle as $item_orden) {
                                            if ($item_orden['id_producto'] == $detalle['id_producto']) {
                                                $enOrdenActual = true;
                                                break;
                                            }
                                        }
                                    }
                                ?>
                                
                            <div class="item-pendiente border mb-2" 
                                style="background-color: <?php echo $colorFondo; ?>; border-left: 4px solid <?php echo $colorBorde; ?> !important; padding: 8px 12px; border-radius: 4px;" 
                                data-item="<?php echo $contador_detalle; ?>">
                                
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="<?php echo $claseTexto; ?>" style="font-weight: 600; font-size: 14px;">
                                        <i class="fa <?php echo $icono; ?>"></i> Item <?php echo $contador_detalle; ?> - <?php echo $estadoTexto; ?>
                                    </span>
                                    <span>
                                        Stock Disponible/Almacén:
                                        <?php echo $detalle['cantidad_disponible_real']; ?> /
                                        <?php echo $detalle['cantidad_disponible_almacen']; ?>
                                    </span>
                                    <?php if ($esAutoOrden) { ?>
                                        <span class="badge badge-primary" style="font-size: 10px; padding: 2px 6px;">
                                            <i class="fa fa-cog"></i> En Orden Automática
                                        </span>
                                        
                                        <div class="datos-auto-orden" style="display: none;"
                                            data-id-detalle="<?php echo $detalle['id_pedido_detalle']; ?>"
                                            data-id-producto="<?php echo $detalle['id_producto']; ?>"
                                            data-descripcion="<?php echo htmlspecialchars($detalle['prod_pedido_detalle']); ?>"
                                            data-cantidad="<?php echo $detalle['cant_pedido_detalle']; ?>">
                                        </div>
                                    <?php } elseif (!$esVerificado && $stockInsuficiente && !$pedidoAnulado) { ?>
                                        <button type="button" class="btn btn-success btn-xs verificar-btn"
                                                data-id-detalle="<?php echo $detalle['id_pedido_detalle']; ?>"
                                                data-cantidad-actual="<?php echo $detalle['cant_pedido_detalle']; ?>"
                                                data-cantidad-almacen="<?php echo $detalle['cantidad_disponible_almacen']; ?>"
                                                title="Verificar Item" style="padding: 2px 8px; font-size: 11px;">
                                            Verificar
                                        </button>
                                        
                                    <?php } elseif ($esVerificado && $stockInsuficiente && $detalle['est_pedido_detalle'] != 2 && !$modo_editar  && !$pedidoAnulado) { ?>
                                        <button type="button" 
                                                class="btn btn-primary btn-xs btn-agregarOrden" 
                                                data-id-detalle="<?php echo $detalle['id_pedido_detalle']; ?>"
                                                data-id-producto="<?php echo $detalle['id_producto']; ?>"
                                                data-descripcion="<?php echo htmlspecialchars($detalle['prod_pedido_detalle']); ?>"
                                                data-cantidad-verificada="<?php echo htmlspecialchars($detalle['cant_fin_pedido_detalle']); ?>"
                                                title="Agregar a Orden" 
                                                style="padding: 2px 8px; font-size: 11px;">
                                            <i class="fa fa-check"></i> Agregar a Orden
                                        </button>
                                        
                                    <?php } elseif ($esVerificado && $stockInsuficiente && $detalle['est_pedido_detalle'] != 2 && $modo_editar && !$enOrdenActual && !$pedidoAnulado) { ?>
                                        <button type="button" 
                                                class="btn btn-primary btn-xs btn-agregarOrden" 
                                                data-id-detalle="<?php echo $detalle['id_pedido_detalle']; ?>"
                                                data-id-producto="<?php echo $detalle['id_producto']; ?>"
                                                data-descripcion="<?php echo htmlspecialchars($detalle['prod_pedido_detalle']); ?>"
                                                data-cantidad-verificada="<?php echo htmlspecialchars($detalle['cant_fin_pedido_detalle']); ?>"
                                                title="Agregar a Orden" 
                                                style="padding: 2px 8px; font-size: 11px;">
                                            <i class="fa fa-plus"></i> Agregar
                                        </button>
                                        
                                    <?php } elseif ($enOrdenActual) { ?>
                                        <span class="badge badge-info" style="font-size: 10px; padding: 2px 6px;">
                                            <i class="fa fa-check"></i> En Orden
                                        </span>
                                        
                                    <?php } elseif ($esVerificado && !$stockInsuficiente) { ?>
                                        <span class="badge badge-success" style="font-size: 10px; padding: 2px 6px;">
                                            ✓ Completo
                                        </span>
                                        
                                    <?php } else { ?>
                                        <span class="badge badge-info" style="font-size: 10px; padding: 2px 6px;">
                                            ✓ Cerrado
                                        </span>
                                    <?php } ?>
                                </div>
                                
                                <div style="font-size: 11px; color: #333; line-height: 1.4;">
                                    <strong>Descripción:</strong> 
                                    <span style="color: #666;"><?php echo strlen($detalle['prod_pedido_detalle']) > 80 ? substr($detalle['prod_pedido_detalle'], 0, 80) . '...' : $detalle['prod_pedido_detalle']; ?></span>
                                    <span style="margin: 0 8px;">|</span>
                                    <strong>Cant:</strong> <?php echo $detalle['cant_pedido_detalle']; ?>
                                    <span style="margin: 0 8px;">|</span>
                                    <strong>Unid:</strong> <?php echo $unidad; ?>
                                    <span style="margin: 0 8px;">|</span>
                                    <strong>SST:</strong> <?php echo $sst; ?>
                                    <span style="margin: 0 8px;">|</span>
                                    <strong>MA:</strong> <?php echo $ma; ?>
                                    <span style="margin: 0 8px;">|</span>
                                    <strong>CA:</strong> <?php echo $ca; ?>
                                    
                                    <?php if ($esVerificado) { ?>
                                        <span style="margin: 0 8px;">|</span>
                                        <strong>Cant. Verificada:</strong> <?php echo $detalle['cant_fin_pedido_detalle']; ?>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php 
                                $contador_detalle++;
                            } 
                            ?>
                        </div>
                        
                        <div id="mensaje-sin-pendientes" style="display: none;" class="text-center p-3">
                            <i class="fa fa-check-circle fa-2x text-success mb-2"></i>
                            <h5 class="text-success">¡Todos verificados!</h5>
                            <p class="text-muted" style="font-size: 12px;">No hay items pendientes.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="x_panel">
                    <div class="x_title" style="padding: 8px 15px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 style="margin: 0; font-size: 16px;">
                                <?php echo $modo_editar ? 'Editando Orden' : 'Items Verificados'; ?>
                                <small id="contador-verificados">(0 items)</small>
                            </h2>
                            <?php if (!$modo_editar && !$pedido_anulado): ?>
                            <button type="button" class="btn btn-primary btn-sm" id="btn-nueva-orden" style="padding: 4px 8px; font-size: 12px;">
                                <i class="fa fa-plus"></i> Nueva Orden
                            </button>
                            <?php endif; ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    
                    <div class="x_content" style="max-height: 650px; overflow-y: auto; padding: 5px;">
                        <!-- TABLA DE ÓRDENES EXISTENTES -->
                        <div id="contenedor-tabla-ordenes" <?php echo $modo_editar ? 'style="display: none;"' : ''; ?>>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" style="font-size: 12px;">
                                    <thead style="background-color: #f8f9fa;">
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
                                                $puede_agregar_pago = false;

                                                switch ($compra['est_compra']) {
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
                                                        $estado_clase = 'success';
                                                        $puede_agregar_pago = true; //  Solo puede pagar si está aprobada
                                                        break;
                                                    case 3:
                                                        $estado_texto = 'Cerrada';
                                                        $estado_clase = 'info';
                                                        $puede_agregar_pago = true; // También puede pagar si está cerrada
                                                        break;
                                                    case 4:  //  AGREGAR ESTE CASO
                                                        $estado_texto = 'Pagada';
                                                        $estado_clase = 'primary';
                                                        $puede_agregar_pago = false;
                                                        break;
                                                    default:
                                                        $estado_texto = 'Desconocido';
                                                        $estado_clase = 'secondary';
                                                }

                                                $fecha_formateada = date('d/m/Y', strtotime($compra['fec_compra']));
                                            ?>
                                                <tr>
                                                    <td><strong>ORD-<?php echo $compra['id_compra']; ?></strong></td>
                                                    <td><?php echo htmlspecialchars($compra['nom_proveedor']); ?></td>
                                                    <td><?php echo $fecha_formateada; ?></td>
                                                    <td>
                                                        <span class="badge badge-<?php echo $estado_clase; ?>">
                                                            <?php echo $estado_texto; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <!-- Botón Ver Detalles -->
                                                        <button class="btn btn-info btn-xs btn-ver-detalle" 
                                                                title="Ver Detalles"
                                                                data-id-compra="<?php echo $compra['id_compra']; ?>">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                        
                                                        <?php 
                                                        // Verificar si tiene aprobaciones (técnica o financiera)
                                                        $tiene_aprobacion_tecnica = !empty($compra['id_personal_aprueba_tecnica']);
                                                        $tiene_aprobacion_financiera = !empty($compra['id_personal_aprueba_financiera']);
                                                        $tiene_alguna_aprobacion = $tiene_aprobacion_tecnica || $tiene_aprobacion_financiera;
                                                        
                                                        // Solo se puede editar si está PENDIENTE y SIN aprobaciones
                                                        $puede_editar = ($compra['est_compra'] == 1 && !$tiene_alguna_aprobacion);
                                                        
                                                        if ($puede_editar) { ?>
                                                            <button class="btn btn-warning btn-xs ml-1 btn-editar-orden" 
                                                                    title="Editar Orden"
                                                                    data-id-compra="<?php echo $compra['id_compra']; ?>">
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                        <?php } else { 
                                                            // Determinar el mensaje según el estado
                                                            if ($compra['est_compra'] == 0) {
                                                                $mensaje = "No se puede editar - Orden anulada";
                                                            } elseif ($compra['est_compra'] == 2) {
                                                                $mensaje = "No se puede editar - Orden aprobada completamente";
                                                            } elseif ($compra['est_compra'] == 3) {
                                                                $mensaje = "No se puede editar - Orden cerrada";
                                                            } elseif ($tiene_alguna_aprobacion) {
                                                                $mensaje = "No se puede editar - Orden con aprobación iniciada";
                                                            } else {
                                                                $mensaje = "No se puede editar";
                                                            }
                                                        ?>
                                                            <button class="btn btn-outline-secondary btn-xs ml-1" 
                                                                    title="<?php echo $mensaje; ?>" 
                                                                    disabled>
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                        <?php } ?>

                                                        <!--  NUEVO: Botón Registrar Pago -->
                                                        <?php if ($puede_agregar_pago): ?>
                                                            <a href="pago_registrar.php?id_compra=<?php echo $compra['id_compra']; ?>" 
                                                            class="btn btn-success btn-xs ml-1" 
                                                            title="Registrar Pago">
                                                                <i class="fa fa-money"></i>
                                                            </a>
                                                        <?php else: ?>
                                                            <button class="btn btn-outline-secondary btn-xs ml-1" 
                                                                    title="No disponible - Orden <?php echo strtolower($estado_texto); ?>" 
                                                                    disabled>
                                                                <i class="fa fa-money"></i>
                                                            </button>
                                                        <?php endif; ?>

                                                        <!-- Botón Anular -->
                                                        <?php if ($compra['est_compra'] != 0) { ?>
                                                            <button class="btn btn-danger btn-xs ml-1 btn-anular-orden" 
                                                                    title="Anular Orden"
                                                                    data-id-compra="<?php echo $compra['id_compra']; ?>"
                                                                    data-id-pedido="<?php echo $id_pedido; ?>">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                        <?php } else { ?>
                                                            <button class="btn btn-outline-secondary btn-xs ml-1" 
                                                                    title="Orden anulada" 
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
                                                    <i class="fa fa-file-text-o fa-2x text-info mb-2"></i>
                                                    <h5 class="text-info">Sin órdenes de compra</h5>
                                                    <p class="text-muted" style="font-size: 12px;">Las órdenes de compra aparecerán aquí.</p>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div id="mensaje-sin-ordenes" class="text-center p-3" style="display: none;">
                                <i class="fa fa-file-text-o fa-2x text-info mb-2"></i>
                                <h5 class="text-info">Sin órdenes de compra</h5>
                                <p class="text-muted" style="font-size: 12px;">Las órdenes de compra aparecerán aquí.</p>
                            </div>
                        </div>

                        <!-- FORMULARIO PARA CREAR/EDITAR ORDEN -->
                        <div id="contenedor-nueva-orden" <?php echo $modo_editar ? 'style="display: block;"' : 'style="display: none;"'; ?>>
                            <form id="form-nueva-orden" method="POST" action="">
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
                                            <?php echo $modo_editar ? 'Editar Orden de Compra ORD-' . $id_compra_editar : 'Nueva Orden de Compra'; ?>
                                        </h6>
                                    </div>
                                    <div class="card-body" style="padding: 12px;">
                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <label style="font-size: 11px; font-weight: bold;">Fecha: <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control form-control-sm" id="fecha_orden" name="fecha_orden" 
                                                    value="<?php echo $modo_editar && $orden_data ? date('Y-m-d', strtotime($orden_data['fec_compra'])) : date('Y-m-d'); ?>" 
                                                    style="font-size: 12px;" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label style="font-size: 11px; font-weight: bold;">Proveedor: <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <select class="form-control form-control-sm" id="proveedor_orden" name="proveedor_orden" 
                                                            style="font-size: 12px;" required>
                                                        <option value="">Seleccionar proveedor...</option>
                                                        <?php
                                                        foreach ($proveedor as $prov) {
                                                            $selected = ($modo_editar && $orden_data && $orden_data['id_proveedor'] == $prov['id_proveedor']) ? 'selected' : '';
                                                            echo '<option value="' . htmlspecialchars($prov['id_proveedor']) . '" ' . $selected . '>' . htmlspecialchars($prov['nom_proveedor']) . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                    <div class="input-group-append" style="margin-left: 8px;">
                                                        <button type="button" class="btn btn-info btn-sm" id="btn-agregar-proveedor" title="Agregar nuevo proveedor">
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
                                                <label style="font-size: 11px; font-weight: bold;">Plazo de Entrega (días):</label>
                                                <input type="number" 
                                                    class="form-control form-control-sm" 
                                                    id="plazo_entrega" 
                                                    name="plazo_entrega"
                                                    value="<?php echo $modo_editar && $orden_data ? htmlspecialchars($orden_data['plaz_compra']) : ''; ?>"
                                                    placeholder="Dejar vacío o 0 para pago al contado"
                                                    min="0"
                                                    step="1"
                                                    style="font-size: 12px;">
                                                <small class="form-text text-muted">
                                                    Si no ingresa plazo, se considera pago al contado (sin alertas)
                                                </small>
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
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <label style="font-size: 11px; font-weight: bold;">Tipo de Porte:</label>
                                                <input type="text" class="form-control form-control-sm" id="tipo_porte" name="tipo_porte"
                                                    value="<?php echo $modo_editar && $orden_data ? htmlspecialchars($orden_data['port_compra']) : ''; ?>"
                                                    placeholder="Ej. Marítimo, Terrestre, Aéreo" style="font-size: 12px;">
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <label style="font-size: 11px; font-weight: bold;">Detracción (Opcional):</label>
                                                <div id="contenedor-detracciones" style="padding: 8px; background-color: #f8f9fa; border-radius: 4px;">
                                                    <?php
                                                    $detracciones = ObtenerDetracciones();
                                                    
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
                                                <small class="form-text text-muted">Seleccione una detracción si aplica. El monto se calculará automáticamente sobre el total.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="contenedor-items-orden" class="mb-3">
                                    <?php if ($modo_editar && !empty($orden_detalle)): ?>
                                        <?php foreach ($orden_detalle as $item): ?>
                                        <div class="alert alert-light p-2 mb-2" id="item-orden-<?php echo $item['id_compra_detalle']; ?>">
                                            <input type="hidden" name="items_orden[<?php echo $item['id_compra_detalle']; ?>][id_compra_detalle]" value="<?php echo $item['id_compra_detalle']; ?>">
                                            <input type="hidden" name="items_orden[<?php echo $item['id_compra_detalle']; ?>][id_producto]" value="<?php echo $item['id_producto']; ?>">
                                            <input type="hidden" name="items_orden[<?php echo $item['id_compra_detalle']; ?>][cantidad]" value="<?php echo $item['cant_compra_detalle']; ?>">
                                            
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <div style="font-size: 12px;">
                                                        <div class="mb-1">
                                                            <strong>Descripción:</strong> <?php echo htmlspecialchars($item['nom_producto']); ?>
                                                        </div>
                                                        <div>
                                                            <strong>Cantidad:</strong> <?php echo $item['cant_compra_detalle']; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label style="font-size: 11px; font-weight: bold;">Precio Unit.:</label>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" style="font-size: 11px;">
                                                                <?php echo ($orden_data['id_moneda'] == 1) ? 'S/.' : 'US$'; ?>
                                                            </span>
                                                        </div>
                                                        <input type="number" 
                                                            class="form-control form-control-sm precio-item" 
                                                            name="items_orden[<?php echo $item['id_compra_detalle']; ?>][precio_unitario]"
                                                            value="<?php echo $item['prec_compra_detalle']; ?>"
                                                            step="0.01" 
                                                            min="0"
                                                            style="font-size: 11px;"
                                                            required>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-danger btn-sm btn-remover-item" 
                                                            data-id-detalle="<?php echo $item['id_compra_detalle']; ?>"
                                                            data-id-compra-detalle="<?php echo $item['id_compra_detalle']; ?>">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-md-12">
                                                    <div class="subtotal-item text-right" id="subtotal-<?php echo $item['id_compra_detalle']; ?>" 
                                                         style="font-size: 12px; font-weight: bold; color: #007bff;">
                                                        Subtotal: <?php echo ($orden_data['id_moneda'] == 1) ? 'S/.' : 'US$'; ?> 
                                                        <?php echo number_format($item['cant_compra_detalle'] * $item['prec_compra_detalle'], 2); ?>
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
                                    <button type="submit" class="btn btn-<?php echo $modo_editar ? 'warning' : 'primary'; ?> btn-sm">
                                        <i class="fa fa-save"></i> <?php echo $modo_editar ? 'Actualizar Orden' : 'Guardar Orden'; ?>
                                    </button>
                                </div>
                            </form>
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
                            <div class="col-md-3">
                                <!-- <button type="button" class="btn btn-success btn-sm btn-block" id="btn-finalizar-verificacion" disabled>
                                    <i class="fa fa-check-circle"></i> Finalizar Verificación
                                </button>
                                 -->
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <p class="text-muted" style="font-size: 12px;">
                                <i class="fa fa-info-circle"></i> 
                                Recuerda verificar todos los items
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DE VERIFICACIÓN -->
<div class="modal fade" id="verificarModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="verificarForm" action="pedido_verificar.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Verificar Cantidad</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?php echo $id_pedido; ?>">
                    <input type="hidden" name="verificar_item" value="true">
                    <input type="hidden" id="id_pedido_detalle_input" name="id_pedido_detalle">
                    <div class="form-group">
                        <label for="fin_cant_pedido_detalle">Cantidad Verificada:</label>
                        <input type="number" class="form-control" id="fin_cant_pedido_detalle" name="fin_cant_pedido_detalle" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
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
                                        <td><input type="text" name="banco[]" class="form-control form-control-sm"></td>
                                        <td>
                                            <select name="id_moneda[]" class="form-control form-control-sm">
                                                <option value="">-- Moneda --</option>
                                                <?php foreach ($moneda as $m) { ?>
                                                    <option value="<?php echo $m['id_moneda']; ?>"><?php echo $m['nom_moneda']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td><input type="text" name="cta_corriente[]" class="form-control form-control-sm"></td>
                                        <td><input type="text" name="cta_interbancaria[]" class="form-control form-control-sm"></td>
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
                <button type="button" class="btn btn-outline-danger" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btn-guardar-proveedor-modal">
                    <i class="fa fa-save"></i> Registrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const esAutoOrden = <?php echo ($pedido['id_producto_tipo'] == 2) ? 'true' : 'false'; ?>;
    const pedidoAnulado = <?php echo ($pedido['est_pedido'] == 0) ? 'true' : 'false'; ?>;
    const modoEditar = <?php echo $modo_editar ? 'true' : 'false'; ?>;
    let itemsAgregadosOrden = new Set();

    // Verificar si generar salida al cargar la página
    if (!esAutoOrden && !pedidoAnulado && !modoEditar) {
        setTimeout(function() {
            verificarSiGenerarSalida();
        }, 1000);
    }
    // Si es auto-orden, preparar formulario automático
    if (esAutoOrden && !modoEditar) {
        setTimeout(function() {
            mostrarFormularioNuevaOrdenAuto();
            autoAgregarItemsAOrden();
        }, 800);
    }

    // Si estamos en modo edición, configurar eventos específicos
    if (modoEditar) {
        configurarEventosEdicion();
        actualizarTotalOrden();
    }

    // Configurar eventos generales
    configurarEventListeners();

    // ====================================================================
    // NUEVO: CONFIGURAR MODAL DE PROVEEDOR
    // ====================================================================
    const tablaCuentasModal = document.getElementById("tabla-cuentas-modal");
    const btnAgregarModal = document.getElementById("agregarCuentaModal");

    // Acción para agregar nueva fila de cuenta bancaria
    if (btnAgregarModal) {
        btnAgregarModal.addEventListener("click", function() {
            const nuevaFila = document.createElement("tr");
            nuevaFila.innerHTML = `
                <td><input type="text" name="banco[]" class="form-control form-control-sm"></td>
                <td>
                    <select name="id_moneda[]" class="form-control form-control-sm">
                        <option value="">-- Moneda --</option>
                        <?php foreach ($moneda as $m) { ?>
                            <option value="<?php echo $m['id_moneda']; ?>"><?php echo $m['nom_moneda']; ?></option>
                        <?php } ?>
                    </select>
                </td>
                <td><input type="text" name="cta_corriente[]" class="form-control form-control-sm"></td>
                <td><input type="text" name="cta_interbancaria[]" class="form-control form-control-sm"></td>
                <td><button type="button" class="btn btn-danger btn-sm eliminar-fila-modal">X</button></td>
            `;
            tablaCuentasModal.appendChild(nuevaFila);
        });
    }

    // Acción para eliminar fila de cuenta bancaria
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

    // Abrir modal de nuevo proveedor
    const btnAgregarProveedor = document.getElementById('btn-agregar-proveedor');
    if (btnAgregarProveedor) {
        btnAgregarProveedor.addEventListener('click', function() {
            $('#modalNuevoProveedor').modal('show');
        });
    }

    // Guardar nuevo proveedor desde modal
    const btnGuardarProveedorModal = document.getElementById('btn-guardar-proveedor-modal');
    if (btnGuardarProveedorModal) {
        btnGuardarProveedorModal.addEventListener('click', function() {
            const form = document.getElementById('form-nuevo-proveedor-modal');
            
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            // Validar RUC manualmente
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
            
            // Deshabilitar botón durante el proceso
            const btnGuardar = this;
            btnGuardar.disabled = true;
            btnGuardar.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Guardando...';
            
            fetch('proveedor_nuevo_directo.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Agregar nuevo proveedor al select
                    const selectProveedor = document.getElementById('proveedor_orden');
                    const newOption = new Option(data.nombre_proveedor, data.id_proveedor, true, true);
                    selectProveedor.add(newOption);
                    
                    // Cerrar modal y limpiar formulario
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
                    text: 'No se pudo conectar con el servidor. Verifique su conexión.'
                });
            })
            .finally(() => {
                btnGuardar.disabled = false;
                btnGuardar.innerHTML = '<i class="fa fa-save"></i> Registrar';
            });
        });
    }

    // Limpiar formulario al cerrar modal
    $('#modalNuevoProveedor').on('hidden.bs.modal', function () {
        const form = document.getElementById('form-nuevo-proveedor-modal');
        if (form) {
            form.reset();
            // Limpiar tabla de cuentas dejando solo una fila
            const tablaCuentas = document.getElementById('tabla-cuentas-modal');
            if (tablaCuentas) {
                const filas = tablaCuentas.querySelectorAll('tr');
                for (let i = filas.length - 1; i > 0; i--) {
                    filas[i].remove();
                }
            }
        }
    });
    function verificarSiGenerarSalida() {
    const itemsPendientes = document.querySelectorAll('.item-pendiente');
    let tieneItems = false;
    let todosConStockCompleto = true;
    
    itemsPendientes.forEach(function(item) {
        tieneItems = true;
        
        const estadoSpan = item.querySelector('span[class*="badge"], .text-success, .text-warning, .text-primary, .text-danger');
        
        if (estadoSpan) {
            const textoEstado = estadoSpan.textContent.trim();
            
            if (!textoEstado.includes('Completo')) {
                todosConStockCompleto = false;
            }
        }
    });
    
    //Verificar el estado del pedido antes de intentar completarlo
    const estadoPedido = <?php echo $pedido['est_pedido']; ?>;
    const tieneSalidaActiva = <?php echo isset($tiene_salida_activa) && $tiene_salida_activa ? 'true' : 'false'; ?>;

    // Si ya tiene salida activa, NO mostrar mensaje
    if (tieneSalidaActiva) {
        console.log('Este pedido ya tiene una salida activa registrada');
        return;
    }

    // Si todos los items tienen stock
    if (tieneItems && todosConStockCompleto) {

        if (estadoPedido === 4) {
            Swal.fire({
                title: '¡Pedido Finalizado!',
                html: '<div style="text-align: left; padding: 10px;">' +
                    '<p style="margin-bottom: 10px;"> Este pedido está marcado como <strong style="color: #28a745;">FINALIZADO</strong>.</p>' +
                    '<p style="margin-bottom: 10px;"> Todos los items tienen stock disponible en el almacén.</p>' +
                    '<p style="margin-bottom: 0;"><strong>¿Desea generar una salida de almacén ahora?</strong></p>' +
                    '</div>',
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
            return;
        }
        // Si el pedido está APROBADO (estado = 2) o INGRESADO (estado = 3)
        if (estadoPedido === 2 || estadoPedido === 3) {
            Swal.fire({
                title: '¡Pedido Aprobado!',
                html: '<div style="text-align: left; padding: 10px;">' +
                    '<p style="margin-bottom: 10px;">✅ Este pedido está aprobado.</p>' +
                    '<p style="margin-bottom: 10px;">✅ Todos los items tienen stock disponible en el almacén.</p>' +
                    '<p style="margin-bottom: 0;"><strong>¿Desea generar una salida de almacén ahora?</strong></p>' +
                    '</div>',
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
            return;
        }
       
        // Si el pedido está PENDIENTE (estado = 1), intentar completarlo
        if (estadoPedido === 1) {
            // Mostrar loading
            Swal.fire({
                title: 'Verificando disponibilidad...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Marcar como FINALIZADO (estado 4) automáticamente
            fetch('pedido_actualizar_estado.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id_pedido=<?php echo $id_pedido; ?>&accion=completar_automatico'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '¡Stock Completo - Pedido Finalizado!',
                        html: '<div style="text-align: left; padding: 10px;">' +
                            '<p style="margin-bottom: 10px;"> Todos los items tienen stock disponible en el almacén.</p>' +
                            '<p style="margin-bottom: 10px;"> El pedido se ha marcado como <strong style="color: #28a745;">FINALIZADO</strong> (sin necesidad de orden de compra).</p>' +
                            '<p style="margin-bottom: 0;"><strong>¿Desea generar una salida de almacén ahora?</strong></p>' +
                            '</div>',
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fa fa-truck"></i> Sí, generar salida',
                        cancelButtonText: '<i class="fa fa-arrow-left"></i> Volver a pedidos',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Redirigiendo...',
                                text: 'Preparando formulario de salida',
                                icon: 'info',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            setTimeout(function() {
                                window.location.href = 'salidas_nuevo.php?desde_pedido=<?php echo $id_pedido; ?>';
                            }, 500);
                        } else {
                            window.location.href = 'pedidos_mostrar.php?success=finalizado_auto';
                        }
                    });
                } else {
                    // Mostrar error detallado
                    let errorHtml = '<div style="text-align: left;">';
                    errorHtml += '<p><strong>Error:</strong> ' + (data.message || 'Error desconocido') + '</p>';
                    
                    if (data.detalles) {
                        errorHtml += '<hr><p><strong>Detalles:</strong></p>';
                        errorHtml += '<pre style="text-align: left; font-size: 11px; max-height: 300px; overflow: auto;">' + 
                                    JSON.stringify(data.detalles, null, 2) + '</pre>';
                    }
                    
                    errorHtml += '</div>';
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al finalizar pedido',
                        html: errorHtml,
                        width: '600px',
                        confirmButtonText: 'Entendido'
                    });
                }
            })
            .catch(error => {
                console.error('Error de red:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    html: '<p>No se pudo conectar con el servidor</p>' +
                        '<p><strong>Error:</strong> ' + error.message + '</p>',
                    confirmButtonText: 'Entendido'
                });
            });
        }
    }
}

    // ====================================================================
    // FIN NUEVO: MODAL DE PROVEEDOR
    // ====================================================================
    
    function configurarEventosEdicion() {
        // Configurar eventos para items existentes en edición
        document.querySelectorAll('.precio-item').forEach(function(input) {
            input.addEventListener('input', function() {
                const idDetalle = this.closest('[id^="item-orden-"]').id.replace('item-orden-', '');
                const cantidad = this.closest('.alert').querySelector('input[name$="[cantidad]"]').value;
                actualizarSubtotalItem(idDetalle, cantidad);
                actualizarTotalOrden();
            });
        });

        // Configurar botones de remover en edición
        document.addEventListener('click', function(event) {
            const btnRemover = event.target.closest('.btn-remover-item');
            if (btnRemover && btnRemover.hasAttribute('data-id-compra-detalle')) {
                event.preventDefault();
                const idCompraDetalle = btnRemover.getAttribute('data-id-compra-detalle');
                removerItemDeOrdenEdicion(idCompraDetalle);
            }
        });
    }

    function removerItemDeOrdenEdicion(idCompraDetalle) {
        Swal.fire({
            title: '¿Está seguro?',
            text: 'Este item se eliminará de la orden',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const itemElement = document.getElementById(`item-orden-${idCompraDetalle}`);
                if (itemElement) {
                    itemElement.remove();
                    actualizarTotalOrden();
                    
                    // Mostrar mensaje de éxito
                    Swal.fire({
                        icon: 'success',
                        title: 'Item eliminado',
                        text: 'El item ha sido removido de la orden',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            }
        });
    }

    function configurarEventListeners() {
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
        
        // Botón Nueva Orden / Ver Órdenes
        const btnNuevaOrden = document.getElementById('btn-nueva-orden');
        if (btnNuevaOrden) {
            btnNuevaOrden.addEventListener('click', toggleFormularioOrden);
        }

        // Botón Editar Orden
        document.addEventListener('click', function(event) {
            const btnEditar = event.target.closest('.btn-editar-orden');
            if (btnEditar) {
                event.preventDefault();
                const idCompra = btnEditar.getAttribute('data-id-compra');
                window.location.href = `pedido_verificar.php?id=<?php echo $id_pedido; ?>&id_compra=${idCompra}`;
            }
        });

        // Listener para cambios en moneda
        const selectMoneda = document.getElementById('moneda_orden');
        if (selectMoneda) {
            selectMoneda.addEventListener('change', function() {
                actualizarEtiquetasMoneda(this.value);
                actualizarTotalOrden();
            });
        }

        // Botones agregar a orden
        document.addEventListener('click', function(event) {
            const btnAgregar = event.target.closest('.btn-agregarOrden');
            if (btnAgregar) {
                event.preventDefault();
                event.stopPropagation();
                
                const idDetalle = btnAgregar.dataset.idDetalle;
                const idProducto = btnAgregar.dataset.idProducto;
                const descripcion = btnAgregar.dataset.descripcion;
                const cantidadVerificada = btnAgregar.dataset.cantidadVerificada;
                
                // Si estamos en modo edición, asegurar que el formulario esté visible
                if (modoEditar) {
                    const contenedorNuevaOrden = document.getElementById('contenedor-nueva-orden');
                    if (contenedorNuevaOrden.style.display === 'none') {
                        contenedorNuevaOrden.style.display = 'block';
                    }
                } else {
                    const contenedorTabla = document.getElementById('contenedor-tabla-ordenes');
                    if (contenedorTabla.style.display !== 'none') {
                        mostrarFormularioNuevaOrden();
                    }
                }
                
                agregarItemAOrden({
                    idDetalle: idDetalle,
                    idProducto: idProducto,
                    descripcion: descripcion,
                    cantidadVerificada: cantidadVerificada,
                    botonOriginal: btnAgregar
                });
            }
        });

        // Modal verificar
        const verificarBtns = document.querySelectorAll('.verificar-btn');
        verificarBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const idDetalle = this.getAttribute('data-id-detalle');
                const cantidadActual = this.getAttribute('data-cantidad-actual');
                const cantidadAlmacen = parseFloat(this.getAttribute('data-cantidad-almacen'));
                const diferencia = cantidadActual - cantidadAlmacen;

                document.getElementById('id_pedido_detalle_input').value = idDetalle;
                document.getElementById('fin_cant_pedido_detalle').value = diferencia;

                $('#verificarModal').modal('show');
            });
        });

        // Botón ver detalle
        document.addEventListener('click', function(event) {
            const btnVerDetalle = event.target.closest('.btn-ver-detalle');
            if (btnVerDetalle) {
                event.preventDefault();
                event.stopPropagation();
                
                const idCompra = btnVerDetalle.getAttribute('data-id-compra');
                mostrarDetalleCompra(idCompra);
            }
        });

        // Validación del formulario
        const formNuevaOrden = document.getElementById('form-nueva-orden');
        if (formNuevaOrden) {
            formNuevaOrden.addEventListener('submit', validarFormularioOrden);
        }
    }        
    
    function toggleFormularioOrden() {
        const contenedorTabla = document.getElementById('contenedor-tabla-ordenes');
        const contenedorNuevaOrden = document.getElementById('contenedor-nueva-orden');
        
        if (contenedorTabla.style.display === 'none') {
            mostrarTablaOrdenes();
        } else {
            mostrarFormularioNuevaOrden();
        }
    }

    function mostrarFormularioNuevaOrden() {
        const contenedorTabla = document.getElementById('contenedor-tabla-ordenes');
        const contenedorNuevaOrden = document.getElementById('contenedor-nueva-orden');
        const btnNuevaOrden = document.getElementById('btn-nueva-orden');
        
        contenedorTabla.style.display = 'none';
        contenedorNuevaOrden.style.display = 'block';
        
        if (btnNuevaOrden) {
            btnNuevaOrden.innerHTML = '<i class="fa fa-table"></i> Ver Órdenes';
            btnNuevaOrden.classList.remove('btn-primary');
            btnNuevaOrden.classList.add('btn-secondary');
        }
        
        document.getElementById('fecha_orden').value = new Date().toISOString().split('T')[0];
    }

    function mostrarTablaOrdenes() {
        const contenedorTabla = document.getElementById('contenedor-tabla-ordenes');
        const contenedorNuevaOrden = document.getElementById('contenedor-nueva-orden');
        const btnNuevaOrden = document.getElementById('btn-nueva-orden');
        
        contenedorTabla.style.display = 'block';
        contenedorNuevaOrden.style.display = 'none';
        
        if (btnNuevaOrden) {
            btnNuevaOrden.innerHTML = '<i class="fa fa-plus"></i> Nueva Orden';
            btnNuevaOrden.classList.remove('btn-secondary');
            btnNuevaOrden.classList.add('btn-primary');
        }
    }

    function agregarItemAOrden(item) {
        const idMonedaSeleccionada = document.getElementById('moneda_orden').value;
        const simboloMoneda = idMonedaSeleccionada == '1' ? 'S/.' : (idMonedaSeleccionada == '2' ? 'US$' : 'S/.');
        
        const contenedorItemsOrden = document.getElementById('contenedor-items-orden');
        const itemElement = document.createElement('div');
        
        // Generar ID único para el item
        const itemId = modoEditar ? 'nuevo-' + Date.now() : item.idDetalle;
        itemElement.id = `item-orden-${itemId}`;
        itemElement.classList.add('alert', 'alert-light', 'p-2', 'mb-2');
        
        itemElement.innerHTML = `
            <input type="hidden" name="items_orden[${itemId}][id_detalle]" value="${item.idDetalle}">
            <input type="hidden" name="items_orden[${itemId}][id_producto]" value="${item.idProducto}">
            <input type="hidden" name="items_orden[${itemId}][cantidad]" value="${item.cantidadVerificada}">
            
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div style="font-size: 12px;">
                        <div class="mb-1">
                            <strong>Descripción:</strong> ${item.descripcion}
                        </div>
                        <div>
                            <strong>Cantidad:</strong> ${item.cantidadVerificada}
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <label style="font-size: 11px; font-weight: bold;">Precio Unit.:</label>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="font-size: 11px;">${simboloMoneda}</span>
                        </div>
                        <input type="number" 
                            class="form-control form-control-sm precio-item" 
                            name="items_orden[${itemId}][precio_unitario]"
                            data-id-detalle="${itemId}"
                            step="0.01" 
                            min="0"
                            placeholder="0.00"
                            style="font-size: 11px;"
                            required>
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm btn-remover-item" data-id-detalle="${itemId}">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="subtotal-item text-right" id="subtotal-${itemId}" style="font-size: 12px; font-weight: bold; color: #007bff;">
                        Subtotal: ${simboloMoneda} 0.00
                    </div>
                </div>
            </div>
        `;
        
        contenedorItemsOrden.appendChild(itemElement);
        itemsAgregadosOrden.add(itemId);
        
        if (item.botonOriginal) {
            item.botonOriginal.disabled = true;
            item.botonOriginal.innerHTML = '<i class="fa fa-check-circle"></i> Agregado';
            item.botonOriginal.classList.remove('btn-primary');
            item.botonOriginal.classList.add('btn-success');
        }
        
        const inputPrecio = itemElement.querySelector('.precio-item');
        inputPrecio.addEventListener('input', function() {
            actualizarSubtotalItem(itemId, item.cantidadVerificada);
            actualizarTotalOrden();
        });

        // Configurar botón remover
        const btnRemover = itemElement.querySelector('.btn-remover-item');
        btnRemover.addEventListener('click', function() {
            removerItemDeOrden(itemId, item.botonOriginal);
        });
    }

    function removerItemDeOrden(idDetalle, botonOriginal) {
        const itemElement = document.getElementById(`item-orden-${idDetalle}`);
        if (itemElement) {
            itemElement.remove();
        }
        itemsAgregadosOrden.delete(idDetalle);
        
        if (botonOriginal) {
            botonOriginal.disabled = false;
            botonOriginal.innerHTML = '<i class="fa fa-check"></i> Agregar a Orden';
            botonOriginal.classList.remove('btn-success');
            botonOriginal.classList.add('btn-primary');
        }
        
        actualizarTotalOrden();
    }

    function actualizarSubtotalItem(idDetalle, cantidad) {
        const inputPrecio = document.querySelector(`input[data-id-detalle="${idDetalle}"]`);
        const subtotalElement = document.getElementById(`subtotal-${idDetalle}`);
        
        if (inputPrecio && subtotalElement) {
            const precio = parseFloat(inputPrecio.value) || 0;
            const subtotal = (parseFloat(cantidad) * precio).toFixed(2);
            const simboloMoneda = obtenerSimboloMoneda();
            subtotalElement.textContent = `Subtotal: ${simboloMoneda} ${subtotal}`;
        }
    }

    function actualizarTotalOrden() {
        const contenedorItemsOrden = document.getElementById('contenedor-items-orden');
        const items = contenedorItemsOrden.querySelectorAll('[id^="item-orden-"]');
        let total = 0;
        
        items.forEach(item => {
            const cantidadInput = item.querySelector('input[name$="[cantidad]"]');
            const inputPrecio = item.querySelector('.precio-item');
            
            if (cantidadInput && inputPrecio) {
                const cantidad = parseFloat(cantidadInput.value) || 0;
                const precio = parseFloat(inputPrecio.value) || 0;
                total += cantidad * precio;
            }
        });
        
        let montoDetraccion = 0;
        let porcentajeDetraccion = 0;
        let nombreDetraccion = '';
        const checkboxDetraccion = document.querySelector('.detraccion-checkbox:checked');
        if (checkboxDetraccion) {
            porcentajeDetraccion = parseFloat(checkboxDetraccion.getAttribute('data-porcentaje')) || 0;
            nombreDetraccion = checkboxDetraccion.getAttribute('data-nombre') || '';
            montoDetraccion = (total * porcentajeDetraccion) / 100;
        }
        
        const totalConDetraccion = total - montoDetraccion;
        
        let totalElement = document.getElementById('total-orden');
        let totalInput = document.getElementById('total_orden_input');
        
        if (!totalElement && items.length > 0) {
            totalElement = document.createElement('div');
            totalElement.id = 'total-orden';
            totalElement.className = 'text-end';
            totalElement.style.fontSize = '14px';
            totalElement.style.fontWeight = 'bold';
            totalElement.style.padding = '12px';
            totalElement.style.marginTop = '10px';
            
            totalInput = document.createElement('input');
            totalInput.type = 'hidden';
            totalInput.name = 'total_orden';
            totalInput.id = 'total_orden_input';
        }
        
        if (totalElement && items.length > 0) {
            const simboloMoneda = obtenerSimboloMoneda();
            
            let html = `
            <div class="text-end" style="font-size: 15px; padding: 10px 15px; background-color: #fff; border: 1px solid #ddd; border-radius: 8px; margin-top: 10px;">
                <div class="mb-2">
                    <i class="fa fa-calculator text-secondary"></i>
                    <strong class="text-secondary"> Subtotal:</strong>
                    <span class="text-dark">${simboloMoneda} ${total.toFixed(2)}</span>
                </div>`;
            
            if (montoDetraccion > 0) {
                html += `
                <div class="mb-2">
                    <i class="fa fa-minus-circle text-secondary"></i>
                    <strong class="text-secondary"> Detracción ${nombreDetraccion} (${porcentajeDetraccion}%):</strong>
                    <span class="text-dark">-${simboloMoneda} ${montoDetraccion.toFixed(2)}</span>
                </div>

                <div style="
                    font-size: 18px; 
                    font-weight: bold; 
                    padding: 10px; 
                    background-color: #28a745; 
                    color: white; 
                    border-radius: 6px;
                    text-align: center;
                    ">
                    <i class="fa fa-money"></i> 
                    TOTAL A PAGAR: ${simboloMoneda} ${totalConDetraccion.toFixed(2)}
                </div>`;
            } else {
                html += `
                <div style="
                    font-size: 18px; 
                    font-weight: bold; 
                    padding: 10px; 
                    background-color: #28a745; 
                    color: white; 
                    border-radius: 6px;
                    text-align: center;
                    ">
                    <i class="fa fa-money"></i> 
                    TOTAL: ${simboloMoneda} ${total.toFixed(2)}
                </div>`;
            }
            
            html += `</div>`;
            
            totalElement.innerHTML = html;
            
            if (totalInput) {
                totalInput.value = total.toFixed(2);
            }
            
            if (!totalElement.parentNode) {
                contenedorItemsOrden.appendChild(totalElement);
                if (totalInput) {
                    contenedorItemsOrden.appendChild(totalInput);
                }
            }
        } else if (totalElement && items.length === 0) {
            totalElement.remove();
            if (totalInput) totalInput.remove();
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
        
        const itemsOrden = document.querySelectorAll('[id^="item-orden-"]');
        itemsOrden.forEach(item => {
            const cantidadInput = item.querySelector('input[name$="[cantidad]"]');
            if (cantidadInput) {
                const idDetalle = item.id.replace('item-orden-', '');
                const cantidad = parseFloat(cantidadInput.value);
                actualizarSubtotalItem(idDetalle, cantidad);
            }
        });
        
        actualizarTotalOrden();
    }

    function validarFormularioOrden(e) {
        const fecha = document.getElementById('fecha_orden').value;
        const proveedor = document.getElementById('proveedor_orden').value;
        const moneda = document.getElementById('moneda_orden').value;
        
        if (!fecha || !proveedor || !moneda) {
            e.preventDefault();
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
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Sin Items',
                text: 'Debe agregar al menos un ítem a la orden antes de guardar.',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Entendido'
            });
            return false;
        }
        
        return true;
    }

    // Función para anular órdenes de compra
    function AnularCompra(id_compra, id_pedido) {
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
                            Swal.fire('¡Anulado!', response.mensaje, 'success').then(() => location.reload());
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
                            Swal.fire('¡Anulado!', response.mensaje, 'success').then(() => location.reload());
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
    }

    function mostrarFormularioNuevaOrdenAuto() {
        const contenedorTabla = document.getElementById('contenedor-tabla-ordenes');
        const contenedorNuevaOrden = document.getElementById('contenedor-nueva-orden');
        const btnNuevaOrden = document.getElementById('btn-nueva-orden');
        
        if (contenedorTabla && contenedorNuevaOrden && btnNuevaOrden) {
            contenedorTabla.style.display = 'none';
            contenedorNuevaOrden.style.display = 'block';
            
            btnNuevaOrden.innerHTML = '<i class="fa fa-table"></i> Ver Órdenes';
            btnNuevaOrden.classList.remove('btn-primary');
            btnNuevaOrden.classList.add('btn-secondary');
            
            const fechaOrden = document.getElementById('fecha_orden');
            if (fechaOrden) {
                fechaOrden.value = new Date().toISOString().split('T')[0];
            }
        }
    }

    function autoAgregarItemsAOrden() {
        const itemsAutoOrden = document.querySelectorAll('.datos-auto-orden');
        let itemsAgregados = 0;
        
        itemsAutoOrden.forEach(function(item) {
            const idDetalle = item.getAttribute('data-id-detalle');
            const idProducto = item.getAttribute('data-id-producto');
            const descripcion = item.getAttribute('data-descripcion');
            const cantidad = item.getAttribute('data-cantidad');
            
            agregarItemAOrdenAutomatico({
                idDetalle: idDetalle,
                idProducto: idProducto,
                descripcion: descripcion,
                cantidadVerificada: cantidad
            });
            
            itemsAgregados++;
        });
        
        if (itemsAgregados > 0) {
            const contadorVerificados = document.getElementById('contador-verificados');
            if (contadorVerificados) {
                contadorVerificados.textContent = `(${itemsAgregados} items auto-agregados)`;
            }
        }
    }
    
    function agregarItemAOrdenAutomatico(item) {
        const contenedorItemsOrden = document.getElementById('contenedor-items-orden');
        const itemElement = document.createElement('div');
        itemElement.id = `item-orden-${item.idDetalle}`;
        itemElement.classList.add('alert', 'alert-primary', 'p-2', 'mb-2');
        itemElement.innerHTML = `
            <input type="hidden" name="items_orden[${item.idDetalle}][id_detalle]" value="${item.idDetalle}">
            <input type="hidden" name="items_orden[${item.idDetalle}][id_producto]" value="${item.idProducto}">
            <input type="hidden" name="items_orden[${item.idDetalle}][cantidad]" value="${item.cantidadVerificada}">
            
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div style="font-size: 12px;">
                        <div class="mb-1">
                            <i class="fa fa-cog text-primary"></i>
                            <strong>Descripción:</strong> ${item.descripcion}
                            <span class="badge badge-primary badge-sm ml-1">AUTO</span>
                        </div>
                        <div>
                            <strong>Cantidad:</strong> ${item.cantidadVerificada}
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <label style="font-size: 11px; font-weight: bold;">Precio Unit.:</label>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="font-size: 11px;">S/.</span>
                        </div>
                        <input type="number" 
                            class="form-control form-control-sm precio-item" 
                            name="items_orden[${item.idDetalle}][precio_unitario]"
                            data-id-detalle="${item.idDetalle}"
                            step="0.01" 
                            min="0"
                            placeholder="0.00"
                            style="font-size: 11px;"
                            required>
                    </div>
                </div>
                <div class="col-md-1">
                    <span class="badge badge-primary" title="Item agregado automáticamente" style="padding: 4px 6px;">
                        <i class="fa fa-cog"></i>
                    </span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="subtotal-item text-right" id="subtotal-${item.idDetalle}" style="font-size: 12px; font-weight: bold; color: #2196f3;">
                        Subtotal: S/. 0.00
                    </div>
                </div>
            </div>
        `;
        
        contenedorItemsOrden.appendChild(itemElement);
        
        const inputPrecio = itemElement.querySelector('.precio-item');
        inputPrecio.addEventListener('input', function() {
            actualizarSubtotalItem(item.idDetalle, item.cantidadVerificada);
            actualizarTotalOrden();
        });
    }

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
    titulo.innerHTML = `<i class="fa fa-file-text-o text-primary"></i> Orden de Compra - ORD-${compra.id_compra}`;
    
    const contenido = document.getElementById('contenido-detalle-compra');
    const fechaFormateada = new Date(compra.fec_compra).toLocaleDateString('es-PE');
    const estadoCompra = parseInt(compra.est_compra);
    
    // CORRECCIÓN: Definir TODOS los estados posibles
    let estadoTexto = 'Desconocido';
    let estadoClase = 'secondary';
    
        switch(estadoCompra) {
            case 0:
                estadoTexto = 'Anulada';
                estadoClase = 'danger';
                break;
            case 1:
                estadoTexto = 'Pendiente';
                estadoClase = 'warning';
                break;
            case 2:
                estadoTexto = 'Aprobada';
                estadoClase = 'success';
                break;
            case 3:
                estadoTexto = 'Cerrada';
                estadoClase = 'info';
                break;
            case 4:
                estadoTexto = 'Pagada';
                estadoClase = 'primary';
                break;
        }
        
        let html = `
            <div class="card mb-3">
                <div class="card-header" style="background-color: #e3f2fd; padding: 10px 15px;">
                    <h6 class="mb-0">
                        <i class="fa fa-info-circle text-primary"></i> 
                        Información General
                    </h6>
                </div>
                <div class="card-body" style="padding: 15px;">
                    <div class="row">
                        <div class="col-md-6">
                            <p style="margin: 5px 0; font-size: 13px;">
                                <strong>N° Orden:</strong> ORD-${compra.id_compra}
                            </p>
                            <p style="margin: 5px 0; font-size: 13px;">
                                <strong>Proveedor:</strong> ${compra.nom_proveedor || 'No especificado'}
                            </p>
                            <p style="margin: 5px 0; font-size: 13px;">
                                <strong>RUC:</strong> ${compra.ruc_proveedor || 'No especificado'}
                            </p>
                            <p style="margin: 5px 0; font-size: 13px;">
                                <strong>Moneda:</strong> ${compra.nom_moneda || 'No especificada'}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p style="margin: 5px 0; font-size: 13px;">
                                <strong>Fecha:</strong> ${fechaFormateada}
                            </p>
                            <p style="margin: 5px 0; font-size: 13px;">
                                <strong>Estado:</strong> 
                                <span class="badge badge-${estadoClase}">${estadoTexto}</span>
                            </p>
                            <p style="margin: 5px 0; font-size: 13px;">
                                <strong>Creado por:</strong> ${compra.nom_personal || 'No especificado'} ${compra.ape_personal || ''}
                            </p>
                            <p style="margin: 5px 0; font-size: 13px;">
                                <strong>Plazo Entrega:</strong> ${compra.plaz_compra || 'No especificado'}
                            </p>
                            ${compra.nombre_detraccion ? `
                                <p style="margin: 5px 0; font-size: 13px;">
                                    <strong>Detracción:</strong> 
                                    <span class="badge badge-warning">${compra.nombre_detraccion} (${compra.porcentaje_detraccion}%)</span>
                                </p>
                            ` : ''}
                        </div>
                    </div>
        `;
        
        if (compra.denv_compra || compra.obs_compra || compra.port_compra) {
            html += `
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="border-top pt-2">
                                ${compra.denv_compra ? `<p style="margin: 5px 0; font-size: 13px;"><strong>Dirección de Envío:</strong> ${compra.denv_compra}</p>` : ''}
                                ${compra.obs_compra ? `<p style="margin: 5px 0; font-size: 13px;"><strong>Observaciones:</strong> ${compra.obs_compra}</p>` : ''}
                                ${compra.port_compra ? `<p style="margin: 5px 0; font-size: 13px;"><strong>Tipo de Porte:</strong> ${compra.port_compra}</p>` : ''}
                            </div>
                        </div>
                    </div>
            `;
        }
        
        html += `
                </div>
            </div>
            
            <div class="card">
                <div class="card-header" style="background-color: #e8f5e8; padding: 10px 15px;">
                    <h6 class="mb-0">
                        <i class="fa fa-list-alt text-success"></i> 
                        Productos de la Orden
                    </h6>
                </div>
                <div class="card-body" style="padding: 15px;">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm" style="font-size: 12px;">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th style="width: 10%;">#</th>
                                    <th style="width: 15%;">Código</th>
                                    <th style="width: 40%;">Descripción</th>
                                    <th style="width: 10%;">Cantidad</th>
                                    <th style="width: 12%;">Precio Unit.</th>
                                    <th style="width: 13%;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
        `;
        
        let total = 0;
        const simboloMoneda = compra.sim_moneda || (compra.id_moneda == 1 ? 'S/.' : 'US$');
        
        detalles.forEach((detalle, index) => {
            const subtotal = parseFloat(detalle.cant_compra_detalle) * parseFloat(detalle.prec_compra_detalle);
            total += subtotal;
            
            html += `
                                <tr>
                                    <td style="font-weight: bold;">${index + 1}</td>
                                    <td>${detalle.cod_material || 'N/A'}</td>
                                    <td>${detalle.nom_producto}</td>
                                    <td class="text-center">${detalle.cant_compra_detalle}</td>
                                    <td class="text-right">${simboloMoneda} ${parseFloat(detalle.prec_compra_detalle).toFixed(2)}</td>
                                    <td class="text-right" style="font-weight: bold;">${simboloMoneda} ${subtotal.toFixed(2)}</td>
                                </tr>
            `;
        });
        
        html += `
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">`;

        // Calcular detracción si existe
        let montoDetraccion = 0;
        let totalFinal = total;

        if (compra.porcentaje_detraccion && compra.porcentaje_detraccion > 0) {
            montoDetraccion = (total * parseFloat(compra.porcentaje_detraccion)) / 100;
            totalFinal = total - montoDetraccion;
            
            html += `
                            <div class="alert alert-light" style="margin-bottom: 10px; padding: 10px;">
                                <div style="font-size: 14px; text-align: center; margin-bottom: 5px;">
                                    <strong>SUBTOTAL:</strong> ${simboloMoneda} ${total.toFixed(2)}
                                </div>
                                <div style="font-size: 13px; text-align: center; color: #dc3545; margin-bottom: 5px;">
                                    <i class="fa fa-minus-circle"></i> 
                                    <strong>Detracción (${compra.porcentaje_detraccion}%):</strong> 
                                    -${simboloMoneda} ${montoDetraccion.toFixed(2)}
                                </div>
                            </div>`;
        }

        html += `
                            <div class="alert alert-info text-center" style="font-size: 16px; font-weight: bold; margin: 0;">
                                <i class="fa fa-calculator"></i> 
                                ${montoDetraccion > 0 ? 'TOTAL A PAGAR' : 'TOTAL'}: ${simboloMoneda} ${totalFinal.toFixed(2)}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        contenido.innerHTML = html;
        contenido.style.display = 'block';
    }

    function mostrarErrorDetalle(mensaje) {
        const errorDiv = document.getElementById('error-detalle-compra');
        errorDiv.querySelector('p').textContent = mensaje;
        errorDiv.style.display = 'block';
    }
    // Manejar checkboxes de detracción (solo uno seleccionado a la vez)
    document.addEventListener('change', function(event) {
        if (event.target.classList.contains('detraccion-checkbox')) {
            // Desmarcar otros checkboxes
            document.querySelectorAll('.detraccion-checkbox').forEach(cb => {
                if (cb !== event.target) {
                    cb.checked = false;
                }
            });
            
            // Actualizar totales
            actualizarTotalOrden();
        }
    });
});
</script>