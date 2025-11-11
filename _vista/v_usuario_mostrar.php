<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Usuario<small></small></h3>
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
                                <h2>Listado de Usuario<small></small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-2">
                                <a href="usuario_nuevo.php" class="btn btn-outline-info btn-sm btn-block">
                                    <i class="fa fa-plus"></i> Nuevo Usuario
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
                                                <th>Personal</th>
                                                <th>Usuario</th>
                                                <th>Área</th>
                                                <th>Cargo</th>
                                                <th>Roles</th>
                                                <th>Estado</th>
                                                <th>Editar</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            $c = 0;
                                            foreach ($usuarios as $value) {
                                                $c++;
                                                $id_usuario = $value['id_usuario'];
                                                $nombre_completo = $value['nom_personal'];
                                                $dni = $value['dni_personal'];
                                                $usu_usuario = $value['usu_usuario'];
                                                $nom_area = $value['nom_area'];
                                                $nom_cargo = $value['nom_cargo'];
                                                $roles = !empty($value['roles']) ? $value['roles'] : 'Sin roles';
                                                $est_usuario = $value['est_usuario'];
                                                $estado = ($est_usuario == 1) ? "ACTIVO" : "INACTIVO";
                                                $estado_class = ($est_usuario == 1) ? "badge badge-success" : "badge badge-danger";
                                            ?>
                                                <tr>
                                                    <td><?php echo $c; ?></td>
                                                    <td>
                                                        <strong><?php echo $nombre_completo; ?></strong><br>
                                                        <small>DNI: <?php echo $dni; ?></small>
                                                    </td>
                                                    <td><?php echo $usu_usuario; ?></td>
                                                    <td><?php echo $nom_area; ?></td>
                                                    <td><?php echo $nom_cargo; ?></td>
                                                    <td>
                                                        <small><?php echo $roles; ?></small>
                                                    </td>
                                                    <td>
                                                        <center>
                                                            <?php if ($est_usuario == 1) { ?>
                                                                <span class="badge badge-success badge_size"><?php echo $estado; ?></span>
                                                            <?php } else { ?>
                                                                <span class="badge badge-danger badge_size"><?php echo $estado; ?></span>
                                                            <?php } ?>
                                                        </center>
                                                    </td>
                                                    <td>
                                                        <center>
                                                            <a class="btn btn-warning btn-sm" href="usuario_editar.php?id_usuario=<?php echo $id_usuario; ?>"><i  title="Editar usuario">
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