<?php
//=======================================================================
// CONTROLADOR: obras_nuevo.php
//=======================================================================
require_once("../_conexion/sesion.php");

//Verificar permisos coherentes con el mapa
if (!verificarPermisoEspecifico('crear_obras')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'OBRAS', 'NUEVO');
    header("location: bienvenido.php?permisos=true");
    exit;
}

require_once("../_modelo/m_obras.php");

//Procesar registro
if (isset($_POST['registrar'])) {
    $nom = strtoupper(trim($_POST['nom']));
    $est = isset($_POST['est']) ? 1 : 0;

    $rpta = RegistrarObra($nom, $est);

    if ($rpta == "SI") {
        header("Location: obras_mostrar.php?registrado=true");
        exit;
    } elseif ($rpta == "NO") {
        header("Location: obras_mostrar.php?existe=true");
        exit;
    } else {
        header("Location: obras_mostrar.php?error=true");
        exit;
    }
}

require_once("../_modelo/m_auditoria.php");
GrabarAuditoria($id, $usuario_sesion, 'INGRESO', 'OBRAS', 'NUEVO');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Nueva Obra</title>
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
             VISTA: Nueva Obra
        ========================== -->
        <div class="right_col" role="main">
            <div class="">
                <div class="page-title">
                    <div class="title_left">
                        <h3>Nueva Obra</h3>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="row">
                    <div class="col-md-12 col-sm-12 ">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>Registrar Obra <small>Ingrese los datos requeridos</small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <br>
                                <form class="form-horizontal form-label-left" action="obras_nuevo.php" method="post">
                                    
                                    <div class="form-group row">
                                        <label class="control-label col-md-3 col-sm-3">
                                            Nombre de la Obra <span class="text-danger">*</span> :
                                        </label>
                                        <div class="col-md-9 col-sm-9">
                                            <input type="text" name="nom" class="form-control" 
                                                   placeholder="Nombre de la obra" required="required">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="control-label col-md-3 col-sm-3">
                                            Estado:
                                        </label>
                                        <div class="col-md-9 col-sm-9">
                                            <label>
                                                <input type="checkbox" name="est" class="js-switch" checked> Activo
                                            </label>
                                        </div>
                                    </div>

                                    <div class="ln_solid"></div>

                                    <div class="form-group">
                                        <div class="col-md-2 col-sm-2 offset-md-10">
                                            <button type="submit" name="registrar" class="btn btn-success btn-block">
                                                Registrar
                                            </button>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-12 col-sm-12">
                                            <p><span class="text-danger">*</span> Los campos con 
                                            (<span class="text-danger">*</span>) son obligatorios.</p>
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
