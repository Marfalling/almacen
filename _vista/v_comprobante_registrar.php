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
                            
                            <?php if (!empty($oc['monto_detraccion']) && $oc['monto_detraccion'] != 0): ?>

                            <?php 
                                // Definir signo según el tipo
                                $signo = ($oc['tipo_afectacion'] === 'percepcion') ? '+' : '-';

                                // Definir clase CSS
                                $clase = ($oc['tipo_afectacion'] === 'percepcion') 
                                            ? 'text-success' 
                                            : 'text-danger';
                            ?>

                            <tr>
                                <td colspan="2">
                                    <strong>
                                        📊 <?php echo ucfirst($oc['tipo_afectacion']); ?>
                                                (<?php echo $oc['nombre_detraccion']; ?> 
                                                <?php echo $signo; ?> 
                                                <?php echo $oc['porcentaje_detraccion']; ?>%)
                                    </strong>
                                </td>

                                <td colspan="2" class="<?php echo $clase; ?>" style="font-weight: bold;">
                                    <?php echo $signo . ($oc['simbolo_moneda'] ?? 'S/.') . ' ' . number_format($oc['monto_detraccion'], 2); ?>
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
                            <a href="generar_excel_individual.php?moneda=<?php echo $oc['id_moneda']; ?>&compra=<?php echo $oc['id_compra']; ?>" class="btn btn-success btn-sm">
                                <i class="fa fa-file-excel-o"></i> Pendiente <?php echo $oc['simbolo_moneda']; ?>
                            </a>
                            <button 
                                type="button" 
                                class="btn <?php echo ($oc['pagado'] == 1) ? 'btn-secondary' : 'btn-primary'; ?>"
                                <?php echo ($oc['pagado'] == 1) ? 'disabled title="Esta compra está pagada"' : 'onclick="abrirModalMasivo()"'; ?>>
                                📤 Subir constancia de pago masivo
                            </button>
                            <!--<button
                                class="btn btn-sm <?php echo ($oc['pagado'] == 1 ) ? 'btn-outline-secondary disabled' : 'btn-outline-success'; ?>"
                                <?php echo ($oc['pagado'] == 1) ? 'disabled title="Esta compra está pagada"' : 'data-toggle="modal" data-target="#modalRegistrarComprobante"'; ?>>
                                <i class="fa fa-plus"></i> Registrar Comprobante
                            </button>-->
                            <button
                                class="btn btn-sm 
                                    <?php 
                                        echo ($oc['pagado'] == 1 || $oc['monto_pendiente'] <= 0) 
                                            ? 'btn-outline-secondary disabled' 
                                            : 'btn-outline-success'; 
                                    ?>"
                                <?php 
                                    echo ($oc['pagado'] == 1) 
                                            ? 'disabled title="Esta compra está pagada"' 
                                        : (($oc['monto_pendiente'] <= 0) 
                                            ? 'disabled title="No hay monto pendiente por registrar"' 
                                            : 'data-toggle="modal" data-target="#modalRegistrarComprobante"'); 
                                ?>>
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
                                        <th>Afectación</th>
                                        <th>Total</th>
                                        <th>Fecha Pago</th>
                                        <th>Constancia de Pago</th>
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
                                        <!--<td><?php echo $comp['simbolo_moneda'] . ' ' . number_format($comp['monto_detraccion'], 2); ?></td>-->
                                        <!--<td><strong><?php echo $comp['simbolo_moneda'] . ' ' . number_format($comp['total_pagar'], 2); ?></strong></td>-->
                                        <!-- Detracción (con ícono si aplica) - fg=2 -->
                                        <td>
                                            <?php 
                                            $montoDetraccion = $comp['monto_detraccion'];
                                            
                                            $signo = '';
                                            if (isset($comp['id_detraccion']) && $montoDetraccion > 0) {
                                                if ($comp['id_detraccion'] == 13) {
                                                    $signo = '+ '; // Percepción (suma)
                                                } else {
                                                    $signo = '- '; // Detracción (resta)
                                                }
                                            }
                                            echo $comp['simbolo_moneda'] . ' ' .$signo.' '. number_format($montoDetraccion, 2);
                                            
                                            // Mostrar ícono solo si tiene detracción y NO es percepción (id_detraccion != 13)
                                            if ($montoDetraccion > 0 && isset($comp['id_detraccion']) && $comp['id_detraccion'] != 13) {
                                                // fg=2 es el pago de detracción
                                                $tienePagoDetraccion = isset($comp['tiene_pago_detraccion']) && $comp['tiene_pago_detraccion'] > 0;
                                                $iconClass = $tienePagoDetraccion ? 'fa-check-circle text-success' : 'fa-exclamation-triangle text-warning';
                                                $iconTitle = $tienePagoDetraccion ? 'Monto ya se pagó' : 'Monto falta pagar';
                                                echo ' <i class="fa ' . $iconClass . '" style="cursor: " title="' . $iconTitle . '" data-toggle="tooltip"></i>';
                                            }
                                            ?>
                                        </td>

                                        <!-- Total al Proveedor (siempre con ícono) - fg=1 -->
                                        <td>
                                            <strong>
                                                <?php 
                                                echo $comp['simbolo_moneda'] . ' ' . number_format($comp['total_pagar'], 2);
                                                
                                                // fg=1 es el pago al proveedor
                                                $tienePagoProveedor = isset($comp['tiene_pago_proveedor']) && $comp['tiene_pago_proveedor'] > 0;
                                                $iconClass = $tienePagoProveedor ? 'fa-check-circle text-success' : 'fa-exclamation-triangle text-warning';
                                                $iconTitle = $tienePagoProveedor ? 'Monto ya se pagó' : 'Monto falta pagar';
                                                echo ' <i class="fa ' . $iconClass . '" style="cursor: " title="' . $iconTitle . '" data-toggle="tooltip"></i>';
                                                ?>
                                            </strong>
                                        </td>
                                        <td><?php echo $comp['fec_pago'] ? date('d/m/Y', strtotime($comp['fec_pago'])) : '<span class="text-muted">Pendiente</span>'; ?></td>
                                        <!-- <td class="text-center">
                                            <?php if (!empty($comp['voucher_pago'])): ?>
                                                <a href="../_upload/vouchers/<?php echo $comp['voucher_pago']; ?>" target="_blank" style="text-decoration: underline; color: #495057;">
                                                    Ver voucher
                                                </a>
                                            <?php else: ?>
                                                <span style="color: #6c757d;">Sin voucher</span>
                                            <?php endif; ?>
                                        </td>-->
                                        <td class="text-center">
                                            <?php 
                                            $tieneVoucherProveedor = !empty($comp['voucher_proveedor']);
                                            $tieneVoucherDetraccion = !empty($comp['voucher_detraccion']);
                                            $montoDetraccion = $comp['monto_detraccion'];
                                            $tieneDetraccion = ($montoDetraccion > 0 && isset($comp['id_detraccion']) && $comp['id_detraccion'] != 13);
                                            
                                            // Determinar el nombre según id_detraccion
                                            $nombreDetraccion = 'Detracción'; // Por defecto
                                            if (isset($comp['id_detraccion'])) {
                                                if ($comp['id_detraccion'] == 13) {
                                                    $nombreDetraccion = 'Percepción';
                                                } elseif ($comp['id_detraccion'] == 12) {
                                                    $nombreDetraccion = 'Retención';
                                                }
                                            }
                                            ?>
                                            
                                            <?php if ($tieneVoucherProveedor): ?>
                                                <a href="../_upload/vouchers/<?php echo $comp['voucher_proveedor']; ?>" 
                                                target="_blank" 
                                                style="text-decoration: underline; color: #007bff;">
                                                    Comprobante
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if ($tieneDetraccion && $tieneVoucherDetraccion): ?>
                                                <?php if ($tieneVoucherProveedor): ?><br><?php endif; ?>
                                                <a href="../_upload/vouchers/<?php echo $comp['voucher_detraccion']; ?>" 
                                                target="_blank" 
                                                style="text-decoration: underline; color: #007bff;">
                                                    <?php echo $nombreDetraccion; ?>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if (!$tieneVoucherProveedor && !$tieneVoucherDetraccion): ?>
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
                                                <button class="btn btn-info btn-sm" title="Ver Detalle" data-toggle="tooltip" onclick="VerDetalleComprobante(<?php echo $comp['id_comprobante']; ?>)">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                
                                                <button class="btn btn-warning btn-sm" title="Editar" data-toggle="tooltip" onclick="CargarModalEditar(<?php echo $comp['id_comprobante']; ?>)">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                
                                                <button class="btn btn-secondary btn-sm" title="Subir Constancia de Pago" data-toggle="tooltip" disabled>
                                                    <i class="fa fa-upload"></i>
                                                </button>
                                                
                                                <button class="btn btn-danger btn-sm" title="Anular" data-toggle="tooltip" onclick="AnularComprobante(<?php echo $comp['id_comprobante']; ?>)">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                                
                                            <?php elseif ($comp['est_comprobante'] == 2): ?>
                                                <!-- ESTADO 2: PENDIENTE - Ver y Subir Voucher activos -->
                                                <button class="btn btn-info btn-sm" title="Ver Detalle" data-toggle="tooltip" onclick="VerDetalleComprobante(<?php echo $comp['id_comprobante']; ?>)">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                
                                                <button class="btn btn-secondary btn-sm" title="Editar" data-toggle="tooltip" disabled>
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                
                                                <button class="btn btn-success btn-sm" title="Subir Constancia de Pago" data-toggle="tooltip" onclick="AbrirModalVoucher(<?php echo $comp['id_comprobante']; ?>, '<?php echo $comp['num_comprobante']; ?>')">
                                                    <i class="fa fa-upload"></i>
                                                </button>
                                                
                                                <button class="btn btn-secondary btn-sm" title="Anular" data-toggle="tooltip" disabled>
                                                    <i class="fa fa-times"></i>
                                                </button>
                                                
                                            <?php elseif ($comp['est_comprobante'] == 3): ?>
                                                <!-- ESTADO 3: ANULADO - Solo Ver disponible -->
                                                <button class="btn btn-info btn-sm" title="Ver Detalle" data-toggle="tooltip" onclick="VerDetalleComprobante(<?php echo $comp['id_comprobante']; ?>)">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                
                                                <button class="btn btn-secondary btn-sm" title="Editar" data-toggle="tooltip" disabled>
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                
                                                <button class="btn btn-secondary btn-sm" title="Subir Constancia de Pago" data-toggle="tooltip" disabled>
                                                    <i class="fa fa-upload"></i>
                                                </button>
                                                
                                                <button class="btn btn-secondary btn-sm" title="Anulado" data-toggle="tooltip" disabled>
                                                    <i class="fa fa-ban"></i>
                                                </button>
                                                
                                            <?php else: ?>
                                                <button class="btn btn-info btn-sm" title="Ver Detalle" data-toggle="tooltip" onclick="VerDetalleComprobante(<?php echo $comp['id_comprobante']; ?>)">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                
                                                <button class="btn btn-secondary btn-sm" title="Editar" data-toggle="tooltip" disabled>
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                
                                                <button class="btn btn-secondary btn-sm" title="Subir Constancia de Pago" data-toggle="tooltip" disabled>
                                                    <i class="fa fa-upload"></i>
                                                </button>
                                                
                                                <button class="btn btn-secondary btn-sm" title="Anulado" data-toggle="tooltip" disabled>
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
            <h5 style="margin:0;">Subida Masiva de Constancias de Pago</h5>
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
                <label><input type="checkbox" id="enviarTesoreria" checked> Enviar a Tesorería</label><br>
                <label><input type="checkbox" id="enviarCompras" checked> Enviar a Compras</label>
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
                            <input 
                                type="number"
                                step="0.01"
                                class="form-control"
                                name="monto_total_igv"
                                id="monto_total_igv"
                                value="<?php echo number_format($oc['monto_pendiente'], 2, '.', ''); ?>"
                                required>
                            <input type="hidden" id="monto_maximo_permitido" value="<?php echo number_format($oc['monto_pendiente'], 2, '.', ''); ?>">
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
                        <label>Afectación</label>

                        <?php 
                        foreach($monedas as $mon) { 
                            if($mon['id_moneda'] == $oc['id_moneda']) {
                                if($mon['id_moneda']==1){
                                    $simbolo_moneda='S/.';
                                }
                                else if($mon['id_moneda']==2){
                                    $simbolo_moneda='US$';
                                }
                            }
                        } 
                        ?>

                        <!-- Campo visible deshabilitado -->
                        <input 
                            type="text" 
                            class="form-control"
                            id="monto_detraccion_visible"
                            value="<?php echo '('. $oc['porcentaje_detraccion'].'%)'.' '.$simbolo_moneda.$oc['monto_detraccion']  ; ?>" 
                            disabled
                        >

                    </div>

                    <!-- Campo oculto para enviar la afectación seleccionada -->
                    <input type="hidden" name="afectacion_seleccionada" id="afectacion_seleccionada" value="<?php echo $oc['id_afectacion']; ?>">
                    <!-- Hidden para id_afectacion -->
                    <input type="hidden" id="id_afectacion" name="id_afectacion" value="<?php echo $oc['id_afectacion']; ?>">
                    <!-- Hidden para monto_detraccion -->
                    <input type="hidden" id="monto_detraccion" name="monto_detraccion" value="">
                    <input type="hidden" id="porcentaje_detraccion" value="<?php echo $oc['porcentaje_detraccion']; ?>">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Total a Pagar</label>
                            <input value="<?php echo $oc['monto_total']; ?>" 
                            type="number" step="0.01" name="total_pagar" id="total_pagar" class="form-control" placeholder="0.00" required readonly style="background-color: #e9ecef;">
                        </div>
                    </div>
                    
                </div>
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Medio de Pago <span class="text-danger">*</span></label>
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
                            <label>Fecha de Emisión</label>
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
                            <label>Archivo PDF <span class="text-danger">*</span></label>
                            <input type="file" name="archivo_pdf" class="form-control" accept=".pdf" required>
                            <small class="form-text text-muted">Máximo 5MB</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Archivo XML <span class="text-danger">*</span> </label>
                            <input type="file" name="archivo_xml" class="form-control" accept=".xml" required>
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
                            <label>Monto Total con IGV</label>
                            <input 
                                type="number"
                                step="0.01"
                                class="form-control"
                                name="monto_total_igv"
                                id="edit_monto_total_igv"
                                value=""
                                required>
                            <input type="hidden" id="edit_monto_maximo_permitido" value="">
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
                        <label>Afectación</label>

                        <?php 
                        foreach($monedas as $mon) { 
                            if($mon['id_moneda'] == $oc['id_moneda']) {
                                if($mon['id_moneda']==1){
                                    $simbolo_moneda='S/.';
                                }
                                else if($mon['id_moneda']==2){
                                    $simbolo_moneda='US$';
                                }
                            }
                        } 
                        ?>

                        <!-- Campo visible deshabilitado -->
                        <input 
                            type="text" 
                            class="form-control"
                            id="edit_monto_detraccion_visible"
                            value="" 
                            disabled
                        >

                    </div>

                    <!-- Campo oculto para enviar la afectación seleccionada -->
                    <input type="hidden" name="afectacion_seleccionada" id="edit_afectacion_seleccionada" value="">
                    <!-- Hidden para id_afectacion -->
                    <input type="hidden" id="edit_id_afectacion" name="id_afectacion" value="">
                    <!-- Hidden para monto_detraccion -->
                    <input type="hidden" id="edit_monto_detraccion" name="monto_detraccion" value="">
                    <input type="hidden" id="edit_porcentaje_detraccion" value="">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Total a Pagar</label>
                            <input value="" 
                            type="number" step="0.01" name="total_pagar" id="edit_total_pagar" class="form-control" placeholder="0.00" required readonly style="background-color: #e9ecef;">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Medio de Pago <span class="text-danger">*</span></label>
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
                            <label>Fecha de Emisión</label>
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
                            <label>Archivo PDF <span class="text-danger">*</span></label>
                            <div id="pdf_actual" class="mb-2"></div>
                            <input type="file" name="archivo_pdf" class="form-control" accept=".pdf">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Archivo XML <span class="text-danger">*</span> </label>
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
                <input type="hidden" id="voucher_num_comprobante">
                
                <div class="form-group">
                    <label>Archivo de Voucher <span class="text-danger">*</span></label>
                    <input type="file" name="voucher_pago" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                    <small class="form-text text-muted">Formatos permitidos: PDF, JPG, PNG (Máx. 5MB)</small>

                    <br>

                    <label>Fecha de Pago <span class="text-danger">*</span></label>
                    <input type="date" name="fec_voucher" id="fec_voucher" class="form-control" required>
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
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="enviar_compras" id="enviar_compras" value="1" checked>
                        <label class="form-check-label" for="enviar_compras">
                            Enviar a Compras
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

