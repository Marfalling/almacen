<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

// ========================================================================
// VERIFICAR PERMISO ESPECÍFICO: VERIFICAR INGRESOS 
// ========================================================================
if (!verificarPermisoEspecifico('verificar_ingresos')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'INGRESOS', 'VERIFICAR INGRESO');
    echo json_encode([
        "tipo_mensaje" => "error",
        "mensaje" => "No tienes permiso para VERIFICAR ingresos desde órdenes de compra.\n\n" .
                    "Contacta al administrador si necesitas este acceso."
    ]);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

try {
    require_once("../_modelo/m_ingreso.php");
    require_once("../_modelo/m_pedidos.php");
    require_once("../_modelo/m_documentos.php");
    
    if (!isset($id_personal) || empty($id_personal)) {
        throw new Exception("Error de sesión: Usuario no identificado");
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido. Solo se acepta POST");
    }

    $id_compra = isset($_POST['id_compra']) ? intval($_POST['id_compra']) : null;
    $productos_seleccionados = $_POST['productos_seleccionados'] ?? array();
    $cantidades = $_POST['cantidades'] ?? array();

    if (!$id_compra || empty($productos_seleccionados)) {
        throw new Exception("Datos incompletos. Debe seleccionar al menos un producto.");
    }

    // ============================================
    // VALIDACIÓN 1: VERIFICAR QUE HAYA DOCUMENTOS
    // ============================================
    $documentos_ingreso = MostrarDocumentos('ingresos', $id_compra);

    if (empty($documentos_ingreso)) {
        echo json_encode([
            "tipo_mensaje" => "warning", 
            "mensaje" => "NO PUEDE PROCESAR EL INGRESO SIN DOCUMENTOS.\n\n" .
                        "Debe adjuntar al menos un documento (guía de remisión, factura, etc.) " .
                        "antes de registrar el ingreso de productos.\n\n" .
                        "Por favor, use el botón 'Subir Documento' en la sección correspondiente."
        ]);
        exit;
    }

    // ============================================
    // VALIDACIÓN 2: VERIFICAR RELACIÓN DOCUMENTOS/INGRESOS
    // ============================================
    include("../_conexion/conexion.php");

    // Contar ingresos YA procesados (no incluye el actual)
    $sql_count_ingresos = "SELECT COUNT(DISTINCT i.id_ingreso) as total_ingresos
                        FROM ingreso i
                        INNER JOIN ingreso_detalle id ON i.id_ingreso = id.id_ingreso
                        WHERE i.id_compra = $id_compra 
                        AND i.est_ingreso = 1
                        AND id.est_ingreso_detalle = 1";
    $res_count = mysqli_query($con, $sql_count_ingresos);
    $row_count = mysqli_fetch_assoc($res_count);
    $total_ingresos_previos = intval($row_count['total_ingresos']);

    // Contar documentos totales
    $total_documentos = count($documentos_ingreso);

    // 🔹 NUEVA LÓGICA: Debe haber al menos (N_ingresos + 1) documentos
    $documentos_minimos_requeridos = $total_ingresos_previos + 1;

    if ($total_documentos < $documentos_minimos_requeridos) {
        mysqli_close($con);
        
        $mensaje_error = "DEBE SUBIR UN NUEVO DOCUMENTO PARA ESTE INGRESO.\n\n";
        
        if ($total_ingresos_previos > 0) {
            $mensaje_error .= "Ya se realizaron $total_ingresos_previos ingreso(s) anterior(es).\n";
            $mensaje_error .= "Cada ingreso requiere su propia guía de remisión.\n\n";
            $mensaje_error .= "Documentos actuales: $total_documentos\n";
            $mensaje_error .= "Documentos requeridos: $documentos_minimos_requeridos\n\n";
        }
        
        $mensaje_error .= "Por favor, suba el nuevo documento antes de continuar.";
        
        echo json_encode([
            "tipo_mensaje" => "warning",
            "mensaje" => $mensaje_error
        ]);
        exit;
    }

    mysqli_close($con);
    // ============================================

    if (!function_exists('ProcesarIngresoProducto')) {
        throw new Exception("Función ProcesarIngresoProducto no encontrada");
    }

    // Procesar productos (MISMO FLUJO PARA SERVICIOS Y MATERIALES)
    $resultados_exitosos = 0;
    $total_productos = 0;
    $errores = array();

    foreach ($productos_seleccionados as $id_producto) {
        $cantidad = floatval($cantidades[$id_producto] ?? 0);

        if ($cantidad > 0) {
            $total_productos++;
            
            $cantidad_disponible = VerificarCantidadDisponible($id_compra, $id_producto);
            
            if ($cantidad > $cantidad_disponible) {
                $errores[] = "Producto ID $id_producto: Cantidad solicitada ($cantidad) mayor a la disponible ($cantidad_disponible)";
                continue;
            }
            
            //  MISMA FUNCIÓN PARA TODO
            $resultado = ProcesarIngresoProducto($id_compra, $id_producto, $cantidad, $id_personal);

            if ($resultado['success']) {
                $resultados_exitosos++;
            } else {
                $errores[] = "Producto ID $id_producto: " . ($resultado['message'] ?? 'Error desconocido');
            }
        }
    }

    // Actualizar estado del pedido si hubo ingresos exitosos
    if ($resultados_exitosos > 0 && $id_compra) {
        include("../_conexion/conexion.php");
        
        $sql_pedido = "SELECT id_pedido FROM compra WHERE id_compra = $id_compra";
        $res_pedido = mysqli_query($con, $sql_pedido);
        
        if ($res_pedido && mysqli_num_rows($res_pedido) > 0) {
            $pedido_data = mysqli_fetch_assoc($res_pedido);
            $id_pedido_asociado = $pedido_data['id_pedido'];
            
            require_once("../_modelo/m_pedidos.php");
            ActualizarEstadoPedidoUnificado($id_pedido_asociado, $con);
        }
        
        mysqli_close($con);
    }

    // Preparar respuesta y auditoría
    if ($resultados_exitosos == $total_productos && $total_productos > 0) {
        //  AUDITORÍA: VERIFICAR INGRESO EXITOSO
        GrabarAuditoria($id, $usuario_sesion, 'VERIFICAR INGRESO', 'INGRESO', "Compra ID: $id_compra | $resultados_exitosos productos");
        
        $response = [
            "tipo_mensaje" => "success",
            "mensaje" => "Ingreso verificado y procesado exitosamente.\n\n" .
                        "$resultados_exitosos producto(s) agregado(s) al stock.\n" .
                        "Documentos adjuntos: " . count($documentos_ingreso),
            "doc_adjuntos" => count($documentos_ingreso)
        ];
    } elseif ($resultados_exitosos > 0) {
        //  AUDITORÍA: VERIFICACIÓN PARCIAL CON ERRORES
        GrabarAuditoria($id, $usuario_sesion, 'VERIFICAR INGRESO PARCIAL', 'INGRESO', "Compra ID: $id_compra | $resultados_exitosos de $total_productos productos");
        
        $mensaje_parcial = "Ingreso parcial: $resultados_exitosos de $total_productos productos ingresados correctamente.";
        if (!empty($errores)) {
            $mensaje_parcial .= "\n\nErrores: " . implode("; ", array_slice($errores, 0, 2));
        }
        $response = [
            "tipo_mensaje" => "warning",
            "mensaje" => $mensaje_parcial
        ];
    } else {
        //  AUDITORÍA: ERROR AL VERIFICAR
        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL VERIFICAR INGRESO', 'INGRESO', "Compra ID: $id_compra");
        
        $mensaje_error = "No se pudo verificar el ingreso.";
        if (!empty($errores)) {
            $mensaje_error .= "\n\nErrores: " . implode("; ", array_slice($errores, 0, 2));
        }
        $response = [
            "tipo_mensaje" => "error",
            "mensaje" => $mensaje_error,
            "doc_adjuntos" => count($documentos_ingreso)
        ];
    }

    echo json_encode($response);

} catch (Exception $e) {
    //  AUDITORÍA: ERROR DE EXCEPCIÓN
    GrabarAuditoria($id, $usuario_sesion, 'ERROR SISTEMA', 'VERIFICAR INGRESO', substr($e->getMessage(), 0, 100));
    
    echo json_encode([
        "tipo_mensaje" => "error",
        "mensaje" => "Error del servidor: " . $e->getMessage()
    ]);
} catch (Error $e) {
    //  AUDITORÍA: ERROR FATAL
    GrabarAuditoria($id, $usuario_sesion, 'ERROR FATAL', 'VERIFICAR INGRESO', substr($e->getMessage(), 0, 100));
    
    echo json_encode([
        "tipo_mensaje" => "error", 
        "mensaje" => "Error fatal del servidor: " . $e->getMessage()
    ]);
}

exit;
?>