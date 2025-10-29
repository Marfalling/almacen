<?php
//=======================================================================
// VISTA: v_banco_editar.php
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Editar Banco</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Datos del Banco <small></small></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br>
                        <form class="form-horizontal form-label-left" action="banco_editar.php" method="post">

                            <!-- Campo: Código -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">
                                    Código <span class="text-danger">*</span> :
                                </label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="cod" value="<?php echo $cod; ?>" class="form-control"
                                        placeholder="Ejemplo: BBVA, BCP, INTERBANK" required="required">
                                </div>
                            </div>

                            <!-- Campo: Nombre -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">
                                    Nombre del Banco <span class="text-danger">*</span> :
                                </label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="nom" value="<?php echo $nom; ?>" class="form-control"
                                        placeholder="Ejemplo: BANCO CONTINENTAL, BANCO DE CRÉDITO DEL PERÚ, etc."
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

                            <!-- Botones -->
                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 offset-md-8">
                                    <a href="banco_mostrar.php"  class="btn btn-outline-danger btn-block">
                                        Cancelar
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <button type="submit" name="registrar" class="btn btn-success btn-block actualizar-btn">
                                     Actualizar
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12 col-sm-12">
                                    <p><span class="text-danger">*</span> Los campos con (<span class="text-danger">*</span>) son obligatorios.</p>
                                </div>
                            </div>

                            <!-- Campos ocultos -->
                            <input type="hidden" name="id_banco" value="<?php echo $id_banco; ?>">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->