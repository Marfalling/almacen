<?php 
//=======================================================================
// VISTA: v_pedidos_nuevo.php
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Nuevo Pedido</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Datos del Pedido <small></small></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br>
                        <form class="form-horizontal form-label-left" action="pedidos_nuevo.php" method="post" enctype="multipart/form-data">
                            
                            <!-- Información básica del pedido -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Tipo de Pedido <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="tipo_pedido" class="form-control" required>
                                       <option value="">Seleccionar</option>
                                        <?php foreach ($producto_tipos as $producto_tipo) { ?>
                                            <option value="<?php echo $producto_tipo['id_producto_tipo']; ?>">
                                                <?php echo $producto_tipo['nom_producto_tipo']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Almacén <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="id_obra" class="form-control" required>
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
                            
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Centro de Costos <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="id_centro_costo" class="form-control" required>
                                        <option value="">Seleccionar</option>
                                        <?php foreach ($centros_costo as $centro) { ?>
                                            <option value="<?php echo $centro['id_centro_costo']; ?>">
                                                <?php echo $centro['nom_centro_costo']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Nombre del Pedido <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="nom_pedido" class="form-control" placeholder="Nombre del Pedido">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Solicitante:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="solicitante" class="form-control" value="<?php echo $usuario_sesion; ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Fecha de Necesidad <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="date" name="fecha_necesidad" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Nº OT/LCL/LCA:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="num_ot" class="form-control" placeholder="Nº OT/LCL/LCA">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Número de contacto <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="contacto" class="form-control" placeholder="Número de contacto" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Lugar de Entrega <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="lugar_entrega" class="form-control" placeholder="Lugar de Entrega" required>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <!-- Sección de materiales/servicios -->
                            <div class="x_title">
                                <h4>Detalles del Pedido <small></small></h4>
                                <div class="clearfix"></div>
                            </div>

                            <div id="contenedor-materiales">
                                <div class="material-item border p-3 mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Material/Servicio <span class="text-danger">*</span>:</label>
                                            <div class="input-group">
                                                <input type="text" name="descripcion[]" class="form-control" placeholder="Material o Servicio" required>
                                                <button onclick="buscarMaterial(this)" class="btn btn-secondary btn-xs" type="button">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </div>
                                            <input type="hidden" name="id_material[]" value="">
                                        </div>

                                        <div class="col-md-3">
                                            <label>Unidad de Medida <span class="text-danger">*</span>:</label>
                                            <select name="unidad[]" class="form-control" required>
                                                <option value="">Seleccionar</option>
                                                <?php foreach ($unidades_medida as $unidad) { ?>
                                                    <option value="<?php echo $unidad['id_unidad_medida']; ?>">
                                                        <?php echo $unidad['nom_unidad_medida']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <label>Cantidad <span class="text-danger">*</span>:</label>
                                            <input type="number" name="cantidad[]" class="form-control" step="0.01" min="0" required>
                                        </div>
                                    </div>
                                    
                                    <!-- NUEVA FILA PARA OT -->
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <label>Nº OT/LCL/LCA <span class="text-danger">*</span>:</label>
                                            <input type="text" name="ot_detalle[]" class="form-control" placeholder="Número de OT/LCL/LCA" required>
                                            <small class="form-text text-muted">Cada material debe tener su número de orden asignado</small>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label>Observaciones:</label>
                                            <input name="observaciones[]" class="form-control" placeholder="Observaciones o comentarios">
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <label>Descripción SST/MA/CA <span class="text-danger">*</span>:</label>
                                            <input name="sst[]" class="form-control" placeholder="Requisitos de SST, MA y CA" required>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label>Adjuntar Archivos:</label>
                                            <input type="file" name="archivos_0[]" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                                            <small class="form-text text-muted">Formatos permitidos: PDF, JPG, PNG, DOC, XLS. Máximo 5MB por archivo.</small>
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
                            </div>

                            <div class="form-group">
                                <button type="button" id="agregar-material" class="btn btn-info btn-sm">
                                    <i class="fa fa-plus"></i> Agregar Material/Servicio
                                </button>
                            </div>

                            <div class="ln_solid"></div>

                            <!-- Aclaraciones -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Aclaraciones:</label>
                                <div class="col-md-9 col-sm-9">
                                    <textarea name="aclaraciones" class="form-control" rows="4" ></textarea>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 offset-md-8">
                                    <button type="reset" class="btn btn-outline-danger btn-block">Limpiar</button>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <button type="submit" name="registrar" id="btn_registrar" class="btn btn-success btn-block">Finalizar Pedido</button>
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
                <div class="text-right mb-3">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#crear_producto" data-dismiss="modal">
                        <i class="fa fa-plus"></i> Nuevo Producto
                    </button>
                </div>
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

<!-- Modal para crear producto (COMPLETA) -->
<div class="modal fade" id="crear_producto" tabindex="-1" role="dialog" aria-labelledby="crearProductoModalLabel">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document" style="max-height: 90vh;">
        <div class="modal-content" style="max-height: 90vh;">
            <div class="modal-header">
                <h4 class="modal-title" id="crearProductoModalLabel">Crear Nuevo Producto</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto; padding: 20px;">
                <form id="form-crear-producto" class="form-horizontal form-label-left" enctype="multipart/form-data">
                    
                    <!-- Información Básica -->
                    <div class="x_title">
                        <h5>Información Básica</h5>
                        <div class="clearfix"></div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Tipo de Producto <span class="text-danger">*</span>:</label>
                                <select name="id_producto_tipo" class="form-control" required>
                                    <option value="">Seleccionar tipo de producto</option>
                                    <?php foreach($producto_tipos as $tipo) { ?>
                                        <option value="<?php echo $tipo['id_producto_tipo']; ?>"><?php echo $tipo['nom_producto_tipo']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Tipo de Material <span class="text-danger">*</span>:</label>
                                <select name="id_material_tipo" class="form-control" required>
                                    <option value="">Seleccionar tipo de material</option>
                                    <?php foreach($material_tipos as $material) { ?>
                                        <option value="<?php echo $material['id_material_tipo']; ?>"><?php echo $material['nom_material_tipo']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Unidad de Medida <span class="text-danger">*</span>:</label>
                                <select name="id_unidad_medida" class="form-control" required>
                                    <option value="">Seleccionar unidad de medida</option>
                                    <?php foreach($unidades_medida as $unidad) { ?>
                                        <option value="<?php echo $unidad['id_unidad_medida']; ?>"><?php echo $unidad['nom_unidad_medida']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Código de Material:</label>
                                <input type="text" name="cod_material" class="form-control" placeholder="Código del material">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Nombre del Producto <span class="text-danger">*</span>:</label>
                                <input type="text" name="nom_producto" class="form-control" placeholder="Nombre del producto" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Número de Serie:</label>
                                <input type="text" name="nser_producto" class="form-control" placeholder="Número de serie">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Modelo:</label>
                                <input type="text" name="mod_producto" class="form-control" placeholder="Modelo del producto">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Marca:</label>
                                <input type="text" name="mar_producto" class="form-control" placeholder="Marca del producto">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Detalle:</label>
                                <textarea name="det_producto" class="form-control" rows="2" placeholder="Detalle del producto"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Documento de Homologación -->
                    <div class="x_title">
                        <h5>Documento de Homologación</h5>
                        <div class="clearfix"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Documento de Homologación:</label>
                                <input type="file" name="hom_archivo" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                <small class="text-muted">Formatos permitidos: PDF, JPG, JPEG, PNG, DOC, DOCX. Tamaño máximo: 10MB</small>
                            </div>
                        </div>
                    </div>

                    <!-- Información de Calibrado -->
                    <div class="x_title">
                        <h5>Información de Calibrado <small>(para materiales)</small></h5>
                        <div class="clearfix"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Fecha Último Calibrado:</label>
                                <input type="date" name="fuc_producto" class="form-control">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Fecha Próximo Calibrado:</label>
                                <input type="date" name="fpc_producto" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Documento de Calibrado:</label>
                                <input type="file" name="dcal_archivo" class="form-control" accept=".pdf,.jpg,.jpeg">
                                <small class="text-muted">Formatos permitidos: PDF, JPG, JPEG. Tamaño máximo: 10MB</small>
                            </div>
                        </div>
                    </div>

                    <!-- Información de Operatividad -->
                    <div class="x_title">
                        <h5>Información de Operatividad <small>(para materiales)</small></h5>
                        <div class="clearfix"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Fecha Última Operatividad:</label>
                                <input type="date" name="fuo_producto" class="form-control">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Fecha Próxima Operatividad:</label>
                                <input type="date" name="fpo_producto" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Documento de Operatividad:</label>
                                <input type="file" name="dope_archivo" class="form-control" accept=".pdf,.jpg,.jpeg">
                                <small class="text-muted">Formatos permitidos: PDF, JPG, JPEG. Tamaño máximo: 10MB</small>
                            </div>
                        </div>
                    </div>

                    <!-- Estado -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Estado:</label>
                                <div class="">
                                    <label>
                                        <input type="checkbox" name="est" class="js-switch" checked> Activo
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <p><span class="text-danger">*</span> Los campos con (<span class="text-danger">*</span>) son obligatorios.</p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btn-guardar-producto">Guardar Producto</button>
            </div>
        </div>
    </div>
</div>

<script>
// Variable global para rastrear qué botón de búsqueda se clickeó
let currentSearchButton = null;

function buscarMaterial(button) {
    // Obtener el valor del select tipo de pedido
    const selectTipoPedido = document.querySelector('select[name="tipo_pedido"]');
    const tipoPedidoValue = selectTipoPedido ? selectTipoPedido.value : '';
    
    // Validar que se haya seleccionado un tipo de pedido
    if (!tipoPedidoValue) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Tipo de pedido requerido',
                text: 'Debe seleccionar un tipo de pedido antes de buscar productos.',
                confirmButtonText: 'Entendido'
            });
        } else {
            alert('Debe seleccionar un tipo de pedido antes de buscar productos.');
        }
        return; // Salir de la función sin abrir la modal
    }
    
    // Guardar referencia al botón que se clickeó
    currentSearchButton = button;
    
    // Abrir la modal
    $('#buscar_producto').modal('show');
    
    // Cargar los productos en la tabla con filtro de tipo
    cargarProductos(tipoPedidoValue);
}

function cargarProductos(tipoPedido = '') {
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
            "url": "producto_mostrar_modal.php",
            "type": "POST",
            "data": function(d) {
                // Agregar el filtro de tipo de pedido a los datos enviados
                d.tipo_pedido = tipoPedido;
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
            { "title": "Acción" }
        ],
        "order": [[1, 'asc']],
        "pageLength": 10,
        "lengthMenu": [10, 25, 50, 100],
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron resultados para este tipo de pedido",
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
            "emptyTable": "No hay productos disponibles para este tipo de pedido",
            "aria": {
                "sortAscending": ": activar para ordenar la columna de manera ascendente",
                "sortDescending": ": activar para ordenar la columna de manera descendente"
            }
        }
    });
}

