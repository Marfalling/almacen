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
                        <!-- 🔹 Filtro de fechas -->
                        <form method="get" action="pedidos_mostrar.php" class="form-inline mb-3">
                            <label for="fecha_inicio" class="mr-2">Desde:</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control mr-2"
                                value="<?php echo htmlspecialchars($fecha_inicio ?? date('Y-m-d')); ?>">

                            <label for="fecha_fin" class="mr-2">Hasta:</label>
                            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control mr-2"
                                value="<?php echo htmlspecialchars($fecha_fin ?? date('Y-m-d')); ?>">

                            <button type="submit" class="btn btn-primary">Consultar</button>
                        </form>
                        <!-- 🔹 Fin filtro -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Código Pedido</th>
                                                <th>Tipo Pedido</th>
                                                <th>Nombre Pedido</th>
                                                <th>Almacén</th>
                                                <th>Ubicación</th>
                                                <th>Centro de Costos</th>
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
                                                    <td><?php echo $pedido['nom_producto_tipo']; ?></td>
                                                    <td><?php echo $pedido['nom_pedido']; ?></td>
                                                    <td><?php echo $pedido['nom_almacen']; ?></td>
                                                    <td><?php echo $pedido['nom_ubicacion']; ?></td>
                                                    <td><?php echo $pedido['nom_centro_costo']; ?></td>
                                                    <td><?php echo $pedido['nom_personal'] . ' ' . $pedido['ape_personal']; ?></td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($pedido['fec_pedido'])); ?></td>
                                                    <td><?php echo date('d/m/Y', strtotime($pedido['fec_req_pedido'])); ?></td>
                                                    <td>
                                                        <?php 
                                                        // Verificar si este pedido está en la lista de rechazados
                                                        $es_rechazado = in_array($pedido['id_pedido'], $pedidos_rechazados);
                                                        
                                                        if ($pedido['est_pedido_calc'] == 1 && $es_rechazado) { ?>
                                                            <span class="badge badge-danger badge_size">Rechazado</span>
                                                        <?php } elseif($pedido['est_pedido_calc'] == 1) { ?>
                                                            <span class="badge badge-warning badge_size">Pendiente</span>
                                                        <?php } elseif($pedido['est_pedido_calc'] == 2) { ?>
                                                            <span class="badge badge-success badge_size">Completado</span>
                                                        <?php } elseif($pedido['est_pedido_calc'] == 0) { ?>
                                                            <span class="badge badge-danger badge_size">Anulado</span>
                                                        <?php } ?>
                                                    </td>
