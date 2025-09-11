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
            <!-- Ventana Izquierda - Items Pendientes COMPACTOS -->
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
                            ?>
                            <!-- ITEM COMPACTO -->
                            <div class="item-pendiente border mb-2" style="background-color: #fff3cd; border-left: 4px solid #ffc107 !important; padding: 8px 12px; border-radius: 4px;" data-item="<?php echo $contador_detalle; ?>">
                                <!-- Header compacto -->
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-warning" style="font-weight: 600; font-size: 14px;">
                                        <i class="fa fa-clock-o"></i> Item <?php echo $contador_detalle; ?>
                                    </span>
                                    <?php if ($detalle['cant_fin_pedido_detalle'] == null) { ?>
                                        <button type="button" class="btn btn-success btn-xs verificar-btn"
                                                data-id-detalle="<?php echo $detalle['id_pedido_detalle']; ?>"
                                                data-cantidad-actual="<?php echo $detalle['cant_pedido_detalle']; ?>"
                                                title="Verificar Item" style="padding: 2px 8px; font-size: 11px;">
                                            Verificar
                                        </button>
                                    <?php } else { ?>
                                        <button type="button" class="btn btn-primary btn-xs btn-agregarOrden" data-item="<?php echo $contador_detalle; ?>" title="Verificar Item" style="padding: 2px 8px; font-size: 11px;">
                                        <i class="fa fa-check"></i> Agregar a Orden
                                    </button>
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
                    <!-- Header con botón Nueva Orden -->
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
                        <!-- Contenedor para la tabla de órdenes -->
                        <div id="contenedor-tabla-ordenes">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" style="font-size: 12px;">
                                    <thead style="background-color: #f8f9fa;">
                                        <tr>
                                            <th style="width: 15%;">N° Orden</th>
                                            <th style="width: 20%;">Proveedor</th>
                                            <th style="width: 15%;">Fecha</th>
                                            <th style="width: 15%;">Total</th>
                                            <th style="width: 15%;">Estado</th>
                                            <th style="width: 20%;">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-ordenes">
                                        <!-- Órdenes existentes (ejemplo) -->
                                        <tr>
                                            <td><strong>ORD-001</strong></td>
                                            <td>Proveedor ABC S.A.C.</td>
                                            <td>10/09/2025</td>
                                            <td>S/ 1,250.00</td>
                                            <td><span class="badge badge-success">Aprobada</span></td>
                                            <td>
                                                <button class="btn btn-info btn-xs" title="Ver Detalles">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <button class="btn btn-warning btn-xs ml-1" title="Editar">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>ORD-002</strong></td>
                                            <td>Distribuidora XYZ E.I.R.L.</td>
                                            <td>09/09/2025</td>
                                            <td>S/ 890.50</td>
                                            <td><span class="badge badge-warning">Pendiente</span></td>
                                            <td>
                                                <button class="btn btn-info btn-xs" title="Ver Detalles">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <button class="btn btn-warning btn-xs ml-1" title="Editar">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Mensaje cuando no hay órdenes -->
                            <div id="mensaje-sin-ordenes" class="text-center p-3" style="display: none;">
                                <i class="fa fa-file-text-o fa-2x text-info mb-2"></i>
                                <h5 class="text-info">Sin órdenes de compra</h5>
                                <p class="text-muted" style="font-size: 12px;">Las órdenes de compra aparecerán aquí.</p>
                            </div>
                        </div>

                        <!-- Formulario para Nueva Orden (inicialmente oculto) -->
                        <div id="contenedor-nueva-orden" style="display: none;">
                            <form id="form-nueva-orden">
                                <div class="card">
                                    <div class="card-header" style="padding: 8px 12px; background-color: #e3f2fd;">
                                        <h6 class="mb-0">
                                            <i class="fa fa-plus-circle text-primary"></i> 
                                            Nueva Orden de Compra
                                        </h6>
                                    </div>
                                    <div class="card-body" style="padding: 12px;">
                                        <!-- Información básica -->
                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <label style="font-size: 11px; font-weight: bold;">N° Orden:</label>
                                                <input type="text" class="form-control form-control-sm" id="num_orden" 
                                                    placeholder="ORD-003" style="font-size: 12px;">
                                            </div>
                                            <div class="col-md-6">
                                                <label style="font-size: 11px; font-weight: bold;">Fecha:</label>
                                                <input type="date" class="form-control form-control-sm" id="fecha_orden" 
                                                    style="font-size: 12px;">
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <label style="font-size: 11px; font-weight: bold;">Proveedor:</label>
                                                <select class="form-control form-control-sm" id="proveedor_orden" style="font-size: 12px;">
                                                    <option value="">Seleccionar proveedor...</option>
                                                    <option value="1">Proveedor ABC S.A.C.</option>
                                                    <option value="2">Distribuidora XYZ E.I.R.L.</option>
                                                    <option value="3">Comercial 123 S.R.L.</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <label style="font-size: 11px; font-weight: bold;">Moneda:</label>
                                                <select class="form-control form-control-sm" id="moneda_orden" style="font-size: 12px;">
                                                    <option value="PEN">Soles (S/)</option>
                                                    <option value="USD">Dólares ($)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label style="font-size: 11px; font-weight: bold;">Total Estimado:</label>
                                                <input type="number" class="form-control form-control-sm" id="total_orden" 
                                                    placeholder="0.00" step="0.01" style="font-size: 12px;">
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <label style="font-size: 11px; font-weight: bold;">Observaciones:</label>
                                                <textarea class="form-control form-control-sm" id="observaciones_orden" 
                                                        rows="2" placeholder="Observaciones adicionales..." 
                                                        style="font-size: 12px; resize: none;"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Botones de acción -->
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

        <!-- Botones compactos -->
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
        
        // Clonar y adaptar para verificado
        const itemVerificado = itemPendiente.cloneNode(true);
        itemVerificado.className = 'item-verificado border mb-2';
        itemVerificado.style.cssText = 'background-color: #d4edda; border-left: 4px solid #28a745 !important; padding: 8px 12px; border-radius: 4px;';
        
        // Actualizar título
        const titulo = itemVerificado.querySelector('span');
        titulo.className = 'text-success';
        titulo.innerHTML = `<i class="fa fa-check-circle"></i> Item ${numeroItem}`;
        
        // Reemplazar botón
        const btnVerificar = itemVerificado.querySelector('.btn-verificar');
        btnVerificar.outerHTML = '<span class="badge badge-success" style="font-size: 10px; padding: 2px 6px;">✓ Verificado</span>';
        
        // Agregar timestamp compacto
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
        
        // Agregar al contenedor
        const contenedorVerificados = document.getElementById('contenedor-verificados');
        contenedorVerificados.insertBefore(itemVerificado, contenedorVerificados.firstChild);
        
        // Animación de salida
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
</script><script>
document.addEventListener('DOMContentLoaded', function() {
    const btnNuevaOrden = document.getElementById('btn-nueva-orden');
    const btnCancelarOrden = document.getElementById('btn-cancelar-orden');
    const contenedorTabla = document.getElementById('contenedor-tabla-ordenes');
    const contenedorNuevaOrden = document.getElementById('contenedor-nueva-orden');
    const formNuevaOrden = document.getElementById('form-nueva-orden');
    
    // Función para mostrar formulario de nueva orden
    function mostrarFormularioNuevaOrden() {
        contenedorTabla.style.display = 'none';
        contenedorNuevaOrden.style.display = 'block';
        
        // Cambiar el botón
        btnNuevaOrden.innerHTML = '<i class="fa fa-table"></i> Ver Órdenes';
        btnNuevaOrden.classList.remove('btn-primary');
        btnNuevaOrden.classList.add('btn-secondary');
        
        // Establecer fecha actual
        document.getElementById('fecha_orden').value = new Date().toISOString().split('T')[0];
        
        // Limpiar formulario
        formNuevaOrden.reset();
    }
    
    // Función para mostrar tabla de órdenes
    function mostrarTablaOrdenes() {
        contenedorTabla.style.display = 'block';
        contenedorNuevaOrden.style.display = 'none';
        
        // Restaurar el botón
        btnNuevaOrden.innerHTML = '<i class="fa fa-plus"></i> Nueva Orden';
        btnNuevaOrden.classList.remove('btn-secondary');
        btnNuevaOrden.classList.add('btn-primary');
    }
    
    // Event listeners
    btnNuevaOrden.addEventListener('click', function() {
        if (contenedorTabla.style.display === 'none') {
            mostrarTablaOrdenes();
        } else {
            mostrarFormularioNuevaOrden();
        }
    });
    
    btnCancelarOrden.addEventListener('click', function() {
        if (confirm('¿Está seguro que desea cancelar? Se perderán los datos ingresados.')) {
            mostrarTablaOrdenes();
        }
    });
    
    // Manejar envío del formulario
    formNuevaOrden.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const datos = {
            num_orden: document.getElementById('num_orden').value,
            fecha_orden: document.getElementById('fecha_orden').value,
            proveedor_orden: document.getElementById('proveedor_orden').value,
            moneda_orden: document.getElementById('moneda_orden').value,
            total_orden: document.getElementById('total_orden').value,
            observaciones_orden: document.getElementById('observaciones_orden').value
        };
        
        // Validaciones básicas
        if (!datos.num_orden || !datos.fecha_orden || !datos.proveedor_orden) {
            alert('Por favor complete los campos obligatorios (N° Orden, Fecha y Proveedor).');
            return;
        }
        
        // Aquí harías la petición AJAX para guardar
        console.log('Datos de la nueva orden:', datos);
        
        // Simulación de guardado exitoso
        if (confirm('¿Desea guardar esta orden de compra?')) {
            // Agregar nueva fila a la tabla (simulación)
            const tbody = document.getElementById('tbody-ordenes');
            const nuevaFila = `
                <tr>
                    <td><strong>${datos.num_orden}</strong></td>
                    <td>${document.getElementById('proveedor_orden').selectedOptions[0].text}</td>
                    <td>${new Date(datos.fecha_orden).toLocaleDateString('es-PE')}</td>
                    <td>${datos.moneda_orden === 'PEN' ? 'S/' : '$'} ${parseFloat(datos.total_orden || 0).toFixed(2)}</td>
                    <td><span class="badge badge-info">Nueva</span></td>
                    <td>
                        <button class="btn btn-info btn-xs" title="Ver Detalles">
                            <i class="fa fa-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-xs ml-1" title="Editar">
                            <i class="fa fa-edit"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('afterbegin', nuevaFila);
            
            alert('Orden de compra creada exitosamente.');
            mostrarTablaOrdenes();
        }
    });
    
    // Actualizar total automáticamente si es necesario
    document.getElementById('total_orden').addEventListener('input', function() {
        const valor = parseFloat(this.value) || 0;
        this.value = valor.toFixed(2);
    });
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

            // Pasa los datos al modal
            idDetalleInput.value = idDetalle;
            //cantidadInput.value = cantidadActual;

            // Muestra el modal (usando el método de Bootstrap)
            $(verificarModal).modal('show');
        });
    });
});
</script>