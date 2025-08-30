<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Proveedor<small></small></h3>
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
                                <h2>Listado de Proveedor<small></small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-2">
                                
                                 <a href="proveedor_nuevo.php" class="btn btn-outline-info btn-sm btn-block">Nuevo proveedor</a>
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
                                                <th>RUC</th>
                                                <th>Dirección</th>
                                                <th>Telefono</th>
                                                <th>Contacto</th>
                                                <th>Estado</th>
                                                <th>Editar</th> 
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            $c = 0;
                                            foreach ($proveedor as  $value) {
                                                $c++;
                                                $id_proveedor = $value['id_proveedor'];
                                                $nom_proveedor = $value['nom_proveedor'];
                                                $ruc_proveedor = $value['ruc_proveedor'];
                                                $dir_proveedor = $value['dir_proveedor'];
                                                $tel_proveedor = $value['tel_proveedor'];
                                                $cont_proveedor = $value['cont_proveedor'];
                                                $est_proveedor = $value['est_proveedor'];
                                                $estado = ($est_proveedor == 1) ? "ACTIVO" : "INACTIVO";
                                                
                                            ?>
                                                <tr>
                                                    <td><?php echo $c; ?></td>
                                                    <td><?php echo $nom_proveedor; ?></td>
                                                    <td><?php echo $ruc_proveedor; ?></td>
                                                    <td><?php echo $dir_proveedor; ?></td>
                                                    <td><?php echo $tel_proveedor; ?></td>
                                                    <td><?php echo $cont_proveedor; ?></td>
                                                    <td><?php echo $estado; ?></td>
                                                    <td>
                                                        <center><a class="btn btn-warning" href=""><i class="fa fa-edit"></i></a></center>
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