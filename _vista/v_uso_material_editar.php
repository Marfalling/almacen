<?php 
//=======================================================================
// VISTA: v_uso_material_editar.php
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Editar Uso de Material</h3>
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
                        <?php if (!empty($uso_material_data)) { 
                            $uso = $uso_material_data[0];
                        ?>
                        <form class="form-horizontal form-label-left" action="uso_material_editar.php?id=<?php echo $id_uso_material; ?>" method="post" enctype="multipart/form-data">
                            
                            <!-- Información básica -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Almacén:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo $uso['nom_almacen']; ?>" readonly>
                                    <input type="hidden" name="id_almacen" value="<?php echo $uso['id_almacen']; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Ubicación:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo $uso['nom_ubicacion']; ?>" readonly>
                                    <input type="hidden" name="id_ubicacion" value="<?php echo $uso['id_ubicacion']; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Solicitante <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="id_solicitante" id="id_solicitante" class="form-control" required>
                                        <option value="">Seleccionar</option>
                                        <?php foreach ($personal as $persona) { ?>
                                            <option value="<?php echo $persona['id_personal']; ?>"
                                                <?php echo ($uso['id_solicitante'] == $persona['id_personal']) ? 'selected' : ''; ?>>
                                                <?php echo $persona['nom_personal']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Registrado por:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo $uso['nom_registrado']; ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Fecha de Registro:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo date('d/m/Y H:i', strtotime($uso['fec_uso_material'])); ?>" readonly>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <!-- Sección de materiales -->
                            <div class="x_title">
                                <h4>Materiales utilizados <small></small></h4>
                                <div class="clearfix"></div>
                            </div>

                            <div id="contenedor-materiales">
                                <?php 
                                $contador = 0;
                                foreach ($uso_material_detalle as $detalle) { 
                                ?>
                                <div class="material-item border p-3 mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Material <span class="text-danger">*</span>:</label>
                                            <div class="input-group">
                                                <input type="text" readonly name="descripcion[]" class="form-control" 
                                                       value="<?php echo $detalle['nom_producto']; ?>" required>
                                                <input type="hidden" name="id_producto[]" value="<?php echo $detalle['id_producto']; ?>">
                                                <input type="hidden" name="id_detalle[]" value="<?php echo $detalle['id_uso_material_detalle']; ?>">
                                                <button onclick="buscarMaterial(this)" class="btn btn-secondary btn-xs" type="button">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <label>Unidad:</label>
                                            <input type="text" name="unidad[]" class="form-control" 
                                                   value="<?php echo $detalle['nom_unidad_medida']; ?>" readonly>
                                        </div>

                                        <div class="col-md-3">
                                            <label>Cantidad <span class="text-danger">*</span>:</label>
                                            <input type="number" name="cantidad[]" class="form-control" step="0.01" min="0.01" 
                                                   value="<?php echo $detalle['cant_uso_material_detalle']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <label>Observaciones:</label>
                                            <input type="text" name="observaciones[]" class="form-control" placeholder="Observaciones del uso"
                                                   value="<?php echo $detalle['obs_uso_material_detalle']; ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label>Adjuntar Evidencias:</label>
                                            <input type="file" name="archivos_<?php echo $contador; ?>[]" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                                            <small class="form-text text-muted">Formatos permitidos: PDF, JPG, PNG, DOC, XLS. Máximo 5MB por archivo.</small>
                                            
                                            <?php if (!empty($detalle['archivos'])) { 
                                                $archivos = explode(',', $detalle['archivos']);
                                                echo '<div class="mt-2"><strong>Archivos existentes:</strong><br>';
                                                foreach ($archivos as $archivo) {
                                                    if (!empty($archivo)) {
                                                        echo '<div class="d-flex align-items-center mb-1">';
                                                        echo '<a href="../_archivos/uso_material/' . $archivo . '" target="_blank" class="btn btn-link btn-sm p-0 me-2">' . $archivo . '</a>';
                                                        echo '<button type="button" class="btn btn-danger btn-xs ms-2" onclick="eliminarArchivo(\'' . $archivo . '\', ' . $detalle['id_uso_material_detalle'] . ', this)" title="Eliminar archivo">';
                                                        echo '<i class="fa fa-trash"></i>';
                                                        echo '</button>';
                                                        echo '</div>';
                                                    }
                                                }
                                                echo '</div>';
                                            } ?>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger btn-sm eliminar-material" 
                                                    <?php echo ($contador == 0) ? 'style="display: none;"' : ''; ?>>
                                                <i class="fa fa-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php 
                                    $contador++;
                                } 
                                ?>
                            </div>

                            <div class="form-group">
                                <button type="button" id="agregar-material" class="btn btn-info btn-sm">
                                    <i class="fa fa-plus"></i> Agregar Material
                                </button>
                            </div>

                            <div class="ln_solid"></div>

                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 offset-md-6">
                                    <a href="uso_material_mostrar.php" class="btn btn-outline-secondary btn-block">Cancelar</a>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <button type="reset" class="btn btn-outline-danger btn-block">Limpiar</button>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <button type="submit" name="actualizar" id="btn_actualizar" class="btn btn-success btn-block">Actualizar</button>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12 col-sm-12">
                                    <p><span class="text-danger">*</span> Los campos con (<span class="text-danger">*</span>) son obligatorios.</p>
                                </div>
                            </div>
                        </form>
                        <?php } ?>
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

<!-- Agregar Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Activar Select2 para el campo solicitante
    $('#id_solicitante').select2({
        placeholder: "Seleccione un solicitante",
        allowClear: true,
        width: '100%'
    });
});
</script>

