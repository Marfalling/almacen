<?php
header('Content-Type: application/json');
include("m_uso_material.php");

try {
    $id_usuario = isset($_POST['id_usuario']) ? $_POST['id_usuario'] : '';
    $filtro = isset($_POST['filtro']) ? $_POST['filtro'] : '';
    
    if (empty($id_usuario)) {
        echo json_encode(array("error" => "ID de usuario requerido"));
        exit;
    }
    
    $resultado = MostrarUsoMaterial($id_usuario, $filtro);
    
    // Formatear datos para Android
    $datos_formateados = array();
    foreach ($resultado as $row) {
        $datos_formateados[] = array(
            "id_uso_material" => $row['id_uso_material'],
            "num_uso_material" => "USO-" . str_pad($row['id_uso_material'], 6, "0", STR_PAD_LEFT),
            "nom_almacen" => $row['nom_almacen'] ?: 'Sin almacén',
            "nom_obra" => $row['nom_obra'] ?: 'Sin obra',
            "nom_solicitante" => $row['nom_solicitante'],
            "fecha_formato" => $row['fecha_formato'],
            "hora_formato" => $row['hora_formato'],
            "est_uso_material" => $row['est_uso_material']
        );
    }
    
    echo json_encode($datos_formateados);
    
} catch (Exception $e) {
    echo json_encode(array("error" => "Error en el servidor: " . $e->getMessage()));
}
?>
