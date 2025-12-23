<?php 
//=======================================================================
// VISTA: v_uso_material_editar.php
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Editar Uso de Material</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Datos del Uso de Material <small></small></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br>
                        <?php if (!empty($uso_material_data)) { 
                            $uso = $uso_material_data[0];
                        ?>
                        <form class="form-horizontal form-label-left" action="uso_material_editar.php?id=<?php echo $id_uso_material; ?>" method="post" enctype="multipart/form-data">
                            
                            <!-- Información básica -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Almacén:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo $uso['nom_almacen']; ?>" readonly>
                                    <input type="hidden" name="id_almacen" value="<?php echo $uso['id_almacen']; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Ubicación:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo $uso['nom_ubicacion']; ?>" readonly>
                                    <input type="hidden" name="id_ubicacion" value="<?php echo $uso['id_ubicacion']; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Solicitante <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="id_solicitante" id="id_solicitante" class="form-control" required>
                                        <option value="">Seleccionar</option>
                                        <?php foreach ($personal as $persona) { ?>
                                            <option value="<?php echo $persona['id_personal']; ?>"
                                                    data-centro-costo="<?php echo isset($persona['id_centro_costo']) ? $persona['id_centro_costo'] : ''; ?>"
                                                    data-centro-costo-nombre="<?php echo isset($persona['nom_centro_costo']) ? htmlspecialchars($persona['nom_centro_costo']) : 'Sin centro de costo asignado'; ?>"
                                                <?php echo ($uso['id_solicitante'] == $persona['id_personal']) ? 'selected' : ''; ?>>
                                                <?php echo $persona['nom_personal']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Centro de Costos (Solicitante):</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" id="texto_centro_costo_solicitante" class="form-control" 
                                        value="Seleccione un solicitante" 
                                        readonly style="background-color: #f9f9f9;">
                                    <input type="hidden" name="id_solicitante_centro_costo" id="id_solicitante_centro_costo" value="">
                                    <small class="text-muted" id="mensaje_sin_centro_solicitante" style="display: none;">
                                        El solicitante no tiene un centro de costo asignado.
                                    </small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Registrado por:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo $uso['nom_registrado']; ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Centro de Costos (Registrador):</label>
                                <div class="col-md-9 col-sm-9">
                                    <?php 
                                    // Buscar el nombre del centro de costo del registrador
                                    $nombre_centro_costo_registrador = 'Sin asignar';
                                    if (isset($uso['id_registrador_centro_costo']) && !empty($uso['id_registrador_centro_costo'])) {
                                        foreach ($centros_costo as $centro) {
                                            if ($centro['id_centro_costo'] == $uso['id_registrador_centro_costo']) {
                                                $nombre_centro_costo_registrador = $centro['nom_centro_costo'];
                                                break;
                                            }
                                        }
                                    }
                                    ?>
                                    <input type="text" class="form-control" 
                                        value="<?php echo htmlspecialchars($nombre_centro_costo_registrador); ?>" 
                                        readonly style="background-color: #f9f9f9;">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Fecha de Registro:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo date('d/m/Y H:i', strtotime($uso['fec_uso_material'])); ?>" readonly>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <!-- Sección de materiales -->
                            <div class="x_title">
                                <h4>Materiales utilizados <small></small></h4>
                                <div class="clearfix"></div>
                            </div>

                            <div id="contenedor-materiales">
                                <?php 
                                $contador = 0;
                                foreach ($uso_material_detalle as $detalle) { 
                                ?>
                                <div class="material-item border p-3 mb-3">
                                    <!-- FILA 1: Material, Unidad, Cantidad -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Material <span class="text-danger">*</span>:</label>
                                            <div class="input-group">
                                                <input type="text" name="descripcion[]" class="form-control" 
                                                    value="<?php echo $detalle['nom_producto']; ?>" readonly required>
                                                <input type="hidden" name="id_producto[]" value="<?php echo $detalle['id_producto']; ?>">
                                                <input type="hidden" name="id_detalle[]" value="<?php echo $detalle['id_uso_material_detalle']; ?>">
                                                <button onclick="buscarMaterial(this)" class="btn btn-secondary btn-xs" type="button" data-toggle="tooltip" title="Buscar Material">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <label>Unidad:</label>
                                            <input type="text" name="unidad[]" class="form-control" 
                                                value="<?php echo $detalle['nom_unidad_medida']; ?>" readonly>
                                        </div>

                                        <div class="col-md-3">
                                            <label>Cantidad <span class="text-danger">*</span>:</label>
                                            <input type="number" name="cantidad[]" class="form-control cantidad-material" 
                                                step="0.01" min="0.01"
                                                value="<?php echo $detalle['cant_uso_material_detalle']; ?>" 
                                                data-stock="<?php echo $detalle['cantidad_disponible_almacen']; ?>" 
                                                required>
                                            <small class="form-text text-muted">
                                                Stock disponible: <?php echo $detalle['cantidad_disponible_almacen']; ?>
                                            </small>
                                        </div>
                                    </div>

                                    <!-- FILA 2: Observaciones y Centros de Costo -->
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <label>Observaciones:</label>
                                            <input type="text" name="observaciones[]" class="form-control" 
                                                placeholder="Observaciones del uso"
                                                value="<?php echo $detalle['obs_uso_material_detalle']; ?>">
                                        </div>

                                        <div class="col-md-6">
                                            <label>Centros de Costo <span class="text-danger">*</span>:</label>
                                            <select name="centros_costo[<?php echo $contador; ?>][]" 
                                                    class="form-control select2-centros-costo-uso-material" 
                                                    multiple required>
                                                <?php 
                                                // Los centros ya vienen en $detalle['centros_costo_ids'] desde el controlador
                                                $centros_seleccionados = isset($detalle['centros_costo_ids']) ? $detalle['centros_costo_ids'] : array();
                                                
                                                foreach ($centros_costo as $centro) { 
                                                    $selected = in_array($centro['id_centro_costo'], $centros_seleccionados) ? 'selected' : '';
                                                ?>
                                                    <option value="<?php echo $centro['id_centro_costo']; ?>" <?php echo $selected; ?>>
                                                        <?php echo $centro['nom_centro_costo']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <small class="form-text text-muted">
                                                <i class="fa fa-info-circle"></i> Seleccione uno o más centros de costo para este material.
                                            </small>
                                        </div>
                                    </div>

                                    <!-- FILA 3: Adjuntar Evidencias (NUEVA FILA SEPARADA) -->
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <label>Adjuntar Evidencias:</label>
                                            <input type="file" name="archivos_<?php echo $contador; ?>[]" 
                                                class="form-control" multiple 
                                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                                            <small class="form-text text-muted">
                                                Formatos permitidos: PDF, JPG, PNG, DOC, XLS. Máximo 5MB por archivo.
                                            </small>
                                            
                                            <?php if (!empty($detalle['archivos'])) { 
                                                $archivos = explode(',', $detalle['archivos']);
                                            ?>
                                                <div class="mt-2">
                                                    <strong>Archivos existentes:</strong><br>
                                                    <?php foreach ($archivos as $archivo) {
                                                        if (!empty($archivo)) {
                                                            $archivo = trim($archivo);
                                                    ?>
                                                        <div class="d-flex align-items-center mb-1">
                                                            <a href="../_archivos/uso_material/<?php echo $archivo; ?>" 
                                                            target="_blank" 
                                                            class="btn btn-link btn-sm p-0 me-2">
                                                                <i class="fa fa-file"></i> <?php echo $archivo; ?>
                                                            </a>
                                                            <button type="button" 
                                                                    class="btn btn-danger btn-xs ms-2" 
                                                                    onclick="eliminarArchivo('<?php echo $archivo; ?>', <?php echo $detalle['id_uso_material_detalle']; ?>, this)" 
                                                                    title="Eliminar archivo">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    <?php 
                                                        }
                                                    } 
                                                    ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <!-- FILA 4: Botón Eliminar -->
                                    <div class="row mt-2">
                                        <div class="col-md-12 d-flex align-items-end justify-content-end">
                                            <button type="button" 
                                                    class="btn btn-danger btn-sm eliminar-material" 
                                                    <?php echo ($contador == 0) ? 'style="display: none;"' : ''; ?>>
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

                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 offset-md-8">
                                    <a href="uso_material_mostrar.php" class="btn btn-outline-danger btn-block">Cancelar</a>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <button type="submit" name="actualizar" id="btn_actualizar" class="btn btn-success btn-block">Actualizar</button>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12 col-sm-12">
                                    <p><span class="text-danger">*</span> Los campos con (<span class="text-danger">*</span>) son obligatorios.</p>
                                </div>
                            </div>
                        </form>
                        <?php } ?>
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
                <h4 class="modal-title" id="myModalLabel">Buscar Material</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Filtrar por Almacén y Ubicación:</label>
                    <select id="filtro_almacen_ubicacion" class="form-control">
                        <option value="">Seleccione almacén y ubicación para ver stock</option>
                    </select>
                </div>
                <div class="table-responsive">
                    <table id="datatable_producto" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Material</th>
                                <th>Tipo</th>
                                <th>Unidad</th>
                                <th>Stock Físico</th>
                                <th>Stock Reservado</th>
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

<!-- Agregar Select2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ============================================
    // ✅ DETECTAR CAMBIO EN SOLICITANTE Y MOSTRAR SU CENTRO DE COSTO
    // ============================================
    const selectSolicitante = document.getElementById('id_solicitante');
    const textoCentroCostoSolicitante = document.getElementById('texto_centro_costo_solicitante');
    const inputIdCentroCostoSolicitante = document.getElementById('id_solicitante_centro_costo');
    const mensajeSinCentro = document.getElementById('mensaje_sin_centro_solicitante');

    // ============================================
    // ✅ FUNCIÓN PARA APLICAR CENTRO DE COSTO A TODOS LOS MATERIALES
    // ============================================
    function aplicarCentroCostoATodosMateriales(idCentroCosto) {
        if (!idCentroCosto) return;
        
        console.log("✅ Aplicando centro de costo del solicitante a todos los materiales:", idCentroCosto);
        
        document.querySelectorAll('.material-item').forEach(item => {
            const selectCentros = item.querySelector('select.select2-centros-costo-uso-material');
            if (selectCentros) {
                if ($(selectCentros).data('select2')) {
                    // Select2 ya inicializado
                    $(selectCentros).val([idCentroCosto]).trigger('change');
                    console.log("✅ Centro aplicado a material existente");
                } else {
                    // Select2 no inicializado, inicializar primero
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
                        $(selectCentros).val([idCentroCosto]).trigger('change');
                        console.log("✅ Centro aplicado a material nuevo");
                    }, 100);
                }
            }
        });
    }

    if (selectSolicitante && textoCentroCostoSolicitante && inputIdCentroCostoSolicitante) {
        console.log('✅ Elementos del centro de costo encontrados');
        
        // Función para actualizar el centro de costo del solicitante
        function actualizarCentroCostoSolicitante() {
            const selectedOption = selectSolicitante.options[selectSolicitante.selectedIndex];
            
            if (!selectedOption) {
                console.log('❌ No hay opción seleccionada');
                return;
            }
            
            const idCentroCosto = selectedOption.getAttribute('data-centro-costo');
            const nombreCentroCosto = selectedOption.getAttribute('data-centro-costo-nombre');
            
            console.log('📊 Centro de costo:', {
                id: idCentroCosto,
                nombre: nombreCentroCosto
            });
            
            if (idCentroCosto && idCentroCosto !== '' && idCentroCosto !== 'null') {
                textoCentroCostoSolicitante.value = nombreCentroCosto || 'Sin nombre';
                inputIdCentroCostoSolicitante.value = idCentroCosto;
                if (mensajeSinCentro) mensajeSinCentro.style.display = 'none';
                console.log('✅ Centro de costo asignado');
                
                // ✅ APLICAR AUTOMÁTICAMENTE A TODOS LOS MATERIALES
                aplicarCentroCostoATodosMateriales(idCentroCosto);
            } else {
                textoCentroCostoSolicitante.value = 'Sin centro de costo asignado';
                inputIdCentroCostoSolicitante.value = '';
                if (mensajeSinCentro) mensajeSinCentro.style.display = 'block';
                console.log('⚠️ Sin centro de costo');
            }
        }
        
        // Evento change con Select2
        $(selectSolicitante).on('select2:select', function() {
            console.log('🔄 Select2 cambió');
            actualizarCentroCostoSolicitante();
        });
        
        // ✅ INICIALIZAR CON EL SOLICITANTE QUE YA ESTÁ SELECCIONADO
        setTimeout(function() {
            if (selectSolicitante.value && selectSolicitante.value !== '') {
                console.log('🎯 Inicializando con valor preseleccionado:', selectSolicitante.value);
                actualizarCentroCostoSolicitante();
            } else {
                console.log('⚠️ No hay valor preseleccionado');
            }
        }, 800); // Aumentado a 800ms para dar tiempo a que Select2 de materiales se inicialice
        
    } else {
        console.error('❌ No se encontraron elementos del centro de costo:', {
            selectSolicitante: !!selectSolicitante,
            textoCentroCostoSolicitante: !!textoCentroCostoSolicitante,
            inputIdCentroCostoSolicitante: !!inputIdCentroCostoSolicitante
        });
    }
    
    // ============================================
    // ✅ EXPONER LA FUNCIÓN GLOBALMENTE PARA USO EN AGREGAR MATERIAL
    // ============================================
    window.aplicarCentroCostoATodosMateriales = aplicarCentroCostoATodosMateriales;
});
</script>
<script>
// ============================================
// JAVASCRIPT COMPLETO PARA v_uso_material_editar.php
// ============================================

