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
                                    <strong style="font-size: 16px;">MONTO TOTAL CON IGV:</strong>
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
                                    <br>
                                </td>
                                <td colspan="2" class="text-danger" style="font-weight: bold;">
                                    -<?php echo ($oc['simbolo_moneda'] ?? 'S/.') . ' ' . number_format($oc['monto_detraccion'], 2); ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            
                            <!--  TOTAL A PAGAR AL PROVEEDOR (más destacado) -->
                            <tr style="background-color: #e3f2fd;">
                                <td colspan="2"><strong> TOTAL:</strong>
                                    <br><small class="text-muted">Monto que recibe el proveedor</small>
                                </td>
                                <td colspan="2">
                                    <span class="badge badge-info" style="font-size: 15px; padding: 8px 14px;">
                                        <?php echo ($oc['simbolo_moneda'] ?? 'S/.') . ' ' . number_format($oc['monto_total'], 2); ?>
                                    </span>
                                </td>
                            </tr>
                            
                            <tr>
                                <td><strong> Pagado:</strong></td>
                                <td><?php echo ($oc['simbolo_moneda'] ?? 'S/.') . ' ' . number_format($oc['monto_pagado'], 2); ?></td>
                                <td><strong> Saldo Pendiente:</strong></td>
                                <td>
                                    <span class="badge badge-<?php echo ($oc['saldo'] > 0 ? 'warning' : 'success'); ?>" style="font-size: 14px; padding: 6px 12px;">
                                        <?php echo ($oc['simbolo_moneda'] ?? 'S/.') . ' ' . number_format($oc['saldo'], 2); ?>
                                    </span>
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
                            <button type="button" class="btn btn-primary" onclick="abrirModalMasivo()">📤 Subir vouchers masivo</button>
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

