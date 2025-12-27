<?php
//=======================================================================
// VISTA: v_unidad_medida_editar.php
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Editar Unidad de Medida</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Datos de la Unidad de Medida <small></small></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br>
                        <form class="form-horizontal form-label-left" action="unidad_medida_editar.php?id_unidad_medida=<?php echo $id_unidad_medida; ?>" method="post">
                            
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Código <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" 
                                           name="cod" 
                                           class="form-control" 
                                           value="<?php echo isset($unidad_medida_data['cod_unidad_medida']) ? $unidad_medida_data['cod_unidad_medida'] : ''; ?>"
                                           placeholder="Código o abreviatura (Ej: UND, KG, M)" 
                                           maxlength="10"
                                           style="text-transform: uppercase;"
                                           required="required">
                                    <small class="form-text text-muted">
                                        <i class="fa fa-info-circle"></i> Abreviatura única de la unidad de medida (máximo 10 caracteres)
                                    </small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Nombre <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" 
                                           name="nom" 
                                           class="form-control" 
                                           value="<?php echo $nom; ?>"
                                           placeholder="Nombre completo de la unidad de medida" 
                                           required="required">
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

                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 offset-md-8">
                                    <a href="unidad_medida_mostrar.php" class="btn btn-outline-danger btn-block">Cancelar</a>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <button type="submit" name="registrar" id="btn_registrar" class="btn btn-success btn-block">Actualizar</button>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12 col-sm-12">
                                    <p><span class="text-danger">*</span> Los campos con (<span class="text-danger">*</span>) son obligatorios.</p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->