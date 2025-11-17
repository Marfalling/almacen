<?php
// Vista para verificar ingreso de productos/servicios - v_ingresos_verificar.php
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>
                    <?php if ($es_servicio): ?>
                        Verificar Servicio<small> - Orden de Servicio <strong>#<?php echo $comprax['id_compra']; ?></strong></small>
                    <?php else: ?>
                        Verificar Ingreso<small> - Orden de Compra <strong>#<?php echo $comprax['id_compra']; ?></strong></small>
                    <?php endif; ?>
                </h3>
            </div>
            <div class="title_right">
                <div class="pull-right">
                    <a href="ingresos_mostrar.php" class="btn btn-outline-primary">
                        <i class="fa fa-arrow-left"></i> Volver al Listado
                    </a>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12">
                
                <!-- ============================================ -->
                <!-- INFORMACIÓN DE LA ORDEN -->
                <!-- ============================================ -->
                <div class="x_panel shadow-sm">
                    <div class="x_title">
                        <h2>
                            <?php if ($es_servicio): ?>
                                Información de la Orden de Servicio
                            <?php else: ?>
                                Información de la Orden de Compra
                            <?php endif; ?>
                        </h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="form-horizontal">

                            <div class="form-group row mb-2">
                                <label class="alert alert-secondary col-md-3 col-sm-3 py-2 mb-0">
                                    N° Orden:
                                </label>
                                <div class="col-md-3 col-sm-3">
                                    <p class="form-control-plaintext fw-bold mb-0">
                                        C00<?php echo $comprax['id_compra']; ?>
                                    </p>
                                </div>

                                <label class="alert alert-secondary col-md-3 col-sm-3 py-2 mb-0">
                                    Proveedor:
                                </label>
                                <div class="col-md-3 col-sm-3">
                                    <p class="form-control-plaintext mb-0">
                                        <?php echo $comprax['nom_proveedor']; ?>
                                    </p>
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                <label class="alert alert-secondary col-md-3 col-sm-3 py-2 mb-0">
                                    Código Pedido:
                                </label>
                                <div class="col-md-3 col-sm-3">
                                    <p class="form-control-plaintext mb-0">
                                        <?php echo $comprax['cod_pedido']; ?>
                                    </p>
                                </div>

                                <label class="alert alert-secondary col-md-3 col-sm-3 py-2 mb-0">
                                    RUC:
                                </label>
                                <div class="col-md-3 col-sm-3">
                                    <p class="form-control-plaintext mb-0">
                                        <?php echo $comprax['ruc_proveedor']; ?>
                                    </p>
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                <label class="alert alert-secondary col-md-3 col-sm-3 py-2 mb-0">
                                    Fecha:
                                </label>
                                <div class="col-md-3 col-sm-3">
                                    <p class="form-control-plaintext mb-0">
                                        <?php echo date('d/m/Y', strtotime($comprax['fec_compra'])); ?>
                                    </p>
                                </div>

                                <label class="alert alert-secondary col-md-3 col-sm-3 py-2 mb-0">
                                    Almacén:
                                </label>
                                <div class="col-md-3 col-sm-3">
                                    <p class="form-control-plaintext mb-0">
                                        <?php echo $comprax['nom_almacen']; ?>
                                    </p>
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                <label class="alert alert-secondary col-md-3 col-sm-3 py-2 mb-0">
                                    Ubicación:
                                </label>
                                <div class="col-md-3 col-sm-3">
                                    <p class="form-control-plaintext mb-0">
                                        <?php echo $comprax['nom_ubicacion']; ?>
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- ============================================ -->
                <!-- DOCUMENTOS DEL INGRESO -->
                <!-- ============================================ -->
                <div class="x_panel">
                    <div class="x_title">
                        <h2>
                            <i class="fa fa-file-text-o"></i> Documentos del Ingreso 
                            <span class="text-danger">*Obligatorio</span>
                        </h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> 
                            <strong>Importante:</strong> Debe adjuntar al menos un documento (guía de remisión, factura, etc.) para procesar el ingreso.
                        </div>
                        
                        <!-- Formulario de Carga -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label><strong>Seleccionar Documento:</strong></label>
                                <input type="file" id="documento_ingreso" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                <small class="text-muted">Formatos permitidos: PDF, JPG, PNG, DOC, DOCX (Máx. 5MB)</small>
                            </div>
                            <div class="col-md-4">
                                <label>&nbsp;</label>
                                <button type="button" onclick="subirDocumentoIngreso()" class="btn btn-primary btn-block">
                                    <i class="fa fa-upload"></i> Subir Documento
                                </button>
                            </div>
                        </div>

                        <!-- Lista de Documentos Subidos -->
                        <div id="lista-documentos-ingreso">
                            <h5><i class="fa fa-folder-open"></i> Documentos Cargados:</h5>
                            <div id="contenedor-documentos" class="mt-2">
                                <?php if (empty($documentos_ingreso)) { ?>
                                    <div class="alert alert-warning text-center">
                                        <i class="fa fa-exclamation-triangle"></i> 
                                        Aún no se han cargado documentos
                                    </div>
                                <?php } else { ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-sm">
                                            <thead>
                                                <tr>
                                                    <th style="width: 8%;">#</th>
                                                    <th style="width: 62%;">Archivo</th>
                                                    <th style="width: 20%;">Fecha</th>
                                                    <th style="width: 10%;">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                foreach ($documentos_ingreso as $index => $doc) { 
                                                    $extension = strtolower(pathinfo($doc['documento'], PATHINFO_EXTENSION));
                                                    $icono = 'fa-file-o';
                                                    if ($extension == 'pdf') $icono = 'fa-file-pdf-o';
                                                    elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) $icono = 'fa-file-image-o';
                                                    elseif (in_array($extension, ['doc', 'docx'])) $icono = 'fa-file-word-o';
                                                    elseif (in_array($extension, ['xls', 'xlsx'])) $icono = 'fa-file-excel-o';
                                                ?>
                                                <tr>
                                                    <td class="text-center font-weight-bold"><?php echo $index + 1; ?></td>
                                                    <td>
                                                        <a href="../uploads/ingresos/<?php echo $doc['documento']; ?>" target="_blank" class="text-primary">
                                                            <i class="fa <?php echo $icono; ?>"></i> <?php echo $doc['documento']; ?>
                                                        </a>
                                                    </td>
                                                    <td class="text-center">
                                                        <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($doc['fec_subida'])); ?></small>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-danger btn-xs" 
                                                                onclick="eliminarDocumentoIngreso(<?php echo $doc['id_doc']; ?>)">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="alert alert-success mt-2 mb-0" style="display: flex; align-items: center; justify-content: center;">
                                        <i class="fa fa-check-circle" style="font-size: 18px; margin-right: 8px;"></i> 
                                        <span style="font-size: 14px;">
                                            <strong><?php echo count($documentos_ingreso); ?></strong> documento(s) adjuntado(s) correctamente
                                        </span>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($productos_pendientes)) { ?>
                <!-- ============================================ -->
                <!-- PRODUCTOS/SERVICIOS PENDIENTES -->
                <!-- ============================================ -->
                <div class="x_panel">
                    <div class="x_title">
                        <h2>
                            <?php if ($es_servicio): ?>
                                Servicios Pendientes de Validación
                            <?php else: ?>
                                Productos Pendientes de Ingreso
                            <?php endif; ?>
                            <small>(<?php echo count($productos_pendientes); ?> <?php echo $es_servicio ? 'servicios' : 'productos'; ?>)</small>
                        </h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        
                        <form id="form-ingreso" class="form-horizontal form-label-left" method="POST">
                            
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card-box table-responsive">
                                        <table class="table table-striped table-bordered" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 5%; text-align: center;">#</th>
                                                    <th style="width: 15%;">Código</th>
                                                    <th style="width: <?php echo $es_servicio ? '45%' : '25%'; ?>;">
                                                        <?php echo $es_servicio ? 'Servicio' : 'Producto'; ?>
                                                    </th>
                                                    <th style="width: <?php echo $es_servicio ? '10%' : '8%'; ?>;">Unidad</th>
                                                    <th style="width: <?php echo $es_servicio ? '15%' : '10%'; ?>; text-align: center;">
                                                        <?php echo $es_servicio ? 'Cantidad' : 'Compra'; ?>
                                                    </th>
                                                    
                                                    <?php if (!$es_servicio): ?>
                                                    <!-- COLUMNAS SOLO PARA MATERIALES -->
                                                    <th style="width: 10%; text-align: center;">Ingresado</th>
                                                    <th style="width: 10%; text-align: center;">Pendiente</th>
                                                    <th style="width: 12%; text-align: center;">Cantidad a Ingresar</th>
                                                    <th style="width: 5%; text-align: center;">Sel.</th>
                                                    <?php endif; ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $contador = 1;
                                                foreach ($productos_pendientes as $producto) { 
                                                ?>
                                                <tr>
                                                    <td style="text-align: center; font-weight: bold; font-size: 16px;">
                                                        <?php echo $contador; ?>
                                                    </td>
                                                    <td><strong><?php echo $producto['cod_material']; ?></strong></td>
                                                    <td><?php echo $producto['nom_producto']; ?></td>
                                                    <td style="text-align: center;"><?php echo $producto['nom_unidad_medida']; ?></td>
                                                    <td style="text-align: center;">
                                                        <span class="badge badge-secondary badge_size">
                                                            <?php echo number_format($producto['cant_compra_detalle'], 2); ?>
                                                        </span>
                                                    </td>
                                                    
                                                    <?php if (!$es_servicio): ?>
                                                    <!-- COLUMNAS SOLO PARA MATERIALES -->
                                                    <td style="text-align: center;">
                                                        <span class="badge badge-success badge_size">
                                                            <?php echo number_format($producto['cantidad_ingresada'], 2); ?>
                                                        </span>
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <span class="badge badge-warning badge_size">
                                                            <?php echo number_format($producto['cantidad_pendiente'], 2); ?>
                                                        </span>
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <input type="number" 
                                                            name="cantidades[<?php echo $producto['id_producto']; ?>]"
                                                            class="form-control cantidad-input text-center"
                                                            min="0.01" 
                                                            max="<?php echo $producto['cantidad_pendiente']; ?>"
                                                            step="0.01"
                                                            placeholder=""
                                                            onchange="validarCantidad(this, <?php echo $producto['cantidad_pendiente']; ?>)">
                                                        <input type="hidden" name="productos_seleccionados[]" value="<?php echo $producto['id_producto']; ?>">
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <div class="checkbox" style="margin: 0;">
                                                            <label style="margin-bottom: 0;">
                                                                <input type="checkbox" 
                                                                    class="producto-checkbox"
                                                                    data-producto="<?php echo $producto['id_producto']; ?>"
                                                                    data-pendiente="<?php echo $producto['cantidad_pendiente']; ?>"
                                                                    onchange="toggleProducto(this)"
                                                                    style="transform: scale(1.3);">
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <?php else: ?>
                                                    <!-- HIDDEN INPUTS PARA SERVICIOS -->
                                                    <input type="hidden" 
                                                           name="cantidades[<?php echo $producto['id_producto']; ?>]" 
                                                           value="<?php echo $producto['cantidad_pendiente']; ?>">
                                                    <input type="hidden" 
                                                           name="productos_seleccionados[]" 
                                                           value="<?php echo $producto['id_producto']; ?>">
                                                    <?php endif; ?>
                                                </tr>
                                                <?php 
                                                $contador++;
                                                } 
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- ============================================ -->
                <!-- BOTÓN DE PROCESAMIENTO -->
                <!-- ============================================ -->
                <div class="x_panel">
                    <div class="x_content text-center" style="padding: 30px;">
                        <?php if (empty($documentos_ingreso)): ?>
                            <div class="alert alert-warning mb-3">
                                <i class="fa fa-exclamation-triangle"></i>
                                Debe adjuntar al menos un documento antes de procesar el ingreso
                            </div>
                            <button type="button" class="btn btn-secondary btn-lg" disabled>
                                <i class="fa fa-lock"></i> 
                                <?php echo $es_servicio ? 'Validar Servicio' : 'Procesar Ingreso'; ?>
                            </button>
                        <?php else: ?>
                            <button type="button" onclick="procesarIngreso()" class="btn btn-success btn-lg">
                                <i class="fa fa-<?php echo $es_servicio ? 'check-circle' : 'plus-circle'; ?>"></i> 
                                <?php echo $es_servicio ? 'Validar Servicio' : 'Procesar Ingreso'; ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <?php } else { ?>
                <!-- ============================================ -->
                <!-- NO HAY PRODUCTOS/SERVICIOS PENDIENTES -->
                <!-- ============================================ -->
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Estado de la Orden</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br>
                        <div class="alert alert-success text-center">
                            <h4>
                                <i class="fa fa-check-circle"></i> 
                                <?php if ($es_servicio): ?>
                                    Todos los servicios han sido validados
                                <?php else: ?>
                                    Todos los productos han sido ingresados
                                <?php endif; ?>
                            </h4>
                            <p>
                                <?php if ($es_servicio): ?>
                                    No hay servicios pendientes de validación para esta orden.
                                <?php else: ?>
                                    No hay productos pendientes de ingreso para esta orden de compra.
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->

<script>
//  VARIABLE GLOBAL
const ES_SERVICIO = <?php echo $es_servicio ? 'true' : 'false'; ?>;

// ============================================
// FUNCIONES PARA MANEJO DE PRODUCTOS
// ============================================
function toggleProducto(checkbox) {
    const productId = checkbox.dataset.producto;
    const cantidadInput = document.querySelector(`input[name="cantidades[${productId}]"]`);
    
    if (checkbox.checked) {
        cantidadInput.value = checkbox.dataset.pendiente;
        cantidadInput.style.backgroundColor = '#fff';
        cantidadInput.focus();
    } else {
        cantidadInput.value = '';
        cantidadInput.style.backgroundColor = '#f8f9fa';
    }
}

function validarCantidad(input, maxCantidad) {
    const value = parseFloat(input.value);
    
    if (value > maxCantidad) {
        Swal.fire({
            icon: 'warning',
            title: 'Cantidad inválida',
            text: `La cantidad ingresada no puede ser mayor a la cantidad pendiente (${maxCantidad})`,
        });
        input.value = maxCantidad;
    }
    
    if (value <= 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Cantidad inválida',
            text: 'La cantidad debe ser mayor a 0',
        });
        input.value = 0.01;
    }
    
    const productId = input.name.match(/\[(\d+)\]/)[1];
    const checkbox = document.querySelector(`input[data-producto="${productId}"]`);
    checkbox.checked = value > 0;
}

