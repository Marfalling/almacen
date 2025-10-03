<?php 
//=======================================================================
// VISTA: v_detraccion_mostrar.php
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Detracción <small></small></h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <!-- Mensajes de alerta -->
        <?php if (isset($_GET['registrado'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Éxito!</strong> Detracción registrada correctamente.
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['actualizado'])): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <strong>Éxito!</strong> Detracción actualizada correctamente.
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['eliminado'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Eliminado!</strong> Detracción eliminada correctamente.
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Error!</strong> No se pudo completar la acción.
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- --------------------------------------- -->
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <div class="row">
                            <div class="col-sm-10">
                                <h2>Listado de Detracciones <small></small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-2">
                                <?php if (verificarPermisoEspecifico('crear_detraccion')): ?>
                                    <a href="detraccion_nuevo.php" class="btn btn-outline-info btn-sm btn-block">Nueva detracción</a>
                                <?php endif; ?>
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
                                                <th>Porcentaje (%)</th>
                                                <th>Editar</th>
                                                <th>Eliminar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $c = 0;
                                            if (!empty($detraccion)) {
                                                foreach ($detraccion as $value) {
                                                    $c++;
                                                    $id = $value['id_detraccion'];
                                                    $nom = $value['nombre_detraccion'];
                                                    $porcentaje = $value['porcentaje'];
                                            ?>
                                                <tr>
                                                    <td><?php echo $c; ?></td>
                                                    <td><?php echo $nom; ?></td>
                                                    <td><?php echo number_format($porcentaje, 2); ?> %</td>
                                                    <td>
                                                        <?php if (verificarPermisoEspecifico('editar_detraccion')): ?>
                                                        <center>
                                                            <a class="btn btn-warning btn-sm" href="detraccion_editar.php?id_detraccion=<?php echo $id; ?>"><i class="fa fa-edit"></i></a>
                                                        </center>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if (verificarPermisoEspecifico('eliminar_detraccion')): ?>
                                                        <center>
                                                            <a class="btn btn-danger btn-sm" href="detraccion_eliminar.php?id_detraccion=<?php echo $id; ?>" onclick="return confirm('¿Eliminar esta detracción?');"><i class="fa fa-trash"></i></a>
                                                        </center>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php
                                                }
                                            } else {
                                            ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">No hay registros disponibles</td>
                                                </tr>
                                            <?php } ?>
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
