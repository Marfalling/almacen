<?php 
//=======================================================================
// VISTA: v_pedidos_mostrar.php
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Pedidos<small></small></h3>
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
                                <h2>Listado de Pedidos<small></small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-2">
                                <a href="pedidos_nuevo.php" class="btn btn-outline-info btn-sm btn-block">Nuevo Pedido</a>
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
                                                <th>Código Pedido</th>
                                                <th>Nombre Pedido</th>
                                                <th>Solicitante</th>
                                                <th>Fecha Pedido</th>
                                                <th>Fecha Necesidad</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php 
                                            $contador = 1;
                                            foreach($pedidos as $pedido) { 
                                            ?>
                                                <tr>
                                                    <td><?php echo $contador; ?></td>
                                                    <td><?php echo $pedido['cod_pedido']; ?></td>
                                                    <td><?php echo $pedido['nom_pedido']; ?></td>
                                                    <td><?php echo $pedido['nom_personal'] . ' ' . $pedido['ape_personal']; ?></td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($pedido['fec_pedido'])); ?></td>
                                                    <td><?php echo date('d/m/Y', strtotime($pedido['fec_req_pedido'])); ?></td>
                                                    <td>
                                                        <?php if($pedido['est_pedido'] == 1) { ?>
                                                            <span class="badge badge-success">Activo</span>
                                                        <?php } else { ?>
                                                            <span class="badge badge-danger">Inactivo</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-info btn-sm" 
                                                                    data-toggle="modal" 
                                                                    data-target="#modalDetallePedido<?php echo $pedido['id_pedido']; ?>" 
                                                                    title="Ver Detalle">
                                                                <i class="fa fa-eye"></i>
                                                            </button>
                                                            <a href="pedidos_editar.php?id=<?php echo $pedido['id_pedido']; ?>" class="btn btn-warning btn-sm" title="Editar">
                                                                <i class="fa fa-edit"></i>
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

<!-- Modales para ver detalle de cada pedido -->
<?php 
foreach($pedidos as $pedido) { 
    // Obtener detalles del pedido para el modal
    $pedido_data = ConsultarPedido($pedido['id_pedido']);
    $pedido_detalle = ConsultarPedidoDetalle($pedido['id_pedido']);
    
    if (!empty($pedido_data)) {
        $pedido_info = $pedido_data[0];
?>
<div class="modal fade" id="modalDetallePedido<?php echo $pedido['id_pedido']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalDetallePedidoLabel<?php echo $pedido['id_pedido']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetallePedidoLabel<?php echo $pedido['id_pedido']; ?>">
                    Detalle del Pedido - <?php echo $pedido_info['cod_pedido']; ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h5><strong>Información General</strong></h5>
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>Código del Pedido:</strong></td>
                                <td><?php echo $pedido_info['cod_pedido']; ?></td>
                                <td><strong>Fecha del Pedido:</strong></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($pedido_info['fec_pedido'])); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Nombre del Pedido:</strong></td>
                                <td><?php echo $pedido_info['nom_pedido']; ?></td>
                                <td><strong>Fecha de Necesidad:</strong></td>
                                <td><?php echo date('d/m/Y', strtotime($pedido_info['fec_req_pedido'])); ?></td>
                            </tr>
                            <tr>
                                <td><strong>OT/LCL/LCA:</strong></td>
                                <td><?php echo $pedido_info['ot_pedido']; ?></td>
                                <td><strong>Contacto:</strong></td>
                                <td><?php echo $pedido_info['cel_pedido']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Lugar de Entrega:</strong></td>
                                <td colspan="3"><?php echo $pedido_info['lug_pedido']; ?></td>
                            </tr>
                            <?php if (!empty($pedido_info['acl_pedido'])) { ?>
                            <tr>
                                <td><strong>Aclaraciones:</strong></td>
                                <td colspan="3"><?php echo $pedido_info['acl_pedido']; ?></td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <h5><strong>Detalles del Pedido</strong></h5>
                        <?php if (!empty($pedido_detalle)) { ?>
                            <table class="table table-striped table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Descripción/Material</th>
                                        <th>Cantidad</th>
                                        <th>Comentarios</th>
                                        <th>Requisitos</th>
                                        <th>Archivos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $contador_detalle = 1;
                                    foreach ($pedido_detalle as $detalle) { 
                                    ?>
                                        <tr>
                                            <td><?php echo $contador_detalle; ?></td>
                                            <td><?php echo $detalle['prod_pedido_detalle']; ?></td>
                                            <td><?php echo $detalle['cant_pedido_detalle']; ?></td>
                                            <td><?php echo $detalle['com_pedido_detalle']; ?></td>
                                            <td>
                                                <small><?php echo str_replace('|', '<br>', $detalle['req_pedido']); ?></small>
                                            </td>
                                            <td>
                                                <?php if (!empty($detalle['archivos'])) { 
                                                    $archivos = explode(',', $detalle['archivos']);
                                                    foreach ($archivos as $archivo) {
                                                        if (!empty(trim($archivo))) {
                                                ?>
                                                            <a href="../_archivos/pedidos/<?php echo trim($archivo); ?>" 
                                                               target="_blank" class="btn btn-sm btn-outline-primary mb-1">
                                                                <i class="fa fa-download"></i> <?php echo trim($archivo); ?>
                                                            </a><br>
                                                <?php 
                                                        }
                                                    }
                                                } else { ?>
                                                    <span class="text-muted">Sin archivos</span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php 
                                        $contador_detalle++;
                                    } 
                                    ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> No hay detalles disponibles para este pedido.
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <a href="pedidos_editar.php?id=<?php echo $pedido['id_pedido']; ?>" class="btn btn-warning">
                    <i class="fa fa-edit"></i> Editar Pedido
                </a>
            </div>
        </div>
    </div>
</div>
<?php 
    }
} 
?>