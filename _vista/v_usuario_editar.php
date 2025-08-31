<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Editar Usuario</h3>
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
                        <form class="form-horizontal form-label-left" action="usuario_editar.php" method="post" id="formUsuario">
                            
                            <!-- Información del personal (solo lectura) -->
                            <div class="form-group row ">
                                <label class="control-label col-md-3 col-sm-3 ">Personal asignado:</label>
                                <div class="col-md-9 col-sm-9 ">
                                    <div class="form-control-static">
                                        <strong><?php echo $nom_personal . ' ' . $ape_personal . ' - ' . $dni_personal . ' (' . $nom_area . ' - ' . $nom_cargo . ')'; ?></strong>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group row ">
                                <label class="control-label col-md-3 col-sm-3 ">Usuario <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9 ">
                                    <input type="text" name="user" value="<?php echo $usu; ?>" class="form-control" placeholder="Nombre de usuario" required="required" maxlength="50" id="inputUsuario">
                                    <small class="form-text text-muted">Máximo 50 caracteres, sin espacios</small>
                                </div>
                            </div>
                            
                            <div class="form-group row ">
                                <label class="control-label col-md-3 col-sm-3 ">Nueva Contraseña:</label>
                                <div class="col-md-9 col-sm-9 ">
                                    <input type="password" name="pass" class="form-control" placeholder="Dejar vacío para mantener la actual" maxlength="255" id="inputPassword">
                                    <small class="form-text text-muted">Dejar vacío si no desea cambiar la contraseña</small>
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
                                                        <input type="checkbox" name="roles[]" value="<?php echo $rol['id_rol']; ?>" class="flat" 
                                                               <?php echo (in_array($rol['id_rol'], $roles_usuario)) ? 'checked' : ''; ?>>
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
                                            <input type="checkbox" name="est" class="js-switch" <?php echo $est; ?>> Activo
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 offset-md-10">
                                    <button type="submit" name="registrar" class="btn btn-warning btn-block">Actualizar</button>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12 col-sm-12">
                                    <p><span class="text-danger">*</span> Los campos con (<span class="text-danger">*</span>) son obligatorios.</p>
                                </div>
                            </div>

                            <!-- Campos ocultos -->
                            <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->