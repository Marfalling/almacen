<?php
require_once("../_conexion/sesion.php");

// VERIFICAR PERMISOS
if (!verificarPermisoEspecifico('anular_pedidos')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'PEDIDOS', 'VERIFICAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido");
    }

    $id_pedido = isset($_POST['id_pedido']) ? intval($_POST['id_pedido']) : 0;

    if (!$id_pedido) {
        throw new Exception("ID de pedido no recibido");
    }

    include("../_conexion/conexion.php");

    // ============================================
    // 🔍 VALIDACIÓN 1: VERIFICAR ESTADO DEL PEDIDO
    // ============================================
    $sql_estado = "SELECT est_pedido FROM pedido WHERE id_pedido = $id_pedido";
    $res_estado = mysqli_query($con, $sql_estado);
    
    if (!$res_estado) {
        throw new Exception("Error al verificar estado del pedido");
    }
    
    $row_estado = mysqli_fetch_assoc($res_estado);
    
    if (!$row_estado) {
        throw new Exception("Pedido no encontrado");
    }
    
    $estado_pedido = intval($row_estado['est_pedido']);
    
    // No se puede anular si ya está anulado (0) o si está atendido/finalizado (2, 3, 4, 5)
    if ($estado_pedido == 0) {
        throw new Exception("El pedido ya está anulado");
    }
    
    if ($estado_pedido >= 2) {
        $estados = [
            2 => "atendido",
            3 => "aprobado",
            4 => "ingresado",
            5 => "finalizado"
        ];
        $nombre_estado = $estados[$estado_pedido] ?? "procesado";
        throw new Exception("No se puede anular un pedido {$nombre_estado}");
    }

    // ============================================
    // 🔍 VALIDACIÓN 2: VERIFICAR ÓRDENES DE COMPRA
    // ============================================
    $sql_ordenes_compra = "SELECT COUNT(*) as total 
                           FROM compra 
                           WHERE id_pedido = $id_pedido 
                           AND est_compra != 0";
    
    $res_oc = mysqli_query($con, $sql_ordenes_compra);
    
    if (!$res_oc) {
        throw new Exception("Error al verificar órdenes de compra");
    }
    
    $row_oc = mysqli_fetch_assoc($res_oc);
    $total_ordenes_compra = intval($row_oc['total']);

    // ============================================
    // 🔍 VALIDACIÓN 3: VERIFICAR ÓRDENES DE SALIDA
    // ============================================
    $sql_ordenes_salida = "SELECT COUNT(*) as total 
                           FROM salida 
                           WHERE id_pedido = $id_pedido 
                           AND est_salida != 0";
    
    $res_os = mysqli_query($con, $sql_ordenes_salida);
    
    if (!$res_os) {
        throw new Exception("Error al verificar órdenes de salida");
    }
    
    $row_os = mysqli_fetch_assoc($res_os);
    $total_ordenes_salida = intval($row_os['total']);

    mysqli_close($con);

    // ============================================
    // 📤 RESPUESTA
    // ============================================
    echo json_encode([
        'success' => true,
        'puede_anular' => ($total_ordenes_compra == 0 && $total_ordenes_salida == 0),
        'tiene_ordenes_compra' => ($total_ordenes_compra > 0),
        'tiene_ordenes_salida' => ($total_ordenes_salida > 0),
        'total_ordenes_compra' => $total_ordenes_compra,
        'total_ordenes_salida' => $total_ordenes_salida
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    error_log("❌ ERROR en pedido_validar_anulacion.php: " . $e->getMessage());
    
    echo json_encode([
        'error' => true,
        'mensaje' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>