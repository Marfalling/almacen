<?php
header('Content-Type: application/json');
require_once("_modelo/m_uso_material.php");

try {
    $id_usuario = isset($_POST['id_usuario']) ? $_POST['id_usuario'] : '';
    
    if (empty($id_usuario)) {
        echo json_encode(array("error" => "ID de usuario requerido"));
        exit;
    }
    
    $resultado = MostrarUsoMaterial($id_usuario);
    
    // Formatear datos para Android
    $datos_formateados = array();
    foreach ($resultado as $row) {
        // Obtener detalles de cada uso de material
        $detalles = ConsultarUsoMaterialDetalle($row['id_uso_material']);
        
        $datos_formateados[] = array(
            "id_uso_material" => $row['id_uso_material'],
            "num_uso_material" => "U" . str_pad($row['id_uso_material'], 3, "0", STR_PAD_LEFT),
            "nom_almacen" => $row['nom_almacen'] ?: 'Sin almacén',
            "nom_ubicacion" => $row['nom_ubicacion'] ?: 'Sin ubicación',
            "nom_obra" => $row['nom_obra'] ?: 'Sin obra',
            "nom_cliente" => $row['nom_cliente'] ?: 'Sin cliente',
            "nom_solicitante" => $row['nom_solicitante'],
            "fecha_formato" => $row['fecha_formato'],
            "est_uso_material" => $row['est_uso_material'],
            "detalles" => $detalles
        );
    }
    
    echo json_encode($datos_formateados);
    
} catch (Exception $e) {
    echo json_encode(array("error" => "Error en el servidor: " . $e->getMessage()));
}
?>