<script>
// Variables globales
let currentSearchButton = null;
let ubicacionActual = '<?php echo $uso['id_ubicacion']; ?>';
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

// Limpiar productos nuevos (no originales del registro)
function limpiarProductosNuevos() {
    const materialesItems = document.querySelectorAll('.material-item');
    
    // Eliminar solo los items nuevos (que no tienen id_detalle o tienen id_detalle = 0)
    materialesItems.forEach((item) => {
        const inputIdDetalle = item.querySelector('input[name="id_detalle[]"]');
        if (inputIdDetalle && (inputIdDetalle.value === '' || inputIdDetalle.value === '0')) {
            item.remove();
        }
    });
    
    formularioModificado = false;
}

// Función para eliminar archivo
function eliminarArchivo(nombreArchivo, idDetalle, botonEliminar) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: '¿Eliminar archivo?',
            text: 'Esta acción no se puede deshacer. El archivo "' + nombreArchivo + '" será eliminado permanentemente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Realizar petición AJAX para eliminar el archivo
                fetch('eliminar_archivo_uso_material.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'nombre_archivo=' + encodeURIComponent(nombreArchivo) + 
                          '&id_detalle=' + idDetalle
                })
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === 'OK') {
                        // Eliminar la fila del archivo del DOM
                        botonEliminar.parentElement.remove();
                        
                        Swal.fire({
                            title: 'Archivo eliminado',
                            text: 'El archivo ha sido eliminado correctamente.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: 'No se pudo eliminar el archivo: ' + data,
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Error de conexión al eliminar el archivo.',
                        icon: 'error'
                    });
                });
            }
        });
    } else {
        if (confirm('¿Está seguro de eliminar el archivo "' + nombreArchivo + '"?')) {
            // Realizar petición AJAX para eliminar el archivo
            fetch('eliminar_archivo_uso_material.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'nombre_archivo=' + encodeURIComponent(nombreArchivo) + 
                      '&id_detalle=' + idDetalle
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === 'OK') {
                    // Eliminar la fila del archivo del DOM
                    botonEliminar.parentElement.remove();
                    alert('Archivo eliminado correctamente.');
                } else {
                    alert('Error al eliminar archivo: ' + data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión al eliminar el archivo.');
            });
        }
    }
}

