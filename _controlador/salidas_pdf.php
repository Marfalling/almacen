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
$numero_salida = $salida['id_salida'] ?? 'NO ESPECIFICADO';
$ndoc_salida = $salida['ndoc_salida'] ?? '';
$tipo_material = $salida['nom_material_tipo'] ?? 'NO ESPECIFICADO';
$fecha_salida = date('d/m/Y H:i', strtotime($salida['fec_salida']));
$fecha_requerida = date('d/m/Y', strtotime($salida['fec_req_salida']));
$nom_personal = trim($salida['nom_personal'] ?? '');
$observaciones = $salida['obs_salida'] ?? 'Sin observaciones especiales';

// Datos de origen
$almacen_origen = $salida['nom_almacen_origen'] ?? '';
$ubicacion_origen = $salida['nom_ubicacion_origen'] ?? '';
$personal_encargado = '';
if (!empty($salida['nom_encargado'])) {
    $personal_encargado = trim($salida['nom_encargado']);
} else {
    $personal_encargado = 'No especificado';
}

// Datos de destino
$almacen_destino = $salida['nom_almacen_destino'] ?? '';
$ubicacion_destino = $salida['nom_ubicacion_destino'] ?? '';
$personal_recibe = '';
if (!empty($salida['nom_recibe'])) {
    $personal_recibe = trim($salida['nom_recibe']);
} else {
    $personal_recibe = 'No especificado';
}

// Estado de la salida
$estado_texto = '';
$estado_color = '';
$estado_bg = '';

switch($salida['est_salida']) {
    case 0:
        $estado_texto = 'ANULADA';
        $estado_color = '#dc3545';
        $estado_bg = '#f8d7da';
        break;
    
    case 1:
        $estado_texto = 'PENDIENTE';
        $estado_color = '#ffc107';
        $estado_bg = '#fff3cd';
        break;
    
    case 2:
        $estado_texto = 'RECEPCIONADA';
        $estado_color = '#17a2b8';
        $estado_bg = '#d1ecf1';
        break;
    
    case 3:
        $estado_texto = 'APROBADA';
        $estado_color = '#28a745';
        $estado_bg = '#d4edda';
        break;
    
    case 4:
        $estado_texto = 'DENEGADA';
        $estado_color = '#6c757d';
        $estado_bg = '#d6d8db';
        break;
    
    default:
        $estado_texto = 'DESCONOCIDO';
        $estado_color = '#6c757d';
        $estado_bg = '#e2e3e5';
}

// 🔹 PREPARAR DETALLES DE LA SALIDA CON NOMBRE DE PRODUCTO Y UNIDAD
$detalles_html = '';
$item = 1;

foreach ($salida_detalle as $detalle) {
    //  LÓGICA PARA OBTENER EL NOMBRE DEL PRODUCTO (igual que en v_salidas_editar.php)
    $nombre_producto = '';
    if (!empty($detalle['nom_producto'])) {
        $nombre_producto = $detalle['nom_producto'];
    } elseif (!empty($detalle['prod_salida_detalle'])) {
        $nombre_producto = $detalle['prod_salida_detalle'];
    } else {
        $nombre_producto = 'Producto ID ' . ($detalle['id_producto'] ?? 'N/A');
    }
    
    $descripcion = htmlspecialchars($nombre_producto, ENT_QUOTES, 'UTF-8');
    $cantidad = number_format($detalle['cant_salida_detalle'], 2);
    
    //  UNIDAD DE MEDIDA con mejor manejo de datos
    $unidad = 'UND'; // Valor por defecto
    if (!empty($detalle['nom_unidad_medida'])) {
        $unidad = htmlspecialchars($detalle['nom_unidad_medida'], ENT_QUOTES, 'UTF-8');
    } elseif (!empty($detalle['abr_unidad_medida'])) {
        $unidad = htmlspecialchars($detalle['abr_unidad_medida'], ENT_QUOTES, 'UTF-8');
    }
    
    $detalles_html .= '
    <tr>
        <td class="text-center">' . $item . '</td>
        <td class="text-left">' . $descripcion . '</td>
        <td class="text-center">' . $cantidad . '</td>
        <td class="text-center">' . $unidad . '</td>
    </tr>';
    
    $item++;
}

// Si no hay detalles, agregar una fila vacía
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