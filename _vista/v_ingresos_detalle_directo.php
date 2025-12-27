<?php
//=======================================================================
// VISTA: v_ingresos_detalle_directo.php - CON MODALES PARA CENTROS DE COSTO
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title no-print">
            <div class="title_left">
                <h3>Detalle de Ingreso Directo</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <div class="row">
                            <div class="col-sm-10">
                                <h2>Ingreso Directo I00<?php echo $detalle_ingreso_directo['ingreso']['id_ingreso']; ?> <small>Sin Orden de Compra</small></h2>
                            </div>
                            <div class="col-sm-2 no-print">
                                <a href="ingresos_mostrar.php?tab=todos-ingresos" class="btn btn-outline-secondary btn-sm btn-block">
                                    <i class="fa fa-arrow-left"></i> Volver al listado
                                </a>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="x_content">
                        <div class="row">
                            <!-- Información General del Ingreso -->
                            <div class="col-md-6 col-print-6">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2><i class="fa fa-info-circle"></i> Información General</h2>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <table class="table table-striped">
                                            <tr>
                                                <td><strong>N° Ingreso Directo:</strong></td>
                                                <td>I00<?php echo $detalle_ingreso_directo['ingreso']['id_ingreso']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Tipo de Ingreso:</strong></td>
                                                <td><span class="badge badge-info badge_size">DIRECTO</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Cliente:</strong></td>
                                                <td><?php echo $detalle_ingreso_directo['ingreso']['nom_cliente']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Obra:</strong></td>
                                                <td><?php echo $detalle_ingreso_directo['ingreso']['nom_obra']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Almacén:</strong></td>
                                                <td><?php echo $detalle_ingreso_directo['ingreso']['nom_almacen']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Ubicación:</strong></td>
                                                <td><?php echo $detalle_ingreso_directo['ingreso']['nom_ubicacion']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Fecha de Registro:</strong></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($detalle_ingreso_directo['ingreso']['fec_ingreso'])); ?></td>
                                            </tr>
                                            
                                            <!--  CENTRO DE COSTO -->
                                            <tr>
                                                <td><strong>Centro de Costo:</strong></td>
                                                <td>
                                                    <?php 
                                                    if (!empty($detalle_ingreso_directo['ingreso']['nom_centro_costo'])) {
                                                        echo '<span class="badge badge-primary badge_size">' . 
                                                            htmlspecialchars($detalle_ingreso_directo['ingreso']['nom_centro_costo']) . 
                                                            '</span>';
                                                    } else {
                                                        echo '<span class="badge badge-secondary badge_size">No asignado</span>';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td><strong>Estado:</strong></td>
                                                <td>
                                                    <?php 
                                                    if ($detalle_ingreso_directo['ingreso']['est_ingreso'] == 0) {
                                                        echo '<span class="badge badge-danger badge_size">ANULADO</span>';
                                                    } elseif ($detalle_ingreso_directo['ingreso']['est_ingreso'] == 1) {
                                                        echo '<span class="badge badge-success badge_size">REGISTRADO</span>';
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

                            <!-- Resumen de Productos -->
                            <div class="col-md-6 col-print-6">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2><i class="fa fa-chart-bar"></i> Resumen de Productos</h2>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <table class="table table-striped">
                                            <tr>
                                                <td><strong>Total de Productos:</strong></td>
                                                <td>
                                                    <span class="badge badge-primary badge_size">
                                                        <?php echo count($detalle_ingreso_directo['productos']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Cantidad Total Ingresada:</strong></td>
                                                <td>
                                                    <?php 
                                                    $cantidad_total = 0;
                                                    foreach ($detalle_ingreso_directo['productos'] as $producto) {
                                                        // Solo sumar si el detalle NO está anulado
                                                        if (isset($producto['est_ingreso_detalle']) && $producto['est_ingreso_detalle'] == 1) {
                                                            $cantidad_total += floatval($producto['cant_ingreso_detalle']);
                                                        }
                                                    }
                                                    
                                                    // Cambiar color del badge según si hay cantidad o no
                                                    $badge_class = $cantidad_total > 0 ? 'badge-success' : 'badge-secondary';
                                                    ?>
                                                    <span class="badge <?php echo $badge_class; ?> badge_size">
                                                        <?php echo number_format($cantidad_total, 2); ?>
                                                    </span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><strong>Estado:</strong></td>
                                                <td>
                                                    <?php 
                                                    if ($detalle_ingreso_directo['ingreso']['est_ingreso'] == 0) {
                                                        echo '<span class="badge badge-danger badge_size">ANULADO</span>';
                                                    } elseif ($detalle_ingreso_directo['ingreso']['est_ingreso'] == 1) {
                                                        echo '<span class="badge badge-success badge_size">REGISTRADO</span>';
                                                    } else {
                                                        echo '<span class="badge badge-warning badge_size">PENDIENTE</span>';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Registrado Por:</strong></td>
                                                <td><?php echo $detalle_ingreso_directo['ingreso']['nom_personal'] ?? 'No especificado'; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Fecha de Pago:</strong></td>
                                                <td>
                                                    <?php 
                                                    if (!empty($detalle_ingreso_directo['ingreso']['fpag_ingreso'])) {
                                                        echo date('d/m/Y H:i', strtotime($detalle_ingreso_directo['ingreso']['fpag_ingreso']));
                                                    } else {
                                                        echo 'No especificado';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detalle de Productos Ingresados -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2><i class="fa fa-list"></i> Detalle de Productos Ingresados</h2>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered print-table">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th style="width: 4%;">#</th>
                                                        <th style="width: 12%;">Código</th>
                                                        <th style="width: 20%;">Producto</th>
                                                        <th style="width: 12%;">Tipo</th>
                                                        <th style="width: 8%;">Marca</th>
                                                        <th style="width: 8%;">Modelo</th>
                                                        <th style="width: 7%;">Unidad</th>
                                                        <th style="width: 10%;">Cantidad</th>
                                                        <th style="width: 19%;">Centro(s) de Costo</th> 
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $contador = 1;
                                                    foreach ($detalle_ingreso_directo['productos'] as $producto) {
                                                        $cantidad_ingresada = floatval($producto['cant_ingreso_detalle']);
                                                    ?>
                                                        <tr>
                                                            <td class="text-center"><strong><?php echo $contador; ?></strong></td>
                                                            <td><strong><?php echo $producto['cod_material']; ?></strong></td>
                                                            <td><?php echo $producto['nom_producto']; ?></td>
                                                            <td><?php echo $producto['nom_material_tipo']; ?></td>
                                                            <td class="text-center"><?php echo $producto['mar_producto'] ?? 'N/A'; ?></td>
                                                            <td class="text-center"><?php echo $producto['mod_producto'] ?? 'N/A'; ?></td>
                                                            <td class="text-center"><?php echo $producto['nom_unidad_medida']; ?></td>
                                                            <td class="text-center">
                                                                <?php if ($producto['est_ingreso_detalle'] == 0): ?>
                                                                    <span class="badge badge-danger badge_size">ANULADO</span>
                                                                <?php else: ?>
                                                                    <span class="badge badge-success badge_size">
                                                                        <?php echo number_format($cantidad_ingresada, 2); ?>
                                                                    </span>
                                                                <?php endif; ?>
                                                            </td>
                                                            
                                                            <!-- 🔹 CENTROS DE COSTO CON MODAL -->
                                                            <td>
                                                                <?php 
                                                                if (!empty($producto['centros_costo'])) { 
                                                                    $total_centros = count($producto['centros_costo']);
                                                                    $modalId = 'modalCentrosCostoIngresoDirecto' . $producto['id_ingreso_detalle'];
                                                                    
                                                                    if ($total_centros === 1) {
                                                                        // Un solo centro de costo - mostrar directamente
                                                                        ?>
                                                                        <span class="badge badge-info badge_size" style="font-size: 11px;">
                                                                            <?php echo htmlspecialchars($producto['centros_costo'][0]['nom_centro_costo']); ?>
                                                                        </span>
                                                                    <?php } else { 
                                                                        // Múltiples centros de costo - mostrar botón con modal
                                                                        $listaCentros = '';
                                                                        foreach ($producto['centros_costo'] as $idx => $cc) {
                                                                            $listaCentros .= '<div style="padding: 8px; margin-bottom: 6px; background-color: #f8f9fa; border-left: 3px solid #17a2b8; border-radius: 4px;">';
                                                                            $listaCentros .= '<strong style="color: #17a2b8;">' . ($idx + 1) . '.</strong> ' . htmlspecialchars($cc['nom_centro_costo']);
                                                                            $listaCentros .= '</div>';
                                                                        }
                                                                        ?>
                                                                        <button class="btn btn-sm btn-info btn-ver-centros-directo" 
                                                                                type="button" 
                                                                                data-modal-id="<?php echo $modalId; ?>"
                                                                                style="font-size: 11px; padding: 3px 10px;">
                                                                            <i class="fa fa-eye"></i> Ver <?php echo $total_centros; ?> centros
                                                                        </button>
                                                                        
                                                                        <!-- Modal para centros de costo -->
                                                                        <div class="modal fade" id="<?php echo $modalId; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header" style="background-color: #17a2b8; color: white; padding: 12px 20px;">
                                                                                        <h6 class="modal-title mb-0">
                                                                                            <i class="fa fa-building"></i> 
                                                                                            Centros de Costo Asignados
                                                                                        </h6>
                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.8;">
                                                                                            <span aria-hidden="true">&times;</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body" style="padding: 20px;">
                                                                                        <div style="margin-bottom: 15px; padding: 10px; background-color: #e7f3ff; border-radius: 4px; border-left: 4px solid #17a2b8;">
                                                                                            <strong>Producto:</strong> <?php echo htmlspecialchars($producto['nom_producto']); ?>
                                                                                        </div>
                                                                                        <div style="max-height: 400px; overflow-y: auto;">
                                                                                            <?php echo $listaCentros; ?>
                                                                                        </div>
                                                                                        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6; text-align: center;">
                                                                                            <span class="badge badge-info" style="font-size: 12px; padding: 6px 12px;">
                                                                                                Total: <?php echo $total_centros; ?> centro(s) de costo
                                                                                            </span>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="modal-footer" style="padding: 10px 20px;">
                                                                                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                                                                                            <i class="fa fa-times"></i> Cerrar
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php } ?>
                                                                <?php } else { ?>
                                                                    <small class="text-muted">Sin asignar</small>
                                                                <?php } ?>
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
                        
                        <!-- Documentos Adjuntos -->
                        <?php if (!empty($documentos_ingreso)) { ?>
                        <div class="row no-print">
                            <div class="col-md-12">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2><i class="fa fa-paperclip"></i> Documentos Adjuntos</h2>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th width="5%">#</th>
                                                        <th width="60%">Archivo</th>
                                                        <th width="20%">Fecha de Subida</th>
                                                        <th width="15%">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $contador_doc = 1;
                                                    foreach ($documentos_ingreso as $doc) { 
                                                    ?>
                                                    <tr>
                                                        <td class="text-center"><?php echo $contador_doc++; ?></td>
                                                        <td>
                                                            <i class="fa fa-file-pdf text-danger"></i> 
                                                            <?php echo basename($doc['documento']); ?>
                                                        </td>
                                                        <td><?php echo date('d/m/Y H:i', strtotime($doc['fec_subida'])); ?></td>
                                                        <td class="text-center">
                                                            <a href="../uploads/ingreso_directo/<?php echo $doc['documento']; ?>" 
                                                            target="_blank" 
                                                            class="btn btn-info btn-xs" 
                                                            data-toggle="tooltip"
                                                            title="Ver documento">
                                                                <i class="fa fa-eye"></i> Ver
                                                            </a>
                                                            <a href="../uploads/ingreso_directo/<?php echo $doc['documento']; ?>" 
                                                            download 
                                                            class="btn btn-success btn-xs"
                                                            data-toggle="tooltip"
                                                            title="Descargar">
                                                                <i class="fa fa-download"></i> Descargar
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        
                        <!-- Información Adicional -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2><i class="fa fa-info"></i> Información Adicional</h2>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <div class="alert alert-info print-info">
                                            <h4><i class="fa fa-info-circle"></i> Características del Ingreso Directo:</h4>
                                            <ul class="list-unstyled" style="margin-bottom: 0;">
                                                <li><i class="fa fa-check-circle text-success"></i> Los productos han sido ingresados directamente al almacén sin orden de compra previa</li>
                                                <li><i class="fa fa-check-circle text-success"></i> El stock ha sido actualizado automáticamente al momento del registro</li>
                                                <li><i class="fa fa-check-circle text-success"></i> Se han generado los movimientos de inventario correspondientes</li>
                                                <li><i class="fa fa-info-circle text-info"></i> Fecha de registro: <?php echo date('d/m/Y H:i', strtotime($detalle_ingreso_directo['ingreso']['fec_ingreso'])); ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="row no-print">
                            <div class="col-md-12">
                                <div class="form-group text-center">
                                    <a href="ingresos_mostrar.php?tab=todos-ingresos" class="btn btn-secondary">
                                        <i class="fa fa-arrow-left"></i> Volver al Listado
                                    </a>
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

<!-- 🔹 SCRIPT PARA MANEJAR MODALES DE CENTROS DE COSTO -->
<script>
(function() {
    'use strict';
    
    // Esperar a que jQuery y Bootstrap estén disponibles
    function esperarLibrerias(callback) {
        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.modal !== 'undefined') {
            callback();
        } else {
            setTimeout(function() { esperarLibrerias(callback); }, 100);
        }
    }
    
    esperarLibrerias(function() {
        console.log('🟢 Inicializando modales de centros de costo en ingresos directos');
        
        // Manejar clic en botones de ver centros de costo
        jQuery(document).off('click', '.btn-ver-centros-directo').on('click', '.btn-ver-centros-directo', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const modalId = jQuery(this).data('modal-id');
            console.log('👁️ Abriendo modal de centros:', modalId);
            
            const $modal = jQuery('#' + modalId);
            if ($modal.length) {
                $modal.modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            }
        });
        
        console.log('✅ Eventos de modales configurados');
        console.log('🔢 Botones encontrados:', jQuery('.btn-ver-centros-directo').length);
    });
})();
</script>

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
    .col-print-6 {
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
    .print-table {
        width: 100% !important;
        border-collapse: collapse !important;
    }
    
    .print-table th,
    .print-table td {
        border: 1px solid #000 !important;
        padding: 4px !important;
        font-size: 11px !important;
        page-break-inside: avoid;
    }
    
    .print-table th {
        background-color: #f0f0f0 !important;
        color: #000 !important;
        font-weight: bold !important;
        text-align: center;
    }
    
    /* Mejorar alert para impresión */
    .print-info {
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
    .print-table tr {
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
    .print-table thead {
        display: table-header-group;
    }
    
    /* Evitar que elementos importantes se corten */
    .table-responsive {
        overflow: visible !important;
    }
}

/* Estilos adicionales para mejorar la visualización */
.print-table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}
</style>