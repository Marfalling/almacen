<?php 
//=======================================================================
// VISTA: v_detraccion_nuevo.php
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Nueva Detracción <small></small></h3>
            </div>
        </div>
        <div class="clearfix"></div>

        <div class="row">
            <!-- --------------------------------------- -->
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Registrar Detracción <small></small></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <form class="form-horizontal form-label-left" method="POST" action="">
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Nombre detracción</label>
                                <div class="col-md-6 col-sm-6">
                                    <input type="text" name="nom" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Porcentaje (%)</label>
                                <div class="col-md-6 col-sm-6">
                                    <input type="number" step="0.01" name="porcentaje" class="form-control" required>
                                </div>
                            </div>

                            <div class="ln_solid"></div>
                            <div class="form-group row">
                                <div class="col-md-6 col-sm-6 offset-md-3">
                                    <button type="submit" name="registrar" class="btn btn-success">Guardar</button>
                                    <a href="detraccion_mostrar.php" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- --------------------------------------- -->
        </div>
    </div>
</div>
<!-- /page content -->
