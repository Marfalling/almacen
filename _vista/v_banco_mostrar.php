<?php
//=======================================================================
// VISTA: v_banco_mostrar.php
//=======================================================================

// ========================================================================
// VERIFICAR PERMISOS AL INICIO
// ========================================================================
$tiene_permiso_crear = verificarPermisoEspecifico('crear_banco');
$tiene_permiso_editar = verificarPermisoEspecifico('editar_banco');
?>

<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Banco<small></small></h3>
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
                                <h2>Listado de Bancos<small></small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-2">
                                <!-- ============================================ -->
                                <!-- BOTÓN NUEVO BANCO -->
                                <!-- ============================================ -->
                                <?php if (!$tiene_permiso_crear) { ?>
                                    <a href="#" 
                                       class="btn btn-outline-secondary btn-sm btn-block disabled"
                                       title="No tienes permiso para crear bancos"
                                       tabindex="-1" 
                                       aria-disabled="true">
                                        <i class="fa fa-plus"></i> Nuevo Banco
                                    </a>
                                <?php } else { ?>
                                    <a href="banco_nuevo.php" 
                                       class="btn btn-outline-info btn-sm btn-block"
                                       title="Crear nuevo banco">
                                        <i class="fa fa-plus"></i> Nuevo Banco
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
                                                <th>Código</th>
                                                <th>Nombre</th>
                                                <th>Estado</th>
                                                <th>Editar</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            $c = 0;
                                            foreach ($banco as $value) {
                                                $c++;
                                                $id_banco = $value['id_banco'];
                                                $cod_banco = $value['cod_banco'];
                                                $nom_banco = $value['nom_banco'];
                                                $est_banco = $value['est_banco'];
                                                $estado_texto = ($est_banco == 1) ? "Activo" : "Inactivo";
                                            ?>
                                                <tr>
                                                    <td><?php echo $c; ?></td>
                                                    <td><?php echo $cod_banco; ?></td>
                                                    <td><?php echo $nom_banco; ?></td>
                                                    <td>
                                                        <center>
                                                            <?php if ($est_banco == 1) { ?>
                                                                <span class="badge badge-success badge_size">ACTIVO</span>
                                                            <?php } else { ?>
                                                                <span class="badge badge-danger badge_size">INACTIVO</span>
                                                            <?php } ?>
                                                        </center>
                                                    </td>
                                                    <td class="text-center">
                                                        <!-- ============================================ -->
                                                        <!-- BOTÓN EDITAR BANCO -->
                                                        <!-- ============================================ -->
                                                        <?php if (!$tiene_permiso_editar) { ?>
                                                            <span data-toggle="tooltip" title="No tienes permiso para editar bancos">
                                                                <a href="#" 
                                                                class="btn btn-outline-secondary btn-sm disabled"
                                                                tabindex="-1" 
                                                                aria-disabled="true">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                            </span>
                                                        <?php } else { ?>
                                                            <a class="btn btn-warning btn-sm" 
                                                               href="banco_editar.php?id_banco=<?php echo $id_banco; ?>"
                                                               data-toggle="tooltip"
                                                               data-placement="top"
                                                               title="Editar banco">
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