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
                        <h2>Datos del Proveedor</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <form class="form-horizontal form-label-left" action="proveedor_editar.php" method="post">
                            <!-- Datos principales -->
                            <input type="text" name="nom" value="<?php echo $nom; ?>" class="form-control" placeholder="Nombre" required><br>
                            <input type="text" name="ruc" value="<?php echo $ruc; ?>" class="form-control" placeholder="RUC" maxlength="11" required><br>
                            <textarea name="dir" class="form-control" placeholder="Dirección" required><?php echo $dir; ?></textarea><br>
                            <input type="text" name="tel" value="<?php echo $tel; ?>" class="form-control" placeholder="Teléfono" required><br>
                            <input type="text" name="cont" value="<?php echo $cont; ?>" class="form-control" placeholder="Contacto" required><br>
                            <input type="email" name="email" value="<?php echo $email; ?>" class="form-control" placeholder="Correo"><br>

                            <!-- Cuentas bancarias -->
                            <h4>Cuentas Bancarias</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Banco</th>
                                        <th>Moneda</th>
                                        <th>Cuenta Corriente</th>
                                        <th>Cuenta Interbancaria</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="tabla-cuentas">
                                    <?php foreach ($cuentas as $c) { ?>
                                        <tr>
                                            <td><input type="text" name="banco[]" value="<?php echo $c['banco_proveedor']; ?>" class="form-control" required></td>
                                            <td>
                                                <select name="id_moneda[]" class="form-control" required>
                                                    <option value="">-- Moneda --</option>
                                                    <?php foreach ($monedas as $m) {
                                                        $selected = ($m['id_moneda'] == $c['id_moneda']) ? "selected" : "";
                                                        echo "<option value='{$m['id_moneda']}' {$selected}>{$m['nom_moneda']}</option>";
                                                    } ?>
                                                </select>
                                            </td>
                                            <td><input type="text" name="cta_corriente[]" value="<?php echo $c['nro_cuenta_corriente']; ?>" class="form-control" required></td>
                                            <td><input type="text" name="cta_interbancaria[]" value="<?php echo $c['nro_cuenta_interbancaria']; ?>" class="form-control" required></td>
                                            <td><button type="button" class="btn btn-danger btn-sm eliminar-fila">X</button></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-success btn-sm" id="agregarCuenta">+ Agregar Cuenta</button><br><br>

                            <!-- Estado -->
                            <label><input type="checkbox" name="est" class="js-switch" <?php echo $est; ?>> Activo</label><br><br>

                            <!-- Botones -->
                            <button type="submit" name="registrar" class="btn btn-warning">Actualizar</button>

                            <!-- Campo oculto -->
                            <input type="hidden" name="id_proveedor" value="<?php echo $id_proveedor; ?>">
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
                <select name="id_moneda[]" class="form-control" required>
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
    });

    // Acción para eliminar fila
    tablaCuentas.addEventListener("click", function(e) {
        if (e.target.classList.contains("eliminar-fila")) {
            e.target.closest("tr").remove();
        }
    });
});
</script>
