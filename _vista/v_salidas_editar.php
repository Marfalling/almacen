<?php 
//=======================================================================
// VISTA: v_salidas_editar.php
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Editar Salida</h3>
            </div>
            <div class="title_right">
                <div class="col-md-12 col-sm-12 col-xs-12 form-group" style="text-align: right; padding-right: 0;">
                    <a href="salidas_mostrar.php" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Volver a Salidas
                    </a>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Datos de Salida - ID: <?php echo $salida_datos[0]['id_salida']; ?></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br>
                        <form class="form-horizontal form-label-left" action="salidas_editar.php?id=<?php echo $id_salida; ?>" method="post" enctype="multipart/form-data">
                            
                            <!-- Información básica del traslado -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Tipo de Material <span class="text-danger">*</span>:</label>
                                <div class="col-md-4 col-sm-4">
                                    <select name="id_material_tipo" class="form-control" required readonly disabled>
                                        <option value="">Seleccionar</option>
                                        <?php foreach ($material_tipos as $material_tipo) { ?>
                                            <option value="<?php echo $material_tipo['id_material_tipo']; ?>" 
                                                <?php echo ($material_tipo['id_material_tipo'] == $salida_datos[0]['id_material_tipo']) ? 'selected' : ''; ?>>
                                                <?php echo $material_tipo['nom_material_tipo']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <!-- Campo hidden para enviar el valor -->
                                    <input type="hidden" name="id_material_tipo" value="<?php echo $salida_datos[0]['id_material_tipo']; ?>">
                                    <small class="form-text text-muted">El tipo de material no se puede modificar</small>
                                </div>
                                <label class="control-label col-md-2 col-sm-2">Nº Documento de Salida <span class="text-danger">*</span>:</label>
                                <div class="col-md-3 col-sm-3">
                                    <input type="text" name="ndoc_salida" class="form-control" 
                                           placeholder="Número de documento de Salida" 
                                           value="<?php echo htmlspecialchars($salida_datos[0]['ndoc_salida']); ?>" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Fecha Requerida <span class="text-danger">*</span>:</label>
                                <div class="col-md-4 col-sm-4">
                                    <input type="date" name="fec_req_salida" class="form-control" 
                                           value="<?php echo $salida_datos[0]['fec_req_salida']; ?>" 
                                           min="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <label class="control-label col-md-2 col-sm-2">Registrado por:</label>
                                <div class="col-md-3 col-sm-3">
                                    <input type="text" class="form-control" 
                                           value="<?php echo $salida_datos[0]['nom_personal'] . ' ' . $salida_datos[0]['ape_personal']; ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Observaciones:</label>
                                <div class="col-md-9 col-sm-9">
                                    <textarea name="obs_salida" class="form-control" rows="2" 
                                              placeholder="Observaciones"><?php echo htmlspecialchars($salida_datos[0]['obs_salida']); ?></textarea>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <!-- Sección dividida en dos columnas: ORIGEN y DESTINO -->
                            <div class="row">
                                <!-- COLUMNA IZQUIERDA - ORIGEN -->
                                <div class="col-md-6">
                                    <div class="x_title">
                                        <h4><i class="fa fa-arrow-circle-up text-info"></i> Origen</h4>
                                        <div class="clearfix"></div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Almacén Origen <span class="text-danger">*</span>:</label>
                                        <select name="id_almacen_origen" id="id_almacen_origen" class="form-control" required>
                                            <option value="">Seleccionar</option>
                                            <?php foreach ($almacenes as $almacen) { ?>
                                                <option value="<?php echo $almacen['id_almacen']; ?>"
                                                    <?php echo ($almacen['id_almacen'] == $salida_datos[0]['id_almacen_origen']) ? 'selected' : ''; ?>>
                                                    <?php echo $almacen['nom_almacen']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Ubicación Origen <span class="text-danger">*</span>:</label>
                                        <select name="id_ubicacion_origen" id="id_ubicacion_origen" class="form-control" required>
                                            <option value="">Seleccionar</option>
                                            <?php foreach ($ubicaciones as $ubicacion) { ?>
                                                <option value="<?php echo $ubicacion['id_ubicacion']; ?>"
                                                    <?php echo ($ubicacion['id_ubicacion'] == $salida_datos[0]['id_ubicacion_origen']) ? 'selected' : ''; ?>>
                                                    <?php echo $ubicacion['nom_ubicacion']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Personal Encargado:</label>
                                        <select name="id_personal_encargado" class="form-control">
                                            <option value="0">No especificado</option>
                                            <?php foreach ($personal as $persona) { ?>
                                                <option value="<?php echo $persona['id_personal']; ?>" 
                                                    <?php echo ($persona['id_personal'] == $salida_datos[0]['id_personal_encargado']) ? 'selected' : ''; ?>>
                                                    <?php echo $persona['nom_personal'] . ' ' . $persona['ape_personal']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- COLUMNA DERECHA - DESTINO -->
                                <div class="col-md-6">
                                    <div class="x_title">
                                        <h4><i class="fa fa-arrow-circle-down text-success"></i> Destino</h4>
                                        <div class="clearfix"></div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Almacén Destino <span class="text-danger">*</span>:</label>
                                        <select name="id_almacen_destino" id="id_almacen_destino" class="form-control" required>
                                            <option value="">Seleccionar</option>
                                            <?php foreach ($almacenes as $almacen) { ?>
                                                <option value="<?php echo $almacen['id_almacen']; ?>"
                                                    <?php echo ($almacen['id_almacen'] == $salida_datos[0]['id_almacen_destino']) ? 'selected' : ''; ?>>
                                                    <?php echo $almacen['nom_almacen']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Ubicación Destino <span class="text-danger">*</span>:</label>
                                        <select name="id_ubicacion_destino" id="id_ubicacion_destino" class="form-control" required>
                                            <option value="">Seleccionar</option>
                                            <?php foreach ($ubicaciones as $ubicacion) { ?>
                                                <option value="<?php echo $ubicacion['id_ubicacion']; ?>"
                                                    <?php echo ($ubicacion['id_ubicacion'] == $salida_datos[0]['id_ubicacion_destino']) ? 'selected' : ''; ?>>
                                                    <?php echo $ubicacion['nom_ubicacion']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Personal que Recibe:</label>
                                        <select name="id_personal_recibe" class="form-control">
                                            <option value="0">No especificado</option>
                                            <?php foreach ($personal as $persona) { ?>
                                                <option value="<?php echo $persona['id_personal']; ?>"
                                                    <?php echo ($persona['id_personal'] == $salida_datos[0]['id_personal_recibe']) ? 'selected' : ''; ?>>
                                                    <?php echo $persona['nom_personal'] . ' ' . $persona['ape_personal']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <!-- Sección de materiales -->
                            <div class="x_title">
                                <h4>Materiales a Trasladar <small></small></h4>
                                <div class="clearfix"></div>
                            </div>

                            <div id="contenedor-materiales">
                                <?php 
                                $contador = 0;
                                foreach ($salida_detalles as $detalle) { 
                                ?>
                                <div class="material-item border p-3 mb-3">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <label>Material <span class="text-danger">*</span>:</label>
                                            <div class="input-group">
                                                <input type="text" name="descripcion[]" class="form-control" 
                                                       placeholder="Material" 
                                                       value="<?php echo htmlspecialchars($detalle['prod_salida_detalle']); ?>" required>
                                                <input type="hidden" name="id_producto[]" value="<?php echo $detalle['id_producto']; ?>">
                                                <button onclick="buscarMaterial(this)" class="btn btn-secondary btn-xs" type="button">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Cantidad <span class="text-danger">*</span>:</label>
                                            <input type="number" name="cantidad[]" class="form-control" step="0.01" 
                                                   value="<?php echo $detalle['cant_salida_detalle']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12 d-flex justify-content-end">
                                            <button type="button" class="btn btn-danger btn-sm eliminar-material" 
                                                    <?php echo (count($salida_detalles) <= 1) ? 'style="display: none;"' : ''; ?>>
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

                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="row justify-content-center">
                                        <div class="col-md-3">
                                            <a href="salidas_mostrar.php" class="btn btn-outline-secondary btn-block">
                                                <i class="fa fa-times"></i> Cancelar
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" name="actualizar" id="btn_actualizar" class="btn btn-success btn-block">
                                                <i class="fa fa-save"></i> Actualizar Salida
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12 col-sm-12">
                                    <p class="text-center"><span class="text-danger">*</span> Los campos con (<span class="text-danger">*</span>) son obligatorios.</p>
                                    <p class="text-center"><small class="text-muted">Nota: El tipo de material no se puede modificar una vez creada la salida.</small></p>
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Buscar Producto/Material</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="datatable_producto" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Tipo</th>
                                <th>Unidad</th>
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
// Variable global para rastrear qué botón de búsqueda se clickeó
let currentSearchButton = null;
// Contador para los materiales dinámicos
let contadorMateriales = <?php echo count($salida_detalles); ?>;

function buscarMaterial(button) {
    // Obtener valores de almacén y ubicación origen
    const idAlmacenOrigen = document.getElementById('id_almacen_origen').value;
    const idUbicacionOrigen = document.getElementById('id_ubicacion_origen').value;
    
    // Obtener el tipo de material desde el formulario
    const idMaterialTipo = document.querySelector('input[name="id_material_tipo"]').value;
    
    // Validar que se haya seleccionado almacén y ubicación origen
    if (!idAlmacenOrigen || !idUbicacionOrigen) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Almacén y Ubicación requeridos',
                text: 'Debe seleccionar un almacén y ubicación de origen antes de buscar productos.',
                confirmButtonText: 'Entendido'
            });
        } else {
            alert('Debe seleccionar un almacén y ubicación de origen antes de buscar productos.');
        }
        return;
    }
    
    // Guardar referencia al botón que se clickeó
    currentSearchButton = button;
    
    // Abrir la modal
    $('#buscar_producto').modal('show');
    
    // Cargar los productos en la tabla con información de stock Y filtro de tipo de material
    cargarProductos(idAlmacenOrigen, idUbicacionOrigen, idMaterialTipo);
}

function cargarProductos(idAlmacen, idUbicacion, tipoMaterial = '') {
    // Si la tabla ya está inicializada, destrúyela antes de crear una nueva instancia
    if ($.fn.dataTable.isDataTable('#datatable_producto')) {
        $('#datatable_producto').DataTable().destroy();
    }

    // Inicializar DataTable con configuración y AJAX
    $('#datatable_producto').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "ajax": {
            "url": "producto_mostrar_modal_salidas.php",
            "type": "POST",
            "data": function(d) {
                d.id_almacen = idAlmacen;
                d.id_ubicacion = idUbicacion;
                // Agregar el filtro de tipo de material si existe
                if (tipoMaterial) {
                    d.tipo_material = tipoMaterial;
                }
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
        "lengthMenu": [10, 25, 50, 100],
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron resultados",
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
            "processing": "Procesando...",
            "emptyTable": "No hay productos disponibles"
        }
    });
}

function seleccionarProducto(idProducto, nombreProducto, stockDisponible) {
    if (currentSearchButton) {
        let materialItem = currentSearchButton.closest('.material-item');
        
        if (materialItem) {
            // Obtener la cantidad actual para sumarla al stock disponible (en caso de edición)
            let inputCantidadActual = materialItem.querySelector('input[name="cantidad[]"]');
            let cantidadActual = parseFloat(inputCantidadActual.value) || 0;
            
            // Actualizar el input de descripción
            let inputDescripcion = materialItem.querySelector('input[name="descripcion[]"]');
            if (inputDescripcion) {
                inputDescripcion.value = nombreProducto;
            }
            
            // Actualizar el input hidden del ID del material
            let inputIdMaterial = materialItem.querySelector('input[name="id_producto[]"]');
            if (inputIdMaterial) {
                inputIdMaterial.value = idProducto;
            }
            
            // Actualizar el stock disponible
            let stockElement = materialItem.querySelector('[id^="stock-disponible-"]');
            let inputCantidad = materialItem.querySelector('input[name="cantidad[]"]');
            
            if (stockElement) {
                const stock = parseFloat(stockDisponible);
                stockElement.textContent = stock.toFixed(2);
                
                if (inputCantidad) {
                    if (stock > 0) {
                        // Si hay stock, establecer min y max
                        inputCantidad.setAttribute('min', '0.01');
                        inputCantidad.max = stockDisponible;
                        // Limpiar la cantidad actual para que el usuario ingrese una nueva
                        inputCantidad.value = '';
                    } else {
                        // Si no hay stock, remover min para evitar validación HTML5
                        inputCantidad.removeAttribute('min');
                        inputCantidad.removeAttribute('max');
                        inputCantidad.value = ''; // Limpiar cualquier valor
                    }
                }
            }
        }
    }
    
    // Mostrar advertencia si no hay stock ANTES de cerrar la modal
    if (parseFloat(stockDisponible) <= 0) {
        // Cerrar la modal primero
        $('#buscar_producto').modal('hide');
        
        // Luego mostrar la advertencia
        setTimeout(() => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Producto sin stock',
                    text: `El producto "${nombreProducto}" no tiene stock disponible en esta ubicación. No podrá registrar cantidades para este producto.`,
                    confirmButtonText: 'Entendido'
                });
            } else {
                alert(`El producto "${nombreProducto}" no tiene stock disponible en esta ubicación. No podrá registrar cantidades para este producto.`);
            }
        }, 300);
    } else {
        // Cerrar la modal
        $('#buscar_producto').modal('hide');
        
        // Mostrar mensaje de éxito
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Producto seleccionado',
                text: 'El producto "' + nombreProducto + '" ha sido seleccionado.',
                showConfirmButton: false,
                timer: 2000
            });
        }
    }
    
    // Limpiar la referencia
    currentSearchButton = null;
}

