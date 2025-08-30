<!-- page content -->
			<div class="right_col" role="main">
				<div class="">
					<div class="page-title">
						<div class="title_left">
							<h3>Nuevo Tipo de Usuario</h3>
						</div>
					</div>

					<div class="clearfix"></div>

					<div class="row">
						<div class="col-md-12 ">
							<div class="x_panel">
								<div class="x_title">
									<h2>Datos de Tipo de Usuario <small></small></h2>

									<div class="clearfix"></div>
								</div>
								<div class="x_content">
									<br />
									<form class="form-horizontal form-label-left" action="tipo_usuario_nuevo.php" method="post">
										<div class="form-group row ">
											<label class="control-label col-md-3 col-sm-3 ">Nombre <span class="text-danger">*</span> :</label>
											<div class="col-md-9 col-sm-9 ">
												<input type="text" name="nom" class="form-control" placeholder="Nombre" required="required">
											</div>
										</div>

										<!-- Dashboard -->
										<div class="form-group row ">
											<label class="control-label col-md-3 col-sm-3 ">Dashboard:</label>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="ver_dashboard" class="js-switch"> Ver dashboard
												</label>
											</div>
											<div class="col-md-3 col-sm-3"></div>
											<div class="col-md-3 col-sm-3"></div>
										</div>
									

										<!-- Seguimiento -->
										<div class="form-group row ">
											<label class="control-label col-md-3 col-sm-3 ">Seguimiento:</label>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="ver_seguimiento" class="js-switch"> Ver seguimiento
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="reg_seguimiento" class="js-switch"> Registrar seguimiento
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="edi_seguimiento" class="js-switch"> Editar seguimiento
												</label>
											</div>
										</div>


										<!-- Uso de Material -->
										<div class="form-group row ">
											<label class="control-label col-md-3 col-sm-3 ">Uso de Material:</label>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="ver_uso_material" class="js-switch"> Ver uso de material
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="reg_uso_material" class="js-switch"> Registrar uso
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="edi_uso_material" class="js-switch"> Editar uso
												</label>
											</div>
										</div>

										<!-- Productos/Materiales -->
										<div class="form-group row ">
											<label class="control-label col-md-3 col-sm-3 ">Productos:</label>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="ver_producto" class="js-switch"> Ver productos
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="reg_producto" class="js-switch"> Registrar productos
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="edi_producto" class="js-switch"> Editar productos
												</label>
											</div>
										</div>

										<!-- Tipos de Producto -->
										<div class="form-group row ">
											<label class="control-label col-md-3 col-sm-3 ">Tipos de Producto:</label>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="ver_tipo_producto" class="js-switch"> Ver tipos
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="reg_tipo_producto" class="js-switch"> Registrar tipos
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="edi_tipo_producto" class="js-switch"> Editar tipos
												</label>
											</div>
										</div>

										<!-- Tipos de Material -->
										<div class="form-group row ">
											<label class="control-label col-md-3 col-sm-3 ">Tipos de Material:</label>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="ver_tipo_material" class="js-switch"> Ver tipos
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="reg_tipo_material" class="js-switch"> Registrar tipos
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="edi_tipo_material" class="js-switch"> Editar tipos
												</label>
											</div>
										</div>

										<!-- Unidades de Medida -->
										<div class="form-group row ">
											<label class="control-label col-md-3 col-sm-3 ">Unidades de Medida:</label>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="ver_unidad_medida" class="js-switch"> Ver unidades
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="reg_unidad_medida" class="js-switch"> Registrar unidades
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="edi_unidad_medida" class="js-switch"> Editar unidades
												</label>
											</div>
										</div>

										<!-- Pedidos -->
										<div class="form-group row ">
											<label class="control-label col-md-3 col-sm-3 ">Pedidos:</label>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="ver_pedido" class="js-switch"> Ver pedidos
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="reg_pedido" class="js-switch"> Registrar pedidos
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="edi_pedido" class="js-switch"> Editar pedidos
												</label>
											</div>
										</div>

										<!-- Compras -->
										<div class="form-group row ">
											<label class="control-label col-md-3 col-sm-3 ">Compras:</label>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="ver_compra" class="js-switch"> Ver compras
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="reg_compra" class="js-switch"> Registrar compras
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="edi_compra" class="js-switch"> Editar compras
												</label>
											</div>
										</div>

										<!-- Ingresos al Almacén -->
										<div class="form-group row ">
											<label class="control-label col-md-3 col-sm-3 ">Ingresos Almacén:</label>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="ver_ingreso" class="js-switch"> Ver ingresos
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="reg_ingreso" class="js-switch"> Registrar ingresos
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="edi_ingreso" class="js-switch"> Editar ingresos
												</label>
											</div>
										</div>

										<!-- Salidas del Almacén -->
										<div class="form-group row ">
											<label class="control-label col-md-3 col-sm-3 ">Salidas Almacén:</label>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="ver_salida" class="js-switch"> Ver salidas
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="reg_salida" class="js-switch"> Registrar salidas
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="edi_salida" class="js-switch"> Editar salidas
												</label>
											</div>
										</div>

										<!-- Devoluciones -->
										<div class="form-group row ">
											<label class="control-label col-md-3 col-sm-3 ">Devoluciones:</label>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="ver_devolucion" class="js-switch"> Ver devoluciones
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="reg_devolucion" class="js-switch"> Registrar devoluciones
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="edi_devolucion" class="js-switch"> Editar devoluciones
												</label>
											</div>
										</div>

										<!-- Almacén Arce -->
										<div class="form-group row ">
											<label class="control-label col-md-3 col-sm-3 ">Almacén Arce:</label>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="ver_almacen_arce" class="js-switch"> Ver Almacén Arce
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="edi_almacen_arce" class="js-switch"> Editar Almacén Arce
												</label>
											</div>
											<div class="col-md-3 col-sm-3"></div>
										</div>

										<!-- Personal -->
										<div class="form-group row ">
											<label class="control-label col-md-3 col-sm-3 ">Personal:</label>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="ver_personal" class="js-switch"> Ver personal
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="reg_personal" class="js-switch"> Registrar personal
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="edi_personal" class="js-switch"> Editar personal
												</label>
											</div>
										</div>

										<!-- Tipos de Usuario -->
										<div class="form-group row ">
											<label class="control-label col-md-3 col-sm-3 ">Tipos de Usuario:</label>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="ver_tipo_usuario" class="js-switch"> Ver tipos usuario
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="reg_tipo_usuario" class="js-switch"> Registrar tipos
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="edi_tipo_usuario" class="js-switch"> Editar tipos
												</label>
											</div>
										</div>

										<!-- Áreas -->
										<div class="form-group row ">
											<label class="control-label col-md-3 col-sm-3 ">Áreas:</label>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="ver_area" class="js-switch"> Ver áreas
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="reg_area" class="js-switch"> Registrar áreas
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="edi_area" class="js-switch"> Editar áreas
												</label>
											</div>
										</div>

										<!-- Cargos -->
										<div class="form-group row ">
											<label class="control-label col-md-3 col-sm-3 ">Cargos:</label>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="ver_cargo" class="js-switch"> Ver cargos
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="reg_cargo" class="js-switch"> Registrar cargos
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="edi_cargo" class="js-switch"> Editar cargos
												</label>
											</div>
										</div>

										<!-- Monedas -->
										<div class="form-group row ">
											<label class="control-label col-md-3 col-sm-3 ">Monedas:</label>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="ver_moneda" class="js-switch"> Ver monedas
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="reg_moneda" class="js-switch"> Registrar monedas
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="edi_moneda" class="js-switch"> Editar monedas
												</label>
											</div>
										</div>

										<!-- Almacenes -->
										<div class="form-group row ">
											<label class="control-label col-md-3 col-sm-3 ">Almacenes:</label>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="ver_almacen" class="js-switch"> Ver almacenes
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="reg_almacen" class="js-switch"> Registrar almacenes
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="edi_almacen" class="js-switch"> Editar almacenes
												</label>
											</div>
										</div>

										<!-- Ubicaciones -->
										<div class="form-group row ">
											<label class="control-label col-md-3 col-sm-3 ">Ubicaciones:</label>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="ver_ubicacion" class="js-switch"> Ver ubicaciones
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="reg_ubicacion" class="js-switch"> Registrar ubicaciones
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="edi_ubicacion" class="js-switch"> Editar ubicaciones
												</label>
											</div>
										</div>

										<!-- Obras -->
										<div class="form-group row ">
											<label class="control-label col-md-3 col-sm-3 ">Obras:</label>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="ver_obra" class="js-switch"> Ver obras
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="reg_obra" class="js-switch"> Registrar obras
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="edi_obra" class="js-switch"> Editar obras
												</label>
											</div>
										</div>

										<!-- Proveedores -->
										<div class="form-group row ">
											<label class="control-label col-md-3 col-sm-3 ">Proveedores:</label>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="ver_proveedor" class="js-switch"> Ver proveedores
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="reg_proveedor" class="js-switch"> Registrar proveedores
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="edi_proveedor" class="js-switch"> Editar proveedores
												</label>
											</div>
										</div>

										<!-- Clientes -->
										<div class="form-group row ">
											<label class="control-label col-md-3 col-sm-3 ">Clientes:</label>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="ver_cliente" class="js-switch"> Ver clientes
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="reg_cliente" class="js-switch"> Registrar clientes
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="edi_cliente" class="js-switch"> Editar clientes
												</label>
											</div>
										</div>


										<!-- Mantenimiento -->
										<div class="form-group row ">
											<label class="control-label col-md-3 col-sm-3 ">Mantenimiento:</label>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="ver_mantenimiento" class="js-switch"> Ver mantenimiento
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="reg_mantenimiento" class="js-switch"> Registrar mantenimiento
												</label>
											</div>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="edi_mantenimiento" class="js-switch"> Editar mantenimiento
												</label>
											</div>
										</div>

										<!-- Auditoria -->
										<div class="form-group row ">
											<label class="control-label col-md-3 col-sm-3 ">Auditoria:</label>
											<div class="col-md-3 col-sm-3">
												<label>
													<input type="checkbox" name="ver_auditoria" class="js-switch"> Ver auditoria
												</label>
											</div>
											<div class="col-md-3 col-sm-3"></div>
											<div class="col-md-3 col-sm-3"></div>
										</div>

										<div class="form-group row">
											<label class="control-label col-md-3 col-sm-3 ">Estado:</label>
											<div class="col-md-9 col-sm-9 ">
												<label>
													<input type="checkbox" name="est" class="js-switch" checked> Activo
												</label>
											</div>
										</div>

										<div class="ln_solid"></div>

										<div class="form-group">
											<div class="col-md-2 col-sm-2  offset-md-10">
												<button type="submit" name="registrar" class="btn btn-success btn-block">Registrar</button>
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
				let reg_normas = document.getElementById('reg_normas');
				let edi_normas = document.getElementById('edi_normas');
				let pass_normas = document.getElementById('pass_normas');

				reg_normas.addEventListener('change', function() {
					if (reg_normas.checked || edi_normas.checked) {
						pass_normas.setAttribute('required', 'required');
					} else {
						pass_normas.removeAttribute('required');
					}
				});
				
				edi_normas.addEventListener('change', function() {
					if (reg_normas.checked || edi_normas.checked) {
						pass_normas.setAttribute('required', 'required');
					} else {
						pass_normas.removeAttribute('required');
					}
				});
			</script>