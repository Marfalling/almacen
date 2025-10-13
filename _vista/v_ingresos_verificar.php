<?php
// Vista para verificar ingreso de productos - v_ingresos_verificar.php
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Verificar Ingreso<small> - Orden de Compra <strong>#<?php echo $comprax['id_compra']; ?></strong></small></h3>
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
                <!-- Panel de Información de la Orden -->
               <div class="x_panel shadow-sm">
                    <div class="x_title">
                        <h2>Información de la Orden de Compra</h2>
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
                                    <?php echo $comprax['id_compra']; ?>
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


                <?php if (!empty($productos_pendientes)) { ?>
                <!-- Panel de Productos Pendientes -->
                <div class="x_panel">
                    <div class="x_title">
                        <div class="row">
                            <div class="col-sm-8">
                                <h2>Productos Pendientes de Ingreso <small>(<?php echo count($productos_pendientes); ?> productos)</small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-4 text-right">
                                <button type="button" onclick="procesarIngreso()" class="btn btn-success btn-sm">
                                    <i class="fa fa-plus-circle"></i> Agregar a Stock
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="x_content">
                        
                        <form id="form-ingreso" class="form-horizontal form-label-left" method="POST" onsubmit="return validarFormulario()">
                            
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card-box table-responsive">
                                        <table class="table table-striped table-bordered" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 5%; text-align: center;">#</th>
                                                    <th style="width: 15%;">Código</th>
                                                    <th style="width: 25%;">Producto</th>
                                                    <th style="width: 8%;">Unidad</th>
                                                    <th style="width: 10%; text-align: center;">Compra</th>
                                                    <th style="width: 10%; text-align: center;">Ingresado</th>
                                                    <th style="width: 10%; text-align: center;">Pendiente</th>
                                                    <th style="width: 12%; text-align: center;">Cantidad a Ingresar</th>
                                                    <th style="width: 5%; text-align: center;">Sel.</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $contador = 1;
                                                foreach ($productos_pendientes as $producto) { 
                                                ?>
                                                <tr>
                                                    <td style="text-align: center; font-weight: bold; font-size: 16px;"><?php echo $contador; ?></td>
                                                    <td><strong><?php echo $producto['cod_material']; ?></strong></td>
                                                    <td><?php echo $producto['nom_producto']; ?></td>
                                                    <td style="text-align: center;"><?php echo $producto['nom_unidad_medida']; ?></td>
                                                    <td style="text-align: center;">
                                                        <span class="badge badge-secondary badge_size"><?php echo number_format($producto['cant_compra_detalle'], 2); ?></span>
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <span class="badge badge-success badge_size"><?php echo number_format($producto['cantidad_ingresada'], 2); ?></span>
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <span class="badge badge-warning badge_size"><?php echo number_format($producto['cantidad_pendiente'], 2); ?></span>
                                                    </td>
                                                    
                                                    <td style="text-align: center;">
                                                        <input type="number" 
                                                            name="cantidades[<?php echo $producto['id_producto']; ?>]"
                                                            class="form-control cantidad-input text-center"
                                                            min="0.01" 
                                                            max="<?php echo $producto['cantidad_pendiente']; ?>"
                                                            step="0.01"
                                                            placeholder=""
                                                            onchange="validarCantidad(this, <?php echo $producto['cantidad_pendiente']; ?>)"
                                                            style="">
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
                    <!-- NUEVA SECCIÓN: DOCUMENTOS OBLIGATORIOS -->
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
                                <strong>Importante:</strong> Debe adjuntar el documento (guía) para poder procesar el ingreso.
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
                                    <div class="alert alert-warning text-center">
                                        <i class="fa fa-exclamation-triangle"></i> 
                                        Aún no se han cargado documentos
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php } else { ?>
                <!-- Panel cuando no hay productos pendientes -->
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Estado de la Orden <small></small></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br>
                        <div class="alert alert-success text-center">
                            <h4><i class="fa fa-check-circle"></i> Todos los productos han sido ingresados</h4>
                            <p>No hay productos pendientes de ingreso para esta orden de compra.</p>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>


<script>
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
    
    // Auto-marcar el checkbox si hay cantidad
    const productId = input.name.match(/\[(\d+)\]/)[1];
    const checkbox = document.querySelector(`input[data-producto="${productId}"]`);
    checkbox.checked = value > 0;
}

function procesarIngreso() {
    const checkboxesSeleccionados = document.querySelectorAll('.producto-checkbox:checked');
    
    if (checkboxesSeleccionados.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Selección requerida',
            text: 'Debe marcar al menos un producto para ingresar',
        });
        return false;
    }
    
    let cantidadesValidas = true;
    let productos_con_error = [];
    
    checkboxesSeleccionados.forEach(checkbox => {
        const productId = checkbox.dataset.producto;
        const cantidadInput = document.querySelector(`input[name="cantidades[${productId}]"]`);
        const cantidad = parseFloat(cantidadInput.value);
        
        if (!cantidad || cantidad <= 0) {
            cantidadesValidas = false;
            productos_con_error.push(productId);
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
    
    // Confirmación antes de procesar
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
    // Mostrar loading
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
    
    // Recopilar datos manualmente solo de productos seleccionados
    const formData = new FormData();
    formData.append('id_compra', <?php echo $id_compra; ?>);
    
    // Solo enviar cantidades de productos seleccionados
    const checkboxesSeleccionados = document.querySelectorAll('.producto-checkbox:checked');
    
    checkboxesSeleccionados.forEach(checkbox => {
        const productId = checkbox.dataset.producto;
        const cantidadInput = document.querySelector(`input[name="cantidades[${productId}]"]`);
        const cantidad = parseFloat(cantidadInput.value) || 0;
        
        if (cantidad > 0) {
            formData.append(`cantidades[${productId}]`, cantidad);
        }
    });
    
    // Debug: Ver qué datos se están enviando
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
    
    $.ajax({
        url: 'ingresos_procesar.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.tipo_mensaje === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: '¡Ingreso exitoso!',
                    text: response.mensaje,
                    confirmButtonColor: '#28a745',
                }).then(() => {
                    window.location.reload();
                });
            } else if (response.tipo_mensaje === 'warning') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Ingreso parcial',
                    text: response.mensaje,
                    confirmButtonColor: '#ffc107',
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error en el ingreso',
                    text: response.mensaje,
                    confirmButtonColor: '#dc3545',
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX:', error);
            console.error('Status:', status);
            console.error('Response:', xhr.responseText);
            
            // Intentar parsear la respuesta como JSON para mostrar error específico
            let errorMessage = 'No se pudo conectar con el servidor.';
            try {
                const errorResponse = JSON.parse(xhr.responseText);
                if (errorResponse.mensaje) {
                    errorMessage = errorResponse.mensaje;
                }
            } catch (e) {
                // Si no se puede parsear como JSON, mostrar texto completo si es corto
                if (xhr.responseText && xhr.responseText.length < 200) {
                    errorMessage = xhr.responseText;
                }
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: errorMessage,
                confirmButtonColor: '#dc3545',
            });
        }
    });
}
</script>

