<?php 
//=======================================================================
// VISTA: v_salidas_nuevo.php
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Nueva Salida</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Datos de Salida</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br>
                        <form class="form-horizontal form-label-left" action="salidas_nuevo.php" method="post" enctype="multipart/form-data">
                            <?php if ($desde_pedido > 0 && $pedido_origen): ?>
                            <input type="hidden" name="id_pedido_origen" value="<?php echo $desde_pedido; ?>">

                            <div class="alert alert-dismissible fade show" role="alert" 
                                 style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                                        border: none; 
                                        border-radius: 10px; 
                                        color: white; 
                                        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="color: white; opacity: 0.8;">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                    <div style="background: rgba(255,255,255,0.2); 
                                                border-radius: 50%; 
                                                width: 50px; 
                                                height: 50px; 
                                                display: flex; 
                                                align-items: center; 
                                                justify-content: center; 
                                                margin-right: 15px;">
                                        <i class="fa fa-truck" style="font-size: 24px;"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0" style="font-weight: 600; font-size: 18px;">
                                            Generando Salida desde Pedido
                                        </h5>
                                        <small style="opacity: 0.9;">Los productos se han cargado automáticamente</small>
                                    </div>
                                </div>
                                
                                <div style="background: rgba(255,255,255,0.15); 
                                            border-radius: 8px; 
                                            padding: 12px; 
                                            margin-top: 10px;">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <strong style="display: block; opacity: 0.9; font-size: 12px; margin-bottom: 4px;">
                                                <i class="fa fa-barcode"></i> CÓDIGO DEL PEDIDO
                                            </strong>
                                            <span style="font-size: 16px; font-weight: 600;">
                                                <?php echo $pedido_origen['cod_pedido']; ?>
                                            </span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong style="display: block; opacity: 0.9; font-size: 12px; margin-bottom: 4px;">
                                                <i class="fa fa-tag"></i> NOMBRE DEL PEDIDO
                                            </strong>
                                            <span style="font-size: 16px; font-weight: 600;">
                                                <?php echo $pedido_origen['nom_pedido']; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div style="margin-top: 12px; 
                                            padding: 10px; 
                                            background: rgba(255,255,255,0.1); 
                                            border-left: 3px solid rgba(255,255,255,0.5); 
                                            border-radius: 5px;">
                                    <i class="fa fa-lightbulb-o"></i>
                                    <small style="opacity: 0.95;">
                                        <strong>Importante:</strong> No puede agregar productos adicionales. 
                                        Solo puede modificar cantidades o eliminar items según necesite.
                                    </small>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Información básica del traslado -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Tipo de Material <span class="text-danger">*</span>:</label>
                                <div class="col-md-4 col-sm-4">
                                    <select name="id_material_tipo" class="form-control" required>
                                        <option value="">Seleccionar</option>
                                        <?php foreach ($material_tipos as $material_tipo) { 
                                            if ($material_tipo['id_material_tipo'] != 1) {
                                                $selected = ($id_material_tipo_pedido > 0 && $id_material_tipo_pedido == $material_tipo['id_material_tipo']) ? 'selected' : '';
                                            ?>
                                            <option value="<?php echo $material_tipo['id_material_tipo']; ?>" <?php echo $selected; ?>>
                                                <?php echo $material_tipo['nom_material_tipo']; ?>
                                            </option>
                                        <?php }} ?>
                                    </select>
                                </div>
                                <label class="control-label col-md-2 col-sm-2">Nº Documento de Salida <span class="text-danger">*</span>:</label>
                                <div class="col-md-3 col-sm-3">
                                    <input type="text" name="ndoc_salida" class="form-control" placeholder="Número de documento de Salida" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Fecha Requerida <span class="text-danger">*</span>:</label>
                                <div class="col-md-4 col-sm-4">
                                    <input type="date" name="fec_req_salida" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <label class="control-label col-md-2 col-sm-2">Registrado por:</label>
                                <div class="col-md-3 col-sm-3">
                                    <input type="text" class="form-control" value="<?php echo $usuario_sesion; ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Observaciones:</label>
                                <div class="col-md-9 col-sm-9">
                                    <textarea name="obs_salida" class="form-control" rows="2" placeholder="Observaciones"></textarea>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <div class="form-group">
                                <label>Subir Documento</label>
                                <input type="file" name="documento[]" id="documento" class="form-control" multiple required>
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
                                            <?php foreach ($almacenes as $almacen) { 
                                                $selected = ($pedido_origen && $pedido_origen['id_almacen'] == $almacen['id_almacen']) ? 'selected' : '';
                                            ?>
                                                <option value="<?php echo $almacen['id_almacen']; ?>" <?php echo $selected; ?>>
                                                    <?php echo $almacen['nom_almacen']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Ubicación Origen <span class="text-danger">*</span>:</label>
                                        <select name="id_ubicacion_origen" id="id_ubicacion_origen" class="form-control" required>
                                            <option value="">Seleccionar</option>
                                            <?php foreach ($ubicaciones as $ubicacion) { 
                                                $selected = ($pedido_origen && $pedido_origen['id_ubicacion'] == $ubicacion['id_ubicacion']) ? 'selected' : '';
                                            ?>
                                                <option value="<?php echo $ubicacion['id_ubicacion']; ?>" <?php echo $selected; ?>>
                                                    <?php echo $ubicacion['nom_ubicacion']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <!-- Personal Encargado -->
                                    <div class="form-group">
                                        <label class="control-label">Personal Encargado:</label>
                                        <select name="id_personal_encargado" id="id_personal_encargado" class="form-control">
                                            <option value="0">No especificado</option>
                                            <?php foreach ($personal as $persona) { ?>
                                                <option value="<?php echo $persona['id_personal']; ?>" 
                                                    <?php echo ($persona['id_personal'] == $id_personal) ? 'selected' : ''; ?>>
                                                    <?php echo $persona['nom_personal']; ?>
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
                                                <option value="<?php echo $almacen['id_almacen']; ?>">
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
                                                <option value="<?php echo $ubicacion['id_ubicacion']; ?>">
                                                    <?php echo $ubicacion['nom_ubicacion']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <!-- Personal que Recibe -->
                                    <div class="form-group">
                                        <label class="control-label">Personal que Recibe:</label>
                                        <select name="id_personal_recibe" id="id_personal_recibe" class="form-control">
                                            <option value="0">No especificado</option>
                                            <?php foreach ($personal as $persona) { ?>
                                                <option value="<?php echo $persona['id_personal']; ?>">
                                                    <?php echo $persona['nom_personal']; ?>
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
                                <?php if ($desde_pedido > 0 && !empty($items_pedido)): ?>
                                    <?php foreach ($items_pedido as $index => $item): ?>
                                    <div class="material-item border p-3 mb-3" style="background-color: #f0f8ff;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Material <span class="text-danger">*</span>:</label>
                                                <div class="input-group">
                                                    <input type="text" name="descripcion[]" class="form-control" 
                                                           value="<?php echo htmlspecialchars($item['descripcion']); ?>" 
                                                           style="background-color: #e8f4f8;" required readonly>
                                                    <input type="hidden" name="id_producto[]" value="<?php echo $item['id_producto']; ?>">
                                                    <button onclick="buscarMaterial(this)" class="btn btn-secondary btn-xs" type="button" disabled style="cursor: not-allowed;">
                                                        <i class="fa fa-search"></i>
                                                    </button>
                                                </div>
                                                <small class="text-muted">
                                                    <i class="fa fa-tag"></i> Producto pre-cargado desde pedido
                                                </small>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Cantidad <span class="text-danger">*</span>:</label>
                                                <input type="number" name="cantidad[]" class="form-control" 
                                                       value="<?php echo $item['cantidad']; ?>" 
                                                       step="0.01" min="0.01" max="<?php echo $item['stock_disponible']; ?>" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Stock Disponible:</label>
                                                <div class="form-control" id="stock-disponible-<?php echo $index; ?>" 
                                                     style="background-color: #d4edda; font-weight: bold; color: #155724;">
                                                    <?php echo number_format($item['stock_disponible'], 2); ?>
                                                </div>
                                                <label><?php echo number_format($item['stock_disponible'], 2); ?> /
                                                    <?php echo isset($item['stock_fisico']) ? number_format($item['stock_fisico'], 2) : '0.00'; ?></label>
                                                
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-12 d-flex justify-content-end">
                                                <button type="button" class="btn btn-danger btn-sm eliminar-material" 
                                                        <?php echo ($index == 0 && count($items_pedido) == 1) ? 'style="display: none;"' : ''; ?>>
                                                    <i class="fa fa-trash"></i> Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <!-- Material vacío por defecto (código original) -->
                                    <div class="material-item border p-3 mb-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Material <span class="text-danger">*</span>:</label>
                                                <div class="input-group">
                                                    <input type="text" name="descripcion[]" class="form-control" placeholder="Material" required>
                                                    <input type="hidden" name="id_producto[]" value="">
                                                    <button onclick="buscarMaterial(this)" class="btn btn-secondary btn-xs" data-toggle="tooltip" title="Buscar Material" type="button">
                                                        <i class="fa fa-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Cantidad <span class="text-danger">*</span>:</label>
                                                <input type="number" name="cantidad[]" class="form-control" step="0.01" required>
                                            </div>
                                            <!--
                                            <div class="col-md-3">
                                                <label>Stock Disponible:</label>
                                                <div class="form-control" id="stock-disponible-0" style="background-color: #f8f9fa;">
                                                    0.00
                                                </div>
                                            </div>
                                            -->
                                            <div hidden>
                                                <div id="stock-disponible-0">0.00</div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-12 d-flex justify-content-end">
                                                <button type="button" class="btn btn-danger btn-sm eliminar-material" style="display: none;">
                                                    <i class="fa fa-trash"></i> Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <?php if ($desde_pedido > 0): ?>
                                    <!-- Si viene de pedido, NO mostrar el botón agregar -->
                                    <div class="alert alert-warning" style="font-size: 13px; padding: 10px;">
                                        <i class="fa fa-info-circle"></i> 
                                        <strong>Nota:</strong> Estos son los productos del pedido. No puede agregar productos adicionales. 
                                        Solo puede modificar cantidades o eliminar items si es necesario.
                                    </div>
                                <?php else: ?>
                                    <!-- Solo mostrar el botón si NO viene de pedido -->
                                    <button type="button" id="agregar-material" class="btn btn-info btn-sm">
                                        <i class="fa fa-plus"></i> Agregar Material
                                    </button>
                                <?php endif; ?>
                            </div>



                            <!-- BOTONES -->
                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 offset-md-8">
                                    <a href="salidas_mostrar.php" class="btn btn-outline-danger btn-block">
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
<!-- /page content -->

<!-- Modal para buscar productos  -->
<div class="modal fade" id="buscar_producto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-xl" role="document">
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
const idPedido = <?php echo $desde_pedido > 0 ? intval($desde_pedido) : 0; ?>;

function buscarMaterial(button) {
    // Obtener valores de almacén y ubicación origen
    const idAlmacenOrigen = document.getElementById('id_almacen_origen').value;
    const idUbicacionOrigen = document.getElementById('id_ubicacion_origen').value;
    const idAlmacenDestino = document.getElementById('id_almacen_destino').value;
    const idUbicacionDestino = document.getElementById('id_ubicacion_destino').value;
    
    // Obtener el tipo de material desde el formulario
    const idMaterialTipo = document.querySelector('select[name="id_material_tipo"]');
    const tipoMaterial = idMaterialTipo ? idMaterialTipo.value : '';
    
    // VALIDACIÓN 1: Almacén y ubicación origen requeridos
    if (!idAlmacenOrigen || !idUbicacionOrigen) {
        mostrarAlerta('warning', 'Almacén y Ubicación requeridos', 
            'Debe seleccionar un almacén y ubicación de origen antes de buscar productos.');
        return;
    }
    
    // VALIDACIÓN 2: Tipo de material requerido
    if (!tipoMaterial) {
        mostrarAlerta('warning', 'Tipo de material requerido', 
            'Debe seleccionar un tipo de material antes de buscar productos.');
        return;
    }
    
    // VALIDACIÓN 3: Verificar que no sea material tipo "NA" (id = 1)
    if (tipoMaterial == '1') {
        mostrarAlerta('error', 'Tipo de material no válido', 
            'No se puede realizar salidas para materiales tipo "NA". Este tipo está reservado para servicios.');
        return;
    }
    
    // VALIDACIÓN 4: Verificar que origen y destino no sean iguales
    if (idAlmacenOrigen && idUbicacionOrigen && idAlmacenDestino && idUbicacionDestino) {
        if (idAlmacenOrigen === idAlmacenDestino && idUbicacionOrigen === idUbicacionDestino) {
            mostrarAlerta('warning', 'Ubicaciones idénticas', 
                'No puede realizar una salida hacia la misma ubicación de origen. Seleccione un destino diferente.');
            return;
        }
    }
    
    // Guardar referencia al botón que se clickeó
    currentSearchButton = button;
    
    // Abrir la modal
    $('#buscar_producto').modal('show');
    
    // Cargar los productos en la tabla con información de stock Y filtro de tipo de material
    cargarProductos(idAlmacenOrigen, idUbicacionOrigen, tipoMaterial);
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
                if (tipoMaterial) {
                    d.tipo_material = tipoMaterial;
                }
                return d;
            },
            "error": function(xhr, error, thrown) {
                console.error('Error en DataTable:', error);
                mostrarAlerta('error', 'Error al cargar productos', 
                    'No se pudieron cargar los productos. Verifique su conexión.');
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
            "zeroRecords": "No se encontraron productos con stock disponible",
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
            "emptyTable": "No hay productos disponibles con stock"
        }
    });
}

function seleccionarProducto(idProducto, nombreProducto, stockDisponible) {
    // 🔹 Verificar si el producto ya está seleccionado en algún material-item
    const materialItems = document.querySelectorAll('.material-item');
    let productoExistente = null;

    materialItems.forEach(item => {
        const inputId = item.querySelector('input[name="id_producto[]"]');
        if (inputId && parseInt(inputId.value) === parseInt(idProducto)) {
            productoExistente = item;
        }
    });

    if (productoExistente) {
        // 🔸 Producto ya existe → resaltarlo y mostrar aviso
        productoExistente.classList.add('duplicado-resaltado');
        productoExistente.scrollIntoView({ behavior: 'smooth', block: 'center' });

        setTimeout(() => productoExistente.classList.remove('duplicado-resaltado'), 2000);

        $('#buscar_producto').modal('hide');
        return; // detener aquí
    }
    
    if (currentSearchButton) {
        let materialItem = currentSearchButton.closest('.material-item');
        
        if (materialItem) {
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
                        inputCantidad.setAttribute('max', stockDisponible);
                        inputCantidad.setAttribute('step', '0.01');
                    } else {
                        // Si no hay stock, deshabilitar el campo
                        inputCantidad.value = '';
                        inputCantidad.setAttribute('readonly', 'readonly');
                        inputCantidad.setAttribute('title', 'No hay stock disponible');
                    }
                }
            }
        }
    }
    
    // Cerrar la modal
    $('#buscar_producto').modal('hide');
    
    // Mostrar mensaje según el stock
    if (parseFloat(stockDisponible) <= 0) {
        mostrarAlerta('warning', 'Producto sin stock',
            `El producto "${nombreProducto}" no tiene stock disponible en esta ubicación.`);
    } else {
       Swal.fire({
            icon: 'success',
            title: 'Producto seleccionado',
            text: 'El producto "' + nombreProducto + '" ha sido seleccionado.',
            showConfirmButton: false,
            timer: 1500
        });
    }
    
    // Limpiar la referencia
    currentSearchButton = null;
}

