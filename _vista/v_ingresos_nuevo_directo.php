<?php
// Vista para crear ingreso directo - v_ingresos_nuevo_directo.php
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Nuevo Ingreso Directo <small>Sin Orden de Compra</small></h3>
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
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Información del Ingreso Directo</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br>
                        <form class="form-horizontal form-label-left" action="ingresos_directo_nuevo.php" method="post">
                            
                            <!-- Información básica del ingreso -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Almacén <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="id_almacen" class="form-control" required>
                                        <option value="">Seleccionar Almacén</option>
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
                                        <option value="">Seleccionar Ubicación</option>
                                        <?php foreach ($ubicaciones as $ubicacion) { ?>
                                            <option value="<?php echo $ubicacion['id_ubicacion']; ?>">
                                                <?php echo $ubicacion['nom_ubicacion']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Personal que Registra:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo $usuario_sesion; ?>" readonly>
                                    <input type="hidden" name="id_personal" value="<?php echo $id; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Fecha de Ingreso:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo date('d/m/Y H:i:s'); ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Observaciones:</label>
                                <div class="col-md-9 col-sm-9">
                                    <textarea name="observaciones" class="form-control" rows="3" placeholder="Observaciones sobre el ingreso directo"></textarea>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <!-- Sección de productos -->
                            <div class="x_title">
                                <h4>Productos a Ingresar</h4>
                                <div class="clearfix"></div>
                            </div>

                            <div id="contenedor-productos">
                                <div class="producto-item border p-3 mb-3" style="background-color: #f8f9fa;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Material <span class="text-danger">*</span>:</label>
                                            <div class="input-group">
                                                <input type="text" name="descripcion[]" class="form-control" placeholder="Buscar material..." required readonly>
                                                <button onclick="buscarMaterial(this)" class="btn btn-secondary" type="button">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </div>
                                            <input type="hidden" name="id_producto[]" value="" required>
                                        </div>

                                        <div class="col-md-3">
                                            <label>Unidad:</label>
                                            <input type="text" name="unidad_display[]" class="form-control" readonly>
                                        </div>

                                        <div class="col-md-3">
                                            <label>Cantidad <span class="text-danger">*</span>:</label>
                                            <input type="number" name="cantidad[]" class="form-control" step="0.01" min="0.01" required>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-10">
                                            <label>Observaciones del Producto:</label>
                                            <input type="text" name="obs_producto[]" class="form-control" placeholder="Observaciones específicas de este producto">
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger btn-sm eliminar-producto" style="display: none;">
                                                <i class="fa fa-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="button" id="agregar-producto" class="btn btn-info btn-sm">
                                    <i class="fa fa-plus"></i> Agregar Producto
                                </button>
                            </div>

                            <div class="ln_solid"></div>

                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 offset-md-8">
                                    <button type="reset" class="btn btn-outline-danger btn-block">Limpiar</button>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <button type="submit" name="registrar" class="btn btn-success btn-block">
                                        <i class="fa fa-save"></i> Registrar Ingreso
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12">
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

<!-- Modal para buscar materiales (usando modal existente) -->
<div class="modal fade" id="buscar_material" tabindex="-1" role="dialog" aria-labelledby="modalBuscarMaterial">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Buscar Material con Stock</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Filtros -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Almacén:</label>
                        <select id="filtro-almacen" class="form-control">
                            <option value="">Todos los almacenes</option>
                            <?php foreach ($almacenes as $almacen) { ?>
                                <option value="<?php echo $almacen['id_almacen']; ?>">
                                    <?php echo $almacen['nom_almacen']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Ubicación:</label>
                        <select id="filtro-ubicacion" class="form-control">
                            <option value="">Todas las ubicaciones</option>
                            <?php foreach ($ubicaciones as $ubicacion) { ?>
                                <option value="<?php echo $ubicacion['id_ubicacion']; ?>">
                                    <?php echo $ubicacion['nom_ubicacion']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="solo-con-stock" checked>
                            <label class="form-check-label" for="solo-con-stock">
                                Mostrar solo productos con stock disponible
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table id="datatable_material" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Material</th>
                                <th>Tipo</th>
                                <th>Unidad</th>
                                <th>Stock Actual</th>
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

<style>
.producto-item {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    background-color: #f8f9fa;
}

.producto-item:hover {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background-color: #5a6268;
    border-color: #545b62;
}
</style>

<script>
let currentSearchButton = null;
let contadorProductos = 1;

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar eventos
    actualizarEventosEliminar();
    
    // Agregar nuevo producto
    document.getElementById('agregar-producto').addEventListener('click', function() {
        const contenedor = document.getElementById('contenedor-productos');
        const nuevoProducto = document.querySelector('.producto-item').cloneNode(true);
        
        // Limpiar valores
        const inputs = nuevoProducto.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            if (input.type !== 'button') {
                input.value = '';
            }
        });
        
        // Mostrar botón eliminar
        const btnEliminar = nuevoProducto.querySelector('.eliminar-producto');
        btnEliminar.style.display = 'block';
        
        contenedor.appendChild(nuevoProducto);
        contadorProductos++;
        
        actualizarEventosEliminar();
    });
});

