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
                                <label class="control-label col-md-3">Almacén Origen<span class="text-danger">*</span>:</label>
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
                                <label class="control-label col-md-3">Ubicación Origen<span class="text-danger">*</span>:</label>
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
                                <label class="control-label col-md-3">Centro de Costos <span class="text-danger">*</span>:</label>
                                <div class="col-md-9">
                                    <?php if ($centro_costo_usuario) { ?>
                                        <input type="text" class="form-control" 
                                            value="<?php echo htmlspecialchars($centro_costo_usuario['nom_centro_costo']); ?>" 
                                            readonly style="background-color: #f9f9f9;">
                                        <input type="hidden" name="id_centro_costo" 
                                            value="<?php echo $centro_costo_usuario['id_centro_costo']; ?>">
                                    <?php } else { ?>
                                        <input type="text" class="form-control" 
                                            value="Sin centro de costo asignado" 
                                            readonly style="background-color: #f9f9f9;">
                                        <small class="text-danger">No tienes un área asignada. Contacta con el administrador.</small>
                                    <?php } ?>
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
                                <!--  ESTRUCTURA CORREGIDA - Plantilla: se clonará -->
                                <div class="material-item border p-3 mb-3">
                                    <!-- FILA 1: Material y Cantidad -->
                                    <div class="row">
                                        <div class="col-md-8">
                                            <label>Material <span class="text-danger">*</span>:</label>
                                            <div class="input-group">
                                                <input type="text" name="descripcion[]" class="form-control" placeholder="Buscar material..." readonly required>
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
                                    </div>
                                    <!--  FIN de FILA 1 -->

                                    <!-- FILA 2: Centros de Costo (SEPARADA) -->
                                    <div class="row mt-2">
                                        <div class="col-md-12">
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
                                    </div>
                                    <!--  FIN de FILA 2 -->

                                    <!-- FILA 3: Botón Eliminar -->
                                    <div class="row mt-2">
                                        <div class="col-md-12 d-flex justify-content-end">
                                            <button type="button" class="btn btn-danger btn-sm eliminar-material" style="display: none;">
                                                <i class="fa fa-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    </div>
                                    <!--  FIN de FILA 3 -->
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
                                        <i class="bi bi-x-square"></i> Cancelar
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <button type="submit" name="registrar" id="btn_registrar"
                                        class="btn btn-success btn-block"><i class="bi bi-pencil-square"></i> Registrar</button>
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

