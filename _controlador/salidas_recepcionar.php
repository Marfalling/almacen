<?php
//=======================================================================
// CONTROLADOR: salidas_recepcionar.php
// Recepciona una salida aprobada (cambia estado 3 → 2)
//=======================================================================

header('Content-Type: application/json; charset=utf-8');
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_salidas.php");
require_once("../_modelo/m_auditoria.php");

if (!verificarPermisoEspecifico('recepcionar_salidas')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'SALIDAS', 'RECEPCIONAR');
    
    echo json_encode([
        "success" => false,
        "message" => "No tienes permiso para recepcionar salidas."
    ]);
    exit;
}

$id_salida = isset($_POST['id_salida']) ? intval($_POST['id_salida']) : null;

if (!$id_salida) {
    echo json_encode([
        "success" => false,
        "message" => "ID de salida no proporcionado."
    ]);
    exit;
}

// Llamar a la función de recepción
$resultado = RecepcionarSalida($id_salida, $id_personal);

if ($resultado) {
    
    //  AUDITORÍA: RECEPCIÓN EXITOSA 
    GrabarAuditoria($id, $usuario_sesion, 'RECEPCIONAR', 'SALIDAS', "ID: $id_salida (RECEPCIÓN CONFIRMADA)");
    
    echo json_encode([
        "success" => true,
        "message" => "✅ Salida recepcionada exitosamente."
    ]);
} else {
    
    //  AUDITORÍA: ERROR AL RECEPCIONAR
    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL RECEPCIONAR', 'SALIDAS', "ID: $id_salida | Estado no válido o error en BD");
    
    echo json_encode([
        "success" => false,
        "message" => "❌ Error al recepcionar. La salida podría estar en un estado no válido."
    ]);
}

exit;
?>