<!-- MODAL SIMPLE -->
<div id="modalSubidaMasivo" 
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            background:rgba(0,0,0,0.5); z-index:1040; justify-content:center; align-items:center;">
    
    <div style="background:white; width:85%; max-width:950px; border-radius:10px; 
                display:flex; flex-direction:column; max-height:90vh; overflow:hidden;">

        <!-- CABECERA -->
        <div style="background:#667eea; color:white; padding:10px 20px; 
                    display:flex; justify-content:space-between; align-items:center;">
            <h5 style="margin:0;">Subida Masiva de Vouchers</h5>
            <button onclick="cerrarModalMasivo()" 
                    style="background:none; border:none; color:white; font-size:20px;">&times;</button>
        </div>

        <!-- CONTENIDO SCROLLEABLE -->
        <div style="padding:20px; overflow-y:auto; flex:1;">

            <!-- CONTENEDOR PRINCIPAL -->
            <div style="display:flex; flex-wrap:wrap; gap:20px; align-items:stretch;">
                
                <!-- INSTRUCCIONES -->
                <div style="flex:1; min-width:300px; background:#f1f3f5; border-radius:8px; padding:15px;">
                    <strong>Instrucciones:</strong>
                    <ol style="margin:5px 0 0 20px; font-size:14px; color:#333;">
                        <li>Archivos deben llamarse como la serie-número del comprobante</li>
                        <li>Ej: <code>F001-1234.pdf</code>, <code>B002-5678.jpg</code></li>
                        <li>Formatos: PDF, JPG, JPEG, PNG</li>
                        <li>Máx. 5MB por archivo</li>
                    </ol>
                </div>

                <!-- ZONA DE CARGA -->
                <div style="flex:1; min-width:300px; display:flex; align-items:center; justify-content:center;">
                    <div id="dropZone" onclick="document.getElementById('inputArchivos').click()"
                        style="border:2px dashed #667eea; border-radius:8px; padding:25px; text-align:center;
                               background:#f9f9fc; cursor:pointer; transition:0.3s; width:100%;">
                        <i class="fa fa-cloud-upload" style="font-size:40px; color:#667eea; margin-bottom:8px;"></i>
                        <h5 style="color:#667eea; font-size:16px; margin-bottom:5px;">Haz clic para seleccionar archivos</h5>
                        <p style="color:#6c757d; font-size:13px; margin:0;">(Puedes seleccionar varios a la vez)</p>
                        <input type="file" id="inputArchivos" multiple style="display:none" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                </div>
            </div>

            <!-- LISTA CON SCROLL -->
            <div id="listaArchivos" 
                 style="margin-top:25px; max-height:250px; overflow-y:auto; border:1px solid #e0e0e0; border-radius:6px; padding:10px;">
            </div>

            <!-- OPCIONES DE CORREO -->
            <div style="margin-top:20px; background:#f8f9fa; padding:15px; border-radius:8px;">
                <h6><i class="fa fa-envelope"></i> Notificaciones:</h6>
                <label><input type="checkbox" id="enviarProveedor" checked> Enviar al Proveedor</label><br>
                <label><input type="checkbox" id="enviarContabilidad" checked> Enviar a Contabilidad</label><br>
                <label><input type="checkbox" id="enviarTesoreria" checked> Enviar a Tesorería</label>
            </div>
        </div>

        <!-- PIE FIJO -->
        <div style="padding:10px 20px; text-align:right; border-top:1px solid #ddd; background:#fff;">
            <button class="btn btn-secondary" onclick="cerrarModalMasivo()">Cancelar</button>
            <button class="btn btn-primary" id="btnProcesarMasivo" onclick="procesarArchivos()">Procesar</button>
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
                            <input name="monto_total_igv" id="monto_total_igv" class="form-control" placeholder="0.00" required>
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Medio de Pago</label>
                            <select name="id_medio_pago" id="id_medio_pago" class="form-control">
                                <option value="">Seleccionar...</option>
                                <?php foreach($medios_pago as $mp) { ?>
                                    <option value="<?php echo $mp['id_medio_pago']; ?>">
                                        <?php echo $mp['nom_medio_pago']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Fecha de Pago</label>
                            <input type="date" name="fec_pago" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-6">                    
                        <div class="form-group">
                            <label for="id_cuenta_proveedor">Cuenta bancaria</label>
                            <select name="id_cuenta_proveedor" id="id_cuenta_proveedor" class="form-control" disabled>
                                <option value="">-- Primero selecciona un medio de pago --</option>
                                <?php if (!empty($oc['cuentas'])): ?>
                                    <?php foreach ($oc['cuentas'] as $cuenta): ?>
                                        <option value="<?php echo $cuenta['id_proveedor_cuenta']; ?>">
                                            <?php echo htmlspecialchars($cuenta['banco_proveedor'] . ' - ' . $cuenta['nro_cuenta_corriente']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">Sin cuentas registradas</option>
                                <?php endif; ?>
                            </select>
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
                            <input type="text" name="serie" id="edit_serie" class="form-control" placeholder="Ej: F001" required>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Número <span class="text-danger">*</span></label>
                            <input type="text" name="numero" id="edit_numero" class="form-control" placeholder="Ej: 00001234" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Monto Total con IGV <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="monto_total_igv" id="edit_monto_total_igv" class="form-control" placeholder="0.00" required>
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
                            <input type="hidden" name="id_moneda" id="edit_id_moneda" value="<?php echo $oc['id_moneda']; ?>">

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
                                                            <input class="form-check-input detraccion-checkbox-edit" 
                                                                type="checkbox" 
                                                                name="id_detraccion[]" 
                                                                value="<?php echo $id_det; ?>" 
                                                                data-porcentaje="<?php echo $porcentaje; ?>" 
                                                                data-nombre="<?php echo $nombre; ?>"
                                                                data-tipo="DETRACCION"
                                                                
                                                                id="edit_detraccion_<?php echo $id_det; ?>">
                                                            <label class="form-check-label" 
                                                                for="edit_detraccion_<?php echo $id_det; ?>" 
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
                                                            <input class="form-check-input retencion-checkbox-edit" 
                                                                type="checkbox" 
                                                                name="id_retencion[]" 
                                                                value="<?php echo $id_ret; ?>" 
                                                                data-porcentaje="<?php echo $porcentaje; ?>" 
                                                                data-nombre="<?php echo $nombre; ?>"
                                                                data-tipo="RETENCION"
                                                                
                                                                id="edit_retencion_<?php echo $id_ret; ?>">
                                                            <label class="form-check-label" 
                                                                for="edit_retencion_<?php echo $id_ret; ?>" 
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
                                                            <input class="form-check-input percepcion-checkbox-edit" 
                                                                type="checkbox" 
                                                                name="id_percepcion[]" 
                                                                value="<?php echo $id_per; ?>" 
                                                                data-porcentaje="<?php echo $porcentaje; ?>" 
                                                                data-nombre="<?php echo $nombre; ?>"
                                                                data-tipo="PERCEPCION"
                                                                
                                                                id="edit_percepcion_<?php echo $id_per; ?>">
                                                            <label class="form-check-label" 
                                                                for="edit_percepcion_<?php echo $id_per; ?>" 
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
                    <input type="hidden" name="afectacion_seleccionada" id="edit_afectacion_seleccionada" value="">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Total a Pagar <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="total_pagar" id="edit_total_pagar" class="form-control" placeholder="0.00" required readonly style="background-color: #e9ecef;">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-3">
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
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Fecha de Pago</label>
                            <input type="date" name="fec_pago" id="edit_fec_pago" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="edit_id_cuenta_proveedor">Cuenta bancaria</label>
                            <select name="edit_id_cuenta_proveedor" id="edit_id_cuenta_proveedor" class="form-control" disabled>
                                <option value="">-- Primero selecciona un medio de pago --</option>
                                <?php if (!empty($oc['cuentas'])): ?>
                                    <?php foreach ($oc['cuentas'] as $cuenta): ?>
                                        <option value="<?php echo $cuenta['id_proveedor_cuenta']; ?>">
                                            <?php echo htmlspecialchars($cuenta['banco_proveedor'] . ' - ' . $cuenta['nro_cuenta_corriente']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">Sin cuentas registradas</option>
                                <?php endif; ?>
                            </select>
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


// Variables globales
let archivosSeleccionados = [];
// ====================================================================
// ESPERAR A QUE JQUERY ESTÉ LISTO
// ====================================================================
(function() {
    'use strict';
    
    console.log('🚀 Iniciando sistema...');

    // ====================================================================
    // FUNCIÓN: CALCULAR TOTAL A PAGAR
    // ====================================================================
    window.calcularTotalPagar = function(isEditMode) {
        console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        console.log('💰 CALCULANDO - Modo:', isEditMode ? 'EDICIÓN' : 'REGISTRO');
        
        const prefix = isEditMode ? 'edit_' : '';
        const suffix = isEditMode ? '-edit' : '';
        
        const inputMonto = document.getElementById(prefix + 'monto_total_igv');
        const inputTotal = document.getElementById(prefix + 'total_pagar');
        
        if (!inputMonto || !inputTotal) {
            console.error('❌ ERROR: Campos no encontrados');
            return;
        }
        
        const montoConIGV = parseFloat(inputMonto.value) || 0;
        console.log('📊 Monto con IGV:', montoConIGV);
        
        if (montoConIGV <= 0) {
            console.log('⚠️ Monto vacío - Limpiando total');
            inputTotal.value = '';
            return;
        }
        
        let totalPagar = montoConIGV;
        let tipoAfectacion = null;
        let porcentaje = 0;
        let montoAfectacion = 0;
        
        // Buscar checkbox marcado
        const detraccionChecked = document.querySelector('.detraccion-checkbox' + suffix + ':checked');
        const retencionChecked = document.querySelector('.retencion-checkbox' + suffix + ':checked');
        const percepcionChecked = document.querySelector('.percepcion-checkbox' + suffix + ':checked');
        
        if (detraccionChecked) {
            tipoAfectacion = 'DETRACCION';
            porcentaje = parseFloat(detraccionChecked.getAttribute('data-porcentaje')) || 0;
            montoAfectacion = (montoConIGV * porcentaje) / 100;
            totalPagar = montoConIGV - montoAfectacion;
            console.log('🔵 DETRACCIÓN:', porcentaje + '%');
        } else if (retencionChecked) {
            tipoAfectacion = 'RETENCION';
            porcentaje = parseFloat(retencionChecked.getAttribute('data-porcentaje')) || 0;
            montoAfectacion = (montoConIGV * porcentaje) / 100;
            totalPagar = montoConIGV - montoAfectacion;
            console.log('🟡 RETENCIÓN:', porcentaje + '%');
        } else if (percepcionChecked) {
            tipoAfectacion = 'PERCEPCION';
            porcentaje = parseFloat(percepcionChecked.getAttribute('data-porcentaje')) || 0;
            montoAfectacion = (montoConIGV * porcentaje) / 100;
            totalPagar = montoConIGV + montoAfectacion;
            console.log('🟢 PERCEPCIÓN:', porcentaje + '%');
        } else {
            console.log('⚪ Sin afectaciones');
        }
        
        inputTotal.value = totalPagar.toFixed(2);
        
        console.log('✅ RESULTADO:');
        console.log('   - Monto IGV:', montoConIGV.toFixed(2));
        console.log('   - Tipo:', tipoAfectacion || 'Ninguna');
        console.log('   - Porcentaje:', porcentaje + '%');
        console.log('   - Afectación:', montoAfectacion.toFixed(2));
        console.log('   - TOTAL:', totalPagar.toFixed(2));
        console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    };

    // ====================================================================
    // FUNCIÓN: MANEJAR CHECKBOXES
    // ====================================================================
    window.manejarCheckbox = function(checkbox, tipo, isEditMode) {
        console.log('📌 CLICK ' + tipo + ' - Modo:', isEditMode ? 'EDICIÓN' : 'REGISTRO');
        
        const suffix = isEditMode ? '-edit' : '';
        const prefix = isEditMode ? 'edit_' : '';
        
        if (checkbox.checked) {
            console.log('   ✓ Marcando:', checkbox.value);
            
            // Desmarcar todos los demás
            const allCheckboxes = document.querySelectorAll(
                '.detraccion-checkbox' + suffix + ', ' +
                '.retencion-checkbox' + suffix + ', ' +
                '.percepcion-checkbox' + suffix
            );
            
            allCheckboxes.forEach(function(cb) {
                if (cb !== checkbox) {
                    cb.checked = false;
                }
            });
            
            const hiddenInput = document.getElementById(prefix + 'afectacion_seleccionada');
            if (hiddenInput) {
                hiddenInput.value = tipo + ':' + checkbox.value;
            }
        } else {
            console.log('   ✗ Desmarcando');
            const hiddenInput = document.getElementById(prefix + 'afectacion_seleccionada');
            if (hiddenInput) {
                hiddenInput.value = '';
            }
        }
        
        calcularTotalPagar(isEditMode);
    };

    // ====================================================================
    // INICIALIZAR CUANDO EL DOM ESTÉ LISTO
    // ====================================================================
    function inicializar() {
        console.log('✅ DOM listo - Configurando eventos...');
        
        // EVENTOS: MONTO REGISTRO
        const montoRegistro = document.getElementById('monto_total_igv');
        if (montoRegistro) {
            montoRegistro.addEventListener('input', function() {
                console.log('💰 Monto REGISTRO cambiado:', this.value);
                calcularTotalPagar(false);
            });
        }
        
        // EVENTOS: MONTO EDICIÓN
        const montoEdicion = document.getElementById('edit_monto_total_igv');
        if (montoEdicion) {
            montoEdicion.addEventListener('input', function() {
                console.log('💰 Monto EDICIÓN cambiado:', this.value);
                calcularTotalPagar(true);
            });
        }
        
        // EVENTOS: CHECKBOXES REGISTRO
        document.querySelectorAll('.detraccion-checkbox').forEach(function(cb) {
            cb.addEventListener('change', function() {
                manejarCheckbox(this, 'DETRACCION', false);
            });
        });
        
        document.querySelectorAll('.retencion-checkbox').forEach(function(cb) {
            cb.addEventListener('change', function() {
                manejarCheckbox(this, 'RETENCION', false);
            });
        });
        
        document.querySelectorAll('.percepcion-checkbox').forEach(function(cb) {
            cb.addEventListener('change', function() {
                manejarCheckbox(this, 'PERCEPCION', false);
            });
        });
        
        // EVENTOS: CHECKBOXES EDICIÓN
        document.querySelectorAll('.detraccion-checkbox-edit').forEach(function(cb) {
            cb.addEventListener('change', function() {
                manejarCheckbox(this, 'DETRACCION', true);
            });
        });
        
        document.querySelectorAll('.retencion-checkbox-edit').forEach(function(cb) {
            cb.addEventListener('change', function() {
                manejarCheckbox(this, 'RETENCION', true);
            });
        });
        
        document.querySelectorAll('.percepcion-checkbox-edit').forEach(function(cb) {
            cb.addEventListener('change', function() {
                manejarCheckbox(this, 'PERCEPCION', true);
            });
        });

        // --- CONTROL CUENTA (inicializar y manejar cambios) ---
        function setupCuentaControl() {
            const medio = document.getElementById('id_medio_pago') || document.querySelector('select[name="id_medio_pago"]');
            const cuenta = document.getElementById('id_cuenta_proveedor');
            if (!medio || !cuenta) return false;

            function aplicarEstado() {
                if ((medio.value || '').toString().trim() === '2') {
                    cuenta.disabled = false;
                    cuenta.required = true;
                    cuenta.style.backgroundColor = '#ffffff';
                } else {
                    cuenta.disabled = true;
                    cuenta.required = false;
                    cuenta.value = '';
                    cuenta.style.backgroundColor = '#e9ecef';
                }
            }

            // aplicar ahora y en cambios
            aplicarEstado();
            medio.removeEventListener('change', aplicarEstado);
            medio.addEventListener('change', aplicarEstado);

            // exponer por si lo necesitamos llamar desde fuera (ej. after AJAX)
            window.setupCuentaControl = setupCuentaControl;
            return true;
        }

        // --- CONTROL CUENTA en modo EDICIÓN ---
        function setupCuentaControlEdit() {
            const medio = document.getElementById('edit_id_medio_pago');
            const cuenta = document.getElementById('edit_id_cuenta_proveedor');
            if (!medio || !cuenta) return false;

            function aplicarEstadoEdit() {
                if ((medio.value || '').toString().trim() === '2') {
                    cuenta.disabled = false;
                    cuenta.required = true;
                    cuenta.style.backgroundColor = '#ffffff';
                } else {
                    cuenta.disabled = true;
                    cuenta.required = false;
                    cuenta.value = '';
                    cuenta.style.backgroundColor = '#e9ecef';
                }
            }

            aplicarEstadoEdit(); // aplicar al cargar
            medio.removeEventListener('change', aplicarEstadoEdit);
            medio.addEventListener('change', aplicarEstadoEdit);

            // expone la función si la necesitas luego
            window.setupCuentaControlEdit = setupCuentaControlEdit;
            return true;
        }

        // llamar en inicializar
        setupCuentaControl();
        setupCuentaControlEdit();
        
        console.log('✅ Eventos configurados correctamente');
    }

    // Ejecutar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializar);
    } else {
        inicializar();
    }

})();

// ====================================================================
// FUNCIONES GLOBALES PARA BOTONES
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
                            location.reload();
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
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor.'
                    });
                }
            });
        }
    });
}

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
            
            console.log('📝 Cargando datos para edición:', data);
            
            // Limpiar checkboxes
            document.querySelectorAll('.detraccion-checkbox-edit, .retencion-checkbox-edit, .percepcion-checkbox-edit').forEach(function(cb) {
                cb.checked = false;
            });
            
            // Llenar campos
            document.getElementById('edit_id_comprobante').value = data.id_comprobante;
            document.getElementById('edit_id_tipo_documento').value = data.id_tipo_documento;
            document.getElementById('edit_serie').value = data.serie;
            document.getElementById('edit_numero').value = data.numero;
            document.getElementById('edit_monto_total_igv').value = data.monto_total_igv;
            document.getElementById('edit_id_moneda').value = data.id_moneda;
            
            if (data.id_medio_pago) {
                document.getElementById('edit_id_medio_pago').value = data.id_medio_pago;
                setupCuentaControlEdit();
            }
            
            if (data.fec_pago) {
                document.getElementById('edit_fec_pago').value = data.fec_pago;
            }
            
            // Marcar checkbox si existe
            if (data.id_detraccion) {
                const checkbox = document.getElementById('edit_detraccion_' + data.id_detraccion);
                if (checkbox) {
                    checkbox.checked = true;
                    document.getElementById('edit_afectacion_seleccionada').value = 'DETRACCION:' + data.id_detraccion;
                }
            }
            
            // Calcular total
            setTimeout(function() {
                calcularTotalPagar(true);
            }, 100);
            
            // Archivos
            if (data.archivo_pdf) {
                document.getElementById('pdf_actual').innerHTML = '<a href="../_upload/comprobantes/' + data.archivo_pdf + '" target="_blank" class="badge badge-danger"><i class="fa fa-file-pdf-o"></i> Ver PDF</a>';
            } else {
                document.getElementById('pdf_actual').innerHTML = '<span class="text-muted">Sin archivo</span>';
            }
            
            if (data.archivo_xml) {
                document.getElementById('xml_actual').innerHTML = '<a href="../_upload/comprobantes/' + data.archivo_xml + '" target="_blank" class="badge badge-info"><i class="fa fa-file-code-o"></i> Ver XML</a>';
            } else {
                document.getElementById('xml_actual').innerHTML = '<span class="text-muted">Sin archivo</span>';
            }
            
            $('#modalEditarComprobante').modal('show');
        },
        error: function() {
            Swal.fire('Error', 'No se pudo cargar la información', 'error');
        }
    });
}