<div class="modal fade" id="modalConflicto" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header bg-warning">
        <h5 class="modal-title">Información</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">
        <p class="mb-1">Se encontraron varios comprobantes con la serie y número:</p>
        <p><strong id="conflicto_serie_numero"></strong></p>
        <p>Seleccione a qué proveedor pertenece el archivo:</p>
        <div id="conflicto_opciones"></div>
        <input type="hidden" id="conflicto_archivo">
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" onclick="resolverConflicto()">Asignar</button>
      </div>

    </div>
  </div>
</div>


<!-- ============================================================ -->
<!-- SCRIPTS -->
<!-- ============================================================ -->
<!-- ============================================ -->
<!-- CARGAR JQUERY PRIMERO (INDEPENDIENTE) -->
<!-- ============================================ -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

if (typeof jQuery === 'undefined') {
    console.error('❌ CRÍTICO: jQuery NO está cargado');
    alert('Error: jQuery no está cargado. Contacte al administrador.');
}

// ============================================================
// VARIABLES GLOBALES
// ============================================================
let archivosSeleccionados = [];
let modalRegistrarInicializado = false;
let modalEditarInicializado = false;
let datosAnalisis = null; // Almacena resultado del análisis

console.log('✅ Variables globales declaradas');

// ============================================================
// FUNCIONES DE CÁLCULO
// ============================================================

