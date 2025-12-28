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
$compra_data = ConsultarCompraPorId($id_compra);
$compra_detalle = ConsultarCompraDetalleConCentros($id_compra); // 🔹 USAR FUNCIÓN CON CENTROS

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
$id_producto_tipo = $compra['id_producto_tipo'] ?? 1;

if ($id_producto_tipo == 1) {
    $codigo_orden = 'OC' . str_pad($id_compra, 4, '0', STR_PAD_LEFT);
    $tipo_orden = 'COMPRA';
} else {
    $codigo_orden = 'OS' . str_pad($id_compra, 4, '0', STR_PAD_LEFT);
    $tipo_orden = 'SERVICIO';
}

$numero_orden = $id_compra;
$codigo_pedido = $compra['cod_pedido'] ?? '';
$nombre_obra = $compra['nom_obra'] ?? 'NO ESPECIFICADO';
$fecha_compra = date('d/m/Y', strtotime($compra['fec_compra']));
$fecha_requerida = isset($compra['fec_req_pedido']) ? date('d/m/Y', strtotime($compra['fec_req_pedido'])) : '';
$ot_pedido = $compra['ot_pedido'] ?? '';
$nom_personal = trim($compra['nom_personal'] ?? '');
$lugar_entrega = $compra['lug_pedido'] ?? ($compra['denv_compra'] ?? '');
$telefono = $compra['cel_pedido'] ?? '';
$almacen = $compra['nom_almacen'] ?? '';
$observaciones = $compra['obs_compra'] ?? 'Sin observaciones especiales';
$aclaraciones = $compra['acl_pedido'] ?? '';
$plazo_pago_dias = $compra['plaz_compra'] ?? '';  
$plazo_entrega_texto = $compra['plaz_entrega'] ?? '';  
$portes = $compra['port_compra'] ?? '';

// 🔹 OBTENER CENTRO DE COSTO DEL PERSONAL
$centro_costo_personal = $compra['nom_centro_costo'] ?? 'NO ESPECIFICADO';



// Definir condición de pago de forma clara
$es_contado = empty($plazo_pago_dias) || $plazo_pago_dias == '0' || $plazo_pago_dias == 0;
$condicion_pago = $es_contado 
    ? 'Contado' 
    : 'Crédito (' . $plazo_pago_dias . ' días)';

// Datos del proveedor
$nom_proveedor = $compra['nom_proveedor'] ?? '';
$ruc_proveedor = $compra['ruc_proveedor'] ?? '';
$dir_proveedor = $compra['dir_proveedor'] ?? '';
$tel_proveedor = $compra['tel_proveedor'] ?? '';
$cont_proveedor = $compra['cont_proveedor'] ?? '';
$email_proveedor = $compra['mail_proveedor'] ?? ''; 

// Moneda
$moneda = $compra['nom_moneda'] ?? 'SOLES';
$simbolo_moneda = $compra['sim_moneda'] ?? 'S/.';

// Estado de la compra
$estado_texto = '';
switch($compra['est_compra']) {
    case 1: 
        $estado_texto = 'PENDIENTE'; 
        break;
        
    case 2: 
        if ($compra['esta_pagada']) {
            $estado_texto = 'PAGADO';
        } else {
            $estado_texto = 'APROBADO';
        }
        break;
        
    case 3: 
        if ($compra['esta_pagada']) {
            $estado_texto = 'CERRADO';
        } else {
            $estado_texto = 'INGRESADO';
        }
        break;
        
    case 0: 
        $estado_texto = 'ANULADO'; 
        break;
        
    default: 
        $estado_texto = 'DESCONOCIDO';
}

$tiene_detraccion = false;
$nombre_detraccion = '';
$porcentaje_detraccion = 0;

$tiene_retencion = false;
$nombre_retencion = '';
$porcentaje_retencion = 0;

$tiene_percepcion = false;
$nombre_percepcion = '';
$porcentaje_percepcion = 0;

if (!empty($compra['id_detraccion'])) {
    $tiene_detraccion = true;
    $nombre_detraccion = $compra['nombre_detraccion'] ?? '';
    $porcentaje_detraccion = floatval($compra['porcentaje_detraccion'] ?? 0);
}

if (!empty($compra['id_retencion'])) {
    $tiene_retencion = true;
    $nombre_retencion = $compra['nombre_retencion'] ?? '';
    $porcentaje_retencion = floatval($compra['porcentaje_retencion'] ?? 0);
}

if (!empty($compra['id_percepcion'])) {
    $tiene_percepcion = true;
    $nombre_percepcion = $compra['nombre_percepcion'] ?? '';
    $porcentaje_percepcion = floatval($compra['porcentaje_percepcion'] ?? 0);
}

// Preparar detalles de la compra
$detalles_html = '';
$item = 1;
$subtotal = 0;
$igv_total_acumulado = 0; 