// Variables globales
let currentSearchButton = null;
let ubicacionActual = '<?php echo $uso['id_ubicacion']; ?>';
let formularioModificado = false;
let productosSeleccionados = [];

// Detectar cambios en el formulario
function marcarFormularioComoModificado() {
    formularioModificado = true;
}

// Verificar si hay productos seleccionados
function hayProductosSeleccionados() {
    const materialesItems = document.querySelectorAll('.material-item');
    for (let item of materialesItems) {
        const inputDescripcion = item.querySelector('input[name="descripcion[]"]');
        if (inputDescripcion && inputDescripcion.value.trim() !== '') {
            return true;
        }
    }
    return false;
}

// Limpiar productos nuevos (no originales del registro)
function limpiarProductosNuevos() {
    const materialesItems = document.querySelectorAll('.material-item');
    
    // Eliminar solo los items nuevos (que no tienen id_detalle o tienen id_detalle = 0)
    materialesItems.forEach((item) => {
        const inputIdDetalle = item.querySelector('input[name="id_detalle[]"]');
        if (inputIdDetalle && (inputIdDetalle.value === '' || inputIdDetalle.value === '0')) {
            item.remove();
        }
    });
    
    formularioModificado = false;
}

// Función para eliminar archivo
function eliminarArchivo(nombreArchivo, idDetalle, botonEliminar) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: '¿Eliminar archivo?',
            text: 'Esta acción no se puede deshacer. El archivo "' + nombreArchivo + '" será eliminado permanentemente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('eliminar_archivo_uso_material.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'nombre_archivo=' + encodeURIComponent(nombreArchivo) + 
                          '&id_detalle=' + idDetalle
                })
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === 'OK') {
                        botonEliminar.parentElement.remove();
                        
                        Swal.fire({
                            title: 'Archivo eliminado',
                            text: 'El archivo ha sido eliminado correctamente.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: 'No se pudo eliminar el archivo: ' + data,
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Error de conexión al eliminar el archivo.',
                        icon: 'error'
                    });
                });
            }
        });
    } else {
        if (confirm('¿Está seguro de eliminar el archivo "' + nombreArchivo + '"?')) {
            fetch('eliminar_archivo_uso_material.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'nombre_archivo=' + encodeURIComponent(nombreArchivo) + 
                      '&id_detalle=' + idDetalle
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === 'OK') {
                    botonEliminar.parentElement.remove();
                    alert('Archivo eliminado correctamente.');
                } else {
                    alert('Error al eliminar archivo: ' + data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión al eliminar el archivo.');
            });
        }
    }
}

