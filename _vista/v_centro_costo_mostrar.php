<?php 
//=======================================================================
// VISTA: v_centro_costo_mostrar.php 
//=======================================================================

// ========================================================================
// VERIFICAR PERMISOS AL INICIO
// ========================================================================
$tiene_permiso_crear = verificarPermisoEspecifico('crear_centro de costo');
$tiene_permiso_editar = verificarPermisoEspecifico('editar_centro de costo');
?>

<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Centro de Costos <small></small></h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <?php if (isset($_GET['registrado'])): ?>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Centro de Costo registrado correctamente',
                    showConfirmButton: false,
                    timer: 2000
                });
            </script>
        <?php elseif (isset($_GET['actualizado'])): ?>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Centro de Costo actualizado correctamente',
                    showConfirmButton: false,
                    timer: 2000
                });
            </script>
        <?php elseif (isset($_GET['error'])): ?>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo completar la acción',
                    showConfirmButton: true
                });
            </script>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <div class="row">
                            <div class="col-sm-10">
                                <h2>Listado de Centro de Costo</h2>
                            </div>
                            <div class="col-sm-2">
                                <!-- ============================================ -->
                                <!-- BOTÓN NUEVO CENTRO COSTO -->
                                <!-- ============================================ -->
                                <?php if (!$tiene_permiso_crear) { ?>
                                    <a href="#" 
                                       class="btn btn-outline-danger btn-sm btn-block disabled"
                                       title="No tienes permiso para crear centros de costo"
                                       tabindex="-1" 
                                       aria-disabled="true">
                                        <i class="fa fa-plus"></i> Nuevo Centro Costo
                                    </a>
                                <?php } else { ?>
                                    <a href="centro_costo_nuevo.php" 
                                       class="btn btn-outline-info btn-sm btn-block"
                                       title="Crear nuevo centro de costo">
                                       <i class="fa fa-plus"></i> Nuevo Centro Costo
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="x_content">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Nombre</th>
                                                <th class="text-center">Estado</th>
                                                <th class="text-center">Editar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $c = 0;
                                            if (!empty($centros)) {
                                                foreach ($centros as $value) {
                                                    $c++;
                                                    $id = $value['id_centro_costo'];
                                                    $nom = $value['nom_centro_costo'];
                                                    $estado = $value['est_centro_costo'];
                                            ?>
                                                <tr>
                                                    <td><?php echo $c; ?></td>
                                                    <td><?php echo $nom; ?></td>
                                                    <td>
                                                        <center>
                                                            <?php if ($estado == 1) { ?>
                                                                <span class="badge badge-success badge_size">ACTIVO</span>
                                                            <?php } else { ?>
                                                                <span class="badge badge-danger badge_size">INACTIVO</span>
                                                            <?php } ?>
                                                        </center>
                                                    </td>
                                                    <td class="text-center">
                                                        <!-- ============================================ -->
                                                        <!-- BOTÓN EDITAR CENTRO COSTO -->
                                                        <!-- ============================================ -->
                                                        <?php if (!$tiene_permiso_editar) { ?>
                                                            <span data-toggle="tooltip" data-placement="top" title="No tienes permiso para editar centros de costo">
                                                                <a href="#" 
                                                                class="btn btn-outline-danger btn-sm disabled"
                                                                tabindex="-1" 
                                                                aria-disabled="true">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                            </span>
                                                        <?php } else { ?>
                                                            <a class="btn btn-warning btn-sm" 
                                                               href="centro_costo_editar.php?id_centro_costo=<?php echo $id; ?>"
                                                               data-toggle="tooltip"
                                                               data-placement="top"
                                                               title="Editar centro de costo">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php
                                                }
                                            } else {
                                            ?>
                                                <tr>
                                                    <td colspan="4" class="text-center">No hay registros disponibles</td>
                                                </tr>
                                            <?php } ?>
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

<script>

$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});

</script>