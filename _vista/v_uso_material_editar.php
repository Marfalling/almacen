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
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Ubicación <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="id_ubicacion" class="form-control" required>
                                        <option value="">Seleccionar</option>
                                        <?php foreach ($ubicaciones as $ubicacion) { ?>
                                            <option value="<?php echo $ubicacion['id_ubicacion']; ?>" 
                                                <?php echo ($uso['id_ubicacion'] == $ubicacion['id_ubicacion']) ? 'selected' : ''; ?>>
                                                <?php echo $ubicacion['nom_ubicacion']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Solicitante <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="id_solicitante" class="form-control" required>
                                        <option value="">Seleccionar</option>
                                        <?php foreach ($personal as $persona) { ?>
                                            <option value="<?php echo $persona['id_personal']; ?>"
                                                <?php echo ($uso['id_solicitante'] == $persona['id_personal']) ? 'selected' : ''; ?>>
                                                <?php echo $persona['nom_personal'] . ' ' . $persona['ape_personal']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Registrado por:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo $uso['nom_registrado'] . ' ' . $uso['ape_registrado']; ?>" readonly>
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
                                            <label>Cantidad <span class="text-danger">*</span>:</label>
                                            <input type="number" name="cantidad[]" class="form-control" step="0.01" min="0.01" 
                                                   value="<?php echo $detalle['cant_uso_material_detalle']; ?>" required>
                                        </div>

                                        <div class="col-md-3">
                                            <label>Unidad:</label>
                                            <input type="text" name="unidad[]" class="form-control" 
                                                   value="<?php echo $detalle['nom_unidad_medida']; ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <label>Stock Disponible:</label>
                                            <input type="text" name="stock_disponible[]" class="form-control text-primary font-weight-bold" 
                                                   value="<?php echo $detalle['cantidad_disponible_almacen'] . ' ' . $detalle['nom_unidad_medida']; ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Observaciones:</label>
                                            <textarea name="observaciones[]" class="form-control" rows="2" placeholder="Observaciones del uso"><?php echo $detalle['obs_uso_material_detalle']; ?></textarea>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <label>Adjuntar Evidencias:</label>
                                            <input type="file" name="archivos_<?php echo $contador; ?>[]" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                                            <small class="form-text text-muted">Formatos permitidos: PDF, JPG, PNG, DOC, XLS. Máximo 5MB por archivo.</small>
                                            <?php if (!empty($detalle['archivos'])) { 
                                                $archivos = explode(',', $detalle['archivos']);
                                                echo '<div class="mt-2"><strong>Archivos existentes:</strong><br>';
                                                foreach ($archivos as $archivo) {
                                                    if (!empty($archivo)) {
                                                        echo '<a href="../_archivos/uso_material/' . $archivo . '" target="_blank" class="btn btn-link btn-sm p-0">' . $archivo . '</a><br>';
                                                    }
                                                }
                                                echo '</div>';
                                            } ?>
                                        </div>
                                        <div class="col-md-6 d-flex align-items-end">
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
    <div class="modal-dialog modal-lg" role="document">
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
                                <th>Stock</th>
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
// Variable global para rastrear qué botón de búsqueda se clickeó
let currentSearchButton = null;

function buscarMaterial(button) {
    // Obtener almacén de los datos existentes (no editable)
    const selectUbicacion = document.querySelector('select[name="id_ubicacion"]');
    
    if (!selectUbicacion.value) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Datos requeridos',
                text: 'Debe seleccionar ubicación antes de buscar materiales.',
                confirmButtonText: 'Entendido'
            });
        } else {
            alert('Debe seleccionar ubicación antes de buscar materiales.');
        }
        return;
    }
    
    // Guardar referencia al botón que se clickeó
    currentSearchButton = button;
    
    // Actualizar el filtro en la modal (usando almacén fijo del registro actual)
    const filtroSelect = document.getElementById('filtro_almacen_ubicacion');
    const almacenId = '<?php echo $uso['id_almacen']; ?>';
    const almacenNombre = '<?php echo $uso['nom_almacen']; ?>';
    const ubicacionTexto = selectUbicacion.options[selectUbicacion.selectedIndex].text;
    
    filtroSelect.innerHTML = `<option value="${almacenId}_${selectUbicacion.value}">${almacenNombre} - ${ubicacionTexto}</option>`;
    filtroSelect.value = `${almacenId}_${selectUbicacion.value}`;
    
    // Abrir la modal
    $('#buscar_producto').modal('show');
    
    // Cargar los productos en la tabla
    cargarProductos(almacenId, selectUbicacion.value);
}

