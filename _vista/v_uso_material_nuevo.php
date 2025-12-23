<?php 
//=======================================================================
// VISTA: v_uso_material_nuevo.php
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Nuevo Uso de Material</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Datos del Uso de Material <small></small></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br>
                        <form class="form-horizontal form-label-left" action="uso_material_nuevo.php" method="post" enctype="multipart/form-data">
                            
                            <!-- Información básica -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Almacén <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="id_almacen" id="id_almacen" class="form-control" required>
                                        <option value="">Seleccionar</option>
                                        <?php foreach ($almacenes as $almacen) { ?>
                                            <option value="<?php echo $almacen['id_almacen']; ?>" 
                                                    data-cliente="<?php echo isset($almacen['nom_cliente']) ? htmlspecialchars($almacen['nom_cliente']) : '-'; ?>"
                                                    data-obra="<?php echo isset($almacen['nom_obra']) ? htmlspecialchars($almacen['nom_obra']) : '-'; ?>">
                                                <?php echo $almacen['nom_almacen']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <small class="form-text text-muted" id="info-almacen" style="display: none;">
                                        <strong>Cliente/Obra:</strong> <span id="texto-cliente-obra"></span>
                                    </small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Ubicación <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="id_ubicacion" class="form-control" required>
                                        <option value="">Seleccionar</option>
                                        <?php foreach ($ubicaciones as $ubicacion) { ?>
                                            <option value="<?php echo $ubicacion['id_ubicacion']; ?>">
                                                <?php echo $ubicacion['nom_ubicacion']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Solicitante -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Solicitante <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="id_solicitante" id="id_solicitante" class="form-control" required>
                                        <option value="">Seleccionar</option>
                                        <?php foreach ($personal as $persona) { ?>
                                            <option value="<?php echo $persona['id_personal']; ?>" 
                                                    data-centro-costo="<?php echo isset($persona['id_centro_costo']) ? $persona['id_centro_costo'] : ''; ?>"
                                                    data-centro-costo-nombre="<?php echo isset($persona['nom_centro_costo']) ? htmlspecialchars($persona['nom_centro_costo']) : 'Sin centro de costo asignado'; ?>"
                                                <?php echo ($persona['id_personal'] == $id_personal) ? 'selected' : ''; ?>>
                                                <?php echo $persona['nom_personal']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <!-- NUEVO CAMPO: Centro de Costo del Solicitante -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Centro de Costos (Solicitante):</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" id="texto_centro_costo_solicitante" class="form-control" 
                                        value="Seleccione un solicitante" 
                                        readonly style="background-color: #f9f9f9;">
                                    <input type="hidden" name="id_solicitante_centro_costo" id="id_solicitante_centro_costo" value="">
                                    <small class="text-muted" id="mensaje_sin_centro_solicitante" style="display: none;">
                                        El solicitante no tiene un centro de costo asignado.
                                    </small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Registrado por:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo $usuario_sesion; ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Centro de Costos (Registrador):</label>
                                <div class="col-md-9 col-sm-9">
                                    <?php if ($centro_costo_usuario) { ?>
                                        <input type="text" class="form-control" 
                                            value="<?php echo htmlspecialchars($centro_costo_usuario['nom_centro_costo']); ?>" 
                                            readonly style="background-color: #f9f9f9;">
                                        <input type="hidden" name="id_registrador_centro_costo" 
                                            value="<?php echo $centro_costo_usuario['id_centro_costo']; ?>">
                                    <?php } else { ?>
                                        <input type="text" class="form-control" 
                                            value="Sin centro de costo asignado" 
                                            readonly style="background-color: #f9f9f9;">
                                        <small class="text-danger">No tienes un área asignada. Contacta con el administrador.</small>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <!-- Sección de materiales -->
                            <div class="x_title">
                                <h4>Materiales utilizados <small></small></h4>
                                <div class="clearfix"></div>
                            </div>

                            <div id="contenedor-materiales">
                                <div class="material-item border p-3 mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Material <span class="text-danger">*</span>:</label>
                                            <div class="input-group">
                                                <input type="text" name="descripcion[]" class="form-control" placeholder="Buscar material" readonly required>
                                                <input type="hidden" name="id_producto[]" value="">
                                                <button title="Buscar Material" data-toggle="tooltip" onclick="buscarMaterial(this)" class="btn btn-secondary btn-xs" type="button">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <label>Unidad:</label>
                                            <input type="text" name="unidad[]" class="form-control" readonly>
                                        </div>

                                        <div class="col-md-3">
                                            <label>Cantidad <span class="text-danger">*</span>:</label>
                                            <input type="number" name="cantidad[]" class="form-control" step="0.01" min="0.01" required>
                                        </div>

                                        <input type="hidden" name="stock_disponible[]" value="">
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <label>Observaciones:</label>
                                            <input type="text" name="observaciones[]" class="form-control" placeholder="Observaciones del uso">
                                        </div>
                                        <div class="col-md-6">
                                            <label>Centros de Costo <span class="text-danger">*</span>:</label>
                                            <select name="centros_costo[0][]" class="form-control select2-centros-costo-detalle" multiple required>
                                                <?php foreach ($centros_costo as $centro) { ?>
                                                    <option value="<?php echo $centro['id_centro_costo']; ?>">
                                                        <?php echo $centro['nom_centro_costo']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <small class="form-text text-muted">
                                                <i class="fa fa-info-circle"></i> Seleccione uno o más centros de costo para este material.
                                            </small>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Adjuntar Evidencias <span class="text-danger">*</span>:</label>
                                            <input type="file" name="archivos_0[]" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" required>
                                            <small class="form-text text-muted">Formatos permitidos: PDF, JPG, PNG, DOC, XLS. Máximo 5MB por archivo.</small>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger btn-sm eliminar-material" style="display: none;">
                                                <i class="fa fa-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="button" id="agregar-material" class="btn btn-info btn-sm">
                                    <i class="fa fa-plus"></i> Agregar Material
                                </button>
                            </div>

                            <div class="ln_solid"></div>

                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 offset-md-8">
                                    <a href="uso_material_mostrar.php" class="btn btn-outline-danger btn-block">
                                        <i class="bi bi-x-square"></i> Cancelar
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <button type="submit" name="registrar" id="btn_registrar" class="btn btn-success btn-block"><i class="bi bi-pencil-square"></i> Registrar</button>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12 col-sm-12">
                                    <p><span class="text-danger">*</span> Los campos con (<span class="text-danger">*</span>) son obligatorios.</p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->

<!-- Modal para buscar productos  -->
<div class="modal fade" id="buscar_producto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Buscar Material</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Filtrar por Almacén y Ubicación:</label>
                    <select id="filtro_almacen_ubicacion" class="form-control">
                        <option value="">Seleccione almacén y ubicación para ver stock</option>
                    </select>
                </div>
                <div class="table-responsive">
                    <table id="datatable_producto" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Tipo</th>
                                <th>Unidad de Medida</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Stock Disponible</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let currentSearchButton = null;
let almacenUbicacionActual = { almacen: '', ubicacion: '' };
let formularioModificado = false;
let productosSeleccionados = [];

// Detectar cambios en el formulario
function marcarFormularioComoModificado() {
    formularioModificado = true;
}

// Verificar si hay productos seleccionados
function hayProductosSeleccionados() {
    const materialesItems = document.querySelectorAll('.material-item');
    for (let item of materialesItems) {
        const inputDescripcion = item.querySelector('input[name="descripcion[]"]');
        if (inputDescripcion && inputDescripcion.value.trim() !== '') {
            return true;
        }
    }
    return false;
}

// Limpiar todos los productos seleccionados
function limpiarProductosSeleccionados() {
    const materialesItems = document.querySelectorAll('.material-item');
    
    // Eliminar todos los items adicionales, mantener solo el primero
    for (let i = 1; i < materialesItems.length; i++) {
        materialesItems[i].remove();
    }
    
    // Limpiar el primer item de material
    const primerMaterial = document.querySelector('.material-item');
    if (primerMaterial) {
        const inputs = primerMaterial.querySelectorAll('input[name="descripcion[]"], input[name="id_producto[]"], input[name="unidad[]"], input[name="cantidad[]"], input[name="observaciones[]"]');
        inputs.forEach(input => {
            input.value = '';
        });
        
        // Ocultar el botón eliminar del primer item
        const btnEliminar = primerMaterial.querySelector('.eliminar-material');
        if (btnEliminar) {
            btnEliminar.style.display = 'none';
        }
    }
    
    productosSeleccionados = [];
    formularioModificado = false;
}

// Función para buscar material
function buscarMaterial(button) {
    const selectAlmacen = document.querySelector('select[name="id_almacen"]');
    const selectUbicacion = document.querySelector('select[name="id_ubicacion"]');
    
    if (!selectAlmacen.value || !selectUbicacion.value) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Datos requeridos',
                text: 'Debe seleccionar almacén y ubicación antes de buscar materiales.',
                confirmButtonText: 'Entendido'
            });
        } else {
            alert('Debe seleccionar almacén y ubicación antes de buscar materiales.');
        }
        return;
    }
    
    currentSearchButton = button;
    
    const filtroSelect = document.getElementById('filtro_almacen_ubicacion');
    const almacenTexto = selectAlmacen.options[selectAlmacen.selectedIndex].text;
    const ubicacionTexto = selectUbicacion.options[selectUbicacion.selectedIndex].text;
    
    filtroSelect.innerHTML = `<option value="${selectAlmacen.value}_${selectUbicacion.value}">${almacenTexto} - ${ubicacionTexto}</option>`;
    filtroSelect.value = `${selectAlmacen.value}_${selectUbicacion.value}`;
    
    $('#buscar_producto').modal('show');
    cargarProductos(selectAlmacen.value, selectUbicacion.value);
}

// Función para cargar productos
function cargarProductos(idAlmacen, idUbicacion) {
    if ($.fn.dataTable.isDataTable('#datatable_producto')) {
        $('#datatable_producto').DataTable().destroy();
    }

    $('#datatable_producto').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "ajax": {
            "url": "uso_material_mostrar_modal.php",
            "type": "POST",
            "data": function(d) {
                d.id_almacen = idAlmacen;
                d.id_ubicacion = idUbicacion;
                return d;
            }
        },
        "columns": [
            { "title": "Código" },
            { "title": "Producto" },
            { "title": "Tipo" },
            { "title": "Unidad de Medida" },
            { "title": "Marca" },
            { "title": "Modelo" },
            { "title": "Stock Disponible" },
            { "title": "Acción" }
        ],
        "order": [[1, 'asc']],
        "pageLength": 10,
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron materiales con stock disponible",
            "info": "Mostrando página _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros en total)",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Último", 
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "loadingRecords": "Cargando...",
            "processing": "Procesando..."
        }
    });

    $('#datatable_producto').on('draw.dt', function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
}

