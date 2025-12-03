<?php
// VISTA: v_devolucion_nuevo.php
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Nueva Devolución</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Información de la Devolución</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br>
                        <form class="form-horizontal form-label-left" action="devoluciones_nuevo.php" method="post" enctype="multipart/form-data">
                            <!-- Información básica -->
                            <div class="form-group row">
                                <label class="control-label col-md-3">Almacén <span class="text-danger">*</span>:</label>
                                <div class="col-md-9">
                                    <select id="id_almacen" name="id_almacen" class="form-control" required>
                                        <option value="">Seleccionar Almacén</option>
                                        <?php foreach ($almacenes as $almacen) { ?>
                                            <option value="<?php echo $almacen['id_almacen']; ?>"
                                                data-id-cliente="<?php echo $almacen['id_cliente']; ?>">
                                                <?php echo $almacen['nom_almacen']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3">Ubicación <span class="text-danger">*</span>:</label>
                                <div class="col-md-9">
                                    <select id="id_ubicacion" name="id_ubicacion" class="form-control" required>
                                        <option value="">Seleccionar Ubicación</option>
                                        
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3">Registrado por:</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" value="<?php echo $usuario_sesion; ?>" readonly>
                                    <input type="hidden" name="id_personal" value="<?php echo $id; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3">Fecha:</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" value="<?php echo date('d/m/Y H:i:s'); ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3">Observaciones:</label>
                                <div class="col-md-9">
                                    <textarea name="obs_devolucion" class="form-control" rows="2" placeholder="Observaciones"></textarea>
                                </div>
                            </div>

                            <!--<div class="form-group row">
                                <label class="control-label col-md-3">Ubicación Destino:</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" value="BASE" readonly style="background-color: #f8f9fa;">
                                    <input type="hidden" name="id_ubicacion_destino" value="1">
                                </div>
                            </div>-->

                            <div class="ln_solid"></div>

                            <!-- Materiales -->
                            <div class="x_title">
                                <h4>Materiales a Devolver</h4>
                                <div class="clearfix"></div>
                            </div>

                            <div id="contenedor-materiales">
                                <!-- Plantilla: se clonará -->
                                <div class="material-item border p-3 mb-3">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <label>Material <span class="text-danger">*</span>:</label>
                                            <div class="input-group">
                                                <input type="text" name="descripcion[]" class="form-control" placeholder="Material" required>
                                                <input type="hidden" name="id_producto[]" value="">
                                                <button title="Buscar Material" data-toggle="tooltip" onclick="buscarMaterial(this)" class="btn btn-secondary btn-xs" type="button">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <label>Cantidad <span class="text-danger">*</span>:</label>
                                            <input type="number" name="cantidad[]" class="form-control" step="0.01" required>
                                        </div>

                                        <!-- <div class="col-md-3">
                                            <label>Stock Disponible:</label>
                                            <div class="form-control" id="stock-disponible-0" style="background-color: #f8f9fa;">0.00</div>
                                        </div>-->
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-md-12 d-flex justify-content-end">
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

                            <!-- BOTONES -->
                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 offset-md-8">
                                    <a href="devoluciones_mostrar.php" class="btn btn-outline-danger btn-block">
                                        Cancelar
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <button type="submit" name="registrar" id="btn_registrar"
                                        class="btn btn-success btn-block">Registrar</button>
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

<!-- Modal para buscar productos -->
<div class="modal fade" id="buscar_producto" tabindex="-1" role="dialog" aria-labelledby="modalBuscarProducto">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Buscar Producto/Material</h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
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
                        <tbody></tbody>
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

let currentSearchButton = null;
var ubicaciones = <?php echo json_encode($ubicaciones, JSON_UNESCAPED_UNICODE); ?>;
console.log("UBICACIONES CARGADAS DESDE PHP:", ubicaciones);

function buscarMaterial(button) {
    const idAlmacen = document.getElementById('id_almacen').value;
    const idUbicacion = document.getElementById('id_ubicacion').value;

    if (!idAlmacen || !idUbicacion) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Almacén y Ubicación requeridos',
                text: 'Debe seleccionar un almacén y una ubicación antes de buscar productos.',
                confirmButtonText: 'Entendido'
            });
        } else {
            alert('Debe seleccionar un almacén y una ubicación antes de buscar productos.');
        }
        return;
    }

    currentSearchButton = button;
    $('#buscar_producto').modal('show');
    cargarProductos(idAlmacen, idUbicacion);
}

