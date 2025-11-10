<?php
// ====================================================================
// CONTROLADOR DE COMPROBANTES DE PAGO
// ====================================================================
require_once("../_conexion/sesion.php");

require_once("../_conexion/conexion.php");
require_once("../_modelo/m_comprobante.php");

// ====================================================================
// VALIDAR QUE SE RECIBIÓ ID DE COMPRA
// ====================================================================
if (!isset($_GET['id_compra']) || empty($_GET['id_compra'])) {
    header("Location: compras_mostrar.php");
    exit;
}

$id_compra = intval($_GET['id_compra']);

// ====================================================================
// CARGAR DATOS DE LA ORDEN DE COMPRA
// ====================================================================
$oc = ConsultarCompraCom($id_compra); // Necesitas esta función en m_compra.php

if (!$oc) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Error - Compra no encontrada</title>
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
                <div class="alert alert-danger">
                    <strong>Error:</strong> No se encontró la orden de compra especificada.
                    <br><br>
                    <a href="compras_mostrar.php" class="btn btn-primary">
                        <i class="fa fa-arrow-left"></i> Volver al listado de compras
                    </a>
                </div>
            </div>
            <?php require_once("../_vista/v_footer.php"); ?>
        </div>
    </div>
    <?php require_once("../_vista/v_script.php"); ?>
    </body>
    </html>
    <?php
    exit;
}

// ====================================================================
// CARGAR COMPROBANTES DE ESTA COMPRA
// ====================================================================
$comprobantes = MostrarComprobantesCompra($id_compra);

// ====================================================================
// CARGAR CATÁLOGOS PARA LOS FORMULARIOS
// ====================================================================
$tipos_documento = ConsultarTiposDocumento();
$monedas = ConsultarMonedas();
$detracciones = ConsultarDetracciones();
$medios_pago = ConsultarMediosPago();

// ====================================================================
// VARIABLES DE ALERTA
// ====================================================================
$mostrar_alerta = false;
$tipo_alerta = '';
$titulo_alerta = '';
$mensaje_alerta = '';

// ====================================================================
// PROCESAR ACCIONES POST
// ====================================================================

