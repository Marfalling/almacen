<?php
//-----------------------------------------------------------------------
// CONTROLADOR: pedido_anular.php
//-----------------------------------------------------------------------
header('Content-Type: application/json; charset=utf-8');

require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");
require_once('../_modelo/m_anulaciones.php');

// VERIFICAR PERMISOS
if (!verificarPermisoEspecifico('anular_pedidos')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'PEDIDOS', 'ANULAR');
    echo json_encode([
        'success' => false,
        'message' => 'No tienes permisos para anular pedidos'
    ]);
    exit;
}

$id_pedido = isset($_POST['id_pedido']) ? intval($_POST['id_pedido']) : 0;
$id_personal = isset($_SESSION['id_personal']) ? intval($_SESSION['id_personal']) : 0;

if (!$id_pedido) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de pedido no recibido.'
    ]);
    exit;
}

if (!$id_personal) {
    echo json_encode([
        'success' => false,
        'message' => 'Sesión inválida.'
    ]);
    exit;
}

// ============================================
// VALIDACIÓN ADICIONAL: Verificar que no tenga órdenes
// ============================================
include("../_conexion/conexion.php");

$sql_validar = "SELECT 
                    (SELECT COUNT(*) FROM compra WHERE id_pedido = $id_pedido AND est_compra != 0) as tiene_oc,
                    (SELECT COUNT(*) FROM salida WHERE id_pedido = $id_pedido AND est_salida != 0) as tiene_os,
                    (SELECT est_pedido FROM pedido WHERE id_pedido = $id_pedido) as estado";

$res_validar = mysqli_query($con, $sql_validar);

if (!$res_validar) {
    mysqli_close($con);
    echo json_encode([
        'success' => false,
        'message' => 'Error al validar pedido: ' . mysqli_error($con)
    ]);
    exit;
}

$row_validar = mysqli_fetch_assoc($res_validar);

if (!$row_validar) {
    mysqli_close($con);
    echo json_encode([
        'success' => false,
        'message' => 'Pedido no encontrado.'
    ]);
    exit;
}

// Validar que no tenga órdenes asociadas
if ($row_validar['tiene_oc'] > 0 || $row_validar['tiene_os'] > 0) {
    mysqli_close($con);
    
    $restricciones = [];
    if ($row_validar['tiene_oc'] > 0) {
        $restricciones[] = $row_validar['tiene_oc'] . " orden(es) de compra";
    }
    if ($row_validar['tiene_os'] > 0) {
        $restricciones[] = $row_validar['tiene_os'] . " orden(es) de salida";
    }
    
    //  AUDITORÍA: INTENTO DE ANULACIÓN CON RESTRICCIONES
    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL ANULAR', 'PEDIDOS', "ID: $id_pedido | Tiene " . implode(' y ', $restricciones));
    
    echo json_encode([
        'success' => false,
        'message' => 'No se puede anular. El pedido tiene ' . implode(' y ', $restricciones) . ' asociadas. Debes anularlas primero.'
    ]);
    exit;
}

// Validar estado del pedido
$estado = intval($row_validar['estado']);

if ($estado == 0) {
    mysqli_close($con);
    
    //  AUDITORÍA: INTENTO DE ANULAR PEDIDO YA ANULADO
    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL ANULAR', 'PEDIDOS', "ID: $id_pedido | Ya está anulado");
    
    echo json_encode([
        'success' => false,
        'message' => 'El pedido ya está anulado.'
    ]);
    exit;
}

if ($estado >= 2) {
    mysqli_close($con);
    
    $estados_texto = [
        2 => "atendido",
        3 => "aprobado",
        4 => "ingresado",
        5 => "finalizado"
    ];
    
    $nombre_estado = $estados_texto[$estado] ?? "procesado";
    
    //  AUDITORÍA: INTENTO DE ANULAR PEDIDO EN ESTADO NO PERMITIDO
    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL ANULAR', 'PEDIDOS', "ID: $id_pedido | Estado: $nombre_estado");
    
    echo json_encode([
        'success' => false,
        'message' => "No se puede anular un pedido {$nombre_estado}."
    ]);
    exit;
}

mysqli_close($con);

// ============================================
// PROCEDER CON LA ANULACIÓN
// ============================================
$resultado = AnularPedido($id_pedido, $id_personal);

// El resultado de AnularPedido() ya viene con el formato correcto
if ($resultado['success']) {
    //  AUDITORÍA: ANULACIÓN EXITOSA
    GrabarAuditoria($id, $usuario_sesion, 'ANULAR', 'PEDIDOS', "ID: $id_pedido");
    
    echo json_encode([
        'success' => true,
        'message' => $resultado['mensaje']
    ]);
} else {
    //  AUDITORÍA: ERROR AL ANULAR
    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL ANULAR', 'PEDIDOS', "ID: $id_pedido | " . $resultado['mensaje']);
    
    echo json_encode([
        'success' => false,
        'message' => $resultado['mensaje']
    ]);
}
?>