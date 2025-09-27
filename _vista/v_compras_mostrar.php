<?php 
//=======================================================================
// VISTA: v_pedidos_mostrar.php
//=======================================================================
?>

<script>
function AprobarCompra(id_compra) {
    Swal.fire({
        title: '¿Deseas aprobar esta compra?',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, aprobar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'compras_aprobar.php',
                type: 'POST',
                data: { id_compra: id_compra },
                dataType: 'json', // 👈 importante
                success: function(response) {
                    if (response.tipo_mensaje === 'success') {
                        Swal.fire(
                            '¡Aprobado!',
                            response.mensaje,
                            'success'
                        ).then(() => {
                            location.reload(); // Recargar cambios
                        });
                    } else {
                        Swal.fire(
                            'Error',
                            response.mensaje,
                            'error'
                        );
                    }
                },
                error: function() {
                    Swal.fire(
                        'Error',
                        'No se pudo conectar con el servidor.',
                        'error'
                    );
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
            // 🔹 Anular solo la orden de compra
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
            // 🔹 Anular la orden y también el pedido
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

</script>

<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Compras<small></small></h3>
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
                                <h2>Listado de Compras<small></small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-2">
                               <!-- 
                               <a href="compras_nuevo.php" class="btn btn-outline-info btn-sm btn-block">Nueva Compra</a>
                               -->  
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
                                            foreach($compras as $compra) { 
                                            ?>
                                                <tr>
                                                    <td><?php echo $contador; ?></td>
                                                    <td><?php echo $compra['id_compra']; ?></td>
                                                    <td><a class="btn btn-sm btn-outline-secondary" target="_blank" href="pedido_pdf.php?id=<?php echo $compra['id_pedido']; ?>"><?php echo $compra['cod_pedido']; ?></a></td>
                                                    <td><?php echo $compra['nom_proveedor']; ?></td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($compra['fec_compra'])); ?></td>
                                                    <td><?php echo $compra['nom_registrado']; ?></td>
                                                    <td><?php echo $compra['nom_aprobado']; ?></td>
                                                    <td>
                                                        <?php if($compra['est_compra'] == 1) { ?>
                                                            <span class="badge badge-warning badge_size">Pendiente</span>
                                                        <?php } elseif($compra['est_compra'] == 2) { ?>
                                                            <span class="badge badge-success badge_size">Aprobado</span>
                                                        <?php } elseif($compra['est_compra'] == 3) { ?>
                                                            <span class="badge badge-success badge_size">Aprobado</span>
                                                        <?php } else { ?>
                                                            <span class="badge badge-danger badge_size">Anulado</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-wrap gap-2">

                                                        <?php
                                                        // Si está anulado o aprobado, bloquear botones de aprobar y anular
                                                        if ($compra['est_compra'] == 0 || $compra['est_compra'] == 2 || $compra['est_compra'] == 3) { ?>
                                                            <a href="#" class="btn btn-outline-secondary btn-sm disabled" title="Verificar" tabindex="-1" aria-disabled="true">
                                                                <i class="fa fa-check"></i>
                                                            </a>
                                                            <a href="#" class="btn btn-outline-secondary btn-sm disabled" title="Anular" tabindex="-1" aria-disabled="true">
                                                                <i class="fa fa-times"></i>
                                                            </a>
                                                            <a href="compras_pdf.php?id=<?php echo $compra['id_compra']; ?>"
                                                               class="btn btn-secondary btn-sm"
                                                               title="Generar PDF"
                                                               target="_blank">
                                                                <i class="fa fa-file-pdf-o"></i>
                                                            </a>
                                                        <?php
                                                        } else { ?>
                                                            <a href="#" onclick="AprobarCompra(<?php echo $compra['id_compra']; ?>)"
                                                               class="btn btn-success btn-sm"
                                                               title="Verificar">
                                                                <i class="fa fa-check"></i>
                                                            </a>
                                                            <a href="#" onclick="AnularCompra(<?php echo $compra['id_compra']; ?>, <?php echo $compra['id_pedido']; ?>)"
                                                               class="btn btn-danger btn-sm"
                                                               title="Anular">
                                                                <i class="fa fa-times"></i>
                                                            </a>
                                                            <a href="compras_pdf.php?id=<?php echo $compra['id_compra']; ?>"
                                                               class="btn btn-secondary btn-sm"
                                                               title="Generar PDF"
                                                               target="_blank">
                                                                <i class="fa fa-file-pdf-o"></i>
                                                            </a>
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
