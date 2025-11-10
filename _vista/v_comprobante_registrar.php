<?php
// ====================================================================
// VISTA: Gestión de Comprobantes de Pago
// SOLO MUESTRA DATOS - NO CARGA NADA
// ====================================================================

// Validar que existan los datos cargados por el controlador
/*if (!$oc) {
    echo '<div class="right_col" role="main">
            <div class="alert alert-danger">
                <strong>Error:</strong> No se especificó una orden de compra válida o no existe.
                <br><br>
                <a href="compras_mostrar.php" class="btn btn-primary">
                    <i class="fa fa-arrow-left"></i> Volver al listado de compras
                </a>
            </div>
          </div>';
    return;
}*/

require_once("../_modelo/m_detraccion.php");
?>

<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Pagos de la Orden de Compra</h3>
            </div>
            <div class="title_right">
                <div class="pull-right">
                    <a href="compras_mostrar.php" class="btn btn-outline-primary">
                        <i class="fa fa-arrow-left"></i> Volver al Listado
                    </a>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>

        <!-- ============================================================ -->
        <!-- INFORMACIÓN DE LA ORDEN DE COMPRA -->
        <!-- ============================================================ -->
        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Información de la Orden de Compra</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>Nº OC:</strong></td>
                                <td><?php echo 'C00'.$oc['id_compra']; ?></td>
                                <td><strong>Proveedor:</strong></td>
                                <td><?php echo $oc['nom_proveedor']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Subtotal:</strong></td>
                                <td><?php echo ($oc['simbolo_moneda'] ?? 'S/.') . ' ' . number_format($oc['subtotal'], 2); ?></td>
                                <td><strong>IGV (18%):</strong></td>
                                <td><?php echo ($oc['simbolo_moneda'] ?? 'S/.') . ' ' . number_format($oc['total_igv'], 2); ?></td>
                            </tr>
                            
                            <!-- MONTO TOTAL CON IGV (destacado) -->
                            <tr style="background-color: #d4edda; border: 2px solid #28a745;">
                                <td colspan="2">
                                    <strong style="font-size: 16px;">💰 MONTO TOTAL CON IGV:</strong>
                                </td>
                                <td colspan="2">
                                    <span class="badge badge-success" style="font-size: 18px; padding: 10px 16px;">
                                        <?php echo ($oc['simbolo_moneda'] ?? 'S/.') . ' ' . number_format($oc['total_con_igv'], 2); ?>
                                    </span>
                                </td>
                            </tr>
                            
                            <!-- Mostrar Detracción si existe -->
                            <?php if (!empty($oc['monto_detraccion']) && $oc['monto_detraccion'] > 0): ?>
                            <tr>
                                <td colspan="2">
                                    <strong>📊 Detracción (<?php echo $oc['nombre_detraccion']; ?> - <?php echo $oc['porcentaje_detraccion']; ?>%):</strong>
                                    <br><small class="text-muted">A depositar en cuenta SUNAT del proveedor</small>
                                </td>
                                <td colspan="2" class="text-danger" style="font-weight: bold;">
                                    -<?php echo ($oc['simbolo_moneda'] ?? 'S/.') . ' ' . number_format($oc['monto_detraccion'], 2); ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            
                            <tr>
                                <td><strong>Estado:</strong></td>
                                <td colspan="3">
                                    <?php
                                    $badge_class = 'secondary';
                                    $estado_texto = 'Desconocido';
                                    
                                    switch($oc['est_compra']) {
                                        case 0: $badge_class = 'danger'; $estado_texto = 'Anulado'; break;
                                        case 1: $badge_class = 'warning'; $estado_texto = 'Pendiente'; break;
                                        case 2: $badge_class = 'info'; $estado_texto = 'Aprobado'; break;
                                        case 3: $badge_class = 'success'; $estado_texto = 'Ingresado'; break;
                                        case 4: $badge_class = 'primary'; $estado_texto = 'Pagado'; break;
                                    }
                                    ?>
                                    <span class="badge badge-<?php echo $badge_class; ?>"><?php echo $estado_texto; ?></span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- ============================================================ -->
                <!-- LISTADO DE COMPROBANTES -->
                <!-- ============================================================ -->
                <div class="x_panel">
                    <div class="x_title">
                        <h2><i class="fa fa-file-text"></i> Comprobantes Registrados</h2>
                        <div class="pull-right">
                            <button
                                class="btn btn-sm <?php echo ($oc['est_compra'] == 4 || $oc['est_compra'] == 3) ? 'btn-outline-secondary disabled' : 'btn-outline-success'; ?>"
                                <?php echo ($oc['est_compra'] == 4 || $oc['est_compra'] == 3) ? 'disabled title="Esta compra está cerrada"' : 'data-toggle="modal" data-target="#modalRegistrarComprobante"'; ?>>
                                <i class="fa fa-plus"></i> Registrar Comprobante
                            </button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="table-responsive">
                            <table id="tablaComprobantes" class="table table-striped table-bordered table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tipo Doc.</th>
                                        <th>Serie-Número</th>
                                        <th>Monto IGV</th>
                                        <th>Total</th>
                                        <th>Fecha Pago</th>
                                        <th>Voucher</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; foreach($comprobantes as $comp) { ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $comp['nom_tipo_documento']; ?></td>
                                        <td><strong><?php echo $comp['num_comprobante']; ?></strong></td>
                                        <td><?php echo $comp['simbolo_moneda'] . ' ' . number_format($comp['monto_total_igv'], 2); ?></td>
                                        <td><strong><?php echo $comp['simbolo_moneda'] . ' ' . number_format($comp['total_pagar'], 2); ?></strong></td>
                                        <td><?php echo $comp['fec_pago'] ? date('d/m/Y', strtotime($comp['fec_pago'])) : '<span class="text-muted">Pendiente</span>'; ?></td>
                                        <td class="text-center">
                                            <?php if (!empty($comp['voucher_pago'])): ?>
                                                <a href="../_upload/vouchers/<?php echo $comp['voucher_pago']; ?>" target="_blank" style="text-decoration: underline; color: #495057;">
                                                    Ver voucher
                                                </a>
                                            <?php else: ?>
                                                <span style="color: #6c757d;">Sin voucher</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (!isset($comp['est_comprobante']) || $comp['est_comprobante'] == 1): ?>
                                                <span class="badge badge-warning badge_size">Pendiente</span>
                                            <?php elseif (!isset($comp['est_comprobante']) || $comp['est_comprobante'] == 2): ?>
                                                <span class="badge badge-primary badge_size">Por Pagar</span>
                                            <?php elseif (!isset($comp['est_comprobante']) || $comp['est_comprobante'] == 3): ?>
                                                <span class="badge badge-success badge_size">Pagado</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger badge_size">Anulado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($comp['est_comprobante'] == 1): ?>
                                                <!-- ESTADO 1: ACTIVO - Todo disponible excepto Subir Voucher -->
                                                <button class="btn btn-info btn-sm" title="Ver Detalle" onclick="VerDetalleComprobante(<?php echo $comp['id_comprobante']; ?>)">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                
                                                <button class="btn btn-warning btn-sm" title="Editar" onclick="CargarModalEditar(<?php echo $comp['id_comprobante']; ?>)">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                
                                                <button class="btn btn-secondary btn-sm" title="Subir Voucher" disabled>
                                                    <i class="fa fa-upload"></i>
                                                </button>
                                                
                                                <button class="btn btn-danger btn-sm" title="Anular" onclick="AnularComprobante(<?php echo $comp['id_comprobante']; ?>)">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                                
                                            <?php elseif ($comp['est_comprobante'] == 2): ?>
                                                <!-- ESTADO 2: PENDIENTE - Ver y Subir Voucher activos -->
                                                <button class="btn btn-info btn-sm" title="Ver Detalle" onclick="VerDetalleComprobante(<?php echo $comp['id_comprobante']; ?>)">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                
                                                <button class="btn btn-secondary btn-sm" title="Editar" disabled>
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                
                                                <button class="btn btn-success btn-sm" title="Subir Voucher" onclick="AbrirModalVoucher(<?php echo $comp['id_comprobante']; ?>)">
                                                    <i class="fa fa-upload"></i>
                                                </button>
                                                
                                                <button class="btn btn-secondary btn-sm" title="Anular" disabled>
                                                    <i class="fa fa-times"></i>
                                                </button>
                                                
                                            <?php elseif ($comp['est_comprobante'] == 3): ?>
                                                <!-- ESTADO 3: ANULADO - Solo Ver disponible -->
                                                <button class="btn btn-info btn-sm" title="Ver Detalle" onclick="VerDetalleComprobante(<?php echo $comp['id_comprobante']; ?>)">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                
                                                <button class="btn btn-secondary btn-sm" title="Editar" disabled>
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                
                                                <button class="btn btn-secondary btn-sm" title="Subir Voucher" disabled>
                                                    <i class="fa fa-upload"></i>
                                                </button>
                                                
                                                <button class="btn btn-secondary btn-sm" title="Anulado" disabled>
                                                    <i class="fa fa-ban"></i>
                                                </button>
                                                
                                            <?php else: ?>
                                                <button class="btn btn-info btn-sm" title="Ver Detalle" onclick="VerDetalleComprobante(<?php echo $comp['id_comprobante']; ?>)">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                
                                                <button class="btn btn-secondary btn-sm" title="Editar" disabled>
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                
                                                <button class="btn btn-secondary btn-sm" title="Subir Voucher" disabled>
                                                    <i class="fa fa-upload"></i>
                                                </button>
                                                
                                                <button class="btn btn-secondary btn-sm" title="Anulado" disabled>
                                                    <i class="fa fa-ban"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                        
                                    </tr>
                                    <?php } ?>
                                    <?php if(empty($comprobantes)) { ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">
                                            <i class="fa fa-inbox fa-3x"></i><br>
                                            No hay comprobantes registrados para esta orden de compra
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
    </div>
</div>

<!-- ============================================================ -->
<!-- MODAL: REGISTRAR NUEVO COMPROBANTE -->
<!-- ============================================================ -->
<div class="modal fade" id="modalRegistrarComprobante" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <form action="comprobante_registrar.php?id_compra=<?php echo $id_compra; ?>" method="post" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fa fa-plus-circle"></i> Registrar Nuevo Comprobante</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="accion" value="registrar">
                <input type="hidden" name="id_compra" value="<?php echo $id_compra; ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tipo de Documento <span class="text-danger">*</span></label>
                            <select name="id_tipo_documento" class="form-control" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach($tipos_documento as $td) { ?>
                                    <option value="<?php echo $td['id_tipo_documento']; ?>">
                                        <?php echo $td['nom_tipo_documento']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Serie <span class="text-danger">*</span></label>
                            <input type="text" name="serie" class="form-control" placeholder="Ej: F001" required>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Número <span class="text-danger">*</span></label>
                            <input type="text" name="numero" class="form-control" placeholder="Ej: 00001234" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Monto Total con IGV <span class="text-danger">*</span></label>
                            <input name="monto_total_igv" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Moneda <span class="text-danger">*</span></label>
                            <div class="form-control" style="background-color: #f8f9fa; border: 1px solid #ced4da; padding: 0.375rem 0.75rem;">
                                <?php 
                                foreach($monedas as $mon) { 
                                    if($mon['id_moneda'] == $oc['id_moneda']) {
                                        if($mon['id_moneda']==1){
                                            $simbolo_moneda='S/.';
                                        }
                                        else if($mon['id_moneda']==2){
                                            $simbolo_moneda='US$';
                                        }
                                        echo  $mon['nom_moneda'] . ' (' . $simbolo_moneda . ')</strong>';
                                    }
                                } 
                                ?>
                            </div>
                            
                            <!-- Campo oculto para enviar el valor -->
                            <input type="hidden" name="id_moneda" value="<?php echo $oc['id_moneda']; ?>">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Afectaciones Tributarias (Opcional)</label>
                            <div class="card" style="border: 1px solid #dee2e6;">
                                <div class="card-header" style="background-color: #f8f9fa; padding: 8px 12px; cursor: pointer;" 
                                    data-toggle="collapse" 
                                    data-target="#afectacionesCollapse"
                                    aria-expanded="false">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0" style="font-size: 13px;">
                                            <i class="fa fa-percent text-info"></i> 
                                            Detracción, Retención y Percepción
                                        </h6>
                                        <i class="fa fa-chevron-down"></i>
                                    </div>
                                </div>
                                
                                <div class="collapse" id="afectacionesCollapse">
                                    <div class="card-body" style="padding: 12px;">
                                        <!-- DETRACCIÓN -->
                                        <div class="mb-3">
                                            <label style="font-size: 11px; font-weight: bold;">Detracción:</label>
                                            <div id="contenedor-detracciones" style="padding: 8px; background-color: #fff3cd; border-radius: 4px; border: 1px solid #ffc107;">
                                                <?php
                                                $detracciones_lista = ObtenerDetraccionesPorTipo('DETRACCION');
                                                
                                                if (!empty($detracciones_lista)) {
                                                    foreach ($detracciones_lista as $detraccion) {
                                                        $id_det = $detraccion['id_detraccion'];
                                                        $porcentaje = $detraccion['porcentaje'];
                                                        $nombre = htmlspecialchars($detraccion['nombre_detraccion'], ENT_QUOTES);
                                                        ?>
                                                        <div class="form-check" style="margin-bottom: 5px;">
                                                            <input class="form-check-input detraccion-checkbox" 
                                                                type="checkbox" 
                                                                name="id_detraccion[]" 
                                                                value="<?php echo $id_det; ?>" 
                                                                data-porcentaje="<?php echo $porcentaje; ?>" 
                                                                data-nombre="<?php echo $nombre; ?>"
                                                                data-tipo="DETRACCION"
                                                                onchange="manejarClickDetraccion(this)"
                                                                id="detraccion_<?php echo $id_det; ?>">
                                                            <label class="form-check-label" 
                                                                for="detraccion_<?php echo $id_det; ?>" 
                                                                style="font-size: 12px; cursor: pointer;">
                                                                <?php echo $nombre; ?> 
                                                                <strong>(<?php echo $porcentaje; ?>%)</strong>
                                                            </label>
                                                        </div>
                                                        <?php
                                                    }
                                                } else {
                                                    echo '<p class="text-muted" style="font-size: 11px; margin: 0;"><i class="fa fa-info-circle"></i> No hay detracciones configuradas</p>';
                                                }
                                                ?>
                                            </div>
                                            <small class="form-text text-muted">Se descuenta del total con IGV</small>
                                        </div>

                                        <!-- RETENCIÓN -->
                                        <div class="mb-3">
                                            <label style="font-size: 11px; font-weight: bold;">Retención:</label>
                                            <div id="contenedor-retenciones" style="padding: 8px; background-color: #e7f3ff; border-radius: 4px; border: 1px solid #2196f3;">
                                                <?php
                                                $retenciones_lista = ObtenerDetraccionesPorTipo('RETENCION');
                                                
                                                if (!empty($retenciones_lista)) {
                                                    foreach ($retenciones_lista as $retencion) {
                                                        $id_ret = $retencion['id_detraccion'];
                                                        $porcentaje = $retencion['porcentaje'];
                                                        $nombre = htmlspecialchars($retencion['nombre_detraccion'], ENT_QUOTES);
                                                        ?>
                                                        <div class="form-check" style="margin-bottom: 5px;">
                                                            <input class="form-check-input retencion-checkbox" 
                                                                type="checkbox" 
                                                                name="id_retencion[]" 
                                                                value="<?php echo $id_ret; ?>" 
                                                                data-porcentaje="<?php echo $porcentaje; ?>" 
                                                                data-nombre="<?php echo $nombre; ?>"
                                                                data-tipo="RETENCION"
                                                                onchange="manejarClickRetencion(this)"
                                                                id="retencion_<?php echo $id_ret; ?>">
                                                            <label class="form-check-label" 
                                                                for="retencion_<?php echo $id_ret; ?>" 
                                                                style="font-size: 12px; cursor: pointer;">
                                                                <?php echo $nombre; ?> 
                                                                <strong>(<?php echo $porcentaje; ?>%)</strong>
                                                            </label>
                                                        </div>
                                                        <?php
                                                    }
                                                } else {
                                                    echo '<p class="text-muted" style="font-size: 11px; margin: 0;"><i class="fa fa-info-circle"></i> No hay retenciones configuradas</p>';
                                                }
                                                ?>
                                            </div>
                                            <small class="form-text text-muted">Se descuenta del total con IGV</small>
                                        </div>

                                        <!-- PERCEPCIÓN -->
                                        <div class="mb-2">
                                            <label style="font-size: 11px; font-weight: bold;">Percepción:</label>
                                            <div id="contenedor-percepciones" style="padding: 8px; background-color: #e8f5e9; border-radius: 4px; border: 1px solid #4caf50;">
                                                <?php
                                                $percepciones_lista = ObtenerDetraccionesPorTipo('PERCEPCION');
                                                
                                                if (!empty($percepciones_lista)) {
                                                    foreach ($percepciones_lista as $percepcion) {
                                                        $id_per = $percepcion['id_detraccion'];
                                                        $porcentaje = $percepcion['porcentaje'];
                                                        $nombre = htmlspecialchars($percepcion['nombre_detraccion'], ENT_QUOTES);
                                                        ?>
                                                        <div class="form-check" style="margin-bottom: 5px;">
                                                            <input class="form-check-input percepcion-checkbox" 
                                                                type="checkbox" 
                                                                name="id_percepcion[]" 
                                                                value="<?php echo $id_per; ?>" 
                                                                data-porcentaje="<?php echo $porcentaje; ?>" 
                                                                data-nombre="<?php echo $nombre; ?>"
                                                                data-tipo="PERCEPCION"
                                                                onchange="manejarClickPercepcion(this)"
                                                                id="percepcion_<?php echo $id_per; ?>">
                                                            <label class="form-check-label" 
                                                                for="percepcion_<?php echo $id_per; ?>" 
                                                                style="font-size: 12px; cursor: pointer;">
                                                                <?php echo $nombre; ?> 
                                                                <strong>(<?php echo $porcentaje; ?>%)</strong>
                                                            </label>
                                                        </div>
                                                        <?php
                                                    }
                                                } else {
                                                    echo '<p class="text-muted" style="font-size: 11px; margin: 0;"><i class="fa fa-info-circle"></i> No hay percepciones configuradas</p>';
                                                }
                                                ?>
                                            </div>
                                            <small class="form-text text-muted">Se suma al total con IGV</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Campo oculto para enviar la afectación seleccionada -->
                    <input type="hidden" name="afectacion_seleccionada" id="afectacion_seleccionada" value="">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Total a Pagar <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="total_pagar" id="total_pagar" class="form-control" placeholder="0.00" required readonly style="background-color: #e9ecef;">
                        </div>
                    </div>
                    
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Medio de Pago</label>
                            <select name="id_medio_pago" class="form-control">
                                <option value="">Seleccionar...</option>
                                <?php foreach($medios_pago as $mp) { ?>
                                    <option value="<?php echo $mp['id_medio_pago']; ?>">
                                        <?php echo $mp['nom_medio_pago']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha de Pago</label>
                            <input type="date" name="fec_pago" class="form-control">
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Archivo PDF</label>
                            <input type="file" name="archivo_pdf" class="form-control" accept=".pdf">
                            <small class="form-text text-muted">Máximo 5MB</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Archivo XML</label>
                            <input type="file" name="archivo_xml" class="form-control" accept=".xml">
                            <small class="form-text text-muted">Máximo 5MB</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancelar
                </button>
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i> Guardar Comprobante
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================ -->
<!-- MODAL: EDITAR COMPROBANTE -->
<!-- ============================================================ -->
<div class="modal fade" id="modalEditarComprobante" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <form action="comprobante_registrar.php?id_compra=<?php echo $id_compra; ?>" method="post" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title"><i class="fa fa-edit"></i> Editar Comprobante</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id_compra" value="<?php echo $id_compra; ?>">
                <input type="hidden" name="id_comprobante" id="edit_id_comprobante">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tipo de Documento <span class="text-danger">*</span></label>
                            <select name="id_tipo_documento" id="edit_id_tipo_documento" class="form-control" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach($tipos_documento as $td) { ?>
                                    <option value="<?php echo $td['id_tipo_documento']; ?>">
                                        <?php echo $td['nom_tipo_documento']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Serie <span class="text-danger">*</span></label>
                            <input type="text" name="serie" id="edit_serie" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Número <span class="text-danger">*</span></label>
                            <input type="text" name="numero" id="edit_numero" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Monto Total con IGV <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="monto_total_igv" id="edit_monto_total_igv" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Total a Pagar <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="total_pagar" id="edit_total_pagar" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Moneda <span class="text-danger">*</span></label>
                            <select name="id_moneda" id="edit_id_moneda" class="form-control" required>
                                <?php foreach($monedas as $mon) { 
                                    if($mon['id_moneda']==1){
                                        $simbolo_moneda='S/.';
                                    }
                                    else if($mon['id_moneda']==2){
                                        $simbolo_moneda='US$';
                                    }?>
                                    <option value="<?php echo $mon['id_moneda']; ?>">
                                        <?php echo $mon['nom_moneda']; ?> (<?php echo $simbolo_moneda; ?>)
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Detracción</label>
                            <select name="id_detraccion" id="edit_id_detraccion" class="form-control">
                                <option value="">Sin detracción</option>
                                <?php foreach($detracciones as $det) { ?>
                                    <option value="<?php echo $det['id_detraccion']; ?>">
                                        <?php echo $det['nombre_detraccion']; ?> (<?php echo $det['porcentaje']; ?>%)
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Medio de Pago</label>
                            <select name="id_medio_pago" id="edit_id_medio_pago" class="form-control">
                                <option value="">Seleccionar...</option>
                                <?php foreach($medios_pago as $mp) { ?>
                                    <option value="<?php echo $mp['id_medio_pago']; ?>">
                                        <?php echo $mp['nom_medio_pago']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha de Pago</label>
                            <input type="date" name="fec_pago" id="edit_fec_pago" class="form-control">
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Archivo PDF</label>
                            <div id="pdf_actual" class="mb-2"></div>
                            <input type="file" name="archivo_pdf" class="form-control" accept=".pdf">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Archivo XML</label>
                            <div id="xml_actual" class="mb-2"></div>
                            <input type="file" name="archivo_xml" class="form-control" accept=".xml">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancelar
                </button>
                <button type="submit" class="btn btn-warning">
                    <i class="fa fa-save"></i> Actualizar Comprobante
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================ -->
<!-- MODAL: SUBIR VOUCHER DE PAGO -->
<!-- ============================================================ -->
<div class="modal fade" id="modalSubirVoucher" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="comprobante_registrar.php?id_compra=<?php echo $id_compra; ?>" method="post" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fa fa-upload"></i> Subir Voucher de Pago</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="accion" value="subir_voucher">
                <input type="hidden" name="id_comprobante" id="voucher_id_comprobante">
                
                <div class="form-group">
                    <label>Archivo de Voucher <span class="text-danger">*</span></label>
                    <input type="file" name="voucher_pago" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                    <small class="form-text text-muted">Formatos permitidos: PDF, JPG, PNG (Máx. 5MB)</small>
                </div>
                
                <hr>
                
                <div class="form-group">
                    <label><strong>Notificaciones por correo:</strong></label>
                    
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="enviar_proveedor" id="enviar_proveedor" value="1" checked>
                        <label class="form-check-label" for="enviar_proveedor">
                            Enviar al Proveedor
                        </label>
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="enviar_contabilidad" id="enviar_contabilidad" value="1" checked>
                        <label class="form-check-label" for="enviar_contabilidad">
                            Enviar a Contabilidad
                        </label>
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="enviar_tesoreria" id="enviar_tesoreria" value="1" checked>
                        <label class="form-check-label" for="enviar_tesoreria">
                            Enviar a Tesorería
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancelar
                </button>
                <button type="submit" class="btn btn-info">
                    <i class="fa fa-upload"></i> Subir Voucher
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================
     MODAL PARA VER DETALLE DEL COMPROBANTE
============================================= -->
<div class="modal fade" id="modalDetalleComprobante" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fa fa-file-text-o"></i> Información del Comprobante
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="contenidoDetalleComprobante">
                <!-- Aquí se cargará el contenido -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- SCRIPTS -->
<!-- ============================================================ -->
<script>

function manejarClickDetraccion(checkbox) {
    console.log('🔵 Click en DETRACCIÓN');
    
    if (checkbox.checked) {
        document.querySelectorAll('.retencion-checkbox, .percepcion-checkbox').forEach(function(cb) {
            cb.checked = false;
        });
        
        document.querySelectorAll('.detraccion-checkbox').forEach(function(cb) {
            if (cb !== checkbox) {
                cb.checked = false;
            }
        });
        
        document.getElementById('afectacion_seleccionada').value = 'DETRACCION:' + checkbox.value;
    } else {
        document.getElementById('afectacion_seleccionada').value = '';
    }
    
    calcularTotalPagar();
}

function manejarClickRetencion(checkbox) {
    console.log('🟡 Click en RETENCIÓN');
    
    if (checkbox.checked) {
        document.querySelectorAll('.detraccion-checkbox, .percepcion-checkbox').forEach(function(cb) {
            cb.checked = false;
        });
        
        document.querySelectorAll('.retencion-checkbox').forEach(function(cb) {
            if (cb !== checkbox) {
                cb.checked = false;
            }
        });
        
        document.getElementById('afectacion_seleccionada').value = 'RETENCION:' + checkbox.value;
    } else {
        document.getElementById('afectacion_seleccionada').value = '';
    }
    
    calcularTotalPagar();
}

function manejarClickPercepcion(checkbox) {
    console.log('🟢 Click en PERCEPCIÓN');
    
    if (checkbox.checked) {
        document.querySelectorAll('.detraccion-checkbox, .retencion-checkbox').forEach(function(cb) {
            cb.checked = false;
        });
        
        document.querySelectorAll('.percepcion-checkbox').forEach(function(cb) {
            if (cb !== checkbox) {
                cb.checked = false;
            }
        });
        
        document.getElementById('afectacion_seleccionada').value = 'PERCEPCION:' + checkbox.value;
    } else {
        document.getElementById('afectacion_seleccionada').value = '';
    }
    
    calcularTotalPagar();
}

function calcularTotalPagar() {
    var inputMonto = document.querySelector('input[name="monto_total_igv"]');
    var inputTotal = document.getElementById('total_pagar');
    
    if (!inputMonto || !inputTotal) {
        console.error('❌ No se encontraron los campos de entrada');
        return;
    }
    
    var montoConIGV = parseFloat(inputMonto.value) || 0;
    
    if (montoConIGV <= 0) {
        inputTotal.value = '';
        return;
    }
    
    var totalPagar = montoConIGV;
    var tipoAfectacion = null;
    var porcentaje = 0;
    var montoAfectacion = 0;
    
    var detraccionChecked = document.querySelector('.detraccion-checkbox:checked');
    if (detraccionChecked) {
        tipoAfectacion = 'DETRACCION';
        porcentaje = parseFloat(detraccionChecked.getAttribute('data-porcentaje')) || 0;
        montoAfectacion = (montoConIGV * porcentaje) / 100;
        totalPagar = montoConIGV - montoAfectacion;
    }
    
    var retencionChecked = document.querySelector('.retencion-checkbox:checked');
    if (retencionChecked) {
        tipoAfectacion = 'RETENCION';
        porcentaje = parseFloat(retencionChecked.getAttribute('data-porcentaje')) || 0;
        montoAfectacion = (montoConIGV * porcentaje) / 100;
        totalPagar = montoConIGV - montoAfectacion;
    }
    
    var percepcionChecked = document.querySelector('.percepcion-checkbox:checked');
    if (percepcionChecked) {
        tipoAfectacion = 'PERCEPCION';
        porcentaje = parseFloat(percepcionChecked.getAttribute('data-porcentaje')) || 0;
        montoAfectacion = (montoConIGV * porcentaje) / 100;
        totalPagar = montoConIGV + montoAfectacion;
    }
    
    inputTotal.value = totalPagar.toFixed(2);
    
    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    console.log('📊 CÁLCULO DE TOTAL A PAGAR');
    console.log('💰 Monto con IGV: ' + montoConIGV.toFixed(2));
    console.log('📌 Tipo: ' + (tipoAfectacion || 'Ninguna'));
    console.log('📊 Porcentaje: ' + porcentaje + '%');
    console.log('💵 Afectación: ' + montoAfectacion.toFixed(2));
    console.log('✅ Total: ' + totalPagar.toFixed(2));
    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
}

// ====================================================================
// JQUERY READY
// ====================================================================
$(document).ready(function() {
    
    console.log('🚀 Script iniciado correctamente');
    
    // Evento para cambio de monto
    $(document).on('input change keyup', 'input[name="monto_total_igv"]', function() {
        console.log('💰 Monto cambiado: ' + $(this).val());
        calcularTotalPagar();
    });
    
    // Evento para cambio de moneda
    $(document).on('change', 'select[name="id_moneda"]', function() {
        console.log('💱 Moneda cambiada');
        calcularTotalPagar();
    });
    
    // Limpiar al abrir modal
    $('#modalRegistrarComprobante').on('show.bs.modal', function() {
        console.log('🔄 Modal abierto - Limpiando');
        $('.detraccion-checkbox, .retencion-checkbox, .percepcion-checkbox').prop('checked', false);
        $('input[name="monto_total_igv"]').val('');
        $('#total_pagar').val('');
        $('#afectacion_seleccionada').val('');
    });
    
    // Validación
    $('#modalRegistrarComprobante form').on('submit', function(e) {
        var montoIGV = parseFloat($('input[name="monto_total_igv"]').val()) || 0;
        var totalPagar = parseFloat($('#total_pagar').val()) || 0;
        
        if (montoIGV <= 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debe ingresar el Monto Total con IGV'
            });
            return false;
        }
        
        if (totalPagar <= 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El Total a Pagar debe ser mayor a cero'
            });
            return false;
        }
        
        console.log('✅ Formulario validado correctamente');
        return true;
    });
    
    // DataTables con manejo de errores
    try {
        if ($.fn.DataTable.isDataTable('#tablaComprobantes')) {
            $('#tablaComprobantes').DataTable().destroy();
        }
        
        $('#tablaComprobantes').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            "order": [[0, "desc"]],
            "pageLength": 25,
            "responsive": true
        });
        
        console.log('✅ DataTables inicializado correctamente');
    } catch (error) {
        console.error('❌ Error al inicializar DataTables:', error);
    }
        
    console.log('✅ Todo configurado');
});

// ====================================================================
// FUNCIÓN PARA ANULAR COMPROBANTE
// ====================================================================
function AnularComprobante(id_comprobante) {
    Swal.fire({
        title: '¿Seguro que deseas anular este comprobante?',
        text: 'Esta acción no se puede revertir',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, anular',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'comprobante_anular.php',
                type: 'POST',
                data: { id_comprobante: id_comprobante },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Anulado!',
                            text: response.message,
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            window.location.href = 'comprobante_registrar.php?id_compra=<?php echo $id_compra; ?>';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            confirmButtonColor: '#d33'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX:', error);
                    console.error('Respuesta:', xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor.',
                        confirmButtonColor: '#d33'
                    });
                }
            });
        }
    });
}

