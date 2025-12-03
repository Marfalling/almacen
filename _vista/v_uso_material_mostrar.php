<?php 
//=======================================================================
// VISTA: v_uso_material_mostrar.php
//=======================================================================

// ========================================================================
// VERIFICAR PERMISOS AL INICIO
// ========================================================================
$tiene_permiso_crear = verificarPermisoEspecifico('crear_uso de material');
$tiene_permiso_editar = verificarPermisoEspecifico('editar_uso de material');
$tiene_permiso_anular = verificarPermisoEspecifico('anular_uso de material');
?>

<script>
function AnularUso(id_uso_material) {
    Swal.fire({
        title: '¿Deseas anular este uso de material?',
        text: "Esta acción no se puede deshacer y el stock será devuelto al almacén.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, anular',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'uso_material_anular.php',
                type: 'POST',
                data: { id_uso_material: id_uso_material },
                dataType: 'json',
                success: function(response) {
                    if (response.tipo_mensaje === 'success') {
                        Swal.fire(
                            '¡Anulado!',
                            response.mensaje,
                            'success'
                        ).then(() => {
                            location.reload();
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
                                <!-- ============================================ -->
                                <!-- BOTÓN NUEVO USO -->
                                <!-- ============================================ -->
                                <?php if (!$tiene_permiso_crear) { ?>
                                    <a href="#" 
                                       class="btn btn-outline-secondary btn-sm btn-block disabled"
                                       title="No tienes permiso para crear uso de material"
                                       tabindex="-1" 
                                       aria-disabled="true">
                                        <i class="fa fa-plus"></i> Nuevo Uso
                                    </a>
                                <?php } else { ?>
                                    <a href="uso_material_nuevo.php" 
                                       class="btn btn-outline-info btn-sm btn-block"
                                       title="Crear nuevo uso de material">
                                        <i class="fa fa-plus"></i> Nuevo Uso
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <div class="x_content">
                        <!-- ========== FILTRO DE FECHAS ========== -->
                        <form method="get" action="uso_material_mostrar.php" class="form-inline mb-3 filtro-fechas">
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
                            <button type="button" class="btn btn-secondary mb-2" onclick="window.location.href='uso_material_mostrar.php'"><i class="bi bi-eraser"></i> Limpiar</button>
                        </form>
                        <!-- ======================================= -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Código Uso</th>
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
                                                    <td><?php echo 'U00' . $uso['id_uso_material']; ?></td>
                                                    <td><?php echo $uso['nom_almacen']; ?></td>
                                                    <td><?php echo $uso['nom_ubicacion']; ?></td>
                                                    <td><?php echo $uso['nom_obra']; ?></td>
                                                    <td><?php echo $uso['nom_cliente']; ?></td>
                                                    <td><?php echo $uso['nom_completo_solicitante']; ?></td>
                                                    <td><?php echo $uso['nom_registrado']; ?></td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($uso['fec_uso_material'])); ?></td>
                                                    <td>  
                                                        <center>
                                                            <?php if($uso['est_uso_material'] == 2) { ?>
                                                                <span class="badge badge-success badge_size">REGISTRADO</span>
                                                            <?php } else { ?>
                                                                <span class="badge badge-danger badge_size">ANULADO</span>
                                                            <?php } ?>
                                                        </center>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            <?php
                                                            // ============================================
                                                            // BOTÓN ANULAR USO DE MATERIAL
                                                            // ============================================
                                                            if (!$tiene_permiso_anular) { ?>
                                                                <a href="#" 
                                                                   class="btn btn-outline-secondary btn-sm disabled"
                                                                   title="No tienes permiso para anular uso de material"
                                                                   tabindex="-1" 
                                                                   aria-disabled="true">
                                                                    <i class="fa fa-times"></i>
                                                                </a>
                                                            <?php } elseif ($uso['est_uso_material'] == 0) { ?>
                                                                <a href="#" 
                                                                   class="btn btn-outline-secondary btn-sm disabled"
                                                                   title="Uso de material ya anulado"
                                                                   tabindex="-1" 
                                                                   aria-disabled="true">
                                                                    <i class="fa fa-times"></i>
                                                                </a>
                                                            <?php } else { ?>
                                                                <a href="#" 
                                                                   onclick="AnularUso(<?php echo $uso['id_uso_material']; ?>)"
                                                                   class="btn btn-danger btn-sm"
                                                                   title="Anular uso de material">
                                                                    <i class="fa fa-times"></i>
                                                                </a>
                                                            <?php } ?>

                                                            <?php
                                                            // ============================================
                                                            // BOTÓN EDITAR USO DE MATERIAL
                                                            // ============================================
                                                            if (!$tiene_permiso_editar) { ?>
                                                                <a href="#" 
                                                                   class="btn btn-outline-secondary btn-sm disabled"
                                                                   title="No tienes permiso para editar uso de material"
                                                                   tabindex="-1" 
                                                                   aria-disabled="true">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                            <?php } elseif ($uso['est_uso_material'] == 0) { ?>
                                                                <a href="#" 
                                                                   class="btn btn-outline-secondary btn-sm disabled"
                                                                   title="No se puede editar - Uso de material anulado"
                                                                   tabindex="-1" 
                                                                   aria-disabled="true">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                            <?php } else { ?>
                                                                <a href="uso_material_editar.php?id=<?php echo $uso['id_uso_material']; ?>"
                                                                   class="btn btn-warning btn-sm"
                                                                   title="Editar uso de material">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                            <?php } ?>

                                                            <!-- Botón PDF - siempre visible -->
                                                            <a href="uso_material_pdf.php?id=<?php echo $uso['id_uso_material']; ?>"
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

<script>
$(document).ready(function() {
    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip({
        placement: 'top',
        trigger: 'hover'
    });

});
</script>