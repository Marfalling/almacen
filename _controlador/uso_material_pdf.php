<?php
require_once '../_conexion/sesion.php';
require_once '../_modelo/m_auditoria.php';
require_once '../_modelo/m_uso_material.php';
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

// Verificar si se recibió el ID del uso de material
if (!isset($_GET['id']) || $_GET['id'] == "") {
    $titulo = 'Error en datos';
    $mensaje = 'Ocurrió un error al obtener la información del uso de material';
?>
    <script Language="JavaScript">
        location.href = 'uso_material_mostrar.php?error=true&titulo=<?php echo $titulo; ?>&mensaje=<?php echo $mensaje; ?>';
    </script>
<?php
    exit;
}

$id_uso_material = intval($_GET['id']);

// Preparar logo en base64
$imagenLogo = "../_complemento/images/icon.png";
$imagenLogoBase64 = "";
if (file_exists($imagenLogo)) {
    $imagenLogoBase64 = "data:image/png;base64," . base64_encode(file_get_contents($imagenLogo));
}

// Obtener datos del uso de material
$uso_data = ConsultarUsoMaterial($id_uso_material);
$uso_detalle = ConsultarUsoMaterialDetalle($id_uso_material);

if (empty($uso_data)) {
    $titulo = 'Error en datos';
    $mensaje = 'Uso de material no encontrado';
?>
    <script Language="JavaScript">
        location.href = 'uso_material_mostrar.php?error=true&titulo=<?php echo $titulo; ?>&mensaje=<?php echo $mensaje; ?>';
    </script>
<?php
    exit;
}

$uso = $uso_data[0];

// Preparar datos para el PDF
$numero_uso = str_pad($uso['id_uso_material'], 6, '0', STR_PAD_LEFT);
$nombre_almacen = $uso['nom_almacen'] ?? 'NO ESPECIFICADO';
$nombre_obra = $uso['nom_obra'] ?? 'NO ESPECIFICADO';
$nombre_cliente = $uso['nom_cliente'] ?? 'NO ESPECIFICADO';
$nombre_ubicacion = $uso['nom_ubicacion'] ?? 'NO ESPECIFICADO';
$fecha_uso = date('d/m/Y H:i', strtotime($uso['fec_uso_material']));
$nom_registrado = trim($uso['nom_registrado'] ?? '');
$nom_solicitante = trim($uso['nom_solicitante'] ?? '');

// Estado del uso de material
$estado_texto = '';
$color_estado = '';
switch($uso['est_uso_material']) {
    case 1: 
        $estado_texto = 'PENDIENTE'; 
        $color_estado = '#6c757d';
        break;
    case 2: 
        $estado_texto = 'REGISTRADO'; 
        $color_estado = '#6c757d';
        break;
    case 0: 
        $estado_texto = 'ANULADO'; 
        $color_estado = '#6c757d';
        break;
    default: 
        $estado_texto = 'DESCONOCIDO';
        $color_estado = '#6c757d';
}

// Preparar detalles del uso de material
$detalles_html = '';
$item = 1;

foreach ($uso_detalle as $detalle) {
    $descripcion = htmlspecialchars($detalle['nom_producto'], ENT_QUOTES, 'UTF-8');
    $cantidad = number_format($detalle['cant_uso_material_detalle'], 2);
    $unidad = isset($detalle['nom_unidad_medida']) ? htmlspecialchars($detalle['nom_unidad_medida'], ENT_QUOTES, 'UTF-8') : 'UND';
    $observaciones = !empty($detalle['obs_uso_material_detalle']) 
        ? htmlspecialchars($detalle['obs_uso_material_detalle'], ENT_QUOTES, 'UTF-8') 
        : '-';
    
    
    $detalles_html .= '
    <tr>
        <td class="text-center">' . $item . '</td>
        <td class="text-center">' . $cantidad . '</td>
        <td class="text-center">' . $unidad . '</td>
        <td class="text-left">' . $descripcion . '</td>
        <td class="text-left">' . $observaciones . '</td>
    </tr>';
    
    $item++;
}

// Si no hay detalles, agregar una fila vacía para evitar tabla vacía
if (empty($detalles_html)) {
    $detalles_html = '
    <tr>
        <td class="text-center">-</td>
        <td class="text-center">0.00</td>
        <td class="text-center">-</td>
        <td class="text-left">No hay materiales en este uso</td>
        <td class="text-left">-</td>
    </tr>';
}

// Nombre del archivo PDF
$nombre_archivo = "USO_MATERIAL_" . $numero_uso . "_" . date('Ymd') . ".pdf";

// Incluir la vista del PDF
require '../_vista/v_uso_material_pdf.php';

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