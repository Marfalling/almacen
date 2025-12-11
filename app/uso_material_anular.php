<?php
header('Content-Type: application/json');
include("m_uso_material.php");

try {
    $id_uso_material = isset($_POST['id_uso_material']) ? intval($_POST['id_uso_material']) : 0;
    
    if ($id_uso_material <= 0) {
        echo json_encode(array(
            "status" => "error", 
            "message" => "ID de uso de material inválido"
        ));
        exit;
    }
    
    $resultado = AnularUsoMaterial($id_uso_material);
    
    if ($resultado === "SI") {
        echo json_encode(array(
            "status" => "success", 
            "message" => "Uso de material anulado correctamente"
        ));
    } else {
        echo json_encode(array(
            "status" => "error", 
            "message" => $resultado
        ));
    }
    
} catch (Exception $e) {
    echo json_encode(array(
        "status" => "error", 
        "message" => "Error en el servidor: " . $e->getMessage()
    ));
}
?>