function AbrirModalVoucher(id_comprobante) {
    document.getElementById('voucher_id_comprobante').value = id_comprobante;
    $('#modalSubirVoucher').modal('show');
}

function VerDetalleComprobante(id_comprobante) {
    $.ajax({
        url: 'comprobante_consultar.php',
        type: 'POST',
        data: { id_comprobante: id_comprobante },
        dataType: 'json',
        beforeSend: function() {
            $('#contenidoDetalleComprobante').html('<div class="text-center py-3"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i></div>');
            $('#modalDetalleComprobante').modal('show');
        },
        success: function(data) {
            if (data.error) {
                $('#contenidoDetalleComprobante').html('<div class="alert alert-danger">' + data.error + '</div>');
                return;
            }

            let subtotal = parseFloat(data.monto_total_igv > 0 ? data.total_pagar - data.monto_total_igv : data.total_pagar);
            
            let html = `
                <table class="table table-sm table-bordered">
                    <tr><th width="35%">Tipo Documento:</th><td>${data.nom_tipo_documento}</td></tr>
                    <tr><th>Serie - Número:</th><td><strong>${data.num_comprobante}</strong></td></tr>
                    <tr><th>Proveedor:</th><td>${data.nom_proveedor}</td></tr>
                    <tr><th>RUC:</th><td>${data.ruc_proveedor}</td></tr>
                    <tr><th>Cuenta Depósito:</th><td>${data.nro_cuenta_proveedor}</td></tr>
                    <tr><th>Medio de Pago:</th><td>${data.nom_medio_pago || 'No especificado'}</td></tr>
                    <tr><th>Fecha de Pago:</th><td>${data.fec_pago || 'Pendiente'}</td></tr>
                    <tr><th>Monto con IGV:</th><td>${data.simbolo_moneda} ${parseFloat(data.monto_total_igv).toFixed(2)}</td></tr>
                    <tr><th>Detracción:</th><td>${data.simbolo_moneda} ${subtotal.toFixed(2)}</td>
</tr>
                    <tr style="background-color: #d4edda;"><th>TOTAL:</th><td><strong>${data.simbolo_moneda} ${parseFloat(data.total_pagar).toFixed(2)}</strong></td></tr>
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
            `;
            
            $('#contenidoDetalleComprobante').html(html);
        },
        error: function() {
            $('#contenidoDetalleComprobante').html('<div class="alert alert-danger">No se pudo cargar</div>');
        }
    });
}

