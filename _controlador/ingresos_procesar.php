<?php
require_once("../_conexion/sesion.php");

// Para procesar_ingresos.php
if (!verificarPermisoEspecifico('crear_ingresos') && !verificarPermisoEspecifico('editar_ingresos')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'INGRESOS', 'PROCESAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

header('Content-Type: application/json; charset=utf-8');

try {
    // Log inicial
    error_log("=== INICIO PROCESO INGRESO ===");
    error_log("Método: " . $_SERVER['REQUEST_METHOD']);
    error_log("POST data: " . print_r($_POST, true));
    
    // Verificar archivos requeridos
    if (!file_exists("../_conexion/sesion.php")) {
        throw new Exception("Archivo sesion.php no encontrado");
    }
    
    if (!file_exists("../_modelo/m_ingreso.php")) {
        throw new Exception("Archivo m_ingreso.php no encontrado");
    }
    
    require_once("../_conexion/sesion.php");
    require_once("../_modelo/m_ingreso.php");
    
    // Verificar variables de sesión
    if (!isset($id_personal) || empty($id_personal)) {
        error_log("Variable id_personal no definida o vacía");
        throw new Exception("Error de sesión: Usuario no identificado");
    }
    
    error_log("ID Personal desde sesión: " . $id_personal);

    // Verificar si se recibió el POST con los datos necesarios
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido. Solo se acepta POST");
    }

    $id_compra = isset($_POST['id_compra']) ? intval($_POST['id_compra']) : null;
    $productos_seleccionados = $_POST['productos_seleccionados'] ?? array();
    $cantidades = $_POST['cantidades'] ?? array();

    error_log("ID Compra: " . $id_compra);
    error_log("Productos seleccionados: " . print_r($productos_seleccionados, true));
    error_log("Cantidades recibidas: " . print_r($cantidades, true));

    if (!$id_compra || empty($productos_seleccionados)) {
        throw new Exception("Datos incompletos. Debe seleccionar al menos un producto.");
    }

    // Verificar que existe la función ProcesarIngresoProducto
    if (!function_exists('ProcesarIngresoProducto')) {
        throw new Exception("Función ProcesarIngresoProducto no encontrada");
    }

    $resultados_exitosos = 0;
    $total_productos = 0;
    $errores = array();

    foreach ($productos_seleccionados as $id_producto) {
        $cantidad = floatval($cantidades[$id_producto] ?? 0);
        
        error_log("Procesando producto ID: $id_producto, Cantidad: $cantidad");

        if ($cantidad > 0) {
            $total_productos++;
            
            // Verificar cantidad disponible antes de procesar
            $cantidad_disponible = VerificarCantidadDisponible($id_compra, $id_producto);
            error_log("Cantidad disponible para producto $id_producto: $cantidad_disponible");
            
            if ($cantidad > $cantidad_disponible) {
                $errores[] = "Producto ID $id_producto: Cantidad solicitada ($cantidad) mayor a la disponible ($cantidad_disponible)";
                continue;
            }
            
            $resultado = ProcesarIngresoProducto($id_compra, $id_producto, $cantidad, $id_personal);
            
            error_log("Resultado para producto $id_producto: " . print_r($resultado, true));

            if ($resultado['success']) {
                $resultados_exitosos++;
                
                // Registrar auditoría si existe la función
                /*if (file_exists("../_modelo/m_auditoria.php")) {
                    require_once("../_modelo/m_auditoria.php");
                    if (function_exists('GrabarAuditoria') && isset($usuario_sesion)) {
                        GrabarAuditoria($id_personal, $usuario_sesion, 'INGRESO DE PRODUCTO', 'INGRESO', "Compra: $id_compra, Producto: $id_producto, Cantidad: $cantidad");
                    }
                }*/
            } else {
                $errores[] = "Producto ID $id_producto: " . ($resultado['message'] ?? 'Error desconocido');
            }
        }
    }

    error_log("Resultados: $resultados_exitosos exitosos de $total_productos total");
    error_log("Errores: " . print_r($errores, true));

    // Evaluar resultados y enviar respuesta
    if ($resultados_exitosos == $total_productos && $total_productos > 0) {
        $response = [
            "tipo_mensaje" => "success",
            "mensaje" => "Ingreso procesado exitosamente. $resultados_exitosos producto(s) agregado(s) al stock."
        ];
    } elseif ($resultados_exitosos > 0) {
        $mensaje_parcial = "Ingreso parcial: $resultados_exitosos de $total_productos productos ingresados correctamente.";
        if (!empty($errores)) {
            $mensaje_parcial .= " Errores: " . implode("; ", array_slice($errores, 0, 2));
        }
        $response = [
            "tipo_mensaje" => "warning",
            "mensaje" => $mensaje_parcial
        ];
    } else {
        $mensaje_error = "No se pudo procesar el ingreso.";
        if (!empty($errores)) {
            $mensaje_error .= " Errores: " . implode("; ", array_slice($errores, 0, 2));
        }
        $response = [
            "tipo_mensaje" => "error",
            "mensaje" => $mensaje_error
        ];
    }

    error_log("Respuesta final: " . print_r($response, true));
    echo json_encode($response);

} catch (Exception $e) {
    error_log("ERROR CAPTURADO: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    echo json_encode([
        "tipo_mensaje" => "error",
        "mensaje" => "Error del servidor: " . $e->getMessage()
    ]);
} catch (Error $e) {
    error_log("ERROR FATAL: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    echo json_encode([
        "tipo_mensaje" => "error", 
        "mensaje" => "Error fatal del servidor: " . $e->getMessage()
    ]);
}

error_log("=== FIN PROCESO INGRESO ===");
exit;
?>