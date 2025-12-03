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
                            <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>">
                            
                            <div class="form-group row ">
                                <label class="control-label col-md-3 col-sm-3 ">Personal:</label>
                                <div class="col-md-9 col-sm-9 ">
                                    <div class="form-control-static">
                                        <?php echo $nom_personal  . ' - ' . $dni_personal . ' (' . $nom_area . ' - ' . $nom_cargo . ')'; ?>
                                    </div>
                                    <small class="form-text text-muted">El personal asociado no se puede cambiar desde esta vista</small>
                                </div>
                            </div>
                            
                            <div class="form-group row ">
                                <label class="control-label col-md-3 col-sm-3 ">Usuario <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9 ">
                                    <input type="text" name="user" class="form-control" placeholder="Nombre de usuario" required="required" maxlength="50" id="inputUsuario" value="<?php echo $usu; ?>">
                                    <small class="form-text text-muted">Máximo 50 caracteres, sin espacios</small>
                                </div>
                            </div>
                            
                            <div class="form-group row ">
                                <label class="control-label col-md-3 col-sm-3 ">Nueva Contraseña:</label>
                                <div class="col-md-9 col-sm-9 ">
                                    <input type="password" name="pass" class="form-control" placeholder="Dejar vacío para mantener la actual" maxlength="255" id="inputPassword">
                                    <small class="form-text text-muted">Deje vacío si no desea cambiar la contraseña. Mínimo 6 caracteres si va a cambiarla.</small>
                                </div>
                            </div>
                            
                            <div class="form-group row ">
                                <label class="control-label col-md-3 col-sm-3 ">Rol <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9 ">
                                    <?php if(isset($roles_activos) && !empty($roles_activos)) { ?>
                                        <div class="radio-list" id="rolesContainer">
                                            <?php 
                                            // Obtener el rol actual del usuario (asumiendo que solo tiene uno)
                                            $rol_actual = '';
                                            if (!empty($roles_usuario) && is_array($roles_usuario)) {
                                                $rol_actual = $roles_usuario[0]['id_rol'];
                                            }
                                            
                                            foreach($roles_activos as $rol) { 
                                                $checked = ($rol['id_rol'] == $rol_actual) ? 'checked' : '';
                                            ?>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="rol_seleccionado" value="<?php echo $rol['id_rol']; ?>" class="flat" <?php echo $checked; ?> required>
                                                        <?php echo $rol['nom_rol']; ?>
                                                    </label>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <small class="form-text text-muted">Seleccione un rol para el usuario</small>
                                        <div id="rol-error" class="text-danger" style="display: none;">
                                            Debe seleccionar un rol para el usuario.
                                        </div>
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
                                <div class="col-md-2 col-sm-2  offset-md-8">
                                    <a href="usuario_mostrar.php" class="btn btn-outline-danger btn-block"><i class="bi bi-x-square"></i> Cancelar</a>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <button type="submit" name="registrar" id="btn_registrar" class="btn btn-success btn-block"><i class="bi bi-arrow-clockwise"></i> Actualizar</button>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formUsuario');
    const btnRegistrar = document.getElementById('btn_registrar');
    const rolError = document.getElementById('rol-error');
    
    // Validación del formulario al enviar
    form.addEventListener('submit', function(e) {
        const rolSeleccionado = document.querySelector('input[name="rol_seleccionado"]:checked');
        
        if (!rolSeleccionado) {
            e.preventDefault();
            rolError.style.display = 'block';
            
            // Scroll hasta el error
            document.getElementById('rolesContainer').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            
            return false;
        } else {
            rolError.style.display = 'none';
        }
        
        // Validar contraseña si se está cambiando
        const password = document.getElementById('inputPassword').value;
        if (password.length > 0 && password.length < 6) {
            e.preventDefault();
            alert('La contraseña debe tener al menos 6 caracteres si va a cambiarla.');
            return false;
        }
    });
    
    // Ocultar mensaje de error cuando se selecciona un rol
    const radioButtons = document.querySelectorAll('input[name="rol_seleccionado"]');
    radioButtons.forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.checked) {
                rolError.style.display = 'none';
            }
        });
    });
    
    // Validación en tiempo real del nombre de usuario (sin espacios)
    const inputUsuario = document.getElementById('inputUsuario');
    if (inputUsuario) {
        inputUsuario.addEventListener('input', function() {
            // Remover espacios automáticamente
            this.value = this.value.replace(/\s/g, '');
        });
        
        inputUsuario.addEventListener('keypress', function(e) {
            // Prevenir espacios
            if (e.key === ' ') {
                e.preventDefault();
            }
        });
    }
    
    // Validación de contraseña mínima
    const inputPassword = document.getElementById('inputPassword');
    if (inputPassword) {
        inputPassword.addEventListener('blur', function() {
            if (this.value.length > 0 && this.value.length < 6) {
                this.setCustomValidity('La contraseña debe tener al menos 6 caracteres');
            } else {
                this.setCustomValidity('');
            }
        });
    }
});
</script>

<style>
.radio-list .radio {
    margin-bottom: 10px;
}

.radio-list .radio label {
    font-weight: normal;
    cursor: pointer;
    padding-left: 25px;
}

.radio-list .radio input[type="radio"] {
    margin-right: 8px;
}

#rol-error {
    margin-top: 5px;
    font-size: 12px;
}

/* Destacar rol seleccionado */
.radio input[type="radio"]:checked + label {
    color: #26B99A;
    font-weight: 500;
}

.form-control-static {
    padding: 7px 12px;
    margin-bottom: 0;
    background-color: #f9f9f9;
    border: 1px solid #e5e5e5;
    border-radius: 4px;
    font-size: 14px;
}
</style>