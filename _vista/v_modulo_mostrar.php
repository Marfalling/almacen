<?php
//=======================================================================
// VISTA: v_modulo_mostrar.php
//=======================================================================

// ========================================================================
// VERIFICAR PERMISOS AL INICIO
// ========================================================================
$tiene_permiso_crear = verificarPermisoEspecifico('crear_modulos');
$tiene_permiso_editar = verificarPermisoEspecifico('editar_modulos');
?>

<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Módulos<small></small></h3>
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
                                <h2>Listado de Módulos<small></small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-2">
                                <!-- ============================================ -->
                                <!-- BOTÓN NUEVO MÓDULO -->
                                <!-- ============================================ -->
                                <?php if (!$tiene_permiso_crear) { ?>
                                    <a href="#" 
                                       class="btn btn-outline-secondary btn-sm btn-block disabled"
                                       title="No tienes permiso para crear módulos"
                                       tabindex="-1" 
                                       aria-disabled="true">
                                        <i class="fa fa-plus"></i> Nuevo Módulo
                                    </a>
                                <?php } else { ?>
                                    <a href="modulo_nuevo.php" 
                                       class="btn btn-outline-info btn-sm btn-block"
                                       title="Crear nuevo módulo">
                                        <i class="fa fa-plus"></i> Nuevo Módulo
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
                                                <th>Nombre del Módulo</th>
                                                <th>Total Acciones</th>
                                                <th>Estado</th>
                                                <th>Editar</th> 
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            $c = 0;
                                            foreach ($modulo as $value) {
                                                $c++;
                                                $id_modulo = $value['id_modulo'];
                                                $nom_modulo = $value['nom_modulo'];
                                                $est_modulo = $value['est_modulo'];
                                                $total_acciones = $value['total_acciones'];
                                                $estado = ($est_modulo == 1) ? "ACTIVO" : "INACTIVO";
                                                $clase_estado = ($est_modulo == 1) ? "success" : "danger";
                                            ?>
                                                <tr>
                                                    <td><?php echo $c; ?></td>
                                                    <td>
                                                        <strong><?php echo $nom_modulo; ?></strong>
                                                    </td>
                                                    <td>
                                                        <span><?php echo $total_acciones; ?> acciones</span>
                                                    </td>
                                                    <td>
                                                        <center>
                                                            <?php if ($est_modulo == 1) { ?>
                                                                <span class="badge badge-success badge_size">ACTIVO</span>
                                                            <?php } else { ?>
                                                                <span class="badge badge-danger badge_size">INACTIVO</span>
                                                            <?php } ?>
                                                        </center>
                                                    </td>
                                                    <td>
                                                        <center>
                                                            <!-- ============================================ -->
                                                            <!-- BOTÓN EDITAR MÓDULO -->
                                                            <!-- ============================================ -->
                                                            <?php if (!$tiene_permiso_editar) { ?>
                                                                <span data-toggle="tooltip" title="No tienes permiso para editar módulos">
                                                                    <a href="#" 
                                                                    class="btn btn-outline-secondary btn-sm disabled"
                                                                    tabindex="-1" 
                                                                    aria-disabled="true">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                </span>
                                                            <?php } else { ?>
                                                                <a class="btn btn-warning btn-sm" 
                                                                   href="modulo_editar.php?id_modulo=<?php echo $id_modulo; ?>"
                                                                   data-toggle="tooltip"
                                                                   data-placement="top" 
                                                                   title="Editar módulo">
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