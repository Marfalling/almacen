<?php 
//=======================================================================
// VISTA: v_salidas_mostrar.php
//=======================================================================
?>

<script>
// Función para aprobar/recepcionar salida
function AprobarSalida(id_salida) {
    Swal.fire({
        title: '¿Deseas recepcionar esta salida?',
        text: "Esta acción registrará la fecha y hora de recepción.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, recepcionar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../_controlador/salidas_aprobar.php',
                type: 'POST',
                data: { id_salida: id_salida },
                dataType: 'json',
                success: function(response) {
                    if (response.tipo_mensaje === 'success') {
                        Swal.fire('¡Recepcionado!', response.mensaje, 'success')
                        .then(() => { location.reload(); });
                    } else {
                        Swal.fire('Error', response.mensaje, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                }
            });
        }
    });
}

// Función para anular salida
function AnularSalida(id_salida) {
    Swal.fire({
        title: '¿Seguro que deseas anular esta salida?',
        text: 'Se actualizarán los movimientos asociados',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, anular',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../_controlador/salidas_anular.php',
                type: 'POST',
                data: { id: id_salida },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('¡Anulada!', response.message, 'success')
                        .then(() => { location.reload(); });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                }
            });
        }
    });
}
</script>

<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Salidas<small></small></h3>
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
                                <h2>Listado de Salidas<small></small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-2">
                                <a href="salidas_nuevo.php" class="btn btn-outline-info btn-sm btn-block">
                                    <i class="fa fa-plus"></i> Nueva Salida
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="x_content">
                        <!-- Filtro de Fechas -->
                        <form method="get" action="salidas_mostrar.php" class="form-inline mb-3">
                            <label for="fecha_inicio" class="mr-2">Desde:</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control mr-2"
                                value="<?php echo htmlspecialchars($fecha_inicio ?? date('Y-m-01')); ?>">

                            <label for="fecha_fin" class="mr-2">Hasta:</label>
                            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control mr-2"
                                value="<?php echo htmlspecialchars($fecha_fin ?? date('Y-m-d')); ?>">

                            <button type="submit" class="btn btn-primary">Consultar</button>
                        </form>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Código Salida</th>
                                                <th>Nº Documento</th>
                                                <th>Tipo Material</th>
                                                <th>Almacén Origen</th>
                                                <th>Ubicación Origen</th>
                                                <th>Almacén Destino</th>
                                                <th>Ubicación Destino</th>
                                                <th>Fecha Requerida</th>
                                                <th>Fecha Registro</th>
                                                <th>Registrado por</th>
                                                <th>Recepcionado por</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                        <?php 
                                        $contador = 1;
                                        foreach($salidas as $salida) { 
                                                $tiene_aprobacion = !empty($salida['id_personal_aprueba_salida']);
                                        ?>
                                            <tr>
                                                <td><?php echo $contador; ?></td>
                                                <td><?php echo 'S00' . $salida['id_salida']; ?></td>
                                                <td><?php echo $salida['ndoc_salida']; ?></td>
                                                <td><?php echo $salida['nom_material_tipo']; ?></td>
                                                <td><?php echo $salida['nom_almacen_origen']; ?></td>
                                                <td><?php echo $salida['nom_ubicacion_origen']; ?></td>
                                                <td><?php echo $salida['nom_almacen_destino']; ?></td>
                                                <td><?php echo $salida['nom_ubicacion_destino']; ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($salida['fec_req_salida'])); ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($salida['fec_salida'])); ?></td>
                                                <td><?php echo $salida['nom_personal']; ?></td>
                                                <td>
                                                        <?php 
                                                        if ($tiene_aprobacion) {
                                                            echo $salida['nom_aprueba'];
                                                        } else {
                                                            echo '-';
                                                        }
                                                        ?>
                                                </td>
                                                <td>
                                                    <center>
                                                        <?php if ($salida['est_salida'] == 0) { ?>
                                                            <span class="badge badge-danger badge_size">ANULADA</span>
                                                        <?php } elseif ($salida['est_salida'] == 1) { ?>
                                                            <span class="badge badge-warning badge_size">PENDIENTE</span>
                                                        <?php } elseif ($salida['est_salida'] == 2) { ?>
                                                            <span class="badge badge-success badge_size">RECEPCIONADA</span>
                                                        <?php } ?>
                                                    </center>
                                                </td>

                                                <!-- Acciones -->
                                                <td>
                                                    <div class="d-flex flex-wrap gap-2">

                                                        <!-- Ver detalles -->
                                                        <button type="button" 
                                                                class="btn btn-info btn-sm"
                                                                data-toggle="modal"
                                                                data-target="#modalDetalleSalida<?php echo $salida['id_salida']; ?>"
                                                                title="Ver Detalle">
                                                            <i class="fa fa-eye"></i>
                                                        </button>

                                                        <?php if ($salida['est_salida'] == 1) { ?>
                                                                <!-- SALIDA ACTIVA: mostrar botones según estado de aprobación -->

                                                            <!-- Botón Recepcionar -->
                                                            <button onclick="AprobarSalida(<?php echo $salida['id_salida']; ?>)"
                                                                    class="btn btn-success btn-sm"
                                                                    title="Recepcionar Salida">
                                                                <i class="fa fa-check"></i>
                                                            </button>

                                                            <!-- Editar -->
                                                            <a href="salidas_editar.php?id=<?php echo $salida['id_salida']; ?>" 
                                                            class="btn btn-warning btn-sm" 
                                                            title="Editar">
                                                                <i class="fa fa-edit"></i>
                                                            </a>

                                                            <!-- PDF -->
                                                            <a href="salidas_pdf.php?id=<?php echo $salida['id_salida']; ?>" 
                                                            class="btn btn-secondary btn-sm" 
                                                            title="Generar PDF"
                                                            target="_blank">
                                                                <i class="fa fa-file-pdf-o"></i>
                                                            </a>

                                                            <!-- Anular -->
                                                            <button class="btn btn-danger btn-sm"
                                                                    onclick="AnularSalida(<?php echo $salida['id_salida']; ?>)"
                                                                    title="Anular">
                                                                <i class="fa fa-times"></i>
                                                            </button>

                                                        <?php } elseif ($salida['est_salida'] == 2) { ?>

                                                            <!-- TODO DESHABILITADO -->
                                                            <button class="btn btn-outline-secondary btn-sm disabled">
                                                                <i class="fa fa-check"></i>
                                                            </button>

                                                            <button class="btn btn-outline-secondary btn-sm disabled">
                                                                <i class="fa fa-edit"></i>
                                                            </button>

                                                            <a href="salidas_pdf.php?id=<?php echo $salida['id_salida']; ?>" 
                                                            class="btn btn-secondary btn-sm"
                                                            title="PDF"
                                                            target="_blank">
                                                                <i class="fa fa-file-pdf-o"></i>
                                                            </a>

                                                            <button class="btn btn-outline-secondary btn-sm disabled">
                                                                <i class="fa fa-times"></i>
                                                            </button>

                                                        <?php } else { ?>
                                                            <!-- Anulada: bloquear todo excepto PDF -->
                                                            <button class="btn btn-outline-secondary btn-sm disabled">
                                                                <i class="fa fa-check"></i>
                                                            </button>

                                                            <button class="btn btn-outline-secondary btn-sm disabled">
                                                                <i class="fa fa-edit"></i>
                                                            </button>

                                                            <a href="salidas_pdf.php?id=<?php echo $salida['id_salida']; ?>" 
                                                            class="btn btn-secondary btn-sm"
                                                            title="PDF"
                                                            target="_blank">
                                                                <i class="fa fa-file-pdf-o"></i>
                                                            </a>

                                                            <button class="btn btn-outline-secondary btn-sm disabled">
                                                                <i class="fa fa-times"></i>
                                                            </button>

                                                        <?php } ?>

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