function calcularTotalPagar(isEditMode) {
    console.log(`🔢 calcularTotalPagar llamada (${isEditMode ? 'EDIT' : 'NEW'})`);
    
    const prefix = isEditMode ? 'edit_' : '';
    const inputMonto = document.getElementById(prefix + 'monto_total_igv');
    const inputTotal = document.getElementById(prefix + 'total_pagar');
    const inputAfectacion = document.getElementById(prefix + 'id_afectacion');
    const inputDetraccion = document.getElementById(prefix + 'monto_detraccion');
    const inputPorcentaje = document.getElementById(prefix + 'porcentaje_detraccion') 
                          || document.getElementById('porcentaje_detraccion');
    const inputVisible = document.getElementById(prefix + 'monto_detraccion_visible');

    console.log('📋 Elementos encontrados:', {
        inputMonto: !!inputMonto,
        inputTotal: !!inputTotal,
        inputAfectacion: !!inputAfectacion,
        inputDetraccion: !!inputDetraccion,
        inputPorcentaje: !!inputPorcentaje,
        inputVisible: !!inputVisible
    });

    if (!inputMonto || !inputTotal || !inputAfectacion || !inputDetraccion || !inputPorcentaje || !inputVisible) {
        console.error("❌ Faltan campos para el cálculo");
        return;
    }

    const montoConIGV = parseFloat(inputMonto.value) || 0;
    const idAfectacion = parseInt(inputAfectacion.value) || 0;
    const porcDetrac = parseFloat(inputPorcentaje.value) || 0;

    console.log(`📊 Valores:`, {montoConIGV, idAfectacion, porcDetrac});

    if (montoConIGV <= 0) {
        inputDetraccion.value = "0.00";
        inputVisible.value = `(0%) S/. 0.00`;
        inputTotal.value = "";
        console.log('⚠️ Monto <= 0, campos limpiados');
        return;
    }

    let montoAfectacion = 0;
    if (porcDetrac > 0) {
        montoAfectacion = (montoConIGV * porcDetrac / 100);
    }

    inputDetraccion.value = montoAfectacion.toFixed(2);
    inputVisible.value = `(${porcDetrac}%) S/. ${montoAfectacion.toFixed(2)}`;

    let total = montoConIGV;
    if (idAfectacion === 13) {
        total = montoConIGV + montoAfectacion;
    } else if (idAfectacion > 0) {
        total = montoConIGV - montoAfectacion;
    }

    inputTotal.value = total.toFixed(2);
    console.log(`Total calculado: ${total.toFixed(2)}`);
}