function procesarIngreso() {
    // VALIDACIÓN 1: Verificar que hay documentos
    const contenedorDocs = document.getElementById('contenedor-documentos');
    const hayDocumentos = contenedorDocs.querySelector('table') !== null;
    
    if (!hayDocumentos) {
        Swal.fire({
            icon: 'error',
            title: 'Documentos requeridos',
            html: '<p><strong>NO PUEDE PROCESAR EL INGRESO SIN DOCUMENTOS.</strong></p>' +
                  '<p>Debe adjuntar al menos un documento (guía de remisión, factura, etc.) ' +
                  'antes de registrar el ingreso de productos.</p>' +
                  '<p>Por favor, use el botón <strong>"Subir Documento"</strong> en la sección correspondiente.</p>',
            confirmButtonColor: '#dc3545',
        });
        
        document.querySelector('#lista-documentos-ingreso').scrollIntoView({ 
            behavior: 'smooth', 
            block: 'center' 
        });
        
        return false;
    }
    
    // 🆕 PARA SERVICIOS: Validación automática (sin checkboxes)
    if (ES_SERVICIO) {
        Swal.fire({
            title: '¿Confirmar validación?',
            text: `¿Está seguro de que desea validar todos los servicios?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, validar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                enviarFormularioAjax();
            }
        });
        return;
    }
    
    // VALIDACIÓN 2: Productos seleccionados (SOLO MATERIALES)
    const checkboxesSeleccionados = document.querySelectorAll('.producto-checkbox:checked');
    
    if (checkboxesSeleccionados.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Selección requerida',
            text: 'Debe marcar al menos un producto para ingresar',
        });
        return false;
    }
    
    // VALIDACIÓN 3: Cantidades válidas (SOLO MATERIALES)
    let cantidadesValidas = true;
    
    checkboxesSeleccionados.forEach(checkbox => {
        const productId = checkbox.dataset.producto;
        const cantidadInput = document.querySelector(`input[name="cantidades[${productId}]"]`);
        const cantidad = parseFloat(cantidadInput.value);
        
        if (!cantidad || cantidad <= 0) {
            cantidadesValidas = false;
        }
    });
    
    if (!cantidadesValidas) {
        Swal.fire({
            icon: 'error',
            title: 'Cantidades inválidas',
            text: 'Todos los productos seleccionados deben tener una cantidad válida mayor a 0',
        });
        return false;
    }
    
    // CONFIRMACIÓN
    Swal.fire({
        title: '¿Confirmar ingreso?',
        text: `¿Está seguro de que desea agregar ${checkboxesSeleccionados.length} producto(s) al stock?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, procesar ingreso',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            enviarFormularioAjax();
        }
    });
}

