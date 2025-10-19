<?php 
//=======================================================================
// VISTA: v_detraccion_editar.php
//=======================================================================

// Generar código de detracción dinámico (sin guardar en BD)
$codigo_detraccion = 'D' . str_pad($id_detraccion, 3, '0', STR_PAD_LEFT);
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Editar Detracción/Retención/Percepción <small></small></h3>
            </div>
        </div>
        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Editar Registro <small></small></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <form class="form-horizontal form-label-left" method="POST" action="">
                            <input type="hidden" name="id_detraccion" value="<?php echo $id_detraccion; ?>">

                            <!-- Código (readonly) -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Código</label>
                                <div class="col-md-6 col-sm-6">
                                    <input type="text" class="form-control" value="<?php echo $codigo_detraccion; ?>" readonly>
                                </div>
                            </div>

                            <!-- Tipo de detracción -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Tipo <span class="text-danger">*</span></label>
                                <div class="col-md-6 col-sm-6">
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
                                <div class="col-md-6 col-sm-6">
                                    <input type="text" class="form-control" name="nom" value="<?php echo $nom; ?>" required placeholder="Ej: OBRAS, RETENCIÓN 6%, PERCEPCIÓN 2%">
                                </div>
                            </div>

                            <!-- Porcentaje -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Porcentaje (%) <span class="text-danger">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input type="number" step="0.01" class="form-control" name="porcentaje" value="<?php echo $porcentaje; ?>" required placeholder="Ej: 12.00">
                                </div>
                            </div>

                            <!-- Estado -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Estado</label>
                                <div class="col-md-6 col-sm-6">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="estado" name="estado" value="1" <?php echo ($estado == 1) ? 'checked' : ''; ?>>
                                        <label class="custom-control-label" for="estado">Activo / Inactivo</label>
                                    </div>
                                </div>
                            </div>

                            <div class="ln_solid"></div>
                            <div class="form-group row">
                                <div class="col-md-6 col-sm-6 offset-md-3">
                                    <button type="submit" name="registrar" class="btn btn-success">
                                        <i class="fa fa-save"></i> Guardar Cambios
                                    </button>
                                    <a href="detraccion_mostrar.php" class="btn btn-secondary">
                                        <i class="fa fa-times"></i> Cancelar
                                    </a>
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