// Función auxiliar para mostrar alertas
function mostrarAlerta(tipo, titulo, mensaje, tiempo = null) {
    if (typeof Swal !== 'undefined') {
        const config = {
            icon: tipo,
            title: titulo,
            text: mensaje,
            confirmButtonText: 'Entendido'
        };
        
        if (tiempo) {
            config.showConfirmButton = false;
            config.timer = tiempo;
        }
        
        Swal.fire(config);
    } else {
        alert(titulo + ": " + mensaje);
    }
}

// Script para manejo dinámico de materiales y validaciones
document.addEventListener('DOMContentLoaded', function() {
    let contadorMateriales = 1;
    
    // NUEVO: Verificar si viene de pedido
    const desdePedido = <?php echo $desde_pedido > 0 ? 'true' : 'false'; ?>;
    
    // Función para validar stock en tiempo real
    function validarStock(inputCantidad, stockElement, inputDescripcion) {
        const cantidad = parseFloat(inputCantidad.value) || 0;
        const stock = parseFloat(stockElement.textContent) || 0 ;
        const nombreProducto = inputDescripcion.value;
        
        // Si no hay producto seleccionado, permitir cualquier cantidad
        if (!nombreProducto.trim()) {
            return true;
        }
        
        // VALIDACIÓN: Si el stock es 0 o menor
        if (stock <= 0) {
            inputCantidad.value = '';
            mostrarAlerta('error', 'Sin stock disponible',
                `El producto "${nombreProducto}" no tiene stock disponible en esta ubicación.`);
            return false;
        }
        
        // VALIDACIÓN: Si la cantidad excede el stock
        if (cantidad > stock) {
            mostrarAlerta('warning', 'Cantidad excede el stock',
                `La cantidad ingresada (${cantidad}) excede el stock disponible (${stock.toFixed(2)}) para "${nombreProducto}".`);
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
    
    // VALIDACIÓN: Ubicaciones origen y destino
    function validarUbicaciones() {
        const almacenOrigen = document.getElementById('id_almacen_origen');
        const ubicacionOrigen = document.getElementById('id_ubicacion_origen');
        const almacenDestino = document.getElementById('id_almacen_destino');
        const ubicacionDestino = document.getElementById('id_ubicacion_destino');
        
        if (almacenOrigen && ubicacionOrigen && almacenDestino && ubicacionDestino) {
            if (almacenOrigen.value && ubicacionOrigen.value && 
                almacenDestino.value && ubicacionDestino.value) {
                
                if (almacenOrigen.value === almacenDestino.value && 
                    ubicacionOrigen.value === ubicacionDestino.value) {
                    
                    mostrarAlerta('warning', 'Ubicaciones idénticas',
                        'No puede realizar una salida hacia la misma ubicación de origen. Seleccione un destino diferente.');
                    
                    // Limpiar destino
                    almacenDestino.value = '';
                    ubicacionDestino.value = '';
                    return false;
                }
            }
        }
        return true;
    }
    
    // Eventos para validar ubicaciones en tiempo real
    ['id_almacen_destino', 'id_ubicacion_destino'].forEach(id => {
        const elemento = document.getElementById(id);
        if (elemento) {
            elemento.addEventListener('change', validarUbicaciones);
        }
    });
    
    // Agregar nuevo material
    const btnAgregarMaterial = document.getElementById('agregar-material');
    if (btnAgregarMaterial) {
        // Si viene de pedido, deshabilitar el botón completamente
        if (desdePedido) {
            btnAgregarMaterial.style.display = 'none';
        } else {
            btnAgregarMaterial.addEventListener('click', function() {
                const contenedor = document.getElementById('contenedor-materiales');
                const nuevoMaterial = document.querySelector('.material-item').cloneNode(true);
                
                // Limpiar los valores del nuevo elemento
                const inputs = nuevoMaterial.querySelectorAll('input, textarea, select');
                inputs.forEach(input => {
                    if (input.type !== 'button') {
                        input.value = '';
                        input.removeAttribute('readonly');
                        input.removeAttribute('title');
                        input.removeAttribute('disabled');
                    }
                });
                
                // Habilitar el botón de búsqueda en el nuevo item
                const btnBuscar = nuevoMaterial.querySelector('button[onclick*="buscarMaterial"]');
                if (btnBuscar) {
                    btnBuscar.removeAttribute('disabled');
                    btnBuscar.style.cursor = 'pointer';
                }
                
                // Actualizar el ID del elemento de stock
                const stockElement = nuevoMaterial.querySelector('[id^="stock-disponible-"]');
                if (stockElement) {
                    stockElement.id = 'stock-disponible-' + contadorMateriales;
                    stockElement.textContent = '0.00';
                    stockElement.style.backgroundColor = '#f8f9fa';
                    stockElement.style.color = '#333';
                }
                
                // Mostrar el botón eliminar
                const btnEliminar = nuevoMaterial.querySelector('.eliminar-material');
                if (btnEliminar) {
                    btnEliminar.style.display = 'block';
                }
                
                // Remover el estilo de fondo azul
                nuevoMaterial.style.backgroundColor = '';
                
                contenedor.appendChild(nuevoMaterial);
                contadorMateriales++;
                
                // Actualizar eventos
                actualizarEventosEliminar();
                configurarEventosCantidad();
            });
        }
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
    
    // VALIDACIÓN DEL FORMULARIO ANTES DE ENVIAR - MEJORADA
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            let errores = [];
            let tieneProductosSinStock = false;
            
            // VALIDACIÓN 1: Verificar que origen y destino no sean iguales
            const almacenOrigen = document.getElementById('id_almacen_origen').value;
            const ubicacionOrigen = document.getElementById('id_ubicacion_origen').value;
            const almacenDestino = document.getElementById('id_almacen_destino').value;
            const ubicacionDestino = document.getElementById('id_ubicacion_destino').value;
            
            if (almacenOrigen === almacenDestino && ubicacionOrigen === ubicacionDestino) {
                errores.push('No puede realizar una salida hacia la misma ubicación de origen. Seleccione un destino diferente.');
            }
            
            // VALIDACIÓN 2: Verificar tipo de material no sea "NA"
            const tipoMaterial = document.querySelector('select[name="id_material_tipo"]').value;
            if (tipoMaterial === '1') {
                errores.push('No se puede realizar salidas para materiales tipo "NA". Este tipo está reservado para servicios.');
            }
            
            // VALIDACIÓN 3: Verificar que al menos un material tenga cantidad
            const cantidades = document.querySelectorAll('input[name="cantidad[]"]');
            let tieneMateriales = false;
            
            cantidades.forEach(input => {
                if (input.value && parseFloat(input.value) > 0) {
                    tieneMateriales = true;
                }
            });
            
            if (!tieneMateriales) {
                errores.push('Debe agregar al menos un material con cantidad válida');
            }
            
            // VALIDACIÓN 4: Validar stocks y cantidades
            const materialesItems = document.querySelectorAll('.material-item');
            materialesItems.forEach((item, index) => {
                const inputCantidad = item.querySelector('input[name="cantidad[]"]');
                const stockElement = item.querySelector('[id^="stock-disponible-"]');
                const inputDescripcion = item.querySelector('input[name="descripcion[]"]');
                const inputIdProducto = item.querySelector('input[name="id_producto[]"]');
                
                // Solo validar si hay descripción Y id de producto (producto realmente seleccionado)
                if (inputDescripcion && inputDescripcion.value.trim() && 
                    inputIdProducto && inputIdProducto.value) {
                    
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
                
                // VALIDACIÓN ADICIONAL: Si hay descripción pero no ID de producto
                if (inputDescripcion && inputDescripcion.value.trim() && 
                    (!inputIdProducto || !inputIdProducto.value)) {
                    errores.push(`Debe seleccionar un producto válido desde el buscador para "${inputDescripcion.value}"`);
                }
            });
            
            // Mostrar errores si existen
            if (errores.length > 0) {
                e.preventDefault();
                
                let titulo = 'Errores en el formulario';
                let icono = 'error';
                
                if (tieneProductosSinStock) {
                    titulo = 'Productos sin stock disponible';
                    icono = 'warning';
                }
                
                mostrarAlerta(icono, titulo, errores.join('\n'));
            }
        });
    }

    // ACTUALIZAR STOCK CUANDO CAMBIE EL ALMACÉN O UBICACIÓN ORIGEN
    const almacenOrigen = document.getElementById('id_almacen_origen');
    const ubicacionOrigen = document.getElementById('id_ubicacion_origen');
    
    if (almacenOrigen && ubicacionOrigen) {
        // Cambiar almacén origen con confirmación
        almacenOrigen.addEventListener('change', function() {
            const valorAnterior = this.dataset.valorAnterior || '';
            const valorActual = this.value;
            const hayMateriales = document.querySelectorAll('.material-item input[name="id_producto[]"]').length > 0;

            if (valorAnterior && valorAnterior !== valorActual && hayMateriales) {
                Swal.fire({
                    title: '¿Cambiar almacén de origen?',
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
                this.dataset.valorAnterior = valorActual;
            }
        });

        // Cambiar ubicación origen con confirmación
        ubicacionOrigen.addEventListener('change', function() {
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

        // Guardar valor previo al enfocar
        [almacenOrigen, ubicacionOrigen].forEach(sel => {
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
        const idAlmacen = almacenOrigen.value;
        const idUbicacion = ubicacionOrigen.value;
        
        if (!idAlmacen || !idUbicacion) return;
        
        // Actualizar stocks para todos los materiales ya seleccionados
        const materialesItems = document.querySelectorAll('.material-item');
        materialesItems.forEach((item, index) => {
            const inputIdProducto = item.querySelector('input[name="id_producto[]"]');
            if (inputIdProducto && inputIdProducto.value) {
            
                let body = `id_producto=${encodeURIComponent(inputIdProducto.value)}&id_almacen=${encodeURIComponent(idAlmacen)}&id_ubicacion=${encodeURIComponent(idUbicacion)}`;

                if (idPedido && idPedido > 0) {
                    body += `&id_pedido=${encodeURIComponent(idPedido)}`;
                }

                // Crear un controlador temporal para obtener stock actualizado
                fetch(`obtener_stock.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: body
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const stockElement = item.querySelector('[id^="stock-disponible-"]');
                        const inputCantidad = item.querySelector('input[name="cantidad[]"]');
                        const inputDescripcion = item.querySelector('input[name="descripcion[]"]');
                        
                        if (stockElement) {
                            const nuevoStock = parseFloat(data.stock);
                            stockElement.textContent = nuevoStock.toFixed(2);
                            
                            if (inputCantidad) {
                                // Si no hay stock, limpiar cantidad y deshabilitar
                                if (nuevoStock <= 0) {
                                    inputCantidad.value = '';
                                    inputCantidad.setAttribute('readonly', 'readonly');
                                    inputCantidad.setAttribute('title', 'No hay stock disponible');
                                    
                                    // Mostrar advertencia
                                    if (inputDescripcion && inputDescripcion.value) {
                                        mostrarAlerta('warning', 'Stock agotado',
                                            `El producto "${inputDescripcion.value}" ya no tiene stock en la nueva ubicación seleccionada.`);
                                    }
                                } else {
                                    // Habilitar campo si hay stock
                                    inputCantidad.removeAttribute('readonly');
                                    inputCantidad.removeAttribute('title');
                                    inputCantidad.setAttribute('min', '0.01');
                                    inputCantidad.setAttribute('max', nuevoStock);
                                    
                                    // Si la cantidad actual es mayor que el nuevo stock, ajustarla
                                    if (parseFloat(inputCantidad.value) > nuevoStock) {
                                        inputCantidad.value = nuevoStock.toFixed(2);
                                    }
                                }
                            }
                        }
                    }
                })
                .catch(error => console.error('Error al actualizar stock:', error));
            }
        });
    }
});

// Limpiar la referencia cuando se cierre la modal sin seleccionar
$('#buscar_producto').on('hidden.bs.modal', function () {
    currentSearchButton = null;
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