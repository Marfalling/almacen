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

        <div class="right_col" role="main">
            <div class="">
                <div class="page-title">
                    <div class="title_left">
                        <h3>Editar Personal</h3>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="row">
                    <div class="col-md-12 col-sm-12 ">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>Personal <small>Modificar datos</small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <br>
                                <form class="form-horizontal form-label-left" 
                                      action="personal_editar.php?id_personal=<?php echo $id_personal; ?>" 
                                      method="post">

                                    <!-- Área -->
                                    <div class="form-group row">
                                        <label class="control-label col-md-3 col-sm-3">Área *</label>
                                        <div class="col-md-9 col-sm-9">
                                            <select name="id_area" class="form-control" required>
                                                <option value="">Seleccione un área</option>
                                                <?php foreach ($areas as $area): ?>
                                                    <option value="<?php echo $area['id_area']; ?>" 
                                                        <?php echo ($area['id_area'] == $id_area) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($area['nom_area']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Cargo -->
                                    <div class="form-group row">
                                        <label class="control-label col-md-3 col-sm-3">Cargo *</label>
                                        <div class="col-md-9 col-sm-9">
                                            <select name="id_cargo" class="form-control" required>
                                                <option value="">Seleccione un cargo</option>
                                                <?php foreach ($cargos as $cargo): ?>
                                                    <option value="<?php echo $cargo['id_cargo']; ?>" 
                                                        <?php echo ($cargo['id_cargo'] == $id_cargo) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($cargo['nom_cargo']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Nombre -->
                                    <div class="form-group row">
                                        <label class="control-label col-md-3 col-sm-3">Nombre *</label>
                                        <div class="col-md-9 col-sm-9">
                                            <input type="text" name="nom" class="form-control" 
                                                   value="<?php echo htmlspecialchars($nom); ?>" required>
                                        </div>
                                    </div>

                                    <!-- DNI -->
                                    <div class="form-group row">
                                        <label class="control-label col-md-3 col-sm-3">DNI *</label>
                                        <div class="col-md-9 col-sm-9">
                                            <input type="text" name="dni" class="form-control" maxlength="20" 
                                                   value="<?php echo htmlspecialchars($dni); ?>" required>
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="form-group row">
                                        <label class="control-label col-md-3 col-sm-3">Email</label>
                                        <div class="col-md-9 col-sm-9">
                                            <input type="email" name="email" class="form-control" 
                                                   value="<?php echo htmlspecialchars($email); ?>">
                                        </div>
                                    </div>

                                    <!-- Teléfono -->
                                    <div class="form-group row">
                                        <label class="control-label col-md-3 col-sm-3">Celular</label>
                                        <div class="col-md-9 col-sm-9">
                                            <input type="text" name="cel" class="form-control" 
                                                   value="<?php echo htmlspecialchars($cel); ?>">
                                        </div>
                                    </div>

                                    <!-- Estado -->
                                    <div class="form-group row">
                                        <label class="control-label col-md-3 col-sm-3">Estado</label>
                                        <div class="col-md-9 col-sm-9">
                                            <input type="checkbox" name="est" <?php echo $est; ?>> Activo
                                        </div>
                                    </div>

                                    <div class="ln_solid"></div>

                                    <!-- Botones -->
                                    <div class="form-group">
                                        <div class="col-md-2 col-sm-2 offset-md-10">
                                            <button type="submit" name="registrar" class="btn btn-warning btn-block">
                                                Actualizar
                                            </button>
                                        </div>
                                        <div class="col-md-2 col-sm-2 offset-md-10 mt-2">
                                            <a href="personal_mostrar.php" class="btn btn-secondary btn-block">Cancelar</a>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <?php require_once("../_vista/v_footer.php"); ?>
    </div>
</div>
<?php require_once("../_vista/v_script.php"); ?>
</body>
</html>