// Función para buscar material
function buscarMaterial(button) {
    const inputAlmacen = document.querySelector('input[name="id_almacen"]');
    const hiddenUbicacion = document.querySelector('input[name="id_ubicacion"]');
    
    if (!hiddenUbicacion.value) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Error',
                text: 'No se pudo obtener la ubicación.',
                confirmButtonText: 'Entendido'
            });
        } else {
            alert('No se pudo obtener la ubicación.');
        }
        return;
    }
    
    currentSearchButton = button;
    
    const filtroSelect = document.getElementById('filtro_almacen_ubicacion');
    const almacenNombre = '<?php echo $uso['nom_almacen']; ?>';
    const ubicacionNombre = '<?php echo $uso['nom_ubicacion']; ?>';
    
    filtroSelect.innerHTML = `<option value="${inputAlmacen.value}_${hiddenUbicacion.value}">${almacenNombre} - ${ubicacionNombre}</option>`;
    filtroSelect.value = `${inputAlmacen.value}_${hiddenUbicacion.value}`;
    
    $('#buscar_producto').modal('show');
    cargarProductos(inputAlmacen.value, hiddenUbicacion.value);
}

// Función para cargar productos
function cargarProductos(idAlmacen, idUbicacion) {
    if ($.fn.dataTable.isDataTable('#datatable_producto')) {
        $('#datatable_producto').DataTable().destroy();
    }

    $('#datatable_producto').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "ajax": {
            "url": "uso_material_mostrar_modal.php",
            "type": "POST",
            "data": function(d) {
                d.id_almacen = idAlmacen;
                d.id_ubicacion = idUbicacion;
                d.solo_con_stock = true;
                return d;
            }
        },
        "columns": [
            { "title": "Código" },
            { "title": "Material" },
            { "title": "Tipo" },
            { "title": "Unidad" },
            { "title": "Stock Físico" },
            { "title": "Stock Reservado" },
            { "title": "Stock Disponible" },
            { "title": "Acción" }
        ],
        "order": [[1, 'asc']],
        "pageLength": 10,
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron materiales con stock disponible",
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
            "processing": "Procesando..."
        }
    });
}