// Función para seleccionar producto
function seleccionarProducto(idProducto, nombreProducto, unidadMedida, stockDisponible) {
    // Buscar si el producto ya está en la lista
    const materialItems = document.querySelectorAll('.material-item');
    let productoExistente = null;

    materialItems.forEach(item => {
        const inputId = item.querySelector('input[name="id_producto[]"]');
        if (inputId && parseInt(inputId.value) === parseInt(idProducto)) {
            productoExistente = item;
        }
    });

    if (productoExistente) {
        // Producto ya existe → resaltarlo visualmente
        productoExistente.classList.add('duplicado-resaltado');
        productoExistente.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Quitar resaltado después de unos segundos
        setTimeout(() => productoExistente.classList.remove('duplicado-resaltado'), 2000);

        // Cerrar modal y mostrar aviso visual (sin alert)
        $('#buscar_producto').modal('hide');
        return; // Detiene aquí, no lo agrega de nuevo
    }

    if (currentSearchButton) {
        let materialItem = currentSearchButton.closest('.material-item');
        
        if (materialItem) {
            let inputDescripcion = materialItem.querySelector('input[name="descripcion[]"]');
            let inputIdProducto = materialItem.querySelector('input[name="id_producto[]"]');
            let inputUnidad = materialItem.querySelector('input[name="unidad[]"]');
            let inputStock = materialItem.querySelector('input[name="stock_disponible[]"]');
            
            if (inputDescripcion) inputDescripcion.value = nombreProducto;
            if (inputIdProducto) inputIdProducto.value = idProducto;
            if (inputUnidad) inputUnidad.value = unidadMedida;
            inputStock.value = stockDisponible;
        }
    }
    
    $('#buscar_producto').modal('hide');
    currentSearchButton = null;
    
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Material seleccionado',
            text: 'El material "' + nombreProducto + '" ha sido seleccionado.',
            showConfirmButton: false,
            timer: 2000
        });
    }
}

