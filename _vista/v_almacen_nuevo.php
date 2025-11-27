<?php
// Vista para registrar nuevo almacén - v_almacen_nuevo.php

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
                                <label class="control-label col-md-3 col-sm-3">Nombre del Almacén <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" id="nom_almacen" name="nom" class="form-control" placeholder="Nombre del almacén" readonly required="required">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Cliente <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="id_cliente" id="select_cliente" class="form-control" required="required">
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
                                    <select name="id_obra" id="select_obra" class="form-control" required="required">
                                        <option value="">Seleccione una obra</option>
                                        <?php
                                        if ($listaObras && count($listaObras) > 0) {
                                            foreach ($listaObras as $obra) {
                                                echo '<option value="' . $obra['id_subestacion'] . '">' . htmlspecialchars($obra['nom_subestacion']) . '</option>';
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
                                            <input type="checkbox" name="est" class="js-switch" checked> Activo
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 offset-md-8">
                                    <a href="almacen_mostrar.php" class="btn btn-outline-danger btn-block">
                                        Cancelar
                                    </a>
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


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {

        function actualizarNombreAlmacen() {
            // Obtener texto de obra
            var obraText = "";
            if ($("#select_obra").hasClass("select2-hidden-accessible")) {
                var obraData = $("#select_obra").select2('data');
                obraText = obraData.length ? obraData[0].text : "";
            } else {
                obraText = $("#select_obra option:selected").text() || "";
            }

            // Ignorar texto de placeholder
            if (obraText.toLowerCase() === "seleccione una obra") obraText = "";

            // Obtener texto de cliente
            var clienteText = $("#select_cliente option:selected").text() || "";
            if (clienteText.toLowerCase() === "seleccione un cliente") clienteText = "";

            // Construir nombre
            var nombre = "";
            if (obraText && !clienteText) {
                nombre = obraText + " 'CLIENTE'";
            } else if (!obraText && clienteText) {
                nombre = "'OBRA' " + clienteText;
            } else if (obraText && clienteText) {
                nombre = obraText + " " + clienteText;
            }

            $("#nom_almacen").val(nombre.toUpperCase());
        }

        // Eventos
        $("#select_cliente").on("change", actualizarNombreAlmacen);
        $("#select_obra").on("select2:select select2:unselect", actualizarNombreAlmacen);

        // Inicializar al cargar
        actualizarNombreAlmacen();
    });
</script>