// Función para seleccionar producto
function seleccionarProducto(idProducto, nombreProducto, unidadMedida, stockDisponible) {
    // Validación anti-duplicados
    const materialItems = document.querySelectorAll('.material-item');
    let productoExistente = null;

    materialItems.forEach(item => {
        const inputId = item.querySelector('input[name="id_producto[]"]');
        if (inputId && parseInt(inputId.value) === parseInt(idProducto)) {
            productoExistente = item;
        }
    });

    if (productoExistente) {
        productoExistente.classList.add('duplicado-resaltado');
        productoExistente.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(() => productoExistente.classList.remove('duplicado-resaltado'), 2000);
        $('#buscar_producto').modal('hide');
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Producto duplicado',
                text: 'Este material ya está agregado en el uso',
                showConfirmButton: false,
                timer: 2000
            });
        }
        return;
    }
    
    if (currentSearchButton) {
        let materialItem = currentSearchButton.closest('.material-item');
        
        if (materialItem) {
            let inputDescripcion = materialItem.querySelector('input[name="descripcion[]"]');
            let inputIdProducto = materialItem.querySelector('input[name="id_producto[]"]');
            let inputUnidad = materialItem.querySelector('input[name="unidad[]"]');
            let inputCantidad = materialItem.querySelector('input[name="cantidad[]"]');
            
            if (inputDescripcion) inputDescripcion.value = nombreProducto;
            if (inputIdProducto) inputIdProducto.value = idProducto;
            if (inputUnidad) inputUnidad.value = unidadMedida;
            if (inputCantidad) inputCantidad.setAttribute('data-stock', stockDisponible);
        }
    }
    
    $('#buscar_producto').modal('hide');
    currentSearchButton = null;
    
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Material seleccionado',
            text: 'El material "' + nombreProducto + '" ha sido seleccionado.',
            showConfirmButton: false,
            timer: 2000
        });
    }
}

