<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

header('Content-Type: application/json');

/* Verificar permiso
if (!verificarPermisoEspecifico('anular_salidas')) {
    echo json_encode(['success' => false, 'message' => 'No tienes permiso para anular salidas.']);
    exit;
}*/
if (!verificarPermisoEspecifico('anular_salidas')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'SALIDAS', 'ANULAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de salida no recibido.']);
    exit;
}
$id_salida = intval($_POST['id']);

require_once("../_modelo/m_salidas.php");
require_once("../_modelo/m_pedidos.php");

$id_usuario = $_SESSION['id'] ?? 0;

// ============================================
// VALIDAR QUE LA SALIDA EXISTE
// ============================================
include("../_conexion/conexion.php");

$sql_check = "SELECT id_salida, est_salida FROM salida WHERE id_salida = $id_salida";
$res_check = mysqli_query($con, $sql_check);

if (!$res_check || mysqli_num_rows($res_check) == 0) {
    mysqli_close($con);
    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL ANULAR', 'SALIDAS', "ID: $id_salida - Salida no encontrada");
    echo json_encode(['success' => false, 'message' => 'Salida no encontrada']);
    exit;
}

$row_check = mysqli_fetch_assoc($res_check);
$estado_actual = intval($row_check['est_salida']);

// Validar estado
if ($estado_actual == 0) {
    mysqli_close($con);
    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL ANULAR', 'SALIDAS', "ID: $id_salida | Ya está anulada");
    echo json_encode(['success' => false, 'message' => 'La salida ya está anulada']);
    exit;
}

if ($estado_actual == 2) {
    mysqli_close($con);
    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL ANULAR', 'SALIDAS', "ID: $id_salida | Estado: Recepcionada");
    echo json_encode(['success' => false, 'message' => 'No se puede anular una salida recepcionada']);
    exit;
}

mysqli_close($con);

// ============================================
// EJECUTAR ANULACIÓN
// ============================================
$result = AnularSalida($id_salida, $id_usuario);

// ============================================================
// 2️⃣ PROCESAR RESULTADO
// ============================================================
if (is_array($result) && isset($result['success'])) {
    
    if ($result['success']) {
        error_log("✅ Salida anulada correctamente");
        
        //  AUDITORÍA: ANULACIÓN EXITOSA
        GrabarAuditoria($id, $usuario_sesion, 'ANULAR', 'SALIDAS', "ID: $id_salida");
        
        echo json_encode([
            'success' => true, 
            'message' => $result['message']
        ]);
        
    } else {
        error_log("❌ Error al anular salida: " . $result['message']);
        
        //  AUDITORÍA: ERROR AL ANULAR
        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL ANULAR', 'SALIDAS', "ID: $id_salida | " . $result['message']);
        
        echo json_encode([
            'success' => false, 
            'message' => $result['message']
        ]);
    }
    
} else {
    // Error general
    $mensaje_error = is_string($result) ? $result : 'Error desconocido al anular salida';
    error_log("❌ Error al anular salida: $mensaje_error");
    
    //  AUDITORÍA: ERROR GENERAL
    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL ANULAR', 'SALIDAS', "ID: $id_salida | $mensaje_error");
    
    echo json_encode([
        'success' => false, 
        'message' => $mensaje_error
    ]);
}
?>