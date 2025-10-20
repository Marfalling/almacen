<?php
require_once '../_conexion/sesion.php';
//require_once '../_modelo/m_auditoria.php';
require_once '../_modelo/m_pedidos.php';
require_once '../_complemento/dompdf/autoload.inc.php';
require_once '../_complemento/vendor/autoload.php';

setlocale(LC_TIME, 'es_ES.UTF-8');
date_default_timezone_set('America/Lima');
$fecha_actual = date("Y-m-d");
$fecha_completa = new DateTime();
//$fecha_formateada = date('l d \d\e F \d\e Y, H:i:s', $fecha_completa->getTimestamp());

$fecha_formateada = $fecha_completa->format('l d \d\e F \d\e Y, H:i:s');
 $dias_esp = ['Monday' => 'lunes', 'Tuesday' => 'martes', 'Wednesday' => 'miércoles', 
             'Thursday' => 'jueves', 'Friday' => 'viernes', 'Saturday' => 'sábado', 'Sunday' => 'domingo'];
 $meses_esp = ['January' => 'enero', 'February' => 'febrero', 'March' => 'marzo', 
               'April' => 'abril', 'May' => 'mayo', 'June' => 'junio',
               'July' => 'julio', 'August' => 'agosto', 'September' => 'septiembre',
               'October' => 'octubre', 'November' => 'noviembre', 'December' => 'diciembre'];
 $fecha_formateada = str_replace(array_keys($dias_esp), array_values($dias_esp), $fecha_formateada);
 $fecha_formateada = str_replace(array_keys($meses_esp), array_values($meses_esp), $fecha_formateada);

// Verificar si se recibió el ID del pedido
if (!isset($_GET['id']) || $_GET['id'] == "") {
    $titulo = 'Error en datos';
    $mensaje = 'Ocurrió un error al obtener la información del pedido';
?>
    <script Language="JavaScript">
        location.href = 'pedidos_mostrar.php?error=true&titulo=<?php echo $titulo; ?>&mensaje=<?php echo $mensaje; ?>';
    </script>
<?php
    exit;
}

$id_pedido = intval($_GET['id']);

// Preparar logo en base64
$imagenLogo = "../_complemento/images/icon.png";
$imagenLogoBase64 = "";
if (file_exists($imagenLogo)) {
    $imagenLogoBase64 = "data:image/png;base64," . base64_encode(file_get_contents($imagenLogo));
}

// Obtener datos del pedido
$pedido_data = ConsultarPedido($id_pedido);
$pedido_detalle = ConsultarPedidoDetalle($id_pedido);

if (empty($pedido_data)) {
    $titulo = 'Error en datos';
    $mensaje = 'Pedido no encontrado';
?>
    <script Language="JavaScript">
        location.href = 'pedidos_mostrar.php?error=true&titulo=<?php echo $titulo; ?>&mensaje=<?php echo $mensaje; ?>';
    </script>
<?php
    exit;
}

$pedido = $pedido_data[0];

// Preparar datos para el PDF
$codigo_pedido = $pedido['cod_pedido'];
$nombre_obra = $pedido['nom_obra'] ?? 'NO ESPECIFICADO';
$fecha_solicitud = date('d/m/Y', strtotime($pedido['fec_pedido']));
$fecha_requerida = isset($pedido['fec_req_pedido']) ? date('d/m/Y', strtotime($pedido['fec_req_pedido'])) : '';
$ot_pedido = $pedido['ot_pedido'] ?? '';
$nom_personal = $pedido['nom_personal'];
$lugar_entrega = $pedido['lug_pedido'] ?? '';
$telefono = $pedido['cel_pedido'] ?? '';
$almacen = $pedido['nom_almacen'] ?? '';
$ubicacion = $pedido['nom_ubicacion'] ?? '';
$aclaraciones = $pedido['acl_pedido'] ?? 'Sin aclaraciones especiales';

// Función para verificar si es imagen
function esImagen($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, ['jpg', 'jpeg', 'png', 'gif']);
}

