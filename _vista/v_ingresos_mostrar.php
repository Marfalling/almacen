<?php 
//=======================================================================
// VISTA: v_ingresos_mostrar.php
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Ingresos por Orden de Compra<small></small></h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <!-- --------------------------------------- -->
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <div class="row">
                            <div class="col-sm-10">
                                <h2>Órdenes de Compra Aprobadas<small> - Pendientes de Ingreso</small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-2">
                                <!-- Espacio para futuros botones si es necesario -->
                            </div>
                        </div>
                    </div>

                    <div class="x_content">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>N° Orden</th>
                                                <th>Código Pedido</th>
                                                <th>Proveedor</th>
                                                <th>Almacén</th>
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
                                                // Calcular progreso para determinar estado
                                                $porcentaje = $ingreso['total_productos'] > 0 ? 
                                                    round(($ingreso['productos_ingresados'] / $ingreso['total_productos']) * 100) : 0;
                                            ?>
                                                <tr>
                                                    <td><?php echo $contador; ?></td>
                                                    <td><strong><?php echo $ingreso['id_compra']; ?></strong></td>
                                                    <td><a class="btn btn-sm btn-outline-secondary" target="_blank" href="pedido_pdf.php?id=<?php echo $ingreso['id_pedido']; ?>"><?php echo $ingreso['cod_pedido']; ?></a></td>
                                                    <td><?php echo $ingreso['nom_proveedor']; ?></td>
                                                    <td><?php echo $ingreso['nom_almacen']; ?></td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($ingreso['fec_compra'])); ?></td>
                                                    <td><?php echo $ingreso['registrado_por'] ?? 'No especificado'; ?></td>
                                                    <td><?php echo $ingreso['aprobado_por'] ?? 'Pendiente'; ?></td>
                                                    <td>
                                                        <?php if ($ingreso['est_compra'] == 3) { ?>
                                                            <span class="badge badge-success">Completado</span>
                                                        <?php } elseif ($ingreso['productos_ingresados'] > 0) { ?>
                                                            <span class="badge badge-warning">Parcial (<?php echo $ingreso['productos_ingresados']; ?>/<?php echo $ingreso['total_productos']; ?>)</span>
                                                        <?php } else { ?>
                                                            <span class="badge badge-warning">Pendiente</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            <?php if ($ingreso['est_compra'] != 3) { ?>
                                                            <!-- Botón Verificar - solo visible si NO está completado (estado != 3) --> 
                                                            <a href="ingresos_verificar.php?id_compra=<?php echo $ingreso['id_compra']; ?>" 
                                                               class="btn btn-info btn-sm"
                                                               title="Ver detalles de la orden de compra">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            <?php } ?>
                                                            <!-- Botón Ingresos - siempre visible -->
                                                            <a href="ingresos_detalle.php?id_compra=<?php echo $ingreso['id_compra']; ?>" 
                                                                class="btn btn-success btn-sm"
                                                                title="Ver detalles e ingresar productos al stock">
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
                </div>
            </div>
            <!-- --------------------------------------- -->
        </div>
    </div>
</div>
<!-- /page content -->