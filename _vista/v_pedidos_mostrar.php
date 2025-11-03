<?php 
//=======================================================================
// VISTA: v_pedidos_mostrar.php
//=======================================================================
// Estados correctos: 0=Anulado, 1=Pendiente, 2=Completado, 3=Aprobado, 4=Ingresado, 5=Finalizado

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
                                <a href="pedidos_nuevo.php" class="btn btn-outline-info btn-sm btn-block">Nuevo Pedido</a>
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
                                                            // Verificar si este pedido está en la lista de rechazados
                                                            $es_rechazado = in_array($pedido['id_pedido'], $pedidos_rechazados);

                                                            // ESTADOS CORREGIDOS - Tu versión original estaba bien
                                                            if ($pedido['est_pedido'] == 0) { ?>
                                                                <span class="badge badge-danger badge_size">ANULADO</span>
                                                            <?php } elseif ($pedido['est_pedido'] == 1) { ?>
                                                                <span class="badge badge-warning badge_size">PENDIENTE</span>
                                                            <?php } elseif ($pedido['est_pedido'] == 2) { ?>
                                                                <span class="badge badge-info badge_size">COMPLETADO</span>
                                                            <?php } elseif ($pedido['est_pedido'] == 3) { ?>
                                                                <span class="badge badge-primary badge_size">APROBADO</span>
                                                            <?php } elseif ($pedido['est_pedido'] == 4) { ?>
                                                                <span class="badge badge-success badge_size">INGRESADO</span>
                                                            <?php } elseif ($pedido['est_pedido'] == 5) { ?>
                                                                <span class="badge badge-dark badge_size">FINALIZADO</span>
                                                            <?php } else { ?>
                                                                <span class="badge badge-secondary badge_size">DESCONOCIDO</span>
                                                            <?php } ?>
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
    
    <a href="#"
        <?php if ($tiene_tecnica) { ?>
            class="btn btn-outline-secondary btn-sm disabled"
            title="Ya aprobado técnica"
            tabindex="-1" aria-disabled="true"
        <?php } else { ?>
            onclick="AprobarPedidoTecnica(<?php echo $pedido['id_pedido']; ?>)"
            class="btn btn-success btn-sm"
            title="Aprobar Técnica"
        <?php } ?>>
            <i class="fa fa-check"></i>
    </a>
<?php
$es_rechazado = in_array($pedido['id_pedido'], $pedidos_rechazados);


//  BOTÓN EDITAR PEDIDO - LÓGICA CORREGIDA
// Solo se puede editar si:
// - Estado = 1 (Pendiente)
// - NO tiene verificaciones (para MATERIALES)
// - NO está rechazado
// - NO es SERVICIO con órdenes creadas

$puede_editar = false;
$titulo_editar = '';

if ($pedido['est_pedido'] == 0) {
    // Anulado
    $titulo_editar = "No se puede editar - Pedido anulado";
} elseif ($pedido['est_pedido'] >= 3) {
    // Aprobado (3), Ingresado (4) o Finalizado (5)
    $estados = [
        3 => "aprobado",
        4 => "ingresado",
        5 => "finalizado"
    ];
    $estado_nombre = $estados[$pedido['est_pedido']] ?? "procesado";
    $titulo_editar = "No se puede editar - Pedido {$estado_nombre}";
} elseif ($es_rechazado) {
    // Todas las órdenes anuladas
    $titulo_editar = "No se puede editar - Pedido rechazado";
} elseif ($pedido['tiene_verificados'] == 1) {
    // Tiene items verificados (solo aplica a MATERIALES)
    $titulo_editar = "No se puede editar - Pedido con items verificados";
} elseif ($pedido['est_pedido'] == 2) {
    // Completado (tiene órdenes en proceso)
    $titulo_editar = "No se puede editar - Pedido completado (órdenes en proceso)";
} else {
    // Estado 1 (Pendiente) y sin verificaciones → SE PUEDE EDITAR
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

        <!-- NUEVO: Botón Ver/Editar Órdenes de Compra -->
        <?php 
        // Verificar si tiene órdenes de compra
        $ordenes = ConsultarCompra($pedido['id_pedido']);
        $tiene_ordenes = !empty($ordenes);
        
        // Verificar si tiene alguna orden SIN aprobaciones (editable)
        $tiene_orden_editable = false;
        if ($tiene_ordenes) {
            foreach ($ordenes as $orden) {
                $sin_aprobacion_financiera = empty($orden['id_personal_aprueba_financiera']);
                if ($orden['est_compra'] == 1 && $sin_aprobacion_financiera) {
                    $tiene_orden_editable = true;
                    break;
                }
            }
        }
        
        if ($pedido['est_pedido_calc'] == 2) { 
            // Pedido completado - solo ver
        ?>
            <!-- <a href="pedido_verificar.php?id=<?php echo $pedido['id_pedido']; ?>" 
               class="btn btn-secondary btn-sm" 
               title="Ver Órdenes de Compra">
                <i class="fa fa-file-text"></i> Ver OC
            </a> -->
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
<!-- Botón Verificar/Gestionar -->
<?php
//  LÓGICA SIMPLIFICADA Y CORREGIDA
// Permitir acceso si:
// - Estado = 1 (Pendiente) o 2 (Completado)
// - NO está anulado (0)
// - NO está finalizado (5)

$puede_gestionar = (
    ($pedido['est_pedido'] == 1 || $pedido['est_pedido'] == 2) 
    && $pedido['est_pedido'] != 0
    && $pedido['est_pedido'] != 5
);


if ($puede_gestionar) { ?>
    <!-- 🔹 Botón para gestionar pedido -->
    <?php if ($tiene_tecnica) { ?>
        <a href="pedido_verificar.php?id=<?php echo $pedido['id_pedido']; ?>" 
           class="btn btn-success btn-sm" 
           title="Gestionar pedido">
            <i class="fa fa fa-check"></i>
        </a>
    <?php } else { ?>
        <span title="Requiere aprobación técnica">
        <a href="#"
           class="btn btn-outline-secondary btn-sm disabled"
           title="Requiere aprobación técnica"
           tabindex="-1" aria-disabled="true">
            <i class="fa fa fa-check"></i>
        </a>
        </span>
    <?php } ?>
    <!--<a href="pedido_verificar.php?id=<?php echo $pedido['id_pedido']; ?>" 
       class="btn btn-success btn-sm" 
       title="Gestionar pedido">
        <i class="fa fa-check"></i>
    </a>-->
<?php } else {
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

        <?php if ($pedido['est_pedido'] == 1 || $pedido['est_pedido'] == 2) { ?>
            <button 
                class="btn btn-danger btn-sm" 
                onclick="AnularPedido(<?php echo $pedido['id_pedido']; ?>)">
                <i class="fa fa-times"></i>
            </button>
        <?php } ?>
        
        <?php if ($pedido['est_pedido'] == 4): ?>
            <a href="salidas_nuevo.php?desde_pedido=<?php echo $pedido['id_pedido']; ?>" 
            class="btn btn-success btn-sm" title="Generar salida">
                <i class="fa fa-truck"></i>
            </a>
        <?php endif; ?>

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
    Swal.fire({
        title: '¿Estás seguro?',
        text: "El pedido será anulado y se liberará el stock comprometido.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, anular',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
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
}
</script>