// ====================================================================
// DATATABLES (SI EXISTE JQUERY)
// ====================================================================
$(document).ready(function() {
    if ($.fn.DataTable) {
        $('#tablaComprobantes').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            "order": [[0, "desc"]],
            "pageLength": 25
        });
    }
});


// Abrir / cerrar modal
function abrirModalMasivo() {
    const modal = document.getElementById("modalSubidaMasivo");
    if (modal) {
        modal.style.display = "flex";
        console.log("✅ Modal abierto");
        
        // Reinicializar el input cuando se abre el modal
        setTimeout(() => {
            inicializarModalMasivo();
        }, 200);
    } else {
        console.error("❌ Modal no encontrado");
    }
}

function cerrarModalMasivo() {
    const modal = document.getElementById("modalSubidaMasivo");
    if (modal) {
        modal.style.display = "none";
        archivosSeleccionados = [];
        document.getElementById("listaArchivos").innerHTML = "";
    }
}

// Esperar a que el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarModalMasivo);
} else {
    inicializarModalMasivo();
}

function inicializarModalMasivo() {
    console.log("🚀 Inicializando modal masivo...");
    
    // Esperar un momento para que el DOM esté completamente listo
    setTimeout(() => {
        const inputArchivos = document.getElementById("inputArchivos");
        
        if (!inputArchivos) {
            console.error("❌ Input de archivos no encontrado");
            return;
        }
        
        console.log("✅ Input encontrado:", inputArchivos);
        
        // Remover listeners anteriores y agregar uno nuevo
        inputArchivos.removeEventListener("change", manejarCambioArchivos);
        inputArchivos.addEventListener("change", manejarCambioArchivos);
        
        console.log("✅ Listener agregado correctamente");
    }, 100);
}

