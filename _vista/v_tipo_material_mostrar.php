<?php
//=======================================================================
// VISTA: v_tipo_material_mostrar.php
//=======================================================================

// ========================================================================
// VERIFICAR PERMISOS AL INICIO
// ========================================================================
$tiene_permiso_crear = verificarPermisoEspecifico('crear_tipo de material');
$tiene_permiso_editar = verificarPermisoEspecifico('editar_tipo de material');
?>

<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Tipo de Material<small></small></h3>
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
                                <h2>Listado de tipo de Material<small></small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-2">
                                <!-- ============================================ -->
                                <!-- BOTÓN NUEVO TIPO MATERIAL -->
                                <!-- ============================================ -->
                                <?php if (!$tiene_permiso_crear) { ?>
                                    <a href="#" 
                                       class="btn btn-outline-secondary btn-sm btn-block disabled"
                                       title="No tienes permiso para crear tipos de material"
                                       tabindex="-1" 
                                       aria-disabled="true">
                                        <i class="fa fa-plus"></i> Nuevo Tipo Material
                                    </a>
                                <?php } else { ?>
                                    <a href="tipo_material_nuevo.php" 
                                       class="btn btn-outline-info btn-sm btn-block"
                                       title="Crear nuevo tipo de material">
                                        <i class="fa fa-plus"></i> Nuevo Tipo Material
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
                                            foreach ($material_tipo as  $value) {
                                                $c++;
                                                $id_material_tipo = $value['id_material_tipo'];
                                                $nom_material_tipo = $value['nom_material_tipo'];
                                                $est_material_tipo = $value['est_material_tipo'];
                                                $estado = ($est_material_tipo == 1) ? "ACTIVO" : "INACTIVO";
                                            ?>
                                                <tr>
                                                    <td><?php echo $c; ?></td>
                                                    <td><?php echo $nom_material_tipo; ?></td>
                                                    <td>
                                                        <center>
                                                            <?php if ($est_material_tipo == 1) { ?>
                                                                <span class="badge badge-success badge_size">ACTIVO</span>
                                                            <?php } else { ?>
                                                                <span class="badge badge-danger badge_size">INACTIVO</span>
                                                            <?php } ?>
                                                        </center>
                                                    </td>
                                                    <td>
                                                        <center>
                                                            <!-- ============================================ -->
                                                            <!-- BOTÓN EDITAR TIPO MATERIAL -->
                                                            <!-- ============================================ -->
                                                            <?php if (!$tiene_permiso_editar) { ?>
                                                                <span data-toggle="tooltip" title="No tienes permiso para editar tipos de material">
                                                                    <a href="#"
                                                                    class="btn btn-outline-secondary btn-sm disabled"
                                                                    tabindex="-1"
                                                                    aria-disabled="true">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                </span>
                                                            <?php } else { ?>
                                                                <a class="btn btn-warning btn-sm" 
                                                                   href="tipo_material_editar.php?id_material_tipo=<?php echo $id_material_tipo; ?>"
                                                                   data-toggle="tooltip"
                                                                   data-placement="top"
                                                                   title="Editar tipo de material">
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