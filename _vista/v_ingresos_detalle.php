<?php
//=======================================================================
// VISTA: v_ingresos_detalle.php - CON MODALES PARA CENTROS DE COSTO
//=======================================================================

// ========================================================================
// VERIFICAR PERMISOS AL INICIO
// ========================================================================
$tiene_permiso_verificar = verificarPermisoEspecifico('verificar_ingresos');

// Detectar si es servicio
$esServicio = isset($detalle_ingreso['compra']['id_producto_tipo']) && $detalle_ingreso['compra']['id_producto_tipo'] == 2;

// Valores dinámicos
$TIT_COMPRA     = $esServicio ? "Servicio"      : "Compra";
$TIT_COMPRAS    = $esServicio ? "Servicios"     : "Compras";
$TIT_INGRESO    = $esServicio ? "Validación"    : "Ingreso";
$TIT_INGRESO2    = $esServicio ? "validación"    : "ingreso";
$TIT_INGRESOS   = $esServicio ? "Validaciones"  : "Ingresos";
$TIT_INGRESOS2   = $esServicio ? "validaciones"  : "ingresos";
$TIT_INGRESADOS = $esServicio ? "Validados"     : "Ingresados";
$TIT_INGRESADA = $esServicio ? "Validada"     : "Ingresada";

