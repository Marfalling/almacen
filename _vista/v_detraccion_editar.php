<?php 
//=======================================================================
// VISTA: v_detraccion_editar.php
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Editar Detracción/Retención/Percepción</h3>
            </div>
        </div>
        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Editar Registro <small>Modificar información</small></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br>
                        <form class="form-horizontal form-label-left" method="POST" action="">
                            <input type="hidden" name="id_detraccion" value="<?php echo $id_detraccion; ?>">

                            <!-- Tipo de detracción -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Tipo <span class="text-danger">*</span></label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="id_detraccion_tipo" class="form-control" required>
                                        <option value="">Seleccionar tipo...</option>
                                        <?php foreach ($tipos_detraccion as $tipo): ?>
                                            <option value="<?php echo $tipo['id_detraccion_tipo']; ?>"
                                                <?php echo ($tipo['id_detraccion_tipo'] == $id_detraccion_tipo_actual) ? 'selected' : ''; ?>>
                                                <?php echo strtoupper($tipo['nom_detraccion_tipo']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Nombre -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Nombre <span class="text-danger">*</span></label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" name="nom" 
                                           value="<?php echo $nom; ?>" required 
                                           placeholder="Ej: OBRAS, RETENCIÓN 6%, PERCEPCIÓN 2%">
                                </div>
                            </div>

                            <!-- Código -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Código <span class="text-danger">*</span></label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" name="cod_detraccion"
                                           value="<?php echo isset($cod_detraccion) ? $cod_detraccion : ''; ?>" required>
                                    <small id="mensaje_codigo" class="text-danger"></small>
                                </div>
                            </div>

                            <!-- Porcentaje -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Porcentaje (%) <span class="text-danger">*</span></label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="number" step="0.01" class="form-control" name="porcentaje" 
                                           value="<?php echo $porcentaje; ?>" required placeholder="Ej: 12.00">
                                </div>
                            </div>

                            <!-- Estado -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Estado:</label>
                                <div class="col-md-9 col-sm-9">
                                    <div>
                                        <label>
                                            <input type="checkbox" name="estado" class="js-switch" 
                                                   <?php echo ($estado == 1) ? 'checked' : ''; ?>> Activo
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <!-- Botones -->
                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 offset-md-8">
                                    <a href="detraccion_mostrar.php" class="btn btn-outline-danger btn-block">
                                        <i class="bi bi-x-square"></i> Cancelar
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <button type="submit" name="registrar" class="btn btn-success btn-block">
                                        <i class="bi bi-arrow-clockwise"></i> Actualizar
                                    </button>
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

<!-- ====== ESTILOS ====== -->
<style>
/* Consistencia visual con Editar Personal */
.form-control-static {
    padding: 7px 12px;
    margin-bottom: 0;
    background-color: #f9f9f9;
    border: 1px solid #e5e5e5;
    border-radius: 4px;
    font-size: 14px;
}

/* Switch/checkbox activo */
.js-switch + .switchery {
    margin-left: 10px;
}

/* Botones */
.btn-success {
    background-color: #26B99A;
    border-color: #26B99A;
}

.btn-success:hover {
    background-color: #1e9e83;
    border-color: #1e9e83;
}

.btn-outline-secondary {
    border: 1px solid #ccc;
    color: #555;
}

.btn-outline-secondary:hover {
    background-color: #f2f2f2;
    border-color: #999;
}
</style>