<script>
// Variable global para controlar documentos subidos
let documentosSubidos = [];

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

// ============================================
// NUEVA FUNCIÓN: SUBIR DOCUMENTO
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

    // Validar tamaño (5MB máximo)
    if (archivo.size > 5242880) {
        Swal.fire({
            icon: 'error',
            title: 'Archivo muy grande',
            text: 'El archivo no debe superar los 5MB',
        });
        return;
    }

    // Validar extensión
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

    // Preparar FormData
    const formData = new FormData();
    formData.append('entidad', 'ingresos');
    formData.append('id_entidad', <?php echo $id_compra; ?>); 
    formData.append('documento', archivo);

    // Mostrar loading
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

    // Enviar por AJAX
    $.ajax({
        url: 'compras_subir_documentos.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.tipo_mensaje === 'success') {
                documentosSubidos.push({
                    nombre: archivo.name,
                    timestamp: new Date().getTime()
                });
                
                actualizarListaDocumentos();
                document.getElementById('documento_ingreso').value = '';
                
                Swal.fire({
                    icon: 'success',
                    title: '¡Documento cargado!',
                    text: response.mensaje,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al cargar',
                    text: response.mensaje,
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor',
            });
        }
    });
}

// ============================================
// ACTUALIZAR LISTA DE DOCUMENTOS
// ============================================
function actualizarListaDocumentos() {
    const contenedor = document.getElementById('contenedor-documentos');
    
    if (documentosSubidos.length === 0) {
        contenedor.innerHTML = `
            <div class="alert alert-warning text-center">
                <i class="fa fa-exclamation-triangle"></i> 
                Aún no se han cargado documentos
            </div>
        `;
    } else {
        let html = '<div class="table-responsive"><table class="table table-bordered table-sm">';
        html += '<thead><tr><th>#</th><th>Archivo</th><th>Estado</th></tr></thead><tbody>';
        
        documentosSubidos.forEach((doc, index) => {
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td><i class="fa fa-file"></i> ${doc.nombre}</td>
                    <td><span class="badge badge-success">Cargado</span></td>
                </tr>
            `;
        });
        
        html += '</tbody></table></div>';
        contenedor.innerHTML = html;
    }
}

// ============================================
// PROCESAR INGRESO (MODIFICADA - CON VALIDACIÓN DE DOCUMENTOS)
// ============================================
function procesarIngreso() {
    //  VALIDACIÓN 1: Documentos obligatorios
    if (documentosSubidos.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Documentos requeridos',
            text: 'Debe cargar al menos un documento (guía, factura, etc.) antes de procesar el ingreso',
            confirmButtonColor: '#dc3545',
        });
        
        // Scroll hacia la sección de documentos
        document.querySelector('#lista-documentos-ingreso').scrollIntoView({ 
            behavior: 'smooth', 
            block: 'center' 
        });
        
        return false;
    }

    // VALIDACIÓN 2: Productos seleccionados
    const checkboxesSeleccionados = document.querySelectorAll('.producto-checkbox:checked');
    
    if (checkboxesSeleccionados.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Selección requerida',
            text: 'Debe marcar al menos un producto para ingresar',
        });
        return false;
    }
    
    //  VALIDACIÓN 3: Cantidades válidas
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
    
    //  CONFIRMACIÓN
    Swal.fire({
        title: '¿Confirmar ingreso?',
        html: `
            <p>¿Está seguro de que desea procesar este ingreso?</p>
            <div class="text-left mt-3" style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
                <strong><i class="fa fa-check-circle text-success"></i> Productos a ingresar:</strong> ${checkboxesSeleccionados.length}<br>
                <strong><i class="fa fa-file text-primary"></i> Documentos adjuntos:</strong> ${documentosSubidos.length}
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fa fa-check"></i> Sí, procesar ingreso',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            enviarFormularioAjax();
        }
    });
}

