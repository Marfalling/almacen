<?php
// Vista para registrar nuevo almacén - v_almacen_nuevo.php

// Incluir modelos necesarios para obtener listas de clientes y obras
require_once("../_modelo/m_clientes.php");
require_once("../_modelo/m_obras.php");

// Obtener listas para los selects (solo activos)
$listaClientes = MostrarClientesActivos();
$listaObras = MostrarObrasActivas();
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Nuevo Almacén</h3>
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
                        <form class="form-horizontal form-label-left" action="almacen_nuevo.php" method="post">
                            
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Cliente <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="id_cliente" class="form-control" required="required">
                                        <option value="">Seleccione un cliente</option>
                                        <?php
                                        if ($listaClientes && count($listaClientes) > 0) {
                                            foreach ($listaClientes as $cliente) {
                                                echo '<option value="' . $cliente['id_cliente'] . '">' . htmlspecialchars($cliente['nom_cliente']) . '</option>';
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
                                                echo '<option value="' . $obra['id_obra'] . '">' . htmlspecialchars($obra['nom_obra']) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Nombre del Almacén <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="nom" class="form-control" placeholder="Nombre del almacén" required="required">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Estado:</label>
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