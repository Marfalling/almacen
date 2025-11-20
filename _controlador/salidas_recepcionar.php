<?php
//=======================================================================
// CONTROLADOR: salidas_recepcionar.php
// Recepciona una salida aprobada (cambia estado 3 → 2)
//=======================================================================

header('Content-Type: application/json; charset=utf-8');
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_salidas.php");

if (!verificarPermisoEspecifico('recepcionar_salidas')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'SALIDAS', 'RECEPCIONAR');
    
    echo json_encode([
        "success" => false,
        "message" => "No tienes permiso para aprobar salidas."
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

//  Llamar a la función de recepción
$resultado = RecepcionarSalida($id_salida, $id_personal);

if ($resultado) {
    echo json_encode([
        "success" => true,
        "message" => "✅ Salida recepcionada exitosamente."
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "❌ Error al recepcionar. La salida podría estar en un estado no válido."
    ]);
}

exit;