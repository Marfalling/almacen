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
                                    <select name="id_centro_costo" id="id_centro_costo" class="form-control" required>
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
                                <label class="control-label col-md-3 col-sm-3">Nombre del Pedido:</label>
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
                                                <button onclick="buscarMaterial(this)" class="btn btn-secondary btn-xs" data-toggle="tooltip" title="Buscar Material"  type="button">
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

                                    <!-- NUEVA FILA PARA CENTROS DE COSTO MÚLTIPLES -->
                                    <div class="row mt-2">
                                        <div class="col-md-12" id="col-centros-0">
                                            <label>Centros de Costo para este Material <span class="text-danger">*</span>:</label>
                                            <select name="centros_costo[0][]" class="form-control select2-centros-costo-detalle" multiple required>
                                                <?php foreach ($centros_costo as $centro) { ?>
                                                    <option value="<?php echo $centro['id_centro_costo']; ?>">
                                                        <?php echo $centro['nom_centro_costo']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="col-md-6 campo-personal" style="display: none;">
                                            <label>Personal Asignado</label>
                                            <select name="personal_ids[0][]" class="form-control select2-personal-detalle" multiple>
                                                <?php foreach ($personal_list as $persona) { ?>
                                                    <option value="<?php echo $persona['id_personal']; ?>">
                                                        <?php echo $persona['nom_personal']; ?>
                                                        <?php if (!empty($persona['nom_cargo'])) echo ' - ' . $persona['nom_cargo']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <small class="form-text text-muted">
                                                <i class="fa fa-info-circle"></i> Seleccione el personal que trabajará en este servicio.
                                            </small>
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
                                    <a href="pedidos_mostrar.php" class="btn btn-outline-danger btn-block">Cancelar</a>
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
// ============================================
// VARIABLES GLOBALES
// ============================================
let currentSearchButton = null;
let contadorMateriales = 1;
let valorInicialTipoPedido = '';
let formularioModificado = false;
let aplicarCentroCostoAutomaticamente = false;

// ============================================
// FUNCIONES GLOBALES - BÚSQUEDA DE PRODUCTOS
// ============================================
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

    $('#datatable_producto').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "ajax": {
            "url": "producto_mostrar_modal.php",
            "type": "POST",
            "data": function(d) {
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
    // ===== VALIDACIÓN ANTI-DUPLICADOS =====
    const materialItems = document.querySelectorAll('.material-item');
    let productoExistente = null;

    materialItems.forEach(item => {
        const inputId = item.querySelector('input[name="id_material[]"]');
        if (inputId && parseInt(inputId.value) === parseInt(idProducto)) {
            productoExistente = item;
        }
    });

    if (productoExistente) {
        // Producto ya existe → resaltarlo visualmente
        productoExistente.classList.add('duplicado-resaltado');
        productoExistente.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Quitar resaltado después de 2 segundos
        setTimeout(() => productoExistente.classList.remove('duplicado-resaltado'), 2000);

        // Cerrar modal (sin alert)
        $('#buscar_producto').modal('hide');
        return; // Detiene aquí, no lo agrega de nuevo
    }
    // ===== FIN VALIDACIÓN =====
    if (currentSearchButton) {
        let materialItem = currentSearchButton.closest('.material-item');
        
        if (materialItem) {
            let inputDescripcion = materialItem.querySelector('input[name="descripcion[]"]');
            if (inputDescripcion) {
                inputDescripcion.value = nombreProducto;
            }
            
            let inputIdMaterial = materialItem.querySelector('input[name="id_material[]"]');
            if (inputIdMaterial) {
                inputIdMaterial.value = idProducto;
            }
            
            let selectUnidad = materialItem.querySelector('select[name="unidad[]"]');
            if (selectUnidad) {
                if ($(selectUnidad).data('select2')) {
                    $(selectUnidad).val(idUnidad).trigger('change');
                } else {
                    selectUnidad.value = idUnidad;
                }
            }
        }
    }
    
    $('#buscar_producto').modal('hide');
    currentSearchButton = null;
    
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

function seleccionarProductoCreado(producto) {
     // ===== VALIDACIÓN ANTI-DUPLICADOS =====
    const materialItems = document.querySelectorAll('.material-item');
    let productoExistente = null;

    materialItems.forEach(item => {
        const inputId = item.querySelector('input[name="id_material[]"]');
        if (inputId && parseInt(inputId.value) === parseInt(producto.id_producto)) {
            productoExistente = item;
        }
    });

    if (productoExistente) {
        // Producto ya existe → resaltarlo visualmente
        productoExistente.classList.add('duplicado-resaltado');
        productoExistente.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Quitar resaltado después de 2 segundos
        setTimeout(() => productoExistente.classList.remove('duplicado-resaltado'), 2000);

        return; // Detiene aquí, no lo agrega de nuevo
    }
    // ===== FIN VALIDACIÓN =====

    if (currentSearchButton) {
        let materialItem = currentSearchButton.closest('.material-item');
        
        if (materialItem) {
            let inputDescripcion = materialItem.querySelector('input[name="descripcion[]"]');
            if (inputDescripcion) {
                inputDescripcion.value = producto.nom_producto;
            }
            
            let inputIdMaterial = materialItem.querySelector('input[name="id_material[]"]');
            if (inputIdMaterial) {
                inputIdMaterial.value = producto.id_producto;
            }
            
            let selectUnidad = materialItem.querySelector('select[name="unidad[]"]');
            if (selectUnidad) {
                if ($(selectUnidad).data('select2')) {
                    $(selectUnidad).val(producto.id_unidad_medida).trigger('change');
                } else {
                    selectUnidad.value = producto.id_unidad_medida;
                }
            }
        }
    }
    
    currentSearchButton = null;
}

// ============================================
// DOCUMENT READY - TODO CONSOLIDADO
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================
    // FUNCIÓN: Mostrar/Ocultar Personal según Tipo de Pedido
    // ============================================
    const selectTipoPedido = document.querySelector('select[name="tipo_pedido"]');
    
    function toggleCamposPersonal(mostrar) {
        const camposPersonal = document.querySelectorAll('.campo-personal');
        camposPersonal.forEach(campo => {
            if (mostrar) {
                campo.style.display = 'block';
                const rowParent = campo.closest('.row');
                if (rowParent) {
                    const colCentros = rowParent.querySelector('[id^="col-centros-"]');
                    if (colCentros) {
                        colCentros.classList.remove('col-md-12');
                        colCentros.classList.add('col-md-6');
                    }
                }
            } else {
                campo.style.display = 'none';
                const rowParent = campo.closest('.row');
                if (rowParent) {
                    const colCentros = rowParent.querySelector('[id^="col-centros-"]');
                    if (colCentros) {
                        colCentros.classList.remove('col-md-6');
                        colCentros.classList.add('col-md-12');
                    }
                }
            }
        });
    }
    
    function esTipoServicio(idTipo) {
        if (!idTipo) return false;
        const option = selectTipoPedido.querySelector(`option[value="${idTipo}"]`);
        if (option) {
            const texto = option.textContent.toUpperCase();
            return texto.includes('SERVICIO');
        }
        return false;
    }
    
    if (selectTipoPedido) {
        $(selectTipoPedido).on('change', function() {
            const valorSeleccionado = this.value;
            const esServicio = esTipoServicio(valorSeleccionado);
            
            toggleCamposPersonal(esServicio);
            
            const selectsPersonal = document.querySelectorAll('select.select2-personal-detalle');
            selectsPersonal.forEach(select => {
                if (esServicio) {
                    if (!$(select).data('select2')) {
                        $(select).select2({
                            placeholder: 'Seleccionar personal...',
                            allowClear: true,
                            width: '100%',
                            multiple: true,
                            language: {
                                noResults: function () { return 'No se encontraron resultados'; }
                            }
                        });
                    }
                } else {
                    if ($(select).data('select2')) {
                        $(select).val(null).trigger('change');
                    }
                }
            });
            
            // Validar productos ya agregados
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
        
        // Verificar estado inicial
        if (selectTipoPedido.value) {
            const esServicio = esTipoServicio(selectTipoPedido.value);
            toggleCamposPersonal(esServicio);
        }
    }
    
    // ============================================
    // FUNCIÓN: Centro de Costo de Cabecera
    // ============================================
    const selectCentroCostoCabecera = document.querySelector('select[name="id_centro_costo"]');
    
    function aplicarCentroCostoATodosMateriales(centroCostoId) {
        const selectsCentrosCosto = document.querySelectorAll('select.select2-centros-costo-detalle');
        
        selectsCentrosCosto.forEach(select => {
            if ($(select).data('select2')) {
                let valoresActuales = $(select).val() || [];
                
                if (!valoresActuales.includes(centroCostoId)) {
                    valoresActuales = [centroCostoId];
                    $(select).val(valoresActuales).trigger('change');
                }
            }
        });
    }
    
    if (selectCentroCostoCabecera) {
        $(selectCentroCostoCabecera).on('select2:select', function(e) {
            const centroCostoSeleccionado = $(this).val();
            const nombreCentroCosto = $(this).find('option:selected').text();
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'question',
                    title: '¿Aplicar a todos los materiales?',
                    html: `¿Desea aplicar el centro de costo <strong>"${nombreCentroCosto}"</strong> a todos los materiales del pedido?<br><br><small>Los nuevos materiales también usarán este centro de costo automáticamente.</small>`,
                    showCancelButton: true,
                    confirmButtonText: 'Sí, aplicar',
                    cancelButtonText: 'No, solo cabecera',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        aplicarCentroCostoAutomaticamente = true;
                        aplicarCentroCostoATodosMateriales(centroCostoSeleccionado);
                        
                        // Mensaje de confirmación
                        /*Swal.fire({
                            icon: 'success',
                            title: 'Aplicado',
                            text: 'El centro de costo se aplicará a todos los materiales actuales y futuros',
                            timer: 2000,
                            showConfirmButton: false
                        });*/
                    } else {
                        aplicarCentroCostoAutomaticamente = false;
                    }
                });
            } else {
                const confirmar = confirm(`¿Desea aplicar el centro de costo "${nombreCentroCosto}" a todos los materiales del pedido?\n\nLos nuevos materiales también usarán este centro de costo automáticamente.`);
                if (confirmar) {
                    aplicarCentroCostoAutomaticamente = true;
                    aplicarCentroCostoATodosMateriales(centroCostoSeleccionado);
                    /*alert('El centro de costo se aplicará a todos los materiales actuales y futuros');*/
                } else {
                    aplicarCentroCostoAutomaticamente = false;
                }
            }
        });
    }
    
    // ============================================
    // FUNCIÓN: Observador para materiales nuevos
    // ============================================
    const btnAgregarMaterialOriginal = document.getElementById('agregar-material');
    if (btnAgregarMaterialOriginal) {
        // Observar cuando se agregue un nuevo material
        const observador = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length > 0) {
                    mutation.addedNodes.forEach(function(nodo) {
                        // Si el nodo agregado es un material-item
                        if (nodo.classList && nodo.classList.contains('material-item')) {
                            // Solo aplicar si el usuario eligió hacerlo automáticamente
                            if (aplicarCentroCostoAutomaticamente) {
                                const centroCostoCabecera = document.querySelector('select[name="id_centro_costo"]');
                                if (centroCostoCabecera && centroCostoCabecera.value) {
                                    const selectCentros = nodo.querySelector('select.select2-centros-costo-detalle');
                                    if (selectCentros && $(selectCentros).data('select2')) {
                                        // Aplicar el centro de costo automáticamente
                                        setTimeout(() => {
                                            $(selectCentros).val([centroCostoCabecera.value]).trigger('change');
                                        }, 100);
                                    }
                                }
                            }
                        }
                    });
                }
            });
        });
        
        // Configurar el observador en el contenedor de materiales
        const contenedorMateriales = document.getElementById('contenedor-materiales');
        if (contenedorMateriales) {
            observador.observe(contenedorMateriales, { childList: true });
        }
    }
    
    // ============================================
    // FUNCIÓN: Marcar formulario modificado
    // ============================================
    function marcarFormularioComoModificado() {
        formularioModificado = true;
    }
    
    function agregarEventosACampos() {
        const campos = document.querySelectorAll('input:not([type="button"]):not([type="submit"]):not([type="reset"]), textarea, select');
        
        campos.forEach(campo => {
            if (campo.name !== 'tipo_pedido') {
                campo.removeEventListener('change', marcarFormularioComoModificado);
                campo.removeEventListener('input', marcarFormularioComoModificado);
                
                campo.addEventListener('change', marcarFormularioComoModificado);
                campo.addEventListener('input', marcarFormularioComoModificado);
            }
        });
    }
    
    // Función para limpiar todo el formulario
    function limpiarFormularioCompleto() {
        const camposALimpiar = [
            'select[name="id_obra"]',
            'select[name="id_ubicacion"]',
            'select[name="id_centro_costo"]',
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
                if ($(elemento).data('select2')) {
                    $(elemento).val(null).trigger('change');
                } else {
                    elemento.value = '';
                }
            }
        });
        
        // Limpiar sección de materiales
        const contenedorMateriales = document.getElementById('contenedor-materiales');
        const materialesItems = contenedorMateriales.querySelectorAll('.material-item');
        
        // Eliminar todos los items adicionales
        for (let i = 1; i < materialesItems.length; i++) {
            materialesItems[i].remove();
        }
        
        // Limpiar el primer item
        const primerMaterial = contenedorMateriales.querySelector('.material-item');
        if (primerMaterial) {
            const inputs = primerMaterial.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                if (input.name !== 'tipo_pedido') {
                    input.value = '';
                }
            });
            
            // Limpiar selects con Select2
            const selects = primerMaterial.querySelectorAll('select');
            selects.forEach(select => {
                if ($(select).data('select2')) {
                    $(select).val(null).trigger('change');
                } else {
                    select.selectedIndex = 0;
                }
            });
            
            const btnEliminar = primerMaterial.querySelector('.eliminar-material');
            if (btnEliminar) {
                btnEliminar.style.display = 'none';
            }
        }
        
        contadorMateriales = 1;
        formularioModificado = false;
        
        actualizarEventosEliminar();
        agregarEventosACampos();
    }
    
    // Inicializar eventos en campos existentes
    agregarEventosACampos();
    
    // ============================================
    // FUNCIÓN: Cambio de tipo de pedido con validación
    // ============================================
    if (selectTipoPedido) {
        $(selectTipoPedido).on('select2:opening', function() {
            valorInicialTipoPedido = $(this).val();
        });
        
        $(selectTipoPedido).on('select2:select', function(e) {
            const valorActual = $(this).val();
            
            if (formularioModificado && valorInicialTipoPedido !== valorActual) {
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
                            $(selectTipoPedido).val(valorInicialTipoPedido).trigger('change');
                        }
                    });
                } else {
                    if (confirm('Si cambias el tipo de pedido, todos los cambios realizados en el formulario se perderán. ¿Continuar?')) {
                        limpiarFormularioCompleto();
                        alert('Formulario limpiado');
                    } else {
                        $(selectTipoPedido).val(valorInicialTipoPedido).trigger('change');
                    }
                }
            } else {
                valorInicialTipoPedido = valorActual;
            }
        });
    }
    
    // ============================================
    // FUNCIÓN: Agregar material
    // ============================================
    const btnAgregarMaterial = document.getElementById('agregar-material');
    if (btnAgregarMaterial) {
        btnAgregarMaterial.addEventListener('click', function(e) {
            e.preventDefault();
            
            const contenedor = document.getElementById('contenedor-materiales');
            const materialOriginal = contenedor.querySelector('.material-item');
            
            if (materialOriginal) {
                const selectsOriginales = materialOriginal.querySelectorAll('select[name="unidad[]"], select.select2-centros-costo-detalle, select.select2-personal-detalle');
                selectsOriginales.forEach(select => {
                    if ($(select).data('select2')) {
                        $(select).select2('destroy');
                    }
                });
                
                const nuevoMaterial = materialOriginal.cloneNode(true);
                
                selectsOriginales.forEach(select => {
                    if (select.name === 'unidad[]') {
                        $(select).select2({
                            placeholder: 'Seleccionar unidad de medida...',
                            allowClear: true,
                            width: '100%',
                            language: {
                                noResults: function () { return 'No se encontraron resultados'; }
                            }
                        });
                    } else if ($(select).hasClass('select2-centros-costo-detalle')) {
                        $(select).select2({
                            placeholder: 'Seleccionar uno o más centros de costo...',
                            allowClear: true,
                            width: '100%',
                            multiple: true,
                            language: {
                                noResults: function () { return 'No se encontraron resultados'; }
                            }
                        });
                    } else if ($(select).hasClass('select2-personal-detalle')) { 
                        $(select).select2({
                            placeholder: 'Seleccionar personal...',
                            allowClear: true,
                            width: '100%',
                            multiple: true,
                            language: {
                                noResults: function () { return 'No se encontraron resultados'; }
                            }
                        });
                    }
                });
                
                const inputs = nuevoMaterial.querySelectorAll('input, textarea');
                inputs.forEach(input => {
                    if (input.type === 'file') {
                        input.value = '';
                        input.name = `archivos_${contadorMateriales}[]`;
                    } else if (input.type === 'hidden') {
                        input.value = '';
                    } else {
                        input.value = '';
                    }
                });
                
                const selectsClonados = nuevoMaterial.querySelectorAll('select');
                selectsClonados.forEach(select => {
                    if ($(select).hasClass('select2-centros-costo-detalle')) {
                        select.name = `centros_costo[${contadorMateriales}][]`;
                    } else if ($(select).hasClass('select2-personal-detalle')) { 
                        select.name = `personal_ids[${contadorMateriales}][]`;
                    }
                    
                    $(select).removeClass('select2-hidden-accessible');
                    const select2Container = select.nextElementSibling;
                    if (select2Container && select2Container.classList.contains('select2')) {
                        select2Container.remove();
                    }
                    
                    Array.from(select.options).forEach(option => {
                        option.selected = false;
                    });
                    select.selectedIndex = -1;
                });
                
                // Actualizar ID de col-centros
                const colCentros = nuevoMaterial.querySelector('[id^="col-centros-"]');
                if (colCentros) {
                    colCentros.id = `col-centros-${contadorMateriales}`;
                }
                
                const btnEliminar = nuevoMaterial.querySelector('.eliminar-material');
                if (btnEliminar) {
                    btnEliminar.style.display = 'block';
                }
                
                contenedor.appendChild(nuevoMaterial);
                
                const selectsNuevos = nuevoMaterial.querySelectorAll('select');
                selectsNuevos.forEach(select => {
                    if (select.name === 'unidad[]') {
                        $(select).select2({
                            placeholder: 'Seleccionar unidad de medida...',
                            allowClear: true,
                            width: '100%',
                            language: {
                                noResults: function () { return 'No se encontraron resultados'; }
                            }
                        });
                    } else if ($(select).hasClass('select2-centros-costo-detalle')) {
                        $(select).select2({
                            placeholder: 'Seleccionar uno o más centros de costo...',
                            allowClear: true,
                            width: '100%',
                            multiple: true,
                            language: {
                                noResults: function () { return 'No se encontraron resultados'; }
                            }
                        });
                    } else if ($(select).hasClass('select2-personal-detalle')) { 
                        $(select).select2({
                            placeholder: 'Seleccionar personal...',
                            allowClear: true,
                            width: '100%',
                            multiple: true,
                            language: {
                                noResults: function () { return 'No se encontraron resultados'; }
                            }
                        });
                    }
                });
                
                contadorMateriales++;
                actualizarEventosEliminar();
                agregarEventosACampos();
                formularioModificado = true;
                
                nuevoMaterial.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    }
    
    // ============================================
    // FUNCIÓN: Actualizar eventos de eliminar
    // ============================================
    function actualizarEventosEliminar() {
        const botonesEliminar = document.querySelectorAll('.eliminar-material');
        
        botonesEliminar.forEach(btn => {
            btn.onclick = function(e) {
                e.preventDefault();
                const materialesItems = document.querySelectorAll('.material-item');
                
                if (materialesItems.length > 1) {
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
                                const itemAEliminar = this.closest('.material-item');
                                $(itemAEliminar).find('select').each(function() {
                                    if ($(this).data('select2')) {
                                        $(this).select2('destroy');
                                    }
                                });
                                itemAEliminar.remove();
                                reindexarCentrosCosto();
                                formularioModificado = true;
                            }
                        });
                    } else {
                        if (confirm('¿Eliminar este material?')) {
                            const itemAEliminar = this.closest('.material-item');
                            $(itemAEliminar).find('select').each(function() {
                                if ($(this).data('select2')) {
                                    $(this).select2('destroy');
                                }
                            });
                            itemAEliminar.remove();
                            reindexarCentrosCosto();
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
    
    function reindexarCentrosCosto() {
        const materiales = document.querySelectorAll('.material-item');
        materiales.forEach((material, index) => {
            const selectCentros = material.querySelector('select.select2-centros-costo-detalle');
            if (selectCentros) {
                selectCentros.name = `centros_costo[${index}][]`;
            }
            const selectPersonal = material.querySelector('select.select2-personal-detalle');
            if (selectPersonal) {
                selectPersonal.name = `personal_ids[${index}][]`;
            }
            // Actualizar también el ID del contenedor de centros de costo
            const colCentros = material.querySelector('[id^="col-centros-"]');
            if (colCentros) {
                colCentros.id = `col-centros-${index}`;
            }
        });
    }
    
    actualizarEventosEliminar();
    
    // ============================================
    // FUNCIÓN: Botón limpiar
    // ============================================
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
                        if (selectTipoPedido) {
                            selectTipoPedido.value = '';
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
                    if (selectTipoPedido) {
                        selectTipoPedido.value = '';
                        valorInicialTipoPedido = '';
                    }
                    alert('Formulario limpiado');
                }
            }
        });
    }
    
    // ============================================
    // FUNCIÓN: Validación de archivos
    // ============================================
    const form = document.querySelector('form[action="pedidos_nuevo.php"], form[action="pedidos_editar.php"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            let archivosInvalidos = false;
            let mensajeError = '';
            const archivosInputs = form.querySelectorAll('input[type="file"][name^="archivos_"]');
            archivosInputs.forEach(input => {
                for (let i = 0; i < input.files.length; i++) {
                    if (input.files[i].size > 5 * 1024 * 1024) {
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
    
    // ============================================
    // CONTROL DINÁMICO DE UBICACIONES SEGÚN ALMACÉN
    // ============================================
    const selectAlmacen = document.querySelector('select[name="id_obra"]');
    const selectUbicacion = document.querySelector('select[name="id_ubicacion"]');

    // Guardar todas las opciones originales de ubicación
    let todasUbicaciones = [];

    function inicializarUbicaciones() {
        todasUbicaciones = Array.from(selectUbicacion.options).slice(1); // Excluir "Seleccionar"
    }

    function filtrarUbicacionesPorAlmacen() {
        const almacenSeleccionado = selectAlmacen.value;
        
        // Limpiar select de ubicación (mantener opción "Seleccionar")
        selectUbicacion.innerHTML = '<option value="">Seleccionar</option>';
        
        if (!almacenSeleccionado) {
            // Si no hay almacén seleccionado, mostrar todas
            todasUbicaciones.forEach(opt => {
                selectUbicacion.appendChild(opt.cloneNode(true));
            });
            if ($(selectUbicacion).data('select2')) {
                $(selectUbicacion).val(null).trigger('change');
            }
            return;
        }
        
        // Si es BASE ARCE (id_almacen = 1), solo mostrar BASE (id_ubicacion = 1)
        if (almacenSeleccionado === '1') {
            const opcionBase = todasUbicaciones.find(opt => opt.value === '1');
            if (opcionBase) {
                selectUbicacion.appendChild(opcionBase.cloneNode(true));
                // Seleccionar automáticamente BASE
                if ($(selectUbicacion).data('select2')) {
                    $(selectUbicacion).val('1').trigger('change');
                } else {
                    selectUbicacion.value = '1';
                }
            }
        } else {
            // Para otros almacenes de ARCE, mostrar todas las ubicaciones
            todasUbicaciones.forEach(opt => {
                selectUbicacion.appendChild(opt.cloneNode(true));
            });
            if ($(selectUbicacion).data('select2')) {
                $(selectUbicacion).val(null).trigger('change');
            }
        }
    }

    // Ejecutar al cambiar almacén
    if (selectAlmacen && selectUbicacion) {
        // Inicializar opciones
        inicializarUbicaciones();
        
        // Evento de cambio con Select2
        $(selectAlmacen).on('change', filtrarUbicacionesPorAlmacen);
        
        // Estado inicial
        setTimeout(filtrarUbicacionesPorAlmacen, 200);
    }

    // ============================================
    // FUNCIÓN: Crear producto
    // ============================================
    const btnGuardarProducto = document.getElementById('btn-guardar-producto');
    if (btnGuardarProducto) {
        btnGuardarProducto.addEventListener('click', function() {
            const form = document.getElementById('form-crear-producto');
            const formData = new FormData(form);
            
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
            
            btnGuardarProducto.disabled = true;
            const textoOriginal = btnGuardarProducto.textContent;
            btnGuardarProducto.textContent = 'Guardando...';
            
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
                    if (currentSearchButton && data.producto) {
                        seleccionarProductoCreado(data.producto);
                    }
                    
                    $('#crear_producto').modal('hide');
                    
                    form.reset();
                    resetearSwitches(form);
                    
                    if ($.fn.dataTable.isDataTable('#datatable_producto')) {
                        $('#datatable_producto').DataTable().ajax.reload();
                    }
                    
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
                btnGuardarProducto.disabled = false;
                btnGuardarProducto.textContent = textoOriginal;
            });
        });
    }
    
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
    
    $('#crear_producto').on('hidden.bs.modal', function () {
        const form = document.getElementById('form-crear-producto');
        if (form) {
            form.reset();
            resetearSwitches(form);
        }
    });
    
    $('#crear_producto').on('show.bs.modal', function () {
        $('.modal').not(this).modal('hide');
    });
});

// ============================================
// EVENTOS JQUERY FUERA DE DOMCONTENTLOADED
// ============================================
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