function manejarCambioArchivos(e) {
    /*console.log("📂 ¡¡¡CAMBIO DETECTADO!!!");
    console.log("Archivos seleccionados:", e.target.files.length);
    
    if (e.target.files.length === 0) {
        console.log("⚠️ No hay archivos");
        return;
    }
    
    const nuevosArchivos = Array.from(e.target.files);
    console.log("Array creado:", nuevosArchivos);
    
    nuevosArchivos.forEach((archivo) => {
        console.log("Procesando:", archivo.name, archivo.size, "bytes");
        if (!archivosSeleccionados.find(a => a.name === archivo.name)) {
            archivosSeleccionados.push(archivo);
            console.log("✅ Agregado a la lista");
        } else {
            console.log("⚠️ Duplicado, ignorado");
        }
    });*/

    console.log("📂 ¡¡¡CAMBIO DETECTADO!!!");
    const nuevosArchivos = Array.from(e.target.files);
    if (nuevosArchivos.length === 0) return;

    nuevosArchivos.forEach((archivo) => {
        const nombre = archivo.name.trim();
        const tamañoMB = archivo.size / (1024 * 1024);

        // 1️⃣ Validar tamaño (máximo 5 MB)
        if (tamañoMB > 5) {
            mostrarAlerta(
                'error',
                'Archivo demasiado grande',
                `El archivo "${nombre}" pesa ${(tamañoMB).toFixed(2)} MB. El máximo permitido es 5 MB.`
            );
            return;
        }

        // 2️⃣ Validar formato de nombre (0000-00000000)
        const regexNombre = /^[A-Z0-9]{4}-\d{2,8}\.[A-Za-z0-9]+$/i; // permite letras o números antes del guion
        if (!regexNombre.test(nombre)) {
            mostrarAlerta(
                'warning',
                'Formato inválido',
                `El archivo "${nombre}" no cumple el formato "SERIE-NUMERO", por ejemplo: "F001-00012345.pdf"`
            );
            return;
        }

        // 3️⃣ Evitar duplicados y agregar
        if (!archivosSeleccionados.find(a => a.name === nombre)) {
            archivosSeleccionados.push(archivo);
            console.log("✅ Archivo agregado:", nombre);
        } else {
            mostrarAlerta(
                'info',
                'Archivo duplicado',
                `El archivo "${nombre}" ya fue agregado.`
            );
            console.log("⚠️ Duplicado, ignorado:", nombre);
        }
    });

    console.log("Total en memoria:", archivosSeleccionados.length);
    actualizarListaArchivos();
    e.target.value = "";
}