function cargarProductos(idAlmacen, idUbicacion) {
    if ($.fn.dataTable.isDataTable('#datatable_producto')) {
        $('#datatable_producto').DataTable().destroy();
    }

    $('#datatable_producto').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "ajax": {
            "url": "producto_mostrar_modal_devolucion.php",
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
            { "title": "Unidad" },
            { "title": "Marca" },
            { "title": "Modelo" },
            { "title": "Stock Disponible" },
            { "title": "Acción" }
        ],
        "order": [[1, 'asc']],
        "pageLength| ": 10,
        "lengthMenu": [10,25,50,100],
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando página _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros en total)",
            "search": "Buscar:",
            "paginate": {"first":"Primero","last":"Último","next":"Siguiente","previous":"Anterior"},
            "loadingRecords": "Cargando...",
            "processing": "Procesando..."
        }
    });
    $('#datatable_producto').on('draw.dt', function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
}

function seleccionarProducto(idProducto, nombreProducto, stockDisponible) {
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
            // Asignar valores
            let inputDescripcion = materialItem.querySelector('input[name="descripcion[]"]');
            let inputId = materialItem.querySelector('input[name="id_producto[]"]');
            let stockElement = materialItem.querySelector('[id^="stock-disponible-"]');
            let inputCantidad = materialItem.querySelector('input[name="cantidad[]"]');

            if (inputDescripcion) inputDescripcion.value = nombreProducto;
            if (inputId) inputId.value = idProducto;
            if (stockElement) stockElement.textContent = parseFloat(stockDisponible).toFixed(2);
            if (inputCantidad) {
                inputCantidad.setAttribute('min','0.01');
                inputCantidad.max = stockDisponible;
            }
        }
    }

    $('#buscar_producto').modal('hide');

    if (parseFloat(stockDisponible) <= 0) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Producto sin stock',
                text: `El producto "${nombreProducto}" no tiene stock disponible.`,
                confirmButtonText: 'Entendido'
            });
        }
    } else {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Producto seleccionado',
                text: `Se seleccionó "${nombreProducto}".`,
                showConfirmButton: false,
                timer: 1200
            });
        }
    }

    currentSearchButton = null;
}

// Reset referencia al cerrar modal
$('#buscar_producto').on('hidden.bs.modal', function () {
    currentSearchButton = null;
});

