<?php 
//=======================================================================
// VISTA: v_obras_mostrar.php
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Obras<small></small></h3>
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
                                <h2>Listado de Obras<small></small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-2">
                                 <a href="obras_nuevo.php" class="btn btn-outline-info btn-sm btn-block">Nueva Obra</a>
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
                                            foreach ($obras as $value) {
                                                $c++;
                                                $id_obra = $value['id_obra'];
                                                $nom_obra = $value['nom_obra'];
                                                $est_obra = $value['est_obra'];
                                                $estado = ($est_obra == 1) ? "ACTIVO" : "INACTIVO";
                                            ?>
                                                <tr>
                                                    <td><?php echo $c; ?></td>
                                                    <td><?php echo htmlspecialchars($nom_obra); ?></td>
                                                    <td>
                                                        <?php if ($est_obra == 1) { ?>
                                                            <span class="badge badge-success">ACTIVO</span>
                                                        <?php } else { ?>
                                                            <span class="badge badge-danger">INACTIVO</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <a class="btn btn-warning btn-sm" 
                                                           href="obras_editar.php?id_obra=<?php echo $id_obra; ?>" 
                                                           title="Editar obra">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
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
