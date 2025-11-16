<?php
require_once("../_conexion/sesion.php");

//VERIFICAR PERMISOS - FALTANTE

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
    // 🔍 VALIDACIÓN 1: VERIFICAR OTRAS OC
    // ============================================
    $sql_otras_oc = "SELECT COUNT(*) as total 
                     FROM compra 
                     WHERE id_pedido = $id_pedido 
                     AND id_compra != $id_compra 
                     AND est_compra != 0";
    
    $res_otras_oc = mysqli_query($con, $sql_otras_oc);
    
    // ✅ VALIDACIÓN: Verificar si la consulta fue exitosa
    if (!$res_otras_oc) {
        $error_msg = mysqli_error($con);
        error_log("❌ ERROR en consulta otras_oc: " . $error_msg);
        mysqli_close($con);
        throw new Exception("Error al verificar otras órdenes de compra: " . $error_msg);
    }
    
    $row_otras_oc = mysqli_fetch_assoc($res_otras_oc);
    
    // ✅ VALIDACIÓN: Verificar que hay resultado
    if (!$row_otras_oc) {
        error_log("⚠️ No se obtuvo resultado en consulta otras_oc");
        $total_otras_oc = 0;
    } else {
        $total_otras_oc = intval($row_otras_oc['total']);
    }

    // ============================================
    // 🔍 VALIDACIÓN 2: VERIFICAR SALIDAS
    // ============================================
    $sql_salidas = "SELECT COUNT(DISTINCT s.id_salida) as total
                    FROM salida s
                    INNER JOIN salida_detalle sd ON s.id_salida = sd.id_salida
                    WHERE s.id_pedido = $id_pedido
                    AND s.est_salida = 1
                    AND sd.est_salida_detalle = 1";
    
    $res_salidas = mysqli_query($con, $sql_salidas);
    
    // ✅ VALIDACIÓN: Verificar si la consulta fue exitosa
    if (!$res_salidas) {
        $error_msg = mysqli_error($con);
        error_log("❌ ERROR en consulta salidas: " . $error_msg);
        mysqli_close($con);
        throw new Exception("Error al verificar salidas: " . $error_msg);
    }
    
    $row_salidas = mysqli_fetch_assoc($res_salidas);
    
    // ✅ VALIDACIÓN: Verificar que hay resultado
    if (!$row_salidas) {
        error_log("⚠️ No se obtuvo resultado en consulta salidas");
        $total_salidas = 0;
    } else {
        $total_salidas = intval($row_salidas['total']);
    }

    mysqli_close($con);

    // ============================================
    // 📤 RESPUESTA
    // ============================================
    echo json_encode([
        'success' => true,
        'tiene_otras_oc' => ($total_otras_oc > 0),
        'tiene_salidas' => ($total_salidas > 0),
        'total_otras_oc' => $total_otras_oc,
        'total_salidas' => $total_salidas,
        'puede_anular_pedido' => ($total_otras_oc == 0 && $total_salidas == 0)
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    error_log("❌ ERROR en compras_validar_anulacion.php: " . $e->getMessage());
    
    echo json_encode([
        'error' => true,
        'mensaje' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>