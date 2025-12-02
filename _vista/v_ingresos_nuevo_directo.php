<?php
// Vista para crear ingreso directo - v_ingresos_nuevo_directo.php 
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Nuevo Ingreso Directo <small>Sin Orden de Compra</small></h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Información del Ingreso Directo</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br>
                        <form class="form-horizontal form-label-left" action="ingresos_directo_nuevo.php" method="post" id="form-ingreso-directo" enctype="multipart/form-data">
                            
                            
                            <!-- Información básica del ingreso -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Almacén <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="id_almacen" class="form-control" required>
                                        <option value="">Seleccionar Almacén</option>
                                        <?php
                                        if (!is_array($almacenes)) { $almacenes = []; }
                                        // 1. Ordenar: primero el id_almacen = 1
                                        usort($almacenes, function($a, $b) {
                                            if ($a['id_almacen'] == 1) return -1;
                                            if ($b['id_almacen'] == 1) return 1;
                                            return strcmp($a['nom_almacen'], $b['nom_almacen']); // resto se ordena alfabéticamente
                                        });
                                        ?>

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
                                        <option value="">Seleccionar Ubicación</option>
                                        <?php foreach ($ubicaciones as $ubicacion) { ?>
                                            <option value="<?php echo $ubicacion['id_ubicacion']; ?>">
                                                <?php echo $ubicacion['nom_ubicacion']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Personal que Registra:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo $usuario_sesion; ?>" readonly>
                                    <input type="hidden" name="id_personal" value="<?php echo $id; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Fecha de Ingreso:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo date('d/m/Y H:i:s'); ?>" readonly>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <!-- Sección de documentos adjuntos -->
                            <div class="x_title">
                                <h4>Documentos Adjuntos</h4>
                                <div class="clearfix"></div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Adjuntar Documentos <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="file" name="documento[]" class="form-control" multiple required
                                        accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                                    <small class="form-text text-muted">
                                        Formatos permitidos: PDF, JPG, PNG, DOC, XLS. Máximo 5MB por archivo.
                                    </small>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <!-- Sección de productos -->
                            <div class="x_title">
                                <h4>Productos a Ingresar</h4>
                                <div class="clearfix"></div>
                            </div>

                            <div id="contenedor-productos">
                                <div class="producto-item border p-3 mb-3" style="background-color: #f8f9fa;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Material <span class="text-danger">*</span>:</label>
                                            <div class="input-group">
                                                <input type="text" name="descripcion[]" class="form-control" placeholder="Buscar material..." required readonly>
                                                <div class="input-group-append">
                                                    <button onclick="buscarMaterial(this)" class="btn btn-secondary" type="button">
                                                        <i class="fa fa-search"></i> 
                                                    </button>
                                                </div>
                                            </div>
                                            <input type="hidden" name="id_producto[]" value="" required>
                                        </div>

                                        <div class="col-md-3">
                                            <label>Unidad:</label>
                                            <input type="text" name="unidad_display[]" class="form-control" readonly>
                                        </div>

                                        <div class="col-md-3">
                                            <label>Cantidad <span class="text-danger">*</span>:</label>
                                            <input type="number" name="cantidad[]" class="form-control" step="0.01" min="0.01" required>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12 d-flex justify-content-end">
                                            <button type="button" class="btn btn-danger btn-sm eliminar-producto" style="display: none;">
                                                <i class="fa fa-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="ln_solid"></div>

                            <div class="form-group">
                                <button type="button" id="agregar-producto" class="btn btn-info btn-sm">
                                    <i class="fa fa-plus"></i> Agregar Producto
                                </button>
                            </div>

                            <div class="ln_solid"></div>

                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 offset-md-8">
                                    <a href="ingresos_mostrar.php" class="btn btn-outline-danger btn-block">
                                        Cancelar
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <button type="submit" name="registrar" class="btn btn-success btn-block">
                                        <i></i> Registrar 
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12">
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

<!-- Modal para buscar materiales (específico para ingresos directos) -->
<div class="modal fade" id="buscar_material" tabindex="-1" role="dialog" aria-labelledby="modalBuscarMaterial">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Buscar Material para Ingreso Directo</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">                
                <div class="table-responsive">
                    <table id="datatable_material" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Tipo</th>
                                <th>Unidad de Medida</th>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
let currentSearchButton = null;
let contadorProductos = 1;

console.log("DEBUG: ubicaciones PHP →", <?php echo json_encode($ubicaciones); ?>);

const ubicacionesAll = <?php echo json_encode($ubicaciones); ?>;
console.log("DEBUG: ubicacionesAll JS →", ubicacionesAll);

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar eventos
    actualizarEventosEliminar();
    
    // Agregar nuevo producto
    document.getElementById('agregar-producto').addEventListener('click', function() {
        const contenedor = document.getElementById('contenedor-productos');
        const nuevoProducto = document.querySelector('.producto-item').cloneNode(true);
        
        // Limpiar valores
        const inputs = nuevoProducto.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            if (input.type !== 'button') {
                input.value = '';
            }
        });
        
        // Mostrar botón eliminar
        const btnEliminar = nuevoProducto.querySelector('.eliminar-producto');
        btnEliminar.style.display = 'block';
        
        contenedor.appendChild(nuevoProducto);
        contadorProductos++;
        
        actualizarEventosEliminar();
    });
    
    // Validación del formulario
    document.getElementById('form-ingreso-directo').addEventListener('submit', function(e) {
        const productosItems = document.querySelectorAll('.producto-item');
        let hayProductosValidos = false;
        let errores = [];
        
        // Validar almacén y ubicación
        const almacen = document.querySelector('select[name="id_almacen"]').value;
        const ubicacion = document.querySelector('select[name="id_ubicacion"]').value;
        
        if (!almacen) {
            errores.push('Debe seleccionar un almacén');
        }
        
        if (!ubicacion) {
            errores.push('Debe seleccionar una ubicación');
        }
        
        // Validar productos
        productosItems.forEach((item, index) => {
            const idProducto = item.querySelector('input[name="id_producto[]"]').value;
            const cantidad = item.querySelector('input[name="cantidad[]"]').value;
            
            if (idProducto && cantidad && parseFloat(cantidad) > 0) {
                hayProductosValidos = true;
            } else if (idProducto || cantidad) {
                errores.push(`Producto ${index + 1}: Complete todos los campos obligatorios`);
            }
        });
        
        if (!hayProductosValidos) {
            errores.push('Debe agregar al menos un producto válido');
        }
        
        if (errores.length > 0) {
            e.preventDefault();
            mostrarAlerta('error', 'Errores de Validación', errores.join('\n• '));
            return false;
        }
        
        return true;
    });

    const selectAlmacen = document.querySelector('select[name="id_almacen"]');
    const selectUbicacion = document.querySelector('select[name="id_ubicacion"]');

    console.log("DEBUG: selectAlmacen encontrado →", selectAlmacen);
    console.log("DEBUG: selectUbicacion encontrado →", selectUbicacion);

    selectAlmacen.addEventListener('change', function() {
        console.log("CAMBIO DE ALMACÉN → ", this.value);

        const almac = this.value;
        selectUbicacion.innerHTML = '<option value="">Seleccionar Ubicación</option>';

        if (almac == "1") {
            selectUbicacion.innerHTML += `<option value="1">Ubicación 1</option>`;
        } else {
            console.log("CARGANDO ubicacionesAll → ", ubicacionesAll);
            ubicacionesAll.forEach(u => {
                console.log("AGREGANDO UBICACION → ", u);
                selectUbicacion.innerHTML += `
                    <option value="${u.id_ubicacion}">
                        ${u.nom_ubicacion}
                    </option>
                `;
            });
        }
    });


});

