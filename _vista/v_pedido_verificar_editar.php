<?php 
//=======================================================================
// VISTA: v_pedido_verificar_editar.php - EDITAR ORDEN DE COMPRA
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Editar Orden de Compra</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <!-- Información básica de la orden -->
        <div class="row mb-3">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header py-2" style="background-color: #f8f9fa;">
                        <h5 class="mb-0" style="font-size: 16px;">Orden <small>ORD-<?php echo $orden_data['id_compra']; ?></small></h5>
                    </div>
                    <div class="card-body py-2">
                        <div class="row" style="font-size: 13px;">
                            <div class="col-md-3 mb-1">
                                <strong>N° Orden:</strong> ORD-<?php echo $orden_data['id_compra']; ?>
                            </div>
                            <div class="col-md-3 mb-1">
                                <strong>Estado:</strong> 
                                <?php 
                                $estado_texto = ($orden_data['est_compra'] == 2) ? 'Aprobada' : 'Pendiente';
                                $estado_clase = ($orden_data['est_compra'] == 2) ? 'success' : 'warning';
                                ?>
                                <span class="badge badge-<?php echo $estado_clase; ?>"><?php echo $estado_texto; ?></span>
                            </div>
                            <div class="col-md-3 mb-1">
                                <strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($orden_data['fec_compra'])); ?>
                            </div>
                            <div class="col-md-3 mb-1">
                                <strong>ID Pedido:</strong> <?php echo $orden_data['id_pedido']; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header py-2">
                        <h5 class="mb-0" style="font-size: 15px;">
                            <i class="fa fa-edit text-primary"></i> 
                            Formulario de Edición
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <form id="form-editar-orden" method="POST" action="">
                            <input type="hidden" name="actualizar_orden" value="1">
                            <input type="hidden" name="id_compra" value="<?php echo $id_compra; ?>">
                            
                            <!-- Datos básicos de la orden -->
                            <div class="card mb-3">
                                <div class="card-header py-2" style="background-color: #e3f2fd;">
                                    <h6 class="mb-0" style="font-size: 14px;">
                                        <i class="fa fa-info-circle text-primary"></i>
                                        Datos de la Orden
                                    </h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label style="font-size: 12px; font-weight: bold;">Fecha: <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="fecha_orden" name="fecha_orden" 
                                                value="<?php echo date('Y-m-d', strtotime($orden_data['fec_compra'])); ?>" 
                                                style="font-size: 13px;" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label style="font-size: 12px; font-weight: bold;">Proveedor: <span class="text-danger">*</span></label>
                                            <select class="form-control" id="proveedor_orden" name="proveedor_orden" 
                                                    style="font-size: 13px;" required>
                                                <option value="">Seleccionar proveedor...</option>
                                                <?php
                                                if (isset($proveedor) && is_array($proveedor)) {
                                                    foreach ($proveedor as $prov) {
                                                        $selected = ($prov['id_proveedor'] == $orden_data['id_proveedor']) ? 'selected' : '';
                                                        echo '<option value="' . htmlspecialchars($prov['id_proveedor']) . '" ' . $selected . '>' . htmlspecialchars($prov['nom_proveedor']) . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label style="font-size: 12px; font-weight: bold;">Moneda: <span class="text-danger">*</span></label>
                                            <select class="form-control" id="moneda_orden" name="moneda_orden" 
                                                    style="font-size: 13px;" required>
                                                <option value="">Seleccionar moneda...</option>
                                                <?php
                                                if (isset($moneda) && is_array($moneda)) {
                                                    foreach ($moneda as $mon) {
                                                        $selected = ($mon['id_moneda'] == $orden_data['id_moneda']) ? 'selected' : '';
                                                        echo '<option value="' . htmlspecialchars($mon['id_moneda']) . '" ' . $selected . '>' . htmlspecialchars($mon['nom_moneda']) . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label style="font-size: 12px; font-weight: bold;">Plazo de Entrega:</label>
                                            <input type="text" class="form-control" id="plazo_entrega" name="plazo_entrega"
                                                value="<?php echo htmlspecialchars($orden_data['plaz_compra'] ?? ''); ?>"
                                                placeholder="Ej. 15 días hábiles" style="font-size: 13px;">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label style="font-size: 12px; font-weight: bold;">Dirección de Envío:</label>
                                            <textarea class="form-control" id="direccion_envio" name="direccion_envio"
                                                    rows="2" placeholder="Ingrese la dirección de envío..." 
                                                    style="font-size: 13px; resize: none;"><?php echo htmlspecialchars($orden_data['denv_compra'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label style="font-size: 12px; font-weight: bold;">Observaciones:</label>
                                            <textarea class="form-control" id="observaciones_orden" name="observaciones_orden"
                                                    rows="2" placeholder="Observaciones adicionales..." 
                                                    style="font-size: 13px; resize: none;"><?php echo htmlspecialchars($orden_data['obs_compra'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-12">
                                            <label style="font-size: 12px; font-weight: bold;">Tipo de Porte:</label>
                                            <input type="text" class="form-control" id="tipo_porte" name="tipo_porte"
                                                value="<?php echo htmlspecialchars($orden_data['port_compra'] ?? ''); ?>"
                                                placeholder="Ej. Marítimo, Terrestre, Aéreo" style="font-size: 13px;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Items de la orden -->
                            <div class="card mb-3">
                                <div class="card-header py-2" style="background-color: #e8f5e8;">
                                    <h6 class="mb-0" style="font-size: 14px;">
                                        <i class="fa fa-list-alt text-success"></i>
                                        Items de la Orden
                                    </h6>
                                </div>
                                <div class="card-body p-3">
                                    <div id="contenedor-items-orden">
                                        <?php if (isset($orden_detalle) && is_array($orden_detalle) && !empty($orden_detalle)) { ?>
                                            <?php foreach ($orden_detalle as $item): ?>
                                                <div class="border rounded p-2 mb-3" style="border-left: 4px solid #28a745 !important;">
                                                    <input type="hidden" name="items_orden[<?php echo $item['id_compra_detalle']; ?>][id_compra_detalle]" value="<?php echo $item['id_compra_detalle']; ?>">
                                                    <input type="hidden" name="items_orden[<?php echo $item['id_compra_detalle']; ?>][id_producto]" value="<?php echo $item['id_producto']; ?>">
                                                    <input type="hidden" name="items_orden[<?php echo $item['id_compra_detalle']; ?>][cantidad]" value="<?php echo $item['cant_compra_detalle']; ?>">
                                                    
                                                    <div class="row align-items-center">
                                                        <div class="col-md-8">
                                                            <div style="font-size: 13px;">
                                                                <div class="mb-1">
                                                                    <strong>Código:</strong> <?php echo htmlspecialchars($item['cod_material'] ?? 'N/A'); ?>
                                                                    <span style="margin: 0 8px;">|</span>
                                                                    <strong>Descripción:</strong> <?php echo htmlspecialchars($item['nom_producto']); ?>
                                                                </div>
                                                                <div>
                                                                    <strong>Cantidad:</strong> <?php echo $item['cant_compra_detalle']; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label style="font-size: 12px; font-weight: bold;">Precio Unit.:</label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" style="font-size: 12px;">
                                                                        <?php echo ($orden_data['id_moneda'] == 1) ? 'S/.' : 'US$'; ?>
                                                                    </span>
                                                                </div>
                                                                <input type="number" class="form-control precio-item" 
                                                                       name="items_orden[<?php echo $item['id_compra_detalle']; ?>][precio_unitario]"
                                                                       data-id-detalle="<?php echo $item['id_compra_detalle']; ?>"
                                                                       data-cantidad="<?php echo $item['cant_compra_detalle']; ?>"
                                                                       value="<?php echo $item['prec_compra_detalle']; ?>" 
                                                                       step="0.01" min="0" 
                                                                       style="font-size: 13px;" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1 text-center">
                                                            <span class="badge badge-success" style="font-size: 11px; padding: 4px 8px;">
                                                                <i class="fa fa-check"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-12">
                                                            <div class="subtotal-item text-right" 
                                                                 id="subtotal-<?php echo $item['id_compra_detalle']; ?>" 
                                                                 style="font-size: 13px; font-weight: bold; color: #28a745;">
                                                                Subtotal: <?php echo ($orden_data['id_moneda'] == 1) ? 'S/.' : 'US$'; ?> 
                                                                <?php echo number_format($item['cant_compra_detalle'] * $item['prec_compra_detalle'], 2); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php } else { ?>
                                            <div class="text-center p-3">
                                                <i class="fa fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                                                <h5 class="text-warning">Sin items</h5>
                                                <p class="text-muted" style="font-size: 13px;">No se encontraron items para esta orden.</p>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="text-center mt-4">
                                <a href="pedido_verificar.php?id=<?php echo $orden_data['id_pedido']; ?>" 
                                   class="btn btn-secondary mr-3">
                                    <i class="fa fa-arrow-left"></i> Volver
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let formularioModificado = false;
    
    // Inicializar cálculos al cargar la página
    setTimeout(function() {
        actualizarTotalOrden();
    }, 100);
    
    // Event listeners para cambios en precios
    document.querySelectorAll('.precio-item').forEach(function(input) {
        input.addEventListener('input', function() {
            const idDetalle = this.getAttribute('data-id-detalle');
            const cantidad = this.getAttribute('data-cantidad');
            actualizarSubtotalItem(idDetalle, cantidad);
            actualizarTotalOrden();
            formularioModificado = true;
        });
    });

    // Event listener para cambio de moneda
    const selectMoneda = document.getElementById('moneda_orden');
    if (selectMoneda) {
        selectMoneda.addEventListener('change', function() {
            actualizarEtiquetasMoneda(this.value);
            actualizarTotalOrden();
            formularioModificado = true;
        });
    }

    // Marcar como modificado en cambios de otros campos
    document.querySelectorAll('input:not(.precio-item), select, textarea').forEach(function(elemento) {
        elemento.addEventListener('change', function() {
            formularioModificado = true;
        });
    });

    // Validación del formulario antes de enviar
    document.getElementById('form-editar-orden').addEventListener('submit', function(e) {
        if (!validarFormulario()) {
            e.preventDefault();
            mostrarErrorValidacion();
            return false;
        }
    });

    // Confirmación antes de salir si hay cambios
    document.querySelector('a.btn-secondary').addEventListener('click', function(e) {
        if (formularioModificado) {
            e.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '¿Está seguro?',
                    text: 'Tiene cambios sin guardar. ¿Desea salir de todas formas?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, salir',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = e.target.href;
                    }
                });
            } else {
                if (confirm('Tiene cambios sin guardar. ¿Desea salir de todas formas?')) {
                    window.location.href = e.target.href;
                }
            }
        }
    });

    // FUNCIONES DE CÁLCULO
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
            const subtotal = (parseFloat(cantidad) * precio).toFixed(2);
            const simboloMoneda = obtenerSimboloMoneda();
            subtotalElement.textContent = `Subtotal: ${simboloMoneda} ${subtotal}`;
        }
    }

    function actualizarTotalOrden() {
        const items = document.querySelectorAll('.precio-item');
        let total = 0;
        
        items.forEach(function(input) {
            const cantidad = parseFloat(input.getAttribute('data-cantidad')) || 0;
            const precio = parseFloat(input.value) || 0;
            total += cantidad * precio;
        });

        let totalElement = document.getElementById('total-orden');
        let totalInput = document.getElementById('total_orden_input');
        
        if (!totalElement && items.length > 0) {
            totalElement = document.createElement('div');
            totalElement.id = 'total-orden';
            totalElement.className = 'alert alert-success text-center mt-3';
            totalElement.style.fontSize = '16px';
            totalElement.style.fontWeight = 'bold';
            
            totalInput = document.createElement('input');
            totalInput.type = 'hidden';
            totalInput.name = 'total_orden';
            totalInput.id = 'total_orden_input';
            
            const contenedorItems = document.getElementById('contenedor-items-orden');
            contenedorItems.appendChild(totalElement);
            contenedorItems.appendChild(totalInput);
        }
        
        if (totalElement && items.length > 0) {
            const simboloMoneda = obtenerSimboloMoneda();
            totalElement.innerHTML = `<i class="fa fa-calculator"></i> TOTAL DE LA ORDEN: ${simboloMoneda} ${total.toFixed(2)}`;
            if (totalInput) totalInput.value = total.toFixed(2);
        }
    }

    function actualizarEtiquetasMoneda(idMoneda) {
        const simboloMoneda = idMoneda == '1' ? 'S/.' : (idMoneda == '2' ? 'US$' : 'S/.');
        
        // Actualizar etiquetas de moneda en los inputs
        document.querySelectorAll('.input-group-text').forEach(function(etiqueta) {
            if (etiqueta.textContent.includes('S/.') || etiqueta.textContent.includes('US$')) {
                etiqueta.textContent = simboloMoneda;
            }
        });
        
        // Actualizar subtotales
        document.querySelectorAll('.precio-item').forEach(function(input) {
            const idDetalle = input.getAttribute('data-id-detalle');
            const cantidad = input.getAttribute('data-cantidad');
            actualizarSubtotalItem(idDetalle, cantidad);
        });
    }

    // VALIDACIÓN DEL FORMULARIO
    function validarFormulario() {
        const fecha = document.getElementById('fecha_orden').value;
        const proveedor = document.getElementById('proveedor_orden').value;
        const moneda = document.getElementById('moneda_orden').value;
        
        // Validar campos obligatorios
        if (!fecha || !proveedor || !moneda) {
            return false;
        }
        
        // Validar que todos los precios sean números válidos y positivos
        const preciosValidos = Array.from(document.querySelectorAll('.precio-item')).every(function(input) {
            const valor = parseFloat(input.value);
            return !isNaN(valor) && valor >= 0;
        });
        
        return preciosValidos;
    }

    function mostrarErrorValidacion() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Error de Validación',
                text: 'Por favor complete todos los campos obligatorios y asegúrese de que los precios sean válidos.',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Entendido'
            });
        } else {
            alert('Por favor complete todos los campos obligatorios y asegúrese de que los precios sean válidos.');
        }
    }

    // Efectos visuales para mejor UX
    document.querySelectorAll('.precio-item').forEach(function(input) {
        input.addEventListener('focus', function() {
            this.style.borderColor = '#007bff';
            this.style.boxShadow = '0 0 0 0.2rem rgba(0,123,255,.25)';
        });
        
        input.addEventListener('blur', function() {
            this.style.borderColor = '';
            this.style.boxShadow = '';
        });
    });
});
</script>