<?php
header('Content-Type: application/json; charset=utf-8');

require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");
require_once("../_modelo/m_compras.php");

if (!verificarPermisoEspecifico('aprobar_compras')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'COMPRA', 'APROBAR');
    echo json_encode([
        "tipo_mensaje" => "error",
        "mensaje" => "No tienes permisos para aprobar compras."
    ]);
    exit;
}

$id_compra = isset($_POST['id_compra']) ? intval($_POST['id_compra']) : null;

if ($id_compra) {
    $resultado = AprobarCompra($id_compra, $id_personal);

    if ($resultado) {
        //  AUDITORÍA: APROBACIÓN EXITOSA
        GrabarAuditoria($id, $usuario_sesion, 'APROBAR', 'COMPRA', "ID: $id_compra");
        
        echo json_encode([
            "tipo_mensaje" => "success",
            "mensaje" => "Compra aprobada exitosamente."
        ]);
    } else {
        //  AUDITORÍA: ERROR AL APROBAR
        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL APROBAR', 'COMPRA', "ID: $id_compra");
        
        echo json_encode([
            "tipo_mensaje" => "error",
            "mensaje" => "Error al aprobar la compra."
        ]);
    }
} else {
    echo json_encode([
        "tipo_mensaje" => "error",
        "mensaje" => "ID de compra no proporcionado."
    ]);
}
exit;