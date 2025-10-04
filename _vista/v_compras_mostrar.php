<?php 
//=======================================================================
// VISTA: v_compras_mostrar.php (Versión fusionada)
//=======================================================================
?>

<script>
// Aprobar por el área técnica
function AprobarCompraTecnica(id_compra) {
    Swal.fire({
        title: '¿Deseas aprobar técnicamente esta compra?',
        text: "Esta acción no se puede deshacer.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, aprobar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'compras_aprobar_tecnica.php',
                type: 'POST',
                data: { id_compra: id_compra },
                dataType: 'json',
                success: function(response) {
                    if (response.tipo_mensaje === 'success') {
                        Swal.fire('¡Aprobado!', response.mensaje, 'success')
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

// Aprobar por el área financiera
function AprobarCompraFinanciera(id_compra) {
    Swal.fire({
        title: '¿Deseas aprobar financieramente esta compra?',
        text: "Esta acción no se puede deshacer.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, aprobar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'compras_aprobar_financiera.php',
                type: 'POST',
                data: { id_compra: id_compra },
                dataType: 'json',
                success: function(response) {
                    if (response.tipo_mensaje === 'success') {
                        Swal.fire('¡Aprobado!', response.mensaje, 'success')
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

function AnularCompra(id_compra, id_pedido) {
    Swal.fire({
        title: '¿Qué deseas anular?',
        text: "Selecciona una opción:",
        icon: 'warning',
        showCancelButton: true,
        showDenyButton: true,
        confirmButtonColor: '#d33',
        denyButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Solo O/C',
        denyButtonText: 'O/C y Pedido',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Anular solo la orden de compra
            $.ajax({
                url: 'compras_anular.php',
                type: 'POST',
                data: { id_compra: id_compra },
                dataType: 'json',
                success: function(response) {
                    if (response.tipo_mensaje === 'success') {
                        Swal.fire('¡Anulado!', response.mensaje, 'success')
                        .then(() => { location.reload(); });
                    } else {
                        Swal.fire('Error', response.mensaje, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                }
            });
        } else if (result.isDenied) {
            // Anular la orden y también el pedido
            $.ajax({
                url: 'compras_pedido_anular.php',
                type: 'POST',
                data: { id_compra: id_compra, id_pedido: id_pedido },
                dataType: 'json',
                success: function(response) {
                    if (response.tipo_mensaje === 'success') {
                        Swal.fire('¡Anulado!', response.mensaje, 'success')
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

function SubirDocumento(id_compra) {
    Swal.fire({
        title: 'Subir documento',
        html: '<input type="file" id="documento" class="swal2-file">',
        showCancelButton: true,
        confirmButtonText: 'Subir'
    }).then((result) => {
        if (result.isConfirmed) {
            const archivo = document.getElementById('documento').files[0];
            if (!archivo) {
                Swal.fire('Error', 'Debes seleccionar un archivo', 'error');
                return;
            }

            let formData = new FormData();
            formData.append("entidad", "compras");
            formData.append("id_entidad", id_compra);
            formData.append("documento", archivo);

            $.ajax({
                url: 'compras_subir_documentos.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.tipo_mensaje === 'success') {
                        Swal.fire('¡Éxito!', response.mensaje, 'success')
                        .then(() => location.reload());
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

function EliminarDocumento(id_doc) {
    Swal.fire({
        title: '¿Eliminar documento?',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'compras_eliminar_documento.php',
                type: 'POST',
                data: { id_doc: id_doc },
                dataType: 'json',
                success: function(response) {
                    if (response.tipo_mensaje === 'success') {
                        Swal.fire('Eliminado', response.mensaje, 'success')
                        .then(() => location.reload());
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
</script>

<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Compras</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="x_panel">
                    <div class="x_title">
                        <div class="row">
                            <div class="col-sm-10">
                                <h2>Listado de Compras</h2>
                            </div>
                            <div class="col-sm-2">
                               <!-- <a href="compras_nuevo.php" class="btn btn-outline-info btn-sm btn-block">Nueva Compra</a> -->  
                            </div>
                        </div>
                    </div>

                    <div class="x_content">
                        <!-- Filtro por fechas -->
                        <form method="get" action="compras_mostrar.php" class="form-inline mb-3">
                            <label for="fecha_inicio" class="mr-2">Desde:</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control mr-2"
                                   value="<?php echo htmlspecialchars($fecha_inicio ?? date('Y-m-d')); ?>">

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
                                                <th>N° Orden</th>
                                                <th>Código Pedido</th>
                                                <th>Proveedor</th>
                                                <th>Fecha Registro</th>
                                                <th>Tipo Pago</th>
                                                <th>Registrado Por</th>
                                                <th>Aprob. Técnica Por</th>
                                                <th>Aprob. Financiera Por</th>
                                                <th>Estado</th>
                                                <th>Documento</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php 
                                            $contador = 1;
                                            foreach($compras as $compra) { 
                                                $tiene_tecnica = !empty($compra['id_personal_aprueba_tecnica']);
                                                $tiene_financiera = !empty($compra['id_personal_aprueba_financiera']);
                                                
                                                // Determinar si es al contado o crédito
                                                $es_contado = empty($compra['plaz_compra']) || $compra['plaz_compra'] == 0;
                                                
                                                // Calcular clase para sombrado SOLO si es crédito y está activa
                                                $clase_fila = '';
                                                if (!$es_contado && $compra['est_compra'] == 1) {
                                                    $fecha_vencimiento = date('Y-m-d', strtotime($compra['fec_compra'] . ' + ' . $compra['plaz_compra'] . ' days'));
                                                    $dias_restantes = (strtotime($fecha_vencimiento) - strtotime(date('Y-m-d'))) / 86400;
                                                    
                                                    if ($dias_restantes <= 0) {
                                                        $clase_fila = 'table-danger'; // Vencido o vence hoy
                                                    } elseif ($dias_restantes <= 3) {
                                                        $clase_fila = 'table-warning'; // Por vencer (3 días o menos)
                                                    }
                                                }
                                            ?>
                                                <tr class="<?php echo $clase_fila; ?>">
                                                    <td><?php echo $contador; ?></td>
                                                    <td><?php echo $compra['id_compra']; ?></td>
                                                    <td>
                                                        <a class="btn btn-sm btn-outline-secondary" target="_blank" 
                                                        href="pedido_pdf.php?id=<?php echo $compra['id_pedido']; ?>">
                                                            <?php echo $compra['cod_pedido']; ?>
                                                        </a>
                                                    </td>
                                                    <td><?php echo $compra['nom_proveedor']; ?></td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($compra['fec_compra'])); ?></td>
                                                    
                                                    <!-- COLUMNA TIPO DE PAGO -->
                                                    <td class="text-center">
                                                        <?php if ($es_contado) { ?>
                                                            <span class="badge badge-success">Contado</span>
                                                        <?php } else { ?>
                                                            <span class="badge badge-info">Crédito (<?php echo $compra['plaz_compra']; ?> días)</span>
                                                            <?php
                                                            // Mostrar fecha de vencimiento si está activa
                                                            if ($compra['est_compra'] == 1) {
                                                                $fecha_vencimiento = date('d/m/Y', strtotime($compra['fec_compra'] . ' + ' . $compra['plaz_compra'] . ' days'));
                                                                echo '<br><small class="text-muted">Vence: ' . $fecha_vencimiento . '</small>';
                                                            }
                                                            ?>
                                                        <?php } ?>
                                                    </td>
                                                    
                                                    <td><?php echo $compra['nom_registrado']; ?></td>
                                                    <td>
                                                        <?php 
                                                        if ($tiene_tecnica) {
                                                            echo $compra['nom_aprobado_tecnica'];
                                                        } else {
                                                            echo '-';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                        if ($tiene_financiera) {
                                                            echo $compra['nom_aprobado_financiera'];
                                                        } else {
                                                            echo '-';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($compra['est_compra'] == 1) { ?>
                                                            <?php if ($tiene_financiera && !$tiene_tecnica) { ?>
                                                                <span class="badge badge-info badge_size">Aprobado Financiera</span>
                                                            <?php } elseif ($tiene_tecnica && !$tiene_financiera) { ?>
                                                                <span class="badge badge-info badge_size">Aprobado Técnico</span>
                                                            <?php } elseif (!$tiene_tecnica && !$tiene_financiera) { ?>
                                                                <span class="badge badge-warning badge_size">Pendiente</span>
                                                            <?php } ?>
                                                        <?php } elseif ($compra['est_compra'] == 2) { ?>
                                                            <span class="badge badge-success badge_size">Aprobado</span>
                                                        <?php } elseif ($compra['est_compra'] == 3) { ?>
                                                            <span class="badge badge-success badge_size">Aprobado</span>
                                                        <?php } elseif ($compra['est_compra'] == 4) { ?>
                                                            <span class="badge badge-primary badge_size">Pagado</span>
                                                        <?php } else { ?>
                                                            <span class="badge badge-danger badge_size">Anulado</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($compra['est_compra'] == 0) { ?>
                                                            <span class="text-muted">Bloqueado</span>
                                                        <?php } else { ?>
                                                            <button type="button" 
                                                                    class="btn btn-info btn-sm" 
                                                                    data-toggle="modal" 
                                                                    data-target="#modalDocumentos<?php echo $compra['id_compra']; ?>">
                                                                <i class="fa fa-folder-open"></i> Ver Documentos
                                                            </button>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            <?php 
                                                            // Estados bloqueados: 0 = anulado, 2 o 3 = aprobada, 4 = pagada
                                                            if ($compra['est_compra'] == 0 || $compra['est_compra'] == 2 || $compra['est_compra'] == 3 || $compra['est_compra'] == 4) { 
                                                            ?>
                                                                <!-- Compra anulada, aprobada o pagada: botones deshabilitados -->
                                                                <a href="#" class="btn btn-outline-secondary btn-sm disabled" title="Aprobar Técnica" tabindex="-1" aria-disabled="true">
                                                                    <i class="fa fa-check"></i> Téc
                                                                </a>
                                                                <a href="#" class="btn btn-outline-secondary btn-sm disabled" title="Aprobar Financiera" tabindex="-1" aria-disabled="true">
                                                                    <i class="fa fa-check"></i> Fin
                                                                </a>

                                                                <!-- Botón anular -->
                                                                <a href="#" class="btn btn-outline-secondary btn-sm disabled"
                                                                title="<?php 
                                                                        if ($compra['est_compra'] == 0) echo 'Ya anulada'; 
                                                                        elseif ($compra['est_compra'] == 4) echo 'No se puede anular: compra pagada'; 
                                                                        else echo 'No se puede anular: compra aprobada'; 
                                                                ?>"
                                                                tabindex="-1" aria-disabled="true">
                                                                    <i class="fa fa-times"></i>
                                                                </a>

                                                                <!-- PDF -->
                                                                <a href="compras_pdf.php?id=<?php echo $compra['id_compra']; ?>"
                                                                class="btn btn-secondary btn-sm"
                                                                title="Generar PDF"
                                                                target="_blank">
                                                                    <i class="fa fa-file-pdf-o"></i>
                                                                </a>

                                                                <!-- Botón Pagos -->
                                                                <?php if ($compra['est_compra'] == 2 || $compra['est_compra'] == 3) { ?>
                                                                    <!-- Solo si está aprobada -->
                                                                    <a href="pago_registrar.php?id_compra=<?php echo $compra['id_compra']; ?>"
                                                                    class="btn btn-warning btn-sm"
                                                                    title="Registrar/Ver Pagos">
                                                                        <i class="fa fa-money"></i>
                                                                    </a>
                                                                <?php } elseif ($compra['est_compra'] == 4) { ?>
                                                                    <!-- Si ya está pagada -->
                                                                    <a href="#"
                                                                    class="btn btn-outline-success btn-sm disabled"
                                                                    title="Compra completamente pagada"
                                                                    tabindex="-1" aria-disabled="true">
                                                                        <i class="fa fa-money"></i>
                                                                    </a>
                                                                <?php } else { ?>
                                                                    <a href="#"
                                                                    class="btn btn-outline-secondary btn-sm disabled"
                                                                    title="No disponible"
                                                                    tabindex="-1" aria-disabled="true">
                                                                        <i class="fa fa-money"></i>
                                                                    </a>
                                                                <?php } ?>

                                                            <?php } else { ?>
                                                                <!-- Compra pendiente (est_compra = 1): botones habilitados -->
                                                                <a href="#"
                                                                <?php if ($tiene_tecnica) { ?>
                                                                    class="btn btn-outline-secondary btn-sm disabled"
                                                                    title="Ya aprobado técnica"
                                                                    tabindex="-1" aria-disabled="true"
                                                                <?php } else { ?>
                                                                    onclick="AprobarCompraTecnica(<?php echo $compra['id_compra']; ?>)"
                                                                    class="btn btn-success btn-sm"
                                                                    title="Aprobar Técnica"
                                                                <?php } ?>>
                                                                    <i class="fa fa-check"></i> Téc
                                                                </a>

                                                                <a href="#"
                                                                <?php if ($tiene_financiera) { ?>
                                                                    class="btn btn-outline-secondary btn-sm disabled"
                                                                    title="Ya aprobado financiera"
                                                                    tabindex="-1" aria-disabled="true"
                                                                <?php } else { ?>
                                                                    onclick="AprobarCompraFinanciera(<?php echo $compra['id_compra']; ?>)"
                                                                    class="btn btn-primary btn-sm"
                                                                    title="Aprobar Financiera"
                                                                <?php } ?>>
                                                                    <i class="fa fa-check"></i> Fin
                                                                </a>

                                                                <!-- Botón anular -->
                                                                <a href="#"
                                                                onclick="AnularCompra(<?php echo $compra['id_compra']; ?>, <?php echo $compra['id_pedido']; ?>)"
                                                                class="btn btn-danger btn-sm"
                                                                title="Anular">
                                                                    <i class="fa fa-times"></i>
                                                                </a>

                                                                <!-- PDF -->
                                                                <a href="compras_pdf.php?id=<?php echo $compra['id_compra']; ?>"
                                                                class="btn btn-secondary btn-sm"
                                                                title="Generar PDF"
                                                                target="_blank">
                                                                    <i class="fa fa-file-pdf-o"></i>
                                                                </a>

                                                                <!-- Botón Pagos -->
                                                                <?php if ($tiene_tecnica && $tiene_financiera) { ?>
                                                                    <a href="pago_registrar.php?id_compra=<?php echo $compra['id_compra']; ?>"
                                                                    class="btn btn-warning btn-sm"
                                                                    title="Registrar/Ver Pagos">
                                                                        <i class="fa fa-money"></i>
                                                                    </a>
                                                                <?php } else { ?>
                                                                    <a href="#"
                                                                    class="btn btn-outline-secondary btn-sm disabled"
                                                                    title="Requiere aprobación técnica y financiera"
                                                                    tabindex="-1" aria-disabled="true">
                                                                        <i class="fa fa-money"></i>
                                                                    </a>
                                                                <?php } ?>
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
        </div>
    </div>
</div>
<!-- /page content -->

<!-- Modales de documentos -->
<?php foreach($compras as $compra) { 
    $documentos = MostrarDocumentos('compras', $compra['id_compra']); ?>
<div class="modal fade" id="modalDocumentos<?php echo $compra['id_compra']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalDocumentosLabel<?php echo $compra['id_compra']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Documentos de la Compra #<?php echo $compra['id_compra']; ?></h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <?php if (!empty($documentos)) { ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Documento</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1; foreach($documentos as $doc) { ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><a href="../uploads/compras/<?php echo $doc['documento']; ?>" target="_blank"><i class="fa fa-file"></i> <?php echo $doc['documento']; ?></a></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($doc['fec_subida'])); ?></td>
                                <td>
                                    <?php if ($compra['est_compra'] != 0) { ?>
                                        <button class="btn btn-sm btn-outline-danger" onclick="EliminarDocumento(<?php echo $doc['id_doc']; ?>)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    <?php } else { ?>
                                        <span class="text-muted">Bloqueado</span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div class="alert alert-info"><i class="fa fa-info-circle"></i> No hay documentos registrados.</div>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <?php if ($compra['est_compra'] != 0) { ?>
                    <button class="btn btn-primary" onclick="SubirDocumento(<?php echo $compra['id_compra']; ?>)">
                        <i class="fa fa-upload"></i> Subir Documento
                    </button>
                <?php } ?>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<?php } ?>