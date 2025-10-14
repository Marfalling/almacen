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
                        <!-- Filtro de fechas -->
                        <form method="get" action="compras_mostrar.php" class="form-inline mb-3">
                            <div class="form-group mr-3">
                                <label for="fecha_inicio" class="mr-2 font-weight-bold">Desde:</label>
                                <input type="date" id="fecha_inicio" name="fecha_inicio"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($fecha_inicio); ?>">
                            </div>

                            <div class="form-group mr-3">
                                <label for="fecha_fin" class="mr-2 font-weight-bold">Hasta:</label>
                                <input type="date" id="fecha_fin" name="fecha_fin"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($fecha_fin); ?>">
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-search"></i> Consultar
                            </button>
                       </form>

                        <!-- Auto-abrir modal desde URL -->
                        <?php if (isset($_GET['abrir_modal']) && !empty($_GET['abrir_modal'])): ?>
                        <script>
                        window.addEventListener('load', function() {
                            // Esperar a que jQuery esté disponible
                            (function esperarTodo() {
                                if (typeof jQuery === 'undefined' || typeof abrirModalEditarOrden === 'undefined') {
                                    setTimeout(esperarTodo, 200);
                                    return;
                                }
                                
                                const idCompraAbrir = <?php echo intval($_GET['abrir_modal']); ?>;
                                console.log('=== TODO LISTO - Abriendo modal para orden:', idCompraAbrir, '===');
                                
                                setTimeout(function() {
                                    abrirModalEditarOrden(idCompraAbrir);
                                    
                                    // Limpiar URL sin usar jQuery
                                    setTimeout(function() {
                                        const url = new URL(window.location);
                                        url.searchParams.delete('abrir_modal');
                                        window.history.replaceState({}, document.title, url.pathname + (url.search || ''));
                                    }, 1000);
                                }, 1000);
                            })();
                        });
                        </script>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Código Orden</th>
                                                <th>Código Pedido</th>
                                                <th>Proveedor</th>
                                                <th>Fecha Registro</th>
                                                <th>Tipo Pago</th>
                                                <th>Registrado Por</th>
                                                <th>Aprob. Técnica Por</th>
                                                <th>Aprob. Financiera Por</th>
                                                <th>Estado</th>
                                                <th>Docs</th>
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
                                                    <td><?php echo 'U00' . $compra['id_compra']; ?></td>
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
                                                            // Mostrar fecha de vencimiento si NO está anulada
                                                            if ($compra['est_compra'] != 0) {
                                                                $fecha_vencimiento = date('d/m/Y', strtotime($compra['fec_compra'] . ' + ' . $compra['plaz_compra'] . ' days'));
                                                                $dias_restantes = (strtotime($compra['fec_compra'] . ' + ' . $compra['plaz_compra'] . ' days') - strtotime(date('Y-m-d'))) / 86400;
                                                                
                                                                // Agregar color según urgencia
                                                                $clase_vencimiento = '';
                                                                if ($dias_restantes <= 0) {
                                                                    $clase_vencimiento = 'text-danger font-weight-bold';
                                                                } elseif ($dias_restantes <= 3) {
                                                                    $clase_vencimiento = 'text-warning font-weight-bold';
                                                                }
                                                                
                                                                echo '<br><small class="' . $clase_vencimiento . ' text-muted">Vence: ' . $fecha_vencimiento . '</small>';
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
                                                                <i class="fa fa-folder-open"></i>
                                                            </button>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            <?php 
                                                            // Verificar si tiene aprobaciones
                                                            $tiene_aprobacion = !empty($compra['id_personal_aprueba_tecnica']) || !empty($compra['id_personal_aprueba_financiera']);
                                                            
                                                            // LÓGICA CORREGIDA: Solo cuando está PENDIENTE (1) se permiten acciones
                                                            if ($compra['est_compra'] == 1) { 
                                                            ?>
                                                                <!-- Compra PENDIENTE: botones habilitados según aprobaciones -->
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
                                                                    <i class="fa fa-check"></i>
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
                                                                    <i class="fa fa-check"></i>
                                                                </a>

                                                                <!-- Botón anular - DESHABILITAR SI TIENE APROBACIÓN -->
                                                                <?php if ($tiene_aprobacion) { ?>
                                                                    <a href="#"
                                                                    class="btn btn-outline-secondary btn-sm disabled"
                                                                    title="No se puede anular: tiene aprobación iniciada"
                                                                    tabindex="-1" aria-disabled="true">
                                                                        <i class="fa fa-times"></i>
                                                                    </a>
                                                                <?php } else { ?>
                                                                    <a href="#"
                                                                    onclick="AnularCompra(<?php echo $compra['id_compra']; ?>, <?php echo $compra['id_pedido']; ?>)"
                                                                    class="btn btn-danger btn-sm"
                                                                    title="Anular">
                                                                        <i class="fa fa-times"></i>
                                                                    </a>
                                                                <?php } ?>

                                                                <!-- PDF -->
                                                                <a href="compras_pdf.php?id=<?php echo $compra['id_compra']; ?>"
                                                                class="btn btn-secondary btn-sm"
                                                                title="Generar PDF"
                                                                target="_blank">
                                                                    <i class="fa fa-file-pdf-o"></i>
                                                                </a>

                                                                <!-- Botón Editar - SOLO SI NO TIENE APROBACIONES -->
                                                                <?php if (!$tiene_aprobacion) { ?>
                                                                    <a href="#" 
                                                                    class="btn btn-warning btn-sm"
                                                                    onclick="abrirModalEditarOrden(<?php echo $compra['id_compra']; ?>)"
                                                                    title="Editar Orden">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                <?php } else { ?>
                                                                    <a href="#" 
                                                                    class="btn btn-outline-secondary btn-sm disabled"
                                                                    title="No se puede editar - Tiene aprobación iniciada"
                                                                    tabindex="-1" aria-disabled="true">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                <?php } ?>

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
                                                            <?php } else { ?>
                                                                <!-- Compra NO pendiente (anulada, aprobada o pagada): todos deshabilitados -->
                                                                <a href="#" class="btn btn-outline-secondary btn-sm disabled" title="Aprobar Técnica" tabindex="-1" aria-disabled="true">
                                                                    <i class="fa fa-check"></i>
                                                                </a>
                                                                <a href="#" class="btn btn-outline-secondary btn-sm disabled" title="Aprobar Financiera" tabindex="-1" aria-disabled="true">
                                                                    <i class="fa fa-check"></i>
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

                                                                <!-- Botón Editar - DESHABILITADO -->
                                                                <a href="#" 
                                                                class="btn btn-outline-secondary btn-sm disabled"
                                                                title="<?php 
                                                                    if ($compra['est_compra'] == 0) echo 'No se puede editar - Orden anulada';
                                                                    elseif ($compra['est_compra'] == 4) echo 'No se puede editar - Orden pagada';
                                                                    else echo 'No se puede editar - Orden aprobada';
                                                                ?>"
                                                                tabindex="-1" aria-disabled="true">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>

                                                                <!-- Botón Pagos -->
                                                                <?php if ($compra['est_compra'] == 2 || $compra['est_compra'] == 3) { ?>
                                                                    <a href="pago_registrar.php?id_compra=<?php echo $compra['id_compra']; ?>"
                                                                    class="btn btn-warning btn-sm"
                                                                    title="Registrar/Ver Pagos">
                                                                        <i class="fa fa-money"></i>
                                                                    </a>
                                                                <?php } elseif ($compra['est_compra'] == 4) { ?>
                                                                    <!--debe poder verse la vista pero no registrar nuevos pagos-->
                                                                    <a href="pago_registrar.php?id_compra=<?php echo $compra['id_compra']; ?>"
                                                                    class="btn btn-warning btn-sm"
                                                                    title="Compra pagada"
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
<!-- Modal Editar Orden de Compra -->
<div class="modal fade" id="modalEditarOrden" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #fff3cd; padding: 10px 15px;">
                <h5 class="modal-title">
                    <i class="fa fa-edit text-warning"></i> 
                    Editar Orden de Compra <span id="orden-numero"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 600px; overflow-y: auto; padding: 20px;">
                <div id="loading-editar" class="text-center" style="padding: 40px;">
                    <i class="fa fa-spinner fa-spin fa-3x text-warning"></i>
                    <p class="mt-2">Cargando datos de la orden...</p>
                </div>
                
                <div id="contenido-editar-orden" style="display: none;">
                    <form id="form-editar-orden-modal">
                        <input type="hidden" name="id_compra" id="edit_id_compra">
                        <input type="hidden" name="actualizar_orden_modal" value="1">
                        
                        <!-- Información General -->
                        <div class="card mb-3">
                            <div class="card-header" style="background-color: #e3f2fd; padding: 8px 12px;">
                                <h6 class="mb-0">
                                    <i class="fa fa-info-circle text-primary"></i> 
                                    Información General
                                </h6>
                            </div>
                            <div class="card-body" style="padding: 12px;">
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <label style="font-size: 11px; font-weight: bold;">Fecha: <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control form-control-sm" name="fecha_orden" id="edit_fecha_orden" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label style="font-size: 11px; font-weight: bold;">Proveedor: <span class="text-danger">*</span></label>
                                        <select class="form-control form-control-sm" name="proveedor_orden" id="edit_proveedor_orden" required>
                                            <option value="">Seleccionar...</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <label style="font-size: 11px; font-weight: bold;">Moneda: <span class="text-danger">*</span></label>
                                        <select class="form-control form-control-sm" name="moneda_orden" id="edit_moneda_orden" required>
                                            <option value="">Seleccionar...</option>
                                            <option value="1">Soles (S/.)</option>
                                            <option value="2">Dólares (US$)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label style="font-size: 11px; font-weight: bold;">Plazo de Entrega (días):</label>
                                        <input type="number" class="form-control form-control-sm" name="plazo_entrega" id="edit_plazo_entrega" min="0" placeholder="0 = Contado">
                                        <small class="text-muted">Dejar vacío o 0 para contado</small>
                                    </div>
                                </div>
                                
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <label style="font-size: 11px; font-weight: bold;">Dirección de Envío:</label>
                                        <textarea class="form-control form-control-sm" name="direccion_envio" id="edit_direccion_envio" rows="2" style="resize: none;"></textarea>
                                    </div>
                                </div>
                                
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <label style="font-size: 11px; font-weight: bold;">Observaciones:</label>
                                        <textarea class="form-control form-control-sm" name="observaciones_orden" id="edit_observaciones_orden" rows="2" style="resize: none;"></textarea>
                                    </div>
                                </div>
                                
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <label style="font-size: 11px; font-weight: bold;">Tipo de Porte:</label>
                                        <input type="text" class="form-control form-control-sm" name="tipo_porte" id="edit_tipo_porte">
                                    </div>
                                </div>
                                
                                <!-- Detracción -->
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <label style="font-size: 11px; font-weight: bold;">Detracción (Opcional):</label>
                                        <div id="edit_contenedor_detracciones" style="padding: 8px; background-color: #f8f9fa; border-radius: 4px;">
                                            <!-- Se cargará dinámicamente -->
                                        </div>
                                        <small class="form-text text-muted">Seleccione una detracción si aplica. El monto se calculará automáticamente.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Items de la orden -->
                        <div class="card">
                            <div class="card-header" style="background-color: #e8f5e8; padding: 8px 12px;">
                                <h6 class="mb-0">
                                    <i class="fa fa-list-alt text-success"></i> 
                                    Productos de la Orden
                                </h6>
                            </div>
                            <div class="card-body" style="padding: 12px;" id="edit_items_container">
                                <!-- Se cargará dinámicamente -->
                            </div>
                        </div>
                        
                        <!-- Total -->
                        <div id="edit_total_orden" class="mt-3">
                            <!-- Se calculará dinámicamente -->
                        </div>
                    </form>
                </div>
                
                <div id="error-editar-orden" style="display: none;" class="text-center">
                    <i class="fa fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h5 class="text-warning">Error al cargar datos</h5>
                    <p class="text-muted">No se pudieron cargar los datos de la orden.</p>
                </div>
            </div>
            <div class="modal-footer" style="padding: 15px; background-color: #f8f9fa;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-warning" id="btn-guardar-edicion-orden">
                    <i class="fa fa-save"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ============================================================================
// FUNCIONES PARA EDITAR ORDEN EN MODAL
// ============================================================================

function abrirModalEditarOrden(id_compra) {
    $('#modalEditarOrden').modal('show');
    
    // Mostrar loading
    document.getElementById('loading-editar').style.display = 'block';
    document.getElementById('contenido-editar-orden').style.display = 'none';
    document.getElementById('error-editar-orden').style.display = 'none';
    
    // Cargar datos de la orden
    fetch('compras_obtener_datos_edicion.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id_compra=' + id_compra
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('loading-editar').style.display = 'none';
        
        if (data.success) {
            cargarDatosOrdenModal(data.orden, data.detalles, data.proveedores, data.detracciones);
            document.getElementById('contenido-editar-orden').style.display = 'block';
        } else {
            document.getElementById('error-editar-orden').style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('loading-editar').style.display = 'none';
        document.getElementById('error-editar-orden').style.display = 'block';
    });
}

function cargarDatosOrdenModal(orden, detalles, proveedores, detracciones) {
    // Cargar datos básicos
    document.getElementById('orden-numero').textContent = 'ORD-' + orden.id_compra;
    document.getElementById('edit_id_compra').value = orden.id_compra;
    document.getElementById('edit_fecha_orden').value = orden.fec_compra.split(' ')[0];
    document.getElementById('edit_moneda_orden').value = orden.id_moneda;
    document.getElementById('edit_plazo_entrega').value = orden.plaz_compra || '';
    document.getElementById('edit_direccion_envio').value = orden.denv_compra || '';
    document.getElementById('edit_observaciones_orden').value = orden.obs_compra || '';
    document.getElementById('edit_tipo_porte').value = orden.port_compra || '';

    let inputEliminados = document.getElementById('edit_items_eliminados');
    if (!inputEliminados) {
        inputEliminados = document.createElement('input');
        inputEliminados.type = 'hidden';
        inputEliminados.name = 'items_eliminados';
        inputEliminados.id = 'edit_items_eliminados';
        inputEliminados.value = '';
        document.getElementById('form-editar-orden-modal').appendChild(inputEliminados);
    } else {
        inputEliminados.value = '';
    }
    
    // Cargar proveedores
    const selectProveedor = document.getElementById('edit_proveedor_orden');
    selectProveedor.innerHTML = '<option value="">Seleccionar...</option>';
    proveedores.forEach(prov => {
        const option = document.createElement('option');
        option.value = prov.id_proveedor;
        option.textContent = prov.nom_proveedor;
        option.selected = (prov.id_proveedor == orden.id_proveedor);
        selectProveedor.appendChild(option);
    });
    
    // Cargar detracciones
    const contenedorDetracciones = document.getElementById('edit_contenedor_detracciones');
    contenedorDetracciones.innerHTML = '';
    
    if (detracciones && detracciones.length > 0) {
        detracciones.forEach(det => {
            const checked = (orden.id_detraccion == det.id_detraccion) ? 'checked' : '';
            contenedorDetracciones.innerHTML += `
                <div class="form-check" style="margin-bottom: 5px;">
                    <input class="form-check-input edit-detraccion-checkbox" 
                           type="checkbox" 
                           name="id_detraccion" 
                           value="${det.id_detraccion}" 
                           data-porcentaje="${det.porcentaje}" 
                           data-nombre="${det.nombre_detraccion}"
                           id="edit_detraccion_${det.id_detraccion}" 
                           ${checked}>
                    <label class="form-check-label" 
                           for="edit_detraccion_${det.id_detraccion}" 
                           style="font-size: 12px; cursor: pointer;">
                        ${det.nombre_detraccion} <strong>(${det.porcentaje}%)</strong>
                    </label>
                </div>
            `;
        });
    } else {
        contenedorDetracciones.innerHTML = '<p class="text-muted mb-0" style="font-size: 11px;"><i class="fa fa-info-circle"></i> No hay detracciones configuradas</p>';
    }
    
    // Configurar checkboxes de detracción (solo uno a la vez)
    document.querySelectorAll('.edit-detraccion-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            if (this.checked) {
                document.querySelectorAll('.edit-detraccion-checkbox').forEach(otherCb => {
                    if (otherCb !== this) otherCb.checked = false;
                });
            }
            calcularTotalOrdenModal();
        });
    });
    
    // Cargar items
    const itemsContainer = document.getElementById('edit_items_container');
    itemsContainer.innerHTML = '';
    
    const simboloMoneda = orden.id_moneda == 1 ? 'S/.' : 'US$';
    
    detalles.forEach((item, index) => {
        const subtotal = parseFloat(item.cant_compra_detalle) * parseFloat(item.prec_compra_detalle);
        
        itemsContainer.innerHTML += `
            <div class="alert alert-light p-2 mb-2" id="edit_item_${item.id_compra_detalle}" style="border-left: 3px solid #28a745;">
                <input type="hidden" name="items_orden[${item.id_compra_detalle}][id_compra_detalle]" value="${item.id_compra_detalle}">
                <input type="hidden" name="items_orden[${item.id_compra_detalle}][cantidad]" value="${item.cant_compra_detalle}">
                
                <div class="row align-items-center">
                    <div class="col-md-1 text-center">
                        <strong style="font-size: 14px; color: #28a745;">${index + 1}</strong>
                    </div>
                    <div class="col-md-7">
                        <div style="font-size: 12px;">
                            <div class="mb-1">
                                <strong>Descripción:</strong> ${item.nom_producto}
                            </div>
                            <div>
                                <strong>Cantidad:</strong> ${item.cant_compra_detalle}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label style="font-size: 11px; font-weight: bold;">Precio Unit.:</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text edit-simbolo-moneda" style="font-size: 11px;">${simboloMoneda}</span>
                            </div>
                            <input type="number" 
                                   class="form-control form-control-sm edit-precio-item" 
                                   name="items_orden[${item.id_compra_detalle}][precio_unitario]"
                                   data-id-detalle="${item.id_compra_detalle}"
                                   data-cantidad="${item.cant_compra_detalle}"
                                   value="${item.prec_compra_detalle}"
                                   step="0.01" 
                                   min="0"
                                   style="font-size: 11px;"
                                   required>
                        </div>
                    </div>
                    <div class="col-md-1 text-center">
                        <button type="button" class="btn btn-danger btn-sm btn-remover-item" 
                                data-id-detalle="${item.id_compra_detalle}"
                                title="Eliminar item">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <div class="edit-subtotal-item text-right" 
                             id="edit_subtotal_${item.id_compra_detalle}" 
                             style="font-size: 12px; font-weight: bold; color: #28a745;">
                            Subtotal: ${simboloMoneda} ${subtotal.toFixed(2)}
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    // Configurar eventos de precios
    document.querySelectorAll('.edit-precio-item').forEach(input => {
        input.addEventListener('input', function() {
            const idDetalle = this.getAttribute('data-id-detalle');
            const cantidad = parseFloat(this.getAttribute('data-cantidad'));
            const precio = parseFloat(this.value) || 0;
            const subtotal = cantidad * precio;
            
            const simbolo = document.getElementById('edit_moneda_orden').value == 1 ? 'S/.' : 'US$';
            document.getElementById('edit_subtotal_' + idDetalle).textContent = 
                `Subtotal: ${simbolo} ${subtotal.toFixed(2)}`;
            
            calcularTotalOrdenModal();
        });
    });
    
    // Configurar eventos de remover items
    document.querySelectorAll('.btn-remover-item').forEach(btn => {
        btn.addEventListener('click', function() {
            const idDetalle = this.getAttribute('data-id-detalle');
            Swal.fire({
                title: '¿Eliminar item?',
                text: 'Este item se eliminará de la orden',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // AGREGAR A LA LISTA DE ELIMINADOS
                    const inputEliminados = document.getElementById('edit_items_eliminados');
                    let eliminados = inputEliminados.value ? inputEliminados.value.split(',') : [];
                    
                    // Solo agregar si no es un item nuevo (los nuevos tienen prefijo 'nuevo-')
                    if (!idDetalle.toString().startsWith('nuevo-')) {
                        eliminados.push(idDetalle);
                        inputEliminados.value = eliminados.join(',');
                    }
                    
                    // Remover del DOM
                    document.getElementById('edit_item_' + idDetalle).remove();
                    calcularTotalOrdenModal();
                    
                    console.log('Items eliminados:', inputEliminados.value);
                }
            });
        });
    });
    
    // Evento para cambio de moneda
    document.getElementById('edit_moneda_orden').addEventListener('change', function() {
        const simbolo = this.value == 1 ? 'S/.' : 'US$';
        document.querySelectorAll('.edit-simbolo-moneda').forEach(el => {
            el.textContent = simbolo;
        });
        calcularTotalOrdenModal();
    });
    
    // Calcular total inicial
    calcularTotalOrdenModal();
}

function calcularTotalOrdenModal() {
    const items = document.querySelectorAll('.edit-precio-item');
    let total = 0;
    
    items.forEach(input => {
        const cantidad = parseFloat(input.getAttribute('data-cantidad')) || 0;
        const precio = parseFloat(input.value) || 0;
        total += cantidad * precio;
    });
    
    const checkboxDetraccion = document.querySelector('.edit-detraccion-checkbox:checked');
    let montoDetraccion = 0;
    let porcentajeDetraccion = 0;
    let nombreDetraccion = '';
    
    if (checkboxDetraccion) {
        porcentajeDetraccion = parseFloat(checkboxDetraccion.getAttribute('data-porcentaje')) || 0;
        nombreDetraccion = checkboxDetraccion.getAttribute('data-nombre') || '';
        montoDetraccion = (total * porcentajeDetraccion) / 100;
    }
    
    const totalFinal = total - montoDetraccion;
    const simboloMoneda = document.getElementById('edit_moneda_orden').value == 1 ? 'S/.' : 'US$';
    
    let html = `
        <div class="text-end" style="font-size: 14px; padding: 15px; background-color: #fff; border: 1px solid #ddd; border-radius: 8px;">
            <div class="mb-2" style="font-size: 13px;">
                <i class="fa fa-calculator text-secondary"></i>
                <strong class="text-secondary"> Subtotal:</strong>
                <span class="text-dark">${simboloMoneda} ${total.toFixed(2)}</span>
            </div>`;
    
    if (montoDetraccion > 0) {
        html += `
            <div class="mb-2" style="font-size: 13px; color: #dc3545;">
                <i class="fa fa-minus-circle"></i>
                <strong> Detracción ${nombreDetraccion} (${porcentajeDetraccion}%):</strong>
                <span>-${simboloMoneda} ${montoDetraccion.toFixed(2)}</span>
            </div>
            <div class="alert alert-info text-center mb-0" style="font-size: 16px; font-weight: bold; padding: 12px;">
                <i class="fa fa-money"></i> TOTAL A PAGAR: ${simboloMoneda} ${totalFinal.toFixed(2)}
            </div>`;
    } else {
        html += `
            <div class="alert alert-info text-center mb-0" style="font-size: 16px; font-weight: bold; padding: 12px;">
                <i class="fa fa-money"></i> TOTAL: ${simboloMoneda} ${total.toFixed(2)}
            </div>`;
    }
    
    html += `</div>`;
    
    document.getElementById('edit_total_orden').innerHTML = html;
}

// Guardar cambios
document.getElementById('btn-guardar-edicion-orden').addEventListener('click', function() {
    const form = document.getElementById('form-editar-orden-modal');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const btnGuardar = this;
    btnGuardar.disabled = true;
    btnGuardar.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Guardando...';
    
    const formData = new FormData(form);
    
    fetch('compras_actualizar_orden.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Actualizado!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                $('#modalEditarOrden').modal('hide');
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = '<i class="fa fa-save"></i> Guardar Cambios';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor'
        });
        btnGuardar.disabled = false;
        btnGuardar.innerHTML = '<i class="fa fa-save"></i> Guardar Cambios';
    });
});

// Limpiar modal al cerrar
document.getElementById('modalEditarOrden').addEventListener('hidden.bs.modal', function () {
    document.getElementById('form-editar-orden-modal').reset();
    document.getElementById('edit_items_container').innerHTML = '';
    document.getElementById('edit_total_orden').innerHTML = '';
});

</script>