<!-- ============================================ -->
<!-- JAVASCRIPT CORREGIDO Y MEJORADO -->
<!-- ============================================ -->

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
        "pageLength": 10,
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
            let inputCantidad = materialItem.querySelector('input[name="cantidad[]"]');

            if (inputDescripcion) inputDescripcion.value = nombreProducto;
            if (inputId) inputId.value = idProducto;
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

    // ============================================
    //  ASIGNACIÓN AUTOMÁTICA DEL CENTRO DE COSTO DEL SOLICITANTE
    // ============================================
    const inputCentroCostoCabecera = document.querySelector('input[name="id_centro_costo"]');

    if (inputCentroCostoCabecera && inputCentroCostoCabecera.value) {
        const centroCostoSolicitante = inputCentroCostoCabecera.value;
        
        console.log(" Centro de costo del solicitante:", centroCostoSolicitante);
        
        // Función para aplicar a un material específico
        function aplicarCentroCostoAMaterial(materialItem) {
            const selectCentros = materialItem.querySelector('select.select2-centros-costo-detalle');
            if (selectCentros) {
                if ($(selectCentros).data('select2')) {
                    console.log("✅ Aplicando centro de costo a material existente");
                    setTimeout(() => {
                        $(selectCentros).val([centroCostoSolicitante]).trigger('change');
                    }, 100);
                } else {
                    console.log(" Inicializando Select2 y aplicando centro de costo");
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
                        $(selectCentros).val([centroCostoSolicitante]).trigger('change');
                    }, 200);
                }
            }
        }
        
        // Aplicar a materiales existentes (el primer material al cargar)
        setTimeout(() => {
            document.querySelectorAll('.material-item').forEach(item => {
                aplicarCentroCostoAMaterial(item);
            });
        }, 500);
        
        // Observar nuevos materiales agregados dinámicamente
        const contenedorMateriales = document.getElementById('contenedor-materiales');
        if (contenedorMateriales) {
            const observador = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(nodo) {
                        if (nodo.classList && nodo.classList.contains('material-item')) {
                            console.log("✅ Nuevo material agregado, aplicando centro de costo");
                            aplicarCentroCostoAMaterial(nodo);
                        }
                    });
                });
            });
            
            observador.observe(contenedorMateriales, { childList: true });
        }
    }

    // ============================================
    // AGREGAR / ELIMINAR MATERIALES (CLON)
    // ============================================
    function actualizarEventosEliminar() {
        document.querySelectorAll('.eliminar-material').forEach(btn => {
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
            const materialOriginal = contenedor.querySelector('.material-item');
            
            if (materialOriginal) {
                // 1. GUARDAR valores de Select2 ANTES de destruir
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
                
                // 3. CLONAR el elemento COMPLETO
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
                
                // 5. LIMPIAR TODOS los campos del NUEVO material
                const inputs = nuevoMaterial.querySelectorAll('input, textarea');
                inputs.forEach(input => {
                    if (input.name === 'id_producto[]') {
                        input.value = '';
                    } else if (input.name === 'descripcion[]') {
                        input.value = '';
                    } else if (input.name === 'cantidad[]') {
                        input.value = '';
                    } else if (input.type !== 'button') {
                        input.value = '';
                    }
                });
                
                // 6. LIMPIAR y PREPARAR los selects del NUEVO material
                const selectsClonados = nuevoMaterial.querySelectorAll('select');
                selectsClonados.forEach(select => {
                    //  CORRECCIÓN PRINCIPAL: Actualizar name con índice correcto
                    if (select.name && select.name.includes('centros_costo')) {
                        select.name = `centros_costo[${contadorMateriales}][]`;
                        console.log('✅ Name actualizado:', select.name);
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
                    if (select.name && select.name.includes('centros_costo')) {
                        $(select).select2({
                            placeholder: 'Seleccionar uno o más centros de costo...',
                            allowClear: true,
                            width: '100%',
                            multiple: true,
                            language: {
                                noResults: function () { return 'No se encontraron resultados'; }
                            }
                        });
                        
                        // APLICAR AUTOMÁTICAMENTE EL CENTRO DE COSTO DEL SOLICITANTE
                        if (inputCentroCostoCabecera && inputCentroCostoCabecera.value) {
                            setTimeout(() => {
                                $(select).val([inputCentroCostoCabecera.value]).trigger('change');
                            }, 100);
                        }
                    }
                });
                
                // 10. INCREMENTAR contador y actualizar eventos
                contadorMateriales++;
                actualizarEventosEliminar();
                configurarEventosCantidad();
                
                // 11. SCROLL al nuevo elemento
                nuevoMaterial.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                
                console.log('✅ Nuevo material agregado en devoluciones, contador:', contadorMateriales);
            }
        });
    }
    actualizarEventosEliminar();

    // --- Validación / eventos para inputs cantidad
    function validarStock(inputCantidad, nombreProducto) {
        const cantidad = parseFloat(inputCantidad.value) || 0;
        
        // Si no hay producto seleccionado, no validar stock aquí
        if (!nombreProducto || !nombreProducto.trim()) return true;

        if (cantidad <= 0) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ 
                    icon:'warning', 
                    title:'Cantidad inválida', 
                    text:`Debe ingresar una cantidad mayor a cero.`, 
                    confirmButtonText:'Entendido' 
                });
            }
            return false;
        }
        return true;
    }

    function configurarEventosCantidad() {
        document.querySelectorAll('input[name="cantidad[]"]').forEach(input => {
            input.oninput = null;
            input.onblur = null;
            
            input.oninput = function(e) {
                const materialItem = input.closest('.material-item');
                const descripcionInput = materialItem.querySelector('input[name="descripcion[]"]');
                if (descripcionInput) validarStock(input, descripcionInput.value);
            };
            
            input.onblur = function(e) {
                const materialItem = input.closest('.material-item');
                const descripcionInput = materialItem.querySelector('input[name="descripcion[]"]');
                if (descripcionInput && input.value) validarStock(input, descripcionInput.value);
            };
        });
    }
    configurarEventosCantidad();

    // --- Actualizar cuando cambie almacén o ubicación
    const almacenSelect = document.getElementById('id_almacen');
    const ubicacionSelect = document.getElementById('id_ubicacion');

    if (almacenSelect && ubicacionSelect) {
        // Cambiar almacén
        almacenSelect.addEventListener('change', function() {
            const valorAnterior = this.dataset.valorAnterior || '';
            const valorActual = this.value;
            const hayMateriales = document.querySelectorAll('.material-item input[name="id_producto[]"]').length > 0;

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
                this.dataset.valorAnterior = valorActual;
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
                this.dataset.valorAnterior = valorActual;
            }
        });

        // Guardar valor inicial
        [almacenSelect, ubicacionSelect].forEach(sel => {
            sel.addEventListener('focus', function() {
                this.dataset.valorAnterior = this.value;
            });
        });
    }

    // --- Limpia todos los materiales del formulario
    function limpiarTodosMateriales() {
        const contenedor = document.getElementById('contenedor-materiales');
        if (!contenedor) return;

        const items = contenedor.querySelectorAll('.material-item');
        items.forEach((item, i) => {
            if (i > 0) item.remove();
        });

        const primero = contenedor.querySelector('.material-item');
        if (primero) {
            primero.querySelectorAll('input, textarea, select').forEach(input => {
                if (input.type !== 'button') input.value = '';
            });
        }
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

                if (descripcion && cantidad > 0) tieneMateriales = true;
                if (descripcion && cantidad <= 0) errores.push(`Debe ingresar una cantidad válida para "${descripcion}"`);
            });

            if (!tieneMateriales) errores.push('Debe agregar al menos un material con cantidad válida');

            if (errores.length > 0) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ 
                        icon:'error', 
                        title:'Errores en el formulario', 
                        html: errores.join('<br>'), 
                        confirmButtonText:'Revisar' 
                    });
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
            $ubicacion.trigger('change.select2');
        } else {
            $ubicacion.trigger('change');
        }
    }

    $almacen.off('change.filtroUbicaciones').on('change.filtroUbicaciones', function () {
        var idCliente = $(this).find(':selected').data('idCliente');

        if (typeof idCliente === 'undefined') {
            idCliente = $(this).find(':selected').data('id-cliente') || $(this).find(':selected').attr('data-id-cliente');
        }

        console.log("ID CLIENTE DEL ALMACÉN:", idCliente);

        rellenarUbicacionesSegunAlmacen(Number(idCliente));
    });

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
    background-color: #ffe6e6 !important;
    border: 2px solid #ff4d4d !important;
    box-shadow: 0 0 10px rgba(255, 77, 77, 0.6);
    transition: all 0.3s ease;
}
</style>