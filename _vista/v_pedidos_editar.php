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
                                <label class="control-label col-md-3 col-sm-3">Tipo de Pedido:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo isset($pedido['nom_producto_tipo']) ? $pedido['nom_producto_tipo'] : 'N/A'; ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Almacén:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo isset($pedido['nom_almacen']) ? $pedido['nom_almacen'] : 'N/A'; ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Ubicación <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="id_ubicacion" class="form-control" required>
                                        <option value="">Seleccionar</option>
                                        <?php foreach ($ubicaciones as $ubicacion) { ?>
                                            <option value="<?php echo $ubicacion['id_ubicacion']; ?>" 
                                                    <?php echo ($ubicacion['id_ubicacion'] == $pedido['id_ubicacion']) ? 'selected' : ''; ?>>
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
                                        <?php 
                                        // Cargar centros de costo en el controlador principal
                                        foreach ($centros_costo as $centro) { ?>
                                            <option value="<?php echo $centro['id_centro_costo']; ?>"
                                                    <?php echo ($centro['id_centro_costo'] == $pedido['id_centro_costo']) ? 'selected' : ''; ?>>
                                                <?php echo $centro['nom_centro_costo']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Código del Pedido:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo isset($pedido['cod_pedido']) ? $pedido['cod_pedido'] : 'N/A'; ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Nombre del Pedido:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="nom_pedido" class="form-control" value="<?php echo $pedido['nom_pedido']; ?>" placeholder="Nombre del Pedido">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Solicitante:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo $pedido['nom_personal']; ?>" readonly>
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

                                    // CAMBIO: Obtener directamente el valor de req_pedido
                                    $sst_descripcion = $detalle['req_pedido'];

                                    $stock_real = isset($detalle['cantidad_disponible_real']) ? $detalle['cantidad_disponible_real'] : 0;
                                    $stock_almacen = isset($detalle['cantidad_disponible_almacen']) ? $detalle['cantidad_disponible_almacen'] : 0;
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
                                            <input type="hidden" name="id_material[]" value="<?php echo $detalle['id_producto'] ?? ''; ?>">
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
                                            <input 
                                                type="number" 
                                                name="cantidad[]" 
                                                class="form-control" 
                                                step="0.01" 
                                                min="0" 
                                                value="<?php echo $detalle['cant_pedido_detalle']; ?>" 
                                                required
                                                data-stock="<?php echo isset($detalle['cantidad_disponible_real']) ? $detalle['cantidad_disponible_real'] : 0; ?>">
                                            <small class="form-text text-muted">
                                                Stock Disponible/Almacén:
                                                <?php echo isset($detalle['cantidad_disponible_real']) ? $detalle['cantidad_disponible_real'] : 0; ?> /
                                                <?php echo isset($detalle['cantidad_disponible_almacen']) ? $detalle['cantidad_disponible_almacen'] : 0; ?>
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <label>N° OT/LCL/LCA del Material <span class="text-danger">*</span>:</label>
                                            <input type="text" 
                                                name="ot_detalle[]" 
                                                class="form-control" 
                                                value="<?php echo isset($detalle['ot_pedido_detalle']) ? htmlspecialchars($detalle['ot_pedido_detalle']) : ''; ?>" 
                                                placeholder="Ingrese OT específico de este material" 
                                                required>
                                        </div>

                                        <div class="col-md-6">
                                            <label>Observaciones:</label>
                                            <input type="text" name="observaciones[]" class="form-control"  
                                                value="<?php echo $observaciones; ?>" placeholder="Observaciones o comentarios">
                                        </div>
                            
                                    </div>
                                    
                                    <!-- Dentro de cada material-item, después de la sección de observaciones -->
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <label>Centros de Costo para este Material <span class="text-danger">*</span>:</label>
                                            <select name="centros_costo[<?php echo $contador_material; ?>][]" class="form-control select2-centros-costo-detalle" multiple required>
                                                <?php 
                                                // Obtener centros de costo seleccionados para este detalle
                                                $centros_seleccionados = array();
                                                if (isset($detalle['id_pedido_detalle'])) {
                                                    $centros_seleccionados = ObtenerCentrosCostoPorDetalle($detalle['id_pedido_detalle']);
                                                }
                                                
                                                foreach ($centros_costo as $centro) { 
                                                    $selected = in_array($centro['id_centro_costo'], $centros_seleccionados) ? 'selected' : '';
                                                ?>
                                                    <option value="<?php echo $centro['id_centro_costo']; ?>" <?php echo $selected; ?>>
                                                        <?php echo $centro['nom_centro_costo']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <small class="form-text text-muted">
                                                <i class="fa fa-info-circle"></i> Seleccione uno o más centros de costo específicos para este material.
                                            </small>
                                        </div>


                                        <div class="col-md-6">
                                            <label>Personal Asignado</label>
                                            <select name="personal_ids[<?php echo $contador_material; ?>][]" class="form-control select2-personal-detalle" multiple>
                                                <?php 
                                                $personal_seleccionados = isset($detalle['personal_ids']) ? $detalle['personal_ids'] : array();
                                                
                                                foreach ($personal_list as $persona) { 
                                                    $selected = in_array($persona['id_personal'], $personal_seleccionados) ? 'selected' : '';
                                                ?>
                                                    <option value="<?php echo $persona['id_personal']; ?>" <?php echo $selected; ?>>
                                                        <?php echo $persona['nom_personal']; ?>
                                                        <?php if (!empty($persona['nom_cargo'])) echo ' - ' . $persona['nom_cargo']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mt-2">

                                        <div class="col-md-6">
                                            <label>Descripción SST/MA/CA <span class="text-danger">*</span>:</label>
                                            <input type="text" name="sst[]" class="form-control" value="<?php echo htmlspecialchars($sst_descripcion); ?>" placeholder="Requisitos de SST, MA y CA" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label>Adjuntar Archivos:</label>
                                            <input type="hidden" name="id_detalle[]" value="<?php echo $detalle['id_pedido_detalle']; ?>">
                                            <input type="file" name="archivos_<?php echo $contador_material; ?>[]" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                                            <small class="form-text text-muted">Formatos permitidos: PDF, JPG, PNG, DOC, XLS. Máximo 5MB por archivo.</small>
                                            
                                            <?php if (!empty($detalle['archivos'])) { ?>
                                                <div class="archivos-existentes text-muted small mt-1">
                                                    <strong>Archivos actuales:</strong>
                                                    <div class="mt-1">
                                                        <?php 
                                                        // Dividir los archivos si están separados por comas
                                                        $archivos = explode(',', $detalle['archivos']);
                                                        foreach ($archivos as $archivo) { 
                                                            $archivo = trim($archivo);
                                                            if (!empty($archivo)) {
                                                                // Ruta ajustada a tu estructura de directorios
                                                                $archivo_url = "../_archivos/pedidos/" . $archivo;
                                                                
                                                                // Determinar el icono según la extensión
                                                                $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
                                                                $icono = 'fa-file';
                                                                $clase_color = 'text-info';
                                                                
                                                                switch ($extension) {
                                                                    case 'pdf':
                                                                        $icono = 'fa-file-pdf-o';
                                                                        $clase_color = 'text-danger';
                                                                        break;
                                                                    case 'jpg':
                                                                    case 'jpeg':
                                                                    case 'png':
                                                                    case 'gif':
                                                                        $icono = 'fa-file-image-o';
                                                                        $clase_color = 'text-success';
                                                                        break;
                                                                    case 'doc':
                                                                    case 'docx':
                                                                        $icono = 'fa-file-word-o';
                                                                        $clase_color = 'text-primary';
                                                                        break;
                                                                    case 'xls':
                                                                    case 'xlsx':
                                                                        $icono = 'fa-file-excel-o';
                                                                        $clase_color = 'text-warning';
                                                                        break;
                                                                }
                                                        ?>
                                                            <a href="<?php echo $archivo_url; ?>" target="_blank" class="<?php echo $clase_color; ?> mr-2 d-inline-block mb-1" title="Ver <?php echo $archivo; ?>" style="text-decoration: none;">
                                                                <i class="fa <?php echo $icono; ?>"></i>
                                                                <?php echo strlen($archivo) > 25 ? substr($archivo, 0, 25) . '...' : $archivo; ?>
                                                            </a>
                                                        <?php 
                                                            }
                                                        } 
                                                        ?>
                                                    </div>
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
    
    // CORRECCIÓN 1: Obtener el tipo de producto del pedido para filtrar correctamente
    const tipoProductoInput = document.querySelector('input[value*="MATERIAL"], input[value*="SERVICIO"]');
    let tipoProducto = '';
    
    // Intentar obtener el tipo desde el input readonly
    if (tipoProductoInput) {
        const valor = tipoProductoInput.value.toUpperCase();
        if (valor.includes('MATERIAL')) {
            tipoProducto = '1'; // ID para MATERIAL
        } else if (valor.includes('SERVICIO')) {
            tipoProducto = '2'; // ID para SERVICIO
        }
    }
    
    // Si no se puede determinar el tipo, buscar en los datos del pedido
    if (!tipoProducto) {
        // Como fallback, podríamos usar una variable PHP si está disponible
        tipoProducto = typeof window.tipoPedidoActual !== 'undefined' ? window.tipoPedidoActual : '';
    }
    
    // Abrir la modal
    $('#buscar_producto').modal('show');
    
    // Cargar los productos en la tabla con el filtro de tipo
    cargarProductos(tipoProducto);
}

function cargarProductos(tipoProducto = '') {
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
                
                if (tipoProducto) {
                    d.tipo_producto = tipoProducto;
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
            { "title": "Acción" }
        ],
        "order": [[1, 'asc']],
        "pageLength": 10,
        "lengthMenu": [10, 25, 50, 100],
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron resultados para el tipo de pedido actual",
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
            
            //  Actualizar el select de unidad de medida con Select2
            let selectUnidad = materialItem.querySelector('select[name="unidad[]"]');
            if (selectUnidad) {
                
                if ($(selectUnidad).data('select2')) {
                    $(selectUnidad).val(idUnidad).trigger('change');
                } else {
                    // Si aún no es Select2, asignar valor normal
                    selectUnidad.value = idUnidad;
                }
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

document.addEventListener('DOMContentLoaded', function() {
    let contadorMateriales = <?php echo count($pedido_detalle); ?>;
    let formularioModificado = false;
    
    // Variable para control de aplicación automática de centro de costo
    let aplicarCentroCostoAutomaticamente = false;
    
    // CORRECCIÓN 2: Obtener y almacenar el tipo de producto para uso en JavaScript
    const tipoProductoElement = document.querySelector('input[value*="MATERIAL"], input[value*="SERVICIO"]');
    if (tipoProductoElement) {
        const valor = tipoProductoElement.value.toUpperCase();
        if (valor.includes('MATERIAL')) {
            window.tipoPedidoActual = '1';
        } else if (valor.includes('SERVICIO')) {
            window.tipoPedidoActual = '2';
        }
    }
    
    // Sincronización de centro de costo de cabecera con materiales
    const selectCentroCostoCabecera = document.querySelector('select[name="id_centro_costo"]');
    
    if (selectCentroCostoCabecera) {
        $(selectCentroCostoCabecera).on('select2:select', function(e) {
            const centroCostoSeleccionado = $(this).val();
            const nombreCentroCosto = $(this).find('option:selected').text();
            
            // Preguntar si desea aplicar a todos los materiales
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
                        formularioModificado = true;
                        
                        // Mensaje de confirmación
                        Swal.fire({
                            icon: 'success',
                            title: 'Aplicado',
                            text: 'El centro de costo se aplicará a todos los materiales actuales y futuros',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        aplicarCentroCostoAutomaticamente = false;
                    }
                });
            } else {
                // Fallback para navegadores sin SweetAlert
                const confirmar = confirm(`¿Desea aplicar el centro de costo "${nombreCentroCosto}" a todos los materiales del pedido?\n\nLos nuevos materiales también usarán este centro de costo automáticamente.`);
                if (confirmar) {
                    aplicarCentroCostoAutomaticamente = true;
                    aplicarCentroCostoATodosMateriales(centroCostoSeleccionado);
                    formularioModificado = true;
                    alert('El centro de costo se aplicará a todos los materiales actuales y futuros');
                } else {
                    aplicarCentroCostoAutomaticamente = false;
                }
            }
        });
    }
    
    // Función para aplicar centro de costo a todos los materiales
    function aplicarCentroCostoATodosMateriales(centroCostoId) {
        const selectsCentrosCosto = document.querySelectorAll('select.select2-centros-costo-detalle');
        
        selectsCentrosCosto.forEach(select => {
            if ($(select).data('select2')) {
                // Obtener valores actuales
                let valoresActuales = $(select).val() || [];
                
                // Si no está ya incluido, agregarlo
                if (!valoresActuales.includes(centroCostoId)) {
                    valoresActuales = [centroCostoId]; // Reemplazar con el de cabecera
                    $(select).val(valoresActuales).trigger('change');
                }
            }
        });
    }
    
    // Detectar cambios en cualquier campo del formulario
    function marcarFormularioComoModificado() {
        formularioModificado = true;
    }
    
    // Agregar evento change a todos los campos
    function actualizarEventosCampos() {
        const todosLosCamposActualizados = document.querySelectorAll('input, textarea, select');
        todosLosCamposActualizados.forEach(campo => {
            campo.removeEventListener('change', marcarFormularioComoModificado);
            campo.removeEventListener('input', marcarFormularioComoModificado);
            campo.addEventListener('change', marcarFormularioComoModificado);
            campo.addEventListener('input', marcarFormularioComoModificado);
        });
    }
    
    const btnAgregarMaterial = document.getElementById('agregar-material');
    if (btnAgregarMaterial) {
        btnAgregarMaterial.addEventListener('click', function(e) {
            e.preventDefault();
            
            const contenedor = document.getElementById('contenedor-materiales');
            const materialOriginal = contenedor.querySelector('.material-item');
            
            if (materialOriginal) {

                const valoresOriginalesSelect2 = {};
                const selectsOriginales = materialOriginal.querySelectorAll(
                    'select[name="unidad[]"], select.select2-centros-costo-detalle, select.select2-personal-detalle'
                );
                
                selectsOriginales.forEach((select, index) => {
                    if ($(select).data('select2')) {
                        valoresOriginalesSelect2[index] = $(select).val();
                    }
                });
                
                // Destruir Select2 SOLO del material que se va a clonar
                selectsOriginales.forEach(select => {
                    if ($(select).data('select2')) {
                        $(select).select2('destroy');
                    }
                });
                
                // Clonar el elemento
                const nuevoMaterial = materialOriginal.cloneNode(true);
                
                selectsOriginales.forEach((select, index) => {
                    if (select.name === 'unidad[]') {
                        $(select).select2({
                            placeholder: 'Seleccionar unidad de medida...',
                            allowClear: true,
                            width: '100%',
                            language: {
                                noResults: function () { return 'No se encontraron resultados'; }
                            }
                        });
                        if (valoresOriginalesSelect2[index]) {
                            $(select).val(valoresOriginalesSelect2[index]).trigger('change');
                        }
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
                        if (valoresOriginalesSelect2[index]) {
                            $(select).val(valoresOriginalesSelect2[index]).trigger('change');
                        }
                    } else if ($(select).hasClass('select2-personal-detalle')) {
                        //  Reinicializar select de personal
                        $(select).select2({
                            placeholder: 'Seleccionar personal...',
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
                    }
                });
                
                // Limpiar campos del nuevo material (código existente sin cambios)
                const inputDescripcion = nuevoMaterial.querySelector('input[name="descripcion[]"]');
                if (inputDescripcion) inputDescripcion.value = '';
                
                const inputIdMaterial = nuevoMaterial.querySelector('input[name="id_material[]"]');
                if (inputIdMaterial) inputIdMaterial.value = '';
                
                const inputCantidad = nuevoMaterial.querySelector('input[name="cantidad[]"]');
                if (inputCantidad) inputCantidad.value = '';

                const inputIdDetalle = nuevoMaterial.querySelector('input[name="id_detalle[]"]');
                if (inputIdDetalle) inputIdDetalle.value = '';

                const inputEspecificaciones = nuevoMaterial.querySelector('textarea[name="especificaciones[]"]');
                if (inputEspecificaciones) inputEspecificaciones.value = '';

                const inputOtDetalle = nuevoMaterial.querySelector('input[name="ot_detalle[]"]');
                if (inputOtDetalle) inputOtDetalle.value = '';

                const inputObservaciones = nuevoMaterial.querySelector('input[name="observaciones[]"]');
                if (inputObservaciones) inputObservaciones.value = '';

                const inputSST = nuevoMaterial.querySelector('input[name="sst[]"]');
                if (inputSST) inputSST.value = '';

                const inputFoto = nuevoMaterial.querySelector('input[type="file"]');
                if (inputFoto) {
                    inputFoto.value = '';
                    const archivoName = inputFoto.getAttribute('name').match(/\d+/);
                    if (archivoName) {
                        const nuevoName = 'archivos_' + contadorMateriales;
                        inputFoto.setAttribute('name', nuevoName + '[]');
                    }
                }
                
                //  Limpiar centros de costo del NUEVO material
                const selectsCentros = nuevoMaterial.querySelectorAll('select.select2-centros-costo-detalle');
                selectsCentros.forEach(select => {
                    $(select).removeClass('select2-hidden-accessible');
                    const select2Container = select.nextElementSibling;
                    if (select2Container && select2Container.classList.contains('select2')) {
                        select2Container.remove();
                    }
                    
                    Array.from(select.options).forEach(option => {
                        option.selected = false;
                    });
                    select.selectedIndex = -1;
                    select.name = `centros_costo[${contadorMateriales}][]`;
                });

                //  Limpiar personal del NUEVO material
                const selectsPersonal = nuevoMaterial.querySelectorAll('select.select2-personal-detalle');
                selectsPersonal.forEach(select => {
                    $(select).removeClass('select2-hidden-accessible');
                    const select2Container = select.nextElementSibling;
                    if (select2Container && select2Container.classList.contains('select2')) {
                        select2Container.remove();
                    }
                    
                    Array.from(select.options).forEach(option => {
                        option.selected = false;
                    });
                    select.selectedIndex = -1;
                    select.name = `personal_ids[${contadorMateriales}][]`;
                });

                // Remover archivos existentes (código sin cambios)
                const archivosExistentes = nuevoMaterial.querySelector('.archivos-existentes');
                if (archivosExistentes) {
                    archivosExistentes.remove();
                }
                
                const divConArchivos = nuevoMaterial.querySelector('div[class*="text-muted"][class*="small"][class*="mt-1"]');
                if (divConArchivos && divConArchivos.textContent.includes('Archivos actuales')) {
                    divConArchivos.remove();
                }
                
                const enlacesArchivos = nuevoMaterial.querySelectorAll('a[href*="_archivos/pedidos/"]');
                enlacesArchivos.forEach(enlace => {
                    const contenedorPadre = enlace.closest('.archivos-existentes') || 
                                        enlace.closest('div.text-muted.small.mt-1') ||
                                        enlace.parentElement;
                    if (contenedorPadre && contenedorPadre.textContent.includes('Archivos actuales')) {
                        contenedorPadre.remove();
                    }
                });
                
                // Mostrar botón eliminar
                const btnEliminar = nuevoMaterial.querySelector('.eliminar-material');
                if (btnEliminar) {
                    btnEliminar.style.display = 'block';
                }
                
                // Agregar al contenedor
                contenedor.appendChild(nuevoMaterial);
                
                //  Inicializar Select2 en el NUEVO elemento (centros de costo Y personal)
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
                        
                        // Aplicar centro de costo de cabecera si existe
                        if (selectCentroCostoCabecera && selectCentroCostoCabecera.value) {
                            setTimeout(() => {
                                $(select).val([selectCentroCostoCabecera.value]).trigger('change');
                            }, 100);
                        }
                    } else if ($(select).hasClass('select2-personal-detalle')) {
                        //  Inicializar Select2 de personal en el nuevo material
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
                actualizarEventosCampos();
                formularioModificado = true;
                
                nuevoMaterial.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    }

    // Función para actualizar eventos de eliminar
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
                                // Reindexar después de eliminar
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
                            // Reindexar después de eliminar
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
    
    /*
    // Reindexar los names de centros de costo después de eliminar
    function reindexarCentrosCosto() {
        const materiales = document.querySelectorAll('.material-item');
        materiales.forEach((material, index) => {
            const selectCentros = material.querySelector('select.select2-centros-costo-detalle');
            if (selectCentros) {
                selectCentros.name = `centros_costo[${index}][]`;
            }
        });
    }
    */
    
    // Inicializar eventos
    actualizarEventosEliminar();
    actualizarEventosCampos();
    
    // Interceptar el botón reset
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
                        location.reload();
                    }
                });
            } else {
                if (confirm('¿Restaurar valores originales?')) {
                    location.reload();
                }
            }
        });
    }
    
    // Validación de archivos
    const form = document.querySelector('form[action="pedidos_editar.php"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            let archivosInvalidos = false;
            let mensajeError = '';
            
            const archivosInputs = form.querySelectorAll('input[type="file"][name^="archivos_"]');
            archivosInputs.forEach(input => {
                for (let i = 0; i < input.files.length; i++) {
                    if (input.files[i].size > 5 * 1024 * 1024) {
                        archivosInvalidos = true;
                        mensajeError = 'Uno o más archivos superan el límite de 5MB.';
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
    
    // Limpiar la referencia cuando se cierre la modal sin seleccionar
    $('#buscar_producto').on('hidden.bs.modal', function () {
        currentSearchButton = null;
    });
    
    // Validación de stock
    document.querySelectorAll('input[name="cantidad[]"]').forEach(input => {
        input.addEventListener('change', e => {
            const maxStock = parseFloat(input.dataset.stock || 0);
            const valor = parseFloat(input.value || 0);
            if (valor > maxStock) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stock insuficiente',
                    text: 'La cantidad supera el stock disponible. Se ajustará al máximo permitido.',
                    timer: 2000,
                    showConfirmButton: false
                });
                input.value = maxStock;
            }
        });
    });
});
</script>