// Limpiar la referencia cuando se cierre la modal sin seleccionar
$('#buscar_producto').on('hidden.bs.modal', function () {
    currentSearchButton = null;
});

// Script para manejo dinámico de materiales
document.addEventListener('DOMContentLoaded', function() {
    
    // Función para validar stock en tiempo real - MEJORADA PARA EDICIÓN
    function validarStock(inputCantidad, stockElement, inputDescripcion) {
        const cantidad = parseFloat(inputCantidad.value) || 0;
        const stock = parseFloat(stockElement.textContent) || 0;
        const nombreProducto = inputDescripcion.value;
        
        // Si no hay producto seleccionado, permitir cualquier cantidad
        if (!nombreProducto.trim()) {
            return true;
        }
        
        // Si el stock es 0 o menor
        if (stock <= 0) {
            // Limpiar el campo y mostrar alerta
            inputCantidad.value = '';
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Sin stock disponible',
                    text: `El producto "${nombreProducto}" no tiene stock disponible en esta ubicación. No se puede realizar la salida.`,
                    confirmButtonText: 'Entendido'
                });
            } else {
                alert(`El producto "${nombreProducto}" no tiene stock disponible en esta ubicación. No se puede realizar la salida.`);
            }
            return false;
        }
        
        // Si la cantidad excede el stock
        if (cantidad > stock) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cantidad excede el stock',
                    text: `La cantidad ingresada (${cantidad}) excede el stock disponible (${stock.toFixed(2)}) para "${nombreProducto}".`,
                    confirmButtonText: 'Entendido'
                });
            } else {
                alert(`La cantidad ingresada (${cantidad}) excede el stock disponible (${stock.toFixed(2)}) para "${nombreProducto}".`);
            }
            return false;
        }
        
        return true;
    }
    
    // Función para configurar eventos en inputs de cantidad
    function configurarEventosCantidad() {
        document.querySelectorAll('input[name="cantidad[]"]').forEach(input => {
            // Remover eventos anteriores para evitar duplicados
            input.removeEventListener('input', validarCantidadEnTiempoReal);
            input.removeEventListener('blur', validarCantidadAlSalir);
            
            // Agregar eventos
            input.addEventListener('input', validarCantidadEnTiempoReal);
            input.addEventListener('blur', validarCantidadAlSalir);
        });
    }
    
    function validarCantidadEnTiempoReal(e) {
        const inputCantidad = e.target;
        const materialItem = inputCantidad.closest('.material-item');
        const stockElement = materialItem.querySelector('[id^="stock-disponible-"]');
        const inputDescripcion = materialItem.querySelector('input[name="descripcion[]"]');
        
        if (stockElement && inputDescripcion) {
            const stock = parseFloat(stockElement.textContent) || 0;
            
            // Si no hay stock y se está intentando ingresar una cantidad
            if (stock <= 0 && inputCantidad.value && parseFloat(inputCantidad.value) > 0) {
                validarStock(inputCantidad, stockElement, inputDescripcion);
            }
        }
    }
    
    function validarCantidadAlSalir(e) {
        const inputCantidad = e.target;
        const materialItem = inputCantidad.closest('.material-item');
        const stockElement = materialItem.querySelector('[id^="stock-disponible-"]');
        const inputDescripcion = materialItem.querySelector('input[name="descripcion[]"]');
        
        if (stockElement && inputDescripcion && inputCantidad.value) {
            validarStock(inputCantidad, stockElement, inputDescripcion);
        }
    }
    
    // Agregar nuevo material
    const btnAgregarMaterial = document.getElementById('agregar-material');
    if (btnAgregarMaterial) {
        btnAgregarMaterial.addEventListener('click', function() {
            const contenedor = document.getElementById('contenedor-materiales');
            const nuevoMaterial = document.querySelector('.material-item').cloneNode(true);
            
            // Limpiar los valores del nuevo elemento
            const inputs = nuevoMaterial.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                if (input.type !== 'button') {
                    input.value = '';
                }
            });
            
            // Actualizar el ID del elemento de stock
            const stockElement = nuevoMaterial.querySelector('[id^="stock-disponible-"]');
            if (stockElement) {
                stockElement.id = 'stock-disponible-' + contadorMateriales;
                stockElement.textContent = '0.00';
            }
            
            // Mostrar el botón eliminar
            const btnEliminar = nuevoMaterial.querySelector('.eliminar-material');
            if (btnEliminar) {
                btnEliminar.style.display = 'block';
            }
            
            contenedor.appendChild(nuevoMaterial);
            contadorMateriales++;
            
            // Actualizar eventos
            actualizarEventosEliminar();
            configurarEventosCantidad(); // Reconfigurar eventos de cantidad
        });
    }
    
    // Función para actualizar eventos de eliminar
    function actualizarEventosEliminar() {
        document.querySelectorAll('.eliminar-material').forEach(btn => {
            btn.onclick = function() {
                if (document.querySelectorAll('.material-item').length > 1) {
                    this.closest('.material-item').remove();
                }
            };
        });
    }
    
    // Inicializar eventos
    actualizarEventosEliminar();
    configurarEventosCantidad();
    
    // Validación del formulario antes de enviar - MEJORADA PARA EDICIÓN
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            let errores = [];
            let tieneProductosSinStock = false;
            
            // Validar que al menos un material tenga cantidad
            const cantidades = document.querySelectorAll('input[name="cantidad[]"]');
            let tieneMateriales = false;
            
            cantidades.forEach(input => {
                if (input.value && parseFloat(input.value) > 0) {
                    tieneMateriales = true;
                }
            });
            
            if (!tieneMateriales) {
                errores.push('Debe tener al menos un material con cantidad válida');
            }
            
            // Validar stocks y cantidades
            const materialesItems = document.querySelectorAll('.material-item');
            materialesItems.forEach((item, index) => {
                const inputCantidad = item.querySelector('input[name="cantidad[]"]');
                const stockElement = item.querySelector('[id^="stock-disponible-"]');
                const inputDescripcion = item.querySelector('input[name="descripcion[]"]');
                
                // Solo validar si hay descripción (producto seleccionado)
                if (inputDescripcion && inputDescripcion.value.trim()) {
                    const stock = parseFloat(stockElement.textContent) || 0;
                    const cantidad = parseFloat(inputCantidad.value) || 0;
                    
                    // Verificar stock cero
                    if (stock <= 0) {
                        errores.push(`El producto "${inputDescripcion.value}" no tiene stock disponible`);
                        tieneProductosSinStock = true;
                    }
                    // Verificar cantidad mayor a stock
                    else if (cantidad > stock) {
                        errores.push(`La cantidad de "${inputDescripcion.value}" (${cantidad}) excede el stock disponible (${stock.toFixed(2)})`);
                    }
                    // Verificar que tenga cantidad si tiene producto
                    else if (cantidad <= 0) {
                        errores.push(`Debe ingresar una cantidad válida para "${inputDescripcion.value}"`);
                    }
                }
            });
            
            if (errores.length > 0) {
                e.preventDefault();
                
                let titulo = 'Errores en el formulario';
                let icono = 'error';
                
                if (tieneProductosSinStock) {
                    titulo = 'Productos sin stock disponible';
                    icono = 'warning';
                }
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: icono,
                        title: titulo,
                        html: errores.join('<br>'),
                        confirmButtonText: 'Revisar'
                    });
                } else {
                    alert(titulo + ':\n' + errores.join('\n'));
                }
            }
        });
    }

    // Actualizar stock cuando cambie el almacén o ubicación origen
    const almacenOrigen = document.getElementById('id_almacen_origen');
    const ubicacionOrigen = document.getElementById('id_ubicacion_origen');
    
    if (almacenOrigen && ubicacionOrigen) {
        almacenOrigen.addEventListener('change', actualizarStocks);
        ubicacionOrigen.addEventListener('change', actualizarStocks);
    }
    
    function actualizarStocks() {
        const idAlmacen = almacenOrigen.value;
        const idUbicacion = ubicacionOrigen.value;
        
        if (!idAlmacen || !idUbicacion) return;
        
        // Actualizar stocks para todos los materiales ya seleccionados
        const materialesItems = document.querySelectorAll('.material-item');
        materialesItems.forEach((item, index) => {
            const inputIdProducto = item.querySelector('input[name="id_producto[]"]');
            const inputCantidad = item.querySelector('input[name="cantidad[]"]');
            const cantidadOriginal = parseFloat(inputCantidad.dataset.cantidadOriginal || inputCantidad.value) || 0;
            
            if (inputIdProducto && inputIdProducto.value) {
                // Hacer una solicitud AJAX para obtener el stock actualizado
                fetch(`../_controlador/c_obtener_stock.php?id_producto=${inputIdProducto.value}&id_almacen=${idAlmacen}&id_ubicacion=${idUbicacion}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const stockElement = item.querySelector('[id^="stock-disponible-"]');
                            const inputDescripcion = item.querySelector('input[name="descripcion[]"]');
                            
                            if (stockElement) {
                                // Para edición: sumar la cantidad original al stock para mostrar disponibilidad total
                                const stockReal = parseFloat(data.stock);
                                const stockConDevolucion = stockReal + cantidadOriginal;
                                stockElement.textContent = stockConDevolucion.toFixed(2);
                                
                                if (inputCantidad) {
                                    // Si no hay stock (incluso considerando la devolución), limpiar cantidad
                                    if (stockConDevolucion <= 0) {
                                        inputCantidad.value = '';
                                        inputCantidad.removeAttribute('min');
                                        
                                        // Mostrar advertencia
                                        if (inputDescripcion && inputDescripcion.value) {
                                            if (typeof Swal !== 'undefined') {
                                                Swal.fire({
                                                    icon: 'warning',
                                                    title: 'Stock agotado',
                                                    text: `El producto "${inputDescripcion.value}" ya no tiene stock en la nueva ubicación seleccionada.`,
                                                    confirmButtonText: 'Entendido'
                                                });
                                            }
                                        }
                                    } else {
                                        // Restaurar atributo min si hay stock
                                        inputCantidad.setAttribute('min', '0.01');
                                        inputCantidad.max = stockConDevolucion;
                                        
                                        // Si la cantidad actual es mayor que el nuevo stock, ajustarla
                                        if (parseFloat(inputCantidad.value) > stockConDevolucion) {
                                            inputCantidad.value = stockConDevolucion.toFixed(2);
                                        }
                                    }
                                }
                            }
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
    }
    
    // Guardar cantidades originales para cálculos de stock en edición
    document.querySelectorAll('input[name="cantidad[]"]').forEach(input => {
        input.dataset.cantidadOriginal = input.value;
    });
});
</script>