// Verificar si hay productos nuevos
function hayProductosNuevos() {
    const materialesItems = document.querySelectorAll('.material-item');
    for (let item of materialesItems) {
        const inputIdDetalle = item.querySelector('input[name="id_detalle[]"]');
        if (inputIdDetalle && (inputIdDetalle.value === '' || inputIdDetalle.value === '0')) {
            const inputDescripcion = item.querySelector('input[name="descripcion[]"]');
            if (inputDescripcion && inputDescripcion.value.trim() !== '') {
                return true;
            }
        }
    }
    return false;
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    let contadorMateriales = <?php echo count($uso_material_detalle); ?>;
    
    // Inicializar Select2 para solicitante
    $('#id_solicitante').select2({
        placeholder: "Seleccione un solicitante",
        allowClear: true,
        width: '100%'
    });
    
    // Inicializar Select2 para centros de costo existentes
    $('.select2-centros-costo-uso-material').each(function() {
        $(this).select2({
            placeholder: 'Seleccionar uno o más centros de costo...',
            allowClear: true,
            width: '100%',
            multiple: true,
            language: {
                noResults: function () { return 'No se encontraron resultados'; }
            }
        });
    });
    
    // Detectar cambios en campos del formulario
    const todosLosCampos = document.querySelectorAll('input, textarea, select');
    todosLosCampos.forEach(campo => {
        campo.addEventListener('change', marcarFormularioComoModificado);
        campo.addEventListener('input', marcarFormularioComoModificado);
    });
    
    // Agregar nuevo material
    const btnAgregarMaterial = document.getElementById('agregar-material');
    if (btnAgregarMaterial) {
        btnAgregarMaterial.addEventListener('click', function(e) {
            e.preventDefault();
            
            const contenedor = document.getElementById('contenedor-materiales');
            const materialOriginal = contenedor.querySelector('.material-item');
            
            if (materialOriginal) {
                // 1. GUARDAR valores de Select2 ANTES de destruir
                const valoresOriginalesSelect2 = {};
                const selectsOriginales = materialOriginal.querySelectorAll(
                    'select.select2-centros-costo-uso-material'
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
                    if (input.type === 'file') {
                        input.value = '';
                        input.name = `archivos_${contadorMateriales}[]`;
                    } else if (input.name === 'id_detalle[]') {
                        input.value = '0';
                    } else if (input.name === 'id_producto[]') {
                        input.value = '';
                    } else if (input.name === 'descripcion[]') {
                        input.value = '';
                    } else if (input.name === 'unidad[]') {
                        input.value = '';
                    } else if (input.name === 'cantidad[]') {
                        input.value = '';
                        input.removeAttribute('data-stock');
                    } else if (input.name === 'observaciones[]') {
                        input.value = '';
                    } else {
                        input.value = '';
                    }
                });

                // 6. REMOVER SOLO la sección de "Archivos existentes" (NO el input file)
                // Buscamos el div que contiene SOLO los archivos existentes
                const filasArchivos = nuevoMaterial.querySelectorAll('.row.mt-2');
                filasArchivos.forEach(fila => {
                    // Dentro de cada fila, buscar divs con mt-2 que contengan "Archivos existentes"
                    const divsInternos = fila.querySelectorAll('div.mt-2');
                    divsInternos.forEach(div => {
                        const strong = div.querySelector('strong');
                        if (strong && strong.textContent.trim() === 'Archivos existentes:') {
                            // SOLO eliminar este div interno, NO toda la fila
                            div.remove();
                        }
                    });
                });
                
                // Limpiar el texto del small de stock disponible
                const smallsStock = nuevoMaterial.querySelectorAll('small.form-text.text-muted');
                smallsStock.forEach(small => {
                    if (small.textContent.includes('Stock disponible:')) {
                        small.textContent = 'Stock disponible: ';
                    }
                });

                // 7. LIMPIAR y PREPARAR los selects del NUEVO material
                const selectsClonados = nuevoMaterial.querySelectorAll('select');
                selectsClonados.forEach(select => {
                    if (select.name && select.name.includes('centros_costo')) {
                        select.name = `centros_costo[${contadorMateriales}][]`;
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

                // 8. MOSTRAR botón eliminar
                const btnEliminar = nuevoMaterial.querySelector('.eliminar-material');
                if (btnEliminar) {
                    btnEliminar.style.display = 'block';
                }
                
                // 9. AGREGAR al contenedor
                contenedor.appendChild(nuevoMaterial);
                
                // 10. INICIALIZAR Select2 en el NUEVO material
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
                        
                        //  APLICAR AUTOMÁTICAMENTE EL CENTRO DE COSTO DEL SOLICITANTE
                        const inputIdCentroCostoSolicitante = document.getElementById('id_solicitante_centro_costo');
                        if (inputIdCentroCostoSolicitante && inputIdCentroCostoSolicitante.value) {
                            setTimeout(() => {
                                $(select).val([inputIdCentroCostoSolicitante.value]).trigger('change');
                                console.log('✅ Centro de costo aplicado automáticamente al nuevo material');
                            }, 100);
                        }
                    }
                });
                
                // 11. INCREMENTAR contador y actualizar eventos
                contadorMateriales++;
                actualizarEventosEliminar();
                actualizarEventosCampos();
                formularioModificado = true;
                
                // 12. SCROLL al nuevo elemento
                nuevoMaterial.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                
                console.log('✅ Nuevo material agregado, contador:', contadorMateriales);
            }
        });
    }
    
    function actualizarEventosEliminar() {
        document.querySelectorAll('.eliminar-material').forEach(btn => {
            btn.onclick = function(e) {
                e.preventDefault();
                if (document.querySelectorAll('.material-item').length > 1) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '¿Eliminar material?',
                            text: 'Se eliminará este material del registro',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.closest('.material-item').remove();
                            }
                        });
                    } else {
                        if (confirm('¿Eliminar este material?')) {
                            this.closest('.material-item').remove();
                        }
                    }
                }
            };
        });
    }
    
    function actualizarEventosCampos() {
        const todosLosCamposActualizados = document.querySelectorAll('input, textarea, select');
        todosLosCamposActualizados.forEach(campo => {
            campo.removeEventListener('change', marcarFormularioComoModificado);
            campo.removeEventListener('input', marcarFormularioComoModificado);
            campo.addEventListener('change', marcarFormularioComoModificado);
            campo.addEventListener('input', marcarFormularioComoModificado);
        });
    }
    
    actualizarEventosEliminar();
    
    // Validación antes del envío
    document.querySelector('form').addEventListener('submit', function(e) {
        const materialesConDatos = document.querySelectorAll('.material-item input[name="descripcion[]"]');
        let hayMaterialesValidos = false;
        
        materialesConDatos.forEach(input => {
            if (input.value.trim() !== '') {
                hayMaterialesValidos = true;
            }
        });
        
        if (!hayMaterialesValidos) {
            e.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de validación',
                    text: 'Debe seleccionar al menos un material.'
                });
            } else {
                alert('Debe seleccionar al menos un material.');
            }
            return;
        }
        
        // Validación de stock
        let excedeStock = false;
        let mensajeStock = '';

        const cantidades = document.querySelectorAll('input[name="cantidad[]"]');
        cantidades.forEach((input, index) => {
            const stock = parseFloat(input.dataset.stock) || 0;
            const valor = parseFloat(input.value) || 0;

            if (valor > stock && stock > 0) {
                excedeStock = true;
                mensajeStock = `La cantidad del material #${index + 1} excede el stock disponible (${stock.toFixed(2)}).`;
            }
        });

        if (excedeStock) {
            e.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Stock insuficiente',
                    text: mensajeStock
                });
            } else {
                alert(mensajeStock);
            }
        }
    });
    
    // Validación en tiempo real de cantidad vs stock
    document.addEventListener('input', function(e) {
        if (e.target && e.target.name === 'cantidad[]' && e.target.classList.contains('cantidad-material')) {
            const stock = parseFloat(e.target.dataset.stock) || 0;
            const valor = parseFloat(e.target.value) || 0;

            if (valor > stock && stock > 0) {
                e.target.classList.add('border', 'border-danger');
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cantidad excede stock disponible',
                        text: `Stock disponible: ${stock.toFixed(2)}, cantidad ingresada: ${valor.toFixed(2)}`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            } else {
                e.target.classList.remove('border', 'border-danger');
            }
        }
    });
    
    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip();
});

