<?php
require_once '../_conexion/sesion.php';
require_once '../_modelo/m_auditoria.php';
require_once '../_modelo/m_salidas.php';
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

// Verificar si se recibió el ID de la salida
if (!isset($_GET['id']) || $_GET['id'] == "") {
    $titulo = 'Error en datos';
    $mensaje = 'Ocurrió un error al obtener la información de la salida';
?>
    <script Language="JavaScript">
        location.href = 'salidas_mostrar.php?error=true&titulo=<?php echo $titulo; ?>&mensaje=<?php echo $mensaje; ?>';
    </script>
<?php
    exit;
}

$id_salida = intval($_GET['id']);

// Preparar logo en base64
$imagenLogo = "../_complemento/images/icon.png";
$imagenLogoBase64 = "";
if (file_exists($imagenLogo)) {
    $imagenLogoBase64 = "data:image/png;base64," . base64_encode(file_get_contents($imagenLogo));
}

// Obtener datos de la salida
$salida_data = ConsultarSalida($id_salida);
$salida_detalle = ConsultarSalidaDetalle($id_salida);

if (empty($salida_data)) {
    $titulo = 'Error en datos';
    $mensaje = 'Salida no encontrada';
?>
    <script Language="JavaScript">
        location.href = 'salidas_mostrar.php?error=true&titulo=<?php echo $titulo; ?>&mensaje=<?php echo $mensaje; ?>';
    </script>
<?php
    exit;
}

$salida = $salida_data[0];

// Preparar datos para el PDF
$numero_salida = str_pad($salida['id_salida'], 6, '0', STR_PAD_LEFT);
$ndoc_salida = $salida['ndoc_salida'] ?? '';
$tipo_material = $salida['nom_material_tipo'] ?? 'NO ESPECIFICADO';
$fecha_salida = date('d/m/Y H:i', strtotime($salida['fec_salida']));
$fecha_requerida = date('d/m/Y', strtotime($salida['fec_req_salida']));
$nom_personal = trim(($salida['nom_personal'] ?? '') . ' ' . ($salida['ape_personal'] ?? ''));
$observaciones = $salida['obs_salida'] ?? 'Sin observaciones especiales';

// Datos de origen
$almacen_origen = $salida['nom_almacen_origen'] ?? '';
$ubicacion_origen = $salida['nom_ubicacion_origen'] ?? '';
$personal_encargado = '';
if (!empty($salida['nom_encargado']) && !empty($salida['ape_encargado'])) {
    $personal_encargado = trim($salida['nom_encargado'] . ' ' . $salida['ape_encargado']);
} else {
    $personal_encargado = 'No especificado';
}

// Datos de destino
$almacen_destino = $salida['nom_almacen_destino'] ?? '';
$ubicacion_destino = $salida['nom_ubicacion_destino'] ?? '';
$personal_recibe = '';
if (!empty($salida['nom_recibe']) && !empty($salida['ape_recibe'])) {
    $personal_recibe = trim($salida['nom_recibe'] . ' ' . $salida['ape_recibe']);
} else {
    $personal_recibe = 'No especificado';
}

// Estado de la salida
$estado_texto = '';
switch($salida['est_salida']) {
    case 1: $estado_texto = 'ACTIVO'; break;
    case 0: $estado_texto = 'INACTIVO'; break;
    default: $estado_texto = 'DESCONOCIDO';
}

// Preparar detalles de la salida
$detalles_html = '';
$item = 1;

foreach ($salida_detalle as $detalle) {
    $descripcion = htmlspecialchars($detalle['prod_salida_detalle'], ENT_QUOTES, 'UTF-8');
    $cantidad = number_format($detalle['cant_salida_detalle'], 2);
    
    // Unidad de medida
    $unidad = isset($detalle['nom_unidad_medida']) ? htmlspecialchars($detalle['nom_unidad_medida'], ENT_QUOTES, 'UTF-8') : 'UND';
    
    $detalles_html .= '
    <tr>
        <td class="text-center">' . $item . '</td>
        <td class="text-left">' . $descripcion . '</td>
        <td class="text-center">' . $cantidad . '</td>
        <td class="text-center">' . $unidad . '</td>
    </tr>';
    
    $item++;
}

// Si no hay detalles, agregar una fila vacía para evitar tabla vacía
if (empty($detalles_html)) {
    $detalles_html = '
    <tr>
        <td class="text-center">1</td>
        <td class="text-left">No hay materiales en esta salida</td>
        <td class="text-center">0.00</td>
        <td class="text-center">UND</td>
    </tr>';
}

// Nombre del archivo PDF
$nombre_archivo = "SALIDA_" . $numero_salida . "_" . date('Ymd') . ".pdf";

// Incluir la vista del PDF
require '../_vista/v_salidas_pdf.php';

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