// Función para manejar cambios en almacén
function manejarCambioAlmacen(elemento) {
    elemento.addEventListener('focus', function() {
        almacenUbicacionActual.almacen = this.value;
    });
    
    elemento.addEventListener('change', function() {
        const valorActual = this.value;
        const hayProductos = hayProductosSeleccionados();
        
        if (hayProductos && almacenUbicacionActual.almacen !== valorActual) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '¿Cambiar almacén?',
                    text: 'Si cambias el almacén, todos los materiales seleccionados se eliminarán.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, cambiar almacén',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        limpiarProductosSeleccionados();
                        almacenUbicacionActual.almacen = valorActual;
                        
                        Swal.fire({
                            title: 'Almacén cambiado',
                            text: 'Los materiales seleccionados han sido eliminados.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        this.value = almacenUbicacionActual.almacen;
                    }
                });
            } else {
                if (confirm('Si cambias el almacén, todos los materiales seleccionados se eliminarán. ¿Continuar?')) {
                    limpiarProductosSeleccionados();
                    almacenUbicacionActual.almacen = valorActual;
                    alert('Almacén cambiado. Materiales eliminados.');
                } else {
                    this.value = almacenUbicacionActual.almacen;
                }
            }
        } else {
            almacenUbicacionActual.almacen = valorActual;
        }
    });
}