foreach ($compra_detalle as $detalle) {
    // 🔹 DESCRIPCIÓN BASE
    $descripcion = !empty($detalle['prod_pedido_detalle']) 
        ? htmlspecialchars($detalle['prod_pedido_detalle'], ENT_QUOTES, 'UTF-8')
        : htmlspecialchars($detalle['nom_producto'], ENT_QUOTES, 'UTF-8');
    
    // 🔹 AGREGAR REQUISITOS SST/MA/CA SI EXISTE
    $req_compra = isset($detalle['req_compra_detalle']) && !empty($detalle['req_compra_detalle'])
        ? htmlspecialchars($detalle['req_compra_detalle'], ENT_QUOTES, 'UTF-8')
        : '';

    if (!empty($req_compra)) {
        $descripcion .= ' - ' . $req_compra;
    }
    
    // 🔹 OBTENER CENTROS DE COSTO
    $centros_texto = '';
    if (!empty($detalle['centros_costo']) && is_array($detalle['centros_costo'])) {
        $nombres_centros = array_map(function($centro) {
            return htmlspecialchars($centro['nom_centro_costo'], ENT_QUOTES, 'UTF-8');
        }, $detalle['centros_costo']);
        $centros_texto = implode(', ', $nombres_centros);
    } else {
        $centros_texto = 'No especificado';
    }
    
    $cantidad = number_format($detalle['cant_compra_detalle'], 2);
    
    $unidad = 'UND'; // Valor por defecto
    
    // Prioridad 1: Código de unidad (si existe y no está vacío)
    if (isset($detalle['cod_unidad_medida']) && !empty($detalle['cod_unidad_medida'])) {
        $unidad = htmlspecialchars($detalle['cod_unidad_medida'], ENT_QUOTES, 'UTF-8');
    } 
    // Prioridad 2: Nombre completo de unidad
    elseif (isset($detalle['nom_unidad_medida']) && !empty($detalle['nom_unidad_medida'])) {
        $unidad = htmlspecialchars($detalle['nom_unidad_medida'], ENT_QUOTES, 'UTF-8');
    }    
    $precio_unitario = isset($detalle['prec_compra_detalle']) && $detalle['prec_compra_detalle'] > 0 
        ? number_format($detalle['prec_compra_detalle'], 2) 
        : '0.00';
    
    $precio_num = isset($detalle['prec_compra_detalle']) ? floatval($detalle['prec_compra_detalle']) : 0;
    
    // Obtener IGV real de la BD
    $igv_porcentaje = isset($detalle['igv_compra_detalle']) ? floatval($detalle['igv_compra_detalle']) : 18;
    
    // Calcular subtotal del item
    $subtotal_item = $detalle['cant_compra_detalle'] * $precio_num;
    
    // Calcular IGV del item según su porcentaje específico
    $igv_item = $subtotal_item * ($igv_porcentaje / 100);
    
    // Total del item
    $total_item = $subtotal_item + $igv_item;
    $total_formateado = number_format($total_item, 2);
    
    $subtotal += $subtotal_item;
    $igv_total_acumulado += $igv_item;
    
    // 🔹 AGREGAR COLUMNA DE CENTRO DE COSTO
    $detalles_html .= '
    <tr>
        <td class="text-center">' . $item . '</td>
        <td class="text-center">' . $cantidad . '</td>
        <td class="text-center">' . $unidad . '</td>
        <td class="text-left">' . $descripcion . '</td>
        <td class="text-left">' . $centros_texto . '</td>
        <td class="text-right">' . $precio_unitario . '</td>
        <td class="text-right">' . $total_formateado . '</td>
    </tr>';
    
    $item++;
}

if (empty($detalles_html)) {
    $detalles_html = '
    <tr>
        <td class="text-center">1</td>
        <td class="text-center">0.00</td>
        <td class="text-center">UND</td>
        <td class="text-left">No hay productos en esta compra</td>
        <td class="text-left">-</td>
        <td class="text-right">0.00</td>
        <td class="text-right">0.00</td>
    </tr>';
}

// Cálculos finales
$igv = $igv_total_acumulado;
$total_con_igv = $subtotal + $igv;

// Aplicar detracción/retención/percepción
$monto_detraccion = 0;
$monto_retencion = 0;
$monto_percepcion = 0;
$total_final = $total_con_igv;

if ($tiene_detraccion) {
    $monto_detraccion = $total_con_igv * ($porcentaje_detraccion / 100);
    $total_final -= $monto_detraccion;
}

if ($tiene_retencion) {
    $monto_retencion = $total_con_igv * ($porcentaje_retencion / 100);
    $total_final -= $monto_retencion;
}

if ($tiene_percepcion) {
    $monto_percepcion = $total_con_igv * ($porcentaje_percepcion / 100);
    $total_final += $monto_percepcion;
}

$subtotal_formateado = number_format($subtotal, 2);
$igv_formateado = number_format($igv, 2);
$total_con_igv_formateado = number_format($total_con_igv, 2);
$total_formateado = number_format($total_final, 2);

$nombre_archivo = $codigo_orden . "_" . date('Ymd') . ".pdf";

require '../_vista/v_compras_pdf.php';

ini_set("memory_limit", "128M");

use Dompdf\Dompdf;

$dompdf = new Dompdf();
$dompdf->setPaper('A4', 'portrait');
$html = mb_convert_encoding($html, 'UTF-8', mb_list_encodings());
$dompdf->loadHtml($html);
$dompdf->render();
$dompdf->stream($nombre_archivo, array("Attachment" => false));
?>