function seleccionarProducto(idProducto, nombreProducto, idUnidad, nombreUnidad) {
    if (currentSearchButton) {
        // Encontrar el contenedor padre del botón que se clickeó
        let materialItem = currentSearchButton.closest('.material-item');
        
        if (materialItem) {
            // Actualizar el input de descripción
            let inputDescripcion = materialItem.querySelector('input[name="descripcion[]"]');
            if (inputDescripcion) {
                inputDescripcion.value = nombreProducto;
            }
            
            // Actualizar el input hidden del ID del material
            let inputIdMaterial = materialItem.querySelector('input[name="id_material[]"]');
            if (inputIdMaterial) {
                inputIdMaterial.value = idProducto;
            }
            
            // Actualizar el select de unidad de medida
            let selectUnidad = materialItem.querySelector('select[name="unidad[]"]');
            if (selectUnidad) {
                selectUnidad.value = idUnidad;
            }
        }
    }
    
    // Cerrar la modal
    $('#buscar_producto').modal('hide');
    
    // Limpiar la referencia
    currentSearchButton = null;
    
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

// Limpiar la referencia cuando se cierre la modal sin seleccionar
$('#buscar_producto').on('hidden.bs.modal', function () {
    currentSearchButton = null;
});

// Agregar validación adicional cuando se cambie el tipo de pedido
document.addEventListener('DOMContentLoaded', function() {
    const selectTipoPedido = document.querySelector('select[name="tipo_pedido"]');
    if (selectTipoPedido) {
        selectTipoPedido.addEventListener('change', function() {
            // Si hay productos ya agregados, mostrar advertencia
            const materialesItems = document.querySelectorAll('.material-item');
            let hayProductosSeleccionados = false;
            
            materialesItems.forEach(item => {
                const inputDescripcion = item.querySelector('input[name="descripcion[]"]');
                if (inputDescripcion && inputDescripcion.value.trim() !== '') {
                    hayProductosSeleccionados = true;
                }
            });
            
            if (hayProductosSeleccionados && this.value) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Tipo de pedido cambiado',
                        text: 'Al cambiar el tipo de pedido, los productos mostrados en la búsqueda se filtrarán según la nueva selección.',
                        confirmButtonText: 'Entendido'
                    });
                }
            }
        });
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let contadorMateriales = 1;
    let selectTipoProducto = document.querySelector('select[name="tipo_pedido"]'); 
    let valorInicialTipoPedido = '';
    let formularioModificado = false;
    
    // Función para marcar formulario como modificado
    function marcarFormularioComoModificado() {
        formularioModificado = true;
    }
    
    // Función para agregar eventos a campos específicos (excluye botones y selects específicos)
    function agregarEventosACampos() {
        const campos = document.querySelectorAll('input:not([type="button"]):not([type="submit"]):not([type="reset"]), textarea, select');
        
        campos.forEach(campo => {
            // Solo agregar eventos a campos que NO sean el tipo de pedido
            if (campo.name !== 'tipo_pedido') {
                // Remover eventos anteriores para evitar duplicados
                campo.removeEventListener('change', marcarFormularioComoModificado);
                campo.removeEventListener('input', marcarFormularioComoModificado);
                
                // Agregar eventos nuevamente
                campo.addEventListener('change', marcarFormularioComoModificado);
                campo.addEventListener('input', marcarFormularioComoModificado);
            }
        });
    }
    
    // Función para limpiar todo el formulario
    function limpiarFormularioCompleto() {
        // Limpiar campos básicos
        const camposALimpiar = [
            'select[name="id_obra"]',
            'select[name="id_ubicacion"]', 
            'input[name="nom_pedido"]',
            'input[name="fecha_necesidad"]',
            'input[name="num_ot"]',
            'input[name="contacto"]',
            'input[name="lugar_entrega"]',
            'textarea[name="aclaraciones"]'
        ];
        
        camposALimpiar.forEach(selector => {
            const elemento = document.querySelector(selector);
            if (elemento) {
                elemento.value = '';
            }
        });
        
        // Limpiar sección de materiales - mantener solo el primer item y limpiarlo
        const contenedorMateriales = document.getElementById('contenedor-materiales');
        const materialesItems = contenedorMateriales.querySelectorAll('.material-item');
        
        // Eliminar todos los items adicionales, mantener solo el primero
        for (let i = 1; i < materialesItems.length; i++) {
            materialesItems[i].remove();
        }
        
        // Limpiar el primer item de material
        const primerMaterial = contenedorMateriales.querySelector('.material-item');
        if (primerMaterial) {
            const inputs = primerMaterial.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                if (input.name !== 'tipo_pedido') {
                    input.value = '';
                }
            });
            
            // Ocultar el botón eliminar del primer item
            const btnEliminar = primerMaterial.querySelector('.eliminar-material');
            if (btnEliminar) {
                btnEliminar.style.display = 'none';
            }
        }
        
        // Reiniciar contadores y estado
        contadorMateriales = 1;
        formularioModificado = false;
        
        // Actualizar eventos
        actualizarEventosEliminar();
        agregarEventosACampos();
    }
    
    // Inicializar eventos en campos existentes
    agregarEventosACampos();
    
    // Evento para el select tipo de pedido
    if (selectTipoProducto) {
        selectTipoProducto.addEventListener('focus', function() {
            valorInicialTipoPedido = this.value;
        });
        
        selectTipoProducto.addEventListener('change', function() {
            const valorActual = this.value;
            
            // Si el formulario ha sido modificado y se está cambiando el tipo de pedido
            if (formularioModificado && valorInicialTipoPedido !== valorActual) {
                // Verificar si SweetAlert está disponible
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: 'Si cambias el tipo de pedido, todos los cambios realizados en el formulario se perderán.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, continuar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            limpiarFormularioCompleto();
                            Swal.fire({
                                title: 'Formulario limpiado',
                                text: 'Todos los cambios han sido eliminados.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            this.value = valorInicialTipoPedido;
                        }
                    });
                } else {
                    if (confirm('Si cambias el tipo de pedido, todos los cambios realizados en el formulario se perderán. ¿Continuar?')) {
                        limpiarFormularioCompleto();
                        alert('Formulario limpiado');
                    } else {
                        this.value = valorInicialTipoPedido;
                    }
                }
            } else {
                valorInicialTipoPedido = valorActual;
            }
        });
    }
    
    // FUNCIONALIDAD PRINCIPAL: Agregar nuevo material
    const btnAgregarMaterial = document.getElementById('agregar-material');
    if (btnAgregarMaterial) {
        btnAgregarMaterial.addEventListener('click', function(e) {
            e.preventDefault(); // Prevenir comportamiento por defecto
            
            const contenedor = document.getElementById('contenedor-materiales');
            const materialOriginal = contenedor.querySelector('.material-item');
            
            if (materialOriginal) {
                // Clonar el elemento original
                const nuevoMaterial = materialOriginal.cloneNode(true);
                
                // Limpiar todos los valores del nuevo elemento
                const inputs = nuevoMaterial.querySelectorAll('input, textarea, select');
                inputs.forEach(input => {
                    if (input.type === 'file') {
                        // Para inputs de archivo, limpiar el valor y actualizar el name
                        input.value = '';
                        input.name = `archivos_${contadorMateriales}[]`;
                    } else if (input.type === 'hidden') {
                        input.value = '';
                    } else if (input.tagName.toLowerCase() === 'select') {
                        input.selectedIndex = 0; // Seleccionar la primera opción (vacía)
                    } else if (input.name === 'ot_detalle[]') {
                        // Limpiar campo OT también
                        input.value = '';
                    } else {
                        input.value = '';
                    }
                });
                
                // Mostrar el botón eliminar en el nuevo elemento
                const btnEliminar = nuevoMaterial.querySelector('.eliminar-material');
                if (btnEliminar) {
                    btnEliminar.style.display = 'block';
                }
                
                // Agregar el nuevo elemento al contenedor
                contenedor.appendChild(nuevoMaterial);
                contadorMateriales++;
                
                // Actualizar eventos
                actualizarEventosEliminar();
                agregarEventosACampos();
                
                // Marcar como modificado
                formularioModificado = true;
                
                // Scroll suave hacia el nuevo elemento agregado
                nuevoMaterial.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'nearest' 
                });
            }
        });
    }
    
    // Función para actualizar eventos de eliminar
    function actualizarEventosEliminar() {
        // Remover todos los event listeners anteriores y agregar nuevos
        const botonesEliminar = document.querySelectorAll('.eliminar-material');
        
        botonesEliminar.forEach(btn => {
            // Crear una nueva función para cada botón para evitar conflicts
            btn.onclick = function(e) {
                e.preventDefault();
                const materialesItems = document.querySelectorAll('.material-item');
                
                if (materialesItems.length > 1) {
                    // Confirmar eliminación
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '¿Eliminar material?',
                            text: 'Se eliminará este material del pedido',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.closest('.material-item').remove();
                                formularioModificado = true;
                            }
                        });
                    } else {
                        if (confirm('¿Eliminar este material?')) {
                            this.closest('.material-item').remove();
                            formularioModificado = true;
                        }
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'info',
                            title: 'No se puede eliminar',
                            text: 'Debe mantener al menos un material en el pedido'
                        });
                    } else {
                        alert('Debe mantener al menos un material en el pedido');
                    }
                }
            };
        });
    }
    
    // Inicializar eventos de eliminar
    actualizarEventosEliminar();
    
    // Interceptar el botón reset del formulario
    const btnLimpiar = document.querySelector('button[type="reset"]');
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '¿Limpiar formulario?',
                    text: 'Se eliminarán todos los datos ingresados.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, limpiar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.querySelector('form').reset();
                        limpiarFormularioCompleto();
                        if (selectTipoProducto) {
                            selectTipoProducto.value = '';
                            valorInicialTipoPedido = '';
                        }
                        
                        Swal.fire({
                            title: 'Formulario limpiado',
                            text: 'Todos los datos han sido eliminados.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            } else {
                if (confirm('¿Limpiar formulario? Se eliminarán todos los datos ingresados.')) {
                    document.querySelector('form').reset();
                    limpiarFormularioCompleto();
                    if (selectTipoProducto) {
                        selectTipoProducto.value = '';
                        valorInicialTipoPedido = '';
                    }
                    alert('Formulario limpiado');
                }
            }
        });
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action="pedidos_nuevo.php"], form[action="pedidos_editar.php"]');
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
<script>
// Función para seleccionar automáticamente el producto recién creado
function seleccionarProductoCreado(producto) {
    if (currentSearchButton) {
        // Encontrar el contenedor padre del botón que se clickeó
        let materialItem = currentSearchButton.closest('.material-item');
        
        if (materialItem) {
            // Actualizar el input de descripción
            let inputDescripcion = materialItem.querySelector('input[name="descripcion[]"]');
            if (inputDescripcion) {
                inputDescripcion.value = producto.nom_producto;
            }
            
            // Actualizar el input hidden del ID del material
            let inputIdMaterial = materialItem.querySelector('input[name="id_material[]"]');
            if (inputIdMaterial) {
                inputIdMaterial.value = producto.id_producto;
            }
            
            // Actualizar el select de unidad de medida
            let selectUnidad = materialItem.querySelector('select[name="unidad[]"]');
            if (selectUnidad) {
                selectUnidad.value = producto.id_unidad_medida;
            }
        }
    }
    
    // Limpiar la referencia
    currentSearchButton = null;
}

