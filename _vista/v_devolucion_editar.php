<?php 
//=======================================================================
// VISTA: v_devoluciones_editar.php
//=======================================================================
?>
<!-- page content-->
<div class="right_col" role="main"> 
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Editar Devolución</h3>
            </div>
            <div class="title_right">
                <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                    <a href="devoluciones_mostrar.php" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Volver a Devoluciones
                    </a>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Datos de Devolución - ID: <?php echo $devolucion_datos[0]['id_devolucion']; ?></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br>
                        <form class="form-horizontal form-label-left" action="devoluciones_editar.php?id=<?php echo $id_devolucion; ?>" method="post" enctype="multipart/form-data">
                            
                            <!-- Información básica -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Almacén:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" 
                                        value="<?php echo $devolucion_datos[0]['nom_almacen']; ?>" readonly>
                                    <input type="hidden" id="id_almacen" name="id_almacen" 
                                        value="<?php echo $devolucion_datos[0]['id_almacen']; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Ubicación:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" 
                                        value="<?php echo $devolucion_datos[0]['nom_ubicacion']; ?>" readonly>
                                    <input type="hidden" id="id_ubicacion" name="id_ubicacion" 
                                        value="<?php echo $devolucion_datos[0]['id_ubicacion']; ?>">
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
                                    <textarea name="obs_devolucion" class="form-control" rows="2" placeholder="Observaciones"><?php echo htmlspecialchars($devolucion_datos[0]['obs_devolucion']); ?></textarea>
                                </div>
                            </div>

                            <!--<div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Cliente destino:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        value="<?php echo htmlspecialchars($devolucion_datos[0]['nom_cliente_destino']); ?>" 
                                        readonly
                                    >
                                    <input 
                                        type="hidden" 
                                        name="id_cliente_destino" 
                                        value="<?php echo htmlspecialchars($devolucion_datos[0]['id_cliente_destino']); ?>"
                                    >
                                </div>
                            </div>-->

                            <div class="ln_solid"></div>

                            <!-- Materiales -->
                            <div class="x_title">
                                <h4>Materiales a Devolver</h4>
                                <div class="clearfix"></div>
                            </div>

                            <div id="contenedor-materiales">
                                <?php 
                                $contador = 0;
                                foreach ($devolucion_detalles as $detalle) { 
                                ?>
                                <div class="material-item border p-3 mb-3">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <label>Material <span class="text-danger">*</span>:</label>
                                            <div class="input-group">
                                                <input type="text" name="descripcion[]" class="form-control" 
                                                       placeholder="Material" 
                                                       value="<?php echo htmlspecialchars($detalle['det_devolucion_detalle']); ?>" required>
                                                <input type="hidden" name="id_producto[]" value="<?php echo $detalle['id_producto']; ?>">
                                                <input type="hidden" name="id_devolucion_detalle[]" value="<?php echo $detalle['id_devolucion_detalle']; ?>">
                                                <button onclick="buscarMaterial(this)" class="btn btn-secondary btn-xs" type="button">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Cantidad <span class="text-danger">*</span>:</label>
                                            <input type="number" name="cantidad[]" class="form-control" step="0.01" 
                                                   value="<?php echo $detalle['cant_devolucion_detalle']; ?>" required>
                                        </div>
                                        <div class="col-md-3" style="display: none;">
                                            <label>Stock Disponible:</label>
                                            <div class="form-control" id="stock-disponible-<?php echo $contador; ?>" style="background-color: #f8f9fa;">
                                                <?php 
                                                // Calcular stock disponible actual + cantidad ya asignada
                                                $stock_actual = ObtenerStockDisponible($detalle['id_producto'], $devolucion_datos[0]['id_almacen'], $devolucion_datos[0]['id_ubicacion']);
                                                // si el cliente destino es ARCE (id = 1) no se suma la devolución
                                                $stock_con_devolucion = $stock_actual + $detalle['cant_devolucion_detalle'];
                                                
                                                echo number_format($stock_con_devolucion, 2);
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12 d-flex justify-content-end">
                                            <button type="button" class="btn btn-danger btn-sm eliminar-material" 
                                                    <?php echo (count($devolucion_detalles) <= 1) ? 'style="display: none;"' : ''; ?>>
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

                            <!-- Botones -->
                            <div class="form-group">
                                <div class="col-md-2 offset-md-8">
                                    <button type="reset" class="btn btn-outline-danger btn-block">Limpiar</button>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" name="actualizar" id="btn_actualizar" class="btn btn-success btn-block">
                                        <i class="fa fa-save"></i> Actualizar Devolución
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12">
                                    <p><span class="text-danger">*</span> Los campos obligatorios son requeridos.</p>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

let currentSearchButton = null;

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
    let contadorMateriales = 1;

    // --- Agregar / eliminar materiales (clon)
    function actualizarEventosEliminar() {
        document.querySelectorAll('.eliminar-material').forEach(btn => {
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

            //Limpiar el id_devolucion_detalle para nuevos registros
            const inputIdDetalle = nuevoMaterial.querySelector('input[name="id_devolucion_detalle[]"]');
            if (inputIdDetalle) {
                inputIdDetalle.value = ''; // Vacío indica que es un registro NUEVO
            }

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
        almacenSelect.addEventListener('change', function() {
            limpiarTodosMateriales();  // limpia productos al cambiar almacén
            actualizarStocks();        // refresca stock
        });

        ubicacionSelect.addEventListener('change', function() {
            limpiarTodosMateriales();  // limpia productos al cambiar ubicación
            actualizarStocks();        // refresca stock
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
});

document.addEventListener("DOMContentLoaded", function () {
    const clienteSelect = document.getElementById('id_cliente_destino');
    const almacenSelect = document.getElementById('id_almacen');
    const ubicacionSelect = document.getElementById('id_ubicacion');

    if (clienteSelect && almacenSelect && ubicacionSelect) {
        // deshabilitar al inicio si no hay almacén/ubicación
        if (!almacenSelect.value || !ubicacionSelect.value) {
            clienteSelect.disabled = true;
        }

        function validarClienteSelect() {
            if (!almacenSelect.value || !ubicacionSelect.value) {
                clienteSelect.value = "";
                clienteSelect.disabled = true;
            } else {
                clienteSelect.disabled = false;
            }
        }

        almacenSelect.addEventListener('change', validarClienteSelect);
        ubicacionSelect.addEventListener('change', validarClienteSelect);

        clienteSelect.addEventListener('change', function() {
            if (!almacenSelect.value || !ubicacionSelect.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atención',
                    text: 'Primero debes seleccionar un almacén y una ubicación antes de elegir cliente.',
                    confirmButtonText: 'Entendido'
                });
                clienteSelect.value = "";
                clienteSelect.disabled = true;
            }
        });
    }
});

</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAlmacen = document.getElementById('id_almacen');
    const selectCliente = document.getElementById('id_cliente_destino');
    if (!selectAlmacen || !selectCliente) return; // <-- ✅ evita el error si no existen

    // 🔹 Cargar clientes por almacén
    function cargarClientes(idAlmacen, clienteSeleccionado = null) {
        console.log('📦 Cargando clientes para almacén:', idAlmacen);

        selectCliente.innerHTML = '<option value="">Cargando clientes...</option>';
        if (!idAlmacen) return;

        fetch('../_controlador/clientes_por_almacen.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id_almacen=' + encodeURIComponent(idAlmacen)
        })
        .then(res => res.json())
        .then(data => {
            console.log('✅ Respuesta del servidor:', data);

            selectCliente.innerHTML = '<option value="">Seleccionar Cliente</option>';
            let tieneArce = false;

            if (Array.isArray(data) && data.length > 0) {
                data.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id_cliente;
                    opt.textContent = c.nom_cliente;
                    if (String(c.id_cliente) === String(clienteSeleccionado)) {
                        opt.selected = true;
                    }
                    selectCliente.appendChild(opt);
                    if (parseInt(c.id_cliente) === 9) tieneArce = true;
                });
            } else {
                console.warn('⚠️ No se encontraron clientes para este almacén.');
            }

            // Agregar cliente ARCE si no está
            if (!tieneArce) {
                const optArce = document.createElement('option');
                optArce.value = 9;
                optArce.textContent = 'ARCE';
                if (String(clienteSeleccionado) === '9') optArce.selected = true;
                selectCliente.appendChild(optArce);
            }

            // 🔹 Dejar el select bloqueado (solo lectura)
            selectCliente.disabled = true;
            selectCliente.style.backgroundColor = "#f9f9f9";
            selectCliente.style.cursor = "not-allowed";
        })
        .catch(err => {
            console.error('❌ Error al cargar clientes:', err);
            selectCliente.innerHTML = '<option value="">Error al cargar clientes</option>';
        });
    }

    // 🔹 Cuando cambia el almacén
    selectAlmacen.addEventListener('change', function() {
        cargarClientes(this.value);
    });

    // 🔹 Carga inicial automática con el almacén y cliente actual
    const almacenInicial = selectAlmacen.value;
    if (almacenInicial) {
        cargarClientes(almacenInicial, clienteActual);
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