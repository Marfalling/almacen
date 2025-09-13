<?php
require_once '../_conexion/sesion.php';
require_once '../_modelo/m_auditoria.php';
require_once '../_modelo/m_compras.php';
require_once '../_complemento/dompdf/autoload.inc.php';

setlocale(LC_TIME, 'es_ES.UTF-8');
date_default_timezone_set('America/Lima');
$fecha_actual = date("Y-m-d");
$fecha_completa = new DateTime();
$fecha_formateada = $fecha_completa->format('l d \d\e F \d\e Y, H:i:s');
$dias_esp = ['Monday' => 'lunes', 'Tuesday' => 'martes', 'Wednesday' => 'miércoles', 
             'Thursday' => 'jueves', 'Friday' => 'viernes', 'Saturday' => 'sábado', 'Sunday' => 'domingo'];
$meses_esp = ['January' => 'enero', 'February' => 'febrero', 'March' => 'marzo', 
              'April' => 'abril', 'May' => 'mayo', 'June' => 'junio',
              'July' => 'julio', 'August' => 'agosto', 'September' => 'septiembre',
              'October' => 'octubre', 'November' => 'noviembre', 'December' => 'diciembre'];
$fecha_formateada = str_replace(array_keys($dias_esp), array_values($dias_esp), $fecha_formateada);
$fecha_formateada = str_replace(array_keys($meses_esp), array_values($meses_esp), $fecha_formateada);

// Verificar si se recibió el ID de la compra
if (!isset($_GET['id']) || $_GET['id'] == "") {
    $titulo = 'Error en datos';
    $mensaje = 'Ocurrió un error al obtener la información de la compra';
?>
    <script Language="JavaScript">
        location.href = 'compras_mostrar.php?error=true&titulo=<?php echo $titulo; ?>&mensaje=<?php echo $mensaje; ?>';
    </script>
<?php
    exit;
}

$id_compra = intval($_GET['id']);

// Preparar logo en base64
$imagenLogo = "../_complemento/images/icon.png";
$imagenLogoBase64 = "";
if (file_exists($imagenLogo)) {
    $imagenLogoBase64 = "data:image/png;base64," . base64_encode(file_get_contents($imagenLogo));
}

// Obtener datos de la compra
$compra_data = ConsultarCompra($id_compra);
$compra_detalle = ConsultarCompraDetalle($id_compra);

if (empty($compra_data)) {
    $titulo = 'Error en datos';
    $mensaje = 'Compra no encontrada';
?>
    <script Language="JavaScript">
        location.href = 'compras_mostrar.php?error=true&titulo=<?php echo $titulo; ?>&mensaje=<?php echo $mensaje; ?>';
    </script>
<?php
    exit;
}

$compra = $compra_data[0];

// Preparar datos para el PDF
$numero_orden = str_pad($compra['id_compra'], 6, '0', STR_PAD_LEFT);
$codigo_pedido = $compra['cod_pedido'] ?? '';
$nombre_obra = $compra['nom_obra'] ?? 'NO ESPECIFICADO';
$fecha_compra = date('d/m/Y', strtotime($compra['fec_compra']));
$fecha_requerida = isset($compra['fec_req_pedido']) ? date('d/m/Y', strtotime($compra['fec_req_pedido'])) : '';
$ot_pedido = $compra['ot_pedido'] ?? '';
$nom_personal = trim(($compra['nom_personal'] ?? '') . ' ' . ($compra['ape_personal'] ?? ''));
$lugar_entrega = $compra['lug_pedido'] ?? ($compra['denv_compra'] ?? '');
$telefono = $compra['cel_pedido'] ?? '';
$almacen = $compra['nom_almacen'] ?? '';
$observaciones = $compra['obs_compra'] ?? 'Sin observaciones especiales';
$aclaraciones = $compra['acl_pedido'] ?? '';
$plazo_entrega = $compra['plaz_compra'] ?? '';
$portes = $compra['port_compra'] ?? '';