(function() {
    try {
        console.log("DEBUG: inicio script de control de almacén/ubicación");

        // seguridad: la variable php debe imprimirse ya procesada
        const ubicacionesAll = <?php echo $ubicaciones_json ?? '[]'; ?>;
        console.log("DEBUG: ubicacionesAll (len):", Array.isArray(ubicacionesAll) ? ubicacionesAll.length : typeof ubicacionesAll, ubicacionesAll);

        // Esperar a que todo cargue (incluido Select2 y jQuery)
        window.addEventListener('load', init, { once: true });

        // fallback si load no es viable (double-check)
        setTimeout(function() {
            if (!window.__ingreso_init_run) init();
        }, 2000);

    } catch (err) {
        console.error("ERROR CRÍTICO al iniciar script:", err);
    }

    function init() {
        if (window.__ingreso_init_run) return;
        window.__ingreso_init_run = true;

        try {
            console.log("DEBUG: init ejecutado. jQuery =>", typeof window.jQuery, " $ =>", typeof window.$);

            const selAlmacen = document.querySelector('select[name="id_almacen"]');
            const selUbicacion = document.querySelector('select[name="id_ubicacion"]');

            console.log("DEBUG: selAlmacen:", !!selAlmacen, " selUbicacion:", !!selUbicacion);

            if (!selAlmacen || !selUbicacion) {
                console.warn("WARN: No se encontraron selects. Revisar nombres 'id_almacen' / 'id_ubicacion'.");
                return;
            }

            // función para poblar ubicaciones desde el array JS
            function poblarUbicaciones(array) {
                selUbicacion.innerHTML = '<option value="">Seleccionar Ubicación</option>';
                if (!Array.isArray(array) || array.length === 0) return;
                array.forEach(u => {
                    // proteger contra valores inesperados
                    const id = (u.id_ubicacion !== undefined) ? u.id_ubicacion : u.id;
                    const nombre = (u.nom_ubicacion !== undefined) ? u.nom_ubicacion : u.nombre || ('Ubic ' + id);
                    const opt = document.createElement('option');
                    opt.value = id;
                    opt.textContent = nombre;
                    selUbicacion.appendChild(opt);
                });
            }

            // bind nativo
            selAlmacen.addEventListener('change', cambioAlmacenHandler);

            // si jQuery y select2 existen, también escuchar con jQuery
            if (typeof window.jQuery !== 'undefined') {
                try {
                    const $ = window.jQuery;
                    $(selAlmacen).on('change', cambioAlmacenHandler);
                    // si Select2 está inicializado, escuchar evento específico
                    if ($.fn && $.fn.select2) {
                        $(selAlmacen).on('change.select2', cambioAlmacenHandler);
                    }
                    console.log("DEBUG: bind jQuery agregado al selectAlmacen");
                } catch (e) {
                    console.warn("WARN: error agregando binds jQuery:", e);
                }
            }

            // función handler
            function cambioAlmacenHandler(evt) {
                try {
                    const val = (evt && evt.target) ? evt.target.value : (this && this.value) || evt;
                    console.log("DEBUG: cambioAlmacenHandler -> valor seleccionado:", val);

                    // reset básico
                    selUbicacion.innerHTML = '<option value="">Seleccionar Ubicación</option>';

                    if (String(val) === "1") {
                        console.log("DEBUG: almacén 1 seleccionado → mostrar solo ubicación 1");
                        const opt = document.createElement('option');
                        opt.value = "1";
                        opt.textContent = "BASE";
                        selUbicacion.appendChild(opt);
                    } else {
                        console.log("DEBUG: almacén distinto de 1 → poblando todas las ubicaciones desde JS");
                        if (!ubicacionesAll || !Array.isArray(ubicacionesAll) || ubicacionesAll.length === 0) {
                            console.warn("WARN: ubicacionesAll vacío. No se agregan opciones.");
                            return;
                        }
                        poblarUbicaciones(ubicacionesAll);
                    }

                    // si Select2 está activo, refrescarlo via jQuery (si está presente)
                    if (typeof window.jQuery !== 'undefined' && window.jQuery.fn && window.jQuery.fn.select2) {
                        try {
                            window.jQuery(selUbicacion).trigger('change.select2'); // notifica select2
                            // si quieres forzar render: window.jQuery(selUbicacion).select2();
                        } catch (e) {
                            console.warn("WARN: error al notificar select2 del cambio:", e);
                        }
                    }
                } catch (err) {
                    console.error("ERROR en cambioAlmacenHandler:", err);
                }
            }

            // LOG: probar binder manual para debug (no borres)
            console.log("DEBUG: agregando test de disparo manual (no persistente).");
            // Para pruebas manuales en consola: window.triggerAlmacen('1') por ejemplo
            window.triggerAlmacen = function(v) {
                try {
                    const ev = new Event('change', { bubbles: true });
                    selAlmacen.value = v;
                    selAlmacen.dispatchEvent(ev);
                } catch (e) {
                    console.error(e);
                }
            };

            console.log("DEBUG: init finalizado correctamente.");

        } catch (errInit) {
            console.error("ERROR en init():", errInit);
        }

    } // end init

})();

