<?php 
//=======================================================================
// VISTA: v_obras_editar.php
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Editar Obra</h3>
            </div>
        </div>
        
        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Modificar datos de la Obra <small></small></h2>
                        <div class="clearfix"></div>
                    </div>

                    <div class="x_content">
                        <br>
                        <form class="form-horizontal form-label-left" action="obras_editar.php" method="post">
                            <input type="hidden" name="id_obra" value="<?php echo $id_obra; ?>">

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Nombre de la Obra <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="nom" class="form-control" 
                                           value="<?php echo htmlspecialchars($nom); ?>" 
                                           placeholder="Nombre de la obra" required="required">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Estado:</label>
                                <div class="col-md-9 col-sm-9">
                                    <label>
                                        <input type="checkbox" name="est" class="js-switch" <?php echo $est; ?>> Activo
                                    </label>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 offset-md-10">
                                    <button type="submit" name="registrar" class="btn btn-success btn-block">Actualizar</button>
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