// ====================================================================
// 1. REGISTRAR NUEVO COMPROBANTE
// ====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'registrar') {
    
    $archivo_pdf = null;
    $archivo_xml = null;
    $error_archivo = false;

    // Procesar PDF
    if (!empty($_FILES['archivo_pdf']['name'])) {
        $resultado_pdf = procesarArchivo($_FILES['archivo_pdf'], 'pdf');
        if ($resultado_pdf['error']) {
            $mostrar_alerta = true;
            $tipo_alerta = 'warning';
            $titulo_alerta = 'Error en PDF';
            $mensaje_alerta = $resultado_pdf['mensaje'];
            $error_archivo = true;
        } else {
            $archivo_pdf = $resultado_pdf['ruta'];
        }
    }

    // Procesar XML
    if (!$error_archivo && !empty($_FILES['archivo_xml']['name'])) {
        $resultado_xml = procesarArchivo($_FILES['archivo_xml'], 'xml');
        if ($resultado_xml['error']) {
            $mostrar_alerta = true;
            $tipo_alerta = 'warning';
            $titulo_alerta = 'Error en XML';
            $mensaje_alerta = $resultado_xml['mensaje'];
            $error_archivo = true;
        } else {
            $archivo_xml = $resultado_xml['ruta'];
        }
    }

    if (!$error_archivo) {
        $datos = [
            'id_compra' => $id_compra,
            'id_tipo_documento' => $_POST['id_tipo_documento'],
            'serie' => trim($_POST['serie']),
            'numero' => trim($_POST['numero']),
            'monto_total_igv' => $_POST['monto_total_igv'],
            'id_detraccion' => !empty($_POST['id_detraccion']) ? $_POST['id_detraccion'] : null,
            'id_moneda' => $_POST['id_moneda'],
            'total_pagar' => $_POST['total_pagar'],
            'id_medio_pago' => !empty($_POST['id_medio_pago']) ? $_POST['id_medio_pago'] : null,
            'fec_pago' => !empty($_POST['fec_pago']) ? $_POST['fec_pago'] : null,
            'archivo_pdf' => $archivo_pdf,
            'archivo_xml' => $archivo_xml,
            'id_personal' => $_SESSION['id_personal'],
            'est_comprobante' => 1
        ];

        $resultado = GrabarComprobante($datos);

        if (strpos($resultado, 'SI|') === 0) {
            $partes = explode('|', $resultado);
            $id_comprobante = $partes[1];
            ?>
            <script>
            setTimeout(function() {
                window.location.href = 'comprobante_registrar.php?id_compra=<?php echo $id_compra; ?>&alert=success&tipo=registrar&id=<?php echo $id_comprobante; ?>';
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

// ====================================================================
// 2. EDITAR COMPROBANTE
// ====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {
    
    $id_comprobante = intval($_POST['id_comprobante']);
    $comprobante_actual = ConsultarComprobante($id_comprobante);
    
    if (!$comprobante_actual) {
        $mostrar_alerta = true;
        $tipo_alerta = 'error';
        $titulo_alerta = 'Comprobante no encontrado';
        $mensaje_alerta = 'El comprobante que intenta editar no existe.';
    } else {
        $archivo_pdf = $comprobante_actual['archivo_pdf'];
        $archivo_xml = $comprobante_actual['archivo_xml'];
        $error_archivo = false;

        if (!empty($_FILES['archivo_pdf']['name'])) {
            $resultado_pdf = procesarArchivo($_FILES['archivo_pdf'], 'pdf');
            if ($resultado_pdf['error']) {
                $mostrar_alerta = true;
                $tipo_alerta = 'warning';
                $titulo_alerta = 'Error en PDF';
                $mensaje_alerta = $resultado_pdf['mensaje'];
                $error_archivo = true;
            } else {
                if ($archivo_pdf && file_exists("../_upload/comprobantes/" . $archivo_pdf)) {
                    unlink("../_upload/comprobantes/" . $archivo_pdf);
                }
                $archivo_pdf = $resultado_pdf['ruta'];
            }
        }

        if (!$error_archivo && !empty($_FILES['archivo_xml']['name'])) {
            $resultado_xml = procesarArchivo($_FILES['archivo_xml'], 'xml');
            if ($resultado_xml['error']) {
                $mostrar_alerta = true;
                $tipo_alerta = 'warning';
                $titulo_alerta = 'Error en XML';
                $mensaje_alerta = $resultado_xml['mensaje'];
                $error_archivo = true;
            } else {
                if ($archivo_xml && file_exists("../_upload/comprobantes/" . $archivo_xml)) {
                    unlink("../_upload/comprobantes/" . $archivo_xml);
                }
                $archivo_xml = $resultado_xml['ruta'];
            }
        }

        if (!$error_archivo) {
            $datos = [
                'id_compra' => $id_compra,
                'id_tipo_documento' => $_POST['id_tipo_documento'],
                'serie' => trim($_POST['serie']),
                'numero' => trim($_POST['numero']),
                'monto_total_igv' => $_POST['monto_total_igv'],
                'id_detraccion' => !empty($_POST['id_detraccion']) ? $_POST['id_detraccion'] : null,
                'id_moneda' => $_POST['id_moneda'],
                'total_pagar' => $_POST['total_pagar'],
                'id_medio_pago' => !empty($_POST['id_medio_pago']) ? $_POST['id_medio_pago'] : null,
                'fec_pago' => !empty($_POST['fec_pago']) ? $_POST['fec_pago'] : null,
                'archivo_pdf' => $archivo_pdf,
                'archivo_xml' => $archivo_xml
            ];

            $resultado = EditarComprobante($id_comprobante, $datos);

            if ($resultado === "SI") {
                ?>
                <script>
                setTimeout(function() {
                    window.location.href = 'comprobante_registrar.php?id_compra=<?php echo $id_compra; ?>&alert=success&tipo=editar&id=<?php echo $id_comprobante; ?>';
                }, 100);
                </script>
                <?php
                exit();
            } else {
                $mostrar_alerta = true;
                $tipo_alerta = 'error';
                $titulo_alerta = 'Error al actualizar';
                $mensaje_alerta = $resultado;
            }
        }
    }
}

// ====================================================================
// 3. SUBIR VOUCHER DE PAGO
// ====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'subir_voucher') {
    
    // 🔍 DEBUG: Ver qué está llegando
    error_log("========== DEBUG SUBIR VOUCHER ==========");
    error_log("POST completo: " . print_r($_POST, true));
    error_log("id_comprobante recibido: " . $_POST['id_comprobante']);
    error_log("=========================================");
    
    $id_comprobante = intval($_POST['id_comprobante']);
    
    // 🔍 DEBUG: Ver el valor después de intval
    error_log("id_comprobante después de intval: $id_comprobante");
    
    $enviar_proveedor = isset($_POST['enviar_proveedor']) ? true : false;
    $enviar_contabilidad = isset($_POST['enviar_contabilidad']) ? true : false;
    $enviar_tesoreria = isset($_POST['enviar_tesoreria']) ? true : false;

    $error_archivo = false;
    $voucher_pago = null;

    if (!empty($_FILES['voucher_pago']['name'])) {
        $resultado_voucher = procesarArchivo($_FILES['voucher_pago'], 'voucher');
        if ($resultado_voucher['error']) {
            $mostrar_alerta = true;
            $tipo_alerta = 'warning';
            $titulo_alerta = 'Error en voucher';
            $mensaje_alerta = $resultado_voucher['mensaje'];
            $error_archivo = true;
        } else {
            $voucher_pago = $resultado_voucher['ruta'];
        }
    } else {
        $mostrar_alerta = true;
        $tipo_alerta = 'warning';
        $titulo_alerta = 'Archivo requerido';
        $mensaje_alerta = 'Debe seleccionar un archivo de voucher.';
        $error_archivo = true;
    }

    if (!$error_archivo) {
        $resultado = SubirVoucherComprobante(
            $id_comprobante,
            $voucher_pago,
            $enviar_proveedor,
            $enviar_contabilidad,
            $enviar_tesoreria
        );

        if (strpos($resultado, 'SI|') === 0) {
            ?>
            <script>
            setTimeout(function() {
                window.location.href = 'comprobante_registrar.php?id_compra=<?php echo $id_compra; ?>&alert=success&tipo=voucher&id=<?php echo $id_comprobante; ?>';
            }, 100);
            </script>
            <?php
            exit();
        } else {
            $mostrar_alerta = true;
            $tipo_alerta = 'error';
            $titulo_alerta = 'Error al subir voucher';
            $mensaje_alerta = $resultado;
        }
    }
}
// ====================================================================
// FUNCIÓN PARA PROCESAR ARCHIVOS
// ====================================================================
function procesarArchivo($archivo, $tipo) {
    $resultado = ['error' => false, 'mensaje' => '', 'ruta' => null];
    
    $tamano_max = 5 * 1024 * 1024;
    if ($archivo['size'] > $tamano_max) {
        $resultado['error'] = true;
        $resultado['mensaje'] = 'El archivo no debe superar los 5MB.';
        return $resultado;
    }

    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    
    switch ($tipo) {
        case 'pdf':
            $extensiones_permitidas = ['pdf'];
            $carpeta = 'comprobantes';
            $prefijo = 'comp';
            break;
        case 'xml':
            $extensiones_permitidas = ['xml'];
            $carpeta = 'comprobantes';
            $prefijo = 'comp';
            break;
        case 'voucher':
            $extensiones_permitidas = ['pdf', 'jpg', 'jpeg', 'png'];
            $carpeta = 'vouchers';
            $prefijo = 'voucher';
            break;
        default:
            $extensiones_permitidas = [];
            $carpeta = 'otros';
            $prefijo = 'file';
    }
    
    if (!in_array($extension, $extensiones_permitidas)) {
        $resultado['error'] = true;
        $resultado['mensaje'] = 'Formato de archivo no permitido para ' . strtoupper($tipo) . '. Extensiones permitidas: ' . implode(', ', $extensiones_permitidas);
        return $resultado;
    }

    $carpeta_destino = __DIR__ . "/../_upload/$carpeta/";
    
    if (!is_dir($carpeta_destino)) {
        mkdir($carpeta_destino, 0777, true);
    }

    $nombre_archivo = $prefijo . "_" . time() . "_" . uniqid() . "." . $extension;
    $ruta_destino = $carpeta_destino . $nombre_archivo;

    if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
        $resultado['ruta'] = $nombre_archivo;
    } else {
        $resultado['error'] = true;
        $resultado['mensaje'] = 'No se pudo guardar el archivo. Verifique los permisos de la carpeta.';
    }

    return $resultado;
}


// ====================================================================
// MOSTRAR ALERTA DE ÉXITO SI VIENE DE REDIRECCIÓN
// ====================================================================
if (isset($_GET['alert']) && $_GET['alert'] === 'success') {
    $mostrar_alerta = true;
    $tipo_alerta = 'success';
    
    $tipo_accion = isset($_GET['tipo']) ? $_GET['tipo'] : '';
    
    switch ($tipo_accion) {
        case 'registrar':
            $titulo_alerta = '¡Comprobante Registrado!';
            $mensaje_alerta = 'El comprobante ha sido registrado correctamente.';
            break;
        case 'editar':
            $titulo_alerta = '¡Comprobante Actualizado!';
            $mensaje_alerta = 'Los cambios han sido guardados correctamente.';
            break;
        case 'voucher':
            $titulo_alerta = '¡Voucher Subido!';
            $mensaje_alerta = 'El voucher ha sido registrado y las notificaciones han sido enviadas.';
            break;
        case 'anular':
            $titulo_alerta = '¡Comprobante Anulado!';
            $mensaje_alerta = 'El comprobante ha sido anulado correctamente.';
            break;
        default:
            $titulo_alerta = '¡Operación Exitosa!';
            $mensaje_alerta = 'La operación se completó correctamente.';
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

    <title>Gestión de Comprobantes de Pago - OC #<?php echo $id_compra; ?></title>

    <?php require_once("../_vista/v_estilo.php"); ?>
    

</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            // Vista principal
            require_once("../_vista/v_comprobante_registrar.php");

            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>

    <?php require_once("../_vista/v_script.php"); ?>
    <?php  require_once("../_vista/v_alertas.php"); ?>
    <?php
    
    // ====================================================================
    // MOSTRAR ALERTAS CON SWEETALERT2
    // ====================================================================
    if ($mostrar_alerta) {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '<?php echo $tipo_alerta; ?>',
                title: '<?php echo $titulo_alerta; ?>',
                html: '<?php echo $mensaje_alerta; ?>',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '<?php echo ($tipo_alerta == "error") ? "#d33" : "#3085d6"; ?>',
                <?php if ($tipo_alerta === 'success'): ?>
                timer: 3000,
                timerProgressBar: true
                <?php endif; ?>
            }).then(function() {
                <?php if (isset($_GET['alert'])): ?>
                if (window.history.replaceState) {
                    const url = new URL(window.location);
                    url.searchParams.delete('alert');
                    url.searchParams.delete('tipo');
                    url.searchParams.delete('id');
                    window.history.replaceState({}, document.title, url);
                }
                <?php endif; ?>
            });
        });
        </script>
        <?php
    }
    ?>

</body>
</html>