function actualizarListaArchivos() {
    console.log("📋 Actualizando lista, total:", archivosSeleccionados.length);
    
    const listaArchivos = document.getElementById("listaArchivos");
    
    if (archivosSeleccionados.length === 0) {
        listaArchivos.innerHTML = "";
        return;
    }

    let html = `
        <h6><i class="fa fa-list"></i> Archivos seleccionados (${archivosSeleccionados.length}):</h6>
        <table class="table table-sm table-bordered" style="margin-top:10px;">
            <thead style="background:#667eea; color:white;">
                <tr>
                    <th width="5%">#</th>
                    <th>Nombre</th>
                    <th width="15%">Tamaño</th>
                    <th width="10%">Acción</th>
                </tr>
            </thead>
            <tbody>
    `;

    archivosSeleccionados.forEach((archivo, index) => {
        html += `
            <tr>
                <td>${index + 1}</td>
                <td><i class="fa fa-file-o"></i> ${archivo.name}</td>
                <td>${(archivo.size / 1024).toFixed(1)} KB</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-danger" onclick="eliminarArchivo(${index})">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    html += `</tbody></table></div>`;
    listaArchivos.innerHTML = html;
}

function eliminarArchivo(index) {
    archivosSeleccionados.splice(index, 1);
    actualizarListaArchivos();
}

async function procesarArchivos() {
    if (archivosSeleccionados.length === 0) {
        Swal.fire('Advertencia', 'Selecciona al menos un archivo', 'warning');
        return;
    }

    const formData = new FormData();
    archivosSeleccionados.forEach((archivo) => {
        formData.append('archivos[]', archivo);
    });

    formData.append('enviar_proveedor', document.getElementById('enviarProveedor').checked ? 1 : 0);
    formData.append('enviar_contabilidad', document.getElementById('enviarContabilidad').checked ? 1 : 0);
    formData.append('enviar_tesoreria', document.getElementById('enviarTesoreria').checked ? 1 : 0);

    try {
        Swal.fire({
            title: 'Procesando...',
            text: 'Subiendo vouchers, por favor espera...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        // 👇 ruta relativa (sube una carpeta y entra a _controlador)
        const response = await fetch('../_controlador/comprobante_subida_masiva.php', {
            method: 'POST',
            body: formData
        });

        console.log("🔍 Estado HTTP:", response.status);
        if (!response.ok) {
            throw new Error(`Error HTTP ${response.status}`);
        }

        const data = await response.json();
        console.log("📥 Respuesta del servidor:", data);

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Subida completada',
                html: `<b>${data.exitosos}</b> archivos subidos correctamente.<br>
                       <b>${data.fallidos}</b> archivos fallaron.`,
                confirmButtonText: 'Aceptar'
            }).then(() => {
                cerrarModalMasivo();
            });
        } else {
            Swal.fire('Error', data.mensaje || 'Ocurrió un error en el servidor', 'error');
        }
    } catch (error) {
        console.error('❌ Error al enviar archivos:', error);
        Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
    }
}

</script>

<style>
    #listaArchivos {
        max-height: 300px; /* o el valor que prefieras */
        overflow-y: auto;
        margin-top: 20px;
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        scroll-behavior: smooth;
    }
    #listaArchivos table {
        width: 100%;
        border-collapse: collapse;
    }
    #listaArchivos th, #listaArchivos td {
        padding: 8px;
        border: 1px solid #dee2e6;
    }

    #id_cuenta_proveedor:disabled,
    #edit_id_cuenta_proveedor:disabled {
        background-color: #e9ecef;
        cursor: not-allowed;
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>