<?php
require_once '../_conexion/sesion.php';
require_once '../_modelo/m_auditoria.php';
require_once '../_modelo/m_devolucion.php';
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
        location.href = 'devoluciones_mostrar.php?error=true&titulo=<?php echo $titulo; ?>&mensaje=<?php echo $mensaje; ?>';
    </script>
<?php
    exit;
}

$id_devolucion = intval($_GET['id']);

// Preparar logo en base64
$imagenLogo = "../_complemento/images/icon.png";
$imagenLogoBase64 = "";
if (file_exists($imagenLogo)) {
    $imagenLogoBase64 = "data:image/png;base64," . base64_encode(file_get_contents($imagenLogo));
}

// Obtener datos de la salida
$devolucion_data = ConsultarDevolucion($id_devolucion);
$devolucion_detalle = ConsultarDevolucionDetalle($id_devolucion);

if (empty($devolucion_data)) {
    $titulo = 'Error en datos';
    $mensaje = 'Salida no encontrada';
?>
    <script Language="JavaScript">
        location.href = 'devoluciones_mostrar.php?error=true&titulo=<?php echo $titulo; ?>&mensaje=<?php echo $mensaje; ?>';
    </script>
<?php
    exit;
}

$devolucion = $devolucion_data[0];

// Preparar datos para el PDF
$numero_devolucion = $devolucion['id_devolucion'] ?? '';
$fecha_devolucion = date('d/m/Y H:i', strtotime($devolucion['fec_devolucion']));
$nom_personal = trim(($devolucion['nom_personal'] ?? '') . ' ' . ($devolucion['ape_personal'] ?? ''));
$observaciones = $devolucion['obs_devolucion'] ?? 'Sin observaciones especiales';
$almacen = $devolucion['nom_almacen'] ?? '';
$ubicacion = $devolucion['nom_ubicacion'] ?? '';
$cliente_destino = $devolucion['nom_cliente_destino'] ?? 'No especificado';

$personal_encargado = '';
if (!empty($devolucion['nom_personal']) && !empty($devolucion['ape_personal'])) {
    $personal_encargado = trim($devolucion['nom_personal'] . ' ' . $devolucion['ape_personal']);
} else {
    $personal_encargado = 'No especificado';
}

// Estado de la devolucion
$estado_texto = '';
switch($devolucion['est_devolucion']) {
    case 2: $estado_texto = 'CONFIRMADO'; break;
    case 1: $estado_texto = 'ACTIVO'; break;
    case 0: $estado_texto = 'ANULADO'; break;
    default: $estado_texto = 'DESCONOCIDO';
}

// Preparar detalles de la devolucion
$detalles_html = '';
$item = 1;

foreach ($devolucion_detalle as $detalle) {
    $descripcion = htmlspecialchars($detalle['det_devolucion_detalle'], ENT_QUOTES, 'UTF-8');
    $cantidad = number_format($detalle['cant_devolucion_detalle'], 2);
    
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
        <td class="text-left">No hay materiales en esta devolucion</td>
        <td class="text-center">0.00</td>
        <td class="text-center">UND</td>
    </tr>';
}

// Nombre del archivo PDF
$nombre_archivo = "DEVOLUCION_" . $numero_devolucion . "_" . date('Ymd') . ".pdf";

// Incluir la vista del PDF
require '../_vista/v_devoluciones_pdf.php';

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