<?php
header('Content-Type: application/json; charset=utf-8');
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_salidas.php");

if (!verificarPermisoEspecifico('aprobar_salidas')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'SALIDAS', 'APROBAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

$id_salida = isset($_POST['id_salida']) ? intval($_POST['id_salida']) : null;

if ($id_salida) {
    $resultado = AprobarSalida($id_salida, $id_personal);

    if ($resultado) {
        echo json_encode([
            "tipo_mensaje" => "success",
            "mensaje" => "Salida recepcionada exitosamente."
        ]);
    } else {
        echo json_encode([
            "tipo_mensaje" => "error",
            "mensaje" => "Error al recepcionar la salida. Ya podría estar recepcionada o anulada."
        ]);
    }
} else {
    echo json_encode([
        "tipo_mensaje" => "error",
        "mensaje" => "ID de salida no proporcionado."
    ]);
}
exit;