// ====================================================================
// FUNCIÓN PARA CARGAR DATOS EN MODAL DE EDICIÓN
// ====================================================================
function CargarModalEditar(id_comprobante) {
    $.ajax({
        url: 'comprobante_consultar.php',
        type: 'POST',
        data: { id_comprobante: id_comprobante },
        dataType: 'json',
        success: function(data) {
            if (data.error) {
                Swal.fire('Error', data.error, 'error');
                return;
            }
            
            // Llenar campos del modal
            $('#edit_id_comprobante').val(data.id_comprobante);
            $('#edit_id_tipo_documento').val(data.id_tipo_documento);
            $('#edit_serie').val(data.serie);
            $('#edit_numero').val(data.numero);
            $('#edit_monto_total_igv').val(data.monto_total_igv);
            $('#edit_id_detraccion').val(data.id_detraccion || '');
            $('#edit_total_pagar').val(data.total_pagar);
            $('#edit_id_moneda').val(data.id_moneda);
            $('#edit_id_medio_pago').val(data.id_medio_pago || '');
            $('#edit_fec_pago').val(data.fec_pago || '');
            
            // Mostrar archivos actuales
            if (data.archivo_pdf) {
                $('#pdf_actual').html('<a href="../_upload/comprobantes/' + data.archivo_pdf + '" target="_blank" class="badge badge-danger"><i class="fa fa-file-pdf-o"></i> Ver PDF actual</a>');
            } else {
                $('#pdf_actual').html('<span class="text-muted">Sin archivo</span>');
            }
            
            if (data.archivo_xml) {
                $('#xml_actual').html('<a href="../_upload/comprobantes/' + data.archivo_xml + '" target="_blank" class="badge badge-info"><i class="fa fa-file-code-o"></i> Ver XML actual</a>');
            } else {
                $('#xml_actual').html('<span class="text-muted">Sin archivo</span>');
            }
            
            // Abrir modal
            $('#modalEditarComprobante').modal('show');
        },
        error: function() {
            Swal.fire('Error', 'No se pudo cargar la información del comprobante', 'error');
        }
    });
}

