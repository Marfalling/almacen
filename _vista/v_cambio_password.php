<?php 
//=======================================================================
// VISTA: v_cambio_password.php 
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">

        <!-- Título centrado arriba del formulario -->
        <div class="page-title text-center" style="margin-bottom: 60px;">
            <h3>Cambiar Contraseña</h3>
        </div>

        <!-- Centramos el contenido con Bootstrap -->
        <div class="row justify-content-center">
            <div class="col-md-6 col-sm-8 col-xs-12">
                <div class="x_panel">
                    <div class="x_title text-center">
                        <h2>Actualizar Contraseña</h2>
                        <div class="clearfix"></div>
                    </div>

                    <div class="x_content">
                        <?php echo $mensaje ?? ''; ?>

                        <form method="POST" autocomplete="off">
                            <div class="form-group">
                                <label for="password_actual">Contraseña actual:</label>
                                <input type="password" id="password_actual" name="password_actual" 
                                       class="form-control" required 
                                       placeholder="Ingrese su contraseña actual">
                            </div>

                            <div class="form-group">
                                <label for="password_nueva">Nueva contraseña:</label>
                                <input type="password" id="password_nueva" name="password_nueva" 
                                       class="form-control" required minlength="5" 
                                       placeholder="Ingrese nueva contraseña">
                            </div>

                            <div class="form-group">
                                <label for="password_confirmar">Confirmar nueva contraseña:</label>
                                <input type="password" id="password_confirmar" name="password_confirmar" 
                                       class="form-control" required minlength="5" 
                                       placeholder="Confirme la nueva contraseña">
                            </div>

                            <div class="ln_solid"></div>

                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-success">Guardar</button>
                                <a href="bienvenido.php" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- /page content -->
