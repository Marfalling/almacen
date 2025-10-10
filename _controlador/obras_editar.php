</div>
<<?php
//=======================================================================
// CONTROLADOR: obras_editar.php 
//=======================================================================
require_once("../_conexion/sesion.php");

//Verificar permisos
if (!verificarPermisoEspecifico('editar_obras')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'OBRAS', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

require_once("../_modelo/m_obras.php");

//Si se envía el formulario
if (isset($_POST['registrar'])) {
    $id_obra = intval($_POST['id_obra']);
    $nom = strtoupper(trim($_POST['nom']));
    $est = isset($_POST['est']) ? 1 : 0;

    $rpta = ActualizarObra($id_obra, $nom, $est);

    if ($rpta == "SI") {
        header("Location: obras_mostrar.php?actualizado=true");
        exit;
    } elseif ($rpta == "NO") {
        header("Location: obras_mostrar.php?existe=true");
        exit;
    } else {
        header("Location: obras_mostrar.php?error=true");
        exit;
    }
}

//Obtener obra a editar
$id_obra = isset($_GET['id_obra']) ? intval($_GET['id_obra']) : 0;

// Consultar obra/subestacion por ID
$obra = ConsultarObra($id_obra);
if (!$obra) {
    header("Location: obras_mostrar.php?error=true");
    exit;
}

$nom = $obra['nom_subestacion'];
$est = ($obra['act_subestacion'] == 1) ? "checked" : "";

require_once("../_modelo/m_auditoria.php");
GrabarAuditoria($id, $usuario_sesion, 'INGRESO', 'OBRAS', 'EDITAR');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Editar Obra</title>
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
<div class="container body">
    <div class="main_container">
        <?php
        require_once("../_vista/v_menu.php");
        require_once("../_vista/v_menu_user.php");
        ?>

        <!-- ==========================
             VISTA: Editar Obra
        ========================== -->
        <div class="right_col" role="main">
            <div class="">
                <div class="page-title">
                    <div class="title_left">
                        <h3>Editar Obra / Subestación</h3>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="row">
                    <div class="col-md-12 col-sm-12 ">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>Obra <small>Modificar datos</small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <br>
                                <form class="form-horizontal form-label-left" action="obras_editar.php" method="post">
                                    <div class="form-group row">
                                        <label class="control-label col-md-3 col-sm-3">
                                            Nombre de la Obra <span class="text-danger">*</span> :
                                        </label>
                                        <div class="col-md-9 col-sm-9">
                                            <input type="text" name="nom" 
                                                   value="<?php echo htmlspecialchars($nom ?? ''); ?>" 
                                                   class="form-control" placeholder="Nombre de la obra" required="required">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="control-label col-md-3 col-sm-3">
                                            Estado:
                                        </label>
                                        <div class="col-md-9 col-sm-9">
                                            <label>
                                                <input type="checkbox" name="est" class="js-switch" <?php echo $est; ?>> Activo
                                            </label>
                                        </div>
                                    </div>

                                    <div class="ln_solid"></div>

                                    <div class="form-group">
                                        <div class="col-md-2 col-sm-2 offset-md-10">
                                            <button type="submit" name="registrar" class="btn btn-warning btn-block">
                                                Actualizar
                                            </button>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-12 col-sm-12">
                                            <p><span class="text-danger">*</span> Los campos con (<span class="text-danger">*</span>) son obligatorios.</p>
                                        </div>
                                    </div>

                                    <!-- Campo oculto -->
                                    <input type="hidden" name="id_obra" value="<?php echo $id_obra; ?>">
                                </form>
                            </div>
                        </div>
                    </div>
             /div>
        </div>

        <?php require_once("../_vista/v_footer.php"); ?>
    </div>
</div>
<?php require_once("../_vista/v_script.php"); ?>
</body>
</html>