// Limpiar referencia al cerrar modal
$('#buscar_producto').on('hidden.bs.modal', function () {
    currentSearchButton = null;
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Seleccionamos todos los inputs de cantidad
    const cantidadInputs = document.querySelectorAll('.cantidad-material');

    cantidadInputs.forEach(input => {
        input.addEventListener('input', function() {
            const stock = parseFloat(this.getAttribute('data-stock')) || 0;
            const valor = parseFloat(this.value) || 0;

            if (valor > stock) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cantidad excede stock disponible',
                        text: `Stock disponible: ${stock}, cantidad ingresada: ${valor}`,
                        timer: 2500,
                        showConfirmButton: false
                    });
                } else {
                    alert(`Cantidad excede stock disponible: ${stock}`);
                }
            }
        });
    });
});
</script>

<script>
// Validación inmediata de cantidad vs stock
document.addEventListener('DOMContentLoaded', function() {
    const cantidadInputs = document.querySelectorAll('.cantidad-material');

    cantidadInputs.forEach(input => {
        input.addEventListener('input', function() {
            const stock = parseFloat(this.dataset.stock);
            const valor = parseFloat(this.value);

            if (!isNaN(valor) && valor > stock) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cantidad excede stock disponible',
                        text: `Stock disponible: ${stock.toFixed(2)}, cantidad ingresada: ${valor.toFixed(2)}`,
                        confirmButtonText: 'Entendido'
                    });
                } else {
                    alert(`Cantidad ingresada (${valor}) supera el stock disponible (${stock})`);
                }
                this.classList.add('border border-danger'); // resalta el input
            } else {
                this.classList.remove('border', 'border-danger');
            }
        });
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