function buscarMaterial(button) {
    // Validar que se haya seleccionado almacén y ubicación
    const almacen = document.querySelector('select[name="id_almacen"]').value;
    const ubicacion = document.querySelector('select[name="id_ubicacion"]').value;
    
    if (!almacen || !ubicacion) {
        Swal.fire({
            icon: 'warning',
            title: 'Datos requeridos',
            text: 'Debe seleccionar primero el almacén y la ubicación antes de buscar materiales.',
            confirmButtonText: 'Entendido'
        });
        return;
    }
    
    currentSearchButton = button;
    $('#buscar_material').modal('show');
    cargarMateriales();
}

function cargarMateriales() {
    if ($.fn.dataTable.isDataTable('#datatable_material')) {
        $('#datatable_material').DataTable().destroy();
    }

    // Obtener valores validados del formulario principal
    const idAlmacen = $('select[name="id_almacen"]').val();
    const idUbicacion = $('select[name="id_ubicacion"]').val();

    $('#datatable_material').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "ajax": {
            "url": "material_mostrar_modal_directo.php",
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
            { "title": "Unidad de Medida" },
            { "title": "Marca" },
            { "title": "Modelo" },
            { "title": "Stock Disponible" },
            { "title": "Acción" }
        ],
        "order": [[1, 'asc']],
        "pageLength": 10,
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron materiales",
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


function seleccionarProducto(id, nombre, unidad, stockActual) {
    if (currentSearchButton) {
        let productoItem = currentSearchButton.closest('.producto-item');
        
        if (productoItem) {
            productoItem.querySelector('input[name="descripcion[]"]').value = nombre;
            productoItem.querySelector('input[name="id_producto[]"]').value = id;
            productoItem.querySelector('input[name="unidad_display[]"]').value = unidad;            
            // NO establecer cantidad máxima ni placeholder para ingresos directos
            const cantidadInput = productoItem.querySelector('input[name="cantidad[]"]');
            cantidadInput.removeAttribute('max');
            cantidadInput.setAttribute('placeholder', 'Ingrese cantidad');
        }
    }
    
    $('#buscar_material').modal('hide');
    currentSearchButton = null;
    
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Material seleccionado',
            text: 'El material "' + nombre + '" ha sido seleccionado.',
            showConfirmButton: false,
            timer: 2000
        });
    }
}