// Función para manejar cambios en ubicación
function manejarCambioUbicacion(elemento) {
    elemento.addEventListener('focus', function() {
        almacenUbicacionActual.ubicacion = this.value;
    });
    
    elemento.addEventListener('change', function() {
        const valorActual = this.value;
        const hayProductos = hayProductosSeleccionados();
        
        if (hayProductos && almacenUbicacionActual.ubicacion !== valorActual) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '¿Cambiar ubicación?',
                    text: 'Si cambias la ubicación, todos los materiales seleccionados se eliminarán.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, cambiar ubicación',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        limpiarProductosSeleccionados();
                        almacenUbicacionActual.ubicacion = valorActual;
                        
                        Swal.fire({
                            title: 'Ubicación cambiada',
                            text: 'Los materiales seleccionados han sido eliminados.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        this.value = almacenUbicacionActual.ubicacion;
                    }
                });
            } else {
                if (confirm('Si cambias la ubicación, todos los materiales seleccionados se eliminarán. ¿Continuar?')) {
                    limpiarProductosSeleccionados();
                    almacenUbicacionActual.ubicacion = valorActual;
                    alert('Ubicación cambiada. Materiales eliminados.');
                } else {
                    this.value = almacenUbicacionActual.ubicacion;
                }
            }
        } else {
            almacenUbicacionActual.ubicacion = valorActual;
        }
    });
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    let contadorMateriales = 1;
    
    // Detectar cambios en campos del formulario
    const todosLosCampos = document.querySelectorAll('input, textarea, select');
    todosLosCampos.forEach(campo => {
        if (campo.name !== 'id_almacen' && campo.name !== 'id_ubicacion') {
            campo.addEventListener('change', marcarFormularioComoModificado);
            campo.addEventListener('input', marcarFormularioComoModificado);
        }
    });
    
    // Controlar cambios en almacén y ubicación
    const selectAlmacen = document.querySelector('select[name="id_almacen"]');
    const selectUbicacion = document.querySelector('select[name="id_ubicacion"]');
    const infoAlmacen = document.getElementById('info-almacen');
    const textoClienteObra = document.getElementById('texto-cliente-obra');

    // Mostrar cliente/obra del almacén seleccionado (CON SELECT2)
    if (selectAlmacen && infoAlmacen && textoClienteObra) {
        $(selectAlmacen).on('select2:select', function(e) {
            const selectedOption = e.params.data.element;
            
            if (this.value) {
                const cliente = selectedOption.getAttribute('data-cliente') || '-';
                const obra = selectedOption.getAttribute('data-obra') || '-';
                
                textoClienteObra.textContent = cliente + ' / ' + obra;
                infoAlmacen.style.display = 'block';
            } else {
                infoAlmacen.style.display = 'none';
            }
        });
        
        $(selectAlmacen).on('select2:clear', function() {
            infoAlmacen.style.display = 'none';
        });
    }

    // Aplicar los event listeners específicos
    if (selectAlmacen) manejarCambioAlmacen(selectAlmacen);
    if (selectUbicacion) manejarCambioUbicacion(selectUbicacion);
    
    // ============================================
    // ✅ DETECTAR CAMBIO EN SOLICITANTE Y MOSTRAR SU CENTRO DE COSTO
    // ============================================
    const selectSolicitante = document.getElementById('id_solicitante');
    const textoCentroCostoSolicitante = document.getElementById('texto_centro_costo_solicitante');
    const inputIdCentroCostoSolicitante = document.getElementById('id_solicitante_centro_costo');
    const mensajeSinCentro = document.getElementById('mensaje_sin_centro_solicitante');

    if (selectSolicitante) {
        // Función para actualizar el centro de costo del solicitante
        function actualizarCentroCostoSolicitante() {
            const selectedOption = selectSolicitante.options[selectSolicitante.selectedIndex];
            const idCentroCosto = selectedOption.getAttribute('data-centro-costo');
            const nombreCentroCosto = selectedOption.getAttribute('data-centro-costo-nombre');
            
            if (idCentroCosto && idCentroCosto !== '') {
                textoCentroCostoSolicitante.value = nombreCentroCosto;
                inputIdCentroCostoSolicitante.value = idCentroCosto;
                mensajeSinCentro.style.display = 'none';
                
                // ✅ APLICAR AUTOMÁTICAMENTE A TODOS LOS MATERIALES
                aplicarCentroCostoATodosMateriales(idCentroCosto);
            } else {
                textoCentroCostoSolicitante.value = 'Sin centro de costo asignado';
                inputIdCentroCostoSolicitante.value = '';
                mensajeSinCentro.style.display = 'block';
            }
        }
        
        // Evento change con Select2
        $(selectSolicitante).on('select2:select', function() {
            actualizarCentroCostoSolicitante();
        });
        
        // Inicializar con el solicitante predeterminado
        if (selectSolicitante.value) {
            actualizarCentroCostoSolicitante();
        }
    }
    
    // ============================================
    // ✅ FUNCIÓN PARA APLICAR CENTRO DE COSTO DEL SOLICITANTE A MATERIALES
    // ============================================
    function aplicarCentroCostoATodosMateriales(idCentroCosto) {
        if (!idCentroCosto) return;
        
        console.log("✅ Aplicando centro de costo del solicitante:", idCentroCosto);
        
        document.querySelectorAll('.material-item').forEach(item => {
            const selectCentros = item.querySelector('select.select2-centros-costo-detalle');
            if (selectCentros) {
                if ($(selectCentros).data('select2')) {
                    $(selectCentros).val([idCentroCosto]).trigger('change');
                } else {
                    $(selectCentros).select2({
                        placeholder: 'Seleccionar uno o más centros de costo...',
                        allowClear: true,
                        width: '100%',
                        multiple: true,
                        language: {
                            noResults: function () { return 'No se encontraron resultados'; }
                        }
                    });
                    setTimeout(() => {
                        $(selectCentros).val([idCentroCosto]).trigger('change');
                    }, 100);
                }
            }
        });
    }
    
    // Agregar nuevo material
    const btnAgregarMaterial = document.getElementById('agregar-material');
    if (btnAgregarMaterial) {
        btnAgregarMaterial.addEventListener('click', function(e) {
            e.preventDefault();
            
            const contenedor = document.getElementById('contenedor-materiales');
            const materialOriginal = contenedor.querySelector('.material-item');
            
            if (materialOriginal) {
                // 1. GUARDAR valores de Select2 antes de destruir
                const valoresOriginalesSelect2 = {};
                const selectsOriginales = materialOriginal.querySelectorAll(
                    'select.select2-centros-costo-detalle'
                );
                
                selectsOriginales.forEach((select, index) => {
                    if ($(select).data('select2')) {
                        valoresOriginalesSelect2[index] = $(select).val();
                    }
                });
                
                // 2. DESTRUIR Select2 en el original
                selectsOriginales.forEach(select => {
                    if ($(select).data('select2')) {
                        $(select).select2('destroy');
                    }
                });
                
                // 3. CLONAR el elemento
                const nuevoMaterial = materialOriginal.cloneNode(true);
                
                // 4. RESTAURAR Select2 en el ORIGINAL con sus valores
                selectsOriginales.forEach((select, index) => {
                    $(select).select2({
                        placeholder: 'Seleccionar uno o más centros de costo...',
                        allowClear: true,
                        width: '100%',
                        multiple: true,
                        language: {
                            noResults: function () { return 'No se encontraron resultados'; }
                        }
                    });
                    if (valoresOriginalesSelect2[index]) {
                        $(select).val(valoresOriginalesSelect2[index]).trigger('change');
                    }
                });
                
                // 5. LIMPIAR campos del nuevo material
                const inputs = nuevoMaterial.querySelectorAll('input, textarea');
                inputs.forEach(input => {
                    if (input.type === 'file') {
                        input.value = '';
                        input.name = `archivos_${contadorMateriales}[]`;
                    } else if (input.type === 'hidden') {
                        input.value = '';
                    } else {
                        input.value = '';
                    }
                });
                
                // 6. PREPARAR los selects del NUEVO material
                const selectsClonados = nuevoMaterial.querySelectorAll('select');
                selectsClonados.forEach(select => {
                    // Actualizar name con índice correcto
                    if ($(select).hasClass('select2-centros-costo-detalle')) {
                        select.name = `centros_costo[${contadorMateriales}][]`;
                    }
                    
                    // Remover clases de Select2 previo
                    $(select).removeClass('select2-hidden-accessible');
                    const select2Container = select.nextElementSibling;
                    if (select2Container && select2Container.classList.contains('select2')) {
                        select2Container.remove();
                    }
                    
                    // Limpiar selección
                    Array.from(select.options).forEach(option => {
                        option.selected = false;
                    });
                    select.selectedIndex = -1;
                });
                
                // 7. MOSTRAR botón eliminar
                const btnEliminar = nuevoMaterial.querySelector('.eliminar-material');
                if (btnEliminar) {
                    btnEliminar.style.display = 'block';
                }
                
                // 8. AGREGAR al contenedor
                contenedor.appendChild(nuevoMaterial);
                
                // 9. INICIALIZAR Select2 en el NUEVO material
                const selectsNuevos = nuevoMaterial.querySelectorAll('select');
                selectsNuevos.forEach(select => {
                    if ($(select).hasClass('select2-centros-costo-detalle')) {
                        $(select).select2({
                            placeholder: 'Seleccionar uno o más centros de costo...',
                            allowClear: true,
                            width: '100%',
                            multiple: true,
                            language: {
                                noResults: function () { return 'No se encontraron resultados'; }
                            }
                        });
                        
                        // ✅ APLICAR AUTOMÁTICAMENTE EL CENTRO DE COSTO DEL SOLICITANTE
                        const idCentroCostoSolicitante = inputIdCentroCostoSolicitante.value;
                        if (idCentroCostoSolicitante) {
                            setTimeout(() => {
                                $(select).val([idCentroCostoSolicitante]).trigger('change');
                            }, 100);
                        }
                    }
                });
                
                // 10. INCREMENTAR contador y actualizar eventos
                contadorMateriales++;
                actualizarEventosEliminar();
                actualizarEventosCampos();
                formularioModificado = true;
                
                // 11. SCROLL al nuevo elemento
                nuevoMaterial.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                
                console.log('✅ Nuevo material agregado, contador:', contadorMateriales);
            }
        });
    }
    
    function actualizarEventosEliminar() {
        document.querySelectorAll('.eliminar-material').forEach(btn => {
            btn.onclick = function() {
                if (document.querySelectorAll('.material-item').length > 1) {
                    this.closest('.material-item').remove();
                }
            };
        });
    }
    
    function actualizarEventosCampos() {
        const todosLosCamposActualizados = document.querySelectorAll('input, textarea, select');
        todosLosCamposActualizados.forEach(campo => {
            if (campo.name !== 'id_almacen' && campo.name !== 'id_ubicacion') {
                campo.removeEventListener('change', marcarFormularioComoModificado);
                campo.removeEventListener('input', marcarFormularioComoModificado);
                campo.addEventListener('change', marcarFormularioComoModificado);
                campo.addEventListener('input', marcarFormularioComoModificado);
            }
        });
    }
    
    actualizarEventosEliminar();
    
    // Validación de cantidad vs stock
    document.addEventListener('input', function(e) {
        if (e.target && e.target.name === 'cantidad[]') {
            const materialItem = e.target.closest('.material-item');
            const stockInput = materialItem.querySelector('input[name="stock_disponible[]"]');
            if (stockInput) {
                const stockText = stockInput.value;
                const stockNumero = parseFloat(stockText.split(' ')[0]) || 0;
                const cantidadIngresada = parseFloat(e.target.value) || 0;
                
                if (cantidadIngresada > stockNumero) {
                    e.target.title = `La cantidad no puede ser mayor al stock disponible (${stockNumero})`;
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Cantidad excede el stock',
                            text: `La cantidad ingresada (${cantidadIngresada}) supera el stock disponible (${stockNumero}).`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                } else {
                    e.target.style.borderColor = '';
                    e.target.title = '';
                }
            }
        }
    });
    
    // Validación antes del envío
    document.querySelector('form').addEventListener('submit', function(e) {
        let hayErrores = false;
        const cantidadInputs = document.querySelectorAll('input[name="cantidad[]"]');
        
        cantidadInputs.forEach(input => {
            const materialItem = input.closest('.material-item');
            const stockInput = materialItem.querySelector('input[name="stock_disponible[]"]');
            if (stockInput) {
                const stockText = stockInput.value;
                const stockNumero = parseFloat(stockText.split(' ')[0]) || 0;
                const cantidadIngresada = parseFloat(input.value) || 0;
                
                if (cantidadIngresada > stockNumero) {
                    hayErrores = true;
                    input.style.borderColor = 'red';
                }
            }
        });
        
        if (hayErrores) {
            e.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de validación',
                    text: 'Hay cantidades que superan el stock disponible. Por favor revise los campos marcados en rojo.'
                });
            } else {
                alert('Hay cantidades que superan el stock disponible. Por favor revise los campos marcados en rojo.');
            }
        }
    });
});