<td>
    <div class="d-flex flex-wrap gap-2">
        <!-- Botón Ver Detalle -->
        <button type="button" 
                class="btn btn-info btn-sm" 
                data-toggle="modal" 
                data-target="#modalDetallePedido<?php echo $pedido['id_pedido']; ?>" 
                title="Ver Detalle">
            <i class="fa fa-eye"></i>
        </button>

        <?php
        $es_rechazado = in_array($pedido['id_pedido'], $pedidos_rechazados);
        
        // Botón Editar Pedido - Solo si no tiene verificaciones
        if ($pedido['tiene_verificados'] == 1 || $pedido['est_pedido_calc'] == 0 || $pedido['est_pedido_calc'] == 2 || $es_rechazado) { 
            $titulo_editar = $es_rechazado ? "No se puede editar - Pedido rechazado" : "No se puede editar - Pedido verificado";
        ?>
            <a href="#" class="btn btn-outline-secondary btn-sm disabled" 
               title="<?php echo $titulo_editar; ?>" tabindex="-1" aria-disabled="true">
                <i class="fa fa-edit"></i>
            </a>
        <?php } else { ?>
            <a href="pedidos_editar.php?id=<?php echo $pedido['id_pedido']; ?>" 
               class="btn btn-warning btn-sm" 
               title="Editar Pedido">
                <i class="fa fa-edit"></i>
            </a>
        <?php } ?>

        <!-- NUEVO: Botón Ver/Editar Órdenes de Compra -->
        <?php 
        // Verificar si tiene órdenes de compra
        $ordenes = ConsultarCompra($pedido['id_pedido']);
        $tiene_ordenes = !empty($ordenes);
        
        // Verificar si tiene alguna orden SIN aprobaciones (editable)
        $tiene_orden_editable = false;
        if ($tiene_ordenes) {
            foreach ($ordenes as $orden) {
                $sin_aprobacion_tecnica = empty($orden['id_personal_aprueba_tecnica']);
                $sin_aprobacion_financiera = empty($orden['id_personal_aprueba_financiera']);
                if ($orden['est_compra'] == 1 && $sin_aprobacion_tecnica && $sin_aprobacion_financiera) {
                    $tiene_orden_editable = true;
                    break;
                }
            }
        }
        
        if ($pedido['est_pedido_calc'] == 2) { 
            // Pedido completado - solo ver
        ?>
            <a href="pedido_verificar.php?id=<?php echo $pedido['id_pedido']; ?>" 
               class="btn btn-secondary btn-sm" 
               title="Ver Órdenes de Compra">
                <i class="fa fa-file-text"></i> Ver OC
            </a>
        <?php } elseif ($pedido['est_pedido_calc'] == 0 || $es_rechazado) { 
            // Pedido anulado/rechazado - no accesible
        ?>
            <a href="#" class="btn btn-outline-secondary btn-sm disabled" 
               title="No disponible - Pedido anulado/rechazado" tabindex="-1" aria-disabled="true">
                <i class="fa fa-file-text"></i>
            </a>
        <?php } elseif ($tiene_orden_editable) { 
            // Tiene órdenes editables - botón amarillo
        ?>
            <!--
            <a href="pedido_verificar.php?id=<?php echo $pedido['id_pedido']; ?>" 
               class="btn btn-warning btn-sm" 
               title="Ver/Editar Órdenes de Compra">
                <i class="fa fa-file-text"></i> Editar OC
            </a>
            -->
        <?php } elseif ($tiene_ordenes) { 
            // Tiene órdenes pero todas con aprobaciones - solo ver
        ?>
            <!-- <a href="pedido_verificar.php?id=<?php echo $pedido['id_pedido']; ?>" 
               class="btn btn-info btn-sm" 
               title="Ver Órdenes de Compra (no editables)">
                <i class="fa fa-file-text"></i> Ver OC
            </a>
            -->
        <?php } ?>

        <!-- Botón Verificar -->
        <?php if ($pedido['est_pedido_calc'] == 2) { ?>
            <a href="#" class="btn btn-outline-secondary btn-sm disabled" 
               title="Pedido completado" tabindex="-1" aria-disabled="true">
                <i class="fa fa-check"></i>
            </a>
        <?php } elseif ($pedido['est_pedido_calc'] == 0 || $es_rechazado) { 
            $titulo_verificar = $es_rechazado ? "No se puede verificar - Pedido rechazado" : "No se puede verificar - Pedido anulado";
        ?>
            <a href="#" class="btn btn-outline-secondary btn-sm disabled" 
               title="<?php echo $titulo_verificar; ?>" tabindex="-1" aria-disabled="true">
                <i class="fa fa-check"></i>
            </a>
        <?php } else { ?>
            <a href="pedido_verificar.php?id=<?php echo $pedido['id_pedido']; ?>" 
               class="btn btn-success btn-sm" 
               title="Verificar">
                <i class="fa fa-check"></i>
            </a>
        <?php } ?>

        <!-- Botón PDF -->
        <a href="pedido_pdf.php?id=<?php echo $pedido['id_pedido']; ?>" 
           class="btn btn-secondary btn-sm" 
           title="Generar PDF"
           target="_blank">
            <i class="fa fa-file-pdf-o"></i>
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
                                        <th>Material/Servicio</th>
                                        <th>Unidad de Medida</th>
                                        <th>Cantidad</th>
                                        <th>Observaciones</th>
                                        <th>Descripción SST/MA/CA</th>
                                        <th>Archivos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $contador_detalle = 1;
                                    foreach ($pedido_detalle as $detalle) { 
                                        // Parsear comentarios para extraer unidad y observaciones
                                        $comentario = $detalle['com_pedido_detalle'];
                                        $unidad_nombre = '';
                                        $observaciones = '';
                                        
                                        // Extraer unidad de medida del comentario
                                        if (preg_match('/Unidad:\s*([^|]*)\s*\|/', $comentario, $matches)) {
                                            $unidad_nombre = trim($matches[1]);
                                        }
                                        
                                        // Extraer observaciones del comentario
                                        if (preg_match('/Obs:\s*(.*)$/', $comentario, $matches)) {
                                            $observaciones = trim($matches[1]);
                                        }
                                        
                                        // La descripción SST/MA/CA está en req_pedido
                                        $sst_descripcion = $detalle['req_pedido'];
                                    ?>
                                        <tr>
                                            <td><?php echo $contador_detalle; ?></td>
                                            <td>
                                                <strong><?php echo $detalle['prod_pedido_detalle']; ?></strong>
                                                <?php if (!empty($detalle['cod_material'])) { ?>
                                                    <br><small class="text-muted">Código: <?php echo $detalle['cod_material']; ?></small>
                                                <?php } elseif (!empty($detalle['nom_producto'])) { ?>
                                                    <br><small class="text-muted">Producto: <?php echo $detalle['nom_producto']; ?></small>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary badge_size"><?php echo $unidad_nombre; ?></span>
                                            </td>
                                            <td>
                                                <span class="badge badge-primary badge_size"><?php echo $detalle['cant_pedido_detalle']; ?></span>
                                                <?php if ($detalle['cant_fin_pedido_detalle'] !== null) { ?>
                                                    <br><small class="text-success">Verificado: <?php echo $detalle['cant_fin_pedido_detalle']; ?></small>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($observaciones)) { ?>
                                                    <small><?php echo $observaciones; ?></small>
                                                <?php } else { ?>
                                                    <small class="text-muted">Sin observaciones</small>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($sst_descripcion)) { ?>
                                                    <small><?php echo nl2br($sst_descripcion); ?></small>
                                                <?php } else { ?>
                                                    <small class="text-muted">Sin descripción SST/MA/CA</small>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $archivos_activos = ObtenerArchivosActivosDetalle($detalle['id_pedido_detalle']);
                                                
                                                if (!empty($archivos_activos)) { 
                                                    foreach ($archivos_activos as $archivo) { 
                                                        // Determinar el icono según la extensión
                                                        $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
                                                        $icono = 'fa-file';
                                                        $clase_color = 'text-info';
                                                        
                                                        switch ($extension) {
                                                            case 'pdf':
                                                                $icono = 'fa-file-pdf-o';
                                                                $clase_color = 'text-danger';
                                                                break;
                                                            case 'jpg':
                                                            case 'jpeg':
                                                            case 'png':
                                                            case 'gif':
                                                                $icono = 'fa-file-image-o';
                                                                $clase_color = 'text-success';
                                                                break;
                                                            case 'doc':
                                                            case 'docx':
                                                                $icono = 'fa-file-word-o';
                                                                $clase_color = 'text-primary';
                                                                break;
                                                            case 'xls':
                                                            case 'xlsx':
                                                                $icono = 'fa-file-excel-o';
                                                                $clase_color = 'text-warning';
                                                                break;
                                                        }
                                                ?>
                                                        <a href="../_archivos/pedidos/<?php echo $archivo; ?>" 
                                                        target="_blank" 
                                                        class="btn btn-sm btn-outline-primary mb-1 d-block text-left <?php echo $clase_color; ?>"
                                                        title="Ver <?php echo $archivo; ?>"
                                                        style="font-size: 11px;">
                                                            <i class="fa <?php echo $icono; ?>"></i> 
                                                            <?php echo strlen($archivo) > 20 ? substr($archivo, 0, 20) . '...' : $archivo; ?>
                                                        </a>
                                                <?php } 
                                                } else { ?>
                                                    <small class="text-muted">Sin archivos adjuntos</small>
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
                <?php
                $es_rechazado = in_array($pedido['id_pedido'], $pedidos_rechazados);
                // Si tiene detalles verificados o está rechazado, no se puede editar
                if ($pedido['tiene_verificados'] == 1 || $es_rechazado) { 
                    if ($es_rechazado) { ?>
                        <a href="#" class="btn btn-outline-secondary disabled" title="No se puede editar - Pedido rechazado" tabindex="-1" aria-disabled="true">
                            <i class="fa fa-edit"></i> Pedido rechazado
                        </a>
                    <?php } else { ?>
                        <a href="#" class="btn btn-outline-secondary disabled" title="No se puede editar - Pedido verificado" tabindex="-1" aria-disabled="true">
                            <i class="fa fa-edit"></i> No se puede editar
                        </a>
                    <?php } ?>
                <?php } else { ?>
                    <a href="pedidos_editar.php?id=<?php echo $pedido['id_pedido']; ?>" class="btn btn-warning">
                        <i class="fa fa-edit"></i> Editar Pedido
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php 
    }
} 
?>