<?php
require_once("../_conexion/sesion.php");

// VERIFICAR PERMISOS
if (!verificarPermisoEspecifico('anular_pedidos')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'COMPRAS', 'VALIDAR ANULACIÓN');
    header("location: bienvenido.php?permisos=true");
    exit;
}

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido");
    }

    $id_compra = isset($_POST['id_compra']) ? intval($_POST['id_compra']) : 0;
    $id_pedido = isset($_POST['id_pedido']) ? intval($_POST['id_pedido']) : 0;

    if (!$id_compra || !$id_pedido) {
        throw new Exception("Datos incompletos");
    }

    include("../_conexion/conexion.php");

    // ============================================
    //  VALIDACIÓN 1: VERIFICAR ESTADO DE LA OC ACTUAL
    // ============================================
    $sql_oc_actual = "SELECT 
                        est_compra,
                        id_personal_aprueba,
                        id_personal_aprueba_financiera
                      FROM compra 
                      WHERE id_compra = $id_compra";
    
    $res_oc = mysqli_query($con, $sql_oc_actual);
    
    if (!$res_oc) {
        throw new Exception("Error al verificar la orden de compra");
    }
    
    $row_oc = mysqli_fetch_assoc($res_oc);
    
    if (!$row_oc) {
        throw new Exception("Orden de compra no encontrada");
    }
    
    $estado_oc = intval($row_oc['est_compra']);
    $tiene_aprobacion_tecnica = !empty($row_oc['id_personal_aprueba']);
    $tiene_aprobacion_financiera = !empty($row_oc['id_personal_aprueba_financiera']);
    
    //  NO SE PUEDE ANULAR SI:
    // - Ya está anulada (0)
    // - Tiene aprobación técnica o financiera
    // - Está cerrada (4)
    
    if ($estado_oc == 0) {
        throw new Exception("La orden de compra ya está anulada");
    }
    
    if ($tiene_aprobacion_tecnica || $tiene_aprobacion_financiera) {
        throw new Exception("No se puede anular una orden que tiene aprobaciones");
    }
    
    if ($estado_oc == 4) {
        throw new Exception("No se puede anular una orden cerrada");
    }

    // ============================================
    //  VALIDACIÓN 2: VERIFICAR OTRAS OC DEL PEDIDO
    // ============================================
    $sql_otras_oc = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN est_compra = 1 THEN 1 ELSE 0 END) as pendientes,
                        SUM(CASE WHEN est_compra = 2 THEN 1 ELSE 0 END) as aprobadas_tecnica,
                        SUM(CASE WHEN est_compra = 3 THEN 1 ELSE 0 END) as aprobadas_financiera,
                        SUM(CASE WHEN est_compra = 4 THEN 1 ELSE 0 END) as cerradas
                     FROM compra 
                     WHERE id_pedido = $id_pedido 
                     AND id_compra != $id_compra 
                     AND est_compra != 0";
    
    $res_otras_oc = mysqli_query($con, $sql_otras_oc);
    
    if (!$res_otras_oc) {
        throw new Exception("Error al verificar otras órdenes de compra");
    }
    
    $row_otras_oc = mysqli_fetch_assoc($res_otras_oc);
    
    $total_otras_oc = intval($row_otras_oc['total']);
    $oc_pendientes = intval($row_otras_oc['pendientes']);
    $oc_aprobadas_tecnica = intval($row_otras_oc['aprobadas_tecnica']);
    $oc_aprobadas_financiera = intval($row_otras_oc['aprobadas_financiera']);
    $oc_cerradas = intval($row_otras_oc['cerradas']);

    // ============================================
    //  VALIDACIÓN 3: VERIFICAR SALIDAS ACTIVAS
    // ============================================
    $sql_salidas = "SELECT COUNT(*) as total
                    FROM salida 
                    WHERE id_pedido = $id_pedido
                    AND est_salida IN (1, 2, 3)"; // Pendiente, Recepcionada, Aprobada
    
    $res_salidas = mysqli_query($con, $sql_salidas);
    
    if (!$res_salidas) {
        throw new Exception("Error al verificar salidas");
    }
    
    $row_salidas = mysqli_fetch_assoc($res_salidas);
    $total_salidas = intval($row_salidas['total']);

    // ============================================
    //  VALIDACIÓN 4: VERIFICAR INGRESOS ASOCIADOS A ESTA OC
    // ============================================
    $sql_ingresos = "SELECT COUNT(*) as total
                     FROM ingreso 
                     WHERE id_compra = $id_compra
                     AND est_ingreso != 0";
    
    $res_ingresos = mysqli_query($con, $sql_ingresos);
    $row_ingresos = mysqli_fetch_assoc($res_ingresos);
    $tiene_ingresos = (intval($row_ingresos['total']) > 0);

    if ($tiene_ingresos) {
        throw new Exception("No se puede anular una orden que tiene ingresos registrados");
    }

    mysqli_close($con);

    // ============================================
    //  LÓGICA DE DECISIÓN
    // ============================================
    
    //  Determinar si se puede anular PEDIDO completo
    // SOLO si NO hay:
    // - Otras OC (en cualquier estado activo)
    // - Salidas activas
    
    $puede_anular_pedido = true;
    $mensaje_restriccion = [];
    
    if ($oc_pendientes > 0) {
        $puede_anular_pedido = false;
        $mensaje_restriccion[] = "$oc_pendientes orden(es) de compra PENDIENTE(S)";
    }
    
    if ($oc_aprobadas_tecnica > 0) {
        $puede_anular_pedido = false;
        $mensaje_restriccion[] = "$oc_aprobadas_tecnica orden(es) de compra APROBADA(S)";
    }
    
    if ($oc_aprobadas_financiera > 0) {
        $puede_anular_pedido = false;
        $mensaje_restriccion[] = "$oc_aprobadas_financiera orden(es) de compra APROBADA(S)";
    }
    
    if ($oc_cerradas > 0) {
        $puede_anular_pedido = false;
        $mensaje_restriccion[] = "$oc_cerradas orden(es) de compra CERRADA(S)";
    }
    
    if ($total_salidas > 0) {
        $puede_anular_pedido = false;
        $mensaje_restriccion[] = "$total_salidas orden(es) de salida activa(s)";
    }

    // ============================================
    // 📤 RESPUESTA
    // ============================================
    echo json_encode([
        'success' => true,
        'puede_anular_pedido' => $puede_anular_pedido,
        'puede_anular_oc' => true, // Siempre se puede anular solo la OC si pasó las validaciones
        'tiene_otras_oc' => ($total_otras_oc > 0),
        'tiene_salidas' => ($total_salidas > 0),
        'total_otras_oc' => $total_otras_oc,
        'total_salidas' => $total_salidas,
        'oc_pendientes' => $oc_pendientes,
        'oc_aprobadas_tecnica' => $oc_aprobadas_tecnica,
        'oc_aprobadas_financiera' => $oc_aprobadas_financiera,
        'oc_cerradas' => $oc_cerradas,
        'mensaje_restriccion' => $mensaje_restriccion,
        'estado_oc_actual' => $estado_oc
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    error_log("❌ ERROR en compras_validar_anulacion.php: " . $e->getMessage());
    
    echo json_encode([
        'error' => true,
        'mensaje' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>