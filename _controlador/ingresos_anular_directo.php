<?php
//=======================================================================
// CONTROLADOR PARA ANULAR INGRESOS DIRECTOS
//=======================================================================
if (!verificarPermisoEspecifico('anular_ingresos')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'INGRESOS', 'ANULAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

$_REQUEST['id_ingreso'] = isset($_REQUEST['id_ingreso']) ? $_REQUEST['id_ingreso'] : null;

if ($_REQUEST['id_ingreso']) {
    require_once("../_conexion/sesion.php");
    require_once("../_modelo/m_ingreso.php");

    $id_ingreso = intval($_REQUEST['id_ingreso']);
    $resultado = AnularIngresoDirecto($id_ingreso, $id);

    if ($resultado['success']) {
        echo json_encode([
            "tipo_mensaje" => "success",
            "mensaje" => $resultado['message']
        ]);
    } else {
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
?>