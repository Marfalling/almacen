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
                                    <td style="width: 20%; background-color: #f8f9fa;"><strong>Fecha del Pedido:</strong></td>
                                    <td style="width: 30%;"><?php echo date('d/m/Y H:i', strtotime($pedido['fec_pedido'])); ?></td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f8f9fa;"><strong>Nombre del Pedido:</strong></td>
                                    <td><?php echo $pedido['nom_pedido']; ?></td>
                                    <td style="background-color: #f8f9fa;"><strong>Fecha de Necesidad:</strong></td>
                                    <td><?php echo date('d/m/Y', strtotime($pedido['fec_req_pedido'])); ?></td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f8f9fa;"><strong>OT/LCL/LCA:</strong></td>
                                    <td><?php echo $pedido['ot_pedido']; ?></td>
                                    <td style="background-color: #f8f9fa;"><strong>Contacto:</strong></td>
                                    <td><?php echo $pedido['cel_pedido']; ?></td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f8f9fa;"><strong>Lugar de Entrega:</strong></td>
                                    <td colspan="3"><?php echo $pedido['lug_pedido']; ?></td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f8f9fa;"><strong>Almacén:</strong></td>
                                    <td><?php echo $pedido['nom_almacen']; ?></td>
                                    <td style="background-color: #f8f9fa;"><strong>Ubicación:</strong></td>
                                    <td><?php echo $pedido['nom_ubicacion']; ?></td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f8f9fa;"><strong>Centro de Costos:</strong></td>
                                    <td><?php echo $pedido['nom_centro_costo']; ?></td>
                                    <td style="background-color: #f8f9fa;"><strong>Solicitante:</strong></td>
                                    <td><?php echo $pedido['nom_personal']; ?></td>
                                </tr>
                                <?php if (!empty($pedido['acl_pedido'])) { ?>
                                <tr>
                                    <td style="background-color: #f8f9fa;"><strong>Aclaraciones:</strong></td>
                                    <td colspan="3"><?php echo $pedido['acl_pedido']; ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
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
                                        $detalle['cantidad_ya_ordenada'] = ObtenerCantidadYaOrdenada($id_pedido, $detalle['id_producto']);
    $detalle['cantidad_pendiente'] = ObtenerCantidadPendienteOrdenar($id_pedido, $detalle['id_producto']);
    
    // 🔹 INICIALIZAR VARIABLES PARA EVITAR WARNINGS
    $cantidad_pendiente = $detalle['cantidad_pendiente'];
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
                                    
                                    // 🔹 CAMBIO DE TU COMPAÑERA: Usar cantidad_disponible_real
                                    $esVerificado = !is_null($detalle['cant_fin_pedido_detalle']);
                                    $stockInsuficiente = $detalle['cantidad_disponible_real'] < $detalle['cant_pedido_detalle'];
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
                                    data-item="<?php echo $contador_detalle; ?>"
                                    data-id-detalle="<?php echo $detalle['id_pedido_detalle']; ?>"
                                    data-id-producto="<?php echo $detalle['id_producto']; ?>"
                                    data-cant-pedido="<?php echo number_format($detalle['cant_pedido_detalle'], 2, '.', ''); ?>"
                                    data-cant-disponible="<?php echo number_format($detalle['cantidad_disponible_real'], 2, '.', ''); ?>"
                                >
                                
                                
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
                                                                            
                                    <?php 
                                    } elseif (!$esVerificado && $stockInsuficiente && !$pedidoAnulado) { ?>
                                        <button type="button" class="btn btn-success btn-xs verificar-btn"
                                                data-id-detalle="<?php echo $detalle['id_pedido_detalle']; ?>"
                                                data-cantidad-actual="<?php echo $detalle['cant_pedido_detalle']; ?>"
                                                data-cantidad-almacen="<?php echo $detalle['cantidad_disponible_almacen']; ?>"
                                                title="Verificar Item" style="padding: 2px 8px; font-size: 11px;">
                                            Verificar
                                        </button>

                                    <?php 
                                        // CALCULAR SI HAY CANTIDAD PENDIENTE DE ORDENAR
                                        $cantidad_pendiente = ObtenerCantidadPendienteOrdenar($id_pedido, $detalle['id_producto']);
                                        $todo_ordenado = ($cantidad_pendiente <= 0);

                                    } elseif ($esVerificado && $stockInsuficiente && $detalle['est_pedido_detalle'] != 2 && !$modo_editar && !$pedidoAnulado && !$todo_ordenado) { ?>

                                        <button type="button" 
                                                class="btn btn-primary btn-xs btn-agregarOrden" 
                                                data-id-detalle="<?php echo $detalle['id_pedido_detalle']; ?>"
                                                data-id-producto="<?php echo $detalle['id_producto']; ?>"
                                                data-descripcion="<?php echo htmlspecialchars($detalle['prod_pedido_detalle']); ?>"
                                                data-cantidad-verificada="<?php echo htmlspecialchars($detalle['cant_fin_pedido_detalle']); ?>"
                                                data-cantidad-ordenada="<?php echo $detalle['cantidad_ya_ordenada']; ?>"
                                                title="Agregar a Orden" 
                                                style="padding: 2px 8px; font-size: 11px;">
                                            <i class="fa fa-check"></i> Agregar a Orden
                                        </button>

                                    <?php 
                                        // CALCULAR SI HAY CANTIDAD PENDIENTE (modo editar)
                                        $cantidad_pendiente_editar = ObtenerCantidadPendienteOrdenar($id_pedido, $detalle['id_producto']);
                                        $todo_ordenado_editar = ($cantidad_pendiente_editar <= 0);

                                    } elseif ($esVerificado && $stockInsuficiente && $detalle['est_pedido_detalle'] != 2 && $modo_editar && !$enOrdenActual && !$pedidoAnulado && !$todo_ordenado_editar) { ?>
                                        <button type="button" 
                                                class="btn btn-primary btn-xs btn-agregarOrden" 
                                                data-id-detalle="<?php echo $detalle['id_pedido_detalle']; ?>"
                                                data-id-producto="<?php echo $detalle['id_producto']; ?>"
                                                data-descripcion="<?php echo htmlspecialchars($detalle['prod_pedido_detalle']); ?>"
                                                data-cantidad-verificada="<?php echo htmlspecialchars($detalle['cant_fin_pedido_detalle']); ?>"
                                                data-cantidad-ordenada="<?php echo $detalle['cantidad_ya_ordenada']; ?>" 
                                                style="padding: 2px 8px; font-size: 11px;">
                                            <i class="fa fa-plus"></i> Agregar
                                        </button>

                                    <?php } elseif ($enOrdenActual) { ?>
                                        <span class="badge badge-info" style="font-size: 10px; padding: 2px 6px;">
                                            <i class="fa fa-check"></i> En Orden
                                        </span>
                                        
                                    <?php } elseif (isset($todo_ordenado) && $todo_ordenado) { ?>
                                        <!-- Mostrar cuando ya se ordenó todo -->
                                        <span class="badge badge-success" style="font-size: 10px; padding: 2px 6px;">
                                            <i class="fa fa-check-circle"></i> Todo Ordenado
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
                                    <strong>SST/MA/CA:</strong> <?php echo $descripcion_sst_completa; ?>
                                    
                                    <?php if ($esVerificado) { ?>
                                        <span style="margin: 0 8px;">|</span>
                                        <strong>Cant. Verificada:</strong> <?php echo $detalle['cant_fin_pedido_detalle']; ?>
                                        <?php 
                                        // Mostrar cantidad ya ordenada si existe
                                        if (isset($detalle['cantidad_ya_ordenada']) && $detalle['cantidad_ya_ordenada'] > 0) { ?>
                                            <span style="margin: 0 8px;">|</span>
                                            <strong style="color: #28a745;">Ya Ordenado:</strong> <?php echo $detalle['cantidad_ya_ordenada']; ?>
                                        <?php } 
                                        // Mostrar cantidad pendiente
                                        if (isset($detalle['cantidad_pendiente']) && $detalle['cantidad_pendiente'] > 0) { ?>
                                            <span style="margin: 0 8px;">|</span>
                                            <strong style="color: #ffc107;">Pendiente:</strong> <?php echo $detalle['cantidad_pendiente']; ?>
                                        <?php } ?>
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
                                <?php
                                // Verificar si hay items disponibles para agregar a orden
                                $tiene_items_disponibles = false;
                                foreach ($pedido_detalle as $detalle) {
                                    $esVerificado = !is_null($detalle['cant_fin_pedido_detalle']);
                                    $stockInsuficiente = $detalle['cantidad_disponible_almacen'] < $detalle['cant_pedido_detalle'];
                                    $esAutoOrden = ($pedido['id_producto_tipo'] == 2);
                                    $estaCerrado = ($detalle['est_pedido_detalle'] == 2);
                                    
                                    // Si es auto-orden, verificado con stock insuficiente, y no está cerrado
                                    if (($esAutoOrden || ($esVerificado && $stockInsuficiente)) && !$estaCerrado) {
                                        $tiene_items_disponibles = true;
                                        break;
                                    }
                                }
                                ?>
                                
                                <?php if ($tiene_items_disponibles): ?>
                                    <button type="button" class="btn btn-primary btn-sm" id="btn-nueva-orden" style="padding: 4px 8px; font-size: 12px;">
                                        <i class="fa fa-plus"></i> Nueva Orden
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-secondary btn-sm" disabled title="No hay items disponibles para agregar" style="padding: 4px 8px; font-size: 12px;">
                                        <i class="fa fa-ban"></i> Nueva Orden
                                    </button>
                                <?php endif; ?>
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
        <!-- Botón Editar HABILITADO -->
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
        } elseif ($compra['est_compra'] == 4) {
            $mensaje = "No se puede editar - Orden pagada";
        } elseif ($tiene_alguna_aprobacion) {
            $mensaje = "No se puede editar - Orden con aprobación iniciada";
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

    <!-- Botón Registrar Pago -->
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
    <?php 
    // Solo se puede anular si NO está anulada Y NO tiene aprobaciones
    $puede_anular = ($compra['est_compra'] != 0 && !$tiene_alguna_aprobacion);
    
    if ($puede_anular) { ?>
        <!-- Botón anular HABILITADO -->
        <button class="btn btn-danger btn-xs ml-1 btn-anular-orden" 
                title="Anular Orden"
                data-id-compra="<?php echo $compra['id_compra']; ?>"
                data-id-pedido="<?php echo $id_pedido; ?>">
            <i class="fa fa-times"></i>
        </button>
    <?php } else { 
        // Determinar el mensaje según el estado
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
        <!-- Botón anular DESHABILITADO -->
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
                                        <!-- SECCIÓN DE DETRACCIÓN, RETENCIÓN Y PERCEPCIÓN -->
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <div class="card" style="border: 1px solid #dee2e6;">
                                                    <div class="card-header" style="background-color: #f8f9fa; padding: 8px 12px;">
                                                        <h6 class="mb-0" style="font-size: 13px;">
                                                            <i class="fa fa-percent text-info"></i> Detracción, Retención y Percepción (Opcional)
                                                        </h6>
                                                    </div>
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
                                                            <small class="form-text text-muted">Se aplica sobre el subtotal antes de IGV</small>
                                                        </div>
                                                        <!-- RETENCIÓN -->
                                                        <div class="mb-3">
                                                            <label style="font-size: 11px; font-weight: bold;">Retención:</label>
                                                            <div id="contenedor-retenciones" style="padding: 8px; background-color: #e7f3ff; border-radius: 4px; border: 1px solid #2196f3;">
                                                                <?php
                                                                $retenciones = ObtenerDetraccionesPorTipo('RETENCION'); // ← CAMBIO AQUÍ
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
                                                                $percepciones = ObtenerDetraccionesPorTipo('PERCEPCION'); // ← CAMBIO AQUÍ
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
                                
                                <div id="contenedor-items-orden" class="mb-3">
                                    <?php if ($modo_editar && !empty($orden_detalle)): ?>
                                        <?php foreach ($orden_detalle as $item): 
                                            //  OBTENER DATOS DE VALIDACIÓN PARA ESTE PRODUCTO
                                            $cantidad_verificada_item = 0;
                                            $cantidad_ordenada_item = 0;
                                            
                                            foreach ($pedido_detalle as $detalle) {
                                                if ($detalle['id_producto'] == $item['id_producto']) {
                                                    $cantidad_verificada_item = isset($detalle['cant_fin_pedido_detalle']) ? $detalle['cant_fin_pedido_detalle'] : 0;
                                                    $cantidad_ordenada_item = isset($detalle['cantidad_ya_ordenada']) ? $detalle['cantidad_ya_ordenada'] : 0;
                                                    break;
                                                }
                                            }
                                        ?>
                                        <div class="alert alert-light p-2 mb-2" id="item-orden-<?php echo $item['id_compra_detalle']; ?>">
                                            <!-- Inputs hidden -->
                                            <input type="hidden" name="items_orden[<?php echo $item['id_compra_detalle']; ?>][id_compra_detalle]" value="<?php echo $item['id_compra_detalle']; ?>">
                                            <input type="hidden" name="items_orden[<?php echo $item['id_compra_detalle']; ?>][id_producto]" value="<?php echo $item['id_producto']; ?>">
                                            <input type="hidden" name="items_orden[<?php echo $item['id_compra_detalle']; ?>][es_nuevo]" value="0">
                                            
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
    const urlActual = window.location.pathname;
    const esVistaVerificar = urlActual.includes('pedido_verificar.php');
    
    if (!esVistaVerificar) {
        console.log('Script de verificación omitido - no estamos en pedido_verificar.php');
        return;
    }
    
    const esAutoOrden = <?php echo ($pedido['id_producto_tipo'] == 2) ? 'true' : 'false'; ?>;
    const pedidoAnulado = <?php echo ($pedido['est_pedido'] == 0) ? 'true' : 'false'; ?>;
    const modoEditar = <?php echo $modo_editar ? 'true' : 'false'; ?>;
    let itemsAgregadosOrden = new Set();
    // ============================================
    // INICIALIZACIÓN
    // ============================================
    if (!esAutoOrden && !pedidoAnulado && !modoEditar) {
        setTimeout(verificarSiGenerarSalida, 1000);
    }
    
    if (esAutoOrden && !modoEditar) {
        setTimeout(function() {
            mostrarFormularioNuevaOrdenAuto();
            autoAgregarItemsAOrden();
        }, 800);
    }
    if (modoEditar) {
        configurarEventosEdicion();
    }
    configurarEventListeners();
    configurarModalProveedor();
    configurarValidacionTiempoReal();
    configurarExclusividadCheckboxes();
    
    // ============================================
    // FUNCIÓN PARA RECALCULAR ESTADO DE ITEMS DESPUÉS DE EDICIÓN
    // ============================================
    function recalcularEstadoItems() {
        const itemsPendientes = document.querySelectorAll('.item-pendiente');
        let tieneItemsDisponibles = false;
        
        itemsPendientes.forEach(function(item) {
            const idProducto = item.getAttribute('data-id-producto');
            const esAutoOrden = item.querySelector('.datos-auto-orden') !== null;
            const estaCerrado = item.querySelector('.badge-danger') !== null && 
                            item.querySelector('.badge-danger').textContent.includes('Cerrado');
            
            // Verificar si tiene badge "Todo Ordenado"
            const badgeTodoOrdenado = item.querySelector('.badge-success');
            const tieneTodoOrdenado = badgeTodoOrdenado && 
                                    badgeTodoOrdenado.textContent.includes('Todo Ordenado');
            
            // Si NO está todo ordenado y NO está cerrado -> hay items disponibles
            if (!tieneTodoOrdenado && !estaCerrado) {
                tieneItemsDisponibles = true;
            }
        });
        
        // Actualizar botón "Nueva Orden"
        const btnNuevaOrden = document.getElementById('btn-nueva-orden');
        if (btnNuevaOrden && !modoEditar) {
            if (tieneItemsDisponibles) {
                btnNuevaOrden.disabled = false;
                btnNuevaOrden.classList.remove('btn-secondary');
                btnNuevaOrden.classList.add('btn-primary');
                btnNuevaOrden.title = '';
                btnNuevaOrden.innerHTML = '<i class="fa fa-plus"></i> Nueva Orden';
            } else {
                btnNuevaOrden.disabled = true;
                btnNuevaOrden.classList.remove('btn-primary');
                btnNuevaOrden.classList.add('btn-secondary');
                btnNuevaOrden.title = 'No hay items disponibles para agregar';
                btnNuevaOrden.innerHTML = '<i class="fa fa-ban"></i> Nueva Orden';
            }
        }
    }

    // ============================================
    // EJECUTAR AL CARGAR Y DESPUÉS DE ACCIONES
    // ============================================
    // Ejecutar al cargar la página
    if (!modoEditar) {
        setTimeout(recalcularEstadoItems, 500);
    }
    // ============================================
    // FUNCIONES DE VERIFICACIÓN DE SALIDA
    // ============================================
    function verificarSiGenerarSalida() {
        const itemsPendientes = document.querySelectorAll('.item-pendiente');
        let tieneItems = false;
        let todosConStockCompleto = true;
        
        itemsPendientes.forEach(function(item) {
            tieneItems = true;

            // CAMBIO DE TU COMPAÑERA: Nuevo cálculo real basado en cantidades del backend
            const cantPedido = parseFloat(item.getAttribute('data-cant-pedido')) || 0;
            const cantDisponible = parseFloat(item.getAttribute('data-cant-disponible')) || 0;

            if (cantDisponible < cantPedido) {
                todosConStockCompleto = false;
            }

            /*const estadoSpan = item.querySelector('span[class*="badge"], .text-success, .text-warning, .text-primary, .text-danger');
            if (estadoSpan && !estadoSpan.textContent.trim().includes('Completo')) {
                todosConStockCompleto = false;
            }*/
        });
        
        const estadoPedido = <?php echo $pedido['est_pedido']; ?>;
        const tieneSalidaActiva = <?php echo isset($tiene_salida_activa) && $tiene_salida_activa ? 'true' : 'false'; ?>;

        if (tieneSalidaActiva) {
            console.log('Este pedido ya tiene una salida activa registrada');
            return;
        }

        if (tieneItems && todosConStockCompleto) {
            if (estadoPedido === 5) {
                mostrarAlertaPedidoFinalizado();
            } else if (estadoPedido === 3 || estadoPedido === 4) {
                mostrarAlertaPedidoAprobado(estadoPedido);
            } else if (estadoPedido === 2) {
                mostrarAlertaPedidoCompletado();
            } else if (estadoPedido === 1) {
                completarPedidoAutomaticamente();
            }
        }
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
    // ============================================
    // VALIDACIÓN Y OTRAS FUNCIONES
    // ============================================
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
    
    // NUEVA VALIDACIÓN: Verificar cantidades antes de enviar
    const erroresValidacion = validarCantidadesCliente(itemsOrden);
    
        if (erroresValidacion.length > 0) {
            e.preventDefault();
            
            // Construir mensaje HTML con todos los errores
            let mensajeHTML = '<div style="text-align: left; padding: 10px;">' +
                            '<p style="margin-bottom: 10px;"><strong>No se puede guardar la orden:</strong></p>' +
                            '<ul style="color: #dc3545; font-size: 13px; margin-left: 20px;">';
            
            erroresValidacion.forEach(error => {
                mensajeHTML += `<li style="margin-bottom: 8px;">${error}</li>`;
            });
            
            mensajeHTML += '</ul></div>';
            
            Swal.fire({
                icon: 'error',
                title: ' Cantidad No Permitida',
                html: mensajeHTML,
                confirmButtonColor: '#d33',
                confirmButtonText: '<i class="fa fa-times"></i> Entendido',
                allowOutsideClick: false
            });
            
            return false;
        }
        
        return true;
    }
    //  NUEVA FUNCIÓN: Validar cantidades en el cliente
    function validarCantidadesCliente(itemsOrden) {
    const errores = [];
    const inputIdCompra = document.querySelector('input[name="id_compra"]');
    const idCompraActual = inputIdCompra ? parseInt(inputIdCompra.value) : null;
    
    console.log(' === VALIDACIÓN CLIENTE ===');
    console.log('ID Compra actual:', idCompraActual);
    console.log('Modo editar:', modoEditar);
    
    itemsOrden.forEach(itemElement => {
        const idProductoInput = itemElement.querySelector('input[name*="[id_producto]"]');
        const cantidadInput = itemElement.querySelector('.cantidad-item');
        const esNuevoInput = itemElement.querySelector('input[name*="[es_nuevo]"]');
        
        if (!idProductoInput || !cantidadInput) return;
        
        const idProducto = parseInt(idProductoInput.value);
        const cantidadNueva = parseFloat(cantidadInput.value) || 0;
        const esNuevo = esNuevoInput && esNuevoInput.value === '1';
        
        let cantidadVerificada = 0;
        let cantidadOrdenada = 0;
        let descripcionProducto = '';
        
        console.log(` Validando Producto ID ${idProducto}, Cantidad: ${cantidadNueva}, Es nuevo: ${esNuevo}`);
        
        // Leer correctamente los atributos data
        if (cantidadInput.hasAttribute('data-cantidad-verificada') && cantidadInput.hasAttribute('data-cantidad-ordenada')) {
            cantidadVerificada = parseFloat(cantidadInput.getAttribute('data-cantidad-verificada')) || 0;
            cantidadOrdenada = parseFloat(cantidadInput.getAttribute('data-cantidad-ordenada')) || 0;
            
            console.log(`   Datos obtenidos del input (modo edición):`);
            console.log(`     Verificada: ${cantidadVerificada}, Ordenada: ${cantidadOrdenada}`);
            
            // Obtener descripción del item
            const rowElement = cantidadInput.closest('[id^="item-orden-"]');
            if (rowElement) {
                const descripcionElement = rowElement.querySelector('strong');
                if (descripcionElement && descripcionElement.nextSibling) {
                    descripcionProducto = descripcionElement.nextSibling.textContent.trim();
                }
            }
        } else {
            //Buscar en botones "Agregar a Orden" (modo creación o items nuevos)
            const botonesAgregar = document.querySelectorAll('.btn-agregarOrden');
            botonesAgregar.forEach(btn => {
                if (parseInt(btn.dataset.idProducto) === idProducto) {
                    cantidadVerificada = parseFloat(btn.dataset.cantidadVerificada) || 0;
                    cantidadOrdenada = parseFloat(btn.dataset.cantidadOrdenada) || 0;
                    descripcionProducto = btn.dataset.descripcion || `Producto ID ${idProducto}`;
                    
                    console.log(`   Datos obtenidos de botón Agregar:`);
                    console.log(`     Verificada: ${cantidadVerificada}, Ordenada: ${cantidadOrdenada}`);
                }
            });
            
            // Si no encontramos, buscar en items pendientes (auto-orden)
            if (cantidadVerificada === 0) {
                const itemsPendientes = document.querySelectorAll('.item-pendiente');
                itemsPendientes.forEach(itemPendiente => {
                    const datosAutoOrden = itemPendiente.querySelector('.datos-auto-orden');
                    if (datosAutoOrden && parseInt(datosAutoOrden.dataset.idProducto) === idProducto) {
                        cantidadVerificada = parseFloat(datosAutoOrden.dataset.cantidad) || 0;
                        descripcionProducto = datosAutoOrden.dataset.descripcion || `Producto ID ${idProducto}`;
                        
                        console.log(`   Datos obtenidos de auto-orden:`);
                        console.log(`     Verificada: ${cantidadVerificada}`);
                    }
                });
            }
        }
        
        //CALCULAR DISPONIBLE
        let cantidadDisponible = 0;
        
        if (esNuevo) {
            // Item nuevo en modo edición o creación
            cantidadDisponible = cantidadVerificada - cantidadOrdenada;
            console.log(`   [NUEVO] Disponible = ${cantidadVerificada} - ${cantidadOrdenada} = ${cantidadDisponible}`);
        } else if (modoEditar && idCompraActual) {
            // Item existente en modo edición: usar TODO lo verificado
            cantidadDisponible = cantidadVerificada;
            console.log(`   [EDITANDO] Disponible = ${cantidadVerificada} (sin restar ordenado)`);
        } else {
            cantidadDisponible = cantidadVerificada;
            console.log(`   [OTRO] Disponible = ${cantidadVerificada}`);
        }
        
        // Validar que no exceda lo disponible
        if (cantidadNueva > cantidadDisponible) {
            const descripcionCorta = descripcionProducto.length > 50 
                ? descripcionProducto.substring(0, 50) + '...' 
                : descripcionProducto;
            
            const tipoItem = esNuevo ? '[NUEVO]' : '[EDITANDO]';
            
            const error = `<strong>${tipoItem} ${descripcionCorta}:</strong><br>` +
                `Cantidad ingresada: <strong>${cantidadNueva}</strong><br>` +
                `Verificado: ${cantidadVerificada} | ` +
                `<strong style="color: #28a745;">Disponible: ${cantidadDisponible.toFixed(2)}</strong>`;
            
            console.log(`   ERROR: ${error}`);
            errores.push(error);
        } else {
            console.log(`   Validación OK`);
        }
    });
    
    console.log(` Total errores encontrados: ${errores.length}`);
    return errores;
}
    //  VALIDACIÓN EN TIEMPO REAL: Alertar cuando se excede la cantidad
    //  VALIDACIÓN EN TIEMPO REAL: Alertar cuando se excede la cantidad
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
                
                // Leer correctamente los atributos data
                if (cantidadInput.hasAttribute('data-cantidad-verificada') && cantidadInput.hasAttribute('data-cantidad-ordenada')) {
                    cantidadVerificada = parseFloat(cantidadInput.getAttribute('data-cantidad-verificada')) || 0;
                    cantidadOrdenada = parseFloat(cantidadInput.getAttribute('data-cantidad-ordenada')) || 0;
                } else {
                    // 🔹 SI NO: Buscar en botones "Agregar a Orden" (modo creación)
                    const botonesAgregar = document.querySelectorAll('.btn-agregarOrden');
                    botonesAgregar.forEach(btn => {
                        if (parseInt(btn.dataset.idProducto) === idProducto) {
                            cantidadVerificada = parseFloat(btn.dataset.cantidadVerificada) || 0;
                            cantidadOrdenada = parseFloat(btn.dataset.cantidadOrdenada) || 0;
                        }
                    });
                    
                    // Si no encontramos, buscar en items pendientes (auto-orden)
                    if (cantidadVerificada === 0) {
                        const itemsPendientes = document.querySelectorAll('.item-pendiente');
                        itemsPendientes.forEach(itemPendiente => {
                            const datosAutoOrden = itemPendiente.querySelector('.datos-auto-orden');
                            if (datosAutoOrden && parseInt(datosAutoOrden.dataset.idProducto) === idProducto) {
                                cantidadVerificada = parseFloat(datosAutoOrden.dataset.cantidad) || 0;
                            }
                        });
                    }
                }
                
                // 🔹 CALCULAR MÁXIMO PERMITIDO SEGÚN MODO
                let cantidadMaxima = 0;
                
                if (esNuevo) {
                    cantidadMaxima = cantidadVerificada - cantidadOrdenada;
                } else if (modoEditar && idCompraActual) {
                    cantidadMaxima = cantidadVerificada;
                } else {
                    cantidadMaxima = cantidadVerificada;
                }
                
                // Cambiar color del input según validación
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
                    tooltip.textContent = ` Excede máximo: ${cantidadMaxima.toFixed(2)}`;
                } else {
                    cantidadInput.style.borderColor = '#28a745';
                    cantidadInput.style.backgroundColor = '#d4edda';
                    
                    const tooltip = itemElement.querySelector('.tooltip-error-cantidad');
                    if (tooltip) tooltip.remove();
                }
            }
        });
    }
    // ============================================
    // CONFIGURACIÓN DE EVENTOS
    // ============================================
    function configurarEventosEdicion() {
        document.querySelectorAll('[id^="item-orden-"]').forEach(function(item) {
            const cantidadInput = item.querySelector('.cantidad-item');
            const precioInput = item.querySelector('.precio-item');
            const igvInput = item.querySelector('.igv-item');
            const idDetalle = item.id.replace('item-orden-', '');
            
            //  GUARDAR CANTIDAD ORIGINAL para validación
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
        
        calcularTotales(); //  Calcular al cargar
    });
    
    //Calcular total general inicial con detracción/retención/percepción
    setTimeout(function() {
        actualizarTotalGeneral();
    }, 100);
}
    function configurarEventListeners() {
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
        
        // Nueva orden
        const btnNuevaOrden = document.getElementById('btn-nueva-orden');
        if (btnNuevaOrden) {
            btnNuevaOrden.addEventListener('click', toggleFormularioOrden);
        }
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
        // Agregar a orden
        document.addEventListener('click', function(event) {
            const btnAgregar = event.target.closest('.btn-agregarOrden');
            if (btnAgregar) {
                event.preventDefault();
                event.stopPropagation();
                
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
                    idDetalle: btnAgregar.dataset.idDetalle,
                    idProducto: btnAgregar.dataset.idProducto,
                    descripcion: btnAgregar.dataset.descripcion,
                    cantidadVerificada: btnAgregar.dataset.cantidadVerificada,
                    cantidadOrdenada: btnAgregar.dataset.cantidadOrdenada || '0',
                    botonOriginal: btnAgregar
                });
            }
        });
        // Verificar modal
        document.querySelectorAll('.verificar-btn').forEach(btn => {
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
        // Ver detalle
        document.addEventListener('click', function(event) {
            const btnVerDetalle = event.target.closest('.btn-ver-detalle');
            if (btnVerDetalle) {
                event.preventDefault();
                event.stopPropagation();
                const idCompra = btnVerDetalle.getAttribute('data-id-compra');
                mostrarDetalleCompra(idCompra);
            }
        });
        // Validar formulario
        const formNuevaOrden = document.getElementById('form-nueva-orden');
        if (formNuevaOrden) {
            formNuevaOrden.addEventListener('submit', validarFormularioOrden);
          }
}
        function configurarExclusividadCheckboxes() {
        document.addEventListener('change', function(event) {
            //  DETRACCIÓN: Desmarcar RETENCIÓN y PERCEPCIÓN
            if (event.target.classList.contains('detraccion-checkbox')) {
                if (event.target.checked) {
                    document.querySelectorAll('.retencion-checkbox').forEach(cb => cb.checked = false);
                    document.querySelectorAll('.percepcion-checkbox').forEach(cb => cb.checked = false);
                }
                // Desmarcar otros checkboxes de DETRACCIÓN
                document.querySelectorAll('.detraccion-checkbox').forEach(cb => {
                    if (cb !== event.target) cb.checked = false;
                });
                actualizarTotalGeneral();
            }
            
            //  RETENCIÓN: Desmarcar DETRACCIÓN y PERCEPCIÓN
            if (event.target.classList.contains('retencion-checkbox')) {
                if (event.target.checked) {
                    document.querySelectorAll('.detraccion-checkbox').forEach(cb => cb.checked = false);
                    document.querySelectorAll('.percepcion-checkbox').forEach(cb => cb.checked = false);
                }
                // Desmarcar otros checkboxes de RETENCIÓN
                document.querySelectorAll('.retencion-checkbox').forEach(cb => {
                    if (cb !== event.target) cb.checked = false;
                });
                actualizarTotalGeneral();
            }
            
            //  PERCEPCIÓN: Desmarcar DETRACCIÓN y RETENCIÓN
            if (event.target.classList.contains('percepcion-checkbox')) {
                if (event.target.checked) {
                    document.querySelectorAll('.detraccion-checkbox').forEach(cb => cb.checked = false);
                    document.querySelectorAll('.retencion-checkbox').forEach(cb => cb.checked = false);
                }
                // Desmarcar otros checkboxes de PERCEPCIÓN
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
        const btnAgregarModal = document.getElementById("agregarCuentaModal");
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
    // ============================================
    // FUNCIONES DE ORDEN
    // ============================================
    function toggleFormularioOrden() {
        const contenedorTabla = document.getElementById('contenedor-tabla-ordenes');
        if (contenedorTabla.style.display === 'none') {
            mostrarTablaOrdenes();
        } else {
            mostrarFormularioNuevaOrden();
        }
    }
    function mostrarFormularioNuevaOrden() {
        document.getElementById('contenedor-tabla-ordenes').style.display = 'none';
        document.getElementById('contenedor-nueva-orden').style.display = 'block';
        
        const btnNuevaOrden = document.getElementById('btn-nueva-orden');
        if (btnNuevaOrden) {
            btnNuevaOrden.innerHTML = '<i class="fa fa-table"></i> Ver Órdenes';
            btnNuevaOrden.classList.remove('btn-primary');
            btnNuevaOrden.classList.add('btn-secondary');
        }
        
        document.getElementById('fecha_orden').value = new Date().toISOString().split('T')[0];
    }
    function mostrarTablaOrdenes() {
        document.getElementById('contenedor-tabla-ordenes').style.display = 'block';
        document.getElementById('contenedor-nueva-orden').style.display = 'none';
        
        const btnNuevaOrden = document.getElementById('btn-nueva-orden');
        if (btnNuevaOrden) {
            btnNuevaOrden.innerHTML = '<i class="fa fa-plus"></i> Nueva Orden';
            btnNuevaOrden.classList.remove('btn-secondary');
            btnNuevaOrden.classList.add('btn-primary');
        }
    }
    function agregarItemAOrden(item) {
    const idMonedaSeleccionada = document.getElementById('moneda_orden').value;
    const simboloMoneda = idMonedaSeleccionada == '1' ? 'S/.' : (idMonedaSeleccionada == '2' ? 'US$' : 'S/.');
    
    const itemId = 'nuevo-' + Date.now();
    const cantidadVerificada = parseFloat(item.cantidadVerificada) || 0;
    const cantidadOrdenada = parseFloat(item.cantidadOrdenada) || 0;
    const cantidadPendiente = cantidadVerificada - cantidadOrdenada;
    
    const itemElement = document.createElement('div');
    itemElement.id = `item-orden-${itemId}`;
    itemElement.classList.add('alert', 'alert-light', 'p-2', 'mb-2');
    
    itemElement.innerHTML = `
    <input type="hidden" name="items_orden[${itemId}][id_detalle]" value="${item.idDetalle}">
    <input type="hidden" name="items_orden[${itemId}][id_pedido_detalle]" value="${item.idDetalle}">
    <input type="hidden" name="items_orden[${itemId}][id_producto]" value="${item.idProducto}">
    <input type="hidden" name="items_orden[${itemId}][es_nuevo]" value="1">
    
    <div class="row align-items-center mb-2">
        <div class="col-md-11">
            <div style="font-size: 12px;">
                <strong>Descripción:</strong> ${item.descripcion}
                ${modoEditar ? '<span class="badge badge-info badge-sm ml-1">NUEVO</span>' : ''}
            </div>
            <small class="text-muted" style="font-size: 11px;">
                Verificado: ${cantidadVerificada} | Ya ordenado: ${cantidadOrdenada} | 
                <strong class="text-warning">Pendiente: ${cantidadPendiente}</strong>
            </small>
        </div>
        <div class="col-md-1 text-right">
            <button type="button" class="btn btn-danger btn-sm btn-remover-item" data-id-detalle="${itemId}">
                <i class="fa fa-trash"></i>
            </button>
        </div>
    </div>
    
    <div class="row">
        <!-- Cantidad -->
        <div class="col-md-2">
            <label style="font-size: 11px; font-weight: bold; margin-bottom: 4px; display: block;">Cantidad:</label>
            <input type="number" class="form-control form-control-sm cantidad-item" 
                name="items_orden[${itemId}][cantidad]" data-id-detalle="${itemId}"
                value="${cantidadPendiente}" min="0.01" max="${cantidadPendiente}" step="0.01"
                style="font-size: 12px;" required>
            <small class="text-info" style="font-size: 10px;">Máx: ${cantidadPendiente}</small>
        </div>
        
        <!-- Precio Unitario -->
        <div class="col-md-2">
            <label style="font-size: 11px; font-weight: bold; margin-bottom: 4px; display: block;">Precio Unit.:</label>
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <span class="input-group-text" style="font-size: 11px; background-color: #f8f9fa; border: 1px solid #ced4da;">${simboloMoneda}</span>
                </div>
                <input type="number" class="form-control form-control-sm precio-item" 
                    name="items_orden[${itemId}][precio_unitario]" data-id-detalle="${itemId}"
                    step="0.01" min="0" placeholder="0.00" style="font-size: 11px;" required>
            </div>
        </div>
        
        <!-- IGV (%) -->
        <div class="col-md-2">
            <label style="font-size: 11px; font-weight: bold; margin-bottom: 4px; display: block;">IGV (%):</label>
            <input type="number" class="form-control form-control-sm igv-item" 
                name="items_orden[${itemId}][igv]" data-id-detalle="${itemId}"
                value="18" min="0" max="100" step="0.01" style="font-size: 12px;" required>
        </div>
        
        <!-- Homologación -->
        <div class="col-md-3">
            <label style="font-size: 11px; font-weight: bold; margin-bottom: 4px; display: block;">Homologación:</label>
            <input type="file" class="form-control-file" name="homologacion[${item.idDetalle}]"
                accept=".pdf,.jpg,.jpeg,.png" style="font-size: 11px; padding-top: 4px;">
            <small class="text-muted" style="font-size: 10px;">PDF, JPG, PNG</small>
        </div>
        
        <!-- Cálculos -->
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
    
        
        const contenedorItemsOrden = document.getElementById('contenedor-items-orden');
        const totalElement = document.getElementById('total-orden');
        if (totalElement) {
            contenedorItemsOrden.insertBefore(itemElement, totalElement);
        } else {
            contenedorItemsOrden.appendChild(itemElement);
        }
        
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
        
        actualizarTotalGeneral();
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
                    actualizarTotalGeneral();
                    
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
    // ============================================
    // FUNCIONES DE CÁLCULO
    // ============================================
    function actualizarTotalGeneral() {
    const items = document.querySelectorAll('[id^="item-orden-"]');
    let subtotalGeneral = 0;
    let totalIgv = 0;
    
    // Calcular subtotal e IGV de todos los items
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
    
    // Calcular TOTAL CON IGV primero
    const totalConIgv = subtotalGeneral + totalIgv;
    
    // ========================================
    // NUEVA LÓGICA: Solo UNA opción activa
    // ========================================
    
    let tipoDescuentoCargo = null;
    let porcentaje = 0;
    let nombreConcepto = '';
    let montoAfectacion = 0;
    
    // Verificar cuál checkbox está marcado
    const checkboxDetraccion = document.querySelector('.detraccion-checkbox:checked');
    const checkboxRetencion = document.querySelector('.retencion-checkbox:checked');
    const checkboxPercepcion = document.querySelector('.percepcion-checkbox:checked');
    
    if (checkboxDetraccion) {
        tipoDescuentoCargo = 'DETRACCION';
        porcentaje = parseFloat(checkboxDetraccion.getAttribute('data-porcentaje')) || 0;
        nombreConcepto = checkboxDetraccion.getAttribute('data-nombre') || '';
        // 🔹 DETRACCIÓN: Se aplica sobre el TOTAL CON IGV
        montoAfectacion = (totalConIgv * porcentaje) / 100;
    } else if (checkboxRetencion) {
        tipoDescuentoCargo = 'RETENCION';
        porcentaje = parseFloat(checkboxRetencion.getAttribute('data-porcentaje')) || 0;
        nombreConcepto = checkboxRetencion.getAttribute('data-nombre') || '';
        // 🔹 RETENCIÓN: Se aplica sobre el TOTAL CON IGV
        montoAfectacion = (totalConIgv * porcentaje) / 100;
    } else if (checkboxPercepcion) {
        tipoDescuentoCargo = 'PERCEPCION';
        porcentaje = parseFloat(checkboxPercepcion.getAttribute('data-porcentaje')) || 0;
        nombreConcepto = checkboxPercepcion.getAttribute('data-nombre') || '';
        // 🔹 PERCEPCIÓN: Se aplica sobre el TOTAL CON IGV
        montoAfectacion = (totalConIgv * porcentaje) / 100;
    }
    
    // ========================================
    // CALCULAR TOTAL FINAL
    // ========================================
    
    let totalFinal = 0;
    
    if (tipoDescuentoCargo === 'DETRACCION') {
        // Detracción se RESTA del total con IGV
        totalFinal = totalConIgv - montoAfectacion;
    } else if (tipoDescuentoCargo === 'RETENCION') {
        // Retención se RESTA del total con IGV
        totalFinal = totalConIgv - montoAfectacion;
    } else if (tipoDescuentoCargo === 'PERCEPCION') {
        // Percepción se SUMA al total con IGV
        totalFinal = totalConIgv + montoAfectacion;
    } else {
        // Sin afectación
        totalFinal = totalConIgv;
    }
    
    // ========================================
    // MOSTRAR RESUMEN
    // ========================================
    
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
                <!-- SUBTOTAL -->
                <div class="mb-2">
                    <i class="fa fa-calculator text-secondary"></i>
                    <strong class="text-secondary"> Subtotal:</strong>
                    <span class="text-dark">${simboloMoneda} ${subtotalGeneral.toFixed(2)}</span>
                </div>
                
                <!-- IGV TOTAL -->
                <div class="mb-2">
                    <i class="fa fa-percent text-secondary"></i>
                    <strong class="text-secondary"> IGV Total:</strong>
                    <span class="text-dark">${simboloMoneda} ${totalIgv.toFixed(2)}</span>
                </div>
                
                <!-- TOTAL CON IGV -->
                <div class="mb-2" style="font-weight: bold; font-size: 16px; padding: 5px; background-color: #e3f2fd; border-radius: 4px;">
                    <i class="fa fa-calculator text-primary"></i>
                    <strong class="text-primary"> Total con IGV:</strong>
                    <span class="text-primary">${simboloMoneda} ${totalConIgv.toFixed(2)}</span>
                </div>`;
        
        // Mostrar DETRACCIÓN si existe
        if (tipoDescuentoCargo === 'DETRACCION') {
            html += `
                <div class="mb-2">
                    <i class="fa fa-minus-circle text-warning"></i>
                    <strong class="text-warning"> Detracción ${nombreConcepto} (${porcentaje}%):</strong>
                    <span class="text-warning">-${simboloMoneda} ${montoAfectacion.toFixed(2)}</span>
                </div>`;
        }
        
        // Mostrar RETENCIÓN si existe
        if (tipoDescuentoCargo === 'RETENCION') {
            html += `
                <div class="mb-2">
                    <i class="fa fa-minus-circle text-info"></i>
                    <strong class="text-info"> Retención ${nombreConcepto} (${porcentaje}%):</strong>
                    <span class="text-info">-${simboloMoneda} ${montoAfectacion.toFixed(2)}</span>
                </div>`;
        }
        
        // Mostrar PERCEPCIÓN si existe
        if (tipoDescuentoCargo === 'PERCEPCION') {
            html += `
                <div class="mb-2">
                    <i class="fa fa-plus-circle text-success"></i>
                    <strong class="text-success"> Percepción ${nombreConcepto} (${porcentaje}%):</strong>
                    <span class="text-success">+${simboloMoneda} ${montoAfectacion.toFixed(2)}</span>
                </div>`;
        }
        
        // TOTAL FINAL A PAGAR
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
    // VALIDACIÓN Y OTRAS FUNCIONES
    // ============================================
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
        
        // VALIDACIÓN DE CANTIDADES 
        const erroresValidacion = validarCantidadesCliente(itemsOrden);
        
        if (erroresValidacion.length > 0) {
            e.preventDefault();
            
            // Construir HTML simple
            let mensajeHTML = '<div style="text-align: left; font-size: 14px;">';
            mensajeHTML += '<p><strong>Las siguientes cantidades exceden lo verificado:</strong></p>';
            mensajeHTML += '<ul style="margin: 10px 0; padding-left: 20px;">';
            
            erroresValidacion.forEach(error => {
                mensajeHTML += `<li style="margin-bottom: 8px;">${error}</li>`;
            });
            
            mensajeHTML += '</ul>';
            mensajeHTML += '<p style="margin-top: 10px; color: #6c757d;">Ajusta las cantidades e intenta nuevamente.</p>';
            mensajeHTML += '</div>';
            
            Swal.fire({
                title: 'Cantidad No Permitida',
                html: mensajeHTML,
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Entendido'
            });
            
            return false;
        }
        
        // Si llegó hasta aquí, todas las validaciones pasaron
        return true;
    }
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
                            Swal.fire('¡Anulado!', response.mensaje, 'success').then(() => {
                                location.reload(); // ← Esto ya recarga, pero si no quieres recargar, usa:
                                // recalcularEstadoItems();
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
                                location.reload(); // ← Esto ya recarga
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
    }
    // ============================================
    // FUNCIONES AUTO-ORDEN
    // ============================================
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
            
            <div class="row align-items-center mb-2">
                <div class="col-md-11">
                    <div style="font-size: 12px;">
                        <i class="fa fa-cog text-primary"></i>
                        <strong>Descripción:</strong> ${item.descripcion}
                        <span class="badge badge-primary badge-sm ml-1">AUTO</span>
                    </div>
                    <small class="text-muted" style="font-size: 11px;">
                        <strong>Cantidad:</strong> ${item.cantidadVerificada}
                    </small>
                </div>
                <div class="col-md-1">
                    <span class="badge badge-primary" title="Item agregado automáticamente" style="padding: 4px 6px;">
                        <i class="fa fa-cog"></i>
                    </span>
                </div>
            </div>
            
            <div class="row align-items-end">
                <div class="col-md-2">
                    <label style="font-size: 11px; font-weight: bold;">Precio Unit.:</label>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="font-size: 11px;">S/.</span>
                        </div>
                        <input type="number" class="form-control form-control-sm precio-item" 
                            name="items_orden[${item.idDetalle}][precio_unitario]"
                            data-id-detalle="${item.idDetalle}"
                            step="0.01" min="0" placeholder="0.00"
                            style="font-size: 11px;" required>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <label style="font-size: 11px; font-weight: bold;">IGV (%):</label>
                    <input type="number" class="form-control form-control-sm igv-item" 
                        name="items_orden[${item.idDetalle}][igv]"
                        data-id-detalle="${item.idDetalle}"
                        value="" min="0" max="100" step="0.01"
                        style="font-size: 12px;" required>
                </div>
                
                <div class="col-md-3">
                    <label style="font-size: 11px; font-weight: bold;">Homologación:</label>
                    <input type="file" class="form-control-file" 
                        name="homologacion[${item.idDetalle}]"
                        accept=".pdf,.jpg,.jpeg,.png"
                        style="font-size: 11px;">
                    <small class="text-muted" style="font-size: 10px;">PDF, JPG, PNG</small>
                </div>
                
                <div class="col-md-5 text-right">
                    <div class="calculo-item" id="calculo-${item.idDetalle}" style="font-size: 11px; line-height: 1.4;">
                        <div class="subtotal-text">Subtotal: S/. 0.00</div>
                        <div class="igv-text">IGV: S/. 0.00</div>
                        <div class="total-text" style="font-weight: bold; color: #2196f3;">Total: S/. 0.00</div>
                    </div>
                </div>
            </div>
        `;
        
        contenedorItemsOrden.appendChild(itemElement);
        
        const precioInput = itemElement.querySelector('.precio-item');
        const igvInput = itemElement.querySelector('.igv-item');
        
        function calcularTotales() {
            const cantidad = parseFloat(item.cantidadVerificada) || 0;
            const precio = parseFloat(precioInput.value) || 0;
            const igvPorcentaje = parseFloat(igvInput.value) || 0;
            
            const subtotal = cantidad * precio;
            const montoIgv = subtotal * (igvPorcentaje / 100);
            const total = subtotal + montoIgv;
            
            const simboloMoneda = obtenerSimboloMoneda();
            const calculoDiv = document.getElementById(`calculo-${item.idDetalle}`);
            if (calculoDiv) {
                calculoDiv.querySelector('.subtotal-text').textContent = `Subtotal: ${simboloMoneda} ${subtotal.toFixed(2)}`;
                calculoDiv.querySelector('.igv-text').textContent = `IGV: ${simboloMoneda} ${montoIgv.toFixed(2)}`;
                calculoDiv.querySelector('.total-text').textContent = `Total: ${simboloMoneda} ${total.toFixed(2)}`;
            }
            actualizarTotalGeneral();
        }
        
        precioInput.addEventListener('input', calcularTotales);
        igvInput.addEventListener('input', calcularTotales);
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
    titulo.innerHTML = `<i class="fa fa-file-text-o text-primary"></i> Orden de Compra - ORD-${compra.id_compra}`;
    
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
                        <p style="margin: 5px 0; font-size: 13px;"><strong>N° Orden:</strong> ORD-${compra.id_compra}</p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>Proveedor:</strong> ${compra.nom_proveedor || 'No especificado'}</p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>RUC:</strong> ${compra.ruc_proveedor || 'No especificado'}</p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>Moneda:</strong> ${compra.nom_moneda || 'No especificada'}</p>
                    </div>
                    <div class="col-md-6">
                        <p style="margin: 5px 0; font-size: 13px;"><strong>Fecha:</strong> ${fechaFormateada}</p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>Estado:</strong> <span class="badge badge-${estadoClase}">${estadoTexto}</span></p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>Creado por:</strong> ${compra.nom_personal || 'No especificado'}</p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>Plazo Entrega:</strong> ${compra.plaz_compra || 'No especificado'}</p>
                    </div>
                </div>`;
    
    // 🔹 MOSTRAR BADGE DE DETRACCIÓN/RETENCIÓN/PERCEPCIÓN
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
    
    // Si no hay ninguna afectación, mostrar mensaje
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
        if (compra.port_compra) html += `<p style="margin: 5px 0; font-size: 13px;"><strong>Tipo de Porte:</strong> ${compra.port_compra}</p>`;
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
                                <th style="width: 8%;">#</th>
                                <th style="width: 15%;">Código</th>
                                <th style="width: 35%;">Descripción</th>
                                <th style="width: 10%;">Cantidad</th>
                                <th style="width: 12%;">Precio Unit.</th>
                                <th style="width: 10%;">IGV (%)</th>
                                <th style="width: 10%;">Subtotal</th>
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
        
        html += `<tr>
                    <td style="font-weight: bold;">${index + 1}</td>
                    <td>${detalle.cod_material || 'N/A'}</td>
                    <td>${detalle.nom_producto}</td>
                    <td class="text-center">${cantidad.toFixed(2)}</td>
                    <td class="text-right">${simboloMoneda} ${precioUnit.toFixed(2)}</td>
                    <td class="text-center">${igvPorcentaje}%</td>
                    <td class="text-right" style="font-weight: bold;">${simboloMoneda} ${subtotal.toFixed(2)}</td>
                </tr>`;
    });
    
    html += `</tbody></table></div><div class="row mt-3"><div class="col-md-12">`;
    
    // Calcular TOTAL CON IGV primero
    const totalConIgv = subtotalGeneral + totalIgv;
    
    // Determinar tipo de afectación
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
    
    // Calcular total final
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
    
    // MOSTRAR RESUMEN
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
}
function mostrarErrorDetalle(mensaje) {
    const errorDiv = document.getElementById('error-detalle-compra');
    errorDiv.querySelector('p').textContent = mensaje;
    errorDiv.style.display = 'block';
}
    // ============================================
    // CALCULAR TOTALES AL CARGAR EN MODO EDICIÓN
    // ============================================
    if (modoEditar) {
        // Esperar a que el DOM esté completamente cargado
        setTimeout(function() {
            console.log(' Recalculando totales en modo edición...');
            
            // Recalcular todos los items existentes
            document.querySelectorAll('[id^="item-orden-"]').forEach(function(item) {
                const cantidadInput = item.querySelector('.cantidad-item');
                const precioInput = item.querySelector('.precio-item');
                const igvInput = item.querySelector('.igv-item');
                
                if (cantidadInput && precioInput && igvInput) {
                    // Forzar recálculo disparando evento input
                    cantidadInput.dispatchEvent(new Event('input'));
                }
            });
            
            // Calcular total general con detracción/retención/percepción
            actualizarTotalGeneral();
            
            console.log(' Totales recalculados correctamente');
        }, 300);
    }
    
});
</script>