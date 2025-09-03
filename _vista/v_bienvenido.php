<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Bienvenido<small></small></h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <!-- --------------------------------------- -->
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <div class="row">
                            <div class="col-sm-12">
                                <h2>¡Bienvenido al Sistema ARCE PERÚ!<small></small></h2>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>

                    <div class="x_content">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-box">
                                    <div class="text-center">
                                        <!-- Imagen de perfil -->
                                        <div class="profile_pic" style="margin: 20px auto;">
                                            <img src="../_complemento/images/img.jpg" alt="Perfil" class="img-circle profile_img" style="width: 150px; height: 150px;">
                                        </div>
                                        
                                        <!-- Información del usuario -->
                                        <div class="profile_info" style="margin: 20px 0;">
                                            <h1 style="color: #73879C; font-size: 36px; margin-bottom: 10px;">
                                                <?php echo $usuario_sesion; ?>
                                            </h1>
                                            
                                            <div style="margin: 20px 0;">
                                                <h3 style="color: #2A3F54; margin: 10px 0;">
                                                    <strong>Cargo:</strong> <?php echo $cargo_sesion; ?>
                                                </h3>
                                                
                                                <?php if (!empty($area_sesion)): ?>
                                                <h3 style="color: #2A3F54; margin: 10px 0;">
                                                    <strong>Área:</strong> <?php echo $area_sesion; ?>
                                                </h3>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- Mensaje de bienvenida -->
                                            <div style="margin: 30px 0; padding: 20px; background-color: #f5f5f5; border-radius: 10px;">
                                                <p style="font-size: 18px; color: #555; margin: 0;">
                                                    Has iniciado sesión exitosamente en el sistema ARCE PERÚ.<br>
                                                    Utiliza el menú lateral para acceder a las funciones disponibles según tus permisos.
                                                </p>
                                            </div>

                                            <!-- Información adicional -->
                                            <div style="margin-top: 30px;">
                                                <small style="color: #999;">
                                                    Fecha y hora de acceso: <?php echo date('d/m/Y H:i:s'); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- --------------------------------------- -->
        </div>
    </div>
</div>
<!-- /page content -->