<?php 
//=======================================================================
// VISTA: v_pedidos_verificar.php - VERSIÓN COMPACTA
//=======================================================================
$pedido = $pedido_data[0]; // Datos del pedido principal
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Verificar Pedido</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <!-- Información básica del pedido - MÁS COMPACTA -->
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
                        <h2 style="margin: 0; font-size: 16px;">Items Pendientes <small id="contador-pendientes">(<?php echo count($pedido_detalle); ?> items)</small></h2>
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

                                $esAutoOrden = ($pedido['id_producto_tipo'] == 2);
                                if ($esAutoOrden) {
                                    $esVerificado = true;
                                    $cantidadVerificada = $detalle['cant_pedido_detalle'];
                                    $colorFondo = '#e3f2fd';
                                    $colorBorde = '#2196f3';
                                    $claseTexto = 'text-primary';
                                    $icono = 'fa-cog';
                                    $estadoTexto = 'Auto-Orden';
                                } else if ($esVerificado && $stockInsuficiente && $detalle['est_pedido_detalle'] != 2) {
                                    $colorFondo = '#d4edda';
                                    $colorBorde = '#28a745';
                                    $claseTexto = 'text-success';
                                    $icono = 'fa-check-circle';
                                    $estadoTexto = 'Verificado';
                                } else if ($detalle['est_pedido_detalle'] == 2) {
                                    $colorFondo = '#f8d7da';
                                    $colorBorde = '#dc3545';
                                    $claseTexto = 'text-danger';
                                    $icono = 'fa-times-circle';
                                    $estadoTexto = 'Cerrado';
                                } else {
                                    $colorFondo = '#fff3cd';
                                    $colorBorde = '#ffc107';
                                    $claseTexto = 'text-warning';
                                    $icono = 'fa-clock-o';
                                    $estadoTexto = 'Pendiente';
                                }
                            ?>
                            <div class="item-pendiente border mb-2" 
                                style="background-color: <?php echo $colorFondo; ?>; border-left: 4px solid <?php echo $colorBorde; ?> !important; padding: 8px 12px; border-radius: 4px;" 
                                data-item="<?php echo $contador_detalle; ?>">
                                
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="<?php echo $claseTexto; ?>" style="font-weight: 600; font-size: 14px;">
                                        <i class="fa <?php echo $icono; ?>"></i> Item <?php echo $contador_detalle; ?> - <?php echo $estadoTexto; ?>
                                    </span>
                                    <span>Stock Almacen: <?php echo $detalle['cantidad_disponible_almacen']; ?></span>
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
                                    <?php } elseif (!$esVerificado && $stockInsuficiente) { ?>
                                        <button type="button" class="btn btn-success btn-xs verificar-btn"
                                                data-id-detalle="<?php echo $detalle['id_pedido_detalle']; ?>"
                                                data-cantidad-actual="<?php echo $detalle['cant_pedido_detalle']; ?>"
                                                data-cantidad-almacen="<?php echo $detalle['cantidad_disponible_almacen']; ?>"
                                                title="Verificar Item" style="padding: 2px 8px; font-size: 11px;">
                                            Verificar
                                        </button>
                                        
                                    <?php } elseif ($esVerificado && $stockInsuficiente && $detalle['est_pedido_detalle'] != 2) { ?>
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
                                Items Verificados 
                                <small id="contador-verificados">(0 items)</small>
                            </h2>
                            <button type="button" class="btn btn-primary btn-sm" id="btn-nueva-orden" style="padding: 4px 8px; font-size: 12px;">
                                <i class="fa fa-plus"></i> Nueva Orden
                            </button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    
                    <div class="x_content" style="max-height: 650px; overflow-y: auto; padding: 5px;">
                        <div id="contenedor-tabla-ordenes">
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
                                                $estado_texto = ($compra['est_compra'] == 2) ? 'Aprobada' : 'Pendiente';
                                                $estado_clase = ($compra['est_compra'] == 2) ? 'success' : 'warning';
                                                $fecha_formateada = date('d/m/Y', strtotime($compra['fec_compra']));
                                            ?>
                                                <tr>
                                                    <td><strong>ORD-<?php echo $compra['id_compra']; ?></strong></td>
                                                    <td><?php echo htmlspecialchars($compra['nom_proveedor']); ?></td>
                                                    <td><?php echo $fecha_formateada; ?></td>
                                                    <td><span class="badge badge-<?php echo $estado_clase; ?>"><?php echo $estado_texto; ?></span></td>
                                                    <td>
                                                        <button class="btn btn-info btn-xs" title="Ver Detalles">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                        <?php if ($compra['est_compra'] == 1) { ?>
                                                            <button class="btn btn-warning btn-xs ml-1" title="Editar">
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                        <?php } else { ?>
                                                            <button class="btn btn-outline-secondary btn-xs ml-1" title="Editar">
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <tr>
                                                <td colspan="6" class="text-center p-3">
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

                        <div id="contenedor-nueva-orden" style="display: none;">
                            <form id="form-nueva-orden" method="POST" action="">
                                <input type="hidden" name="crear_orden" value="1">
                                <input type="hidden" name="id" value="<?php echo $id_pedido; ?>">
                                
                                <div class="card">
                                    <div class="card-header" style="padding: 8px 12px; background-color: #e3f2fd;">
                                        <h6 class="mb-0">
                                            <i class="fa fa-plus-circle text-primary"></i>
                                            Nueva Orden de Compra
                                        </h6>
                                    </div>
                                    <div class="card-body" style="padding: 12px;">
                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <label style="font-size: 11px; font-weight: bold;">Fecha: <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control form-control-sm" id="fecha_orden" name="fecha_orden" 
                                                    style="font-size: 12px;" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label style="font-size: 11px; font-weight: bold;">Proveedor: <span class="text-danger">*</span></label>
                                                <select class="form-control form-control-sm" id="proveedor_orden" name="proveedor_orden" 
                                                        style="font-size: 12px;" required>
                                                    <option value="">Seleccionar proveedor...</option>
                                                    <?php
                                                    foreach ($proveedor as $prov) {
                                                        echo '<option value="' . htmlspecialchars($prov['id_proveedor']) . '">' . htmlspecialchars($prov['nom_proveedor']) . '</option>';
                                                    }
                                                    ?>
                                                </select>
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
                                                        echo '<option value="' . htmlspecialchars($mon['id_moneda']) . '">' . htmlspecialchars($mon['nom_moneda']) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label style="font-size: 11px; font-weight: bold;">Plazo de Entrega:</label>
                                                <input type="text" class="form-control form-control-sm" id="plazo_entrega" name="plazo_entrega"
                                                    placeholder="Ej. 15 días hábiles" style="font-size: 12px;">
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <label style="font-size: 11px; font-weight: bold;">Dirección de Envío:</label>
                                                <textarea class="form-control form-control-sm" id="direccion_envio" name="direccion_envio"
                                                        rows="2" placeholder="Ingrese la dirección de envío..." 
                                                        style="font-size: 12px; resize: none;"></textarea>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <label style="font-size: 11px; font-weight: bold;">Observaciones:</label>
                                                <textarea class="form-control form-control-sm" id="observaciones_orden" name="observaciones_orden"
                                                        rows="2" placeholder="Observaciones adicionales..." 
                                                        style="font-size: 12px; resize: none;"></textarea>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <label style="font-size: 11px; font-weight: bold;">Tipo de Porte:</label>
                                                <input type="text" class="form-control form-control-sm" id="tipo_porte" name="tipo_porte"
                                                    placeholder="Ej. Marítimo, Terrestre, Aéreo" style="font-size: 12px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="contenedor-items-orden" class="mb-3">
                                </div>
                                
                                <div class="text-center mt-2" style="padding: 8px;">
                                    <button type="button" class="btn btn-secondary btn-sm mr-2" id="btn-cancelar-orden">
                                        <i class="fa fa-times"></i> Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fa fa-save"></i> Guardar Orden
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
                            <div class="col-md-3 offset-md-3">
                                <a href="pedidos_mostrar.php" class="btn btn-outline-secondary btn-sm btn-block">
                                    <i class="fa fa-arrow-left"></i> Volver
                                </a>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-success btn-sm btn-block" id="btn-finalizar-verificacion" disabled>
                                    <i class="fa fa-check-circle"></i> Finalizar Verificación
                                </button>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <p class="text-muted" style="font-size: 12px;">
                                <i class="fa fa-info-circle"></i> 
                                Verifica todos los items antes de finalizar.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const esAutoOrden = <?php echo ($pedido['id_producto_tipo'] == 2) ? 'true' : 'false'; ?>;
    if (esAutoOrden) {
        setTimeout(function() {
            mostrarFormularioNuevaOrdenAuto();
            autoAgregarItemsAOrden();
        }, 800);
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
});

document.addEventListener('DOMContentLoaded', function() {
    let itemsVerificados = 0;
    const totalItems = <?php echo count($pedido_detalle); ?>;
    
    function actualizarContadores() {
        const itemsPendientes = totalItems - itemsVerificados;
        document.getElementById('contador-pendientes').textContent = `(${itemsPendientes} items)`;
        document.getElementById('contador-verificados').textContent = `(${itemsVerificados} items)`;
        
        const contenedorPendientes = document.getElementById('contenedor-pendientes');
        const mensajeSinPendientes = document.getElementById('mensaje-sin-pendientes');
        const mensajeSinVerificados = document.getElementById('mensaje-sin-verificados');
        
        if (itemsPendientes === 0) {
            contenedorPendientes.style.display = 'none';
            mensajeSinPendientes.style.display = 'block';
        } else {
            mensajeSinPendientes.style.display = 'none';
        }
        
        if (itemsVerificados === 0) {
            mensajeSinVerificados.style.display = 'block';
        } else {
            mensajeSinVerificados.style.display = 'none';
        }
        
        const btnFinalizar = document.getElementById('btn-finalizar-verificacion');
        if (itemsVerificados === totalItems && totalItems > 0) {
            btnFinalizar.disabled = false;
            btnFinalizar.classList.remove('btn-outline-success');
            btnFinalizar.classList.add('btn-success');
        } else {
            btnFinalizar.disabled = true;
            btnFinalizar.classList.remove('btn-success');
            btnFinalizar.classList.add('btn-outline-success');
        }
    }
    
    function verificarItem(numeroItem) {
        const itemPendiente = document.querySelector(`.item-pendiente[data-item="${numeroItem}"]`);
        
        if (!itemPendiente) return;
        
        const itemVerificado = itemPendiente.cloneNode(true);
        itemVerificado.className = 'item-verificado border mb-2';
        itemVerificado.style.cssText = 'background-color: #d4edda; border-left: 4px solid #28a745 !important; padding: 8px 12px; border-radius: 4px;';
        
        const titulo = itemVerificado.querySelector('span');
        titulo.className = 'text-success';
        titulo.innerHTML = `<i class="fa fa-check-circle"></i> Item ${numeroItem}`;
        
        const btnVerificar = itemVerificado.querySelector('.btn-verificar');
        btnVerificar.outerHTML = '<span class="badge badge-success" style="font-size: 10px; padding: 2px 6px;">✓ Verificado</span>';
        
        const timestamp = new Date().toLocaleString('es-PE', {
            day: '2-digit',
            month: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        const timestampElement = document.createElement('div');
        timestampElement.style.cssText = 'font-size: 10px; color: #666; margin-top: 4px;';
        timestampElement.innerHTML = `<i class="fa fa-clock-o"></i> ${timestamp}`;
        itemVerificado.appendChild(timestampElement);
        
        const contenedorVerificados = document.getElementById('contenedor-verificados');
        contenedorVerificados.insertBefore(itemVerificado, contenedorVerificados.firstChild);
        
        itemPendiente.style.transition = 'all 0.3s ease';
        itemPendiente.style.opacity = '0';
        itemPendiente.style.transform = 'translateX(20px)';
        
        setTimeout(() => {
            itemPendiente.remove();
            itemsVerificados++;
            actualizarContadores();
        }, 300);
    }
    
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-verificar')) {
            const numeroItem = e.target.closest('.btn-verificar').getAttribute('data-item');
            verificarItem(numeroItem);
        }
    });
    
    document.getElementById('btn-finalizar-verificacion').addEventListener('click', function() {
        if (itemsVerificados === totalItems) {
            if (confirm('¿Está seguro que desea finalizar la verificación de este pedido?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                
                const inputFinalizar = document.createElement('input');
                inputFinalizar.type = 'hidden';
                inputFinalizar.name = 'finalizar_verificacion';
                inputFinalizar.value = '1';
                
                form.appendChild(inputFinalizar);
                document.body.appendChild(form);
                form.submit();
            }
        }
    });
    
    actualizarContadores();
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemsAgregadosOrden = new Set();
    
    document.addEventListener('click', function(event) {
        const btnAgregar = event.target.closest('.btn-agregarOrden');
        if (btnAgregar) {
            event.preventDefault();
            event.stopPropagation();
            
            const idDetalle = btnAgregar.dataset.idDetalle;
            const idProducto = btnAgregar.dataset.idProducto;
            const descripcion = btnAgregar.dataset.descripcion;
            const cantidadVerificada = btnAgregar.dataset.cantidadVerificada;
            const contenedorTabla = document.getElementById('contenedor-tabla-ordenes');
            if (contenedorTabla.style.display !== 'none') {
                mostrarFormularioNuevaOrden();
            }
            
            agregarItemAOrden({
                idDetalle: idDetalle,
                idProducto: idProducto,
                descripcion: descripcion,
                cantidadVerificada: cantidadVerificada,
                botonOriginal: btnAgregar
            });
        }
        
        const btnRemover = event.target.closest('.btn-remover-item');
        if (btnRemover) {
            event.preventDefault();
            event.stopPropagation();
            const idDetalle = btnRemover.dataset.idDetalle;
            removerItemDeOrden(idDetalle);
        }
    });
    
    function agregarItemAOrden(item) {
        const contenedorItemsOrden = document.getElementById('contenedor-items-orden');
        const itemElement = document.createElement('div');
        itemElement.id = `item-orden-${item.idDetalle}`;
        itemElement.classList.add('alert', 'alert-light', 'p-2', 'mb-2');
        itemElement.innerHTML = `
            <input type="hidden" name="items_orden[${item.idDetalle}][id_detalle]" value="${item.idDetalle}">
            <input type="hidden" name="items_orden[${item.idDetalle}][id_producto]" value="${item.idProducto}">
            <input type="hidden" name="items_orden[${item.idDetalle}][cantidad]" value="${item.cantidadVerificada}">
            
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
                    <button type="button" class="btn btn-danger btn-sm btn-remover-item" data-id-detalle="${item.idDetalle}">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="subtotal-item text-right" id="subtotal-${item.idDetalle}" style="font-size: 12px; font-weight: bold; color: #007bff;">
                        Subtotal: S/. 0.00
                    </div>
                </div>
            </div>
        `;
        
        contenedorItemsOrden.appendChild(itemElement);
        itemsAgregadosOrden.add(item.idDetalle);
        item.botonOriginal.disabled = true;
        item.botonOriginal.innerHTML = '<i class="fa fa-check-circle"></i> Agregado';
        item.botonOriginal.classList.remove('btn-primary');
        item.botonOriginal.classList.add('btn-success');
        const inputPrecio = itemElement.querySelector('.precio-item');
        inputPrecio.addEventListener('input', function() {
            actualizarSubtotalItem(item.idDetalle, item.cantidadVerificada);
            actualizarTotalOrden();
        });
    }
    
    function removerItemDeOrden(idDetalle) {
        const itemElement = document.getElementById(`item-orden-${idDetalle}`);
        itemElement.remove();
        itemsAgregadosOrden.delete(idDetalle);
        const originalBtn = document.querySelector(`.btn-agregarOrden[data-id-detalle="${idDetalle}"]`);
        if (originalBtn) {
            originalBtn.disabled = false;
            originalBtn.innerHTML = '<i class="fa fa-check"></i> Agregar a Orden';
            originalBtn.classList.remove('btn-success');
            originalBtn.classList.add('btn-primary');
        }
        actualizarTotalOrden();
    }

    function obtenerSimboloMoneda() {
        const selectMoneda = document.getElementById('moneda_orden');
        if (!selectMoneda || !selectMoneda.value) return 'S/.';
        
        const idMonedaSeleccionada = selectMoneda.value;
        return idMonedaSeleccionada == '1' ? 'S/.' : (idMonedaSeleccionada == '2' ? 'US$' : 'S/.');
    }

    function actualizarSubtotalItem(idDetalle, cantidad) {
        const inputPrecio = document.querySelector(`input[data-id-detalle="${idDetalle}"]`);
        const subtotalElement = document.getElementById(`subtotal-${idDetalle}`);
        
        if (inputPrecio && subtotalElement) {
            const precio = parseFloat(inputPrecio.value) || 0;
            const subtotal = (cantidad * precio).toFixed(2);
            const simboloMoneda = obtenerSimboloMoneda();
            subtotalElement.textContent = `Subtotal: ${simboloMoneda} ${subtotal}`;
        }
    }
    
    function actualizarTotalOrden() {
        const contenedorItemsOrden = document.getElementById('contenedor-items-orden');
        const items = contenedorItemsOrden.querySelectorAll('[id^="item-orden-"]');
        let total = 0;
        
        items.forEach(item => {
            const cantidad = parseFloat(item.querySelector('input[name$="[cantidad]"]').value);
            const inputPrecio = item.querySelector('input[name$="[precio_unitario]"]');
            const precio = parseFloat(inputPrecio.value) || 0;
            total += cantidad * precio;
        });
        
        let totalElement = document.getElementById('total-orden');
        let totalInput = document.getElementById('total_orden_input');
        
        if (!totalElement && items.length > 0) {
            totalElement = document.createElement('div');
            totalElement.id = 'total-orden';
            totalElement.className = 'alert alert-success text-center';
            totalElement.style.fontSize = '16px';
            totalElement.style.fontWeight = 'bold';
            
            totalInput = document.createElement('input');
            totalInput.type = 'hidden';
            totalInput.name = 'total_orden';
            totalInput.id = 'total_orden_input';
        }
        
        if (totalElement && items.length > 0) {
            const simboloMoneda = obtenerSimboloMoneda();
            totalElement.innerHTML = `<i class="fa fa-calculator"></i> TOTAL DE LA ORDEN: ${simboloMoneda} ${total.toFixed(2)}`;
            totalInput.value = total.toFixed(2);
            
            if (!totalElement.parentNode) {
                contenedorItemsOrden.appendChild(totalElement);
                contenedorItemsOrden.appendChild(totalInput);
            }
        } else if (totalElement && items.length === 0) {
            totalElement.remove();
            if (totalInput) totalInput.remove();
        }
    }
    
    function mostrarFormularioNuevaOrden() {
        const contenedorTabla = document.getElementById('contenedor-tabla-ordenes');
        const contenedorNuevaOrden = document.getElementById('contenedor-nueva-orden');
        const btnNuevaOrden = document.getElementById('btn-nueva-orden');
        
        contenedorTabla.style.display = 'none';
        contenedorNuevaOrden.style.display = 'block';
        btnNuevaOrden.innerHTML = '<i class="fa fa-table"></i> Ver Órdenes';
        btnNuevaOrden.classList.remove('btn-primary');
        btnNuevaOrden.classList.add('btn-secondary');
        document.getElementById('fecha_orden').value = new Date().toISOString().split('T')[0];
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const verificarBtns = document.querySelectorAll('.verificar-btn');
    const verificarModal = document.getElementById('verificarModal');
    const idDetalleInput = document.getElementById('id_pedido_detalle_input');
    const cantidadInput = document.getElementById('fin_cant_pedido_detalle');

    verificarBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const idDetalle = this.getAttribute('data-id-detalle');
            const cantidadActual = this.getAttribute('data-cantidad-actual');
            const cantidadAlmacen = parseFloat(this.getAttribute('data-cantidad-almacen'));
            const diferencia = cantidadActual - cantidadAlmacen;

            idDetalleInput.value = idDetalle;
            cantidadInput.value = diferencia;

            $(verificarModal).modal('show');
        });
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnNuevaOrden = document.getElementById('btn-nueva-orden');
    const btnCancelarOrden = document.getElementById('btn-cancelar-orden');
    const contenedorTabla = document.getElementById('contenedor-tabla-ordenes');
    const contenedorNuevaOrden = document.getElementById('contenedor-nueva-orden');
    const formNuevaOrden = document.getElementById('form-nueva-orden');
    
    function mostrarFormularioNuevaOrden() {
        contenedorTabla.style.display = 'none';
        contenedorNuevaOrden.style.display = 'block';
        
        btnNuevaOrden.innerHTML = '<i class="fa fa-table"></i> Ver Órdenes';
        btnNuevaOrden.classList.remove('btn-primary');
        btnNuevaOrden.classList.add('btn-secondary');
        
        document.getElementById('fecha_orden').value = new Date().toISOString().split('T')[0];
        
        formNuevaOrden.reset();
        document.getElementById('fecha_orden').value = new Date().toISOString().split('T')[0];
    }
    
    function mostrarTablaOrdenes() {
        contenedorTabla.style.display = 'block';
        contenedorNuevaOrden.style.display = 'none';
        
        btnNuevaOrden.innerHTML = '<i class="fa fa-plus"></i> Nueva Orden';
        btnNuevaOrden.classList.remove('btn-secondary');
        btnNuevaOrden.classList.add('btn-primary');
    }
    
    function limpiarItemsOrden() {
        const contenedorItemsOrden = document.getElementById('contenedor-items-orden');
        const itemsOrden = contenedorItemsOrden.querySelectorAll('[id^="item-orden-"]');
        itemsOrden.forEach(item => {
            const idDetalle = item.id.replace('item-orden-', '');
            const originalBtn = document.querySelector(`.btn-agregarOrden[data-id-detalle="${idDetalle}"]`);
            if (originalBtn) {
                originalBtn.disabled = false;
                originalBtn.innerHTML = '<i class="fa fa-check"></i> Agregar a Orden';
                originalBtn.classList.remove('btn-success');
                originalBtn.classList.add('btn-primary');
            }
            if (typeof itemsAgregadosOrden !== 'undefined') {
                itemsAgregadosOrden.delete(idDetalle);
            }
        });
        contenedorItemsOrden.innerHTML = '';
    }
    
    btnNuevaOrden.addEventListener('click', function() {
        if (contenedorTabla.style.display === 'none') {
            mostrarTablaOrdenes();
        } else {
            mostrarFormularioNuevaOrden();
        }
    });
    
    btnCancelarOrden.addEventListener('click', function() {
        if (confirm('¿Está seguro que desea cancelar? Se perderán los datos ingresados.')) {
            limpiarItemsOrden();
            formNuevaOrden.reset();
            mostrarTablaOrdenes();
        }
    });
    
    formNuevaOrden.addEventListener('submit', function(e) {
        const fecha = document.getElementById('fecha_orden').value;
        const proveedor = document.getElementById('proveedor_orden').value;
        const moneda = document.getElementById('moneda_orden').value;
        
        if (!fecha || !proveedor || !moneda) {
            e.preventDefault();
            alert('Por favor complete los campos obligatorios (Fecha, Proveedor y Moneda).');
            return false;
        }
        
        const contenedorItemsOrden = document.getElementById('contenedor-items-orden');
        const itemsOrden = contenedorItemsOrden.querySelectorAll('[id^="item-orden-"]');
        
        if (itemsOrden.length === 0) {
            e.preventDefault();
            alert('Debe agregar al menos un ítem a la orden antes de guardar.');
            return false;
        }
        
        return true;
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectMoneda = document.getElementById('moneda_orden');
    function actualizarEtiquetasMoneda(idMoneda) {
        const simboloMoneda = idMoneda == '1' ? 'S/.' : (idMoneda == '2' ? 'US$' : 'S/.');
        
        const etiquetasMoneda = document.querySelectorAll('.input-group-text');
        etiquetasMoneda.forEach(etiqueta => {
            if (etiqueta.textContent === 'S/.' || etiqueta.textContent === 'US$') {
                etiqueta.textContent = simboloMoneda;
            }
        });
        
        const itemsOrden = document.querySelectorAll('[id^="item-orden-"]');
        itemsOrden.forEach(item => {
            const idDetalle = item.id.replace('item-orden-', '');
            const cantidad = parseFloat(item.querySelector('input[name$="[cantidad]"]').value);
            actualizarSubtotalItem(idDetalle, cantidad);
        });
        
        actualizarTotalOrden();
    }
    
    selectMoneda.addEventListener('change', function() {
        const idMonedaSeleccionada = this.value;
        actualizarEtiquetasMoneda(idMonedaSeleccionada);
    });
    
    const originalAgregarItemAOrden = window.agregarItemAOrden || function() {};
    
    window.agregarItemAOrden = function(item) {
        const idMonedaSeleccionada = document.getElementById('moneda_orden').value;
        const simboloMoneda = idMonedaSeleccionada == '1' ? 'S/.' : (idMonedaSeleccionada == '2' ? 'US$' : 'S/.');
        
        const contenedorItemsOrden = document.getElementById('contenedor-items-orden');
        const itemElement = document.createElement('div');
        itemElement.id = `item-orden-${item.idDetalle}`;
        itemElement.classList.add('alert', 'alert-light', 'p-2', 'mb-2');
        itemElement.innerHTML = `
            <input type="hidden" name="items_orden[${item.idDetalle}][id_detalle]" value="${item.idDetalle}">
            <input type="hidden" name="items_orden[${item.idDetalle}][id_producto]" value="${item.idProducto}">
            <input type="hidden" name="items_orden[${item.idDetalle}][cantidad]" value="${item.cantidadVerificada}">
            
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
                    <button type="button" class="btn btn-danger btn-sm btn-remover-item" data-id-detalle="${item.idDetalle}">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="subtotal-item text-right" id="subtotal-${item.idDetalle}" style="font-size: 12px; font-weight: bold; color: #007bff;">
                        Subtotal: ${simboloMoneda} 0.00
                    </div>
                </div>
            </div>
        `;
        
        contenedorItemsOrden.appendChild(itemElement);
        if (typeof itemsAgregadosOrden !== 'undefined') {
            itemsAgregadosOrden.add(item.idDetalle);
        }
        item.botonOriginal.disabled = true;
        item.botonOriginal.innerHTML = '<i class="fa fa-check-circle"></i> Agregado';
        item.botonOriginal.classList.remove('btn-primary');
        item.botonOriginal.classList.add('btn-success');
        
        const inputPrecio = itemElement.querySelector('.precio-item');
        inputPrecio.addEventListener('input', function() {
            actualizarSubtotalItem(item.idDetalle, item.cantidadVerificada);
            actualizarTotalOrden();
        });
    };
    
    window.actualizarSubtotalItem = function(idDetalle, cantidad) {
        const inputPrecio = document.querySelector(`input[data-id-detalle="${idDetalle}"]`);
        const subtotalElement = document.getElementById(`subtotal-${idDetalle}`);
        
        if (inputPrecio && subtotalElement) {
            const precio = parseFloat(inputPrecio.value) || 0;
            const subtotal = (cantidad * precio).toFixed(2);
            const idMonedaSeleccionada = document.getElementById('moneda_orden').value;
            const simboloMoneda = idMonedaSeleccionada == '1' ? 'S/.' : (idMonedaSeleccionada == '2' ? 'US$' : 'S/.');
            subtotalElement.textContent = `Subtotal: ${simboloMoneda} ${subtotal}`;
        }
    };
    
    window.actualizarTotalOrden = function() {
        const contenedorItemsOrden = document.getElementById('contenedor-items-orden');
        const items = contenedorItemsOrden.querySelectorAll('[id^="item-orden-"]');
        let total = 0;
        
        items.forEach(item => {
            const cantidad = parseFloat(item.querySelector('input[name$="[cantidad]"]').value);
            const inputPrecio = item.querySelector('input[name$="[precio_unitario]"]');
            const precio = parseFloat(inputPrecio.value) || 0;
            total += cantidad * precio;
        });
        
        let totalElement = document.getElementById('total-orden');
        let totalInput = document.getElementById('total_orden_input');
        
        if (!totalElement && items.length > 0) {
            totalElement = document.createElement('div');
            totalElement.id = 'total-orden';
            totalElement.className = 'alert alert-success text-center';
            totalElement.style.fontSize = '16px';
            totalElement.style.fontWeight = 'bold';
            
            totalInput = document.createElement('input');
            totalInput.type = 'hidden';
            totalInput.name = 'total_orden';
            totalInput.id = 'total_orden_input';
        }
        
        if (totalElement && items.length > 0) {
            const idMonedaSeleccionada = document.getElementById('moneda_orden').value;
            const simboloMoneda = idMonedaSeleccionada == '1' ? 'S/.' : (idMonedaSeleccionada == '2' ? 'US$' : 'S/.');
            totalElement.innerHTML = `<i class="fa fa-calculator"></i> TOTAL DE LA ORDEN: ${simboloMoneda} ${total.toFixed(2)}`;
            totalInput.value = total.toFixed(2);
            
            if (!totalElement.parentNode) {
                contenedorItemsOrden.appendChild(totalElement);
                contenedorItemsOrden.appendChild(totalInput);
            }
        } else if (totalElement && items.length === 0) {
            totalElement.remove();
            if (totalInput) totalInput.remove();
        }
    };
});
</script>