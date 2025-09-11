<?php 
//=======================================================================
// VISTA: v_pedidos_editar.php - Restructured to match v_pedidos_nuevo.php
//=======================================================================
$pedido = $pedido_data[0]; // Datos del pedido principal
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Editar Pedido</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Datos del Pedido <small><?php echo $pedido['cod_pedido']; ?></small></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br>
                        <form class="form-horizontal form-label-left" action="pedidos_editar.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php echo $pedido['id_pedido']; ?>">
                            
                            <!-- Información básica del pedido -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Código del Pedido:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo $pedido['cod_pedido']; ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Nombre del Pedido <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="nom_pedido" class="form-control" value="<?php echo $pedido['nom_pedido']; ?>" placeholder="Nombre del Pedido" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Solicitante:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo $pedido['nom_personal'] . ' ' . $pedido['ape_personal']; ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Fecha de Pedido:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo date('d/m/Y H:i', strtotime($pedido['fec_pedido'])); ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Fecha de Necesidad <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <?php 
                                    // Determinar la fecha mínima: la menor entre la fecha actual del pedido y hoy
                                    $fecha_pedido = $pedido['fec_req_pedido'];
                                    $fecha_hoy = date('Y-m-d');
                                    $fecha_minima = (strtotime($fecha_pedido) < strtotime($fecha_hoy)) ? $fecha_pedido : $fecha_hoy;
                                    ?>
                                    <input type="date" name="fecha_necesidad" class="form-control" value="<?php echo $fecha_pedido; ?>" min="<?php echo $fecha_minima; ?>" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Nº OT/LCL/LCA:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="num_ot" class="form-control" value="<?php echo $pedido['ot_pedido']; ?>" placeholder="Nº OT/LCL/LCA">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Número de contacto <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="contacto" class="form-control" value="<?php echo $pedido['cel_pedido']; ?>" placeholder="Número de contacto" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Lugar de Entrega <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="lugar_entrega" class="form-control" value="<?php echo $pedido['lug_pedido']; ?>" placeholder="Lugar de Entrega" required>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <!-- Sección de materiales/servicios -->
                            <div class="x_title">
                                <h4>Detalles del Pedido <small></small></h4>
                                <div class="clearfix"></div>
                            </div>

                            <div id="contenedor-materiales">
                                <?php 
                                $contador_material = 0;
                                foreach ($pedido_detalle as $detalle) { 
                                    // Parsear comentarios para extraer unidad y observaciones
                                    $comentario = $detalle['com_pedido_detalle'];
                                    $unidad_id = '';
                                    $observaciones = '';
                                    
                                    // Parsear el ID de la unidad directamente del comentario
                                    if (preg_match('/Unidad ID:\s*(\d+)\s*\|/', $comentario, $matches)) {
                                        $unidad_id = trim($matches[1]);
                                    }
                                    
                                    // Si no encuentra Unidad ID, parsear por nombre (para compatibilidad con datos antiguos)
                                    if (empty($unidad_id) && preg_match('/Unidad:\s*([^|]*)\s*\|/', $comentario, $matches)) {
                                        $unidad_nombre = trim($matches[1]);
                                        // Buscar el ID de la unidad por nombre
                                        foreach ($unidades_medida as $unidad) {
                                            if ($unidad['nom_unidad_medida'] == $unidad_nombre) {
                                                $unidad_id = $unidad['id_unidad_medida'];
                                                break;
                                            }
                                        }
                                    }
                                    
                                    if (preg_match('/Obs:\s*(.*)$/', $comentario, $matches)) {
                                        $observaciones = trim($matches[1]);
                                    }
    
                                    // Parsear requisitos
                                    $requisitos = $detalle['req_pedido'];
                                    $sst = '';
                                    
                                    if (preg_match('/SST:\s*([^|]*)\s*\|/', $requisitos, $matches)) {
                                        $sst = trim($matches[1]);
                                    }
                                    if (preg_match('/MA:\s*([^|]*)\s*\|/', $requisitos, $matches)) {
                                        $ma = trim($matches[1]);
                                        $sst .= !empty($sst) ? ' | MA: ' . $ma : 'MA: ' . $ma;
                                    }
                                    if (preg_match('/CA:\s*(.*)$/', $requisitos, $matches)) {
                                        $ca = trim($matches[1]);
                                        $sst .= !empty($sst) ? ' | CA: ' . $ca : 'CA: ' . $ca;
                                    }
                                ?>
                                <div class="material-item border p-3 mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Material/Servicio <span class="text-danger">*</span>:</label>
                                            <div class="input-group">
                                                <input type="text" name="descripcion[]" class="form-control" value="<?php echo $detalle['prod_pedido_detalle']; ?>" placeholder="Material o Servicio" required>
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
                                                    <option value="<?php echo $unidad['id_unidad_medida']; ?>" 
                                                            <?php echo ($unidad['id_unidad_medida'] == $unidad_id) ? 'selected' : ''; ?>>
                                                        <?php echo $unidad['nom_unidad_medida']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Cantidad <span class="text-danger">*</span>:</label>
                                            <input type="number" name="cantidad[]" class="form-control" step="0.01" min="0" 
                                                   value="<?php echo $detalle['cant_pedido_detalle']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <label>Observaciones:</label>
                                            <textarea name="observaciones[]" class="form-control" rows="1" placeholder="Observaciones o comentarios"><?php echo $observaciones; ?></textarea>
                                        </div>
                                   
                                        <div class="col-md-6">
                                            <label>SST/MA/CA <span class="text-danger">*</span>:</label>
                                            <?php
                                            // Reconstruir el valor en formato "valor1/valor2/valor3" para edición
                                            $sst_valor = '';
                                            if (preg_match('/SST:\s*([^|]*)\s*\|/', $requisitos, $matches)) {
                                                $sst_valor .= trim($matches[1]);
                                            }
                                            if (preg_match('/MA:\s*([^|]*)\s*\|/', $requisitos, $matches)) {
                                                $sst_valor .= '/' . trim($matches[1]);
                                            } else {
                                                $sst_valor .= '/';
                                            }
                                            if (preg_match('/CA:\s*(.*)$/', $requisitos, $matches)) {
                                                $sst_valor .= '/' . trim($matches[1]);
                                            } else {
                                                $sst_valor .= '/';
                                            }
                                            ?>
                                            <input type="text" name="sst[]" class="form-control" 
                                                value="<?php echo $sst_valor; ?>" 
                                                placeholder="SST / MA / CA (ej: aa / bb / cc)" 
                                                pattern="[^/]+/[^/]+/[^/]+" 
                                                title="Por favor ingresa los tres valores separados por barras (ej: valor1 / valor2 / valor3)" 
                                                required>
                                            <small class="form-text text-muted">Ingresa los tres valores separados por barras (/)</small>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <label>Adjuntar Archivos:</label>
                                            <input type="hidden" name="id_detalle[]" value="<?php echo $detalle['id_pedido_detalle']; ?>">
                                            <input type="file" name="archivos_<?php echo $contador_material; ?>[]" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                                            <small class="form-text text-muted">Formatos permitidos: PDF, JPG, PNG, DOC, XLS. Máximo 5MB por archivo.</small>
                                            <?php if (!empty($detalle['archivos'])) { ?>
                                                <div class="text-muted small mt-1">
                                                    <strong>Archivos actuales:</strong> <?php echo $detalle['archivos']; ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="col-md-6 d-flex align-items-end">
                                            <?php if ($contador_material > 0) { ?>
                                            <button type="button" class="btn btn-danger btn-sm eliminar-material">
                                                <i class="fa fa-trash"></i> Eliminar
                                            </button>
                                            <?php } else { ?>
                                            <button type="button" class="btn btn-danger btn-sm eliminar-material" style="display: none;">
                                                <i class="fa fa-trash"></i> Eliminar
                                            </button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <?php 
                                    $contador_material++;
                                } 
                                ?>
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
                                    <textarea name="aclaraciones" class="form-control" rows="4" ><?php echo $pedido['acl_pedido']; ?></textarea>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 offset-md-6">
                                    <a href="pedidos_mostrar.php" class="btn btn-outline-secondary btn-block">Cancelar</a>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <button type="reset" class="btn btn-outline-danger btn-block">Limpiar</button>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <button type="submit" name="actualizar" id="btn_actualizar" class="btn btn-success btn-block">Actualizar Pedido</button>
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
    <div class="modal-dialog modal-lg" role="document">
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
    // Guardar referencia al botón que se clickeó
    currentSearchButton = button;
    
    // Abrir la modal
    $('#buscar_producto').modal('show');
    
    // Cargar los productos en la tabla
    cargarProductos();
}

function cargarProductos() {
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
            "type": "POST"
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
            "emptyTable": "No hay datos disponibles en la tabla",
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

// Manejo de creación de productos
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
            const archivoCalibrado = document.querySelector('input[name="dcal_archivo"]')?.files[0];
            const archivoOperatividad = document.querySelector('input[name="dope_archivo"]')?.files[0];
            
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    let contadorMateriales = <?php echo count($pedido_detalle); ?>;
    let formularioModificado = false;
    
    // Detectar cambios en cualquier campo del formulario
    const todosLosCampos = document.querySelectorAll('input, textarea, select');
    
    function marcarFormularioComoModificado() {
        formularioModificado = true;
    }
    
    // Agregar evento change a todos los campos
    function actualizarEventosCampos() {
        const todosLosCamposActualizados = document.querySelectorAll('input, textarea, select');
        todosLosCamposActualizados.forEach(campo => {
            // Remover eventos anteriores para evitar duplicados
            campo.removeEventListener('change', marcarFormularioComoModificado);
            campo.removeEventListener('input', marcarFormularioComoModificado);
            
            // Agregar eventos nuevamente
            campo.addEventListener('change', marcarFormularioComoModificado);
            campo.addEventListener('input', marcarFormularioComoModificado);
        });
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
                if (input.type === 'hidden') {
                    input.value = '';
                } else if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                } else if (input.type !== 'file') {
                    input.value = '';
                }
            });

            // Actualizar el name del input file para que sea único
            const fileInput = nuevoMaterial.querySelector('input[type="file"]');
            if (fileInput) {
                fileInput.name = `archivos_${contadorMateriales}[]`;
            }

            // Mostrar el botón eliminar
            const btnEliminar = nuevoMaterial.querySelector('.eliminar-material');
            if (btnEliminar) {
                btnEliminar.style.display = 'block';
            }
            
            // Limpiar texto de archivos actuales
            const archivoActual = nuevoMaterial.querySelector('.text-muted');
            if (archivoActual) {
                archivoActual.remove();
            }
            
            contenedor.appendChild(nuevoMaterial);
            contadorMateriales++;
            
            // Actualizar eventos
            actualizarEventosEliminar();
            actualizarEventosCampos();
            
            // Marcar como modificado
            formularioModificado = true;
        });
    }
    
    // Función para actualizar eventos de eliminar
    function actualizarEventosEliminar() {
        document.querySelectorAll('.eliminar-material').forEach(btn => {
            btn.onclick = function() {
                if (document.querySelectorAll('.material-item').length > 1) {
                    this.closest('.material-item').remove();
                    formularioModificado = true;
                }
            };
        });
    }
    
    // Inicializar eventos
    actualizarEventosEliminar();
    actualizarEventosCampos();
    
    // Interceptar el botón reset del formulario
    const btnLimpiar = document.querySelector('button[type="reset"]');
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '¿Restaurar valores originales?',
                    text: 'Se restaurarán todos los valores a su estado original.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, restaurar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload(); // Recargar la página para restaurar valores originales
                    }
                });
            } else {
                // Fallback sin SweetAlert
                if (confirm('¿Restaurar valores originales? Se restaurarán todos los valores a su estado original.')) {
                    location.reload();
                }
            }
        });
    }
});
</script>