// ============================================
// ENVIAR FORMULARIO (SIN CAMBIOS)
// ============================================
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
    
    const checkboxesSeleccionados = document.querySelectorAll('.producto-checkbox:checked');
    
    checkboxesSeleccionados.forEach(checkbox => {
        const productId = checkbox.dataset.producto;
        const cantidadInput = document.querySelector(`input[name="cantidades[${productId}]"]`);
        const cantidad = parseFloat(cantidadInput.value) || 0;
        
        if (cantidad > 0) {
            formData.append(`cantidades[${productId}]`, cantidad);
        }
    });
    
    $.ajax({
        url: 'ingresos_procesar.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.tipo_mensaje === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: '¡Ingreso exitoso!',
                    text: response.mensaje,
                    confirmButtonColor: '#28a745',
                }).then(() => {
                    window.location.reload();
                });
            } else if (response.tipo_mensaje === 'warning') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Ingreso parcial',
                    text: response.mensaje,
                    confirmButtonColor: '#ffc107',
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error en el ingreso',
                    text: response.mensaje,
                    confirmButtonColor: '#dc3545',
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX:', error);
            
            let errorMessage = 'No se pudo conectar con el servidor.';
            try {
                const errorResponse = JSON.parse(xhr.responseText);
                if (errorResponse.mensaje) {
                    errorMessage = errorResponse.mensaje;
                }
            } catch (e) {
                if (xhr.responseText && xhr.responseText.length < 200) {
                    errorMessage = xhr.responseText;
                }
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: errorMessage,
                confirmButtonColor: '#dc3545',
            });
        }
    });
}

// ============================================
// INICIALIZACIÓN
// ============================================
$(document).ready(function() {
    // Cargar documentos existentes si hay (para cuando se recarga la página)
    cargarDocumentosExistentes();
});

function cargarDocumentosExistentes() {
    $.ajax({
        url: 'ingresos_obtener_documentos.php',
        type: 'POST',
        data: { id_compra: <?php echo $id_compra; ?> },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.documentos.length > 0) {
                documentosSubidos = response.documentos.map(doc => ({
                    nombre: doc.documento,
                    timestamp: new Date(doc.fec_subida).getTime()
                }));
                actualizarListaDocumentos();
            }
        }
    });
}
</script>