<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Roles de Usuario<small></small></h3>
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
                                <h2>Listado de Roles de Usuario<small></small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-2">
                                <a href="rol_usuario_nuevo.php" class="btn btn-outline-info btn-sm btn-block">Nuevo rol de usuario</a> 
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
                                                <th>Nombre del Rol</th>
                                                <th>Total Permisos</th>
                                                <th>Usuarios Asignados</th>
                                                <th>Estado</th>
                                                <th>Editar</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            $c = 0;
                                            foreach ($roles as $value) {
                                                $c++;
                                                $id_rol = $value['id_rol'];
                                                $nom_rol = $value['nom_rol'];
                                                $total_permisos = $value['total_permisos'];
                                                $total_usuarios = $value['total_usuarios'];
                                                $est_rol = $value['est_rol'];
                                                $estado = ($est_rol == 1) ? "ACTIVO" : "INACTIVO";
                                                
                                            ?>
                                                <tr>
                                                    <td><?php echo $c; ?></td>
                                                    <td><?php echo $nom_rol; ?></td>
                                                    <td>
                                                        <center><?php echo $total_permisos; ?></center>
                                                    </td>
                                                    <td>
                                                        <center><?php echo $total_usuarios; ?></center>
                                                    </td>
                                                    <td>
                                                        <center><?php echo $estado; ?></center>
                                                    </td>
                                                    <td>
                                                        <center>
                                                            <a class="btn btn-warning btn-sm" href="rol_usuario_editar.php?id_rol=<?php echo $id_rol; ?>">
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