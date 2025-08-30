<?php
//=======================================================================
// VISTA: v_producto_detalle.php
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Detalle del Producto</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <div class="row">
                            <div class="col-sm-10">
                                <h2>Información del Producto <small><?php echo $producto['nom_producto']; ?></small></h2>
                            </div>
                            <div class="col-sm-2">
                                <a href="producto_mostrar.php" class="btn btn-outline-secondary btn-sm btn-block">
                                    <i class="fa fa-arrow-left"></i> Volver al listado
                                </a>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="x_content">
                        <div class="row">
                            <!-- Información Básica -->
                            <div class="col-md-6">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2><i class="fa fa-info-circle"></i> Información Básica</h2>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <table class="table table-striped">
                                            <tr>
                                                <td><strong>ID Producto:</strong></td>
                                                <td><?php echo $producto['id_producto']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Código de Material:</strong></td>
                                                <td><?php echo !empty($producto['cod_material']) ? $producto['cod_material'] : '<span class="text-muted">No especificado</span>'; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Nombre del Producto:</strong></td>
                                                <td><?php echo $producto['nom_producto']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Tipo de Producto:</strong></td>
                                                <td><?php echo $producto['nom_producto_tipo']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Tipo de Material:</strong></td>
                                                <td><?php echo $producto['nom_material_tipo']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Unidad de Medida:</strong></td>
                                                <td><?php echo $producto['nom_unidad_medida']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Estado:</strong></td>
                                                <td>
                                                    <?php if ($producto['est_producto'] == 1) { ?>
                                                        <span class="badge badge-success">ACTIVO</span>
                                                    <?php } else { ?>
                                                        <span class="badge badge-danger">INACTIVO</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Información Técnica -->
                            <div class="col-md-6">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2><i class="fa fa-cogs"></i> Información Técnica</h2>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <table class="table table-striped">
                                            <tr>
                                                <td><strong>Número de Serie:</strong></td>
                                                <td><?php echo !empty($producto['nser_producto']) ? $producto['nser_producto'] : '<span class="text-muted">No especificado</span>'; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Modelo:</strong></td>
                                                <td><?php echo !empty($producto['mod_producto']) ? $producto['mod_producto'] : '<span class="text-muted">No especificado</span>'; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Marca:</strong></td>
                                                <td><?php echo !empty($producto['mar_producto']) ? $producto['mar_producto'] : '<span class="text-muted">No especificado</span>'; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Detalle:</strong></td>
                                                <td>
                                                    <?php if (!empty($producto['det_producto'])) { ?>
                                                        <textarea class="form-control" rows="3" readonly><?php echo $producto['det_producto']; ?></textarea>
                                                    <?php } else { ?>
                                                        <span class="text-muted">No especificado</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información de Calibrado -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2><i class="fa fa-calibration"></i> Información de Calibrado</h2>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <table class="table table-striped">
                                            <tr>
                                                <td><strong>Fecha Último Calibrado:</strong></td>
                                                <td>
                                                    <?php if (!empty($producto['fuc_producto'])) { ?>
                                                        <?php echo date('d/m/Y', strtotime($producto['fuc_producto'])); ?>
                                                    <?php } else { ?>
                                                        <span class="text-muted">No especificado</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Fecha Próximo Calibrado:</strong></td>
                                                <td>
                                                    <?php if (!empty($producto['fpc_producto'])) { ?>
                                                        <?php 
                                                        $fecha_proxima = date('d/m/Y', strtotime($producto['fpc_producto']));
                                                        $dias_restantes = (strtotime($producto['fpc_producto']) - time()) / (60 * 60 * 24);
                                                        
                                                        if ($dias_restantes < 0) {
                                                            echo '<span class="text-danger">' . $fecha_proxima . ' <small>(Vencido)</small></span>';
                                                        } elseif ($dias_restantes <= 30) {
                                                            echo '<span class="text-warning">' . $fecha_proxima . ' <small>(Por vencer)</small></span>';
                                                        } else {
                                                            echo '<span class="text-success">' . $fecha_proxima . '</span>';
                                                        }
                                                        ?>
                                                    <?php } else { ?>
                                                        <span class="text-muted">No especificado</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Documento de Calibrado:</strong></td>
                                                <td>
                                                    <?php if (!empty($producto['dcal_producto'])) { ?>
                                                        <div class="mb-2">
                                                            <a href="../_uploads/documentos/<?php echo $producto['dcal_producto']; ?>" target="_blank" class="btn btn-primary btn-sm">
                                                                <i class="fa fa-file"></i> Ver Documento
                                                            </a>
                                                        </div>
                                                        <small class="text-muted">Archivo: <?php echo $producto['dcal_producto']; ?></small>
                                                        
                                                        <!-- Vista previa si es imagen -->
                                                        <?php 
                                                        $extension = strtolower(pathinfo($producto['dcal_producto'], PATHINFO_EXTENSION));
                                                        if (in_array($extension, ['jpg', 'jpeg'])) { ?>
                                                            <div class="mt-2">
                                                                <img src="../_uploads/documentos/<?php echo $producto['dcal_producto']; ?>" alt="Documento de calibrado" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                                                            </div>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <span class="text-muted">No hay documento</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Información de Operatividad -->
                            <div class="col-md-6">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2><i class="fa fa-wrench"></i> Información de Operatividad</h2>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <table class="table table-striped">
                                            <tr>
                                                <td><strong>Fecha Última Operatividad:</strong></td>
                                                <td>
                                                    <?php if (!empty($producto['fuo_producto'])) { ?>
                                                        <?php echo date('d/m/Y', strtotime($producto['fuo_producto'])); ?>
                                                    <?php } else { ?>
                                                        <span class="text-muted">No especificado</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Fecha Próxima Operatividad:</strong></td>
                                                <td>
                                                    <?php if (!empty($producto['fpo_producto'])) { ?>
                                                        <?php 
                                                        $fecha_proxima = date('d/m/Y', strtotime($producto['fpo_producto']));
                                                        $dias_restantes = (strtotime($producto['fpo_producto']) - time()) / (60 * 60 * 24);
                                                        
                                                        if ($dias_restantes < 0) {
                                                            echo '<span class="text-danger">' . $fecha_proxima . ' <small>(Vencido)</small></span>';
                                                        } elseif ($dias_restantes <= 30) {
                                                            echo '<span class="text-warning">' . $fecha_proxima . ' <small>(Por vencer)</small></span>';
                                                        } else {
                                                            echo '<span class="text-success">' . $fecha_proxima . '</span>';
                                                        }
                                                        ?>
                                                    <?php } else { ?>
                                                        <span class="text-muted">No especificado</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Documento de Operatividad:</strong></td>
                                                <td>
                                                    <?php if (!empty($producto['dope_producto'])) { ?>
                                                        <div class="mb-2">
                                                            <a href="../_uploads/documentos/<?php echo $producto['dope_producto']; ?>" target="_blank" class="btn btn-primary btn-sm">
                                                                <i class="fa fa-file"></i> Ver Documento
                                                            </a>
                                                        </div>
                                                        <small class="text-muted">Archivo: <?php echo $producto['dope_producto']; ?></small>
                                                        
                                                        <!-- Vista previa si es imagen -->
                                                        <?php 
                                                        $extension = strtolower(pathinfo($producto['dope_producto'], PATHINFO_EXTENSION));
                                                        if (in_array($extension, ['jpg', 'jpeg'])) { ?>
                                                            <div class="mt-2">
                                                                <img src="../_uploads/documentos/<?php echo $producto['dope_producto']; ?>" alt="Documento de operatividad" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                                                            </div>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <span class="text-muted">No hay documento</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group text-center">
                                    <a href="producto_mostrar.php" class="btn btn-secondary">
                                        <i class="fa fa-arrow-left"></i> Volver al Listado
                                    </a>
                                    <a href="producto_editar.php?id=<?php echo $producto['id_producto']; ?>" class="btn btn-warning">
                                        <i class="fa fa-edit"></i> Editar Producto
                                    </a>
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