function actualizarEventosEliminar() {
    document.querySelectorAll('.eliminar-producto').forEach(btn => {
        btn.onclick = function() {
            if (document.querySelectorAll('.producto-item').length > 1) {
                this.closest('.producto-item').remove();
            }
        };
    });
}

// Validación de tamaño de archivos
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action="ingresos_directo_nuevo.php"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            let archivosInvalidos = false;
            let mensajeError = '';
            const archivosInputs = form.querySelectorAll('input[type="file"][name="documento[]"]');
            
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
                return false;
            }
        });
    }
});
// Limpiar referencia cuando se cierre modal
$('#buscar_material').on('hidden.bs.modal', function () {
    currentSearchButton = null;
});

$(document).ready(function () {
    console.log("INIT SELECT2");

    /*$('select[name="id_almacen"]').select2({
        placeholder: "Seleccionar Almacén",
        allowClear: true,
        width: "100%"
    });*/

    $('select[name="id_ubicacion"]').select2({
        placeholder: "Seleccionar Ubicación",
        allowClear: true,
        width: "100%"
    });

    $('select[name="id_almacen"]').select2({
        width: "100%",
        templateResult: formatAlmacen,
        templateSelection: formatAlmacen,
        placeholder: "Seleccionar Almacén",
        allowClear: true,
    });
    
});

function formatAlmacen(almacen) {
    if (!almacen.id) {
        return almacen.text;
    }
    
    // Si es el almacén con id=1, aplicar estilos
    if (almacen.element.value == '1') {
        return $('<span class="special-option">' + almacen.text + '</span>');
    }
    
    return almacen.text;
}
</script>

<style>
    /* Normal (opción en reposo) */
    .select2-results__option .special-option {
        background-color: #d5f8d5;
        display: block;
        padding: 1px;
        border-radius: 3px;
    }

    /* Hover – cuando Select2 resalta la opción */
    .select2-results__option--highlighted .special-option {
        background-color: #5897fb !important; /* hover azul estándar */
        color: white !important;
    }
</style>