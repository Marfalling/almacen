<?php
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('crear_proveedor')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'PROVEEDOR', 'CREAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

require_once("../_modelo/m_proveedor.php");
require_once("../_modelo/m_banco.php");
require_once("../_modelo/m_moneda.php");

$monedas = MostrarMoneda();
$bancos = MostrarBanco(); 

if (isset($_REQUEST['registrar'])) {
    $nom = strtoupper($_REQUEST['nom']);
    $ruc = strtoupper($_REQUEST['ruc']);
    $dir = strtoupper($_REQUEST['dir']);
    $tel = strtoupper($_REQUEST['tel']);
    $cont = strtoupper($_REQUEST['cont']);
    $email = strtolower(trim($_REQUEST['email']));
    $est = isset($_REQUEST['est']) ? 1 : 0;

    $id_proveedor = GrabarProveedor($nom, $ruc, $dir, $tel, $cont, $est, $email);

    if ($id_proveedor !== "NO" && $id_proveedor !== "ERROR") {
        $lista_bancos = $_POST['banco'] ?? [];
        $lista_monedas = $_POST['id_moneda'] ?? [];
        $lista_corrientes = $_POST['cta_corriente'] ?? [];
        $lista_interbancarias = $_POST['cta_interbancaria'] ?? [];

        for ($i = 0; $i < count($lista_bancos); $i++) {
            $id_banco = intval($lista_bancos[$i]);
            $moneda = intval($lista_monedas[$i]);
            $cta_corriente = $lista_corrientes[$i];
            $cta_interbancaria = $lista_interbancarias[$i];
            GrabarCuentaProveedor($id_proveedor, $id_banco, $moneda, $cta_corriente, $cta_interbancaria);
        }

        header("location: proveedor_mostrar.php?registrado=true");
        exit;
    } elseif ($id_proveedor === "NO") {
        header("location: proveedor_mostrar.php?existe=true");
        exit;
    } else {
        header("location: proveedor_mostrar.php?error=true");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Nuevo Proveedor</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_vista/v_proveedor_nuevo.php");
            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>

    <?php
    require_once("../_vista/v_script.php");
    require_once("../_vista/v_alertas.php");
    ?>
</body>
</html>


