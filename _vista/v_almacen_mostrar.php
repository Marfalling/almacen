<?php
// Vista para mostrar almacenes - v_almacen_mostrar.php
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Almacén <small></small></h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <!-- --------------------------------------- -->
            <div class="col-md-12 col-sm-12">
                <div class="x_panel">
                    <div class="x_title">
                        <div class="row">
                            <div class="col-sm-10">
                                <h2>Listado de Almacenes <small></small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-2">
                                <a href="almacen_nuevo.php" class="btn btn-outline-info btn-sm btn-block">Nuevo almacén</a>
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
                                                <th>Cliente</th>
                                                <th>Obra</th>
                                                <th>Nombre Almacén</th>
                                                <th>Estado</th>
                                                <th>Editar</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            $c = 0;
                                            if ($almacen && count($almacen) > 0) {
                                                foreach ($almacen as $value) {
                                                    $c++;
                                                    $id_almacen = $value['id_almacen'];
                                                    $nom_cliente = $value['nom_cliente'];
                                                    $nom_obra = $value['nom_obra'];
                                                    $nom_almacen = $value['nom_almacen'];
                                                    $est_almacen = $value['est_almacen'];
                                                    $estado = ($est_almacen == 1) ? "ACTIVO" : "INACTIVO";
                                            ?>
                                                    <tr>
                                                        <td><?php echo $c; ?></td>
                                                        <td><?php echo htmlspecialchars($nom_cliente); ?></td>
                                                        <td><?php echo htmlspecialchars($nom_obra); ?></td>
                                                        <td><?php echo htmlspecialchars($nom_almacen); ?></td>
                                                        <td><?php echo $estado; ?></td>
                                                        <td>
                                                            <center>
                                                                <a class="btn btn-warning btn-sm" href="almacen_editar.php?id_almacen=<?php echo $id_almacen; ?>" title="Editar almacén">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                            </center>
                                                        </td>
                                                    </tr>
                                            <?php
                                                }
                                            } else {
                                            ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">No hay almacenes registrados</td>
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