function validarMontoMaximo(isEditMode, valor) {
    console.log(`validarMontoMaximo: ${valor} (${isEditMode ? 'EDIT' : 'NEW'})`);
    
    const prefix = isEditMode ? 'edit_' : '';
    const hiddenId = prefix + "monto_maximo_permitido";
    const fallbackHiddenId = "monto_maximo_permitido";
    const hiddenNow = document.getElementById(hiddenId) || document.getElementById(fallbackHiddenId);
    const montoMax = hiddenNow ? parseFloat(hiddenNow.value) : NaN;

    console.log(`Monto máximo permitido: ${montoMax}`);

    if (isNaN(montoMax)) {
        if (valor <= 0) {
            Swal.fire('Error', 'El monto debe ser mayor a 0', 'error');
            return false;
        }
        return true;
    }

    if (Math.round(valor * 100) > Math.round(montoMax * 100)) {
        Swal.fire('Error', `El monto no puede ser mayor a S/. ${montoMax.toFixed(2)}`, 'error');
        return false;
    }
    
    return true;
}

function setupCuentaControl(isEditMode) {
    console.log(`setupCuentaControl (${isEditMode ? 'EDIT' : 'NEW'})`);
    
    const prefix = isEditMode ? 'edit_' : '';
    const medio = document.getElementById(prefix + 'id_medio_pago');
    const cuenta = document.getElementById(prefix + 'id_cuenta_proveedor');
    
    console.log('🔍 Elementos cuenta:', {
        medio: !!medio,
        cuenta: !!cuenta,
        medioValue: medio?.value
    });
    
    if (!medio || !cuenta) {
        console.warn('Campos de cuenta control no encontrados');
        return;
    }

    const aplicarEstado = () => {
        console.log(`Aplicando estado cuenta, medio_pago: "${medio.value}"`);
        
        if ((medio.value || '').toString().trim() === '2') {
            cuenta.disabled = false;
            cuenta.required = true;
            cuenta.style.backgroundColor = '#ffffff';
            console.log('Cuenta ACTIVADA');
        } else {
            cuenta.disabled = true;
            cuenta.required = false;
            cuenta.value = '';
            cuenta.style.backgroundColor = '#e9ecef';
            console.log('Cuenta DESACTIVADA');
        }
    };

    aplicarEstado();
}

// ✅ FUNCIÓN FALTANTE
function setupCuentaControlEdit() {
    console.log('🏦 setupCuentaControlEdit llamada');
    setupCuentaControl(true);
}

// ============================================================
//  INICIALIZACIÓN DE MODALES
// ============================================================

function inicializarModalRegistrar() {
    console.log('inicializarModalRegistrar llamada');
    console.log('Estado actual:', {modalRegistrarInicializado});
    
    if (modalRegistrarInicializado) {
        console.log(' Modal REGISTRAR ya inicializado, solo recalculando');
        calcularTotalPagar(false);
        setupCuentaControl(false);
        return;
    }
    
    const modal = document.getElementById('modalRegistrarComprobante');
    if (!modal) {
        console.error('Modal REGISTRAR no encontrado');
        return;
    }
    
    console.log(' Modal REGISTRAR encontrado, agregando listeners');
    
    // Evento delegado para input
    modal.addEventListener('input', function(e) {
        console.log(' Input detectado en:', e.target.id);
        
        if (e.target.id === 'monto_total_igv') {
            const valor = parseFloat(e.target.value) || 0;
            console.log(' Monto ingresado:', valor);
            
            if (validarMontoMaximo(false, valor)) {
                calcularTotalPagar(false);
            } else {
                const hiddenId = "monto_maximo_permitido";
                const hiddenNow = document.getElementById(hiddenId);
                const montoMax = hiddenNow ? parseFloat(hiddenNow.value) : 0;
                e.target.value = montoMax > 0 ? montoMax.toFixed(2) : '';
                calcularTotalPagar(false);
            }
        }
    });
    
    // Evento delegado para change
    modal.addEventListener('change', function(e) {
        console.log(' Change detectado en:', e.target.id);
        
        if (e.target.id === 'id_afectacion') {
            console.log(' Afectación cambiada');
            calcularTotalPagar(false);
        }
        if (e.target.id === 'id_medio_pago') {
            console.log(' Medio de pago cambiado');
            setupCuentaControl(false);
        }
        if (e.target.id === 'monto_total_igv') {
            const valor = parseFloat(e.target.value);
            if (isNaN(valor) || valor <= 0) {
                Swal.fire('Error', 'El monto debe ser mayor a 0', 'error');
                e.target.value = "";
            }
        }
    });
    
    modalRegistrarInicializado = true;
    setupCuentaControl(false);
    calcularTotalPagar(false);
    console.log(' Modal REGISTRAR inicializado completamente');
}

