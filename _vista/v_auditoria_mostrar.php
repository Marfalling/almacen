<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Auditoria <small></small></h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Listado de Auditoria<small></small></h2>
                        <div class="clearfix"></div>
                    </div>

                    <div class="x_content">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <form class="form-horizontal form-label-left" action="auditoria_mostrar.php" method="post">
                                        <div class="form-group row ">
                                            <label class="control-label col-md-2 col-sm-2 ">Rango de fecha:</label>
                                            <div class="col-md-4 col-sm-4">
                                                <input type="date" name="fecha_inicio" class="form-control" value="<?php echo $fecha_inicio; ?>" required="required">
                                            </div>
                                            <div class="col-md-4 col-sm-4">
                                                <input type="date" name="fecha_fin" class="form-control" value="<?php echo $fecha_fin; ?>" required="required">
                                            </div>
                                            <div class="col-md-2 col-sm-2">
                                                <button type="submit" name="filtrar" class="btn btn-success btn-block">Filtrar</button>
                                            </div>
                                        </div>
                                    </form>

                                    <br>

                                    <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%" data-order='[[4, "desc"]]'>
                                        <thead>
                                            <tr>
                                                <th>Usuario</th>
                                                <th>Acción</th>
                                                <th>Módulo</th>
                                                <th>Descripción</th>
                                                <th>Fecha</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            foreach ($auditoria as  $value) {
                                                $nom_usuario = $value['nom_usuario'];
                                                $accion = $value['accion'];
                                                $modulo = $value['modulo'];
                                                $descripcion = $value['descripcion'];
                                                $fecha = date('d/m/Y H:i:s', strtotime($value['fecha']));
                                            ?>
                                                <tr>
                                                    <td><?php echo $nom_usuario; ?></td>
                                                    <td><?php echo $accion; ?></td>
                                                    <td><?php echo $modulo; ?></td>
                                                    <td><?php echo $descripcion; ?></td>
                                                    <td><?php echo $fecha; ?></td>
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
        </div>
    </div>
</div>
<!-- /page content -->