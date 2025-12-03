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

                            <!-- Nombre del Centro -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">
                                    Nombre del Centro <span class="text-danger">*</span>
                                </label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="nom_centro_costo" required class="form-control" 
                                           value="<?php echo $centro['nom_area']; ?>" maxlength="100">
                                </div>
                            </div>

                            <!-- Estado -->
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
                                    <a href="centro_costo_mostrar.php" class="btn btn-outline-danger btn-block">
                                        <i class="bi bi-x-square"></i> Cancelar
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <button type="submit" class="btn btn-success btn-block actualizar-btn">
                                        <i class="bi bi-arrow-clockwise"></i> Actualizar
                                    </button>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12 col-sm-12">
                                        <p><span class="text-danger">*</span> Los campos con (<span class="text-danger">*</span>) son obligatorios.</p>
                                    </div>
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


