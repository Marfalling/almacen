<?php
require_once("../_conexion/sesion.php");

// Verificar permisos
if (!verificarPermisoEspecifico('crear_ingresos') && !verificarPermisoEspecifico('editar_ingresos')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'INGRESOS', 'PROCESAR');
    header("location: bienvenido.php?permisos=true");
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
    // VALIDACIÓN 2: VERIFICAR DOCUMENTO NUEVO
    // ============================================
    include("../_conexion/conexion.php");
    
    // Obtener la fecha del último ingreso para esta compra
    $sql_ultimo_ingreso = "SELECT MAX(fec_ingreso) as ultima_fecha 
                          FROM ingreso 
                          WHERE id_compra = $id_compra";
    $res_ultimo = mysqli_query($con, $sql_ultimo_ingreso);
    $row_ultimo = mysqli_fetch_assoc($res_ultimo);
    $fecha_ultimo_ingreso = $row_ultimo['ultima_fecha'];
    
    if ($fecha_ultimo_ingreso) {
        // Ya hubo ingresos previos, verificar que haya documento nuevo
        $sql_doc_nuevo = "SELECT COUNT(*) as total 
                         FROM documentos 
                         WHERE entidad = 'ingresos' 
                         AND id_entidad = $id_compra 
                         AND fec_subida > '$fecha_ultimo_ingreso'";
        $res_doc_nuevo = mysqli_query($con, $sql_doc_nuevo);
        $row_doc_nuevo = mysqli_fetch_assoc($res_doc_nuevo);
        
        if ($row_doc_nuevo['total'] == 0) {
            mysqli_close($con);
            echo json_encode([
                "tipo_mensaje" => "warning",
                "mensaje" => "DEBE SUBIR UN NUEVO DOCUMENTO PARA ESTE INGRESO.\n\n" .
                            "Ya se realizó un ingreso anterior. Cada ingreso requiere su propia guía de remisión.\n\n" .
                            "Último ingreso: " . date('d/m/Y H:i', strtotime($fecha_ultimo_ingreso)) . "\n\n" .
                            "Por favor, suba el nuevo documento antes de continuar."
            ]);
            exit;
        }
    }
    
    mysqli_close($con);
    // ============================================

    if (!function_exists('ProcesarIngresoProducto')) {
        throw new Exception("Función ProcesarIngresoProducto no encontrada");
    }

    // Procesar productos
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
        
        $sql_pedido = "SELECT p.id_pedido, p.est_pedido 
                      FROM compra c
                      INNER JOIN pedido p ON c.id_pedido = p.id_pedido
                      WHERE c.id_compra = $id_compra";
        $res_pedido = mysqli_query($con, $sql_pedido);
        
        if ($res_pedido && mysqli_num_rows($res_pedido) > 0) {
            $pedido_data = mysqli_fetch_assoc($res_pedido);
            $id_pedido_asociado = $pedido_data['id_pedido'];
            $estado_actual_pedido = $pedido_data['est_pedido'];
            
            if ($estado_actual_pedido == 3) {
                $sql_update_pedido = "UPDATE pedido SET est_pedido = 4 WHERE id_pedido = $id_pedido_asociado";
                mysqli_query($con, $sql_update_pedido);
            }
        }
        
        mysqli_close($con);
    }

    // Preparar respuesta
    if ($resultados_exitosos == $total_productos && $total_productos > 0) {
        $response = [
            "tipo_mensaje" => "success",
            "mensaje" => "Ingreso procesado exitosamente.\n\n" .
                        "$resultados_exitosos producto(s) agregado(s) al stock.\n" .
                        "Documentos adjuntos: " . count($documentos_ingreso)
        ];
    } elseif ($resultados_exitosos > 0) {
        $mensaje_parcial = "Ingreso parcial: $resultados_exitosos de $total_productos productos ingresados correctamente.";
        if (!empty($errores)) {
            $mensaje_parcial .= "\n\nErrores: " . implode("; ", array_slice($errores, 0, 2));
        }
        $response = [
            "tipo_mensaje" => "warning",
            "mensaje" => $mensaje_parcial
        ];
    } else {
        $mensaje_error = "No se pudo procesar el ingreso.";
        if (!empty($errores)) {
            $mensaje_error .= "\n\nErrores: " . implode("; ", array_slice($errores, 0, 2));
        }
        $response = [
            "tipo_mensaje" => "error",
            "mensaje" => $mensaje_error
        ];
    }

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode([
        "tipo_mensaje" => "error",
        "mensaje" => "Error del servidor: " . $e->getMessage()
    ]);
} catch (Error $e) {
    echo json_encode([
        "tipo_mensaje" => "error", 
        "mensaje" => "Error fatal del servidor: " . $e->getMessage()
    ]);
}

exit;
?>