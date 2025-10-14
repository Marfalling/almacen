<?php
require_once("../_conexion/sesion.php");

// VALIDACIÓN DE PERMISOS
if (!verificarPermisoEspecifico('crear_personal')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'PERSONAL', 'CREAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

require_once("../_modelo/m_personal.php");
require_once("../_conexion/conexion.php");

// OBTENER LISTAS DE ÁREAS Y CARGOS
function ObtenerAreas()
{
    include("../_conexion/conexion.php");


    $sql = "SELECT id_area, nom_area 
            FROM {$bd_complemento}.area 
            WHERE act_area = 1 
            ORDER BY nom_area";

    $res = $con->query($sql);

    $areas = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

    mysqli_close($con);
    return $areas;
}

function ObtenerCargos()
{
    include("../_conexion/conexion.php");
   

    $sql = "SELECT id_cargo, nom_cargo 
            FROM {$bd_complemento}.cargo 
            WHERE act_cargo = 1 
            ORDER BY nom_cargo";

    $res = $con->query($sql);

    $cargos = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

    mysqli_close($con);
    return $cargos;
}


$areas = ObtenerAreas();
$cargos = ObtenerCargos();

// PROCESAR REGISTRO
if (isset($_REQUEST['registrar'])) {

    $id_area   = $_REQUEST['id_area'];
    $id_cargo  = $_REQUEST['id_cargo'];
    $nom       = strtoupper(trim($_REQUEST['nom']));
    $dni       = trim($_REQUEST['dni']);
    $email     = trim($_REQUEST['email']);
    $tel       = trim($_REQUEST['tel']);
    $est       = isset($_REQUEST['est']) ? 1 : 0;

    // Validación de campos obligatorios
    if (empty($id_area) || empty($id_cargo) || empty($nom) || empty($dni)) {
        header("location: personal_mostrar.php?incompleto=true");
        exit;
    }

    // Registrar personal
    $rpta = GrabarPersonal($id_area, $id_cargo, $nom, $dni, $email, $tel, $est);

    if ($rpta == "SI") {
        header("location: personal_mostrar.php?registrado=true");
        exit;
    } elseif ($rpta == "NO") {
        header("location: personal_mostrar.php?existe=true");
        exit;
    } else {
        header("location: personal_mostrar.php?error=true");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Personal</title>
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
                        <h3>Nuevo Personal</h3>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>Registro de Personal</h2>
                                <div class="clearfix"></div>
                            </div>

                            <div class="x_content">
                                <br>
                                <form class="form-horizontal form-label-left" method="post" action="personal_nuevo.php">

                                    <!-- NOMBRE -->
                                    <div class="form-group row">
                                        <label class="control-label col-md-3 col-sm-3">
                                            Nombre <span class="text-danger">*</span> :
                                        </label>
                                        <div class="col-md-9 col-sm-9">
                                            <input type="text" name="nom" class="form-control"
                                                   placeholder="Nombre del personal" required>
                                        </div>
                                    </div>

                                    <!-- DNI -->
                                    <div class="form-group row">
                                        <label class="control-label col-md-3 col-sm-3">
                                            DNI <span class="text-danger">*</span> :
                                        </label>
                                        <div class="col-md-9 col-sm-9">
                                            <input type="text" name="dni" maxlength="8"
                                                   class="form-control" placeholder="Número de DNI" required>
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
                                                <?php foreach($areas as $a): ?>
                                                    <option value="<?php echo $a['id_area']; ?>">
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
                                                <?php foreach($cargos as $c): ?>
                                                    <option value="<?php echo $c['id_cargo']; ?>">
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
                                                   placeholder="Correo electrónico">
                                        </div>
                                    </div>

                                    <!-- CELULAR -->
                                    <div class="form-group row">
                                        <label class="control-label col-md-3 col-sm-3">Teléfono</label>
                                        <div class="col-md-9 col-sm-9">
                                            <input type="text" name="tel" maxlength="9"
                                                   class="form-control" placeholder="Número de celular">
                                        </div>
                                    </div>

                                    <!-- ESTADO -->
                                    <div class="form-group row">
                                        <label class="control-label col-md-3 col-sm-3">Estado:</label>
                                        <div class="col-md-9 col-sm-9">
                                            <label>
                                                <input type="checkbox" name="est" class="js-switch" checked> Activo
                                            </label>
                                        </div>
                                    </div>

                                    <div class="ln_solid"></div>

                                    <!-- BOTONES -->
                                    <div class="form-group">
                                        <div class="col-md-2 col-sm-2 offset-md-8">
                                            <button type="reset" class="btn btn-outline-danger btn-block">Limpiar</button>
                                        </div>
                                        <div class="col-md-2 col-sm-2">
                                            <button type="submit" name="registrar" id="btn_registrar"
                                                    class="btn btn-success btn-block">Registrar</button>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-12 col-sm-12">
                                            <p><span class="text-danger">*</span> Campos obligatorios</p>
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
</body>
</html>
