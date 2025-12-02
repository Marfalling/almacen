<?php
//=======================================================================
// VISTA: v_medio_pago_mostrar.php
//=======================================================================

// ========================================================================
// VERIFICAR PERMISOS AL INICIO
// ========================================================================
$tiene_permiso_crear = verificarPermisoEspecifico('crear_medio de pago');
$tiene_permiso_editar = verificarPermisoEspecifico('editar_medio de pago');
?>

<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Medio Pago<small></small></h3>
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
                                <h2>Listado de Medio Pago<small></small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-2">
                                <!-- ============================================ -->
                                <!-- BOTÓN NUEVO MEDIO PAGO -->
                                <!-- ============================================ -->
                                <?php if (!$tiene_permiso_crear) { ?>
                                    <a href="#" 
                                       class="btn btn-outline-danger btn-sm btn-block disabled"
                                       title="No tienes permiso para crear medios de pago"
                                       tabindex="-1" 
                                       aria-disabled="true">
                                        <i class="fa fa-plus"></i> Nuevo Medio Pago
                                    </a>
                                <?php } else { ?>
                                    <a href="medio_pago_nuevo.php" 
                                       class="btn btn-outline-info btn-sm btn-block"
                                       title="Crear nuevo medio de pago">
                                        <i class="fa fa-plus"></i> Nuevo Medio Pago
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
                                            foreach ($medio_pago as $value) {
                                                $c++;
                                                $id_medio_pago = $value['id_medio_pago'];
                                                $nom_medio_pago = $value['nom_medio_pago'];
                                                $est_medio_pago = $value['est_medio_pago'];
                                                $estado_texto = ($est_medio_pago == 1) ? "Activo" : "Inactivo";
                                            ?>
                                                <tr>
                                                    <td><?php echo $c; ?></td>
                                                    <td><?php echo $nom_medio_pago; ?></td>
                                                    <td>
                                                        <center>
                                                            <?php if ($est_medio_pago == 1) { ?>
                                                                <span class="badge badge-success badge_size">ACTIVO</span>
                                                            <?php } else { ?>
                                                                <span class="badge badge-danger badge_size">INACTIVO</span>
                                                            <?php } ?>
                                                        </center>
                                                    </td>
                                                    <td class="text-center">
                                                        <!-- ============================================ -->
                                                        <!-- BOTÓN EDITAR MEDIO PAGO -->
                                                        <!-- ============================================ -->
                                                        <?php if (!$tiene_permiso_editar) { ?>
                                                            <a href="#" 
                                                               class="btn btn-outline-danger btn-sm disabled"
                                                               title="No tienes permiso para editar medios de pago"
                                                               tabindex="-1" 
                                                               aria-disabled="true">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a class="btn btn-warning btn-sm" 
                                                               href="medio_pago_editar.php?id_medio_pago=<?php echo $id_medio_pago; ?>"
                                                               title="Editar medio de pago">
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