<!-- Modales para ver detalle de cada salida -->
<?php 
foreach($salidas as $salida) { 
    // Obtener detalles de la salida para el modal
    $salida_data = ConsultarSalida($salida['id_salida']);
    $salida_detalle = ConsultarSalidaDetalle($salida['id_salida']);
    $salida_docs = MostrarDocumentos('salidas', $salida['id_salida']);
    
    if (!empty($salida_data)) {
        $salida_info = $salida_data[0];
?>
<div class="modal fade" id="modalDetalleSalida<?php echo $salida['id_salida']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalDetalleSalidaLabel<?php echo $salida['id_salida']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetalleSalidaLabel<?php echo $salida['id_salida']; ?>">
                    Detalle de Salida - <?php echo $salida_info['ndoc_salida']; ?>
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
                                <td><strong>Nº Documento:</strong></td>
                                <td><?php echo $salida_info['ndoc_salida']; ?></td>
                                <td><strong>Fecha de Traslado:</strong></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($salida_info['fec_salida'])); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tipo de Material:</strong></td>
                                <td><?php echo $salida_info['nom_material_tipo']; ?></td>
                                <td><strong>Fecha Requerida:</strong></td>
                                <td><?php echo date('d/m/Y', strtotime($salida_info['fec_req_salida'])); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Almacén Origen:</strong></td>
                                <td><?php echo $salida_info['nom_almacen_origen'] . ' (' . $salida_info['nom_ubicacion_origen'] . ')'; ?></td>
                                <td><strong>Almacén Destino:</strong></td>
                                <td><?php echo $salida_info['nom_almacen_destino'] . ' (' . $salida_info['nom_ubicacion_destino'] . ')'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Registrado por:</strong></td>
                                <td><?php echo $salida_info['nom_personal']; ?></td>
                                <td><strong>Personal Encargado:</strong></td>
                                <td><?php echo ($salida_info['nom_encargado'] ? $salida_info['nom_encargado'] : 'No especificado'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Personal que Recibe:</strong></td>
                                <td><?php echo ($salida_info['nom_recibe'] ? $salida_info['nom_recibe'] : 'No especificado'); ?></td>
                                <td><strong>Recepcionado por:</strong></td>
                                <td>
                                    <?php 
                                    if (!empty($salida_info['id_personal_aprueba_salida'])) {
                                        echo $salida_info['nom_aprueba'];
                                        if (!empty($salida_info['fec_aprueba_salida'])) {
                                            echo '<br><small class="text-muted">' . date('d/m/Y H:i', strtotime($salida_info['fec_aprueba_salida'])) . '</small>';
                                        }
                                    } else {
                                        echo '<span class="badge badge-warning">Pendiente</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php if (!empty($salida_info['obs_salida'])) { ?>
                            <tr>
                                <td><strong>Observaciones:</strong></td>
                                <td colspan="3"><?php echo $salida_info['obs_salida']; ?></td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <h5><strong>Detalles del Traslado</strong></h5>
                        <?php if (!empty($salida_detalle)) { ?>
                            <table class="table table-striped table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Producto/Material</th>
                                        <th>Cantidad</th>
                                        <th>Unidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $contador_detalle = 1;
                                    foreach ($salida_detalle as $detalle) { 
                                    ?>
                                        <tr>
                                            <td><?php echo $contador_detalle; ?></td>
                                            <td><?php echo $detalle['prod_salida_detalle']; ?></td>
                                            <td><?php echo $detalle['cant_salida_detalle']; ?></td>
                                            <td><?php echo $detalle['nom_unidad_medida']; ?></td>
                                        </tr>
                                    <?php 
                                        $contador_detalle++;
                                    } 
                                    ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> No hay detalles disponibles para esta salida.
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <h5><strong>Documentos Adjuntos</strong></h5>
                        <?php if (!empty($salida_docs)) { ?>
                            <table class="table table-striped table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Nombre del Documento</th>
                                        <th>Fecha de Subida</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; foreach ($salida_docs as $doc) { ?>
                                    <tr>
                                        <td><?= $i++; ?></td>
                                        <td><?= htmlspecialchars($doc['documento']); ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($doc['fec_subida'])); ?></td>
                                        <td>
                                            <a href="../uploads/salidas/<?= urlencode($doc['documento']); ?>" 
                                            target="_blank" 
                                            class="btn btn-sm btn-outline-info">
                                                <i class="fa fa-download"></i> Ver / Descargar
                                            </a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <div class="alert alert-secondary mb-0">
                                <i class="fa fa-info-circle"></i> No hay documentos adjuntos para esta salida.
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <?php if ($salida['est_salida'] == 1 && empty($salida_info['id_personal_aprueba_salida'])) { ?>
                    <a href="salidas_editar.php?id=<?php echo $salida['id_salida']; ?>" class="btn btn-warning">
                        <i class="fa fa-edit"></i> Editar Salida
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