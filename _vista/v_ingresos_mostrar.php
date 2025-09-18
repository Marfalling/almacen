<?php 
//=======================================================================
// VISTA: v_ingresos_mostrar.php - MEJORADA CON DIFERENCIACIÓN CLARA
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
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
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
                            <li role="presentation">
                                <a href="#todos-ingresos" aria-controls="todos-ingresos" role="tab" data-toggle="tab">
                                    <i class="fa fa-list"></i> Todos los Ingresos
                                    <span class="badge badge-primary badge_size"><?php echo count($ingresos); ?></span>
                                </a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content" style="margin-top: 15px;">
                            <!-- TAB 1: ÓRDENES DE COMPRA -->
                            <div role="tabpanel" class="tab-pane active" id="ordenes-compra">
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
                                                                <span class="badge badge-success badge_size">Registrado</span>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex flex-wrap gap-2">
                                                                    <a href="ingresos_detalle_directo.php?id_ingreso=<?php echo $ingreso['id_ingreso']; ?>" 
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

                            <!-- TAB 3: TODOS LOS INGRESOS -->
                            <div role="tabpanel" class="tab-pane" id="todos-ingresos">
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
                                                                    <span class="badge badge-success badge_size">Registrado</span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-primary badge_size"><?php echo $ingreso['total_productos']; ?></span>
                                                                <?php if ($tipo == 'COMPRA' && $ingreso['productos_ingresados'] != $ingreso['total_productos']) { ?>
                                                                    / <span class="badge badge-secondary badge_size"><?php echo $ingreso['productos_ingresados']; ?></span>
                                                                <?php } ?>
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
$(document).ready(function() {
    // Configuración base para DataTables (manteniendo la configuración original)
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

    // Inicializar DataTables manteniendo el ID original para compatibilidad
    $('#datatable-compras').DataTable($.extend({}, datatableConfig, {
        "order": [[ 6, 'desc' ]] // Ordenar por fecha
    }));

    $('#datatable-directos').DataTable($.extend({}, datatableConfig, {
        "order": [[ 5, 'desc' ]] // Ordenar por fecha
    }));

    $('#datatable-todos').DataTable($.extend({}, datatableConfig, {
        "order": [[ 6, 'desc' ]] // Ordenar por fecha
    }));

    // Manejar cambio de tabs para ajustar DataTables
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });
});
</script>