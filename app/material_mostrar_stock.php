<?php
// Llamada a funciones
require_once("_modelo/m_producto.php");

// Obtener parámetros
$id_almacen = isset($_POST['id_almacen']) ? intval($_POST['id_almacen']) : 0;
$id_ubicacion = isset($_POST['id_ubicacion']) ? intval($_POST['id_ubicacion']) : 0;
$busqueda = isset($_POST['busqueda']) ? trim($_POST['busqueda']) : '';

// Validar parámetros obligatorios
if ($id_almacen <= 0 || $id_ubicacion <= 0) {
    $rpta = [
        "status" => "error",
        "message" => "Debe seleccionar almacén y ubicación.",
        "data" => []
    ];
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($rpta, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // Consultar materiales con stock disponible
    $materiales = ConsultarMaterialesConStock($id_almacen, $id_ubicacion, $busqueda);

    if (empty($materiales)) {
        $rpta = [
            "status" => "info",
            "message" => "No hay materiales con stock disponible en esta ubicación.",
            "data" => []
        ];
    } else {
        // Formatear datos para Android
        $data_formateada = [];
        foreach ($materiales as $material) {
            $stock_fisico = floatval($material['stock_fisico']);
            $stock_comprometido = floatval($material['stock_comprometido']);
            $stock_disponible = $stock_fisico - $stock_comprometido;

            // Solo incluir si hay stock disponible
            if ($stock_disponible > 0) {
                $data_formateada[] = [
                    'id_producto' => $material['id_producto'],
                    'cod_material' => $material['cod_material'],
                    'nom_producto' => $material['nom_producto'],
                    'nom_material_tipo' => $material['nom_material_tipo'],
                    'nom_unidad_medida' => $material['nom_unidad_medida'],
                    'stock_fisico' => number_format($stock_fisico, 2, '.', ''),
                    'stock_comprometido' => number_format($stock_comprometido, 2, '.', ''),
                    'stock_disponible' => number_format($stock_disponible, 2, '.', '')
                ];
            }
        }

        $rpta = [
            "status" => "success",
            "message" => "Materiales obtenidos correctamente.",
            "data" => $data_formateada
        ];
    }

} catch (Exception $e) {
    error_log("❌ Error en material_mostrar_stock_.php: " . $e->getMessage());
    $rpta = [
        "status" => "error",
        "message" => "Error al obtener materiales. Intente nuevamente.",
        "data" => []
    ];
}

// Respuesta al app
header('Content-Type: application/json; charset=utf-8');
echo json_encode($rpta, JSON_UNESCAPED_UNICODE);
?>
