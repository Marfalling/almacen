<?php 
//=======================================================================
// VISTA: v_devoluciones_mostrar.php
//=======================================================================

// ========================================================================
// VERIFICAR PERMISOS AL INICIO
// ========================================================================
$tiene_permiso_crear = verificarPermisoEspecifico('crear_devoluciones');
$tiene_permiso_editar = verificarPermisoEspecifico('editar_devoluciones');
$tiene_permiso_anular = verificarPermisoEspecifico('anular_devoluciones');
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Devoluciones<small></small></h3>
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
                                <h2>Listado de Devoluciones<small></small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-2">
                                <!-- ============================================ -->
                                <!-- BOTÓN NUEVA DEVOLUCIÓN -->
                                <!-- ============================================ -->
                                <?php if (!$tiene_permiso_crear) { ?>
                                    <a href="#" 
                                       class="btn btn-outline-secondary btn-sm btn-block disabled"
                                       title="No tienes permiso para crear devoluciones"
                                       tabindex="-1" 
                                       aria-disabled="true">
                                        <i class="fa fa-plus"></i> Nueva Devolución
                                    </a>
                                <?php } else { ?>
                                    <a href="devoluciones_nuevo.php" 
                                       class="btn btn-outline-info btn-sm btn-block"
                                       title="Crear nueva devolución">
                                        <i class="fa fa-plus"></i> Nueva Devolución
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <div class="x_content">
                        <!-- Filtro de fechas -->
                        <form method="get" action="devoluciones_mostrar.php" class="form-inline mb-3">
                            <div class="form-group mx-sm-2 mb-2">
                                <label for="fecha_inicio" class="mr-2">Desde:</label>
                                <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control"
                                    value="<?php echo htmlspecialchars($fecha_inicio); ?>">
                            </div>
                            <div class="form-group mx-sm-2 mb-2">
                                <label for="fecha_fin" class="mr-2">Hasta:</label>
                                <input type="date" id="fecha_fin" name="fecha_fin" class="form-control"
                                    value="<?php echo htmlspecialchars($fecha_fin); ?>">
                            </div>
                            <button type="submit" class="btn btn-primary mb-2"><i class="fa fa-search"></i> Consultar</button>
                            <button type="button" class="btn btn-secondary mb-2" onclick="window.location.href='devoluciones_mostrar.php'"><i class="bi bi-eraser"></i> Limpiar</button>
                        </form>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Nº Documento</th>
                                                <th>Almacén</th>
                                                <th>Ubicación</th>
                                                <th>Cliente destino</th>
                                                <th>Registrado por</th>
                                                <th>Fecha de Devolución</th>
                                                <th>Observaciones</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php 
                                            $contador = 1;
                                            foreach($devoluciones as $devolucion) { 
                                            ?>
                                                <tr>
                                                    <td><?php echo $contador; ?></td>
                                                    <td><?php echo 'D00' . $devolucion['id_devolucion']; ?></td>
                                                    <td><?php echo $devolucion['nom_almacen']; ?></td>
                                                    <td><?php echo $devolucion['nom_ubicacion']; ?></td>
                                                    <td><?php echo $devolucion['nom_cliente_destino']; ?></td>
                                                    <td><?php echo $devolucion['nom_personal']; ?></td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($devolucion['fec_devolucion'])); ?></td>
                                                    <td><?php echo $devolucion['obs_devolucion']; ?></td>
                                                    <td>       
                                                        <center>
                                                            <?php if($devolucion['est_devolucion'] == 1) { ?>
                                                            <span class="badge badge-success badge_size">PENDIENTE</span>
                                                            <?php } elseif($devolucion['est_devolucion'] == 2) { ?>
                                                                <span class="badge badge-success badge_size">CONFIRMADO</span>
                                                            <?php } else { ?>
                                                                <span class="badge badge-danger badge_size">ANULADO</span>
                                                            <?php } ?>
                                                        </center>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            <!-- Botón Ver Detalle -->
                                                            <span data-toggle="tooltip" title="Ver Detalle">
                                                                <button type="button" 
                                                                    class="btn btn-info btn-sm" 
                                                                    data-toggle="modal" 
                                                                    data-target="#modalDetalleDevolucion<?php echo $devolucion['id_devolucion']; ?>">
                                                                    <i class="fa fa-eye"></i>
                                                                </button>
                                                            </span>

                                                            <!-- ============================================ -->
                                                            <!-- BOTÓN EDITAR DEVOLUCIÓN -->
                                                            <!-- ============================================ -->
                                                            <?php
                                                            $puede_editar = ($devolucion['est_devolucion'] == 1);
                                                            $titulo_editar = '';
                                                            
                                                            if (!$tiene_permiso_editar) {
                                                                $titulo_editar = "No tienes permiso para editar devoluciones";
                                                            } elseif (!$puede_editar) {
                                                                $titulo_editar = "No se puede editar - Devolución ya procesada";
                                                            }
                                                            
                                                            if (!$tiene_permiso_editar) { ?>
                                                                    <a href="#"
                                                                   data-toggle="tooltip"
                                                                       class="btn btn-outline-secondary btn-sm disabled"
                                                                   title="<?php echo $titulo_editar; ?>"
                                                                       tabindex="-1"
                                                                       aria-disabled="true">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                <?php } elseif (!$puede_editar) { ?>
                                                                    <a href="#"
                                                                       class="btn btn-outline-secondary btn-sm disabled"
                                                                   title="<?php echo $titulo_editar; ?>"
                                                                       tabindex="-1"
                                                                   data-toggle="tooltip"
                                                                       aria-disabled="true">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                <?php } else { ?>
                                                                    <a href="devoluciones_editar.php?id=<?php echo $devolucion['id_devolucion']; ?>" 
                                                                   class="btn btn-warning btn-sm" 
                                                                   title="Editar"
                                                                   data-toggle="tooltip"
                                                                   title="Editar devolución">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                <?php } ?>

                                                            <!-- Botón PDF -->
                                                                <a href="devoluciones_pdf.php?id=<?php echo $devolucion['id_devolucion']; ?>" 
                                                                   class="btn btn-secondary btn-sm" 
                                                               title="Generar PDF"
                                                               data-toggle="tooltip"
                                                                   target="_blank">
                                                                    <i class="fa fa-file-pdf-o"></i>
                                                                </a>

                                                            <!-- Botón Confirmar -->
                                                            <form method="post" action="devoluciones_mostrar.php" style="display:inline;">
                                                                <input type="hidden" name="id_devolucion" value="<?php echo $devolucion['id_devolucion']; ?>">
                                                                <input type="hidden" name="confirmar" value="1">
                                                                
                                                                <?php if ($devolucion['est_devolucion'] != 1) { ?>
                                                                        <button type="button" 
                                                                                class="btn btn-outline-secondary btn-sm disabled"
                                                                            title="Confirmar Devolución"
                                                                            data-toggle="tooltip"
                                                                            disabled>
                                                                            <i class="fa fa-check"></i>
                                                                        </button>
                                                                    <?php } else { ?>
                                                                        <button type="button" 
                                                                                name="confirmar" 
                                                                            class="btn btn-success btn-sm btn-confirmar" 
                                                                            title="Confirmar Devolución" data-toggle="tooltip">
                                                                            <i class="fa fa-check"></i>
                                                                        </button>
                                                                    <?php } ?>
                                                            </form>

                                                            <!-- ============================================ -->
                                                            <!-- BOTÓN ANULAR DEVOLUCIÓN -->
                                                            <!-- ============================================ -->
                                                            <?php
                                                            $puede_anular = ($devolucion['est_devolucion'] == 1);
                                                            $titulo_anular = '';
                                                            
                                                            if (!$tiene_permiso_anular) {
                                                                $titulo_anular = "No tienes permiso para anular devoluciones";
                                                            } elseif (!$puede_anular) {
                                                                $titulo_anular = "No se puede anular - Devolución ya procesada";
                                                            } else {
                                                                $titulo_anular = "Anular Devolución";
                                                            }
                                                            ?>
                                                            
                                                            <form method="post" action="devoluciones_anular.php" style="display:inline;">
                                                                <input type="hidden" name="id_devolucion" value="<?php echo $devolucion['id_devolucion']; ?>">
                                                                <input type="hidden" name="anular" value="1">
                                                                
                                                                <span data-toggle="tooltip" title="<?php echo $titulo_anular; ?>">
                                                                    <?php if (!$tiene_permiso_anular) { ?>
                                                                        <button type="button" 
                                                                                class="btn btn-outline-secondary btn-sm disabled"
                                                                                tabindex="-1"
                                                                                aria-disabled="true">
                                                                            <i class="fa fa-times"></i>
                                                                        </button>
                                                                    <?php } elseif (!$puede_anular) { ?>
                                                                        <button type="button" 
                                                                                class="btn btn-outline-secondary btn-sm disabled"
                                                                                tabindex="-1"
                                                                                aria-disabled="true">
                                                                            <i class="fa fa-times"></i>
                                                                        </button>
                                                                    <?php } else { ?>
                                                                        <button type="button" 
                                                                                name="anular" 
                                                                                class="btn btn-danger btn-sm btn-anular">
                                                                            <i class="fa fa-times"></i>
                                                                        </button>
                                                                    <?php } ?>
                                                                </span>
                                                            </form>

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
 
<!-- Modales para ver detalle de cada devolución -->
<?php 
foreach($devoluciones as $devolucion) { 
    $dev_data = ConsultarDevolucion($devolucion['id_devolucion']);
    $dev_detalle = ConsultarDevolucionDetalleConCentros($devolucion['id_devolucion']);
    
    if (!empty($dev_data)) {
        $dev_info = $dev_data[0];
?>
<div class="modal fade" id="modalDetalleDevolucion<?php echo $devolucion['id_devolucion']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalDetalleDevolucionLabel<?php echo $devolucion['id_devolucion']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetalleDevolucionLabel<?php echo $devolucion['id_devolucion']; ?>">
                    Detalle de Devolución - D00<?php echo $dev_info['id_devolucion']; ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h5><strong>Información General</strong></h5>
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>ID Devolución:</strong></td>
                                <td>D00<?php echo $dev_info['id_devolucion']; ?></td>
                                <td><strong>Fecha y hora:</strong></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($dev_info['fec_devolucion'])); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Almacén:</strong></td>
                                <td><?php echo $dev_info['nom_almacen']; ?></td>
                                <td><strong>Ubicación:</strong></td>
                                <td><?php echo $dev_info['nom_ubicacion']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Cliente destino:</strong></td>
                                <td colspan="3"><?php echo $dev_info['nom_cliente_destino']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Registrado por:</strong></td>
                                <td colspan="3"><?php echo $dev_info['nom_personal']; ?></td>
                            </tr>
                            <!-- 🔹 MOSTRAR CENTRO DE COSTO DEL REGISTRADOR -->
                            <tr>
                                <td><strong>Centro de Costo (Registrador):</strong></td>
                                <td colspan="3">
                                    <span class="badge badge-primary badge_size">
                                        <?php echo !empty($dev_info['nom_centro_costo_registrador']) 
                                                ? $dev_info['nom_centro_costo_registrador'] 
                                                : 'Sin asignar'; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php if (!empty($dev_info['obs_devolucion'])) { ?>
                            <tr>
                                <td><strong>Observaciones:</strong></td>
                                <td colspan="3"><?php echo $dev_info['obs_devolucion']; ?></td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <h5><strong>Detalles de la Devolución</strong></h5>
                        <?php if (!empty($dev_detalle)) { ?>
                            <table class="table table-striped table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th style="width: 30%;">Producto</th>
                                        <th style="width: 10%;">Cantidad</th>
                                        <th style="width: 10%;">Unidad</th>
                                        <th style="width: 25%;">Detalle</th>
                                        <!--  COLUMNA PARA CENTROS DE COSTO -->
                                        <th style="width: 20%;">Centro(s) de Costo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $contador_det = 1;
                                    foreach ($dev_detalle as $detalle) { 
                                        // 🔹 CONSTRUIR HTML DE CENTROS DE COSTO CON MODAL
                                        $centrosCostoHtml = '<small class="text-muted">Sin asignar</small>';
                                        
                                        if (!empty($detalle['centros_costo']) && is_array($detalle['centros_costo'])) {
                                            $totalCentros = count($detalle['centros_costo']);
                                            $modalId = "modalCentrosCostoDevolucion{$devolucion['id_devolucion']}_{$contador_det}";
                                            
                                            if ($totalCentros === 1) {
                                                
                                                $centrosCostoHtml = '<span class="badge badge-info badge_size" style="font-size: 11px;">' 
                                                    . htmlspecialchars($detalle['centros_costo'][0]['nom_centro_costo']) 
                                                    . '</span>';
                                            } else {
                                               
                                                $listaCentros = '';
                                                foreach ($detalle['centros_costo'] as $idx => $centro) {
                                                    $listaCentros .= '<div style="padding: 8px; margin-bottom: 6px; background-color: #f8f9fa; border-left: 3px solid #17a2b8; border-radius: 4px;">';
                                                    $listaCentros .= '<strong style="color: #17a2b8;">' . ($idx + 1) . '.</strong> ';
                                                    $listaCentros .= htmlspecialchars($centro['nom_centro_costo']);
                                                    $listaCentros .= '</div>';
                                                }
                                                
                                                $centrosCostoHtml = '
                                                    <button class="btn btn-sm btn-info" 
                                                            type="button" 
                                                            data-toggle="modal"
                                                            data-target="#' . $modalId . '"
                                                            style="font-size: 11px; padding: 3px 10px;">
                                                        <i class="fa fa-eye"></i> Ver ' . $totalCentros . ' centros
                                                    </button>
                                                    
                                                    <!-- Modal para centros de costo -->
                                                    <div class="modal fade" id="' . $modalId . '" tabindex="-1" role="dialog" data-backdrop="static">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header" style="background-color: #17a2b8; color: white; padding: 12px 20px;">
                                                                    <h6 class="modal-title mb-0">
                                                                        <i class="fa fa-building"></i> 
                                                                        Centros de Costo Asignados
                                                                    </h6>
                                                                    <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 0.8;">
                                                                        <span>&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body" style="padding: 20px;">
                                                                    <div style="margin-bottom: 15px; padding: 10px; background-color: #e7f3ff; border-radius: 4px; border-left: 4px solid #17a2b8;">
                                                                        <strong>Producto:</strong> ' . htmlspecialchars($detalle['nom_producto']) . '
                                                                    </div>
                                                                    <div style="max-height: 400px; overflow-y: auto;">
                                                                        ' . $listaCentros . '
                                                                    </div>
                                                                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6; text-align: center;">
                                                                        <span class="badge badge-info" style="font-size: 12px; padding: 6px 12px;">
                                                                            Total: ' . $totalCentros . ' centro(s) de costo
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
                                                    </div>';
                                            }
                                        }
                                    ?>
                                        <tr>
                                            <td><?php echo $contador_det; ?></td>
                                            <td><?php echo htmlspecialchars($detalle['nom_producto']); ?></td>
                                            <td class="text-center"><?php echo number_format($detalle['cant_devolucion_detalle'], 2); ?></td>
                                            <td><?php echo htmlspecialchars($detalle['nom_unidad_medida']); ?></td>
                                            <td><?php echo htmlspecialchars($detalle['det_devolucion_detalle']); ?></td>
                                            <!--  MOSTRAR CENTROS DE COSTO -->
                                            <td><?php echo $centrosCostoHtml; ?></td>
                                        </tr>
                                    <?php 
                                        $contador_det++;
                                    } 
                                    ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> No hay detalles para esta devolución.
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                
                <?php 
                // BOTÓN EDITAR EN MODAL - CON VALIDACIÓN DE PERMISOS
                $puede_editar_modal = ($devolucion['est_devolucion'] == 1);
                $titulo_editar_modal = '';
                
                if (!$tiene_permiso_editar) {
                    $titulo_editar_modal = "No tienes permiso para editar devoluciones";
                } elseif (!$puede_editar_modal) {
                    $titulo_editar_modal = "No se puede editar - Devolución ya procesada";
                } else {
                    $titulo_editar_modal = "Editar Devolución";
                }
                ?>
                
                <?php if (!$tiene_permiso_editar) { ?>
                    <button type="button" 
                            class="btn btn-outline-secondary disabled"
                            title="<?php echo $titulo_editar_modal; ?>"
                            tabindex="-1"
                            aria-disabled="true">
                        <i class="fa fa-edit"></i> Editar Devolución
                    </button>
                <?php } elseif (!$puede_editar_modal) { ?>
                    <button type="button" 
                            class="btn btn-outline-secondary disabled"
                            title="<?php echo $titulo_editar_modal; ?>"
                            tabindex="-1"
                            aria-disabled="true">
                        <i class="fa fa-edit"></i> Editar Devolución
                    </button>
                <?php } else { ?>
                    <a href="devoluciones_editar.php?id=<?php echo $devolucion['id_devolucion']; ?>" 
                       class="btn btn-warning text-white"
                       title="<?php echo $titulo_editar_modal; ?>">
                        <i class="fa fa-edit"></i> Editar Devolución
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

<script>
$(document).ready(function() {
    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip({
        placement: 'top',
        trigger: 'hover'
    });

    $('button[data-toggle="modal"][title]').hover(
        function() {
            // Mouse ENTRA
            var $btn = $(this);
            var title = $btn.attr('title');
            var pos = $btn.offset();
            
            // Crear tooltip manualmente
            var $tooltip = $('<div class="custom-tooltip">' + title + '</div>');
            $tooltip.css({
                position: 'absolute',
                top: pos.top - 35,
                left: pos.left + ($btn.outerWidth() / 2) - 50,
                background: '#000',
                color: '#fff',
                padding: '5px 10px',
                borderRadius: '4px',
                fontSize: '12px',
                zIndex: 9999,
                whiteSpace: 'nowrap'
            });
            
            $('body').append($tooltip);
            $tooltip.fadeIn(200);
        },
        function() {
            // Mouse SALE
            $('.custom-tooltip').fadeOut(200, function() {
                $(this).remove();
            });
        }
    );
    
    // Ocultar al hacer clic
    $('button[data-toggle="modal"][title]').on('click', function() {
        $('.custom-tooltip').remove();
    });

});
</script>