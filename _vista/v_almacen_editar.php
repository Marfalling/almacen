<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Editar Almacén</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Datos del Almacén <small></small></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br>
                        <form class="form-horizontal form-label-left" action="almacen_editar.php" method="post">
                            
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Nombre del Almacén <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="nom" value="<?php echo $nom; ?>" class="form-control" placeholder="Nombre del almacén" required="required">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Cliente <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="id_cliente" class="form-control" required="required">
                                        <option value="">Seleccione un cliente</option>
                                        <?php
                                        if ($listaClientes && count($listaClientes) > 0) {
                                            foreach ($listaClientes as $cliente) {
                                                $selected = ($cliente['id_cliente'] == $id_cliente) ? 'selected' : '';
                                                echo '<option value="' . $cliente['id_cliente'] . '" ' . $selected . '>' . htmlspecialchars($cliente['nom_cliente']) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Obra <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="id_obra" class="form-control" required="required">
                                        <option value="">Seleccione una obra</option>
                                        <?php
                                        if ($listaObras && count($listaObras) > 0) {
                                            foreach ($listaObras as $obra) {
                                                $selected = ($obra['id_obra'] == $id_obra) ? 'selected' : '';
                                                echo '<option value="' . $obra['id_subestacion'] . '" ' . $selected . '>' . htmlspecialchars($obra['nom_subestacion']) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
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
                            <input type="hidden" name="id_almacen" value="<?php echo $id_almacen; ?>">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->