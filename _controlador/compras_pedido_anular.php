<?php
header('Content-Type: application/json; charset=utf-8');

require_once("../_conexion/sesion.php");
require_once("../_modelo/m_anulaciones.php");
require_once("../_modelo/m_auditoria.php"); 

if (!isset($id_personal) || empty($id_personal)) {
    echo json_encode([
        "tipo_mensaje" => "error",
        "mensaje" => "Sesión no válida."
    ]);
    exit;
}

$id_compra = isset($_POST['id_compra']) ? intval($_POST['id_compra']) : 0;
$id_pedido = isset($_POST['id_pedido']) ? intval($_POST['id_pedido']) : 0;

// Variables de sesión para auditoría
$id_usuario_sesion = $_SESSION['id'] ?? 0;
$usuario_sesion = $_SESSION['usuario_sesion'] ?? '';

if ($id_compra > 0 && $id_pedido > 0) {
    $resultado = AnularCompraPedido($id_compra, $id_pedido, $id_personal);

    if ($resultado) {
        
        //  AUDITORÍA: ANULACIÓN EXITOSA
        GrabarAuditoria($id_usuario_sesion, $usuario_sesion, 'ANULAR', 'COMPRAS', 
            "OC: $id_compra | Pedido: $id_pedido (ANULACIÓN COMPLETA)");
        
        echo json_encode([
            "tipo_mensaje" => "success",
            "mensaje" => "Orden de compra y pedido anulados correctamente."
        ]);
    } else {
        
        //  AUDITORÍA: ERROR AL ANULAR
        GrabarAuditoria($id_usuario_sesion, $usuario_sesion, 'ERROR AL ANULAR', 'COMPRAS', 
            "OC: $id_compra | Pedido: $id_pedido | Error al ejecutar anulación");
        
        echo json_encode([
            "tipo_mensaje" => "error",
            "mensaje" => "Error al anular."
        ]);
    }
} else {
    
    //  AUDITORÍA: PARÁMETROS INVÁLIDOS
    GrabarAuditoria($id_usuario_sesion, $usuario_sesion, 'ERROR AL ANULAR', 'COMPRAS', 
        "Parámetros inválidos - OC: $id_compra | Pedido: $id_pedido");
    
    echo json_encode([
        "tipo_mensaje" => "error",
        "mensaje" => "Parámetros inválidos."
    ]);
}
?>