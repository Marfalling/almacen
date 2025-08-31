<?php 
//=======================================================================
// VISTA: v_personal_mostrar.php
//=======================================================================

?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Personal<small></small></h3>
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
                                <h2>Listado de Personal<small></small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-2">
                                <a href="personal_nuevo.php" class="btn btn-outline-info btn-sm btn-block">Nuevo Personal</a> 
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
                                                <th>DNI</th>
                                                <th>Nombres</th>
                                                <th>Apellidos</th>
                                                <th>Area</th>
                                                <th>Cargo</th>
                                                <th>Email</th>
                                                <th>Teléfono</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            $c = 0;
                                            foreach ($personal as $value) {
                                                $c++;
                                                $id_personal = $value['id_personal'];
                                                $id_area = $value['id_area'];
                                                $id_cargo = $value['id_cargo'];
                                                $nom_personal = $value['nom_personal'];
                                                $ape_personal = $value['ape_personal'];
                                                $dni_personal = $value['dni_personal'];
                                                $email_personal = $value['email_personal'];
                                                $tel_personal = $value['tel_personal'];
                                                $nom_area = $value['nom_area'];
                                                $nom_cargo = $value['nom_cargo'];
                                                $est_personal = $value['est_personal'];
                                                $estado = ($est_personal == 1) ? "ACTIVO" : "INACTIVO";
                                            ?>
                                                <tr>
                                                    <td><?php echo $c; ?></td>
                                                    <td><?php echo $dni_personal; ?></td>
                                                    <td><?php echo $nom_personal; ?></td>
                                                    <td><?php echo $ape_personal; ?></td>
                                                    <td><?php echo $nom_area; ?></td>
                                                    <td><?php echo $nom_cargo; ?></td>
                                                    <td><?php echo $email_personal; ?></td>
                                                    <td><?php echo $tel_personal; ?></td>
                                                    <td>
                                                        <?php if ($est_personal == 1) { ?>
                                                            <span class="badge badge-success"><?php echo $estado; ?></span>
                                                        <?php } else { ?>
                                                            <span class="badge badge-danger"><?php echo $estado; ?></span>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <center>
                                                            <a class="btn btn-warning btn-xs"href="personal_editar.php?id_personal=<?php echo $id_personal; ?>" ><i title="Editar">
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