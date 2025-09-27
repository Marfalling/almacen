<?php
//=======================================================================
// VISTA: v_proveedor_editar.php
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Editar Proveedor</h3>
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
                        <form class="form-horizontal form-label-left" action="proveedor_editar.php" method="post">
                            
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Nombre <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="nom" value="<?php echo $nom; ?>" class="form-control" placeholder="Nombre del proveedor" required="required">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">RUC <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="ruc" value="<?php echo $ruc; ?>" class="form-control" placeholder="RUC del proveedor" required="required" maxlength="11">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Dirección <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <textarea name="dir" class="form-control" rows="3" placeholder="Dirección del proveedor" required="required"><?php echo $dir; ?></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Teléfono <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="tel" value="<?php echo $tel; ?>" class="form-control" placeholder="Teléfono del proveedor" required="required">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Contacto <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="cont" value="<?php echo $cont; ?>" class="form-control" placeholder="Persona de contacto" required="required">
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Email <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="email" name="email" value="<?php echo $email; ?>" class="form-control" placeholder="Correo electrónico" required="required">
                                </div>
                            </div>

                            <!-- Item -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Item <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="number" name="item" value="<?php echo $item; ?>" class="form-control" placeholder="Item" required="required">
                                </div>
                            </div>

                            <!-- Banco -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Banco <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="banco" value="<?php echo $banco; ?>" class="form-control" placeholder="Nombre del banco" required="required">
                                </div>
                            </div>

                            <!-- Moneda -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Moneda <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="id_moneda" class="form-control" required="required">
                                        <option value="">-- Seleccione Moneda --</option>
                                        <?php
                                        foreach ($monedas as $m) {
                                            $selected = ($m['id_moneda'] == $id_moneda) ? "selected" : "";
                                            echo "<option value='{$m['id_moneda']}' {$selected}>{$m['nom_moneda']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Cuenta Corriente -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Cuenta Corriente <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="nro_cuenta_corriente" value="<?php echo $nro_cuenta_corriente; ?>" class="form-control" placeholder="Número de cuenta corriente" required="required">
                                </div>
                            </div>

                            <!-- Cuenta Interbancaria -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Cuenta Interbancaria <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="nro_cuenta_interbancaria" value="<?php echo $nro_cuenta_interbancaria; ?>" class="form-control" placeholder="Número de cuenta interbancaria" required="required">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Estado:</label>
                                <div class="col-md-9 col-sm-9">
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
                            <input type="hidden" name="id_proveedor" value="<?php echo $id_proveedor; ?>">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->