function cargarProductos(idAlmacen, idUbicacion) {
    // Si la tabla ya está inicializada, destrúyela
    if ($.fn.dataTable.isDataTable('#datatable_producto')) {
        $('#datatable_producto').DataTable().destroy();
    }

    // Inicializar DataTable
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
            { "title": "Stock" },
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

function seleccionarProducto(idProducto, nombreProducto, unidadMedida, stockDisponible) {
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
            if (inputStock) inputStock.value = stockDisponible + ' ' + unidadMedida;
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

$('#buscar_producto').on('hidden.bs.modal', function () {
    currentSearchButton = null;
});

document.addEventListener('DOMContentLoaded', function() {
    let contadorMateriales = <?php echo count($uso_material_detalle); ?>;
    
    // Agregar nuevo material
    const btnAgregarMaterial = document.getElementById('agregar-material');
    if (btnAgregarMaterial) {
        btnAgregarMaterial.addEventListener('click', function() {
            const contenedor = document.getElementById('contenedor-materiales');
            const nuevoMaterial = document.querySelector('.material-item').cloneNode(true);
            
            // Limpiar los valores del nuevo elemento
            const inputs = nuevoMaterial.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                if (input.type !== 'hidden') {
                    input.value = '';
                } else if (input.name === 'id_detalle[]') {
                    input.value = '0'; // Nuevo detalle
                } else {
                    input.value = '';
                }
            });

            // Actualizar el name del input file para que sea único
            const fileInput = nuevoMaterial.querySelector('input[type="file"]');
            if (fileInput) {
                fileInput.name = `archivos_${contadorMateriales}[]`;
            }

            // Remover archivos existentes del nuevo elemento
            const archivosExistentes = nuevoMaterial.querySelector('.mt-2');
            if (archivosExistentes) {
                archivosExistentes.remove();
            }

            // Mostrar el botón eliminar
            const btnEliminar = nuevoMaterial.querySelector('.eliminar-material');
            if (btnEliminar) {
                btnEliminar.style.display = 'block';
            }
            
            contenedor.appendChild(nuevoMaterial);
            contadorMateriales++;
            
            actualizarEventosEliminar();
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
    
    actualizarEventosEliminar();
    
    // Validación de cantidad vs stock
    document.addEventListener('input', function(e) {
        if (e.target && e.target.name === 'cantidad[]') {
            const materialItem = e.target.closest('.material-item');
            const stockInput = materialItem.querySelector('input[name="stock_disponible[]"]');
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
    });
    
    // Validación antes del envío
    document.querySelector('form').addEventListener('submit', function(e) {
        let hayErrores = false;
        const cantidadInputs = document.querySelectorAll('input[name="cantidad[]"]');
        
        cantidadInputs.forEach(input => {
            const materialItem = input.closest('.material-item');
            const stockInput = materialItem.querySelector('input[name="stock_disponible[]"]');
            const stockText = stockInput.value;
            const stockNumero = parseFloat(stockText.split(' ')[0]) || 0;
            const cantidadIngresada = parseFloat(input.value) || 0;
            
            if (cantidadIngresada > stockNumero) {
                hayErrores = true;
                input.style.borderColor = 'red';
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
</script>