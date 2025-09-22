<?php
require_once '../_conexion/sesion.php';
require_once '../_modelo/m_auditoria.php';
require_once '../_modelo/m_devolucion.php';
require_once '../_complemento/dompdf/autoload.inc.php';

use Dompdf\Dompdf;

setlocale(LC_TIME, 'es_ES.UTF-8');
date_default_timezone_set('America/Lima');


$fecha_actual = new DateTime();
$dias_esp = [
    'Monday' => 'lunes', 'Tuesday' => 'martes', 'Wednesday' => 'miércoles',
    'Thursday' => 'jueves', 'Friday' => 'viernes', 'Saturday' => 'sábado', 'Sunday' => 'domingo'
];
$meses_esp = [
    'January' => 'enero', 'February' => 'febrero', 'March' => 'marzo',
    'April' => 'abril', 'May' => 'mayo', 'June' => 'junio',
    'July' => 'julio', 'August' => 'agosto', 'September' => 'septiembre',
    'October' => 'octubre', 'November' => 'noviembre', 'December' => 'diciembre'
];
$fecha_formateada = str_replace(
    array_keys($dias_esp), array_values($dias_esp),
    $fecha_actual->format('l d \d\e F \d\e Y, H:i:s')
);
$fecha_formateada = str_replace(array_keys($meses_esp), array_values($meses_esp), $fecha_formateada);


// ID 
if (empty($_GET['id'])) {
    $titulo = 'Error en datos';
    $mensaje = 'Ocurrió un error al obtener la información de la devolución';
    echo "<script>location.href = 'devoluciones_mostrar.php?error=true&titulo=$titulo&mensaje=$mensaje';</script>";
    exit;
}

$id_devolucion = intval($_GET['id']);

// logo en base64
$imagenLogo = "../_complemento/images/icon.png";
$imagenLogoBase64 = "";
if (file_exists($imagenLogo)) {
    $imagenLogoBase64 = "data:image/png;base64," . base64_encode(file_get_contents($imagenLogo));
}

// Consultar datos
$devolucion_data = ConsultarDevolucion($id_devolucion);
$devolucion_detalle = ConsultarDevolucionDetalle($id_devolucion);

if (empty($devolucion_data)) {
    $titulo = 'Error en datos';
    $mensaje = 'Devolución no encontrada';
    echo "<script>location.href = 'devoluciones_mostrar.php?error=true&titulo=$titulo&mensaje=$mensaje';</script>";
    exit;
}

$devolucion = $devolucion_data[0];

// Preparar datos generales
$numero_devolucion = str_pad($devolucion['id_devolucion'], 6, '0', STR_PAD_LEFT);
$fecha_devolucion  = date('d/m/Y H:i', strtotime($devolucion['fec_devolucion']));
$nom_personal      = trim(($devolucion['nom_personal'] ?? '') . ' ' . ($devolucion['ape_personal'] ?? ''));
$observaciones     = $devolucion['obs_devolucion'] ?? 'Sin observaciones especiales';
$almacen           = $devolucion['nom_almacen'] ?? '';
$ubicacion         = $devolucion['nom_ubicacion'] ?? '';

switch ($devolucion['est_devolucion']) {
    case 1:  $estado_texto = 'ACTIVO'; break;
    case 0:  $estado_texto = 'INACTIVO'; break;
    default: $estado_texto = 'DESCONOCIDO';
}

// Preparar detalles
$detalles_html = '';
$item = 1;

if (!empty($devolucion_detalle)) {
    foreach ($devolucion_detalle as $detalle) {
        $cantidad = number_format($detalle['cant_devolucion_detalle'], 2);

        // Descripción = nombre producto + detalle (si existe)
        $descripcion = htmlspecialchars($detalle['nom_producto'], ENT_QUOTES, 'UTF-8');
        if (!empty($detalle['det_devolucion_detalle'])) {
            $descripcion .= ' - ' . htmlspecialchars($detalle['det_devolucion_detalle'], ENT_QUOTES, 'UTF-8');
        }

        $unidad = htmlspecialchars($detalle['nom_unidad_medida'] ?? 'UND', ENT_QUOTES, 'UTF-8');

        $detalles_html .= "
        <tr>
            <td class='text-center'>{$item}</td>
            <td class='text-left'>{$descripcion}</td>
            <td class='text-center'>{$cantidad}</td>
            <td class='text-center'>{$unidad}</td>
        </tr>";
        $item++;
    }
} else {
    $detalles_html = "
    <tr>
        <td class='text-center'>1</td>
        <td class='text-left'>No hay materiales en esta devolución</td>
        <td class='text-center'>0.00</td>
        <td class='text-center'>UND</td>
    </tr>";
}

// Generar PDF
$nombre_archivo = "DEVOLUCION_" . $numero_devolucion . "_" . date('Ymd') . ".pdf";

ob_start();
require '../_vista/v_devoluciones_pdf.php';
$html = ob_get_clean();

$dompdf = new Dompdf();
$dompdf->setPaper('A4', 'portrait');
$html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
$dompdf->loadHtml($html);
$dompdf->render();
$dompdf->stream($nombre_archivo, ["Attachment" => false]); // mostrar en navegador
exit;
?>