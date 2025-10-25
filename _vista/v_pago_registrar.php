<?php
// VISTA: v_pagos_registrar.php

?>


<script>
function AnularPago(id_pago) {
    Swal.fire({
        title: '¿Seguro que deseas anular este pago?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, anular',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../_controlador/pago_anular.php',
                type: 'POST',
                data: { id_pago: id_pago },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Anulado!',
                            text: response.message,
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            window.location.href = 'pago_registrar.php?id_compra=' + response.id_compra;
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
</script>

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
                                <td><?php echo $oc['id_compra']; ?></td>
                                <td><strong>Proveedor:</strong></td>
                                <td><?php echo $oc['nom_proveedor']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Subtotal:</strong></td>
                                <td><?php echo ($oc['sim_moneda'] ?? 'S/.') . ' ' . number_format($oc['subtotal'], 2); ?></td>
                                <td><strong>IGV:</strong></td>
                                <td><?php echo ($oc['sim_moneda'] ?? 'S/.') . ' ' . number_format($oc['total_igv'], 2); ?></td>
                            </tr>
                            
                            <!--  MONTO DE LA FACTURA (destacado) -->
                            <tr style="background-color: #d4edda; border: 2px solid #28a745;">
                                <td colspan="2">
                                    <strong style="font-size: 16px;"> MONTO DE LA FACTURA (Total con IGV):</strong></td>
                                <td colspan="2">
                                    <span class="badge badge-success" style="font-size: 18px; padding: 10px 16px;">
                                        <?php echo ($oc['sim_moneda'] ?? 'S/.') . ' ' . number_format($oc['total_con_igv'], 2); ?>
                                    </span>
                                </td>
                            </tr>
                            
                            <!-- 🔹 Mostrar Detracción si existe -->
                            <?php if (!empty($oc['monto_detraccion']) && $oc['monto_detraccion'] > 0): ?>
                            <tr>
                                <td colspan="2">
                                    <strong> Detracción (<?php echo $oc['nombre_detraccion']; ?> - <?php echo $oc['porcentaje_detraccion']; ?>%):</strong>
                                    <br><small class="text-muted">A depositar en cuenta SUNAT del proveedor</small>
                                </td>
                                <td colspan="2" class="text-danger" style="font-weight: bold;">
                                    -<?php echo ($oc['sim_moneda'] ?? 'S/.') . ' ' . number_format($oc['monto_detraccion'], 2); ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            
                            <!--  Mostrar Retención si existe -->
                            <?php if (!empty($oc['monto_retencion']) && $oc['monto_retencion'] > 0): ?>
                            <tr>
                                <td colspan="2">
                                    <strong> Retención (<?php echo $oc['nombre_retencion']; ?> - <?php echo $oc['porcentaje_retencion']; ?>%):</strong>
                                    <br><small class="text-muted">A retener y declarar a SUNAT</small>
                                </td>
                                <td colspan="2" class="text-info" style="font-weight: bold;">
                                    -<?php echo ($oc['sim_moneda'] ?? 'S/.') . ' ' . number_format($oc['monto_retencion'], 2); ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            
                            <!--  Mostrar Percepción si existe -->
                            <?php if (!empty($oc['monto_percepcion']) && $oc['monto_percepcion'] > 0): ?>
                            <tr>
                                <td colspan="2">
                                    <strong> Percepción (<?php echo $oc['nombre_percepcion']; ?> - <?php echo $oc['porcentaje_percepcion']; ?>%):</strong>
                                    <br><small class="text-muted">Impuesto adicional a pagar</small>
                                </td>
                                <td colspan="2" class="text-success" style="font-weight: bold;">
                                    +<?php echo ($oc['sim_moneda'] ?? 'S/.') . ' ' . number_format($oc['monto_percepcion'], 2); ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            
                            <!--  TOTAL A PAGAR AL PROVEEDOR (más destacado) -->
                            <tr style="background-color: #e3f2fd;">
                                <td colspan="2"><strong> TOTAL:</strong>
                                    <br><small class="text-muted">Monto real a pagar en cuenta bancaria</small>
                                </td>
                                <td colspan="2">
                                    <span class="badge badge-info" style="font-size: 15px; padding: 8px 14px;">
                                        <?php echo ($oc['sim_moneda'] ?? 'S/.') . ' ' . number_format($oc['monto_total'], 2); ?>
                                    </span>
                                </td>
                            </tr>
                            
                            <tr>
                                <td><strong> Pagado:</strong></td>
                                <td><?php echo ($oc['sim_moneda'] ?? 'S/.') . ' ' . number_format($oc['monto_pagado'], 2); ?></td>
                                <td><strong> Saldo Pendiente:</strong></td>
                                <td>
                                    <span class="badge badge-<?php echo ($oc['saldo'] > 0 ? 'warning' : 'success'); ?>" style="font-size: 14px; padding: 6px 12px;">
                                        <?php echo ($oc['sim_moneda'] ?? 'S/.') . ' ' . number_format($oc['saldo'], 2); ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Listado de pagos -->
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Pagos Registrados</h2>
                        <div class="pull-right">
                            <button
                                class="btn btn-sm <?php echo ($oc['est_compra'] == 4) ? 'btn-outline-secondary disabled' : 'btn-outline-success'; ?>"
                                <?php echo ($oc['est_compra'] == 4) ? 'disabled title="Esta compra está cerrada"' : 'data-toggle="modal" data-target="#modalRegistrarPago"'; ?>>
                                <i class="fa fa-plus"></i> Registrar Pago
                            </button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Monto</th>
                                    <th>Cuenta</th>
                                    <th>Comprobante</th>
                                    <th>Fecha</th>
                                    <th>Registrado por</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=1; foreach($pagos as $pago) { ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo number_format($pago['monto'],2); ?></td>
                                    <td><?php echo $pago['num_cuenta']; ?></td>
                                    <td>
                                        <?php if (!empty($pago['comprobante'])): ?>
                                            <a href="../<?php echo $pago['comprobante']; ?>" target="_blank">Ver Comprobante</a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($pago['fecha_reg'])); ?></td>
                                    <td><?php echo $pago['nom_personal']; ?></td>
                                    <td class="text-center">
                                        <?php if (!isset($pago['est_pago']) || $pago['est_pago'] == 1): ?>
                                            <span class="badge badge-success badge_size">Registrado</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger badge_size">Anulado</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center">
                                        <?php if (!isset($pago['est_pago']) || $pago['est_pago'] == 1): ?>
                                            <button class="btn btn-outline-danger btn-sm" title="Anular Pago" onclick="AnularPago(<?php echo $pago['id_pago']; ?>)">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-outline-secondary btn-sm" title="Anulado" disabled>
                                                <i class="fa fa-times"></i>
                                                
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php } ?>
                                <?php if(empty($pagos)) { ?>
                                <tr><td colspan="6" class="text-center">No hay pagos registrados</td></tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Registrar Pago -->
<div class="modal fade" id="modalRegistrarPago" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="pago_registrar.php" method="post" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Nuevo Pago</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_compra" value="<?php echo $oc['id_compra']; ?>">
                <div class="form-group">
                    <label>Monto a Pagar</label>
                    <input type="number" step="0.01" name="monto" class="form-control" max="<?php echo $oc['saldo']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Cuenta Bancaria del Proveedor</label>
                    <select name="id_cuenta" class="form-control" required>
                        <option value="">Seleccionar cuenta</option>
                        <?php foreach($cuentas as $cta) { ?>
                            <option value="<?php echo $cta['id_proveedor_cuenta']; ?>">
                                <?php echo $cta['banco_proveedor'].' - '.$cta['nro_cuenta_corriente'].' ('.$cta['nom_moneda'].')'; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Comprobante (archivo)</label>
                    <input type="file" name="comprobante" class="form-control" required>
                </div>
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" name="enviar_correo" id="enviar_correo" value="1" checked>
                    <label class="form-check-label" for="enviar_correo">Notificar por correo al Proveedor</label>
                </div>
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" name="enviar_correo2" id="enviar_correo2" value="1" checked>
                    <label class="form-check-label" for="enviar_correo2">Notificar por correo a Contabilidad</label>
                </div>
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" name="enviar_correo3" id="enviar_correo3" value="1" checked>
                    <label class="form-check-label" for="enviar_correo3">Notificar por correo a Tesorería</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Guardar Pago</button>
            </div>
        </form>
    </div>
</div>