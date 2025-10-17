<?php
//=======================================================================
// VISTA: v_proveedor_nuevo.php
//=======================================================================
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
                        <h2>Datos del Proveedor</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <form class="form-horizontal form-label-left" action="proveedor_nuevo.php" method="post">
                            
                            <!-- Nombre -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Nombre <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="nom" class="form-control" placeholder="Nombre del proveedor" required>
                                </div>
                            </div>

                            <!-- RUC -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">RUC <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="ruc" class="form-control" placeholder="RUC del proveedor" maxlength="11" required>
                                </div>
                            </div>

                            <!-- Dirección -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Dirección <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <textarea name="dir" class="form-control" rows="3" placeholder="Dirección del proveedor" required></textarea>
                                </div>
                            </div>

                            <!-- Teléfono -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Teléfono <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="tel" class="form-control" placeholder="Teléfono del proveedor" required>
                                </div>
                            </div>

                            <!-- Contacto -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Contacto <span class="text-danger">*</span> :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="cont" class="form-control" placeholder="Persona de contacto" required>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Email :</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="email" name="email" class="form-control" placeholder="Correo electrónico">
                                </div>
                            </div>

                            <!-- Cuentas Bancarias -->
                            <div class="x_panel">
                                <div class="x_title">
                                    <h2>Cuentas Bancarias</h2>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Banco</th>
                                                <th>Moneda</th>
                                                <th>Cuenta Corriente</th>
                                                <th>Cuenta Interbancaria</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabla-cuentas">
                                            <tr>
                                                <td><input type="text" name="banco[]" class="form-control" required></td>
                                                <td>
                                                    <select name="id_moneda[]" class="form-control select2_single" required>
                                                        <option value="">-- Moneda --</option>
                                                        <?php foreach ($monedas as $m) { ?>
                                                            <option value="<?php echo $m['id_moneda']; ?>"><?php echo $m['nom_moneda']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <td><input type="text" name="cta_corriente[]" class="form-control" required></td>
                                                <td><input type="text" name="cta_interbancaria[]" class="form-control" required></td>
                                                <td><button type="button" class="btn btn-danger btn-sm eliminar-fila">X</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-success btn-sm" id="agregarCuenta">+ Agregar Cuenta</button>
                                </div>
                            </div> 

                            <!-- Estado -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Estado :</label>
                                <div class="col-md-9 col-sm-9">
                                    <label>
                                        <input type="checkbox" name="est" class="js-switch" checked> Activo
                                    </label>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <!-- Botones -->
                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 offset-md-8">
                                    <button type="reset" class="btn btn-outline-danger btn-block">Limpiar</button>
                                </div>
                                <div class="col-md-2 col-sm-2">
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

<!-- Script dinámico para manejar cuentas bancarias -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const tablaCuentas = document.getElementById("tabla-cuentas");
    const btnAgregar = document.getElementById("agregarCuenta");

    // Acción para agregar nueva fila
    btnAgregar.addEventListener("click", function() {
        const nuevaFila = document.createElement("tr");
        nuevaFila.innerHTML = `
            <td><input type="text" name="banco[]" class="form-control" required></td>
            <td>
                <select name="id_moneda[]" class="form-control select2_single" required>
                    <option value="">-- Moneda --</option>
                    <?php foreach ($monedas as $m) { ?>
                        <option value="<?php echo $m['id_moneda']; ?>"><?php echo $m['nom_moneda']; ?></option>
                    <?php } ?>
                </select>
            </td>
            <td><input type="text" name="cta_corriente[]" class="form-control" required></td>
            <td><input type="text" name="cta_interbancaria[]" class="form-control" required></td>
            <td><button type="button" class="btn btn-danger btn-sm eliminar-fila">X</button></td>
        `;
        tablaCuentas.appendChild(nuevaFila);

        // Inicializar Select2 en la nueva fila
        $(nuevaFila).find('.select2_single').select2({
            placeholder: "Seleccione una moneda",
            allowClear: true,
            width: '100%'
        });
    });

    // Acción para eliminar fila
    tablaCuentas.addEventListener("click", function(e) {
        if (e.target.classList.contains("eliminar-fila")) {
            e.target.closest("tr").remove();
        }
    });

    // Inicialización inicial de Select2
    $('.select2_single').select2({
        placeholder: "Seleccione una moneda",
        allowClear: true,
        width: '100%'
    });
});
</script>

<!-- Librerías Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