function inicializarModalEditar() {
    console.log(' inicializarModalEditar llamada');
    console.log('Estado actual:', {modalEditarInicializado});
    
    if (modalEditarInicializado) {
        console.log(' Modal EDITAR ya inicializado, solo recalculando');
        calcularTotalPagar(true);
        setupCuentaControl(true);
        return;
    }
    
    const modal = document.getElementById('modalEditarComprobante');
    if (!modal) {
        console.error(' Modal EDITAR no encontrado');
        return;
    }
    
    console.log(' Modal EDITAR encontrado, agregando listeners');
    
    // Evento delegado para input
    modal.addEventListener('input', function(e) {
        console.log(' Input detectado en:', e.target.id);
        
        if (e.target.id === 'edit_monto_total_igv') {
            const valor = parseFloat(e.target.value) || 0;
            console.log(' Monto ingresado:', valor);
            
            if (validarMontoMaximo(true, valor)) {
                calcularTotalPagar(true);
            } else {
                const hiddenId = "edit_monto_maximo_permitido";
                const fallbackHiddenId = "monto_maximo_permitido";
                const hiddenNow = document.getElementById(hiddenId) || document.getElementById(fallbackHiddenId);
                const montoMax = hiddenNow ? parseFloat(hiddenNow.value) : 0;
                e.target.value = montoMax > 0 ? montoMax.toFixed(2) : '';
                calcularTotalPagar(true);
            }
        }
    });
    
    // Evento delegado para change
    modal.addEventListener('change', function(e) {
        console.log(' Change detectado en:', e.target.id);
        
        if (e.target.id === 'edit_id_afectacion') {
            console.log(' Afectación cambiada');
            calcularTotalPagar(true);
        }
        if (e.target.id === 'edit_id_medio_pago') {
            console.log(' Medio de pago cambiado');
            setupCuentaControl(true);
        }
        if (e.target.id === 'edit_monto_total_igv') {
            const valor = parseFloat(e.target.value);
            if (isNaN(valor) || valor <= 0) {
                Swal.fire('Error', 'El monto debe ser mayor a 0', 'error');
                e.target.value = "";
            }
        }
    });
    
    modalEditarInicializado = true;
    setupCuentaControl(true);
    calcularTotalPagar(true);
    console.log(' Modal EDITAR inicializado completamente');
}

// ============================================================
//  EVENTOS DE MODALES
// ============================================================

$(document).ready(function() {
    console.log(' jQuery ready ejecutado');
    
    // Modal Registrar
    $('#modalRegistrarComprobante').on('shown.bs.modal', function() {
        console.log(' Modal REGISTRAR abierto (evento shown.bs.modal)');
        
        setTimeout(() => {
            if (!modalRegistrarInicializado) {
                console.log('Inicializando por primera vez...');
                inicializarModalRegistrar();
            } else {
                console.log('Ya inicializado, solo recalculando...');
                setupCuentaControl(false);
                calcularTotalPagar(false);
            }
        }, 100);
    });
    
    // Modal Editar
    $('#modalEditarComprobante').on('shown.bs.modal', function() {
        console.log(' Modal EDITAR abierto (evento shown.bs.modal)');
        
        setTimeout(() => {
            if (!modalEditarInicializado) {
                console.log('Inicializando por primera vez...');
                inicializarModalEditar();
            } else {
                console.log('Ya inicializado, solo recalculando...');
                setupCuentaControl(true);
                calcularTotalPagar(true);
            }
        }, 150);
    });
    
    // DataTables
    if ($.fn.DataTable && $('#tablaComprobantes').length > 0) {
        $('#tablaComprobantes').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            "order": [[0, "desc"]],
            "pageLength": 25
        });
        console.log(" DataTable inicializado");
    }

    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip({
        placement: 'top',
        trigger: 'hover'
    });
    
    console.log(' Todos los eventos jQuery configurados');
});

