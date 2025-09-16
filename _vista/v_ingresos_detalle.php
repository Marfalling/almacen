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
                                                <td><span class="badge badge-primary badge-lg"><?php echo $detalle_ingreso['resumen']['total_productos']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Completamente Ingresados:</strong></td>
                                                <td><span class="badge badge-success badge-lg"><?php echo $detalle_ingreso['resumen']['productos_completos']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Parcialmente Ingresados:</strong></td>
                                                <td><span class="badge badge-warning badge-lg"><?php echo $detalle_ingreso['resumen']['productos_parciales']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Pendientes:</strong></td>
                                                <td><span class="badge badge-warning badge-lg"><?php echo $detalle_ingreso['resumen']['productos_pendientes']; ?></span></td>
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

                        
                        <!-- Historial de Ingresos -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2><i class="fa fa-history"></i> Historial de Ingresos</h2>
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
                                                            <th>Productos Ingresados</th>
                                                            <th>Cantidad Total</th>
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
                                                                <td>
                                                                    <span class="badge badge-info badge_size">
                                                                        <?php echo $ingreso['productos_count']; ?> producto(s)
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <span class="badge badge-success badge_size">
                                                                        <?php echo number_format($ingreso['cantidad_total'], 2); ?>
                                                                    </span>
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

@media print {
    .btn, .x_title .col-sm-2 {
        display: none !important;
    }
    
    .badge {
        color: #000 !important;
        border: 1px solid #000 !important;
    }
    
    .x_panel {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
}
</style>