<?php 
//=======================================================================
// VISTA: v_salidas_editar.php
//=======================================================================
?>

<!-- 🔹 SCRIPT PARA ELIMINAR DOCUMENTOS -->
<script>
function eliminarDocumento(idDoc) {
    Swal.fire({
        title: '¿Eliminar documento?',
        text: 'Esta acción eliminará el archivo adjunto de forma permanente.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../_controlador/compras_eliminar_documento.php',
                type: 'POST',
                data: { id_doc: idDoc },
                dataType: 'json',
                success: function(response) {
                    if (response.tipo_mensaje === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Documento eliminado',
                            text: response.mensaje,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.mensaje, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                }
            });
        }
    });
}
</script>

<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Editar Salida</h3>
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
                                <!--
                                <label class="control-label col-md-2 col-sm-2">Nº Documento de Salida:</label>
                                <div class="col-md-3 col-sm-3">
                                    <input type="text" name="ndoc_salida" class="form-control" 
                                        placeholder="Número de documento (opcional)" 
                                        value="<?php echo htmlspecialchars($salida_datos[0]['ndoc_salida'] ?? ''); ?>">
                                    <small class="text-muted">Campo opcional</small>
                                </div>
                                -->

                                <label class="control-label col-md-2 col-sm-2">Centro de Costo <span class="text-danger">*</span>:</label>
                                <div class="col-md-3 col-sm-3">
                                    <?php if ($centro_costo_usuario) { ?>
                                        <input type="text" class="form-control" 
                                            value="<?php echo htmlspecialchars($centro_costo_usuario['nom_centro_costo']); ?>" 
                                            readonly 
                                            style="background-color: #e9ecef; font-weight: 500;">
                                        <input type="hidden" name="id_centro_costo" 
                                            value="<?php echo $centro_costo_usuario['id_centro_costo']; ?>">
                                    <?php } else { ?>
                                        <input type="text" class="form-control" 
                                            value="Sin centro de costo asignado" 
                                            readonly 
                                            style="background-color: #f8d7da; color: #721c24;">
                                        <input type="hidden" name="id_centro_costo" value="">
                                        <small class="text-danger">No tienes un área asignada. Contacta con el administrador.</small>
                                    <?php } ?>
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
                                           value="<?php echo $salida_datos[0]['nom_personal']; ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Observaciones:</label>
                                <div class="col-md-9 col-sm-9">
                                    <textarea name="obs_salida" class="form-control" rows="2" 
                                    placeholder="Observaciones"><?php echo htmlspecialchars($salida_datos[0]['obs_salida'] ?? ''); ?></textarea>
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

                                    <!-- Personal Encargado CON CENTRO DE COSTO -->
                                    <div class="form-group">
                                        <label class="control-label">Personal Encargado: <span class="text-danger">*</span></label>
                                        <select name="id_personal_encargado" id="id_personal_encargado" class="form-control" required>
                                            <option value="0">No especificado</option>
                                            <?php foreach ($personal as $persona) { ?>
                                                <option value="<?php echo $persona['id_personal']; ?>"
                                                        data-centro-costo="<?php 
                                                            if (isset($centros_costo_personal[$persona['id_personal']])) {
                                                                echo htmlspecialchars($centros_costo_personal[$persona['id_personal']]['nom_centro_costo']);
                                                            } else {
                                                                echo '';
                                                            }
                                                        ?>"
                                                        <?php echo ($persona['id_personal'] == $salida_datos[0]['id_personal_encargado']) ? 'selected' : ''; ?>>
                                                    <?php echo $persona['nom_personal']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        
                                        <!-- Info de centro de costo -->
                                        <small class="form-text text-muted" id="info-centro-costo-encargado" style="display: none;">
                                            <strong>Centro de Costo:</strong> <span id="texto-centro-costo-encargado"></span>
                                        </small>
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

                                    <!-- Personal que Recibe CON CENTRO DE COSTO -->
                                    <div class="form-group">
                                        <label class="control-label">Personal que Recibe: <span class="text-danger">*</span></label>
                                        <select name="id_personal_recibe" id="id_personal_recibe" class="form-control" required>
                                            <option value="0">No especificado</option>
                                            <?php foreach ($personal as $persona) { ?>
                                                <option value="<?php echo $persona['id_personal']; ?>"
                                                        data-centro-costo="<?php 
                                                            if (isset($centros_costo_personal[$persona['id_personal']])) {
                                                                echo htmlspecialchars($centros_costo_personal[$persona['id_personal']]['nom_centro_costo']);
                                                            } else {
                                                                echo '';
                                                            }
                                                        ?>"
                                                        <?php echo ($persona['id_personal'] == $salida_datos[0]['id_personal_recibe']) ? 'selected' : ''; ?>>
                                                    <?php echo $persona['nom_personal']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        
                                        <!-- Info de centro de costo -->
                                        <small class="form-text text-muted" id="info-centro-costo-recibe" style="display: none;">
                                            <strong>Centro de Costo:</strong> <span id="texto-centro-costo-recibe"></span>
                                        </small>
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
                                    $detalle['stock_disponible_total'] = $detalle['cant_salida_detalle'] + $detalle['cantidad_disponible_origen']; 
                                ?>
                                <div class="material-item border p-3 mb-3">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <label>Material <span class="text-danger">*</span>:</label>
                                            <div class="input-group">
                                                <input type="text" name="descripcion[]" class="form-control" 
                                                    placeholder="Material" 
                                                    value="<?php 
                                                        // Priorizar nom_producto, luego prod_salida_detalle, sino ID
                                                        $nombre_producto = !empty($detalle['nom_producto']) 
                                                                            ? $detalle['nom_producto'] 
                                                                            : (!empty($detalle['prod_salida_detalle']) 
                                                                            ? $detalle['prod_salida_detalle'] 
                                                                            : 'Producto ID ' . $detalle['id_producto']);
                                                        echo htmlspecialchars($nombre_producto);
                                                    ?>" readonly required>
                                                <input type="hidden" name="id_producto[]" value="<?php echo $detalle['id_producto']; ?>">
                                                <button onclick="buscarMaterial(this)" class="btn btn-secondary btn-xs" type="button">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Cantidad <span class="text-danger">*</span>:</label>
                                            <input type="number" name="cantidad[]" class="form-control" step="0.01" 
                                                   value="<?php echo $detalle['cant_salida_detalle']; ?>" 
                                                   data-stock-disponible="<?php echo $detalle['stock_disponible_total']; ?>" 
                                                   required>
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

                            <!-- 🔹 SECCIÓN DOCUMENTOS ADJUNTOS -->
                            <div class="x_title">
                                <h4><i class="fa fa-paperclip text-info"></i> Documentos Adjuntos</h4>
                                <div class="clearfix"></div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-12">
                                    <?php 

                                    if (!empty($documentos)) { ?>
                                        <table class="table table-bordered table-striped">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Nombre del Archivo</th>
                                                    <th>Fecha de Subida</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $i = 1;
                                                foreach ($documentos as $doc) { 
                                                    $url_archivo = "../uploads/" . $doc['entidad'] . "/" . $doc['documento'];
                                                ?>
                                                    <tr id="doc-row-<?php echo $doc['id_doc']; ?>">
                                                        <td><?php echo $i++; ?></td>
                                                        <td><?php echo htmlspecialchars($doc['documento']); ?></td>
                                                        <td><?php echo date('d/m/Y H:i', strtotime($doc['fec_subida'])); ?></td>
                                                        <td>
                                                            <a href="<?php echo $url_archivo; ?>" class="btn btn-sm btn-info" target="_blank" title="Ver o Descargar">
                                                                <i class="fa fa-eye"></i> Ver
                                                            </a>
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-danger" 
                                                                    title="Eliminar Documento"
                                                                    onclick="eliminarDocumento(<?php echo $doc['id_doc']; ?>)">
                                                                <i class="fa fa-trash"></i> Eliminar
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    <?php } else { ?>
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle"></i> No hay documentos adjuntos para esta salida.
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <!-- 🔹 SUBIR NUEVOS DOCUMENTOS (OPCIONAL) -->
                            <div class="form-group">
                                <label>Subir Documentos Adicionales (Opcional)</label>
                                <input type="file" name="documento[]" id="documento" class="form-control" multiple>
                                <small class="text-muted">Puede adjuntar nuevos documentos si lo desea</small>
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

<?php require_once("../_vista/v_script.php"); ?>

<script>
$(document).ready(function() {
    console.log('🔄 Inicializando centros de costo en v_salidas_editar...');
    
    // ============================================
    // FUNCIÓN: Actualizar Centro de Costo
    // ============================================
    function actualizarCentroCosto(selectId, infoDivId, textoSpanId) {
        const $select = $('#' + selectId);
        const selectedOption = $select.find('option:selected');
        const centroCosto = selectedOption.data('centro-costo');
        const $infoDiv = $('#' + infoDivId);
        const $textoSpan = $('#' + textoSpanId);
        const valorSeleccionado = $select.val();
        
        console.log('📊 Actualizando centro de costo:', {
            selectId: selectId,
            valor: valorSeleccionado,
            centroCosto: centroCosto
        });
        
        if (centroCosto && centroCosto.trim() !== '' && valorSeleccionado !== '0' && valorSeleccionado !== '') {
            $textoSpan.text(centroCosto);
            $infoDiv.fadeIn(300);
            console.log('✅ Centro mostrado:', centroCosto);
        } else {
            $infoDiv.fadeOut(300);
            $textoSpan.text('');
            console.log('❌ Centro oculto');
        }
    }
    
    // ============================================
    // EVENTOS: Change
    // ============================================
    $('#id_personal_encargado').on('change', function() {
        console.log('🔄 Change en personal encargado');
        actualizarCentroCosto('id_personal_encargado', 'info-centro-costo-encargado', 'texto-centro-costo-encargado');
    });
    
    $('#id_personal_recibe').on('change', function() {
        console.log('🔄 Change en personal que recibe');
        actualizarCentroCosto('id_personal_recibe', 'info-centro-costo-recibe', 'texto-centro-costo-recibe');
    });
    
    // ============================================
    //  INICIALIZACIÓN: Mostrar centros al cargar (MODO EDICIÓN)
    // ============================================
    function inicializarCentrosCosto() {
        console.log('🎯 Inicializando centros de costo al cargar página (EDICIÓN)...');
        actualizarCentroCosto('id_personal_encargado', 'info-centro-costo-encargado', 'texto-centro-costo-encargado');
        actualizarCentroCosto('id_personal_recibe', 'info-centro-costo-recibe', 'texto-centro-costo-recibe');
        console.log('✅ Centros de costo inicializados en modo edición');
    }
    
    // Ejecutar después de que Select2 se inicialice
    setTimeout(inicializarCentrosCosto, 300);
    
    // Backup: reintentar después de 1 segundo
    setTimeout(inicializarCentrosCosto, 1000);
    
    console.log('✅ Script de centros de costo cargado en v_salidas_editar');
});
</script>

<script>
// Variable global para rastrear qué botón de búsqueda se clickeó
let currentSearchButton = null;
let contadorMateriales = <?php echo count($salida_detalles); ?>;

// Variables para control de cambios en edición
let almacenOrigenInicial = '';
let ubicacionOrigenInicial = '';

function buscarMaterial(button) {
    // Obtener valores de almacén y ubicación origen
    const idAlmacenOrigen = document.getElementById('id_almacen_origen').value;
    const idUbicacionOrigen = document.getElementById('id_ubicacion_origen').value;
    const idAlmacenDestino = document.getElementById('id_almacen_destino').value;
    const idUbicacionDestino = document.getElementById('id_ubicacion_destino').value;
    
    // Obtener el tipo de material desde el formulario
    const idMaterialTipo = document.querySelector('input[name="id_material_tipo"]');
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
            
            // Configurar input de cantidad según el stock disponible
            let inputCantidad = materialItem.querySelector('input[name="cantidad[]"]');
            
            if (inputCantidad) {
                const stock = parseFloat(stockDisponible);
                
                // Guardar el stock como atributo data para validaciones posteriores
                inputCantidad.setAttribute('data-stock-disponible', stock.toFixed(2));
                
                if (stock > 0) {
                    // Si hay stock, establecer min y max
                    inputCantidad.setAttribute('min', '0.01');
                    inputCantidad.setAttribute('max', stockDisponible);
                    inputCantidad.setAttribute('step', '0.01');
                    inputCantidad.removeAttribute('readonly');
                    inputCantidad.removeAttribute('title');
                } else {
                    // Si no hay stock, deshabilitar el campo
                    inputCantidad.value = '';
                    inputCantidad.setAttribute('readonly', 'readonly');
                    inputCantidad.setAttribute('title', 'No hay stock disponible');
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
        mostrarAlerta('success', 'Producto seleccionado',
            `El producto "${nombreProducto}" ha sido seleccionado correctamente.`, 2000);
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

// Verificar si hay materiales con datos
function hayMaterialesSeleccionados() {
    const materiales = document.querySelectorAll('input[name="descripcion[]"]');
    for (let i = 0; i < materiales.length; i++) {
        if (materiales[i].value.trim() !== '') {
            return true;
        }
    }
    return false;
}

// Limpiar todos los materiales
function limpiarTodosMateriales() {
    const descripciones = document.querySelectorAll('input[name="descripcion[]"]');
    const productos = document.querySelectorAll('input[name="id_producto[]"]');
    const cantidades = document.querySelectorAll('input[name="cantidad[]"]');
    
    descripciones.forEach(input => input.value = '');
    productos.forEach(input => input.value = '');
    cantidades.forEach(input => {
        input.value = '';
        input.removeAttribute('min');
        input.removeAttribute('max');
        input.removeAttribute('readonly');
        input.removeAttribute('title');
        input.removeAttribute('data-stock-disponible');
    });
}

// Script para manejo dinámico de materiales y validaciones
document.addEventListener('DOMContentLoaded', function() {
    
    // Guardar valores iniciales para la funcionalidad de edición
    const almacenOrigen = document.getElementById('id_almacen_origen');
    const ubicacionOrigen = document.getElementById('id_ubicacion_origen');
    
    if (almacenOrigen) {
        almacenOrigenInicial = almacenOrigen.value;
    }
    if (ubicacionOrigen) {
        ubicacionOrigenInicial = ubicacionOrigen.value;
    }
    
    // Función para validar stock en tiempo real
    function validarStock(inputCantidad, inputDescripcion) {
        const cantidad = parseFloat(inputCantidad.value) || 0;
        const stock = parseFloat(inputCantidad.getAttribute('data-stock-disponible')) || 0;
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
        const stockElement = materialItem.querySelector('[id^="stock-disponible-"]') || 
                            materialItem.querySelector('.stock-disponible') ||
                            materialItem.querySelector('[data-stock]');
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
        const stockElement = materialItem.querySelector('[id^="stock-disponible-"]') || 
                            materialItem.querySelector('.stock-disponible') ||
                            materialItem.querySelector('[data-stock]');
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
    
    // MANEJAR CAMBIOS EN ORIGEN PARA EDICIÓN (ALERTA DE MATERIALES)
    if (almacenOrigen) {
        almacenOrigen.addEventListener('change', function() {
            const valorActual = this.value;
            
            if (valorActual !== almacenOrigenInicial && hayMaterialesSeleccionados()) {
                // Revertir temporalmente el cambio
                this.value = almacenOrigenInicial;
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: '¿Cambiar almacén de origen?',
                        html: 'Si cambia el <strong>almacén de origen</strong>, se eliminarán todos los materiales seleccionados y deberá volver a agregarlos.<br><br>¿Está seguro que desea continuar?',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, cambiar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Aplicar el cambio
                            this.value = valorActual;
                            almacenOrigenInicial = valorActual;
                            limpiarTodosMateriales();
                            
                            Swal.fire({
                                icon: 'info',
                                title: 'Materiales eliminados',
                                text: 'Los materiales han sido eliminados. Puede volver a seleccionarlos.',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        } else {
                            // Mantener el valor anterior
                            this.value = almacenOrigenInicial;
                        }
                    });
                } else {
                    // Fallback a confirm nativo si no hay SweetAlert
                    if (confirm('ADVERTENCIA: Si cambia el almacén de origen, se eliminarán todos los materiales seleccionados.\n\n¿Desea continuar?')) {
                        this.value = valorActual;
                        almacenOrigenInicial = valorActual;
                        limpiarTodosMateriales();
                        alert('Los materiales han sido eliminados. Puede volver a seleccionarlos.');
                    } else {
                        this.value = almacenOrigenInicial;
                    }
                }
            } else {
                almacenOrigenInicial = valorActual;
            }
            
            validarUbicaciones();
        });
    }
    
    if (ubicacionOrigen) {
        ubicacionOrigen.addEventListener('change', function() {
            const valorActual = this.value;
            
            if (valorActual !== ubicacionOrigenInicial && hayMaterialesSeleccionados()) {
                // Revertir temporalmente el cambio
                this.value = ubicacionOrigenInicial;
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: '¿Cambiar ubicación de origen?',
                        html: 'Si cambia la <strong>ubicación de origen</strong>, se eliminarán todos los materiales seleccionados y deberá volver a agregarlos.<br><br>¿Está seguro que desea continuar?',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, cambiar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Aplicar el cambio
                            this.value = valorActual;
                            ubicacionOrigenInicial = valorActual;
                            limpiarTodosMateriales();
                            
                            Swal.fire({
                                icon: 'info',
                                title: 'Materiales eliminados',
                                text: 'Los materiales han sido eliminados. Puede volver a seleccionarlos.',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        } else {
                            // Mantener el valor anterior
                            this.value = ubicacionOrigenInicial;
                        }
                    });
                } else {
                    // Fallback a confirm nativo si no hay SweetAlert
                    if (confirm('ADVERTENCIA: Si cambia la ubicación de origen, se eliminarán todos los materiales seleccionados.\n\n¿Desea continuar?')) {
                        this.value = valorActual;
                        ubicacionOrigenInicial = valorActual;
                        limpiarTodosMateriales();
                        alert('Los materiales han sido eliminados. Puede volver a seleccionarlos.');
                    } else {
                        this.value = ubicacionOrigenInicial;
                    }
                }
            } else {
                ubicacionOrigenInicial = valorActual;
            }
            
            validarUbicaciones();
        });
    }
    
    // Agregar nuevo material - CORREGIDO PARA EDICIÓN
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
                    //  MANTENER readonly en el campo de descripción
                    if (input.name === 'descripcion[]') {
                        input.setAttribute('readonly', 'readonly');
                    } else {
                        input.removeAttribute('readonly');
                    }
                    input.removeAttribute('title');
                    input.removeAttribute('data-stock-disponible');
                }
            });
            
            // Mostrar el botón eliminar
            const btnEliminar = nuevoMaterial.querySelector('.eliminar-material');
            if (btnEliminar) {
                btnEliminar.style.display = 'block';
            }
            
            contenedor.appendChild(nuevoMaterial);
            contadorMateriales++;
            
            // Actualizar eventos
            actualizarEventosEliminar();
            configurarEventosCantidad();
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
    
    // VALIDACIÓN DEL FORMULARIO ANTES DE ENVIAR
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            let errores = [];
            let tieneProductosSinStock = false;
            
            // VALIDACIÓN 1: Verificar que origen y destino no sean iguales
            const almacenOrigenVal = document.getElementById('id_almacen_origen').value;
            const ubicacionOrigenVal = document.getElementById('id_ubicacion_origen').value;
            const almacenDestinoVal = document.getElementById('id_almacen_destino').value;
            const ubicacionDestinoVal = document.getElementById('id_ubicacion_destino').value;
            
            if (almacenOrigenVal === almacenDestinoVal && ubicacionOrigenVal === ubicacionDestinoVal) {
                errores.push('No puede realizar una salida hacia la misma ubicación de origen. Seleccione un destino diferente.');
            }
            
            // VALIDACIÓN 2: Verificar tipo de material no sea "NA" (solo si existe el campo)
            const tipoMaterialElement = document.querySelector('input[name="id_material_tipo"]') || document.querySelector('select[name="id_material_tipo"]');
            if (tipoMaterialElement && tipoMaterialElement.value === '1') {
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
                const inputDescripcion = item.querySelector('input[name="descripcion[]"]');
                const inputIdProducto = item.querySelector('input[name="id_producto[]"]');
                
                // Solo validar si hay descripción Y id de producto (producto realmente seleccionado)
                if (inputDescripcion && inputDescripcion.value.trim() && 
                    inputIdProducto && inputIdProducto.value) {
                    
                    const stock = parseFloat(inputCantidad.getAttribute('data-stock-disponible')) || 0;
                    const cantidad = parseFloat(inputCantidad.value) || 0;
                    
                    // DEBUGGING: Verificar si el stock se está leyendo correctamente
                    console.log('Producto:', inputDescripcion.value, 'Stock:', stock, 'Cantidad:', cantidad, 'ID Producto:', inputIdProducto.value);
                    
                    // Verificar que tenga cantidad si tiene producto
                    if (cantidad <= 0) {
                        errores.push(`Debe ingresar una cantidad válida para "${inputDescripcion.value}"`);
                    }
                    // Solo validar stock si existe el atributo (para productos seleccionados desde modal)
                    else if (inputCantidad.hasAttribute('data-stock-disponible')) {
                        // Verificar stock cero
                        if (stock <= 0) {
                            errores.push(`El producto "${inputDescripcion.value}" no tiene stock disponible`);
                            tieneProductosSinStock = true;
                        }
                        // Verificar cantidad mayor a stock
                        else if (cantidad > stock) {
                            errores.push(`La cantidad de "${inputDescripcion.value}" (${cantidad}) excede el stock disponible (${stock.toFixed(2)})`);
                        }
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
                
                mostrarAlerta(icono, titulo, errores.join('\n\n'));
            }
        });
    }
    
    // Guardar cantidades originales para cálculos de stock en edición
    document.querySelectorAll('input[name="cantidad[]"]').forEach(input => {
        input.dataset.cantidadOriginal = input.value;
    });
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