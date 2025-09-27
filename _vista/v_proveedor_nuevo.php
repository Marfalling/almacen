<?php
// Vista para registrar nuevo proveedor - v_proveedor_nuevo.php
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Nuevo Proveedor</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Datos del Proveedor <small></small></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br>
                        <form class="form-horizontal form-label-left" action="proveedor_nuevo.php" method="post">
                            
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Nombre <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="nom" class="form-control" placeholder="Nombre del proveedor" required="required">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">RUC <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="ruc" class="form-control" placeholder="RUC del proveedor" required="required" maxlength="11">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Dirección <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <textarea name="dir" class="form-control" rows="3" placeholder="Dirección del proveedor" required="required"></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Teléfono <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="tel" class="form-control" placeholder="Teléfono del proveedor" required="required">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Contacto <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="cont" class="form-control" placeholder="Persona de contacto" required="required">
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Email </label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="email" name="email" class="form-control" placeholder="Correo electrónico">
                                </div>
                            </div>

                            <!-- Item -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Item </label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="number" name="item" class="form-control" placeholder="Item">
                                </div>
                            </div>

                            <!-- Banco -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Banco </label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="banco" class="form-control" placeholder="Nombre del banco">
                                </div>
                            </div>

                            <!-- Moneda (selecta) -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Moneda </label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="id_moneda" class="form-control">
                                        <option value="">-- Seleccione Moneda --</option>
                                        <?php foreach ($monedas as $m) { ?>
                                            <option value="<?php echo $m['id_moneda']; ?>">
                                                <?php echo $m['nom_moneda']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Nro Cuenta Corriente -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Cuenta Corriente </label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="nro_cuenta_corriente" class="form-control" placeholder="Número de cuenta corriente">
                                </div>
                            </div>

                            <!-- Nro Cuenta Interbancaria -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Cuenta Interbancaria </label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="nro_cuenta_interbancaria" class="form-control" placeholder="Número de cuenta interbancaria">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Estado </label>
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