function enviarFormularioAjax() {
    Swal.fire({
        title: 'Procesando ingreso...',
        text: 'Por favor espere mientras se procesan los productos',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    const formData = new FormData();
    formData.append('id_compra', <?php echo $id_compra; ?>);
    formData.append('es_servicio', ES_SERVICIO ? '1' : '0'); // 🆕
    
    if (ES_SERVICIO) {
        // 🆕 PARA SERVICIOS: Enviar todos los hidden inputs automáticamente
        const hiddenProductos = document.querySelectorAll('input[name="productos_seleccionados[]"]');
        const hiddenCantidades = document.querySelectorAll('input[name^="cantidades["]');
        
        hiddenProductos.forEach(input => {
            formData.append('productos_seleccionados[]', input.value);
        });
        
        hiddenCantidades.forEach(input => {
            const matches = input.name.match(/cantidades\[(\d+)\]/);
            if (matches) {
                formData.append(input.name, input.value);
            }
        });
    } else {
        // PARA MATERIALES: Enviar solo los seleccionados
        const checkboxesSeleccionados = document.querySelectorAll('.producto-checkbox:checked');
        
        checkboxesSeleccionados.forEach(checkbox => {
            const productId = checkbox.dataset.producto;
            const cantidadInput = document.querySelector(`input[name="cantidades[${productId}]"]`);
            const cantidad = parseFloat(cantidadInput.value) || 0;
            
            if (cantidad > 0) {
                formData.append(`productos_seleccionados[]`, productId);
                formData.append(`cantidades[${productId}]`, cantidad);
            }
        });
    }
    
    fetch('ingresos_procesar.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.tipo_mensaje === 'success') {
            Swal.fire({
                icon: 'success',
                title: '¡Ingreso exitoso!',
                text: data.mensaje,
                confirmButtonColor: '#28a745',
            }).then(() => {
                window.location.reload();
            });
        } else if (data.tipo_mensaje === 'warning') {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: data.mensaje,
                confirmButtonColor: '#ffc107',
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error en el ingreso',
                text: data.mensaje,
                confirmButtonColor: '#dc3545',
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor',
            confirmButtonColor: '#dc3545',
        });
    });
}

