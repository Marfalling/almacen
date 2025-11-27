<?php
//=======================================================================
// CONTROLADOR: salidas_denegar.php (VERSIÓN ACTUALIZADA)
//=======================================================================
// Este controlador ahora usa DenegarSalidaConFlujoCompleto() 
// para manejar el bloqueo de ubicaciones y la conversión automática OS → OC
//=======================================================================

session_start();
header('Content-Type: application/json');

// Validar sesión
if (!isset($_SESSION['id_personal'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Sesión no válida. Por favor, inicia sesión nuevamente.'
    ]);
    exit;
}

// Cargar modelo
require_once("../_modelo/m_salidas.php");
require_once("../_modelo/m_pedidos.php");

// ============================================
// VALIDAR DATOS
// ============================================
if (!isset($_POST['id_salida']) || empty($_POST['id_salida'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de salida no especificado'
    ]);
    exit;
}

$id_salida = intval($_POST['id_salida']);
$id_personal = intval($_SESSION['id_personal']);
$motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : 'Salida denegada por el usuario';

// ============================================
// VALIDAR QUE LA SALIDA EXISTE Y ESTÁ PENDIENTE
// ============================================
include("../_conexion/conexion.php");

$sql_validar = "SELECT id_salida, est_salida, id_pedido 
                FROM salida 
                WHERE id_salida = $id_salida";

$res_validar = mysqli_query($con, $sql_validar);

if (!$res_validar || mysqli_num_rows($res_validar) == 0) {
    mysqli_close($con);
    echo json_encode([
        'success' => false,
        'message' => 'Salida no encontrada'
    ]);
    exit;
}

$salida_data = mysqli_fetch_assoc($res_validar);
$estado_actual = intval($salida_data['est_salida']);
$id_pedido = intval($salida_data['id_pedido']);

mysqli_close($con);

// Validar estado
if ($estado_actual != 1) {
    $estados = [
        0 => 'ANULADA',
        2 => 'RECEPCIONADA',
        3 => 'APROBADA',
        4 => 'DENEGADA'
    ];
    $nombre_estado = $estados[$estado_actual] ?? 'DESCONOCIDO';
    
    echo json_encode([
        'success' => false,
        'message' => "No se puede denegar. La salida está en estado: $nombre_estado"
    ]);
    exit;
}

// ============================================
//  EJECUTAR DENEGACIÓN CON FLUJO COMPLETO
// ============================================
error_log("🔴 INICIANDO DENEGACIÓN CON FLUJO COMPLETO - Salida ID: $id_salida | Personal: $id_personal");

// Llamar a la función mejorada que hace TODO el flujo
$resultado = DenegarSalidaConFlujoCompleto($id_salida, $id_personal, $motivo);

// ============================================
// PROCESAR RESPUESTA
// ============================================
if ($resultado['success']) {
    //  ÉXITO
    error_log(" Denegación exitosa - Salida ID: $id_salida");
    error_log("   Items convertidos a OC: " . ($resultado['total_convertidos'] ?? 0));
    
    // Construir mensaje detallado
    $mensaje_respuesta = "✅ Salida denegada correctamente.\n\n";
    
    // Agregar información de items convertidos
    if (isset($resultado['items_convertidos']) && count($resultado['items_convertidos']) > 0) {
        $mensaje_respuesta .= " CONVERSIÓN AUTOMÁTICA OS → OC:\n\n";
        
        foreach ($resultado['items_convertidos'] as $item) {
            $mensaje_respuesta .= sprintf(
                "• %s: %.2f unidades\n  (OS: %.2f → OC: %.2f)\n\n",
                $item['producto'],
                $item['cantidad'],
                $item['cant_os_nueva'],
                $item['cant_oc_nueva']
            );
        }
        
        $mensaje_respuesta .= "Las cantidades se han convertido automáticamente a Órdenes de Compra porque no hay más ubicaciones disponibles.\n\n";
    }
    
    // Agregar información de items con ubicaciones alternativas
    if (isset($resultado['items_procesados'])) {
        $items_con_ubicaciones = array_filter($resultado['items_procesados'], function($item) {
            return $item['accion'] == 'os_disponible';
        });
        
        if (count($items_con_ubicaciones) > 0) {
            $mensaje_respuesta .= " ITEMS CON UBICACIONES ALTERNATIVAS:\n\n";
            
            foreach ($items_con_ubicaciones as $item) {
                $mensaje_respuesta .= "• {$item['producto']}\n";
            }
            
            $mensaje_respuesta .= "\nEstos items tienen stock en otras ubicaciones y pueden continuar como Órdenes de Salida.\n";
        }
    }
    
    // Información sobre el bloqueo
    $mensaje_respuesta .= "\n La ubicación de origen ha sido BLOQUEADA para estos productos y no se utilizará en futuras salidas.";
    
    echo json_encode([
        'success' => true,
        'message' => $mensaje_respuesta,
        'denegacion_exitosa' => true,
        'flujo_completo' => $resultado['flujo_completo'] ?? false,
        'total_convertidos' => $resultado['total_convertidos'] ?? 0,
        'items_convertidos' => $resultado['items_convertidos'] ?? [],
        'id_pedido' => $id_pedido
    ]);
    
} else {
    //  ERROR
    error_log(" Error al denegar salida: " . $resultado['message']);
    
    echo json_encode([
        'success' => false,
        'message' => $resultado['message']
    ]);
}

exit;
?>