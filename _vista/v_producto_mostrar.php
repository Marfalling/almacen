<?php
//=======================================================================
// VISTA: v_producto_mostrar.php
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Producto<small></small></h3>
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
                                <h2>Listado de Producto<small></small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-2">
                                <a href="producto_nuevo.php" class="btn btn-outline-info btn-sm btn-block">Nuevo producto</a> 
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
                                                <th>Código</th>
                                                <th>Nombre</th>
                                                <th>Tipo Producto</th>
                                                <th>Tipo Material</th>
                                                <th>N° Serie</th>
                                                <th>Modelo</th>
                                                <th>Marca</th>
                                                <th>Doc. Homolog.</th>
                                                <th>Doc. Calibrado</th>
                                                <th>Doc. Operatividad</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            $c = 0;
                                            foreach ($producto as $value) {
                                                $c++;
                                                $id_producto = $value['id_producto'];
                                                $id_producto_tipo = $value['id_producto_tipo'];
                                                $id_material_tipo = $value['id_material_tipo'];
                                                $id_unidad_medida = $value['id_unidad_medida'];
                                                $cod_material = $value['cod_material'];
                                                $nom_producto = $value['nom_producto'];
                                                $nom_producto_tipo = $value['nom_producto_tipo'];
                                                $nom_material_tipo = $value['nom_material_tipo'];
                                                $nom_unidad_medida = $value['nom_unidad_medida'];
                                                $nser_producto = $value['nser_producto'];
                                                $mod_producto = $value['mod_producto'];
                                                $mar_producto = $value['mar_producto'];
                                                $det_producto = $value['det_producto'];
                                                $hom_producto = $value['hom_producto'];
                                                $fuc_producto = $value['fuc_producto'];
                                                $fpc_producto = $value['fpc_producto'];
                                                $dcal_producto = $value['dcal_producto'];
                                                $fuo_producto = $value['fuo_producto'];
                                                $fpo_producto = $value['fpo_producto'];
                                                $dope_producto = $value['dope_producto'];
                                                $est_producto = $value['est_producto'];
                                                $estado = ($est_producto == 1) ? "ACTIVO" : "INACTIVO";
                                            ?>
                                                <tr>
                                                    <td><?php echo $c; ?></td>
                                                    <td><?php echo $cod_material; ?></td>
                                                    <td><?php echo $nom_producto; ?></td>
                                                    <td><?php echo $nom_producto_tipo; ?></td>
                                                    <td><?php echo $nom_material_tipo; ?></td>
                                                    <td><?php echo $nser_producto; ?></td>
                                                    <td><?php echo $mod_producto; ?></td>
                                                    <td><?php echo $mar_producto; ?></td>
                                                    <td>
                                                        <?php if (!empty($hom_producto)) { ?>
                                                            <a href="../_uploads/documentos/<?php echo $hom_producto; ?>" target="_blank" class="btn btn-success btn-xs" title="Ver documento de homologación">
                                                                <i class="fa fa-file"></i>
                                                            </a>
                                                            <br>
                                                            <small class="text-muted"><?php echo substr($hom_producto, 0, 15) . '...'; ?></small>
                                                        <?php } else { ?>
                                                            <span class="text-muted">Sin documento</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($dcal_producto)) { ?>
                                                            <a href="../_uploads/documentos/<?php echo $dcal_producto; ?>" target="_blank" class="btn btn-primary btn-xs" title="Ver documento de calibrado">
                                                                <i class="fa fa-file"></i>
                                                            </a>
                                                            <br>
                                                            <small class="text-muted"><?php echo substr($dcal_producto, 0, 15) . '...'; ?></small>
                                                        <?php } else { ?>
                                                            <span class="text-muted">Sin documento</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($dope_producto)) { ?>
                                                            <a href="../_uploads/documentos/<?php echo $dope_producto; ?>" target="_blank" class="btn btn-primary btn-xs" title="Ver documento de operatividad">
                                                                <i class="fa fa-file"></i>
                                                            </a>
                                                            <br>
                                                            <small class="text-muted"><?php echo substr($dope_producto, 0, 15) . '...'; ?></small>
                                                        <?php } else { ?>
                                                            <span class="text-muted">Sin documento</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <center>
                                                            <?php if ($est_producto == 1) { ?>
                                                                <span class="badge badge-success badge_size">ACTIVO</span>
                                                            <?php } else { ?>
                                                                <span class="badge badge-danger badge_size">INACTIVO</span>
                                                            <?php } ?>
                                                        </center>
                                                    </td>
                                                    <td>
                                                        <center>
                                                            <a class="btn btn-info btn-xs" href="producto_detalle.php?id=<?php echo $id_producto; ?>" title="Ver Detalle">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            <a class="btn btn-warning btn-xs" href="producto_editar.php?id_producto=<?php echo $id_producto; ?>" title="Editar">
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