// Función para convertir imagen a base64
function imagenABase64($rutaImagen) {
    if (!file_exists($rutaImagen)) {
        return false;
    }
    
    $datosImagen = file_get_contents($rutaImagen);
    if ($datosImagen === false) {
        return false;
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $tipoMime = finfo_buffer($finfo, $datosImagen);
    finfo_close($finfo);
    
    return 'data:' . $tipoMime . ';base64,' . base64_encode($datosImagen);
}

// CORREGIDO: Preparar detalles del pedido con la estructura correcta
$detalles_html = '';
$item = 1;
$todos_requisitos = array();

foreach ($pedido_detalle as $detalle) {
    $descripcion = htmlspecialchars($detalle['prod_pedido_detalle'], ENT_QUOTES, 'UTF-8');

    $ot_material = '';
    if (!empty($detalle['ot_pedido_detalle'])) {
        $ot_material = htmlspecialchars($detalle['ot_pedido_detalle'], ENT_QUOTES, 'UTF-8');
    }
    
    // Construir comentarios completos
    $comentarios_array = array();
    
    if (!empty($detalle['com_pedido_detalle'])) {
        $comentarios_array[] = htmlspecialchars($detalle['com_pedido_detalle'], ENT_QUOTES, 'UTF-8');
    }
    
    if (!empty($detalle['req_pedido'])) {
        $requisitos = str_replace('|', ' / ', $detalle['req_pedido']);
        $comentarios_array[] = "REQUISITOS: " . htmlspecialchars($requisitos, ENT_QUOTES, 'UTF-8');
        
        // Recopilar requisitos únicos
        $req_parts = explode('|', $detalle['req_pedido']);
        foreach ($req_parts as $req) {
            $req = trim($req);
            if (!empty($req) && !in_array($req, $todos_requisitos)) {
                $todos_requisitos[] = $req;
            }
        }
    }
    
    $comentarios = implode("<br>", $comentarios_array);
    
    // Preparar cantidad con unidad
    $cantidad_text = number_format($detalle['cant_pedido_detalle'], 0);
    

    
// Preparar archivos de imagen
$imagenes_html = '';
if (!empty($detalle['archivos'])) {
    $archivos = explode(',', $detalle['archivos']);
    $imagenes_validas = [];
    
    // Primero, filtrar solo las imágenes válidas
    foreach ($archivos as $archivo) {
        $archivo = trim($archivo);
        if (!empty($archivo) && esImagen($archivo)) {
            $ruta_archivo = "../_archivos/pedidos/" . $archivo;
            $imagen_base64 = imagenABase64($ruta_archivo);
            
            if ($imagen_base64) {
                $imagenes_validas[] = $imagen_base64;
            }
        }
    }
    
    // Generar HTML según el número de imágenes
    if (!empty($imagenes_validas)) {
        $total_imagenes = count($imagenes_validas);
        $contador_imagen = 1;
        
        foreach ($imagenes_validas as $imagen_base64) {
            // Determinar la clase según el número de imágenes
            $clase_imagen = '';
            if ($total_imagenes == 1) {
                $clase_imagen = ''; // Una sola imagen, tamaño completo
            } elseif ($total_imagenes == 2) {
                $clase_imagen = 'inline'; // Dos imágenes en línea
            } else {
                $clase_imagen = 'small'; // Tres o más imágenes, más pequeñas
            }
            
            $imagenes_html .= '<div class="imagen-item ' . $clase_imagen . '">';
            $imagenes_html .= '<img src="' . $imagen_base64 . '" alt="Imagen ' . $contador_imagen . '">';
            $imagenes_html .= '</div>';
            $contador_imagen++;
        }
    }
}
    
    // CORREGIDO: Generar HTML con las clases correctas que coinciden con la vista
    $detalles_html .= '
    <tr>
        <td class="item-col text-center">' . $item . '</td>
        <td class="cantidad-col text-center">' . $cantidad_text . '</td>
        <td class="descripcion-col">' . $descripcion . '</td>
        <td class="ot-col">' . (!empty($ot_material) ? $ot_material : '-') . '</td> 
        <td class="comentarios-col">
            <div class="comentarios-texto">' . $comentarios . '</div>
            ' . ($imagenes_html ? '<div class="imagenes-container">' . $imagenes_html . '</div>' : '') . '
        </td>
    </tr>';
    
    $item++;
}

// Preparar requisitos SST
$requisitos_text = implode(", ", $todos_requisitos);
if (empty($requisitos_text)) {
    $requisitos_text = 'N/A';
}

// Nombre del archivo PDF
$nombre_archivo = "PEDIDO_" . str_replace(' ', '_', $codigo_pedido) . "_" . date('Ymd') . ".pdf";

// CORREGIDO: Incluir la vista correcta
require '../_vista/v_pedido_pdf.php';

// Configurar memoria
ini_set("memory_limit", "128M");

use Dompdf\Dompdf;

$dompdf = new Dompdf();
$dompdf->setPaper('A4', 'portrait');
$html = mb_convert_encoding($html, 'UTF-8', mb_list_encodings());
$dompdf->loadHtml($html);
$dompdf->render();
$dompdf->stream($nombre_archivo, array("Attachment" => "0"));
?>