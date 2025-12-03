<?php
//=======================================================================
// VISTA: v_moneda_mostrar.php
//=======================================================================

// ========================================================================
// VERIFICAR PERMISOS AL INICIO
// ========================================================================
$tiene_permiso_crear = verificarPermisoEspecifico('crear_moneda');
$tiene_permiso_editar = verificarPermisoEspecifico('editar_moneda');
?>

<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Moneda<small></small></h3>
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
                                <h2>Listado de Moneda<small></small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-2">
                                <!-- ============================================ -->
                                <!-- BOTÓN NUEVA MONEDA -->
                                <!-- ============================================ -->
                                <?php if (!$tiene_permiso_crear) { ?>
                                    <a href="#" 
                                       class="btn btn-outline-secondary btn-sm btn-block disabled"
                                       title="No tienes permiso para crear monedas"
                                       tabindex="-1" 
                                       aria-disabled="true">
                                        <i class="fa fa-plus"></i> Nueva Moneda
                                    </a>
                                <?php } else { ?>
                                    <a href="moneda_nuevo.php" 
                                       class="btn btn-outline-info btn-sm btn-block"
                                       title="Crear nueva moneda">
                                        <i class="fa fa-plus"></i> Nueva Moneda
                                    </a>
                                <?php } ?>
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
                                                <th>Nombre</th>
                                                <th>Estado</th>
                                                <th>Editar</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            $c = 0;
                                            foreach ($moneda as $value) {
                                                $c++;
                                                $id_moneda = $value['id_moneda'];
                                                $nom_moneda = $value['nom_moneda'];
                                                $est_moneda = $value['est_moneda'];
                                                $estado_texto = ($est_moneda == 1) ? "Activo" : "Inactivo";
                                            ?>
                                                <tr>
                                                    <td><?php echo $c; ?></td>
                                                    <td><?php echo $nom_moneda; ?></td>
                                                    <td>
                                                        <center>
                                                            <?php if ($est_moneda == 1) { ?>
                                                                <span class="badge badge-success badge_size">ACTIVO</span>
                                                            <?php } else { ?>
                                                                <span class="badge badge-danger badge_size">INACTIVO</span>
                                                            <?php } ?>
                                                        </center>
                                                    </td>
                                                    <td class="text-center">
                                                        <!-- ============================================ -->
                                                        <!-- BOTÓN EDITAR MONEDA -->
                                                        <!-- ============================================ -->
                                                        <?php if (!$tiene_permiso_editar) { ?>
                                                            <span data-toggle="tooltip" title="No tienes permiso para editar monedas">
                                                                <a href="#"
                                                                class="btn btn-outline-secondary btn-sm disabled"
                                                                tabindex="-1"
                                                                aria-disabled="true">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                            </span>
                                                        <?php } else { ?>
                                                            <a class="btn btn-warning btn-sm" 
                                                               href="moneda_editar.php?id_moneda=<?php echo $id_moneda; ?>"
                                                               data-toggle="tooltip"
                                                               data-placement="top"
                                                               title="Editar moneda">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php
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

$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});

</script>