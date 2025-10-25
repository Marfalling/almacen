<?php
// ====================================================================
// CONTROLADOR DE PAGOS DE ORDEN DE COMPRA
// ====================================================================
require_once("../_conexion/sesion.php");

/*if (!verificarPermisoEspecifico('crear_pagos')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'PAGOS', 'CREAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}*/

require_once("../_conexion/conexion.php");
require_once("../_modelo/m_pago.php");
require_once("../_modelo/m_proveedor.php");

// ====================================================================
// VARIABLES DE ALERTA
// ====================================================================
$mostrar_alerta = false;
$tipo_alerta = '';
$titulo_alerta = '';
$mensaje_alerta = '';

// ====================================================================
// CARGAR DATOS
// ====================================================================
$id_compra = isset($_REQUEST['id_compra']) ? intval($_REQUEST['id_compra']) : 0;
$oc = ConsultarCompraPago($id_compra);                // Info de la OC

if (!$oc) {
    die("❌ Error: La compra con ID $id_compra no existe.");
}

// Ahora sí puedes usar el id_proveedor
$cuentas = ObtenerCuentasProveedor($oc['id_proveedor']);
$pagos = MostrarPagosCompra($id_compra);          // Pagos registrados

// ====================================================================
// PROCESAR REGISTRO DE PAGO
// ====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['monto'])) {
    $monto = floatval($_POST['monto']);
    $id_cuenta = intval($_POST['id_cuenta']);
    $id_personal = $_SESSION['id_personal'];
    $enviar_correo = isset($_POST['enviar_correo']) ? 1 : 0;
    $enviar_correo2 = isset($_POST['enviar_correo2']) ? 1 : 0;
    $enviar_correo3 = isset($_POST['enviar_correo3']) ? 1 : 0;

    // Guardar archivo de comprobante
    $comprobante = null;
    if (!empty($_FILES['comprobante']['name'])) {
        // Carpeta destino real
        $carpeta_destino = __DIR__ . "/../uploads/pagos/";

        // Crear carpeta si no existe
        if (!is_dir($carpeta_destino)) {
            mkdir($carpeta_destino, 0777, true);
        }

        // Generar nombre único
        $nombre_archivo = "pago_".$id_compra."_".time()."_".basename($_FILES['comprobante']['name']);
        $ruta_destino   = $carpeta_destino . $nombre_archivo;

        // Mover archivo
        if (move_uploaded_file($_FILES['comprobante']['tmp_name'], $ruta_destino)) {
            // Guardamos ruta relativa para mostrar después en la vista
            $comprobante = "uploads/pagos/" . $nombre_archivo;
        }
    }

    // ====================================================================
// VALIDACIONES DE MONTO Y SALDO
// ====================================================================
$monto = round(floatval($_POST['monto']), 2);
$saldo = round(floatval($oc['saldo']), 2);

if ($monto <= 0) {
    $mostrar_alerta = true;
    $tipo_alerta = 'warning';
    $titulo_alerta = 'Monto inválido';
    $mensaje_alerta = 'Debe ingresar un monto mayor a cero.';
} elseif ($monto > $saldo) {
    $mostrar_alerta = true;
    $tipo_alerta = 'warning';
    $titulo_alerta = 'Monto excedido';
    $mensaje_alerta = "El monto del pago ($monto) no puede superar el saldo pendiente ($saldo).";
} else {
    // Registrar pago en BD
    $resultado = GrabarPago($id_compra, $id_cuenta, $monto, $comprobante, $id_personal, $enviar_correo, $enviar_correo2, $enviar_correo3);

    if ($resultado === "SI") {
        ?>
        <script>
        setTimeout(function() {
            window.location.href = 'pago_registrar.php?id_compra=<?php echo $id_compra; ?>&registrado=true';
        }, 100);
        </script>
        <?php
        exit();
    } else {
        $mostrar_alerta = true;
        $tipo_alerta = 'error';
        $titulo_alerta = 'Error al registrar';
        $mensaje_alerta = $resultado;
    }
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

    <title>Pagos - Orden de Compra</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
<div class="container body">
    <div class="main_container">
        <?php
        require_once("../_vista/v_menu.php");
        require_once("../_vista/v_menu_user.php");

        // Vista principal
        require_once("../_vista/v_pago_registrar.php");

        require_once("../_vista/v_footer.php");
        ?>
    </div>
</div>

<?php
require_once("../_vista/v_script.php");
require_once("../_vista/v_alertas.php");

if ($mostrar_alerta) {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: '<?php echo $tipo_alerta; ?>',
            title: '<?php echo $titulo_alerta; ?>',
            html: '<?php echo $mensaje_alerta; ?>',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '<?php echo ($tipo_alerta == "error") ? "#d33" : "#3085d6"; ?>'
        });
    });
    </script>
    <?php
}
?>
</body>
</html>