// ====================================================================
// FUNCIÓN PARA ABRIR MODAL DE SUBIR VOUCHER
// ====================================================================
function AbrirModalVoucher(id_comprobante) {
    $('#voucher_id_comprobante').val(id_comprobante);
    $('#modalSubirVoucher').modal('show');
}

// ====================================================================
// FUNCIÓN PARA VER DETALLES DEL COMPROBANTE - CON MODAL BOOTSTRAP
// ====================================================================
function VerDetalleComprobante(id_comprobante) {
    $.ajax({
        url: 'comprobante_consultar.php',
        type: 'POST',
        data: { id_comprobante: id_comprobante },
        dataType: 'json',
        beforeSend: function() {
            $('#contenidoDetalleComprobante').html(`
                <div class="text-center py-3">
                    <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
                </div>
            `);
            $('#modalDetalleComprobante').modal('show');
        },
        success: function(data) {
            if (data.error) {
                $('#contenidoDetalleComprobante').html(`
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-triangle"></i> ${data.error}
                    </div>
                `);
                return;
            }
            
            let subtotal = parseFloat(data.monto_total_igv > 0 ? data.total_pagar - data.monto_total_igv : data.total_pagar);
            
            let html = `
                <div style="font-family: Arial, sans-serif;">
                    <!-- DATOS -->
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th width="35%" style="background-color: #f8f9fa;">Tipo Documento:</th>
                            <td>${data.nom_tipo_documento}</td>
                        </tr>
                        <tr>
                            <th style="background-color: #f8f9fa;">Serie - Número:</th>
                            <td><strong>${data.num_comprobante}</strong></td>
                        </tr>
                        <tr>
                            <th style="background-color: #f8f9fa;">Proveedor:</th>
                            <td>${data.nom_proveedor}</td>
                        </tr>
                        <tr>
                            <th style="background-color: #f8f9fa;">RUC:</th>
                            <td>${data.ruc_proveedor}</td>
                        </tr>
                        <tr>
                            <th style="background-color: #f8f9fa;">Medio de Pago:</th>
                            <td>${data.nom_medio_pago || 'No especificado'}</td>
                        </tr>
                        <tr>
                            <th style="background-color: #f8f9fa;">Fecha de Pago:</th>
                            <td>${data.fec_pago || '<span class="text-warning">Pendiente</span>'}</td>
                        </tr>
                        <tr>
                            <th style="background-color: #f8f9fa;">Total con IGV (18%):</th>
                            <td>${data.simbolo_moneda} ${parseFloat(data.monto_total_igv || 0).toFixed(2)}</td>
                        </tr>
                        <tr>
                            <th style="background-color: #f8f9fa;">Detracción:</th>
                            <td>${data.simbolo_moneda} ${subtotal.toFixed(2)}</td>
                        </tr>
                        
                        <tr style="background-color: #d4edda;">
                            <th style="font-size: 16px;">TOTAL:</th>
                            <td style="font-size: 18px; font-weight: bold; color: #155724;">
                                ${data.simbolo_moneda} ${parseFloat(data.total_pagar).toFixed(2)}
                            </td>
                        </tr>
                        
                    </table>
                    
                    <!-- ARCHIVOS -->
                    <div style="background-color: #f8f9fa; padding: 12px; border-radius: 5px; margin-top: 15px;">
                        <div style="display: flex; align-items: center;">
                            <strong style="margin: 0; min-width: 35%; flex-shrink: 0;">
                                <i class="fa fa-paperclip"></i> Archivos Adjuntos:
                            </strong>
                            <div style="flex: 1;">
                                ${data.archivo_pdf ? 
                                    '<a href="../_upload/comprobantes/' + data.archivo_pdf + '" target="_blank" class="btn btn-sm btn-danger" style="margin-right: 8px;"><i class="fa fa-file-pdf-o"></i> PDF</a>' : 
                                    '<span class="badge badge-secondary">Sin PDF</span>'}
                                ${data.archivo_xml ? 
                                    '<a href="../_upload/comprobantes/' + data.archivo_xml + '" target="_blank" class="btn btn-sm btn-info" style="margin-right: 8px;"><i class="fa fa-file-code-o"></i> XML</a>' : 
                                    ''}
                                ${data.voucher_pago ? 
                                    '<a href="../_upload/vouchers/' + data.voucher_pago + '" target="_blank" class="btn btn-sm btn-success"><i class="fa fa-check-circle"></i> Voucher</a>' : 
                                    ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('#contenidoDetalleComprobante').html(html);
        },
        error: function() {
            $('#contenidoDetalleComprobante').html(`
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-circle"></i> No se pudo cargar la información
                </div>
            `);
        }
    });
}

</script>
