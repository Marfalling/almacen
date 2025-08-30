<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Nuevo Rol</h3>
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
							<form class="form-horizontal form-label-left" action="rol_usuario_nuevo.php" method="POST">
								
								<!-- Nombre del rol -->
								<div class="form-group row ">
									<label class="control-label col-md-3 col-sm-3 ">Nombre del Rol <span class="text-danger">*</span> :</label>
									<div class="col-md-9 col-sm-9 ">
										<input type="text" name="nom_rol" class="form-control" placeholder="Nombre del rol" required="required" maxlength="100">
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
										?>
											<div class="accordion" id="permisosAccordion">
												<?php foreach($modulos_agrupados as $modulo => $acciones) { ?>
													<div class="card">
														<div class="card-header">
															<h5><?php echo $modulo; ?></h5>
														</div>
														<div class="card-body">
															<div class="row">
																<?php foreach($acciones as $accion) { ?>
																	<div class="col-md-4 col-sm-6 mb-2">
																		<div class="checkbox">
																			<label>
																				<input type="checkbox" name="permisos[]" value="<?php echo $accion['id_modulo_accion']; ?>">
																				<?php echo $accion['nom_accion']; ?>
																			</label>
																		</div>
																	</div>
																<?php } ?>
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
												<input type="checkbox" name="est" checked> Activo
											</label>
										</div>
									</div>
								</div>

								<!-- Botones -->
								<div class="ln_solid"></div>
								<div class="form-group">
									<div class="col-md-2 col-sm-2  offset-md-8">
										<button type="reset" class="btn btn-outline-danger btn-block">Limpiar</button>
									</div>
									<div class="col-md-2 col-sm-2">
										<button type="submit" name="registrar" value="1" class="btn btn-success btn-block">Registrar</button>
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
