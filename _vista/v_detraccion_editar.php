<?php
//=======================================================================
// VISTA: v_detraccion_editar.php
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Editar Detracción</h3>
        </div>
    </div>
    <div class="clearfix"></div>

    <div class="row">
        <div class="col-md-6 col-sm-8">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Formulario de Edición</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <form method="post" class="form-horizontal form-label-left">
                        <input type="hidden" name="id_detraccion" value="<?php echo htmlspecialchars($id_detraccion); ?>">

                        <div class="form-group row">
                            <label class="control-label col-md-3 col-sm-3">Nombre</label>
                            <div class="col-md-7 col-sm-7">
                                <input type="text" name="nom" class="form-control" required value="<?php echo htmlspecialchars($nom); ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3 col-sm-3">Porcentaje (%)</label>
                            <div class="col-md-3 col-sm-3">
                                <input type="number" step="0.01" name="porcentaje" class="form-control" required value="<?php echo htmlspecialchars(number_format((float)$porcentaje, 2, '.', '')); ?>">
                            </div>
                        </div>

                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 offset-md-3">
                                <button type="submit" name="registrar" class="btn btn-success">Actualizar</button>
                                <a href="detraccion_mostrar.php" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->

