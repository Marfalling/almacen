<?php 
//=======================================================================
// VISTA: v_personal_nuevo.php
//=======================================================================

require_once("../_modelo/m_area.php");
require_once("../_modelo/m_cargo.php");

$areas = MostrarAreasActivas();
$cargos = MostrarCargosActivos();
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Nuevo Personal</h3>
            </div>
        </div>
        
        <div class="clearfix"></div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Personal <small></small></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br>
                        <form class="form-horizontal form-label-left" action="personal_nuevo.php" method="post">
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Area <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="id_area" class="form-control select2_single" required="required">
                                        <option value="">Seleccione un área</option>
                                        <?php foreach($areas as $area): ?>
                                            <option value="<?php echo $area['id_area']; ?>"><?php echo $area['nom_area']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Cargo <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="id_cargo" class="form-control select2_single" required="required">
                                        <option value="">Seleccione un cargo</option>
                                        <?php foreach($cargos as $cargo): ?>
                                            <option value="<?php echo $cargo['id_cargo']; ?>"><?php echo $cargo['nom_cargo']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Nombre <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="nom" class="form-control" placeholder="Nombres del personal" required="required">
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Apellidos <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="ape" class="form-control" placeholder="Apellidos del personal" required="required">
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">DNI <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="dni" class="form-control" placeholder="DNI del personal" maxlength="20" required="required">
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Email:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="email" name="email" class="form-control" placeholder="Correo electrónico">
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Teléfono:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="tel" class="form-control" placeholder="Número de teléfono">
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Estado:</label>
                                <div class="col-md-9 col-sm-9">
                                    <div class="">
                                        <label>
                                            <input type="checkbox" name="est" class="js-switch" checked> Activo
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="ln_solid"></div>
                            
                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 offset-md-8">
                                    <button type="reset" class="btn btn-outline-danger btn-block">Limpiar</button>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <button type="submit" name="registrar" id="btn_registrar" class="btn btn-success btn-block">Registrar</button>
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