<?php 
//=======================================================================
// VISTA: v_pedidos_mostrar.php
//=======================================================================
// Estados correctos: 0=Anulado, 1=Pendiente, 2=Completado, 3=Aprobado, 4=Ingresado, 5=Finalizado

// ========================================================================
// VERIFICAR PERMISOS AL INICIO
// ========================================================================
$tiene_permiso_aprobar_tecnica = verificarPermisoEspecifico('aprobar_pedidos');
$tiene_permiso_verificar = verificarPermisoEspecifico('verificar_pedidos');
$tiene_permiso_anular = verificarPermisoEspecifico('anular_pedidos');
?>
<script>
function AprobarPedidoTecnica(id_pedido) {
    Swal.fire({
        title: '¿Deseas aprobar técnicamente este pedido?',
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
                url: 'pedidos_aprobar_tecnica.php',
                type: 'POST',
                data: { id_pedido: id_pedido },
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
</script>
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
                                <a href="pedidos_nuevo.php" class="btn btn-outline-info btn-sm btn-block">
                                    <i class="fa fa-plus"></i> Nuevo Pedido
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="x_content">
                        <!-- Filtro de fechas -->
                        <form method="get" action="pedidos_mostrar.php" class="form-inline mb-3">
                            <label for="fecha_inicio" class="mr-2">Desde:</label>
                            <input 
                                type="date" 
                                id="fecha_inicio" 
                                name="fecha_inicio" 
                                class="form-control mr-2"
                                value="<?php echo htmlspecialchars($fecha_inicio); ?>">

                            <label for="fecha_fin" class="mr-2">Hasta:</label>
                            <input 
                                type="date" 
                                id="fecha_fin" 
                                name="fecha_fin" 
                                class="form-control mr-2"
                                value="<?php echo htmlspecialchars($fecha_fin); ?>">

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
                                                <th>Solicitante</th>
                                                <th>Fecha Pedido</th>
                                                <th>Fecha Necesidad</th>
                                                <th>Aprob. Técnica Por</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php 
                                            $contador = 1;
                                            foreach($pedidos as $pedido) { 
                                                $tiene_tecnica = !empty($pedido['id_personal_aprueba_tecnica']);
                                            ?>
                                                <tr>
                                                    <td><?php echo $contador; ?></td>
                                                    <td><?php echo $pedido['cod_pedido']; ?></td>
                                                    <td><?php echo $pedido['nom_producto_tipo']; ?></td>
                                                    <td><?php echo $pedido['nom_pedido']; ?></td>
                                                    <td><?php echo $pedido['nom_almacen']; ?></td>
                                                    <td><?php echo $pedido['nom_ubicacion']; ?></td>
                                                    <td><?php echo $pedido['nom_personal']; ?></td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($pedido['fec_pedido'])); ?></td>
                                                    <td><?php echo date('d/m/Y', strtotime($pedido['fec_req_pedido'])); ?></td>
                                                    <td>
                                                        <?php 
                                                        if ($tiene_tecnica) {
                                                            echo $pedido['nom_aprobado_tecnica'];
                                                        } else {
                                                            echo '-';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <center>
                                                            <?php  
                                                            $estado_final = '';
                                                            $badge_class = '';

                                                            if ($pedido['est_pedido'] == 0) {
                                                                // ANULADO
                                                                $estado_final = 'ANULADO';
                                                                $badge_class = 'badge-danger';

                                                            } elseif ($pedido['est_pedido'] == 2) {
                                                                // ATENDIDO (Completado)
                                                                $estado_final = 'ATENDIDO';
                                                                $badge_class = 'badge-info';

                                                            } elseif ($pedido['est_pedido'] == 3) {
                                                                // APROBADO
                                                                $estado_final = 'PENDIENTE';
                                                                $badge_class = 'badge-warning';

                                                            } elseif ($pedido['est_pedido'] == 4) {
                                                                // INGRESADO
                                                                $estado_final = 'PENDIENTE';
                                                                $badge_class = 'badge-warning';

                                                            } elseif ($pedido['est_pedido'] == 5) {
                                                                // FINALIZADO
                                                                $estado_final = 'PENDIENTE';
                                                                $badge_class = 'badge-warning';

                                                            } elseif ($pedido['est_pedido'] == 1) {
                                                                //  PENDIENTE - Verificar si tiene aprobación técnica
                                                                if ($tiene_tecnica) {
                                                                    $estado_final = 'APROBADO';
                                                                    $badge_class = 'badge-success';
                                                                } else {
                                                                    $estado_final = 'PENDIENTE';
                                                                    $badge_class = 'badge-warning';
                                                                }

                                                            } else {
                                                                // DESCONOCIDO
                                                                $estado_final = 'DESCONOCIDO';
                                                                $badge_class = 'badge-secondary';
                                                            }
                                                            ?>
                                                            <span class="badge <?php echo $badge_class; ?> badge_size">
                                                                <?php echo $estado_final; ?>
                                                            </span>
                                                        </center>
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
                                                        
                                                            <!-- ============================================ -->
                                                            <!-- BOTÓN APROBAR TÉCNICA -->
                                                            <!-- ============================================ -->
                                                            <?php
                                                            if (!$tiene_permiso_aprobar_tecnica) {
                                                                // SIN PERMISO - Botón rojo outline danger
                                                                ?>
                                                                <a href="#"
                                                                class="btn btn-outline-danger btn-sm disabled"
                                                                title="No tienes permiso para aprobar técnicamente pedidos"
                                                                tabindex="-1" aria-disabled="true">
                                                                    <i class="fa fa-check"></i>
                                                                </a>
                                                            <?php } elseif ($tiene_tecnica) { ?>
                                                                <!-- YA APROBADO - Gris por proceso -->
                                                                <a href="#"
                                                                class="btn btn-outline-secondary btn-sm disabled"
                                                                title="Ya aprobado técnicamente"
                                                                tabindex="-1" aria-disabled="true">
                                                                    <i class="fa fa-check"></i>
                                                                </a>
                                                            <?php } else { ?>
                                                                <!-- PUEDE APROBAR - Verde activo -->
                                                                <a href="#"
                                                                onclick="AprobarPedidoTecnica(<?php echo $pedido['id_pedido']; ?>)"
                                                                class="btn btn-success btn-sm"
                                                                title="Aprobar Técnicamente">
                                                                    <i class="fa fa-check"></i>
                                                                </a>
                                                            <?php } ?>

                                                            <?php
                                                            // ============================================
                                                            // BOTÓN EDITAR PEDIDO
                                                            // ============================================
                                                            $es_rechazado = in_array($pedido['id_pedido'], $pedidos_rechazados);
                                                            $puede_editar = false;
                                                            $titulo_editar = '';

                                                            if ($pedido['est_pedido'] == 0) {
                                                                $titulo_editar = "No se puede editar - Pedido anulado";
                                                            } elseif ($pedido['est_pedido'] >= 3) {
                                                                $estados = [
                                                                    3 => "aprobado",
                                                                    4 => "ingresado",
                                                                    5 => "finalizado"
                                                                ];
                                                                $estado_nombre = $estados[$pedido['est_pedido']] ?? "procesado";
                                                                $titulo_editar = "No se puede editar - Pedido {$estado_nombre}";
                                                            } elseif ($es_rechazado) {
                                                                $titulo_editar = "No se puede editar - Pedido rechazado";
                                                            } elseif ($pedido['tiene_verificados'] == 1) {
                                                                $titulo_editar = "No se puede editar - Pedido con items verificados";
                                                            } elseif ($pedido['est_pedido'] == 2) {
                                                                $titulo_editar = "No se puede editar - Pedido completado (órdenes en proceso)";
                                                            } else {
                                                                $puede_editar = true;
                                                            }

                                                            if ($puede_editar) { ?>
                                                                <a href="pedidos_editar.php?id=<?php echo $pedido['id_pedido']; ?>" 
                                                                class="btn btn-warning btn-sm" 
                                                                title="Editar Pedido">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                            <?php } else { ?>
                                                                <a href="#" class="btn btn-outline-secondary btn-sm disabled" 
                                                                title="<?php echo $titulo_editar; ?>" tabindex="-1" aria-disabled="true">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                            <?php } ?>

                                                            <!-- ============================================ -->
                                                            <!-- BOTÓN GESTIONAR/VERIFICAR PEDIDO -->
                                                            <!-- ============================================ -->
                                                            <?php
                                                            $puede_gestionar = (
                                                                ($pedido['est_pedido'] == 1 || $pedido['est_pedido'] == 2 || 
                                                                $pedido['est_pedido'] == 3 || $pedido['est_pedido'] == 4) 
                                                                && $pedido['est_pedido'] != 0
                                                                && $pedido['est_pedido'] != 5
                                                            );

                                                            if (!$tiene_permiso_verificar) {
                                                                // SIN PERMISO - Botón rojo outline danger
                                                                ?>
                                                                <a href="#"
                                                                class="btn btn-outline-danger btn-sm disabled"
                                                                title="No tienes permiso para verificar pedidos"
                                                                tabindex="-1" aria-disabled="true">
                                                                    <i class="fa fa-check"></i>
                                                                </a>
                                                            <?php } elseif ($puede_gestionar) { ?>
                                                                <!-- CON PERMISO Y PUEDE GESTIONAR -->
                                                                <?php if ($tiene_tecnica) { ?>
                                                                    <a href="pedido_verificar.php?id=<?php echo $pedido['id_pedido']; ?>" 
                                                                    class="btn btn-success btn-sm" 
                                                                    title="Gestionar pedido">
                                                                        <i class="fa fa-check"></i>
                                                                    </a>
                                                                <?php } else { ?>
                                                                    <span title="Requiere aprobación técnica">
                                                                        <a href="#"
                                                                        class="btn btn-outline-secondary btn-sm disabled"
                                                                        title="Requiere aprobación técnica"
                                                                        tabindex="-1" aria-disabled="true">
                                                                            <i class="fa fa-check"></i>
                                                                        </a>
                                                                    </span>
                                                                <?php } ?>
                                                            <?php } else {
                                                                // CON PERMISO PERO NO PUEDE GESTIONAR POR PROCESO
                                                                $titulo_verificar = '';
                                                                
                                                                switch ($pedido['est_pedido']) {
                                                                    case 0: 
                                                                        $titulo_verificar = "No se puede gestionar - Pedido anulado"; 
                                                                        break;
                                                                    case 3: 
                                                                        $titulo_verificar = "No se puede gestionar - Pedido aprobado"; 
                                                                        break;
                                                                    case 4: 
                                                                        $titulo_verificar = "No se puede gestionar - Pedido ingresado"; 
                                                                        break;
                                                                    case 5: 
                                                                        $titulo_verificar = "No se puede gestionar - Pedido finalizado"; 
                                                                        break;
                                                                    default: 
                                                                        $titulo_verificar = "No disponible";
                                                                }
                                                                ?>
                                                                <a href="#" class="btn btn-outline-secondary btn-sm disabled"
                                                                title="<?php echo $titulo_verificar; ?>" tabindex="-1" aria-disabled="true">
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

                                                            <!-- ============================================ -->
                                                            <!-- BOTÓN ANULAR PEDIDO -->
                                                            <!-- ============================================ -->
                                                            <?php 
                                                            $puede_anular = false;
                                                            $titulo_anular = '';

                                                            if (!$tiene_permiso_anular) {
                                                                // SIN PERMISO - Botón rojo outline danger
                                                                $titulo_anular = "No tienes permiso para anular pedidos";
                                                                ?>
                                                                <button class="btn btn-outline-danger btn-sm disabled"
                                                                        title="<?php echo $titulo_anular; ?>"
                                                                        tabindex="-1" 
                                                                        aria-disabled="true">
                                                                    <i class="fa fa-times"></i>
                                                                </button>
                                                            <?php } else {
                                                                // CON PERMISO - Validar estado
                                                                if ($pedido['est_pedido'] == 0) {
                                                                    $titulo_anular = "Pedido ya anulado";
                                                                } elseif ($pedido['est_pedido'] == 2 || $pedido['est_pedido'] >= 3) {
                                                                    $estados_texto = [
                                                                        2 => "atendido",
                                                                        3 => "aprobado", 
                                                                        4 => "ingresado",
                                                                        5 => "finalizado"
                                                                    ];
                                                                    $estado_nombre = $estados_texto[$pedido['est_pedido']] ?? "procesado";
                                                                    $titulo_anular = "No se puede anular - Pedido {$estado_nombre}";
                                                                } else {
                                                                    // Verificar si tiene órdenes de compra activas
                                                                    $tiene_ordenes_compra = false;
                                                                    if (function_exists('ConsultarCompra')) {
                                                                        $ordenes = ConsultarCompra($pedido['id_pedido']);
                                                                        foreach ($ordenes as $orden) {
                                                                            if ($orden['est_compra'] != 0) {
                                                                                $tiene_ordenes_compra = true;
                                                                                break;
                                                                            }
                                                                        }
                                                                    }
                                                                    
                                                                    // Verificar si tiene órdenes de salida activas
                                                                    $tiene_ordenes_salida = false;
                                                                    if (function_exists('ConsultarSalidasPorPedido')) {
                                                                        $salidas = ConsultarSalidasPorPedido($pedido['id_pedido']);
                                                                        foreach ($salidas as $salida) {
                                                                            if ($salida['est_salida'] != 0) {
                                                                                $tiene_ordenes_salida = true;
                                                                                break;
                                                                            }
                                                                        }
                                                                    }
                                                                    
                                                                    if ($tiene_ordenes_compra || $tiene_ordenes_salida) {
                                                                        $restricciones = [];
                                                                        if ($tiene_ordenes_compra) $restricciones[] = "órdenes de compra";
                                                                        if ($tiene_ordenes_salida) $restricciones[] = "órdenes de salida";
                                                                        $titulo_anular = "No se puede anular - Tiene " . implode(" y ", $restricciones) . " asociadas";
                                                                    } else {
                                                                        $puede_anular = ($pedido['est_pedido'] == 1);
                                                                        if (!$puede_anular) {
                                                                            $titulo_anular = "Solo se pueden anular pedidos pendientes";
                                                                        }
                                                                    }
                                                                }

                                                                if ($puede_anular) { 
                                                                ?>
                                                                    <button class="btn btn-danger btn-sm" 
                                                                            onclick="AnularPedido(<?php echo $pedido['id_pedido']; ?>)"
                                                                            title="Anular Pedido">
                                                                        <i class="fa fa-times"></i>
                                                                    </button>
                                                                <?php } else { ?>
                                                                    <button class="btn btn-outline-secondary btn-sm disabled"
                                                                            title="<?php echo $titulo_anular; ?>"
                                                                            tabindex="-1" 
                                                                            aria-disabled="true">
                                                                        <i class="fa fa-times"></i>
                                                                    </button>
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
                                                <?php if ($detalle['cant_oc_pedido_detalle'] !== null) { ?>
                                                    <br><small class="text-success">Verificado: <?php echo $detalle['cant_oc_pedido_detalle']; ?></small>
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

<script>
function AnularPedido(id_pedido) {
    // Primero validar si puede anular
    $.ajax({
        url: 'pedido_validar_anulacion.php',
        type: 'POST',
        data: { id_pedido: id_pedido },
        dataType: 'json',
        success: function(validacion) {
            
            if (validacion.error) {
                Swal.fire('Error', validacion.mensaje, 'error');
                return;
            }

            // Si tiene restricciones, mostrar mensaje
            if (validacion.tiene_ordenes_compra || validacion.tiene_ordenes_salida) {
                let mensaje_restriccion = "No se puede anular el pedido porque:\n\n";
                
                if (validacion.tiene_ordenes_compra) {
                    mensaje_restriccion += `• Tiene ${validacion.total_ordenes_compra} orden(es) de compra asociada(s)\n`;
                }
                
                if (validacion.tiene_ordenes_salida) {
                    mensaje_restriccion += `• Tiene ${validacion.total_ordenes_salida} orden(es) de salida asociada(s)\n`;
                }
                
                mensaje_restriccion += "\nDebes anular primero todas las órdenes asociadas.";

                Swal.fire({
                    title: 'No se puede anular',
                    text: mensaje_restriccion,
                    icon: 'warning',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            // Si no tiene restricciones, confirmar anulación
            Swal.fire({
                title: '¿Estás seguro?',
                text: "El pedido será anulado. Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, anular pedido',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceder con la anulación
                    $.ajax({
                        url: 'pedido_anular.php',
                        type: 'POST',
                        data: { id_pedido: id_pedido },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Anulado', response.message, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire('Error', 'No se pudo anular el pedido.', 'error');
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        },
        error: function() {
            Swal.fire('Error', 'No se pudo validar la anulación. Intente nuevamente.', 'error');
        }
    });
}
</script>