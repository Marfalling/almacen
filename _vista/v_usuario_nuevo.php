<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Nuevo Usuario</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Datos de Usuario <small></small></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br>
                        <form class="form-horizontal form-label-left" action="usuario_nuevo.php" method="post" id="formUsuario">
                            <div class="form-group row ">
                                <label class="control-label col-md-3 col-sm-3 ">Personal <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9 ">
                                    <select name="id_personal" class="form-control" required="required">
                                        <option value="">Seleccione personal</option>
                                        <?php if(isset($personal_sin_usuario) && !empty($personal_sin_usuario)) { ?>
                                            <?php foreach($personal_sin_usuario as $personal) { ?>
                                                <option value="<?php echo $personal['id_personal']; ?>">
                                                    <?php echo $personal['nom_personal'] . ' ' . $personal['ape_personal'] . ' - ' . $personal['dni_personal'] . ' (' . $personal['nom_area'] . ' - ' . $personal['nom_cargo'] . ')'; ?>
                                                </option>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <option value="">No hay personal disponible sin usuario asignado</option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group row ">
                                <label class="control-label col-md-3 col-sm-3 ">Usuario <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9 ">
                                    <input type="text" name="user" class="form-control" placeholder="Nombre de usuario" required="required" maxlength="50" id="inputUsuario">
                                    <small class="form-text text-muted">Máximo 50 caracteres, sin espacios</small>
                                </div>
                            </div>
                            
                            <div class="form-group row ">
                                <label class="control-label col-md-3 col-sm-3 ">Contraseña <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9 ">
                                    <input type="password" name="pass" class="form-control" placeholder="Contraseña" required="required" maxlength="255" id="inputPassword">
                                    <small class="form-text text-muted">Mínimo 6 caracteres recomendado</small>
                                </div>
                            </div>
                            
                            <div class="form-group row ">
                                <label class="control-label col-md-3 col-sm-3 ">Roles <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9 ">
                                    <?php if(isset($roles_activos) && !empty($roles_activos)) { ?>
                                        <div class="checkbox-list">
                                            <?php foreach($roles_activos as $rol) { ?>
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="roles[]" value="<?php echo $rol['id_rol']; ?>" class="flat">
                                                        <?php echo $rol['nom_rol']; ?>
                                                    </label>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <small class="form-text text-muted">Seleccione al menos un rol para el usuario</small>
                                    <?php } else { ?>
                                        <p class="text-danger">No hay roles activos disponibles. Debe crear al menos un rol primero.</p>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3 ">Estado:</label>
                                <div class="col-md-9 col-sm-9 ">
                                    <div class="">
                                        <label>
                                            <input type="checkbox" name="est" class="js-switch" checked> Activo
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <div class="form-group">
                                <div class="col-md-2 col-sm-2  offset-md-8">
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