// Función para buscar material
function buscarMaterial(button) {
    const inputAlmacen = document.querySelector('input[name="id_almacen"]');
    const hiddenUbicacion = document.querySelector('input[name="id_ubicacion"]');
    
    if (!hiddenUbicacion.value) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Error',
                text: 'No se pudo obtener la ubicación.',
                confirmButtonText: 'Entendido'
            });
        } else {
            alert('No se pudo obtener la ubicación.');
        }
        return;
    }
    
    currentSearchButton = button;
    
    const filtroSelect = document.getElementById('filtro_almacen_ubicacion');
    const almacenNombre = '<?php echo $uso['nom_almacen']; ?>';
    const ubicacionNombre = '<?php echo $uso['nom_ubicacion']; ?>';
    
    filtroSelect.innerHTML = `<option value="${inputAlmacen.value}_${hiddenUbicacion.value}">${almacenNombre} - ${ubicacionNombre}</option>`;
    filtroSelect.value = `${inputAlmacen.value}_${hiddenUbicacion.value}`;
    
    $('#buscar_producto').modal('show');
    cargarProductos(inputAlmacen.value, hiddenUbicacion.value);
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
                d.solo_con_stock = true;
                return d;
            }
        },
        "columns": [
            { "title": "Código" },
            { "title": "Material" },
            { "title": "Tipo" },
            { "title": "Unidad" },
            { "title": "Stock Físico" },       // 👈 NUEVO
            { "title": "Stock Reservado" },    // 👈 NUEVO
            { "title": "Stock Disponible" },   // 👈 NUEVO
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

// Verificar si hay productos nuevos (no originales del registro)
function hayProductosNuevos() {
    const materialesItems = document.querySelectorAll('.material-item');
    for (let item of materialesItems) {
        const inputIdDetalle = item.querySelector('input[name="id_detalle[]"]');
        if (inputIdDetalle && (inputIdDetalle.value === '' || inputIdDetalle.value === '0')) {
            const inputDescripcion = item.querySelector('input[name="descripcion[]"]');
            if (inputDescripcion && inputDescripcion.value.trim() !== '') {
                return true;
            }
        }
    }
    return false;
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    let contadorMateriales = <?php echo count($uso_material_detalle); ?>;
    
    // Detectar cambios en campos del formulario
    const todosLosCampos = document.querySelectorAll('input, textarea, select');
    todosLosCampos.forEach(campo => {
        campo.addEventListener('change', marcarFormularioComoModificado);
        campo.addEventListener('input', marcarFormularioComoModificado);
    });
    
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
                } else if (input.name === 'id_detalle[]') {
                    input.value = '0';
                } else {
                    input.value = '';
                }
            });

            const fileInput = nuevoMaterial.querySelector('input[type="file"]');
            if (fileInput) {
                fileInput.name = `archivos_${contadorMateriales}[]`;
            }

            // Remover archivos existentes del nuevo elemento
            const archivosExistentes = nuevoMaterial.querySelector('.mt-2');
            if (archivosExistentes && archivosExistentes.innerHTML.includes('Archivos existentes:')) {
                archivosExistentes.remove();
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
            campo.removeEventListener('change', marcarFormularioComoModificado);
            campo.removeEventListener('input', marcarFormularioComoModificado);
            campo.addEventListener('change', marcarFormularioComoModificado);
            campo.addEventListener('input', marcarFormularioComoModificado);
        });
    }
    
    actualizarEventosEliminar();
    
    // Validación antes del envío
    document.querySelector('form').addEventListener('submit', function(e) {
        // Validaciones básicas - se puede extender según necesidades
        const materialesConDatos = document.querySelectorAll('.material-item input[name="descripcion[]"]');
        let hayMaterialesValidos = false;
        
        materialesConDatos.forEach(input => {
            if (input.value.trim() !== '') {
                hayMaterialesValidos = true;
            }
        });
        
        if (!hayMaterialesValidos) {
            e.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de validación',
                    text: 'Debe seleccionar al menos un material.'
                });
            } else {
                alert('Debe seleccionar al menos un material.');
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
            // Buscar todos los inputs de archivos
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
                // El formulario NO se envía y los datos NO se pierden
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