document.addEventListener('DOMContentLoaded', function() {

    // Verificar si jQuery está disponible
    const useJQuery = typeof $ !== 'undefined';
    
    let contadorMateriales = 1;
    let isUpdatingLocation = false; // Flag para evitar loops

    // --- Funciones auxiliares ---
    function getElement(selector) {
        return useJQuery ? $(selector) : document.querySelector(selector);
    }

    function getValue(element) {
        return useJQuery ? $(element).val() : element.value;
    }

    function setValue(element, value) {
        if (useJQuery) {
            $(element).val(value);
        } else {
            element.value = value;
        }
    }

    // --- Agregar / eliminar materiales (clon)
    function actualizarEventosEliminar() {
        document.querySelectorAll('.eliminar-material').forEach(btn => {
            // Remover listeners anteriores
            btn.onclick = null;
            btn.onclick = function() {
                if (document.querySelectorAll('.material-item').length > 1) {
                    this.closest('.material-item').remove();
                }
            };
        });
    }

    const btnAgregarMaterial = document.getElementById('agregar-material');
    if (btnAgregarMaterial) {
        btnAgregarMaterial.addEventListener('click', function() {
            const contenedor = document.getElementById('contenedor-materiales');
            const nuevoMaterial = document.querySelector('.material-item').cloneNode(true);

            // Limpiar inputs/textarea/select (pero conservar botones)
            nuevoMaterial.querySelectorAll('input, textarea, select').forEach(input => {
                if (input.type !== 'button') input.value = '';
            });

            // Actualizar ID del stock
            const stockElement = nuevoMaterial.querySelector('[id^="stock-disponible-"]');
            if (stockElement) {
                stockElement.id = 'stock-disponible-' + contadorMateriales;
                stockElement.textContent = '0.00';
            }

            // Mostrar boton eliminar en nuevos bloques
            const btnEliminar = nuevoMaterial.querySelector('.eliminar-material');
            if (btnEliminar) btnEliminar.style.display = 'block';

            contenedor.appendChild(nuevoMaterial);
            contadorMateriales++;

            // Reasignar eventos y validaciones
            actualizarEventosEliminar();
            configurarEventosCantidad();
        });
    }
    actualizarEventosEliminar();

    // --- Validación / eventos para inputs cantidad
    function validarStock(inputCantidad, stockElement, nombreProducto) {
        const cantidad = parseFloat(inputCantidad.value) || 0;
        const stock = parseFloat(stockElement.textContent) || 0;

        // Si no hay producto seleccionado, no validar stock aquí
        if (!nombreProducto || !nombreProducto.trim()) return true;

        if (stock <= 0) {
            inputCantidad.value = '';
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon:'error', title:'Sin stock disponible', text:`El producto "${nombreProducto}" no tiene stock disponible.`, confirmButtonText:'Entendido' });
            } else {
                alert(`El producto "${nombreProducto}" no tiene stock disponible.`);
            }
            return false;
        }
        if (cantidad > stock) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon:'warning', title:'Cantidad excede el stock', text:`La cantidad (${cantidad}) excede el stock disponible (${stock.toFixed(2)}) para "${nombreProducto}".`, confirmButtonText:'Entendido' });
            } else {
                alert(`La cantidad (${cantidad}) excede el stock disponible (${stock.toFixed(2)}) para "${nombreProducto}".`);
            }
            return false;
        }
        return true;
    }

    function configurarEventosCantidad() {
        document.querySelectorAll('input[name="cantidad[]"]').forEach(input => {
            // Limpiar listeners anteriores
            input.oninput = null;
            input.onblur = null;
            // evitar duplicar listeners
            input.oninput = function(e) {
                const materialItem = input.closest('.material-item');
                const stockElement = materialItem.querySelector('[id^="stock-disponible-"]');
                const descripcionInput = materialItem.querySelector('input[name="descripcion[]"]');
                if (stockElement && descripcionInput) validarStock(input, stockElement, descripcionInput.value);
            };
            input.onblur = function(e) {
                const materialItem = input.closest('.material-item');
                const stockElement = materialItem.querySelector('[id^="stock-disponible-"]');
                const descripcionInput = materialItem.querySelector('input[name="descripcion[]"]');
                if (stockElement && descripcionInput && input.value) validarStock(input, stockElement, descripcionInput.value);
            };
        });
    }
    configurarEventosCantidad();

    // --- Actualizar stocks cuando cambie almacén o ubicación
    const almacenSelect = document.getElementById('id_almacen');
    const ubicacionSelect = document.getElementById('id_ubicacion');

    if (almacenSelect && ubicacionSelect) {

        // Cambiar almacén
        almacenSelect.addEventListener('change', function() {
            const valorAnterior = this.dataset.valorAnterior || '';
            const valorActual = this.value;
            const hayMateriales = document.querySelectorAll('.material-item input[name="id_producto[]"]').length > 0;

            // Solo preguntar si ya había un valor anterior y es diferente
            if (valorAnterior && valorAnterior !== valorActual && hayMateriales) {
                Swal.fire({
                    title: '¿Cambiar almacén?',
                    text: 'Se eliminarán todos los materiales agregados.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, cambiar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        limpiarTodosMateriales();
                        actualizarStocks();
                        Swal.fire({
                            icon: 'success',
                            title: 'Almacén cambiado',
                            text: 'Los materiales fueron eliminados.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        this.dataset.valorAnterior = valorActual;
                    } else {
                        this.value = valorAnterior;
                    }
                });
            } else {
                limpiarTodosMateriales();
                actualizarStocks();
                this.dataset.valorAnterior = valorActual; // guardar valor actual
            }
        });

        // Cambiar ubicación
        ubicacionSelect.addEventListener('change', function() {
            const valorAnterior = this.dataset.valorAnterior || '';
            const valorActual = this.value;
            const hayMateriales = document.querySelectorAll('.material-item input[name="id_producto[]"]').length > 0;

            if (valorAnterior && valorAnterior !== valorActual && hayMateriales) {
                Swal.fire({
                    title: '¿Cambiar ubicación?',
                    text: 'Se eliminarán todos los materiales agregados.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, cambiar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        limpiarTodosMateriales();
                        actualizarStocks();
                        Swal.fire({
                            icon: 'success',
                            title: 'Ubicación cambiada',
                            text: 'Los materiales fueron eliminados.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        this.dataset.valorAnterior = valorActual;
                    } else {
                        this.value = valorAnterior;
                    }
                });
            } else {
                limpiarTodosMateriales();
                actualizarStocks();
                this.dataset.valorAnterior = valorActual;
            }
        });

        // Guardar valor inicial cuando se enfoque (si cambia después)
        [almacenSelect, ubicacionSelect].forEach(sel => {
            sel.addEventListener('focus', function() {
                this.dataset.valorAnterior = this.value;
            });
        });
    }

    // --- Limpia todos los materiales del formulario ---
    function limpiarTodosMateriales() {
        const contenedor = document.getElementById('contenedor-materiales');
        if (!contenedor) return;

        const items = contenedor.querySelectorAll('.material-item');
        items.forEach((item, i) => {
            if (i > 0) item.remove(); // elimina todos menos el primero
        });

        const primero = contenedor.querySelector('.material-item');
        if (primero) {
            primero.querySelectorAll('input, textarea, select').forEach(input => {
                if (input.type !== 'button') input.value = '';
            });

            const stockElement = primero.querySelector('[id^="stock-disponible-"]');
            if (stockElement) stockElement.textContent = '0.00';
        }
    }

    function actualizarStocks() {
        const idAlmacen = almacenSelect.value;
        const idUbicacion = ubicacionSelect.value;
        if (!idAlmacen || !idUbicacion) return;

        const materialesItems = document.querySelectorAll('.material-item');
        materialesItems.forEach((item) => {
            const inputIdProducto = item.querySelector('input[name="id_producto[]"]');
            const stockElement = item.querySelector('[id^="stock-disponible-"]');
            const descripcionInput = item.querySelector('input[name="descripcion[]"]');
            const inputCantidad = item.querySelector('input[name="cantidad[]"]');

            if (inputIdProducto && inputIdProducto.value) {
                fetch(`../_controlador/c_obtener_stock.php?id_producto=${inputIdProducto.value}&id_almacen=${idAlmacen}&id_ubicacion=${idUbicacion}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const nuevoStock = parseFloat(data.stock) || 0;
                        if (stockElement) {
                            stockElement.textContent = nuevoStock.toFixed(2);
                        }
                        if (inputCantidad) {
                            if (nuevoStock <= 0) {
                                inputCantidad.value = '';
                                inputCantidad.removeAttribute('min');
                                inputCantidad.removeAttribute('max');
                                if (descripcionInput && descripcionInput.value) {
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({ icon:'warning', title:'Stock agotado', text:`El producto "${descripcionInput.value}" ya no tiene stock en la nueva ubicación.`, confirmButtonText:'Entendido' });
                                    }
                                }
                            } else {
                                inputCantidad.setAttribute('min','0.01');
                                inputCantidad.max = nuevoStock;
                                if (inputCantidad.value && parseFloat(inputCantidad.value) > nuevoStock) {
                                    inputCantidad.value = nuevoStock.toFixed(2);
                                }
                            }
                        }
                    }
                })
                .catch(err => console.error('Error al obtener stock:', err));
            } else {
                // Si no hay producto seleccionado, resetear el stock mostrado
                if (stockElement) stockElement.textContent = '0.00';
            }
        });
    }

    // --- Validación final del formulario antes de enviar
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            let errores = [];
            let tieneMateriales = false;

            document.querySelectorAll('.material-item').forEach(item => {
                const descripcion = item.querySelector('input[name="descripcion[]"]').value.trim();
                const cantidad = parseFloat(item.querySelector('input[name="cantidad[]"]').value) || 0;
                const stock = parseFloat(item.querySelector('[id^="stock-disponible-"]').textContent) || 0;

                if (descripcion && cantidad > 0) tieneMateriales = true;
                if (descripcion && cantidad <= 0) errores.push(`Debe ingresar una cantidad válida para "${descripcion}"`);
                if (descripcion && stock <= 0) errores.push(`El producto "${descripcion}" no tiene stock disponible`);
                if (descripcion && cantidad > stock) errores.push(`La cantidad de "${descripcion}" (${cantidad}) excede el stock (${stock.toFixed(2)})`);
            });

            if (!tieneMateriales) errores.push('Debe agregar al menos un material con cantidad válida');

            if (errores.length > 0) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon:'error', title:'Errores en el formulario', html: errores.join('<br>'), confirmButtonText:'Revisar' });
                } else {
                    alert('Errores en el formulario:\n' + errores.join('\n'));
                }
            }
        });
    }

    // -----------------------------------------------------
    // Variables DOM (comprueba existencia por seguridad)
    // -----------------------------------------------------
    var $almacen = $('#id_almacen');
    var $ubicacion = $('#id_ubicacion');

    // Si no existen los selects, salimos (evita errores)
    if ($almacen.length === 0 || $ubicacion.length === 0) {
        console.warn("id_almacen o id_ubicacion no encontrados en DOM.");
        return;
    }

    // -----------------------------------------------------
    // Manejo del filtro de ubicaciones (usa la variable 'ubicaciones' provista por PHP)
    // -----------------------------------------------------
    // Asegúrate que `ubicaciones` exista y sea array
    if (typeof ubicaciones === 'undefined' || !Array.isArray(ubicaciones)) {
        console.warn("La variable 'ubicaciones' no está definida o no es array.", ubicaciones);
    }

    // Limpia y rellena ubicaciones según almacén seleccionado
    function rellenarUbicacionesSegunAlmacen(idCliente) {
        // limpiar select
        $ubicacion.empty();
        $ubicacion.append($('<option>', { value: '', text: 'Seleccionar Ubicación' }));

        if (!Array.isArray(ubicaciones)) return;

        ubicaciones.forEach(function (u) {
            // u.id_ubicacion y u.nom_ubicacion deben existir
            var idU = Number(u.id_ubicacion);
            if (isNaN(idU)) return;

            // regla: si idCliente != 9 entonces NO incluir id_ubicacion == 1
            if (Number(idCliente) !== 9 && idU === 1) return;

            $ubicacion.append($('<option>', { value: u.id_ubicacion, text: u.nom_ubicacion }));
        });

        // si estás usando Select2, actualiza la UI
        if ($.fn.select2 && $ubicacion.hasClass('select2-hidden-accessible')) {
            $ubicacion.trigger('change.select2'); // actualiza Select2
        } else {
            $ubicacion.trigger('change'); // trigger normal
        }
    }

    // Desactivar listeners anteriores (por si hay duplicados) y asignar el handler jQuery
    $almacen.off('change.filtroUbicaciones').on('change.filtroUbicaciones', function () {
        // obtenemos id_cliente desde el option seleccionado
        var idCliente = $(this).find(':selected').data('idCliente');

        // si por alguna razón data devuelve undefined, intenta la otra notación
        if (typeof idCliente === 'undefined') {
            idCliente = $(this).find(':selected').data('id-cliente') || $(this).find(':selected').attr('data-id-cliente');
        }

        console.log("ID CLIENTE DEL ALMACÉN:", idCliente);

        rellenarUbicacionesSegunAlmacen(Number(idCliente));
    });

    // Si quieres que al cargar la página se rellene según opción seleccionada por defecto:
    (function inicializarRelleno() {
        var sel = $almacen.find(':selected');
        if (sel.length > 0 && sel.val() !== "") {
            var idCli = sel.data('idCliente');
            if (typeof idCli === 'undefined') idCli = sel.data('id-cliente') || sel.attr('data-id-cliente');
            rellenarUbicacionesSegunAlmacen(Number(idCli));
        }
    })();

});

/*document.addEventListener('DOMContentLoaded', function() {
    const clienteSelect = document.getElementById('id_cliente_destino');
    const almacenSelect = document.getElementById('id_almacen');
    const ubicacionSelect = document.getElementById('id_ubicacion');

    if (clienteSelect && almacenSelect && ubicacionSelect) {
        clienteSelect.addEventListener('change', function () {
            if (!almacenSelect.value || !ubicacionSelect.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atención',
                    text: 'Primero debes seleccionar un almacén y una ubicación.',
                    confirmButtonText: 'Entendido'
                });
                clienteSelect.value = "";
            }
        });
    }
});*/


$(document).ready(function() {
    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip({
        placement: 'top',
        trigger: 'hover'
    });

    $('button[data-toggle="modal"][title]').hover(
        function() {
            // Mouse ENTRA
            var $btn = $(this);
            var title = $btn.attr('title');
            var pos = $btn.offset();
            
            // Crear tooltip manualmente
            var $tooltip = $('<div class="custom-tooltip">' + title + '</div>');
            $tooltip.css({
                position: 'absolute',
                top: pos.top - 35,
                left: pos.left + ($btn.outerWidth() / 2) - 50,
                background: '#000',
                color: '#fff',
                padding: '5px 10px',
                borderRadius: '4px',
                fontSize: '12px',
                zIndex: 9999,
                whiteSpace: 'nowrap'
            });
            
            $('body').append($tooltip);
            $tooltip.fadeIn(200);
        },
        function() {
            // Mouse SALE
            $('.custom-tooltip').fadeOut(200, function() {
                $(this).remove();
            });
        }
    );
    
    // Ocultar al hacer clic
    $('button[data-toggle="modal"][title]').on('click', function() {
        $('.custom-tooltip').remove();
    });


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




