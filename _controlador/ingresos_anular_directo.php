<?php
//=======================================================================
// CONTROLADOR PARA ANULAR INGRESOS DIRECTOS
//=======================================================================

header('Content-Type: application/json; charset=utf-8');

require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

if (!verificarPermisoEspecifico('anular_ingresos')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'INGRESOS', 'ANULAR');
    echo json_encode([
        "tipo_mensaje" => "error",
        "mensaje" => "No tienes permisos para anular ingresos."
    ]);
    exit;
}

$id_ingreso = isset($_REQUEST['id_ingreso']) ? intval($_REQUEST['id_ingreso']) : 0;

if ($id_ingreso > 0) {
    require_once("../_modelo/m_ingreso.php");
    
    $resultado = AnularIngresoDirecto($id_ingreso, $id);

    if ($resultado['success']) {
        //  AUDITORÍA: ANULACIÓN EXITOSA
        GrabarAuditoria($id, $usuario_sesion, 'ANULAR', 'INGRESO DIRECTO', "ID: $id_ingreso");
        
        echo json_encode([
            "tipo_mensaje" => "success",
            "mensaje" => $resultado['message']
        ]);
    } else {
        //  AUDITORÍA: ERROR AL ANULAR
        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL ANULAR', 'INGRESO DIRECTO', "ID: $id_ingreso");
        
        echo json_encode([
            "tipo_mensaje" => "error",
            "mensaje" => $resultado['message']
        ]);
    }
} else {
    echo json_encode([
        "tipo_mensaje" => "error",
        "mensaje" => "ID de ingreso no proporcionado."
    ]);
}
exit;
?>