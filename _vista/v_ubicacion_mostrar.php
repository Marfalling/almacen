<?php
//=======================================================================
// VISTA: v_ubicacion_mostrar.php
//=======================================================================

// ========================================================================
// VERIFICAR PERMISOS AL INICIO
// ========================================================================
$tiene_permiso_crear = verificarPermisoEspecifico('crear_ubicacion');
$tiene_permiso_editar = verificarPermisoEspecifico('editar_ubicacion');
?>

<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Ubicación<small></small></h3>
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
                                <h2>Listado de Ubicación<small></small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-2">
                                <!-- ============================================ -->
                                <!-- BOTÓN NUEVA UBICACIÓN -->
                                <!-- ============================================ -->
                                <?php if (!$tiene_permiso_crear) { ?>
                                    <a href="#" 
                                       class="btn btn-outline-success btn-sm btn-block disabled"
                                       title="No tienes permiso para crear ubicaciones"
                                       tabindex="-1" 
                                       aria-disabled="true">
                                        <i class="fa fa-plus"></i> Nueva Ubicación
                                    </a>
                                <?php } else { ?>
                                    <a href="ubicacion_nuevo.php" 
                                       class="btn btn-outline-info btn-sm btn-block"
                                       title="Crear nueva ubicación">
                                        <i class="fa fa-plus"></i> Nueva Ubicación
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
                                            foreach ($ubicacion as  $value) {
                                                $c++;
                                                $id_ubicacion = $value['id_ubicacion'];
                                                $nom_ubicacion = $value['nom_ubicacion'];
                                                $est_ubicacion = $value['est_ubicacion'];
                                                $estado = ($est_ubicacion == 1) ? "ACTIVO" : "INACTIVO";
                                            ?>
                                                <tr>
                                                    <td><?php echo $c; ?></td>
                                                    <td><?php echo $nom_ubicacion; ?></td>
                                                    <td>
                                                        <center>
                                                            <?php if ($est_ubicacion == 1) { ?>
                                                                <span class="badge badge-success badge_size">ACTIVO</span>
                                                            <?php } else { ?>
                                                                <span class="badge badge-danger badge_size">INACTIVO</span>
                                                            <?php } ?>
                                                        </center>
                                                    </td>
                                                    <td>
                                                        <center>
                                                            <!-- ============================================ -->
                                                            <!-- BOTÓN EDITAR UBICACIÓN -->
                                                            <!-- ============================================ -->
                                                            <?php if (!$tiene_permiso_editar) { ?>
                                                                <span data-toggle="tooltip" title="No tienes permiso para editar ubicaciones">
                                                                    <a href="#"
                                                                    class="btn btn-outline-success btn-sm disabled"
                                                                    tabindex="-1"
                                                                    aria-disabled="true">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                </span>
                                                            <?php } else { ?>
                                                                <a class="btn btn-warning btn-sm" 
                                                                   href="ubicacion_editar.php?id_ubicacion=<?php echo $id_ubicacion; ?>"
                                                                   data-toggle="tooltip"
                                                                   data-placement="top"
                                                                   title="Editar ubicación">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                            <?php } ?>
                                                        </center>
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