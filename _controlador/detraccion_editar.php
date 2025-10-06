<?php
require_once("../_conexion/sesion.php");

//=======================================================================
// CONTROLADOR: detraccion_editar.php 
//=======================================================================

if (!verificarPermisoEspecifico('editar_detraccion')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'DETRACCION', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

require_once("../_modelo/m_detraccion.php");

if (isset($_POST['registrar'])) {
    $id_detraccion = intval($_POST['id_detraccion']);
    $nom = strtoupper(trim($_POST['nom']));
    $porcentaje = floatval($_POST['porcentaje']);
    $estado = isset($_POST['estado']) ? 1 : 0;

    $rpta = EditarDetraccion($id_detraccion, $nom, $porcentaje, $estado);

    require_once("../_modelo/m_auditoria.php");

    if ($rpta === "SI") {
        GrabarAuditoria($id, $usuario_sesion, 'MODIFICACIÓN', 'DETRACCION', "Se editó la detracción '$nom' (ID: $id_detraccion)");
        header("Location: detraccion_mostrar.php?actualizado=true");
        exit;
    } elseif ($rpta === "NO") {
        GrabarAuditoria($id, $usuario_sesion, 'ERROR MODIFICACIÓN', 'DETRACCION', "Intento duplicado al editar '$nom'");
        header("Location: detraccion_mostrar.php?error=duplicado");
        exit;
    } else {
        GrabarAuditoria($id, $usuario_sesion, 'ERROR MODIFICACIÓN', 'DETRACCION', "Error al editar '$nom'");
        header("Location: detraccion_mostrar.php?error=true");
        exit;
    }
}

$id_detraccion = isset($_GET['id_detraccion']) ? intval($_GET['id_detraccion']) : 0;
if ($id_detraccion <= 0) {
    header("Location: detraccion_mostrar.php?error=true");
    exit;
}

$detraccion_data = ObtenerDetraccionPorId($id_detraccion);
if (!$detraccion_data) {
    header("Location: detraccion_mostrar.php?error=true");
    exit;
}

$nom = $detraccion_data['nombre_detraccion'];
$porcentaje = $detraccion_data['porcentaje'];
$estado = $detraccion_data['est_detraccion'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Editar Detracción</title>
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
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Editar Detracción</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <form action="" method="POST">
                                <input type="hidden" name="id_detraccion" value="<?php echo $id_detraccion; ?>">

                                <div class="form-group">
                                    <label for="nom">Nombre</label>
                                    <input type="text" class="form-control" id="nom" name="nom" value="<?php echo $nom; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="porcentaje">Porcentaje (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="porcentaje" name="porcentaje" value="<?php echo $porcentaje; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="estado">Estado</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="estado" name="estado" value="1" <?php echo ($estado == 1) ? 'checked' : ''; ?>>
                                        <label class="custom-control-label" for="estado">Activo / Inactivo</label>
                                    </div>
                                </div>

                                <button type="submit" name="registrar" class="btn btn-success">Guardar Cambios</button>
                                <a href="detraccion_mostrar.php" class="btn btn-secondary">Cancelar</a>
                            </form>
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
