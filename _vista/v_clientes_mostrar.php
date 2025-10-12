<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Clientes<small></small></h3>
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
                                <h2>Listado de Clientes<small></small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-2">
                                <a href="clientes_nuevo.php" class="btn btn-outline-info btn-sm btn-block">Nuevo cliente</a>
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
                                            foreach ($clientes as $value) {
                                                $c++;
                                                $id_cliente = $value['id_cliente'];
                                                $nom_cliente = $value['nom_cliente'];
                                                $est_cliente = $value['est_cliente'];
                                                $origen = $value['origen'];
                                                $estado = ($est_cliente == 1) ? "ACTIVO" : "INACTIVO";
                                            ?>
                                                <tr>
                                                    <td><?php echo $c; ?></td>
                                                    <td><?php echo $nom_cliente; ?></td>
                                                    <td><?php echo $estado; ?></td>
                                                    <td>
                                                        <center>
                                                           <a class="btn btn-warning" href="clientes_editar.php?id_cliente=<?php echo $id_cliente; ?>"><i class="fa fa-edit"></i></a>
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