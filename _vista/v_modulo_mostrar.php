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
                                <a href="modulo_nuevo.php" class="btn btn-outline-info btn-sm btn-block">Nuevo módulo</a>
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
                                                        <span ><?php echo $total_acciones; ?> acciones</span>
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
                                                            <a class="btn btn-warning btn-sm" href="modulo_editar.php?id_modulo=<?php echo $id_modulo; ?>" title="Editar">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
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