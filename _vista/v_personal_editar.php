<?php
//=======================================================================
// VISTA: v_personal_editar.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Personal</title>
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
<div class="container body">
    <div class="main_container">
        <?php
        require_once("../_vista/v_menu.php");
        require_once("../_vista/v_menu_user.php");
        ?>

        <!-- CONTENIDO PRINCIPAL -->
        <div class="right_col" role="main">
            <div class="">
                <div class="page-title">
                    <div class="title_left">
                        <h3>Editar Personal</h3>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>Datos de Personal <small>Modificar información</small></h2>
                                <div class="clearfix"></div>
                            </div>

                            <div class="x_content">
                                <br>
                                <form class="form-horizontal form-label-left"
                                      method="post"
                                      action="personal_editar.php?id_personal=<?php echo $id_personal; ?>">

                                    <!-- NOMBRE -->
                                    <div class="form-group row">
                                        <label class="control-label col-md-3 col-sm-3">
                                            Nombre <span class="text-danger">*</span> :
                                        </label>
                                        <div class="col-md-9 col-sm-9">
                                            <input type="text" name="nom" class="form-control"
                                                   value="<?php echo htmlspecialchars($nom); ?>" required>
                                        </div>
                                    </div>

                                    <!-- DNI -->
                                    <div class="form-group row">
                                        <label class="control-label col-md-3 col-sm-3">
                                            DNI <span class="text-danger">*</span> :
                                        </label>
                                        <div class="col-md-9 col-sm-9">
                                            <input type="text" name="dni" maxlength="8"
                                                   class="form-control"
                                                   value="<?php echo htmlspecialchars($dni); ?>" required>
                                        </div>
                                    </div>

                                    <!-- ÁREA -->
                                    <div class="form-group row">
                                        <label class="control-label col-md-3 col-sm-3">
                                            Área <span class="text-danger">*</span> :
                                        </label>
                                        <div class="col-md-9 col-sm-9">
                                            <select name="id_area" class="form-control select2_single" required>
                                                <option value="">Seleccione un área</option>
                                                <?php foreach ($areas as $a): ?>
                                                    <option value="<?php echo $a['id_area']; ?>"
                                                        <?php echo ($a['id_area'] == $id_area) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($a['nom_area']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- CARGO -->
                                    <div class="form-group row">
                                        <label class="control-label col-md-3 col-sm-3">
                                            Cargo <span class="text-danger">*</span> :
                                        </label>
                                        <div class="col-md-9 col-sm-9">
                                            <select name="id_cargo" class="form-control select2_single" required>
                                                <option value="">Seleccione un cargo</option>
                                                <?php foreach ($cargos as $c): ?>
                                                    <option value="<?php echo $c['id_cargo']; ?>"
                                                        <?php echo ($c['id_cargo'] == $id_cargo) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($c['nom_cargo']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- EMAIL -->
                                    <div class="form-group row">
                                        <label class="control-label col-md-3 col-sm-3">Email</label>
                                        <div class="col-md-9 col-sm-9">
                                            <input type="email" name="email" class="form-control"
                                                   value="<?php echo htmlspecialchars($email); ?>">
                                        </div>
                                    </div>

                                    <!-- CELULAR -->
                                    <div class="form-group row">
                                        <label class="control-label col-md-3 col-sm-3">Teléfono</label>
                                        <div class="col-md-9 col-sm-9">
                                            <input type="text" name="cel" maxlength="9"
                                                   class="form-control"
                                                   value="<?php echo htmlspecialchars($cel); ?>">
                                        </div>
                                    </div>

                                    <!-- Estado -->
                                    <div class="form-group row">
                                        <label class="control-label col-md-3 col-sm-3">Estado :</label>
                                        <div class="col-md-9 col-sm-9">
                                            <label>
                                                <input type="checkbox" name="est" class="js-switch" <?php echo $est; ?>> Activo
                                            </label>
                                        </div>
                                    </div>

                                    <div class="ln_solid"></div>

                                    <!-- BOTONES -->
                                    <div class="form-group">
                                        <div class="col-md-2 col-sm-2 offset-md-8">
                                            <a href="personal_mostrar.php" class="btn btn-outline-danger btn-block">
                                                Cancelar
                                            </a>
                                        </div>
                                        <div class="col-md-2 col-sm-2">
                                            <button type="submit" name="registrar" class="btn btn-success btn-block">Actualizar</button>
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

        <?php require_once("../_vista/v_footer.php"); ?>
    </div>
</div>

<?php
require_once("../_vista/v_script.php");
require_once("../_vista/v_alertas.php");
?>

<!-- Librerías Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Inicialización de Select2 -->
<script>
$(document).ready(function() {
    $('.select2_single').select2({
        placeholder: "Seleccione una opción",
        allowClear: true,
        width: '100%'
    });
});
</script>

</body>
</html>
