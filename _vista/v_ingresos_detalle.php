<?php
//=======================================================================
// VISTA: v_ingresos_detalle.php
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Detalle de Ingresos por Orden de Compra</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <div class="row">
                            <div class="col-sm-10">
                                <h2>Orden de Compra #<?php echo $detalle_ingreso['compra']['id_compra']; ?> <small><?php echo $detalle_ingreso['compra']['cod_pedido']; ?></small></h2>
                            </div>
                            <div class="col-sm-2">
                                <a href="ingresos_mostrar.php" class="btn btn-outline-secondary btn-sm btn-block">
                                    <i class="fa fa-arrow-left"></i> Volver al listado
                                </a>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="x_content">
                        <div class="row">
                            <!-- Información General de la Compra -->
                            <div class="col-md-6">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2><i class="fa fa-info-circle"></i> Información General</h2>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <table class="table table-striped">
                                            <tr>
                                                <td><strong>N° Orden de Compra:</strong></td>
                                                <td><?php echo $detalle_ingreso['compra']['id_compra']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Código de Pedido:</strong></td>
                                                <td>
                                                    <a href="pedido_pdf.php?id=<?php echo $detalle_ingreso['compra']['id_pedido']; ?>" 
                                                       target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <?php echo $detalle_ingreso['compra']['cod_pedido']; ?>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Proveedor:</strong></td>
                                                <td><?php echo $detalle_ingreso['compra']['nom_proveedor']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Almacén:</strong></td>
                                                <td><?php echo $detalle_ingreso['compra']['nom_almacen']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Fecha de Registro:</strong></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($detalle_ingreso['compra']['fec_compra'])); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Estado:</strong></td>
                                                <td>
                                                    <?php 
                                                    if ($detalle_ingreso['compra']['est_compra'] == 3) {
                                                        echo '<span class="badge badge-success badge_size">COMPLETADO</span>';
                                                    } elseif ($detalle_ingreso['resumen']['productos_parciales'] > 0) {
                                                        echo '<span class="badge badge-warning badge_size">EN PROCESO</span>';
                                                    } else {
                                                        echo '<span class="badge badge-warning badge_size">PENDIENTE</span>';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Resumen de Estado -->
                            <div class="col-md-6">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2><i class="fa fa-chart-bar"></i> Resumen de Ingresos</h2>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <table class="table table-striped">
                                            <tr>
                                                <td><strong>Total de Productos:</strong></td>
                                                <td><span class="badge badge-primary badge_size"><?php echo $detalle_ingreso['resumen']['total_productos']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Completamente Ingresados:</strong></td>
                                                <td><span class="badge badge-success badge_size"><?php echo $detalle_ingreso['resumen']['productos_completos']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Parcialmente Ingresados:</strong></td>
                                                <td><span class="badge badge-warning badge_size"><?php echo $detalle_ingreso['resumen']['productos_parciales']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Pendientes:</strong></td>
                                                <td><span class="badge badge-warning badge_size"><?php echo $detalle_ingreso['resumen']['productos_pendientes']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Registrado Por:</strong></td>
                                                <td><?php echo $detalle_ingreso['compra']['registrado_por'] ?? 'No especificado'; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Aprobado Por:</strong></td>
                                                <td><?php echo $detalle_ingreso['compra']['aprobado_por'] ?? 'Pendiente'; ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detalle de Productos -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2><i class="fa fa-list"></i> Detalle de Productos</h2>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th style="width: 5%;">#</th>
                                                        <th style="width: 15%;">Código</th>
                                                        <th style="width: 30%;">Producto</th>
                                                        <th style="width: 8%;">Unidad</th>
                                                        <th style="width: 12%;">Cantidad Pedida</th>
                                                        <th style="width: 12%;">Cantidad Ingresada</th>
                                                        <th style="width: 15%;">Último Ingreso</th>
                                                        <th style="width: 8%;">Estado</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $contador = 1;
                                                    foreach ($detalle_ingreso['productos'] as $producto) {
                                                        $cantidad_ingresada = floatval($producto['cantidad_ingresada']) ?: 0;
                                                        $cantidad_pedida = floatval($producto['cant_compra_detalle']);
                                                        $porcentaje = $cantidad_pedida > 0 ? ($cantidad_ingresada / $cantidad_pedida) * 100 : 0;
                                                        
                                                        if ($porcentaje >= 100) {
                                                            $estado_badge = '<span class="badge badge-success badge_size">COMPLETO</span>';
                                                        } elseif ($porcentaje > 0) {
                                                            $estado_badge = '<span class="badge badge-warning badge_size">PARCIAL</span>';
                                                        } else {
                                                            $estado_badge = '<span class="badge badge-warning badge_size">PENDIENTE</span>';
                                                        }
                                                    ?>
                                                        <tr>
                                                            <td class="text-center"><strong><?php echo $contador; ?></strong></td>
                                                            <td><strong><?php echo $producto['cod_material']; ?></strong></td>
                                                            <td><?php echo $producto['nom_producto']; ?></td>
                                                            <td class="text-center"><?php echo $producto['nom_unidad_medida']; ?></td>
                                                            <td class="text-center">
                                                                <span class="badge badge-primary badge_size"><?php echo number_format($cantidad_pedida, 2); ?></span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge badge-success badge_size">
                                                                    <?php echo number_format($cantidad_ingresada, 2); ?>
                                                                </span>
                                                            </td>
                                                            <td class="text-center">
                                                                <?php if (!empty($producto['fecha_ultimo_ingreso'])) { ?>
                                                                    <small><?php echo date('d/m/Y H:i', strtotime($producto['fecha_ultimo_ingreso'])); ?></small>
                                                                <?php } else { ?>
                                                                    <span class="text-muted">Sin ingresos</span>
                                                                <?php } ?>
                                                            </td>
                                                            <td class="text-center"><?php echo $estado_badge; ?></td>
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

                        
                        <!-- Historial de Ingresos - VERSIÓN CORREGIDA -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2><i class="fa fa-history"></i> Historial de Ingresos Detallado</h2>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <?php if (!empty($detalle_ingreso['historial'])) { ?>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Fecha/Hora</th>
                                                            <th>Usuario</th>
                                                            <th>Producto</th>
                                                            <th>Código</th>
                                                            <th>Cantidad Ingresada</th>
                                                            <th>ID Ingreso</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($detalle_ingreso['historial'] as $ingreso) { ?>
                                                            <tr>
                                                                <td><?php echo date('d/m/Y H:i', strtotime($ingreso['fec_ingreso'])); ?></td>
                                                                <td>
                                                                    <i class="fa fa-user"></i> 
                                                                    <?php echo $ingreso['nom_personal']; ?>
                                                                </td>
                                                                <td><?php echo $ingreso['nom_producto']; ?></td>
                                                                <td>
                                                                    <strong><?php echo $ingreso['cod_material']; ?></strong>
                                                                </td>
                                                                <td>
                                                                    <span class="badge badge-success badge_size">
                                                                        <?php echo number_format($ingreso['cantidad_individual'], 2); ?>
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <small class="text-muted">ING-<?php echo $ingreso['id_ingreso']; ?></small>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php } else { ?>
                                            <div class="alert alert-info text-center">
                                                <i class="fa fa-info-circle"></i> 
                                                No se han registrado ingresos para esta orden de compra aún.
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN: DOCUMENTOS ADJUNTOS -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2><i class="fa fa-paperclip"></i> Documentos Adjuntos</h2>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <?php
                                        require_once("../_modelo/m_documentos.php");
                                        $documentos_ingreso = MostrarDocumentos('ingresos', $detalle_ingreso['compra']['id_compra']);
                                        
                                        if (!empty($documentos_ingreso)) {
                                        ?>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 5%;">#</th>
                                                            <th style="width: 50%;">Documento</th>
                                                            <th style="width: 25%;">Fecha de Carga</th>
                                                            <th style="width: 20%;">Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        $contador_docs = 1;
                                                        foreach($documentos_ingreso as $doc) { 
                                                        ?>
                                                        <tr>
                                                            <td class="text-center"><?php echo $contador_docs++; ?></td>
                                                            <td>
                                                                <i class="fa fa-file-<?php echo strpos($doc['documento'], '.pdf') !== false ? 'pdf' : 'text'; ?>-o"></i>
                                                                <strong><?php echo $doc['documento']; ?></strong>
                                                            </td>
                                                            <td><?php echo date('d/m/Y H:i', strtotime($doc['fec_subida'])); ?></td>
                                                            <td>
                                                                <a href="../uploads/ingresos/<?php echo $doc['documento']; ?>" 
                                                                target="_blank" 
                                                                class="btn btn-primary btn-sm" 
                                                                title="Ver documento">
                                                                    <i class="fa fa-eye"></i> Ver
                                                                </a>
                                                                <a href="../uploads/ingresos/<?php echo $doc['documento']; ?>" 
                                                                download 
                                                                class="btn btn-success btn-sm" 
                                                                title="Descargar">
                                                                    <i class="fa fa-download"></i> Descargar
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php } else { ?>
                                            <div class="alert alert-info text-center">
                                                <i class="fa fa-info-circle"></i> 
                                                No se han adjuntado documentos para este ingreso.
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group text-center">
                                    <a href="ingresos_mostrar.php" class="btn btn-secondary">
                                        <i class="fa fa-arrow-left"></i> Volver al Listado
                                    </a>
                                    <?php if ($detalle_ingreso['compra']['est_compra'] != 3) { ?>
                                        <a href="ingresos_verificar.php?id_compra=<?php echo $detalle_ingreso['compra']['id_compra']; ?>" class="btn btn-info">
                                            <i class="fa fa-eye"></i> Verificar Orden
                                        </a>
                                    <?php } ?>
                                    <button type="button" class="btn btn-primary" onclick="window.print()">
                                        <i class="fa fa-print"></i> Imprimir Detalle
                                    </button>
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

<style>
.badge-lg {
    font-size: 14px;
    padding: 8px 12px;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.thead-dark th {
    background-color: #343a40;
    color: white;
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}

/* ESTILOS MEJORADOS PARA IMPRESIÓN */
@media print {
    @page {
        size: A4;
        margin: 1cm;
    }
    
    /* Ocultar elementos que no deben imprimirse */
    .no-print, .btn, .x_title .col-sm-2 {
        display: none !important;
    }
    
    /* Asegurar que todo el contenido se vea */
    body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
        font-size: 12px;
        line-height: 1.3;
    }
    
    /* Ajustar columnas para impresión */
    .col-md-6 {
        width: 50%;
        float: left;
    }
    
    /* Mejorar badges para impresión */
    .badge {
        color: #000 !important;
        background-color: transparent !important;
        border: 1px solid #000 !important;
        border-radius: 3px;
        padding: 2px 6px;
        font-size: 11px;
        font-weight: bold;
    }
    
    /* Mejorar paneles para impresión */
    .x_panel {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
        page-break-inside: avoid;
        margin-bottom: 15px;
    }
    
    /* Mejorar tablas para impresión */
    table {
        width: 100% !important;
        border-collapse: collapse !important;
    }
    
    table th,
    table td {
        border: 1px solid #000 !important;
        padding: 4px !important;
        font-size: 11px !important;
        page-break-inside: avoid;
    }
    
    table th {
        background-color: #f0f0f0 !important;
        color: #000 !important;
        font-weight: bold !important;
        text-align: center;
    }
    
    /* Mejorar alert para impresión */
    .alert {
        border: 1px solid #000 !important;
        background-color: #f9f9f9 !important;
        color: #000 !important;
        page-break-inside: avoid;
    }
    
    /* Asegurar que los títulos no se separen del contenido */
    .x_title {
        page-break-after: avoid;
    }
    
    /* Evitar que las filas de la tabla se corten */
    table tr {
        page-break-inside: avoid;
    }
    
    /* Mejorar texto para impresión */
    h2, h3, h4 {
        color: #000 !important;
        page-break-after: avoid;
        margin-bottom: 10px;
    }
    
    .text-center {
        text-align: center !important;
    }
    
    /* Asegurar contraste en iconos */
    .fa {
        color: #000 !important;
    }
    
    /* Forzar salto de página antes del detalle si es necesario */
    table thead {
        display: table-header-group;
    }
    
    /* Evitar que elementos importantes se corten */
    .table-responsive {
        overflow: visible !important;
    }
    
    /* Asegurar que los rows se mantengan juntos */
    .row {
        page-break-inside: avoid;
    }
}

/* Estilos adicionales para mejorar la visualización */
table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}
</style>