<?php
header('Content-Type: application/json; charset=utf-8');

require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");
require_once("../_modelo/m_pedidos.php");

if (!verificarPermisoEspecifico('aprobar_pedidos')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'PEDIDOS', 'APROBAR TECNICA');
    echo json_encode([
        "tipo_mensaje" => "error",
        "mensaje" => "No tienes permisos para aprobar pedidos."
    ]);
    exit;
}

$id_pedido = isset($_POST['id_pedido']) ? intval($_POST['id_pedido']) : null;

if ($id_pedido) {
    // Llama a la función del modelo que aprobará el pedido técnicamente
    $resultado = AprobarPedidoTecnica($id_pedido, $id_personal);

    if ($resultado) {
        //  AUDITORÍA: APROBACIÓN TÉCNICA EXITOSA
        GrabarAuditoria($id, $usuario_sesion, 'APROBAR', 'PEDIDOS', "ID: $id_pedido (APROBACIÓN TÉCNICA)");
        
        echo json_encode([
            "tipo_mensaje" => "success",
            "mensaje" => "Pedido aprobado técnicamente."
        ]);
    } else {
        //  AUDITORÍA: ERROR AL APROBAR TÉCNICAMENTE
        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL APROBAR', 'PEDIDOS', "ID: $id_pedido (APROBACIÓN TÉCNICA)");
        
        echo json_encode([
            "tipo_mensaje" => "error",
            "mensaje" => "Error al aprobar técnicamente el pedido."
        ]);
    }
} else {
    echo json_encode([
        "tipo_mensaje" => "error",
        "mensaje" => "ID de pedido no proporcionado."
    ]);
}
exit;