?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Detalle de <?= $TIT_INGRESOS ?> por Orden de <?= $TIT_COMPRA ?></h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <div class="row">
                            <div class="col-sm-10">
                                <h2>Orden de <?= $TIT_COMPRA ?>: C00<?php echo $detalle_ingreso['compra']['id_compra']; ?> / <small><?php echo "Pedido: " . $detalle_ingreso['compra']['cod_pedido']; ?></small></h2>
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
                                                <td><strong>N° Orden de <?= $TIT_COMPRA ?>:</strong></td>
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
                                            
                                            <!--  CENTRO DE COSTO DEL REGISTRADOR -->
                                            <tr>
                                                <td><strong>Centro Costo (Registrador):</strong></td>
                                                <td>
                                                    <?php 
                                                    if (!empty($detalle_ingreso['compra']['centro_costo_registrador'])) {
                                                        echo '<span class="badge badge-primary badge_size">' . 
                                                            htmlspecialchars($detalle_ingreso['compra']['centro_costo_registrador']) . 
                                                            '</span>';
                                                    } else {
                                                        echo '<span class="badge badge-secondary badge_size">No asignado</span>';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            
                                            <!--  CENTRO DE COSTO DEL APROBADOR -->
                                            <tr>
                                                <td><strong>Centro Costo (Aprobador):</strong></td>
                                                <td>
                                                    <?php 
                                                    if (!empty($detalle_ingreso['compra']['centro_costo_aprobador'])) {
                                                        echo '<span class="badge badge-success badge_size">' . 
                                                            htmlspecialchars($detalle_ingreso['compra']['centro_costo_aprobador']) . 
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
                                                    if ($detalle_ingreso['compra']['est_compra'] == 0) {
                                                        echo '<span class="badge badge-danger badge_size">ANULADO</span>';
                                                    } elseif ($detalle_ingreso['compra']['est_compra'] == 3) {
                                                        echo '<span class="badge badge-success badge_size">COMPLETADO</span>';
                                                    } else {
                                                        echo '<span class="badge badge-warning badge_size">EN PROCESO</span>';
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
                                        <h2><i class="fa fa-chart-bar"></i> Resumen de <?= $TIT_INGRESOS ?></h2>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <table class="table table-striped">
                                            <tr>
                                                <td><strong>Total de Productos:</strong></td>
                                                <td><span class="badge badge-primary badge_size"><?php echo $detalle_ingreso['resumen']['total_productos']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Completamente <?= $TIT_INGRESADOS ?>:</strong></td>
                                                <td><span class="badge badge-success badge_size"><?php echo $detalle_ingreso['resumen']['productos_completos']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Parcialmente <?= $TIT_INGRESADOS ?>:</strong></td>
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
                                                <td><strong>Aprobación Financiera Por:</strong></td>
                                                <td>
                                                    <?php echo $detalle_ingreso['compra']['aprobado_financiera_por'] ?? 'Pendiente'; ?>
                                                </td>
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
                                            <table class="table table-striped table-bordered print-table">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th style="width: 4%;">#</th>
                                                        <th style="width: 10%;">Código</th>
                                                        <th style="width: 22%;">Producto</th>
                                                        <th style="width: 7%;">Unidad</th>
                                                        <th style="width: 10%;">Cantidad Pedida</th>
                                                        <th style="width: 10%;">Cantidad <?= $TIT_INGRESADA ?></th>
                                                        <th style="width: 12%;">Último <?= $TIT_INGRESO ?></th>
                                                        <th style="width: 8%;">Estado</th>
                                                        <th style="width: 17%;">Centro(s) de Costo</th> 
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $contador = 1;
                                                    foreach ($detalle_ingreso['productos'] as $producto) {
                                                        $cantidad_ingresada = floatval($producto['cantidad_ingresada']);
                                                        $cantidad_pedida = floatval($producto['cant_compra_detalle']);
                                                        
                                                        if ($cantidad_ingresada >= $cantidad_pedida) {
                                                            $estado_badge = '<span class="badge badge-success badge_size">COMPLETO</span>';
                                                        } elseif ($cantidad_ingresada > 0) {
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
                                                                    <span class="text-muted">Sin <?= $TIT_INGRESOS2 ?></span>
                                                                <?php } ?>
                                                            </td>
                                                            <td class="text-center"><?php echo $estado_badge; ?></td>
                                                            
                                                            <!-- 🔹 CENTROS DE COSTO CON MODAL -->
                                                            <td>
                                                                <?php 
                                                                if (!empty($producto['centros_costo'])) { 
                                                                    $total_centros = count($producto['centros_costo']);
                                                                    $modalId = 'modalCentrosCostoIngreso' . $producto['id_compra_detalle'];
                                                                    
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
                                                                        <button class="btn btn-sm btn-info btn-ver-centros-ingreso" 
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

                        
                        <!-- Historial de Ingresos -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2><i class="fa fa-history"></i> Historial de <?= $TIT_INGRESOS ?> Detallado</h2>
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
                                                            <th>Cantidad <?= $TIT_INGRESADA ?></th>
                                                            <th>ID <?= $TIT_INGRESO ?></th>
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
                                                                    <small class="text-muted">I00<?php echo $ingreso['id_ingreso']; ?></small>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php } else { ?>
                                            <div class="alert alert-info text-center">
                                                <i class="fa fa-info-circle"></i> 
                                                No se han registrado <?= $TIT_INGRESOS2 ?> para esta orden de compra aún.
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
                                                            <th style="width: 45%;">Documento</th>
                                                            <th style="width: 20%;">Fecha de Carga</th>
                                                            <th style="width: 30%;">Acciones</th>
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
                                                                data-toggle="tooltip"
                                                                title="Ver documento">
                                                                    <i class="fa fa-eye"></i> Ver
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
                                                No se han adjuntado documentos para este <?= $TIT_INGRESO2 ?>.
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
                                    <?php 
                                    //  CALCULAR SI HAY PRODUCTOS PENDIENTES DE INGRESAR
                                    $hay_pendientes = false;
                                    foreach ($detalle_ingreso['productos'] as $producto) {
                                        $cantidad_ingresada = floatval($producto['cantidad_ingresada']) ?: 0;
                                        $cantidad_pedida = floatval($producto['cant_compra_detalle']);
                                        if ($cantidad_ingresada < $cantidad_pedida) {
                                            $hay_pendientes = true;
                                            break;
                                        }
                                    }
                                    
                                    // ============================================
                                    // VALIDACIÓN COMPLETA: PERMISO + ESTADO + PENDIENTES
                                    // ============================================
                                    $puede_verificar = false;
                                    $titulo_verificar = '';
                                    
                                    if (!$tiene_permiso_verificar) {
                                        $titulo_verificar = "No tienes permiso para verificar ingresos";
                                    } elseif ($detalle_ingreso['compra']['est_compra'] == 0) {
                                        $titulo_verificar = "No se puede verificar - Ingreso anulado";
                                    } elseif (!$hay_pendientes) {
                                        $titulo_verificar = "Sin productos pendientes por ingresar";
                                    } else {
                                        $puede_verificar = true;
                                        $titulo_verificar = "Verificar ingreso";
                                    }
                                    
                                    // Mostrar botón según validación
                                    if (!$tiene_permiso_verificar) { ?>
                                        <!-- SIN PERMISO -->
                                        <span data-toggle="tooltip" title="<?php echo $titulo_verificar; ?>" data-placement="top">
                                            <a href="#"
                                               class="btn btn-outline-secondary disabled"
                                               tabindex="-1" 
                                               aria-disabled="true">
                                                <i class="fa fa-eye"></i> Verificar Orden
                                            </a>
                                        </span>
                                    <?php } elseif ($detalle_ingreso['compra']['est_compra'] == 0) { ?>
                                        <!-- ORDEN ANULADA -->
                                        <span data-toggle="tooltip" title="<?php echo $titulo_verificar; ?>" data-placement="top">
                                            <a href="#"
                                               class="btn btn-outline-secondary disabled"
                                               tabindex="-1" 
                                               aria-disabled="true">
                                                <i class="fa fa-eye"></i> Verificar Orden
                                            </a>
                                        </span>
                                    <?php } elseif (!$hay_pendientes) { ?>
                                        <!-- INGRESO COMPLETO -->
                                        <span class="btn btn-success disabled">
                                            <i class="fa fa-check-circle"></i> 
                                            <?= $esServicio ? 'Validación Completa' : 'Ingreso Completo' ?>
                                        </span>
                                    <?php } else { ?>
                                        <!-- PUEDE VERIFICAR -->
                                        <a href="ingresos_verificar.php?id_compra=<?php echo $detalle_ingreso['compra']['id_compra']; ?>" 
                                           class="btn btn-info"
                                           data-toggle="tooltip"
                                           title="<?php echo $titulo_verificar; ?>">
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
        console.log('🟢 Inicializando modales de centros de costo en ingresos');
        
        // Manejar clic en botones de ver centros de costo
        jQuery(document).off('click', '.btn-ver-centros-ingreso').on('click', '.btn-ver-centros-ingreso', function(e) {
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
        console.log('🔢 Botones encontrados:', jQuery('.btn-ver-centros-ingreso').length);
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