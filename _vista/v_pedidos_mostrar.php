<?php 
//=======================================================================
// VISTA: v_pedidos_mostrar.php
//=======================================================================

// ========================================================================
// VERIFICAR PERMISOS AL INICIO
// ========================================================================
$tiene_permiso_crear = verificarPermisoEspecifico('crear_pedidos');
$tiene_permiso_editar = verificarPermisoEspecifico('editar_pedidos');
$tiene_permiso_aprobar_tecnica = verificarPermisoEspecifico('aprobar_pedidos');
$tiene_permiso_verificar = verificarPermisoEspecifico('verificar_pedidos');
$tiene_permiso_anular = verificarPermisoEspecifico('anular_pedidos');
?>
<script>
function AprobarPedidoTecnica(id_pedido) {
    Swal.fire({
        title: '¿Deseas aprobar este pedido?',
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
                            <div class="col-sm-8">
                                <h2>Listado de Pedidos<small></small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-4">
                                <div class="row">
                                    <!-- BOTÓN EXCEL REPORTE -->
                                    <div class="col-sm-6">
                                        <a href="generar_excel_pedidos.php<?php 
                                            // Mantener filtros de fecha si existen
                                            $params = array();
                                            if (!empty($fecha_inicio)) $params[] = 'fecha_inicio=' . $fecha_inicio;
                                            if (!empty($fecha_fin)) $params[] = 'fecha_fin=' . $fecha_fin;
                                            if (!empty($id_personal_filtro)) $params[] = 'id_personal=' . $id_personal_filtro;
                                            echo !empty($params) ? '?' . implode('&', $params) : '';
                                        ?>" 
                                        class="btn btn-success btn-sm btn-block">
                                            <i class="fa fa-file-excel-o"></i> Excel Reporte
                                        </a>
                                    </div>
                                    
                                    <!-- BOTÓN NUEVO PEDIDO -->
                                    <div class="col-sm-6">
                                        <?php if (!$tiene_permiso_crear) { ?>
                                            <a href="#" 
                                            class="btn btn-outline-secondary btn-sm btn-block disabled"
                                            title="No tienes permiso para crear pedidos"
                                            tabindex="-1" 
                                            aria-disabled="true">
                                            <i class="fa fa-plus"></i> Nuevo Pedido
                                            </a>
                                        <?php } else { ?>
                                            <a href="pedidos_nuevo.php" 
                                            class="btn btn-outline-info btn-sm btn-block"
                                            title="Crear nuevo pedido">
                                            <i class="fa fa-plus"></i> Nuevo Pedido
                                            </a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="x_content">
                        <!-- Filtro de fechas -->
                        <form method="get" action="pedidos_mostrar.php" class="form-inline mb-3">
                            <div class="form-group mx-sm-2 mb-2">
                                <label for="fecha_inicio" class="mr-2">Desde:</label>
                                <input 
                                    type="date" 
                                    id="fecha_inicio" 
                                    name="fecha_inicio" 
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($fecha_inicio); ?>">
                            </div>
                            <div class="form-group mx-sm-2 mb-2">
                                <label for="fecha_fin" class="mr-2">Hasta:</label>
                                <input 
                                    type="date" 
                                    id="fecha_fin" 
                                    name="fecha_fin" 
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($fecha_fin); ?>">
                            </div>
                            <button type="submit" class="btn btn-primary mb-2"><i class="fa fa-search"></i> Consultar</button>
                            <button type="button" class="btn btn-secondary mb-2" onclick="window.location.href='pedidos_mostrar.php'"><i class="bi bi-eraser"></i> Limpiar</button>
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
                                                <!-- <th>Nombre Pedido</th> -->
                                                <th>Almacén</th>
                                                <th>Ubicación</th>
                                                <th>Solicitante</th>
                                                <th>Fecha Pedido</th>
                                                <th>Fecha Necesidad</th>
                                                <th>Aprobado Por</th>
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
                                                    <!--<td><?php echo $pedido['nom_pedido']; ?></td> -->
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
                                                                // ATENDIDO (TODO completado)
                                                                $estado_final = 'ATENDIDO';
                                                                $badge_class = 'badge-success';

                                                            } elseif ($pedido['est_pedido'] == 1) {
                                                                // PENDIENTE (puede estar aprobado técnicamente)
                                                                $tiene_tecnica = !empty($pedido['id_personal_aprueba_tecnica']);
                                                                
                                                                if ($tiene_tecnica) {
                                                                    $estado_final = 'APROBADO';
                                                                    $badge_class = 'badge-info';
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
                                                                    class="btn btn-info btn-sm btn-ver-detalle-pedido" 
                                                                    data-toggle="modal"
                                                                    data-target="#modalDetallePedido<?php echo $pedido['id_pedido']; ?>"
                                                                    data-placement="top" 
                                                                    title="Ver Detalle del Pedido">
                                                                <i class="fa fa-eye"></i>
                                                            </button>
                                                        
                                                            <!-- ============================================ -->
                                                            <!-- BOTÓN APROBAR TÉCNICA -->
                                                            <!-- ============================================ -->
                                                            <?php
                                                            if (!$tiene_permiso_aprobar_tecnica) {
                                                                // SIN PERMISO - Botón rojo outline danger
                                                                ?>
                                                                <span data-toggle="tooltip" data-placement="top"
                                                                    title="No tienes permiso para aprobar pedidos">
                                                                    <a href="#"
                                                                    class="btn btn-outline-secondary btn-sm disabled"
                                                                    tabindex="-1" aria-disabled="true">
                                                                        <i class="fa fa-check"></i>
                                                                    </a>
                                                                </span>
                                                            <?php } elseif ($tiene_tecnica) { ?>
                                                                <!-- YA APROBADO - Gris por proceso -->
                                                                <span data-toggle="tooltip" data-placement="top"
                                                                    title="Ya aprobado">
                                                                    <a href="#"
                                                                    class="btn btn-outline-secondary btn-sm disabled"
                                                                    tabindex="-1" aria-disabled="true">
                                                                        <i class="fa fa-check"></i>
                                                                    </a>
                                                                </span>
                                                            <?php } else { ?>
                                                                <!-- PUEDE APROBAR - Verde activo -->
                                                                <a href="#"
                                                                onclick="AprobarPedidoTecnica(<?php echo $pedido['id_pedido']; ?>)"
                                                                class="btn btn-success btn-sm"
                                                                data-toggle="tooltip"
                                                                data-placement="top"
                                                                title="Aprobar">
                                                                    <i class="fa fa-check"></i>
                                                                </a>
                                                            <?php } ?>

                                                            <?php
                                                            // ============================================
                                                            // BOTÓN EDITAR PEDIDO
                                                            // ============================================
                                                            $es_rechazado = in_array($pedido['id_pedido'], $pedidos_rechazados);
                                                            $puede_editar_proceso = false;
                                                            $titulo_editar = '';

                                                            // Verificar si tiene verificación técnica
                                                            $verificacion_tecnica = TieneVerificacionTecnica($pedido['id_pedido']);
                                                            $tiene_verificacion_tecnica = $verificacion_tecnica['verificado'];

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
                                                            } elseif ($tiene_verificacion_tecnica) {
                                                                $titulo_editar = "No se puede editar - Pedido verificado técnicamente";
                                                            } elseif ($pedido['tiene_verificados'] == 1) {
                                                                $titulo_editar = "No se puede editar - Pedido con items verificados";
                                                            } elseif ($pedido['est_pedido'] == 2) {
                                                                $titulo_editar = "No se puede editar - Pedido completado (órdenes en proceso)";
                                                            } else {
                                                                $puede_editar_proceso = true;
                                                            }

                                                            if (!$tiene_permiso_editar) { ?>
                                                                <span data-toggle="tooltip" data-placement="top"
                                                                    title="No tienes permiso para editar pedidos">
                                                                    <a href="#"
                                                                    class="btn btn-outline-secondary btn-sm disabled"
                                                                    tabindex="-1" aria-disabled="true">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                </span>
                                                            <?php } elseif (!$puede_editar_proceso) { ?>
                                                                <span data-toggle="tooltip"
                                                                    data-placement="top"
                                                                    title="<?php echo $titulo_editar; ?>">
                                                                    <a href="#"
                                                                    class="btn btn-outline-secondary btn-sm disabled"
                                                                    tabindex="-1" aria-disabled="true">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                </span>
                                                            <?php } else { ?>
                                                                <a href="pedidos_editar.php?id=<?php echo $pedido['id_pedido']; ?>" 
                                                                class="btn btn-warning btn-sm"
                                                                data-toggle="tooltip"
                                                                data-placement="top"
                                                                title="Editar Pedido">
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
                                                                <span data-toggle="tooltip" title="No tienes permiso para verificar pedidos">
                                                                    <a href="#"
                                                                    class="btn btn-outline-secondary btn-sm disabled"
                                                                    tabindex="-1" aria-disabled="true">
                                                                        <i class="fa fa-check"></i>
                                                                    </a>
                                                                </span>
                                                            <?php } elseif ($puede_gestionar) { ?>
                                                                <!-- CON PERMISO Y PUEDE GESTIONAR -->
                                                                <?php if ($tiene_tecnica) { ?>
                                                                    <a href="pedido_verificar.php?id=<?php echo $pedido['id_pedido']; ?>" 
                                                                    class="btn btn-success btn-sm" 
                                                                    data-toggle="tooltip"
                                                                    data-placement="top"
                                                                    title="Verificar pedido">
                                                                        <i class="fa fa-check"></i>
                                                                    </a>
                                                                <?php } else { ?>
                                                                    <span data-toggle="tooltip" title="Requiere aprobación">
                                                                        <a href="#" 
                                                                        class="btn btn-outline-secondary btn-sm disabled"
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
                                                                <span data-toggle="tooltip" title="<?php echo $titulo_verificar; ?>">
                                                                    <a href="#" 
                                                                    class="btn btn-outline-secondary btn-sm disabled"
                                                                    tabindex="-1" aria-disabled="true">
                                                                        <i class="fa fa-check"></i>
                                                                    </a>
                                                                </span>
                                                            <?php } ?>

                                                            <!-- Botón PDF -->
                                                            <a href="pedido_pdf.php?id=<?php echo $pedido['id_pedido']; ?>" 
                                                            data-toggle="tooltip"
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
                                                                <span data-toggle="tooltip" title="<?php echo $titulo_anular; ?>">
                                                                    <button class="btn btn-outline-secondary btn-sm disabled"
                                                                            tabindex="-1" 
                                                                            aria-disabled="true">
                                                                        <i class="fa fa-times"></i>
                                                                    </button>
                                                                </span>
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
                                                                            data-toggle="tooltip" 
                                                                            title="Anular Pedido">
                                                                        <i class="fa fa-times"></i>
                                                                    </button>
                                                                <?php } else { ?>
                                                                    <span data-toggle="tooltip" title="<?php echo $titulo_anular; ?>">
                                                                        <button class="btn btn-outline-secondary btn-sm disabled"
                                                                                tabindex="-1" 
                                                                                aria-disabled="true">
                                                                            <i class="fa fa-times"></i>
                                                                        </button>
                                                                    </span>
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
                <!-- INFORMACIÓN GENERAL -->
                <div class="row">
                    <div class="col-md-12">
                        <h5><strong>Información General</strong></h5>
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>Código del Pedido:</strong></td>
                                <td><?php echo $pedido_info['cod_pedido']; ?></td>
                                <td><strong>Tipo de Pedido:</strong></td>
                                <td><?php echo $pedido_info['nom_producto_tipo']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Fecha del Pedido:</strong></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($pedido_info['fec_pedido'])); ?></td>
                                <td><strong>Fecha de Necesidad:</strong></td>
                                <td><?php echo date('d/m/Y', strtotime($pedido_info['fec_req_pedido'])); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Almacén:</strong></td>
                                <td><?php echo $pedido_info['nom_almacen']; ?></td>
                                <td><strong>Ubicación:</strong></td>
                                <td><?php echo $pedido_info['nom_ubicacion']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Solicitante:</strong></td>
                                <td><?php echo $pedido_info['nom_personal']; ?></td>
                                <td><strong>Centro de Costo (Solicitante):</strong></td>
                                <td>
                                    <span class="badge badge-primary badge_size">
                                        <?php echo !empty($pedido_info['nom_centro_costo']) ? $pedido_info['nom_centro_costo'] : 'Sin asignar'; ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Contacto:</strong></td>
                                <td><?php echo $pedido_info['cel_pedido']; ?></td>
                                <td><strong>Lugar de Entrega:</strong></td>
                                <td><?php echo $pedido_info['lug_pedido']; ?></td>
                            </tr>
                            <?php if (!empty($pedido_info['acl_pedido'])) { ?>
                            <tr>
                                <td><strong>Aclaraciones:</strong></td>
                                <td colspan="3"><?php echo nl2br($pedido_info['acl_pedido']); ?></td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>

                <!-- 🔹 APROBACIONES Y VERIFICACIONES -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h5><strong>Aprobaciones y Verificaciones</strong></h5>
                        <table class="table table-bordered">
                            <!-- Aprobación Técnica -->
                            <tr>
                                <td style="width: 25%;"><strong>Aprobado Técnicamente Por:</strong></td>
                                <td style="width: 25%;">
                                    <?php 
                                    if (!empty($pedido_info['id_personal_aprueba_tecnica'])) {
                                        echo $pedido_info['nom_aprobado_tecnica'];
                                    } else {
                                        echo '<span class="text-muted">Pendiente</span>';
                                    }
                                    ?>
                                </td>
                                <td style="width: 25%;"><strong>Fecha de Aprobación Técnica:</strong></td>
                                <td style="width: 25%;">
                                    <?php 
                                    if (!empty($pedido_info['fec_aprueba_tecnica'])) {
                                        echo date('d/m/Y H:i', strtotime($pedido_info['fec_aprueba_tecnica']));
                                    } else {
                                        echo '<span class="text-muted">-</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            
                            <!-- Verificación Técnica -->
                            <tr>
                                <td><strong>Verificado Técnicamente Por:</strong></td>
                                <td>
                                    <?php 
                                    if (!empty($pedido_info['id_personal_verifica_tecnica'])) {
                                        echo $pedido_info['nom_verificado_tecnica'];
                                    } else {
                                        echo '<span class="text-muted">Pendiente</span>';
                                    }
                                    ?>
                                </td>
                                <td><strong>Fecha de Verificación Técnica:</strong></td>
                                <td>
                                    <?php 
                                    if (!empty($pedido_info['fec_verifica_tecnica'])) {
                                        echo date('d/m/Y H:i', strtotime($pedido_info['fec_verifica_tecnica']));
                                    } else {
                                        echo '<span class="text-muted">-</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- DETALLES DEL PEDIDO -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h5><strong>Detalles del Pedido</strong></h5>
                        <?php if (!empty($pedido_detalle)) { ?>
                            <table class="table table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Material/Servicio</th>
                                    <th>Unidad</th>
                                    <th>Cantidad</th>
                                    <th>Centro(s) de Costo</th>
                                    <th>Personal Asignado</th>
                                    <th>Nº OT/LCL/LCA</th>
                                    <th>Descripción SST/MA/CA</th>
                                    <th>Archivos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $contador_detalle = 1;
                                foreach ($pedido_detalle as $detalle) { 
                                    // Parsear comentarios para extraer unidad
                                    $comentario = $detalle['com_pedido_detalle'];
                                    $unidad_nombre = '';
                                    
                                    if (preg_match('/Unidad:\s*([^|]*)\s*\|/', $comentario, $matches)) {
                                        $unidad_nombre = trim($matches[1]);
                                    }
                                    
                                    $sst_descripcion = $detalle['req_pedido'];
                                    
                                    $ot_numero = !empty($detalle['ot_pedido_detalle']) ? trim($detalle['ot_pedido_detalle']) : '';

                                    
                                    // Obtener centros de costo del detalle
                                    $centros_costo_detalle = [];
                                    if (function_exists('ObtenerCentrosCostoDetalle')) {
                                        $centros_costo_detalle = ObtenerCentrosCostoDetalle($detalle['id_pedido_detalle']);
                                    }
                                    
                                    // Obtener personal asignado
                                    $personal_asignado = [];
                                    if (function_exists('ObtenerPersonalDetalleCompleto')) {
                                        $personal_asignado = ObtenerPersonalDetalleCompleto($detalle['id_pedido_detalle']);
                                    }
                                ?>
                                    <tr>
                                        <!-- COLUMNA 1: # -->
                                        <td><?php echo $contador_detalle; ?></td>
                                        
                                        <!-- COLUMNA 2: Material/Servicio -->
                                        <td>
                                            <strong><?php echo $detalle['prod_pedido_detalle']; ?></strong>
                                            <?php if (!empty($detalle['cod_material'])) { ?>
                                                <br><small class="text-muted">Código: <?php echo $detalle['cod_material']; ?></small>
                                            <?php } elseif (!empty($detalle['nom_producto'])) { ?>
                                                <br><small class="text-muted">Producto: <?php echo $detalle['nom_producto']; ?></small>
                                            <?php } ?>
                                        </td>
                                        
                                        <!-- COLUMNA 3: Unidad -->
                                        <td>
                                            <span class="badge badge-secondary badge_size">
                                                <?php echo !empty($unidad_nombre) ? $unidad_nombre : 'N/A'; ?>
                                            </span>
                                        </td>
                                        
                                        <!-- COLUMNA 4: Cantidad -->
                                        <td>
                                            <span class="badge badge-primary badge_size"><?php echo $detalle['cant_pedido_detalle']; ?></span>
                                            <?php if ($detalle['cant_oc_pedido_detalle'] !== null) { ?>
                                                <br><small class="text-success"><i class="fa fa-check"></i> Verificado: <?php echo $detalle['cant_oc_pedido_detalle']; ?></small>
                                            <?php } ?>
                                        </td>
                                        
                                        <!-- COLUMNA 5: Centro(s) de Costo -->
                                        <td>
                                            <?php if (!empty($centros_costo_detalle)) { 
                                                $total_centros = count($centros_costo_detalle);
                                                $modalId = 'modalCentrosCostoPedido' . $detalle['id_pedido_detalle'];
                                                
                                                if ($total_centros === 1) {
                                                    ?>
                                                    <span class="badge badge-info badge_size" style="font-size: 11px;">
                                                        <?php echo $centros_costo_detalle[0]['nom_centro_costo']; ?>
                                                    </span>
                                                <?php } else { 
                                                    $listaCentros = '';
                                                    foreach ($centros_costo_detalle as $idx => $cc) {
                                                        $listaCentros .= '<div style="padding: 8px; margin-bottom: 6px; background-color: #f8f9fa; border-left: 3px solid #17a2b8; border-radius: 4px;">';
                                                        $listaCentros .= '<strong style="color: #17a2b8;">' . ($idx + 1) . '.</strong> ' . htmlspecialchars($cc['nom_centro_costo']);
                                                        $listaCentros .= '</div>';
                                                    }
                                                    ?>
                                                    <button class="btn btn-sm btn-info btn-ver-centros-costo-pedido" 
                                                            type="button" 
                                                            data-modal-id="<?php echo $modalId; ?>"
                                                            style="font-size: 11px; padding: 3px 10px;">
                                                        <i class="fa fa-eye"></i> Ver <?php echo $total_centros; ?> centros
                                                    </button>
                                                    
                                                    <!-- Modal para centros de costo -->
                                                    <div class="modal fade" id="<?php echo $modalId; ?>" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header" style="background-color: #17a2b8; color: white; padding: 12px 20px;">
                                                                    <h6 class="modal-title mb-0">
                                                                        <i class="fa fa-building"></i> 
                                                                        Centros de Costo Asignados
                                                                    </h6>
                                                                    <button type="button" class="close close-centros-modal-pedido" data-modal-id="<?php echo $modalId; ?>" aria-label="Close" style="color: white; opacity: 0.8;">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body" style="padding: 20px;">
                                                                    <div style="margin-bottom: 15px; padding: 10px; background-color: #e7f3ff; border-radius: 4px; border-left: 4px solid #17a2b8;">
                                                                        <strong>Producto:</strong> <?php echo htmlspecialchars($detalle['prod_pedido_detalle']); ?>
                                                                    </div>
                                                                    <div style="max-height: 400px; overflow-y: auto;">
                                                                        <?php echo $listaCentros; ?>
                                                                    </div>
                                                                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6; text-align: center;">
                                                                        <span class="badge badge-info" style="font-size: 12px; padding: 6px 12px;">
                                                                            Total: <?php echo $total_centros; ?> centro(s) de costo
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer" style="padding: 10px 20px;">
                                                                    <button type="button" class="btn btn-secondary btn-sm close-centros-modal-pedido" data-modal-id="<?php echo $modalId; ?>">
                                                                        <i class="fa fa-times"></i> Cerrar
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <small class="text-muted">Sin asignar</small>
                                            <?php } ?>
                                        </td>
                                        
                                        <!-- COLUMNA 6: Personal Asignado -->
                                        <td>
                                            <?php if (!empty($personal_asignado)) { 
                                                $total_personal = count($personal_asignado);
                                                $modalIdPersonal = 'modalPersonalPedido' . $detalle['id_pedido_detalle'];
                                                
                                                if ($total_personal === 1) {
                                                    $persona = $personal_asignado[0];
                                                    ?>
                                                    <span class="badge badge-success badge_size d-block" style="font-size: 11px;">
                                                        <?php echo $persona['nom_personal']; ?>
                                                        <?php if (!empty($persona['nom_cargo'])) { ?>
                                                            <br><small><?php echo $persona['nom_cargo']; ?></small>
                                                        <?php } ?>
                                                    </span>
                                                <?php } else { 
                                                    $listaPersonal = '';
                                                    foreach ($personal_asignado as $idx => $persona) {
                                                        $listaPersonal .= '<div style="padding: 10px; margin-bottom: 8px; background-color: #f0f9ff; border-left: 3px solid #28a745; border-radius: 4px;">';
                                                        $listaPersonal .= '<div style="display: flex; align-items: center; gap: 8px;">';
                                                        $listaPersonal .= '<strong style="color: #28a745; font-size: 16px;">' . ($idx + 1) . '.</strong>';
                                                        $listaPersonal .= '<div>';
                                                        $listaPersonal .= '<div style="font-weight: 600; color: #2c3e50;">' . htmlspecialchars($persona['nom_personal']) . '</div>';
                                                        if (!empty($persona['nom_cargo'])) {
                                                            $listaPersonal .= '<small style="color: #7f8c8d;">' . htmlspecialchars($persona['nom_cargo']) . '</small>';
                                                        }
                                                        $listaPersonal .= '</div>';
                                                        $listaPersonal .= '</div>';
                                                        $listaPersonal .= '</div>';
                                                    }
                                                    ?>
                                                    <button class="btn btn-sm btn-success btn-ver-personal-pedido" 
                                                            type="button" 
                                                            data-modal-id="<?php echo $modalIdPersonal; ?>"
                                                            style="font-size: 11px; padding: 3px 10px;">
                                                        <i class="fa fa-users"></i> Ver <?php echo $total_personal; ?> persona(s)
                                                    </button>
                                                    
                                                    <!-- Modal para personal asignado -->
                                                    <div class="modal fade" id="<?php echo $modalIdPersonal; ?>" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header" style="background-color: #28a745; color: white; padding: 12px 20px;">
                                                                    <h6 class="modal-title mb-0">
                                                                        <i class="fa fa-users"></i> 
                                                                        Personal Asignado
                                                                    </h6>
                                                                    <button type="button" class="close close-personal-modal-pedido" data-modal-id="<?php echo $modalIdPersonal; ?>" aria-label="Close" style="color: white; opacity: 0.8;">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body" style="padding: 20px;">
                                                                    <div style="margin-bottom: 15px; padding: 10px; background-color: #e7f3ff; border-radius: 4px; border-left: 4px solid #17a2b8;">
                                                                        <strong>Servicio:</strong> <?php echo htmlspecialchars($detalle['prod_pedido_detalle']); ?>
                                                                    </div>
                                                                    <div style="max-height: 400px; overflow-y: auto;">
                                                                        <?php echo $listaPersonal; ?>
                                                                    </div>
                                                                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6; text-align: center;">
                                                                        <span class="badge badge-success" style="font-size: 12px; padding: 6px 12px;">
                                                                            Total: <?php echo $total_personal; ?> persona(s) asignada(s)
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer" style="padding: 10px 20px;">
                                                                    <button type="button" class="btn btn-secondary btn-sm close-personal-modal-pedido" data-modal-id="<?php echo $modalIdPersonal; ?>">
                                                                        <i class="fa fa-times"></i> Cerrar
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <small class="text-muted">-</small>
                                            <?php } ?>
                                        </td>
                                        
                                        <!-- COLUMNA 7: Nº OT/LCL/LCA -->
                                        <td>
                                            <?php if (!empty($ot_numero)) { ?>
                                                <?php echo htmlspecialchars($ot_numero); ?>
                                            <?php } else { ?>
                                                <small class="text-muted">-</small>
                                            <?php } ?>
                                        </td>
                                        
                                        <!-- COLUMNA 8: Descripción SST/MA/CA -->
                                        <td>
                                            <?php if (!empty($sst_descripcion)) { ?>
                                                <small><?php echo nl2br($sst_descripcion); ?></small>
                                            <?php } else { ?>
                                                <small class="text-muted">-</small>
                                            <?php } ?>
                                        </td>
                                        
                                        <!-- COLUMNA 9: Archivos -->
                                        <td>
                                            <?php 
                                            $archivos_activos = ObtenerArchivosActivosDetalle($detalle['id_pedido_detalle']);
                                            
                                            if (!empty($archivos_activos)) { 
                                                foreach ($archivos_activos as $archivo) { 
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
                                                        <?php echo strlen($archivo) > 25 ? substr($archivo, 0, 25) . '...' : $archivo; ?>
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
                $tiene_tecnica = !empty($pedido['id_personal_aprueba_tecnica']);
                $puede_editar_modal = true;
                $titulo_editar_modal = '';
                
                // Aplicar las mismas validaciones que en la tabla
                // Verificar si tiene verificación técnica (para el modal)
                $verificacion_tecnica_modal = TieneVerificacionTecnica($pedido['id_pedido']);
                $tiene_verificacion_tecnica_modal = $verificacion_tecnica_modal['verificado'];

                // Aplicar las mismas validaciones que en la tabla
                if ($pedido['est_pedido'] == 0) {
                    $puede_editar_modal = false;
                    $titulo_editar_modal = "No se puede editar - Pedido anulado";
                } elseif ($pedido['est_pedido'] >= 3) {
                    $puede_editar_modal = false;
                    $estados = [
                        3 => "aprobado",
                        4 => "ingresado",
                        5 => "finalizado"
                    ];
                    $estado_nombre = $estados[$pedido['est_pedido']] ?? "procesado";
                    $titulo_editar_modal = "No se puede editar - Pedido {$estado_nombre}";
                } elseif ($es_rechazado) {
                    $puede_editar_modal = false;
                    $titulo_editar_modal = "No se puede editar - Pedido rechazado";
                } elseif ($tiene_verificacion_tecnica_modal) {
                    $puede_editar_modal = false;
                    $titulo_editar_modal = "No se puede editar - Pedido verificado técnicamente";
                } elseif ($pedido['tiene_verificados'] == 1) {
                    $puede_editar_modal = false;
                    $titulo_editar_modal = "No se puede editar - Pedido con items verificados";
                } elseif ($pedido['est_pedido'] == 2) {
                    $puede_editar_modal = false;
                    $titulo_editar_modal = "No se puede editar - Pedido completado (órdenes en proceso)";
                }
                
                if (!$tiene_permiso_editar) { ?>
                    <button type="button" 
                            class="btn btn-outline-secondary disabled"
                            title="No tienes permiso para editar pedidos"
                            disabled>
                        <i class="fa fa-edit"></i> Editar Pedido
                    </button>
                <?php } elseif (!$puede_editar_modal) { ?>
                    <a href="#" class="btn btn-outline-secondary disabled" 
                    title="<?php echo $titulo_editar_modal; ?>" 
                    tabindex="-1" aria-disabled="true">
                        <i class="fa fa-edit"></i> No se puede editar
                    </a>
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
// 🔹 CONFIGURAR EVENTOS PARA MODALES ANIDADOS EN PEDIDOS
(function() {
    'use strict';
    
    // Esperar a que jQuery y Bootstrap estén disponibles
    function esperarLibrerias(callback) {
        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.modal !== 'undefined') {
            callback();
        } else {
            setTimeout(function() { esperarLibrerias(callback); }, 100);
        }
    }
    
    esperarLibrerias(function() {
        console.log('🟢 jQuery y Bootstrap disponibles - Inicializando eventos de modales');
        configurarEventosModalesAnidados();
    });
    
    function configurarEventosModalesAnidados() {
        console.log('⚙️ Configurando eventos de modales anidados...');
        
        // ========================================
        // 🔹 CENTROS DE COSTO
        // ========================================
        
        // Abrir modal de centros de costo
        jQuery(document).off('click', '.btn-ver-centros-costo-pedido').on('click', '.btn-ver-centros-costo-pedido', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const modalId = jQuery(this).data('modal-id');
            console.log('👁️ Abriendo modal de centros:', modalId);
            
            abrirModalHijo(modalId);
        });
        
        // Cerrar modal de centros de costo
        jQuery(document).off('click', '.close-centros-modal-pedido').on('click', '.close-centros-modal-pedido', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const modalId = jQuery(this).data('modal-id');
            console.log('❌ Cerrando modal de centros:', modalId);
            
            cerrarModalHijo(modalId);
        });
        
        // ========================================
        // 🔹 PERSONAL ASIGNADO
        // ========================================
        
        // Abrir modal de personal
        jQuery(document).off('click', '.btn-ver-personal-pedido').on('click', '.btn-ver-personal-pedido', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const modalId = jQuery(this).data('modal-id');
            console.log('👥 Abriendo modal de personal:', modalId);
            
            abrirModalHijo(modalId);
        });
        
        // Cerrar modal de personal
        jQuery(document).off('click', '.close-personal-modal-pedido').on('click', '.close-personal-modal-pedido', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const modalId = jQuery(this).data('modal-id');
            console.log('❌ Cerrando modal de personal:', modalId);
            
            cerrarModalHijo(modalId);
        });
        
        // ========================================
        // 🔹 FUNCIONES AUXILIARES REUTILIZABLES
        // ========================================
        
        function abrirModalHijo(modalId) {
            const $modalHijo = jQuery('#' + modalId);
            
            if ($modalHijo.length) {
                console.log('✅ Modal hijo encontrado:', modalId);
                
                // Obtener el modal padre
                const $modalPadre = $modalHijo.closest('.modal').siblings('.modal.show').first();
                if (!$modalPadre.length) {
                    // Alternativa: buscar cualquier modal abierto
                    const $modalPadreAlt = jQuery('.modal.show').last();
                    if ($modalPadreAlt.length) {
                        const modalPadreId = $modalPadreAlt.attr('id');
                        console.log('📦 Modal padre detectado:', modalPadreId);
                        $modalHijo.data('modal-padre-id', modalPadreId);
                    }
                } else {
                    const modalPadreId = $modalPadre.attr('id');
                    console.log('📦 Modal padre detectado:', modalPadreId);
                    $modalHijo.data('modal-padre-id', modalPadreId);
                }
                
                // Abrir modal hijo
                $modalHijo.modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            } else {
                console.error('❌ Modal hijo NO encontrado:', modalId);
            }
        }
        
        function cerrarModalHijo(modalId) {
            const $modalHijo = jQuery('#' + modalId);
            
            if ($modalHijo.length) {
                const modalPadreId = $modalHijo.data('modal-padre-id');
                
                // Cerrar el modal hijo
                $modalHijo.modal('hide');
                
                // Reabrir modal padre cuando el hijo se cierre completamente
                $modalHijo.one('hidden.bs.modal', function() {
                    if (modalPadreId) {
                        const $modalPadre = jQuery('#' + modalPadreId);
                        if ($modalPadre.length) {
                            console.log('🔄 Reabriendo modal padre:', modalPadreId);
                            setTimeout(function() {
                                $modalPadre.modal('show');
                            }, 100);
                        }
                    }
                });
            }
        }
        
        // Prevenir cierre accidental de modales anidados
        jQuery('[id^="modalCentrosCostoPedido"], [id^="modalPersonalPedido"]').each(function() {
            const $modal = jQuery(this);
            
            // Remover handlers previos
            $modal.off('hidden.bs.modal.anidados');
            
            // Agregar handler para restaurar el padre
            $modal.on('hidden.bs.modal.anidados', function(e) {
                e.stopPropagation();
                
                const modalPadreId = jQuery(this).data('modal-padre-id');
                if (modalPadreId) {
                    const $modalPadre = jQuery('#' + modalPadreId);
                    if ($modalPadre.length && !$modalPadre.hasClass('show')) {
                        console.log('🔄 Modal padre cerrado accidentalmente, reabriendo:', modalPadreId);
                        setTimeout(function() {
                            $modalPadre.modal('show');
                        }, 100);
                    }
                }
            });
        });
        
        console.log('✅ Eventos configurados correctamente');
        console.log('🔢 Botones centros de costo:', jQuery('.btn-ver-centros-costo-pedido').length);
        console.log('🔢 Botones personal:', jQuery('.btn-ver-personal-pedido').length);
        console.log('🔢 Modales anidados:', jQuery('[id^="modalCentrosCostoPedido"], [id^="modalPersonalPedido"]').length);
    }
    
    // Hacer la función global para reinicializar si es necesario
    window.configurarEventosModalesAnidados = configurarEventosModalesAnidados;
})();

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