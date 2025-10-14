<?php
// VISTA: v_pagos_registrar.php
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
                                <td><strong>Monto Total:</strong></td>
                                <td><?php echo number_format($oc['monto_total'],2); ?></td>
                                <td><strong>Pagado:</strong></td>
                                <td><?php echo number_format($oc['monto_pagado'],2); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Saldo Pendiente:</strong></td>
                                <td colspan="3">
                                    <span class="badge badge-<?php echo ($oc['saldo']>0?'warning':'success'); ?>">
                                        <?php echo number_format($oc['saldo'],2); ?>
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
                                    <td><?php echo $pago['nom_personal'].' '.$pago['ape_personal']; ?></td>
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
                    <input type="file" name="comprobante" class="form-control">
                </div>
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" name="enviar_correo" id="enviar_correo" value="1">
                    <label class="form-check-label" for="enviar_correo">Notificar por correo al proveedor</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Guardar Pago</button>
            </div>
        </form>
    </div>
</div>