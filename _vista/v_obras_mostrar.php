<?php
//=======================================================================
// VISTA: v_obras_mostrar.php
//=======================================================================

// ========================================================================
// VERIFICAR PERMISOS AL INICIO
// ========================================================================
$tiene_permiso_crear = verificarPermisoEspecifico('crear_obras');
$tiene_permiso_editar = verificarPermisoEspecifico('editar_obras');
?>

<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Listado de Obras </h3>
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
                                <h2>Obras Registradas<small></small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-2">
                                <!-- ============================================ -->
                                <!-- BOTÓN NUEVA OBRA -->
                                <!-- ============================================ -->
                                <?php if (!$tiene_permiso_crear) { ?>
                                    <a href="#" 
                                       class="btn btn-outline-success btn-sm btn-block disabled"
                                       title="No tienes permiso para crear obras"
                                       tabindex="-1" 
                                       aria-disabled="true">
                                        <i class="fa fa-plus"></i> Nueva Obra
                                    </a>
                                <?php } else { ?>
                                    <a href="obras_nuevo.php" 
                                       class="btn btn-outline-info btn-sm btn-block"
                                       title="Crear nueva obra">
                                        <i class="fa fa-plus"></i> Nueva Obra
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <div class="x_content">
                        <?php if (!empty($obras)) { ?>
                        <div class="table-responsive">
                            <table id="datatable-buttons" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nombre</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($obras as $index => $obra) { ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($obra['nom_subestacion']); ?></td>
                                        <td>
                                            <center>
                                                <?php if ($obra['act_subestacion'] == 1) { ?>
                                                    <span class="badge badge-success badge_size">ACTIVO</span>
                                                <?php } else { ?>
                                                    <span class="badge badge-danger badge_size">INACTIVO</span>
                                                <?php } ?>
                                            </center>
                                        </td>
                                        <td class="text-center">
                                            <!-- ============================================ -->
                                            <!-- BOTÓN EDITAR OBRA -->
                                            <!-- ============================================ -->
                                            <?php if (!$tiene_permiso_editar) { ?>
                                                <span data-toggle="tooltip" title="No tienes permiso para editar obras">
                                                    <a href="#"
                                                    class="btn btn-outline-success btn-sm disabled"
                                                    tabindex="-1"
                                                    aria-disabled="true">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                </span>
                                            <?php } else { ?>
                                                <a href="obras_editar.php?id_obra=<?php echo $obra['id_subestacion']; ?>" 
                                                   class="btn btn-warning btn-sm"
                                                   data-toggle="tooltip"
                                                   data-placement="top"
                                                   title="Editar obra">
                                                    <i class="fa fa-edit"></i> 
                                                </a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php } else { ?>
                            <p>No hay obras registradas.</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts de DataTables con exportación y búsqueda -->
<script>
    $(document).ready(function() {
        var table = $('#datatable-buttons').DataTable({
            dom: "Bfrtip",
            buttons: [
                { extend: "copy", className: "btn-sm" },
                { extend: "csv", className: "btn-sm" },
                { extend: "excel", className: "btn-sm" },
                { extend: "pdf", className: "btn-sm" },
                { extend: "print", className: "btn-sm" }
            ],
            responsive: true
        });
    });

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

</script>