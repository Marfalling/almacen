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
                                    <select name="id_almacen" class="form-control" required>
                                        <option value="">Seleccionar</option>
                                        <?php foreach ($almacenes as $almacen) { ?>
                                            <option value="<?php echo $almacen['id_almacen']; ?>">
                                                <?php echo $almacen['nom_almacen']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
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
                                                <?php echo ($persona['id_personal'] == $id_personal) ? 'selected' : ''; ?>>
                                                <?php echo $persona['nom_personal']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Registrado por:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo $usuario_sesion; ?>" readonly>
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
                                                <input type="text" name="descripcion[]" class="form-control" placeholder="Buscar material" required>
                                                <input type="hidden" name="id_producto[]" value="">
                                                <button onclick="buscarMaterial(this)" class="btn btn-secondary btn-xs" type="button">
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
                                            <label>Adjuntar Evidencias:</label>
                                            <input type="file" name="archivos_0[]" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
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
                                    <button type="reset" class="btn btn-outline-danger btn-block">Limpiar</button>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <button type="submit" name="registrar" id="btn_registrar" class="btn btn-success btn-block">Registrar</button>
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
                                <th>Material</th>
                                <th>Tipo</th>
                                <th>Unidad</th>
                                <th>Stock Físico</th>
                                <th>Stock Reservado</th>
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
            "url": "material_mostrar_modal.php",
            "type": "POST",
            "data": function(d) {
                d.id_almacen = idAlmacen;
                d.id_ubicacion = idUbicacion;
                return d;
            }
        },
        "columns": [
            { "title": "Código" },
            { "title": "Material" },
            { "title": "Tipo" },
            { "title": "Unidad" },
            { "title": "Stock Físico" },       //  NUEVO
            { "title": "Stock Reservado" },    //  NUEVO
            { "title": "Stock Disponible" },   //  NUEVO
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
            
            if (inputDescripcion) inputDescripcion.value = nombreProducto;
            if (inputIdProducto) inputIdProducto.value = idProducto;
            if (inputUnidad) inputUnidad.value = unidadMedida;
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
    
    // Aplicar los event listeners específicos
    if (selectAlmacen) manejarCambioAlmacen(selectAlmacen);
    if (selectUbicacion) manejarCambioUbicacion(selectUbicacion);
    
    // Agregar nuevo material
    const btnAgregarMaterial = document.getElementById('agregar-material');
    if (btnAgregarMaterial) {
        btnAgregarMaterial.addEventListener('click', function() {
            const contenedor = document.getElementById('contenedor-materiales');
            const nuevoMaterial = document.querySelector('.material-item').cloneNode(true);
            
            const inputs = nuevoMaterial.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                if (input.type !== 'hidden') {
                    input.value = '';
                } else {
                    input.value = '';
                }
            });

            const fileInput = nuevoMaterial.querySelector('input[type="file"]');
            if (fileInput) {
                fileInput.name = `archivos_${contadorMateriales}[]`;
            }

            const btnEliminar = nuevoMaterial.querySelector('.eliminar-material');
            if (btnEliminar) {
                btnEliminar.style.display = 'block';
            }
            
            contenedor.appendChild(nuevoMaterial);
            contadorMateriales++;
            
            actualizarEventosEliminar();
            actualizarEventosCampos();
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
                    e.target.style.borderColor = 'red';
                    e.target.title = `La cantidad no puede ser mayor al stock disponible (${stockNumero})`;
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
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Selecciona el formulario por action o por clase
    const form = document.querySelector('form[action="uso_material_nuevo.php"], form[action="uso_material_editar.php"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            let archivosInvalidos = false;
            let mensajeError = '';
            const archivosInputs = form.querySelectorAll('input[type="file"][name^="archivos_"]');
            archivosInputs.forEach(input => {
                for (let i = 0; i < input.files.length; i++) {
                    if (input.files[i].size > 5 * 1024 * 1024) { // 5MB
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
</script>

<style>
.duplicado-resaltado {
    background-color: #ffe6e6 !important; /* rojo pálido */
    border: 2px solid #ff4d4d !important;
    box-shadow: 0 0 10px rgba(255, 77, 77, 0.6);
    transition: all 0.3s ease;
}
</style>