// Versión mejorada del manejo de creación de productos
document.addEventListener('DOMContentLoaded', function() {
    const btnGuardarProducto = document.getElementById('btn-guardar-producto');
    if (btnGuardarProducto) {
        btnGuardarProducto.addEventListener('click', function() {
            const form = document.getElementById('form-crear-producto');
            const formData = new FormData(form);
            
            // Validaciones básicas
            const tipoProducto = formData.get('id_producto_tipo');
            const tipoMaterial = formData.get('id_material_tipo');
            const unidadMedida = formData.get('id_unidad_medida');
            const nombreProducto = formData.get('nom_producto');
            
            if (!tipoProducto || !tipoMaterial || !unidadMedida || !nombreProducto) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Campos requeridos',
                        text: 'Por favor complete todos los campos obligatorios (marcados con *)'
                    });
                } else {
                    alert('Por favor complete todos los campos obligatorios (marcados con *)');
                }
                return;
            }
            
            // Validar archivos si se han seleccionado
            const archivoHomologacion = document.querySelector('input[name="hom_archivo"]')?.files[0];
            const archivoCalibrado = document.querySelector('input[name="dcal_archivo"]')?.files[0];
            const archivoOperatividad = document.querySelector('input[name="dope_archivo"]')?.files[0];

            if (archivoHomologacion && archivoHomologacion.size > 10 * 1024 * 1024) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Archivo muy grande',
                        text: 'El archivo de homologación no debe superar los 10MB'
                    });
                } else {
                    alert('El archivo de homologación no debe superar los 10MB');
                }
                return;
            }
            
            if (archivoCalibrado && archivoCalibrado.size > 10 * 1024 * 1024) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Archivo muy grande',
                        text: 'El archivo de calibrado no debe superar los 10MB'
                    });
                } else {
                    alert('El archivo de calibrado no debe superar los 10MB');
                }
                return;
            }
            
            if (archivoOperatividad && archivoOperatividad.size > 10 * 1024 * 1024) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Archivo muy grande',
                        text: 'El archivo de operatividad no debe superar los 10MB'
                    });
                } else {
                    alert('El archivo de operatividad no debe superar los 10MB');
                }
                return;
            }
            
            // Deshabilitar botón mientras se procesa
            btnGuardarProducto.disabled = true;
            const textoOriginal = btnGuardarProducto.textContent;
            btnGuardarProducto.textContent = 'Guardando...';
            
            // Enviar datos via AJAX con FormData para manejar archivos
            fetch('producto_crear_ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Producto creado exitosamente
                    
                    // PRIMERO: Seleccionar automáticamente el producto recién creado
                    if (currentSearchButton && data.producto) {
                        seleccionarProductoCreado(data.producto);
                    }
                    
                    // SEGUNDO: Cerrar modal ANTES de mostrar SweetAlert
                    $('#crear_producto').modal('hide');
                    
                    // TERCERO: Limpiar formulario
                    form.reset();
                    resetearSwitches(form);
                    
                    // CUARTO: Recargar tabla de productos si está abierta
                    if ($.fn.dataTable.isDataTable('#datatable_producto')) {
                        $('#datatable_producto').DataTable().ajax.reload();
                    }
                    
                    // QUINTO: Mostrar mensaje de éxito DESPUÉS de cerrar modal
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Producto creado',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 2000
                        });
                    } else {
                        alert(data.message);
                    }
                    
                } else {
                    // Error al crear producto
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
                        });
                    } else {
                        alert('Error: ' + data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor'
                    });
                } else {
                    alert('Error de conexión: ' + error.message);
                }
            })
            .finally(() => {
                // Rehabilitar botón
                btnGuardarProducto.disabled = false;
                btnGuardarProducto.textContent = textoOriginal;
            });
        });
    }
    
    // Función auxiliar para resetear switches
    function resetearSwitches(form) {
        const switchElements = form.querySelectorAll('.js-switch');
        switchElements.forEach(sw => {
            if (!sw.checked) {
                sw.checked = true;
                if (typeof $.fn.switchery !== 'undefined') {
                    $(sw).trigger('change');
                }
            }
        });
    }
    
    // Limpiar formulario cuando se cierre la modal
    $('#crear_producto').on('hidden.bs.modal', function () {
        const form = document.getElementById('form-crear-producto');
        if (form) {
            form.reset();
            resetearSwitches(form);
        }
    });
    
    // Forzar el cierre de modal si hay problemas
    $('#crear_producto').on('show.bs.modal', function () {
        // Asegurar que cualquier modal anterior esté cerrada
        $('.modal').not(this).modal('hide');
    });
});
</script>