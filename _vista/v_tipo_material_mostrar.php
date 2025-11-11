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
                                <a href="tipo_material_nuevo.php" class="btn btn-outline-info btn-sm btn-block">
                                    <i class="fa fa-plus"></i> Nuevo Tipo Material
                                </a> 
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
                                                        <center><a class="btn btn-warning" href="tipo_material_editar.php?id_material_tipo=<?php echo $id_material_tipo; ?>"><i class="fa fa-edit"></i></a></center>
                                                    </td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
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