// ============================================================
// 3️⃣ FUNCIONES GLOBALES PARA MODALES (Fuera del IIFE)
// ============================================================

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
            
            console.log(' Cargando datos para edición:', data);
            
            // Llenar campos
            document.getElementById('edit_id_comprobante').value = data.id_comprobante;
            document.getElementById('edit_id_tipo_documento').value = data.id_tipo_documento;
            document.getElementById('edit_serie').value = data.serie;
            document.getElementById('edit_numero').value = data.numero;
            document.getElementById('edit_monto_total_igv').value = data.monto_total_igv;
            document.getElementById('edit_id_moneda').value = data.id_moneda;
            
            if (data.id_medio_pago) {
                document.getElementById('edit_id_medio_pago').value = data.id_medio_pago;
            }
            
            if (data.fec_pago) {
                document.getElementById('edit_fec_pago').value = data.fec_pago;
            }

            let simbolo = (data.id_moneda == 1) ? "S/." : "US$";

            let montoDetraccion = 0;
            if (data.porcentaje_detraccion > 0) {
                montoDetraccion = (data.monto_total_igv * data.porcentaje_detraccion) / 100;
            }

            document.getElementById('edit_monto_detraccion_visible').value =
                data.porcentaje_detraccion > 0
                ? '(' + data.porcentaje_detraccion + '%) ' + simbolo + montoDetraccion.toFixed(2)
                : 'Sin detracción';

            document.getElementById('edit_porcentaje_detraccion').value = data.porcentaje_detraccion;
            document.getElementById('edit_id_afectacion').value = data.id_detraccion; 
            document.getElementById('edit_afectacion_seleccionada').value = data.id_detraccion;
            document.getElementById('edit_monto_maximo_permitido').value = data.monto_maximo_permitido;
            
            // LLAMAR a la función global
            setTimeout(function() {
                setupCuentaControlEdit();
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

function AbrirModalVoucher(id_comprobante, numComprobante) {
    document.getElementById('voucher_id_comprobante').value = id_comprobante;
    document.getElementById('voucher_num_comprobante').value = numComprobante.trim();
    
    // Inicializar SOLO cuando se abre el modal
    setTimeout(() => {
        inicializarVoucherPago();
    }, 200);
    
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
            let montoDetraccion = parseFloat(data.monto_detraccion || 0);
            let tieneDetraccion = (montoDetraccion > 0 && data.id_detraccion && data.id_detraccion != 13);

            // Determinar el nombre según id_detraccion
            let nombreDetraccion = 'Detracción';
            if (data.id_detraccion) {
                if (data.id_detraccion == 13) {
                    nombreDetraccion = 'Percepción';
                } else if (data.id_detraccion == 12) {
                    nombreDetraccion = 'Retención';
                }
            }
            
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
                    <tr><th>Detracción:</th><td>${data.simbolo_moneda} ${subtotal.toFixed(2)}</td></tr>
                    <tr style="background-color: #d4edda;"><th>TOTAL:</th><td><strong>${data.simbolo_moneda} ${parseFloat(data.total_pagar).toFixed(2)}</strong></td></tr>
                </table>
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
                            ${data.voucher_proveedor ? 
                                '<a href="../_upload/vouchers/' + data.voucher_proveedor + '" target="_blank" class="btn btn-sm btn-success"><i class="fa fa-check-circle"></i> Pago Comprobante</a>' : 
                                ''}
                            ${(tieneDetraccion && data.voucher_detraccion) ? 
                                '<a href="../_upload/vouchers/' + data.voucher_detraccion + '" target="_blank" class="btn btn-sm btn-success"><i class="fa fa-check-circle"></i> Pago ' + nombreDetraccion + '</a>' : 
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

// ============================================================
//  FUNCIONES DE SUBIDA MASIVA
// ============================================================

// INICIALIZAR SOLO UNA VEZ
function inicializarModalMasivo() {
    const inputArchivos = document.getElementById("inputArchivos");
    
    if (!inputArchivos) {
        console.error(" Input de archivos no encontrado");
        return;
    }
    
    inputArchivos.removeEventListener("change", manejarCambioArchivos);
    inputArchivos.addEventListener("change", manejarCambioArchivos);
    
    console.log(" Modal masivo inicializado");
}

function abrirModalMasivo() {
    const modal = document.getElementById("modalSubidaMasivo");
    if (modal) {
        modal.style.display = "flex";
        
        setTimeout(() => {
            inicializarModalMasivo();
        }, 200);
    } else {
        console.error(" Modal no encontrado");
    }
}

function cerrarModalMasivo() {
    const modal = document.getElementById("modalSubidaMasivo");
    if (modal) {
        modal.style.display = "none";
        archivosSeleccionados = [];
        datosAnalisis = null;
        document.getElementById("listaArchivos").innerHTML = "";
    }
}

function manejarCambioArchivos(e) {
    console.log(" ¡¡¡CAMBIO DETECTADO!!!");
    const nuevosArchivos = Array.from(e.target.files);
    if (nuevosArchivos.length === 0) return;

    nuevosArchivos.forEach((archivo) => {
        const nombre = archivo.name.trim();
        const tamañoMB = archivo.size / (1024 * 1024);

        if (tamañoMB > 5) {
            Swal.fire({
                icon: 'error',
                title: 'Archivo demasiado grande',
                text: `El archivo "${nombre}" pesa ${tamañoMB.toFixed(2)} MB. El máximo permitido es 5 MB.`
            });
            return;
        }

        const regexNombre = /^[A-Z0-9]{4}-\d{2,8}\.[A-Za-z0-9]+$/i;
        if (!regexNombre.test(nombre)) {
            Swal.fire({
                icon: 'warning',
                title: 'Formato inválido',
                text: `El archivo "${nombre}" no cumple el formato "SERIE-NUMERO", por ejemplo: "F001-00012345.pdf"`
            });
            return;
        }

        if (!archivosSeleccionados.find(a => a.name === nombre)) {
            archivosSeleccionados.push(archivo);
            console.log(" Archivo agregado:", nombre);
        } else {
            Swal.fire({
                icon: 'info',
                title: 'Archivo duplicado',
                text: `El archivo "${nombre}" ya fue agregado.`
            });
        }
    });

    console.log("Total en memoria:", archivosSeleccionados.length);
    actualizarListaArchivos();
    e.target.value = "";
}

function actualizarListaArchivos() {
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

    html += `</tbody></table>`;
    listaArchivos.innerHTML = html;
}

function eliminarArchivo(index) {
    archivosSeleccionados.splice(index, 1);
    actualizarListaArchivos();
}

// NUEVO FLUJO: FASE 1 - ANÁLISIS
async function procesarArchivos() {
    if (archivosSeleccionados.length === 0) {
        Swal.fire('Advertencia', 'Selecciona al menos un archivo', 'warning');
        return;
    }

    // 1️⃣ FASE DE ANÁLISIS
    Swal.fire({
        title: 'Analizando archivos...',
        html: '',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();

            // Cerrar automáticamente después de 5 segundos
            setTimeout(() => {
                Swal.close();
            }, 2000);
        }
    });

    const formData = new FormData();
    archivosSeleccionados.forEach(archivo => {
        formData.append('archivos[]', archivo);
    });

    try {
        const response = await fetch('../_controlador/comprobante_analizar_masivo.php', {
            method: 'POST',
            body: formData
        });

        datosAnalisis = await response.json();
        
        Swal.close();

        if (!datosAnalisis.success) {
            Swal.fire('Error', datosAnalisis.mensaje || 'Error al analizar archivos', 'error');
            return;
        }

        // 2️⃣ ¿HAY CONFLICTOS?
        if (datosAnalisis.conflictos.length > 0) {
            //await mostrarResumenAnalisis();
            await resolverConflictos();
        } else {
            // 3️⃣ NO HAY CONFLICTOS → REGISTRAR DIRECTAMENTE
            await registrarArchivosMasivo();
        }

    } catch (error) {
        Swal.close();
        console.error('Error:', error);
        Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
    }
}

// NUEVO: Mostrar resumen del análisis
/*async function mostrarResumenAnalisis() {
    const { correctos, conflictos, errores } = datosAnalisis;

    return Swal.fire({
        icon: 'info',
        title: 'Análisis completado',
        html: `
            <div style="text-align:center; margin:12px 0;">
                <span class="badge badge-success" style="font-size:13px; padding:6px 12px;">
                    ✅ ${correctos.length} correctos
                </span>
                <span class="badge badge-warning" style="font-size:13px; padding:6px 12px;">
                    ⚠️ ${conflictos.length} conflictos
                </span>
                <span class="badge badge-danger" style="font-size:13px; padding:6px 12px;">
                    ❌ ${errores.length} errores
                </span>
            </div>
            <p style="margin-top:12px; font-size:13px;">
                ${conflictos.length > 0 ? 'Debes resolver los conflictos antes de continuar' : 'Procederemos con el registro'}
            </p>
        `,
        confirmButtonText: conflictos.length > 0 ? 'Resolver conflictos' : 'Continuar'
    });
}*/

// NUEVO FLUJO: FASE 2 - RESOLUCIÓN DE CONFLICTOS
async function resolverConflictos() {
    let indice = 0;
    const conflictos = datosAnalisis.conflictos;

    console.log(`🔍 Iniciando resolución de ${conflictos.length} conflictos`);

    async function mostrarSiguienteConflicto() {
        if (indice >= conflictos.length) {
            console.log("✅ Todos los conflictos resueltos");
            // ✅ Todos los conflictos resueltos → REGISTRAR
            await registrarArchivosMasivo();
            return;
        }

        const conflicto = conflictos[indice];
        console.log(`📋 Mostrando conflicto ${indice + 1}/${conflictos.length}:`, conflicto.archivo);
        
        await mostrarModalConflicto(
            conflicto.archivo,
            conflicto.serie,
            conflicto.numero,
            conflicto.opciones,
            indice + 1,
            conflictos.length
        );
    }

    // Callback para siguiente conflicto
    window.resolverConflictoCallback = async (id_comprobante_seleccionado) => {
        console.log(`✅ Resolviendo conflicto ${indice + 1} con ID:`, id_comprobante_seleccionado);
        
        // Mover de conflictos a correctos
        const conflictoResuelto = conflictos[indice];
        const opcionSeleccionada = conflictoResuelto.opciones.find(
            op => op.id_comprobante == id_comprobante_seleccionado
        );

        if (!opcionSeleccionada) {
            console.error("❌ Opción no encontrada");
            return;
        }

        datosAnalisis.correctos.push({
            archivo: conflictoResuelto.archivo,
            serie: conflictoResuelto.serie,
            numero: conflictoResuelto.numero,
            id_comprobante: id_comprobante_seleccionado,
            nom_proveedor: opcionSeleccionada.nom_proveedor,
            ruc_proveedor: opcionSeleccionada.ruc_proveedor,
            archivo_temporal: conflictoResuelto.archivo_temporal,
            extension: conflictoResuelto.extension
        });

        console.log(`✅ Conflicto ${indice + 1} resuelto. Correctos:`, datosAnalisis.correctos.length);

        indice++;
        await mostrarSiguienteConflicto();
    };

    await mostrarSiguienteConflicto();
}

// ACTUALIZAR: Modal de conflicto con contador con SweetAlert2
async function mostrarModalConflicto(archivo, serie, numero, opciones, actual, total) {
    let htmlOpciones = "";
    
    opciones.forEach(op => {
        htmlOpciones += `
        <div class="form-check">
            <input class="form-check-input" type="radio" name="conflictoProveedor" value="${op.id_comprobante}" id="opt_${op.id_comprobante}">
            <label class="form-check-label" for="opt_${op.id_comprobante}">
                <strong>${op.nom_proveedor}</strong><br>
                <small class="text-muted">RUC: ${op.ruc_proveedor}</small>
            </label>
        </div>`;
    });

    const result = await Swal.fire({
        title: 'Información',
        html: `
            <div style="text-align:left;">
                <p class="mb-1">Se encontraron varios comprobantes con la serie y número: <strong>${serie}-${numero}</strong></p>
                <br>
                <p>Seleccione a qué proveedor pertenece el archivo:</p>
                ${htmlOpciones}
            </div>
        `,
        width: '400px',
        showCancelButton: true,
        confirmButtonText: 'Asignar',
        cancelButtonText: 'Cancelar',
        customClass: {
            popup: 'swal2-small',
            title: 'swal2-title-warning'
        },
        preConfirm: () => {
            const seleccionado = document.querySelector('input[name="conflictoProveedor"]:checked');
            if (!seleccionado) {
                Swal.showValidationMessage('Debes seleccionar un proveedor');
                return false;
            }
            return seleccionado.value;
        }
    });

    if (result.isConfirmed) {
        if (window.resolverConflictoCallback) {
            await window.resolverConflictoCallback(result.value);
        }
    } else {
        Swal.fire('Proceso cancelado', 'No se registró ningún archivo', 'info');
    }
}

// ACTUALIZAR: Resolver conflicto (sin registrar)
function resolverConflicto() {
    let id_comprobante = document.querySelector('input[name="conflictoProveedor"]:checked');
    
    if (!id_comprobante) {
        Swal.fire({
            icon: 'warning',
            title: 'Selección requerida',
            text: 'Debes seleccionar un proveedor',
            confirmButtonText: 'Entendido'
        });
        return;
    }

    id_comprobante = id_comprobante.value;
    
    $("#modalConflicto").modal("hide");
    
    // Llamar callback para continuar
    if (window.resolverConflictoCallback) {
        window.resolverConflictoCallback(id_comprobante);
    }
}

// NUEVO FLUJO: FASE 3 - REGISTRO MASIVO
async function registrarArchivosMasivo() {
    const { correctos, errores } = datosAnalisis;

    if (correctos.length === 0) {
        mostrarResultadoFinal(0, errores);
        return;
    }

    Swal.fire({
        title: 'Registrando archivos...',
        html: `Procesando ${correctos.length} archivo(s)`,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();

            // Cerrar automáticamente después de 5 segundos
            setTimeout(() => {
                Swal.close();
            }, 2000);
        }
    });

    // Preparar datos para envío
    const archivos_a_registrar = correctos.map(item => ({
        archivo: item.archivo,
        id_comprobante: item.id_comprobante,
        archivo_temporal: item.archivo_temporal,
        extension: item.extension
    }));

    const payload = {
        archivos_a_registrar: archivos_a_registrar,
        enviar_proveedor: document.getElementById('enviarProveedor').checked ? 1 : 0,
        enviar_contabilidad: document.getElementById('enviarContabilidad').checked ? 1 : 0,
        enviar_tesoreria: document.getElementById('enviarTesoreria').checked ? 1 : 0,
        enviar_compras: document.getElementById('enviarCompras').checked ? 1 : 0
    };

    try {
        const response = await fetch('../_controlador/comprobante_subida_masiva.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const resultado = await response.json();
        
        Swal.close();

        if (resultado.success) {
            // Combinar errores de análisis + errores de registro
            const todosLosErrores = [...errores, ...resultado.errores];
            mostrarResultadoFinal(resultado.exitosos, todosLosErrores);
        } else {
            Swal.fire('Error', resultado.mensaje || 'Error al registrar archivos', 'error');
        }

    } catch (error) {
        Swal.close();
        console.error('Error:', error);
        Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
    }
}

// NUEVO FLUJO: FASE 4 - RESULTADO FINAL
function mostrarResultadoFinal(exitosos, errores) {
    if (errores.length === 0) {
        // ✅ TODO EXITOSO
        Swal.fire({
            icon: 'success',
            title: '¡Proceso completado!',
            html: `<b>${exitosos}</b> archivo(s) procesado(s) exitosamente`,
            confirmButtonText: 'Aceptar'
        }).then(() => {
            cerrarModalMasivo();
            location.reload();
        });
    } else {
        // ⚠️ HAY ERRORES
        let htmlErrores = '<div style="max-height:350px; overflow-y:auto; text-align:left; margin-top:10px;">';
        
        errores.forEach((error) => {
            htmlErrores += `
                <div style="
                    display:flex; 
                    align-items:center; 
                    gap:8px;
                    padding:6px 10px; 
                    margin-bottom:4px;
                    background:#f8f9fa;
                    border-radius:4px;
                    border-left:3px solid #dc3545;
                    transition: all 0.2s;
                " onmouseover="this.style.background='#e9ecef'" onmouseout="this.style.background='#f8f9fa'">
                    <i class="fa fa-file-o" style="color:#dc3545; font-size:12px;"></i>
                    <span style="font-size:12px; color:#495057; flex:1; min-width:0;">
                        ${error.archivo}
                    </span>
                    <span 
                        style="
                            background:#dc3545;
                            color:white;
                            width:18px;
                            height:18px;
                            border-radius:50%;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            font-size:10px;
                            cursor:help;
                            flex-shrink:0;
                        "
                        title="${error.motivo}"
                        data-toggle="tooltip"
                        data-placement="left"
                    >
                        <i class="fa fa-info"></i>
                    </span>
                </div>
            `;
        });
        
        htmlErrores += '</div>';

        Swal.fire({
            icon: exitosos > 0 ? 'warning' : 'error',
            title: exitosos > 0 ? 'Proceso con errores' : 'Proceso fallido',
            html: `
                <div style="text-align:center; margin:8px 0 12px 0;">
                    ${exitosos > 0 ? `
                        <span class="badge badge-success" style="font-size:12px; padding:4px 10px;">
                            ${exitosos} exitosos
                        </span>
                    ` : ''}
                    <span class="badge badge-danger" style="font-size:12px; padding:4px 10px;">
                        ${errores.length} fallidos
                    </span>
                </div>
                ${htmlErrores}
            `,
            width: '500px',
            confirmButtonText: 'Cerrar',
            confirmButtonColor: '#6c757d',
            didOpen: () => {
                $('[data-toggle="tooltip"]').tooltip();
            },
            willClose: () => {
                $('[data-toggle="tooltip"]').tooltip('dispose');
            }
        }).then(() => {
            if (exitosos > 0) {
                cerrarModalMasivo();
                location.reload();
            }
        });
    }
}

// ============================================================
// 5️⃣ FUNCIONES DE VALIDACIÓN DE VOUCHER INDIVIDUAL
// ============================================================

function inicializarVoucherPago() {
    const inputVoucher = document.querySelector('input[name="voucher_pago"]');
    if (!inputVoucher) {
        console.warn("⚠️ Input voucher_pago no encontrado");
        return;
    }

    // ✅ Remover listener anterior
    inputVoucher.removeEventListener('change', validarVoucherPago);
    inputVoucher.addEventListener('change', validarVoucherPago);
    
    console.log("✅ Validación de voucher inicializada");
}

function validarVoucherPago(e) {
    const archivo = e.target.files[0];
    if (!archivo) return;

    const nombre = archivo.name.trim();
    const tamañoMB = archivo.size / (1024 * 1024);

    if (tamañoMB > 5) {
        Swal.fire({
            icon: 'error',
            title: 'Archivo demasiado grande',
            text: `El archivo "${nombre}" pesa ${tamañoMB.toFixed(2)} MB. Máx. permitido: 5 MB.`
        });
        e.target.value = "";
        return;
    }

    const regexNombre = /^[A-Z0-9]{4}-\d{2,8}\.[A-Za-z0-9]+$/i;
    if (!regexNombre.test(nombre)) {
        Swal.fire({
            icon: 'warning',
            title: 'Formato incorrecto',
            text: `El archivo "${nombre}" no cumple el formato "SERIE-NUMERO". Ej: F001-00012345.pdf`
        });
        e.target.value = "";
        return;
    }

    // ============================
    // 🚨 VALIDAR QUE SEA EL MISMO COMPROBANTE
    // ============================
    const numComprobante = document.getElementById('voucher_num_comprobante').value.trim();

    // Extraer nombre sin extensión
    const nombreSinExt = nombre.split('.').slice(0, -1).join('.');

    if (nombreSinExt.toUpperCase() !== numComprobante.toUpperCase()) {
        Swal.fire({
            icon: 'error',
            title: 'Voucher incorrecto',
            text: `El nombre del archivo debe coincidir exactamente con "${numComprobante}".`
        });
        e.target.value = "";
        return;
    }

    console.log("✅ Voucher válido:", nombre);
}

// ============================================================
// 6️⃣ DATATABLES (Inicializar una sola vez)
// ============================================================

/*$(document).ready(function() {
    console.log("✅ jQuery ready");
    
    if ($.fn.DataTable) {
        $('#tablaComprobantes').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            "order": [[0, "desc"]],
            "pageLength": 25
        });
        console.log("✅ DataTable inicializado");
    }
});*/

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

    /* Forzar alineación exacta del checkbox específico */
    #chk_editar_monto {
    transform: translateY(0) !important;   /* elimina la corrección de bootstrap */
    margin-top: 0 !important;
    vertical-align: middle !important;
    align-self: center !important;
    /* opcional: tamaño para uniformar si se ve distinto en navegadores */
    width: 1.05em;
    height: 1.05em;
    }

    .swal2-title-warning {
        background-color: #ffc107 !important;
        color: #212529 !important;
        padding: 15px !important;
        margin: 0 0 20px 0 !important;
        border-radius: 5px 5px 0 0 !important;
    }

    .swal2-small {
        font-size: 14px !important;
    }

    .swal2-small .form-check {
        margin-bottom: 10px;
        text-align: left;
    }
    
</style>