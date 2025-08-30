<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Editar Rol</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Datos del Rol <small></small></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br>
                        <form class="form-horizontal form-label-left" action="rol_usuario_editar.php" method="POST">
                            <input type="hidden" name="id_rol" value="<?php echo $rol['id_rol']; ?>">
                            
                            <!-- Nombre del rol -->
                            <div class="form-group row ">
                                <label class="control-label col-md-3 col-sm-3 ">Nombre del Rol <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9 ">
                                    <input type="text" name="nom_rol" class="form-control" placeholder="Nombre del rol" required="required" maxlength="100" value="<?php echo $rol['nom_rol']; ?>">
                                    <small class="form-text text-muted">Ejemplo: ADMINISTRADOR, OPERADOR, SUPERVISOR, etc.</small>
                                </div>
                            </div>

                            <!-- Permisos -->
                            <div class="form-group row ">
                                <label class="control-label col-md-3 col-sm-3 ">Permisos <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9 ">
                                    <?php if(isset($modulos_acciones) && !empty($modulos_acciones)) { 
                                        $modulos_agrupados = array();
                                        foreach($modulos_acciones as $ma) {
                                            $modulos_agrupados[$ma['nom_modulo']][] = $ma;
                                        }
                                        
                                        // Crear array de permisos actuales para facilitar la verificación
                                        $permisos_actuales = array();
                                        if(isset($rol['permisos'])) {
                                            foreach($rol['permisos'] as $permiso) {
                                                $permisos_actuales[] = $permiso['id_modulo_accion'];
                                            }
                                        }
                                    ?>
                                        <div class="accordion" id="permisosAccordion">
                                            <?php foreach($modulos_agrupados as $modulo => $acciones) { ?>
                                                <div class="card">
                                                    <div class="card-header" id="heading_<?php echo preg_replace('/[^a-zA-Z0-9]/', '', $modulo); ?>">
                                                        <h2 class="mb-0">
                                                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse_<?php echo preg_replace('/[^a-zA-Z0-9]/', '', $modulo); ?>" aria-expanded="false" aria-controls="collapse_<?php echo preg_replace('/[^a-zA-Z0-9]/', '', $modulo); ?>">
                                                                <strong><?php echo $modulo; ?></strong>
                                                                <span class="badge badge-primary ml-2" id="count_<?php echo preg_replace('/[^a-zA-Z0-9]/', '', $modulo); ?>">0</span>
                                                            </button>
                                                            <label class="float-right">
                                                                <input type="checkbox" class="select-all-module" data-module="<?php echo preg_replace('/[^a-zA-Z0-9]/', '', $modulo); ?>"> Seleccionar todo
                                                            </label>
                                                        </h2>
                                                    </div>
                                                    <div id="collapse_<?php echo preg_replace('/[^a-zA-Z0-9]/', '', $modulo); ?>" class="collapse" aria-labelledby="heading_<?php echo preg_replace('/[^a-zA-Z0-9]/', '', $modulo); ?>" data-parent="#permisosAccordion">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <?php foreach($acciones as $accion) { 
                                                                    $checked = in_array($accion['id_modulo_accion'], $permisos_actuales) ? 'checked' : '';
                                                                ?>
                                                                    <div class="col-md-4 col-sm-6 mb-2">
                                                                        <div class="checkbox">
                                                                            <label>
                                                                                <input type="checkbox" name="permisos[]" value="<?php echo $accion['id_modulo_accion']; ?>" class="flat permiso-checkbox" data-module="<?php echo preg_replace('/[^a-zA-Z0-9]/', '', $modulo); ?>" <?php echo $checked; ?>>
                                                                                <?php echo $accion['nom_accion']; ?>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <small class="form-text text-muted">Seleccione los permisos que tendrá este rol</small>
                                    <?php } else { ?>
                                        <p class="text-danger">No hay módulos y acciones disponibles.</p>
                                    <?php } ?>
                                </div>
                            </div>

                            <!-- Estado -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3 ">Estado:</label>
                                <div class="col-md-9 col-sm-9 ">
                                    <div class="">
                                        <label>
                                            <input type="checkbox" name="est" class="js-switch" <?php echo ($rol['est_rol'] == 1) ? 'checked' : ''; ?>> Activo
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <div class="form-group">
                                <div class="col-md-2 col-sm-2  offset-md-8">
                                    <a href="rol_usuario_mostrar.php" class="btn btn-outline-secondary btn-block">Cancelar</a>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <button type="submit" name="editar" id="btn_editar" class="btn btn-warning btn-block">Actualizar</button>
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

