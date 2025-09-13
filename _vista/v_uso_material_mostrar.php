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
function AnularCompra(id_compra) {
    Swal.fire({
        title: '¿Deseas anular esta compra?',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, anular',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'compras_anular.php',
                type: 'POST',
                data: { id_compra: id_compra },
                dataType: 'json', // 👈 importante
                success: function(response) {
                    if (response.tipo_mensaje === 'success') {
                        Swal.fire(
                            '¡Anulado!',
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

</script>

<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Uso de Material<small></small></h3>
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
                                <h2>Listado de Uso de Material<small></small></h2>
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
                                                <th>N° Uso</th>
                                                <th>Almacén</th>
                                                <th>Ubicación</th>
                                                <th>Obra</th>
                                                <th>Cliente</th>
                                                <th>Solicitante</th>
                                                <th>Registrado por</th>
                                                <th>Fecha Registro</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php 
                                            $contador = 1;
                                            foreach($usos_material as $uso) { 
                                            ?>
                                                <tr>
                                                    <td><?php echo $contador; ?></td>
                                                    <td><?php echo $uso['id_uso_material']; ?></td>
                                                    <td><a class="btn btn-sm btn-outline-secondary" target="_blank" href="pedido_pdf.php?id=<?php echo $uso['id_pedido']; ?>"><?php echo $uso['cod_pedido']; ?></a></td>
                                                    <td><?php echo $uso['nom_almacen']; ?></td>
                                                    <td><?php echo $uso['nom_ubicacion']; ?></td>
                                                    <td><?php echo $uso['nom_obra']; ?></td>
                                                    <td><?php echo $uso['nom_cliente']; ?></td>
                                                    <td><?php echo $uso['nom_solicitante']; ?></td>
                                                    <td><?php echo $uso['nom_registrado']; ?></td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($uso['fec_uso_material'])); ?></td>
                                                    <td>
                                                        <?php if($uso['est_uso_material'] == 1) { ?>
                                                            <span class="badge badge-warning badge_size">Pendiente</span>
                                                        <?php } elseif($uso['est_uso_material'] == 2) { ?>
                                                            <span class="badge badge-success badge_size">Aprobado</span>
                                                        <?php } elseif($uso['est_uso_material'] == 3) { ?>
                                                            <span class="badge badge-success badge_size">Aprobado</span>
                                                        <?php } else { ?>
                                                            <span class="badge badge-danger badge_size">Anulado</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-wrap gap-2">

                                                        <?php
                                                        // Si está anulado o aprobado, bloquear botones de aprobar y anular
                                                        if ($uso['est_uso_material'] == 0 || $uso['est_uso_material'] == 2 || $uso['est_uso_material'] == 3) { ?>
                                                            <a href="#" class="btn btn-outline-secondary btn-sm disabled" title="Verificar" tabindex="-1" aria-disabled="true">
                                                                <i class="fa fa-check"></i>
                                                            </a>
                                                            <a href="#" class="btn btn-outline-secondary btn-sm disabled" title="Anular" tabindex="-1" aria-disabled="true">
                                                                <i class="fa fa-times"></i>
                                                            </a>
                                                            <a href="compras_pdf.php?id=<?php echo $uso['id_uso_material']; ?>"
                                                               class="btn btn-secondary btn-sm"
                                                               title="Generar PDF"
                                                               target="_blank">
                                                                <i class="fa fa-file-pdf-o"></i>
                                                            </a>
                                                        <?php
                                                        } else { ?>
                                                            <a href="#" onclick="AprobarUso(<?php echo $uso['id_uso_material']; ?>)"
                                                               class="btn btn-success btn-sm"
                                                               title="Verificar">
                                                                <i class="fa fa-check"></i>
                                                            </a>
                                                            <a href="#" onclick="AnularUso(<?php echo $uso['id_uso_material']; ?>)"
                                                               class="btn btn-danger btn-sm"
                                                               title="Anular">
                                                                <i class="fa fa-times"></i>
                                                            </a>
                                                            <a href="compras_pdf.php?id=<?php echo $uso['id_uso_material']; ?>"
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
