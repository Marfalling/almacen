<?php
//=======================================================================
// VISTA: v_producto_editar.php
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Editar Producto</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Datos del Producto <small></small></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br>
                        <!-- IMPORTANTE: Agregar enctype para subida de archivos -->
                        <form class="form-horizontal form-label-left" action="producto_editar.php" method="post" enctype="multipart/form-data">
                            
                            <!-- Información Básica -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Tipo de Producto <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="id_producto_tipo" class="form-control" required="required">
                                        <option value="">Seleccionar tipo de producto</option>
                                        <?php foreach($producto_tipos as $tipo) { 
                                            $selected = ($tipo['id_producto_tipo'] == $id_producto_tipo) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $tipo['id_producto_tipo']; ?>" <?php echo $selected; ?>><?php echo $tipo['nom_producto_tipo']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Tipo de Material <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="id_material_tipo" class="form-control" required="required">
                                        <option value="">Seleccionar tipo de material</option>
                                        <?php foreach($material_tipos as $material) { 
                                            $selected = ($material['id_material_tipo'] == $id_material_tipo) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $material['id_material_tipo']; ?>" <?php echo $selected; ?>><?php echo $material['nom_material_tipo']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Unidad de Medida <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="id_unidad_medida" class="form-control" required="required">
                                        <option value="">Seleccionar unidad de medida</option>
                                        <?php foreach($unidades_medida as $unidad) { 
                                            $selected = ($unidad['id_unidad_medida'] == $id_unidad_medida) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $unidad['id_unidad_medida']; ?>" <?php echo $selected; ?>><?php echo $unidad['nom_unidad_medida']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Código de Material:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="cod_material" value="<?php echo $cod_material; ?>" class="form-control" placeholder="Código del material">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Nombre del Producto <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="nom_producto" value="<?php echo $nom_producto; ?>" class="form-control" placeholder="Nombre del producto" required="required">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Número de Serie:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="nser_producto" value="<?php echo $nser_producto; ?>" class="form-control" placeholder="Número de serie">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Modelo:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="mod_producto" value="<?php echo $mod_producto; ?>" class="form-control" placeholder="Modelo del producto">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Marca:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="mar_producto" value="<?php echo $mar_producto; ?>" class="form-control" placeholder="Marca del producto">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Detalle:</label>
                                <div class="col-md-9 col-sm-9">
                                    <textarea name="det_producto" class="form-control" rows="3" placeholder="Detalle del producto"><?php echo $det_producto; ?></textarea>
                                </div>
                            </div>

                            <!-- Sección de Homologación -->
                            <div class="x_title">
                                <h2>Documento de Homologación</h2>
                                <div class="clearfix"></div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Documento de Homologación:</label>
                                <div class="col-md-9 col-sm-9">
                                    <?php if (!empty($hom_producto)) { ?>
                                        <p class="text-info">Archivo actual: <?php echo $hom_producto; ?></p>
                                    <?php } ?>
                                    <input type="file" name="hom_archivo" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                    <small class="text-muted">Formatos permitidos: PDF, JPG, JPEG, PNG, DOC, DOCX. Tamaño máximo: 10MB. Si no selecciona archivo, se mantendrá el actual.</small>
                                </div>
                            </div>

                            <!-- Sección de Calibrado -->
                            <div class="x_title">
                                <h2>Información de Calibrado <small>(para materiales)</small></h2>
                                <div class="clearfix"></div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Fecha Último Calibrado:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="date" name="fuc_producto" value="<?php echo $fuc_producto; ?>" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Fecha Próximo Calibrado:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="date" name="fpc_producto" value="<?php echo $fpc_producto; ?>" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Documento de Calibrado:</label>
                                <div class="col-md-9 col-sm-9">
                                    <?php if (!empty($dcal_producto)) { ?>
                                        <p class="text-info">Archivo actual: <?php echo $dcal_producto; ?></p>
                                    <?php } ?>
                                    <input type="file" name="dcal_archivo" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                    <small class="text-muted">Formatos permitidos: PDF, JPG, JPEG, PNG, DOC, DOCX. Tamaño máximo: 10MB. Si no selecciona archivo, se mantendrá el actual.</small>
                                </div>
                            </div>

                            <!-- Sección de Operatividad -->
                            <div class="x_title">
                                <h2>Información de Operatividad <small>(para materiales)</small></h2>
                                <div class="clearfix"></div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Fecha Última Operatividad:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="date" name="fuo_producto" value="<?php echo $fuo_producto; ?>" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Fecha Próxima Operatividad:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="date" name="fpo_producto" value="<?php echo $fpo_producto; ?>" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Documento de Operatividad:</label>
                                <div class="col-md-9 col-sm-9">
                                    <?php if (!empty($dope_producto)) { ?>
                                        <p class="text-info">Archivo actual: <?php echo $dope_producto; ?></p>
                                    <?php } ?>
                                    <input type="file" name="dope_archivo" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                    <small class="text-muted">Formatos permitidos: PDF, JPG, JPEG, PNG, DOC, DOCX. Tamaño máximo: 10MB. Si no selecciona archivo, se mantendrá el actual.</small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Estado:</label>
                                <div class="col-md-9 col-sm-9">
                                    <div class="">
                                        <label>
                                            <input type="checkbox" name="est" class="js-switch" <?php echo $est; ?>> Activo
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <!-- Botones -->
                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 offset-md-8">
                                    <a href="producto_mostrar.php" class="btn btn-outline-danger btn-block">
                                        <i class="bi bi-x-square"></i> Cancelar
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <button type="submit" name="registrar" class="btn btn-success btn-block actualizar-btn">
                                     <i class="bi bi-arrow-clockwise"></i> Actualizar
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12 col-sm-12">
                                    <p><span class="text-danger">*</span> Los campos con (<span class="text-danger">*</span>) son obligatorios.</p>
                                </div>
                            </div>

                            <!-- Campos ocultos -->
                            <input type="hidden" name="id_producto" value="<?php echo $id_producto; ?>">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->