// ============================================
// FUNCIONES PARA SUBIR Y ELIMINAR DOCUMENTOS
// ============================================
function subirDocumentoIngreso() {
    const archivo = document.getElementById('documento_ingreso').files[0];
    
    if (!archivo) {
        Swal.fire({
            icon: 'warning',
            title: 'Archivo requerido',
            text: 'Debe seleccionar un archivo para subir',
        });
        return;
    }

    if (archivo.size > 5242880) {
        Swal.fire({
            icon: 'error',
            title: 'Archivo muy grande',
            text: 'El archivo no debe superar los 5MB',
        });
        return;
    }

    const extensionesPermitidas = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
    const extension = archivo.name.split('.').pop().toLowerCase();
    
    if (!extensionesPermitidas.includes(extension)) {
        Swal.fire({
            icon: 'error',
            title: 'Formato no permitido',
            text: 'Solo se permiten archivos PDF, JPG, PNG, DOC o DOCX',
        });
        return;
    }

    const formData = new FormData();
    formData.append('entidad', 'ingresos');
    formData.append('id_entidad', <?php echo $id_compra; ?>); 
    formData.append('documento', archivo);

    Swal.fire({
        title: 'Subiendo documento...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch('compras_subir_documentos.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.tipo_mensaje === 'success') {
            Swal.fire({
                icon: 'success',
                title: '¡Documento cargado!',
                text: data.mensaje,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error al cargar',
                text: data.mensaje,
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor',
        });
    });
}

function eliminarDocumentoIngreso(id_doc) {
    Swal.fire({
        title: '¿Eliminar documento?',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('compras_eliminar_documento.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id_doc=' + id_doc
            })
            .then(response => response.json())
            .then(data => {
                if (data.tipo_mensaje === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        text: data.mensaje,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.mensaje
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor.'
                });
            });
        }
    });
}
</script>