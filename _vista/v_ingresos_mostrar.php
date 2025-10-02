<?php 
//=======================================================================
// VISTA: v_ingresos_mostrar.php - CON TAB POR DEFECTO Y FORMATO CORREGIDO
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Gestión de Ingresos <small>Órdenes de Compra e Ingresos Directos</small></h3>
            </div>
            <div class="title_right">
                <div class="pull-right">
                    <a href="ingresos_directo_nuevo.php" class="btn btn-success">
                        <i class="fa fa-plus"></i> Nuevo Ingreso Directo
                    </a>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Control de Ingresos</h2>
                        <div class="clearfix"></div>
                    </div>

                    

                    <div class="x_content">
                        <!-- Filtro de fechas -->
                        <form method="get" action="ingresos_mostrar.php" class="form-inline mb-3">
                            <label for="fecha_inicio" class="mr-2">Desde:</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control mr-2"
                                value="<?php echo htmlspecialchars($fecha_inicio ?? date('Y-m-d')); ?>">

                            <label for="fecha_fin" class="mr-2">Hasta:</label>
                            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control mr-2"
                                value="<?php echo htmlspecialchars($fecha_fin ?? date('Y-m-d')); ?>">

                            <button type="submit" class="btn btn-primary">Consultar</button>
                        </form>

                        <!-- Nav tabs -->   

                          <ul class="nav nav-tabs" role="tablist">

                          
                            <li role="presentation" class="active">
                                <a href="#todos-ingresos" aria-controls="todos-ingresos" role="tab" data-toggle="tab">
                                    <i class="fa fa-list"></i> Todos los Ingresos
                                    <span class="badge badge-primary badge_size"><?php echo count($ingresos); ?></span>
                                </a>
                            </li>

                      
                            <li role="presentation">
                                <a href="#ordenes-compra" aria-controls="ordenes-compra" role="tab" data-toggle="tab">
                                    <i class="fa fa-shopping-cart"></i> Órdenes de Compra 
                                    <span class="badge badge-warning badge_size"><?php echo count(array_filter($ingresos, function($ing) { return $ing['tipo'] == 'COMPRA' || !isset($ing['tipo']); })); ?></span>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#ingresos-directos" aria-controls="ingresos-directos" role="tab" data-toggle="tab">
                                    <i class="fa fa-plus-circle"></i> Ingresos Directos
                                    <span class="badge badge-info badge_size"><?php echo count(array_filter($ingresos, function($ing) { return isset($ing['tipo']) && $ing['tipo'] == 'DIRECTO'; })); ?></span>
                                </a>
                            </li>
                            <!-- CAMBIO: Este tab ahora es el activo por defecto -->

                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content" style="margin-top: 15px;">
                            <!-- TAB 1: ÓRDENES DE COMPRA -->
                            <!-- CAMBIO: Ya no es activo por defecto -->
                            <div role="tabpanel" class="tab-pane" id="ordenes-compra">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="card-box table-responsive">
                                            <table id="datatable-compras" class="table table-striped table-bordered" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>N° Orden</th>
                                                        <th>Código Pedido</th>
                                                        <th>Proveedor</th>
                                                        <th>Almacén</th>
                                                        <th>Ubicación</th>
                                                        <th>Fecha Registro</th>
                                                        <th>Registrado Por</th>
                                                        <th>Aprobado Por</th>
                                                        <th>Estado</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $contador = 1;
                                                    foreach ($ingresos as $ingreso) {
                                                        // Solo mostrar órdenes de compra (tipo COMPRA o sin tipo definido para compatibilidad)
                                                        if (isset($ingreso['tipo']) && $ingreso['tipo'] != 'COMPRA') continue;
                                                        
                                                        // Calcular progreso para determinar estado
                                                        $porcentaje = $ingreso['total_productos'] > 0 ? 
                                                            round(($ingreso['productos_ingresados'] / $ingreso['total_productos']) * 100) : 0;
                                                        
                                                        // Compatibilidad con estructura original
                                                        $id_compra = isset($ingreso['id_compra']) ? $ingreso['id_compra'] : (isset($ingreso['id_orden']) ? $ingreso['id_orden'] : '');
                                                        $est_compra = isset($ingreso['est_compra']) ? $ingreso['est_compra'] : (isset($ingreso['estado']) ? $ingreso['estado'] : 0);
                                                        $fec_compra = isset($ingreso['fec_compra']) ? $ingreso['fec_compra'] : (isset($ingreso['fecha']) ? $ingreso['fecha'] : '');
                                                        $nom_proveedor = isset($ingreso['nom_proveedor']) ? $ingreso['nom_proveedor'] : (isset($ingreso['origen']) ? $ingreso['origen'] : '');
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $contador; ?></td>
                                                            <td><strong>OC-<?php echo $id_compra; ?></strong></td>
                                                            <td>
                                                                <?php if (isset($ingreso['id_pedido']) && isset($ingreso['cod_pedido'])) { ?>
                                                                <a class="btn btn-sm btn-outline-secondary" target="_blank" href="pedido_pdf.php?id=<?php echo $ingreso['id_pedido']; ?>">
                                                                    <?php echo $ingreso['cod_pedido']; ?>
                                                                </a>
                                                                <?php } else { ?>
                                                                    N/A
                                                                <?php } ?>
                                                            </td>
                                                            <td><?php echo $nom_proveedor; ?></td>
                                                            <td><?php echo $ingreso['nom_almacen']; ?></td>
                                                            <td><?php echo $ingreso['nom_ubicacion']; ?></td>
                                                            <td><?php echo date('d/m/Y H:i', strtotime($fec_compra)); ?></td>
                                                            <td><?php echo $ingreso['registrado_por'] ?? 'No especificado'; ?></td>
                                                            <td><?php echo $ingreso['aprobado_por'] ?? 'Pendiente'; ?></td>
                                                            <td>
                                                                <?php if ($est_compra == 3) { ?>
                                                                    <span class="badge badge-success badge_size">Completado</span>
                                                                <?php } elseif ($ingreso['productos_ingresados'] > 0) { ?>
                                                                    <span class="badge badge-warning badge_size">Parcial (<?php echo $ingreso['productos_ingresados']; ?>/<?php echo $ingreso['total_productos']; ?>)</span>
                                                                <?php } else { ?>
                                                                    <span class="badge badge-warning badge_size">Pendiente</span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex flex-wrap gap-2">
                                                                    <?php if ($est_compra != 3) { ?>
                                                                    <!-- Botón Verificar - solo visible si NO está completado (estado != 3) --> 
                                                                    <a href="ingresos_verificar.php?id_compra=<?php echo $id_compra; ?>" 
                                                                       class="btn btn-success btn-sm"
                                                                       title="Verificar ingreso">
                                                                        <i class="fa fa-check"></i>
                                                                    </a>
                                                                    <?php } ?>
                                                                    <!-- Botón Ingresos - siempre visible -->
                                                                    <a href="ingresos_detalle.php?id_compra=<?php echo $id_compra; ?>" 
                                                                        class="btn btn-secondary btn-sm"
                                                                        title="Ver ingresos">
                                                                        <i class="fa fa-plus"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php 
                                                        $contador++;
                                                    } 
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 2: INGRESOS DIRECTOS -->
                            <div role="tabpanel" class="tab-pane" id="ingresos-directos">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="card-box table-responsive">
                                            <table id="datatable-directos" class="table table-striped table-bordered" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>N° Ingreso</th>
                                                        <th>Tipo</th>
                                                        <th>Almacén</th>
                                                        <th>Ubicación</th>
                                                        <th>Fecha</th>
                                                        <th>Registrado Por</th>
                                                        <th>Productos</th>
                                                        <th>Estado</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $contador = 1;
                                                    foreach ($ingresos as $ingreso) {
                                                        if (!isset($ingreso['tipo']) || $ingreso['tipo'] != 'DIRECTO') continue;
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $contador; ?></td>
                                                            <td><strong>ING-<?php echo $ingreso['id_ingreso']; ?></strong></td>
                                                            <td>
                                                                <span class="badge badge-info badge_size">DIRECTO</span>
                                                            </td>
                                                            <td><?php echo $ingreso['nom_almacen']; ?></td>
                                                            <td><?php echo $ingreso['nom_ubicacion']; ?></td>
                                                            <td><?php echo date('d/m/Y H:i', strtotime($ingreso['fecha'])); ?></td>
                                                            <td><?php echo $ingreso['registrado_por'] ?? 'No especificado'; ?></td>
                                                            <td>
                                                                <span class="badge badge-primary badge_size"><?php echo $ingreso['total_productos']; ?></span>
                                                            </td>
                                                            <td>
                                                                <?php if ($ingreso['estado'] == 0) { ?>
                                                                    <span class="badge badge-danger badge_size">Anulado</span>
                                                                <?php } else { ?>
                                                                    <span class="badge badge-success badge_size">Registrado</span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex flex-wrap gap-2">
                                                                    <a href="ingresos_detalle_directo.php?id_ingreso=<?php echo $ingreso['id_ingreso']; ?>" 
                                                                        class="btn btn-secondary btn-sm"
                                                                        title="Ver ingresos">
                                                                        <i class="fa fa-plus"></i>
                                                                    </a>
                                                                    <?php if ($ingreso['estado'] == 1) { ?>
                                                                        <button onclick="anularIngresoDirecto(<?php echo $ingreso['id_ingreso']; ?>)" 
                                                                            class="btn btn-danger btn-sm"
                                                                            title="Anular ingreso">
                                                                            <i class="fa fa-times"></i>
                                                                        </button>
                                                                    <?php } else { ?>
                                                                        <span class="btn btn-outline-secondary btn-sm disabled" title="Ya anulado">
                                                                            <i class="fa fa-ban"></i> Anulado
                                                                        </span>
                                                                    <?php } ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php 
                                                        $contador++;
                                                    } 
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 3: TODOS LOS INGRESOS -->
                            <!-- CAMBIO: Ahora este es el tab activo por defecto -->
                            <div role="tabpanel" class="tab-pane active" id="todos-ingresos">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="card-box table-responsive">
                                            <table id="datatable-todos" class="table table-striped table-bordered" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Tipo</th>
                                                        <th>N° Documento</th>
                                                        <th>Origen/Proveedor</th>
                                                        <th>Almacén</th>
                                                        <th>Ubicación</th>
                                                        <th>Fecha</th>
                                                        <th>Estado</th>
                                                        <th>Productos</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $contador = 1;
                                                    foreach ($ingresos as $ingreso) {
                                                        $tipo = isset($ingreso['tipo']) ? $ingreso['tipo'] : 'COMPRA';
                                                        $id_compra = isset($ingreso['id_compra']) ? $ingreso['id_compra'] : (isset($ingreso['id_orden']) ? $ingreso['id_orden'] : '');
                                                        $est_compra = isset($ingreso['est_compra']) ? $ingreso['est_compra'] : (isset($ingreso['estado']) ? $ingreso['estado'] : 0);
                                                        $fec_compra = isset($ingreso['fec_compra']) ? $ingreso['fec_compra'] : (isset($ingreso['fecha']) ? $ingreso['fecha'] : '');
                                                        $nom_proveedor = isset($ingreso['nom_proveedor']) ? $ingreso['nom_proveedor'] : (isset($ingreso['origen']) ? $ingreso['origen'] : '');
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $contador; ?></td>
                                                            <td>
                                                                <?php if ($tipo == 'COMPRA') { ?>
                                                                    <span class="badge badge-warning badge_size">COMPRA</span>
                                                                <?php } else { ?>
                                                                    <span class="badge badge-info badge_size">DIRECTO</span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($tipo == 'COMPRA') { ?>
                                                                    <strong>OC-<?php echo $id_compra; ?></strong>
                                                                    <?php if (isset($ingreso['id_ingreso'])): ?>
                                                                        <br><small class="text-muted">(ING-<?php echo $ingreso['id_ingreso']; ?>)</small>
                                                                    <?php endif; ?>
                                                                <?php } else { ?>
                                                                    <strong>ING-<?php echo $ingreso['id_ingreso']; ?></strong>
                                                                <?php } ?>
                                                            </td>
                                                            <td><?php echo $nom_proveedor; ?></td>
                                                            <td><?php echo $ingreso['nom_almacen']; ?></td>
                                                            <td><?php echo $ingreso['nom_ubicacion']; ?></td>
                                                            <td><?php echo date('d/m/Y H:i', strtotime($fec_compra)); ?></td>
                                                            <td>
                                                                <?php if ($tipo == 'COMPRA') { ?>
                                                                    <?php if ($est_compra == 3) { ?>
                                                                        <span class="badge badge-success badge_size">Completado</span>
                                                                    <?php } elseif ($ingreso['productos_ingresados'] > 0) { ?>
                                                                        <span class="badge badge-warning badge_size">Parcial</span>
                                                                    <?php } else { ?>
                                                                        <span class="badge badge-warning badge_size">Pendiente</span>
                                                                    <?php } ?>
                                                                <?php } else { ?>
                                                                     <?php if ($ingreso['estado'] == 0) { ?>
                                                                        <span class="badge badge-danger badge_size">Anulado</span>
                                                                    <?php } else { ?>
                                                                        <!-- CAMBIO: Formato correcto con mayúscula inicial -->
                                                                        <span class="badge badge-success badge_size">Registrado</span>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-primary badge_size"><?php echo $ingreso['total_productos']; ?></span>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex flex-wrap gap-2">
                                                                    <?php if ($tipo == 'COMPRA') { ?>
                                                                        <?php if ($est_compra != 3) { ?>
                                                                        <a href="ingresos_verificar.php?id_compra=<?php echo $id_compra; ?>" 
                                                                           class="btn btn-success btn-sm" title="Verificar">
                                                                            <i class="fa fa-check"></i>
                                                                        </a>
                                                                        <?php } ?>
                                                                        <a href="ingresos_detalle.php?id_compra=<?php echo $id_compra; ?>" 
                                                                            class="btn btn-secondary btn-sm" title="Ver detalles">
                                                                            <i class="fa fa-plus"></i>
                                                                        </a>
                                                                    <?php } else { ?>
                                                                        <a href="ingresos_detalle_directo.php?id_ingreso=<?php echo $ingreso['id_ingreso']; ?>" 
                                                                            class="btn btn-secondary btn-sm" title="Ver detalles">
                                                                            <i class="fa fa-plus"></i>
                                                                        </a>
                                                                        <?php if ($ingreso['estado'] == 1) { ?>
                                                                            <button onclick="anularIngresoDirecto(<?php echo $ingreso['id_ingreso']; ?>)" 
                                                                                class="btn btn-danger btn-sm" title="Anular ingreso">
                                                                                <i class="fa fa-times"></i>
                                                                            </button>
                                                                        <?php } else { ?>
                                                                            <span class="btn btn-outline-secondary btn-sm disabled" title="Ya anulado">
                                                                                <i class="fa fa-ban"></i>
                                                                            </span>
                                                                        <?php } ?>
                                                                    <?php } ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php 
                                                        $contador++;
                                                    } 
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->
<script>
// Función para anular ingreso directo - definida globalmente
function anularIngresoDirecto(idIngreso) {
    console.log('Intentando anular ingreso:', idIngreso);
    
    // Verificar si SweetAlert2 está disponible
    if (typeof Swal !== 'undefined' && typeof confirmarAccion === 'function') {
        confirmarAccion(
            '¿Confirmar Anulación?', 
            '¿Está seguro que desea anular este ingreso directo? Esta acción solo se puede realizar si todos los productos aún están disponibles en stock.',
            function() {
                procesarAnulacion(idIngreso);
            }
        );
    } else {
        // Fallback con confirm nativo
        if (confirm('¿Está seguro que desea anular este ingreso directo?')) {
            procesarAnulacion(idIngreso);
        }
    }
}

// Función que procesa la anulación
function procesarAnulacion(idIngreso) {
    // Verificar que jQuery esté disponible
    if (typeof jQuery === 'undefined') {
        alert('Error: jQuery no está cargado');
        return;
    }
    
    // Mostrar loading si SweetAlert2 está disponible
    if (typeof mostrarCargando === 'function') {
        mostrarCargando('Verificando disponibilidad...');
    }
    
    jQuery.ajax({
        url: '../_controlador/ingresos_anular_directo.php',
        type: 'POST',
        data: { id_ingreso: idIngreso },
        dataType: 'json',
        beforeSend: function() {
            console.log('Enviando petición para anular ingreso:', idIngreso);
        },
        success: function(response) {
            console.log('Respuesta del servidor:', response);
            
            // Cerrar loading si está disponible
            if (typeof cerrarCargando === 'function') {
                cerrarCargando();
            }
            
            if (response.tipo_mensaje === 'success') {
                if (typeof mostrarAlerta === 'function') {
                    mostrarAlerta('success', '¡Anulación Exitosa!', response.mensaje, function() {
                        location.reload();
                    });
                } else {
                    alert('Éxito: ' + response.mensaje);
                    location.reload();
                }
            } else {
                if (typeof mostrarAlerta === 'function') {
                    mostrarAlerta('error', 'Error al Anular', response.mensaje);
                } else {
                    alert('Error: ' + response.mensaje);
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX:', {
                status: status,
                error: error,
                responseText: xhr.responseText
            });
            
            // Cerrar loading si está disponible
            if (typeof cerrarCargando === 'function') {
                cerrarCargando();
            }
            
            if (typeof mostrarAlerta === 'function') {
                mostrarAlerta('error', 'Error de Conexión', 'No se pudo procesar la solicitud. Error: ' + error);
            } else {
                alert('Error de conexión: ' + error);
            }
        }
    });
}

// Esperar a que jQuery y el DOM estén listos
document.addEventListener('DOMContentLoaded', function() {
    // DETECTAR SI SE REGISTRÓ UN INGRESO DIRECTO EXITOSAMENTE (PRIMERO)
    const urlParams = new URLSearchParams(window.location.search);
    const registradoDirecto = urlParams.get('registrado_directo');
    const idIngreso = urlParams.get('id_ingreso');
    const tab = urlParams.get('tab');
    
    // Si se registró un ingreso directo exitosamente
    if (registradoDirecto === 'true' && idIngreso) {
        console.log('Detectado registro exitoso de ingreso:', idIngreso);
        
        // Mostrar alerta de éxito
        if (typeof mostrarAlerta === 'function') {
            mostrarAlerta('success', '¡Registro Exitoso!', 
                'El ingreso directo ING-' + idIngreso + ' ha sido registrado correctamente.');
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: '¡Registro Exitoso!',
                text: 'El ingreso directo ING-' + idIngreso + ' ha sido registrado correctamente.',
                confirmButtonText: 'Aceptar'
            });
        } else {
            alert('¡Registro Exitoso! El ingreso directo ING-' + idIngreso + ' ha sido registrado correctamente.');
        }
        
        // Activar el tab correcto si se especificó
        if (tab) {
            setTimeout(function() {
                // Remover clase active de todos los tabs
                document.querySelectorAll('.nav-tabs li').forEach(function(li) {
                    li.classList.remove('active');
                });
                document.querySelectorAll('.tab-pane').forEach(function(pane) {
                    pane.classList.remove('active');
                });
                
                // Activar el tab especificado
                const targetTab = document.querySelector('a[href="#' + tab + '"]');
                if (targetTab) {
                    targetTab.closest('li').classList.add('active');
                    const targetPane = document.getElementById(tab);
                    if (targetPane) {
                        targetPane.classList.add('active');
                    }
                }
            }, 100);
        }
        
        // Limpiar la URL para evitar mostrar la alerta al recargar
        const cleanUrl = window.location.origin + window.location.pathname;
        window.history.replaceState({}, document.title, cleanUrl + (tab ? '?tab=' + tab : ''));
    }
    
    // Verificar si jQuery está cargado
    if (typeof jQuery === 'undefined') {
        console.error('jQuery no está cargado');
        return;
    }
    
    jQuery(document).ready(function($) {
        console.log('jQuery cargado correctamente');
        
        // Configuración base para DataTables
        var datatableConfig = {
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": "No se encontraron registros",
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
            },
            "pageLength": 25,
            "responsive": true
        };

        // Inicializar DataTables solo si existen las tablas
        if ($('#datatable-compras').length) {
            $('#datatable-compras').DataTable($.extend({}, datatableConfig, {
                "order": [[ 6, 'desc' ]]
            }));
        }

        if ($('#datatable-directos').length) {
            $('#datatable-directos').DataTable($.extend({}, datatableConfig, {
                "order": [[ 5, 'desc' ]]
            }));
        }

        if ($('#datatable-todos').length) {
            $('#datatable-todos').DataTable($.extend({}, datatableConfig, {
                "order": [[ 6, 'desc' ]]
            }));
        }

        // Manejar cambio de tabs para ajustar DataTables
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            if ($.fn.dataTable) {
                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
            }
        });
    });
});
</script>