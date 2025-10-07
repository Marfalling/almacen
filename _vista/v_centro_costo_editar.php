<?php 
//=======================================================================
// VISTA: v_centro_costo_editar.php 
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Centro de Costo <small>Editar Registro</small></h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Editar Centro de Costo</h2>
                        <div class="clearfix"></div>
                    </div>

                    <div class="x_content">
                        <form class="form-horizontal form-label-left" method="POST" action="../_controlador/centro_costo_editar.php">

                            <input type="hidden" name="id_centro_costo" value="<?php echo $centro['id_area']; ?>">

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Nombre del Centro<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input type="text" name="nom_centro_costo" required="required" class="form-control" value="<?php echo $centro['nom_area']; ?>" maxlength="100">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Estado</label>
                                <div class="col-md-6 col-sm-6">
                                    <select name="est_centro_costo" class="form-control">
                                        <option value="1" <?php echo ($centro['act_area'] == 1) ? 'selected' : ''; ?>>Activo</option>
                                        <option value="0" <?php echo ($centro['act_area'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
                                    </select>
                                </div>
                            </div>

                            <div class="ln_solid"></div>
                            <div class="form-group">
                                <div class="col-md-6 col-sm-6 offset-md-3">
                                    <a href="centro_costo_mostrar.php" class="btn btn-danger">Cancelar</a>
                                    <button type="submit" class="btn btn-success">Actualizar</button>
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