function buscarMaterial(button) {
    currentSearchButton = button;
    $('#buscar_material').modal('show');
    cargarMateriales();
}

function cargarMateriales() {
    if ($.fn.dataTable.isDataTable('#datatable_material')) {
        $('#datatable_material').DataTable().destroy();
    }

    // Obtener valores de filtros
    const idAlmacen = $('#filtro-almacen').val() || 0;
    const idUbicacion = $('#filtro-ubicacion').val() || 0;
    const soloConStock = $('#solo-con-stock').is(':checked');

    $('#datatable_material').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "ajax": {
            "url": "material_mostrar_modal.php", // Usar tu archivo existente
            "type": "POST",
            "data": function(d) {
                d.id_almacen = idAlmacen;
                d.id_ubicacion = idUbicacion;
                d.solo_con_stock = soloConStock;
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
            "zeroRecords": "No se encontraron materiales",
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

    // Event listeners para los filtros
    $('#filtro-almacen, #filtro-ubicacion, #solo-con-stock').off('change.materialModal');
    $('#filtro-almacen, #filtro-ubicacion, #solo-con-stock').on('change.materialModal', function() {
        cargarMateriales(); // Recargar tabla cuando cambien los filtros
    });
}

function seleccionarProducto(id, nombre, unidad, stockActual) {
    if (currentSearchButton) {
        let productoItem = currentSearchButton.closest('.producto-item');
        
        if (productoItem) {
            productoItem.querySelector('input[name="descripcion[]"]').value = nombre;
            productoItem.querySelector('input[name="id_producto[]"]').value = id;
            productoItem.querySelector('input[name="unidad_display[]"]').value = unidad;            
            // NO establecer cantidad máxima ni placeholder para ingresos directos
            const cantidadInput = productoItem.querySelector('input[name="cantidad[]"]');
            cantidadInput.removeAttribute('max');
            cantidadInput.setAttribute('placeholder', 'Ingrese cantidad');
        }
    }
    
    $('#buscar_material').modal('hide');
    currentSearchButton = null;
    
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Material seleccionado',
            text: 'El material "' + nombre + '" ha sido seleccionado.',
            showConfirmButton: false,
            timer: 2000
        });
    }
}

function actualizarEventosEliminar() {
    document.querySelectorAll('.eliminar-producto').forEach(btn => {
        btn.onclick = function() {
            if (document.querySelectorAll('.producto-item').length > 1) {
                this.closest('.producto-item').remove();
            }
        };
    });
}

// Validación del formulario
document.querySelector('form').addEventListener('submit', function(e) {
    const productosItems = document.querySelectorAll('.producto-item');
    let hayProductosValidos = false;
    
    productosItems.forEach(item => {
        const idProducto = item.querySelector('input[name="id_producto[]"]').value;
        const cantidad = item.querySelector('input[name="cantidad[]"]').value;
        
        if (idProducto && cantidad && parseFloat(cantidad) > 0) {
            hayProductosValidos = true;
        }
    });
    
    if (!hayProductosValidos) {
        e.preventDefault();
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Productos requeridos',
                text: 'Debe agregar al menos un producto con cantidad válida.'
            });
        } else {
            alert('Debe agregar al menos un producto con cantidad válida.');
        }
    }
});

// Limpiar referencia cuando se cierre modal
$('#buscar_material').on('hidden.bs.modal', function () {
    currentSearchButton = null;
});
</script>