// Limpiar referencia al cerrar modal
$('#buscar_producto').on('hidden.bs.modal', function () {
    currentSearchButton = null;
});

// Validación de archivos
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action="uso_material_nuevo.php"], form[action="uso_material_editar.php"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            let archivosInvalidos = false;
            let mensajeError = '';
            const archivosInputs = form.querySelectorAll('input[type="file"][name^="archivos_"]');
            archivosInputs.forEach(input => {
                for (let i = 0; i < input.files.length; i++) {
                    if (input.files[i].size > 5 * 1024 * 1024) {
                        archivosInvalidos = true;
                        mensajeError = 'Uno o más archivos superan el límite de 5MB. Por favor seleccione archivos más pequeños.';
                        break;
                    }
                }
            });
            if (archivosInvalidos) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Archivo demasiado grande',
                        text: mensajeError
                    });
                } else {
                    alert(mensajeError);
                }
            }
        });
    }
});

// Tooltips
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip({
        placement: 'top',
        trigger: 'hover'
    });
});
</script>

<style>
.duplicado-resaltado {
    background-color: #ffe6e6 !important;
    border: 2px solid #ff4d4d !important;
    box-shadow: 0 0 10px rgba(255, 77, 77, 0.6);
    transition: all 0.3s ease;
}
</style>