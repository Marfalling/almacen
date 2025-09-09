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
                                    <input type="date" name="fecha_necesidad" class="form-control" value="<?php echo $pedido['fec_req_pedido']; ?>" required>
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
                                            <input type="text" name="sst[]" class="form-control" value="<?php echo $sst; ?>" placeholder="SST/MA/CA" required>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <label>Adjuntar Archivos:</label>
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


<!-- Modal para buscar productos -->
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