// Datos del proveedor
$nom_proveedor = $compra['nom_proveedor'] ?? '';
$ruc_proveedor = $compra['ruc_proveedor'] ?? '';
$dir_proveedor = $compra['dir_proveedor'] ?? '';
$tel_proveedor = $compra['tel_proveedor'] ?? '';
$cont_proveedor = $compra['cont_proveedor'] ?? '';

// Moneda
$moneda = $compra['nom_moneda'] ?? 'SOLES';

// Estado de la compra
$estado_texto = '';
switch($compra['est_compra']) {
    case 1: $estado_texto = 'PENDIENTE'; break;
    case 2: $estado_texto = 'APROBADO'; break;
    case 0: $estado_texto = 'ANULADO'; break;
    default: $estado_texto = 'DESCONOCIDO';
}

// Preparar detalles de la compra
$detalles_html = '';
$item = 1;
$subtotal = 0;

foreach ($compra_detalle as $detalle) {
    // Usar el nombre del producto si prod_pedido_detalle está vacío
    $descripcion = !empty($detalle['prod_pedido_detalle']) 
        ? htmlspecialchars($detalle['prod_pedido_detalle'], ENT_QUOTES, 'UTF-8')
        : htmlspecialchars($detalle['nom_producto'], ENT_QUOTES, 'UTF-8');
    
    $cantidad = number_format($detalle['cant_compra_detalle'], 2);
    
    // Unidad de medida
    $unidad = isset($detalle['nom_unidad_medida']) ? htmlspecialchars($detalle['nom_unidad_medida'], ENT_QUOTES, 'UTF-8') : 'UND';
    
    // Verificar si existe precio unitario
    $precio_unitario = isset($detalle['prec_compra_detalle']) && $detalle['prec_compra_detalle'] > 0 
        ? number_format($detalle['prec_compra_detalle'], 2) 
        : '0.00';
    
    $precio_num = isset($detalle['prec_compra_detalle']) ? floatval($detalle['prec_compra_detalle']) : 0;
    $total_item = $detalle['cant_compra_detalle'] * $precio_num;
    $total_formateado = number_format($total_item, 2);
    
    $subtotal += $total_item;
    
    $detalles_html .= '
    <tr>
        <td class="text-center">' . $item . '</td>
        <td class="text-center">' . $cantidad . '</td>
        <td class="text-left">' . $descripcion . '</td>
        <td class="text-right">' . $precio_unitario . '</td>
        <td class="text-right">' . $total_formateado . '</td>
    </tr>';
    
    $item++;
}

// Si no hay detalles, agregar una fila vacía para evitar tabla vacía
if (empty($detalles_html)) {
    $detalles_html = '
    <tr>
        <td class="text-center">1</td>
        <td class="text-center">0.00</td>
        <td class="text-left">No hay productos en esta compra</td>
        <td class="text-right">0.00</td>
        <td class="text-right">0.00</td>
    </tr>';
}

// Cálculos finales
$igv = $subtotal * 0.18;
$total = $subtotal + $igv;

$subtotal_formateado = number_format($subtotal, 2);
$igv_formateado = number_format($igv, 2);
$total_formateado = number_format($total, 2);

// Nombre del archivo PDF
$nombre_archivo = "ORDEN_COMPRA_" . $numero_orden . "_" . date('Ymd') . ".pdf";

// Incluir la vista del PDF
require '../_vista/v_compras_pdf.php';

// Configurar memoria
ini_set("memory_limit", "128M");

use Dompdf\Dompdf;

$dompdf = new Dompdf();
$dompdf->setPaper('A4', 'portrait');
$html = mb_convert_encoding($html, 'UTF-8', mb_list_encodings());
$dompdf->loadHtml($html);
$dompdf->render();
$dompdf->stream($nombre_archivo, array("Attachment" => false));
?>