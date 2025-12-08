<?php
// Llamada a funciones
require_once("_modelo/m_almacen.php");

// Obtener parámetros opcionales
$id_cliente = isset($_POST['id_cliente']) ? intval($_POST['id_cliente']) : 0;

try {
    // Consultar almacenes activos
    if ($id_cliente > 0) {
        // Filtrar por cliente específico
        $almacenes = MostrarAlmacenesActivosPorCliente($id_cliente);
    } else {
        // Mostrar todos los almacenes activos (incluyendo ARCE base)
        $almacenes = MostrarAlmacenesActivosConArceBase();
    }

    if (empty($almacenes)) {
        $rpta = [
            "status" => "info",
            "message" => "No hay almacenes disponibles.",
            "data" => []
        ];
    } else {
        $rpta = [
            "status" => "success",
            "message" => "Almacenes obtenidos correctamente.",
            "data" => $almacenes
        ];
    }

} catch (Exception $e) {
    error_log("❌ Error en almacen_mostrar_.php: " . $e->getMessage());
    $rpta = [
        "status" => "error",
        "message" => "Error al obtener almacenes. Intente nuevamente.",
        "data" => []
    ];
}

// Respuesta al app
header('Content-Type: application/json; charset=utf-8');
echo json_encode($rpta, JSON_UNESCAPED_UNICODE);
?>
