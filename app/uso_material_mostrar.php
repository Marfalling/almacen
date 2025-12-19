<?php
header('Content-Type: application/json');
require_once("_modelo/m_uso_material.php");
require_once("_modelo/m_usuario.php");

try {
    $id_usuario = isset($_POST['id_usuario']) ? $_POST['id_usuario'] : '';
    
    if (empty($id_usuario)) {
        echo json_encode(array("error" => "ID de usuario requerido"));
        exit;
    }
    
    // VERIFICACIÓN DE PERMISOS
    $permisos = obtenerPermisosUsuario($id_usuario);
    
    //permiso básico de ver
    if (!isset($permisos['ver_uso_de_material']) || !$permisos['ver_uso_de_material']) {
        // AUDITORÍA: Acceso denegado
        GrabarAuditoriaApp($id_usuario, '', 'ERROR DE ACCESO', 'USO_MATERIAL', 'VER - APP MÓVIL');
        
        echo json_encode(array("error" => "No tienes permisos para ver uso de material"));
        exit;
    }
    
    // DETERMINAR FILTRO DE USUARIO (igual que en la web)
    $id_usuario_filtro = $id_usuario; // Por defecto, solo ve sus registros
    
    if (isset($permisos['ver_todo_uso_de_material']) && $permisos['ver_todo_uso_de_material']) {
        // Tiene permiso "Ver Todo" - puede ver todos los registros
        $id_usuario_filtro = null; 
        error_log("Usuario $id_usuario puede ver TODOS los usos de material");
    } else {
        // Solo ve sus propios registros
        error_log("Usuario $id_usuario solo puede ver sus propios registros");
    }
    
    $resultado = MostrarUsoMaterial($id_usuario_filtro);
    
    // Formatear datos 
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
            "detalles" => $detalles,
            // INCLUIR PERMISOS PARA CADA ITEM
            "puede_editar" => isset($permisos['editar_uso_de_material']) && $permisos['editar_uso_de_material'],
            "puede_anular" => isset($permisos['anular_uso_de_material']) && $permisos['anular_uso_de_material']
        );
    }
    
    // AUDITORÍA: Consulta exitosa
    $descripcion = "APP MÓVIL - Registros mostrados: " . count($datos_formateados) . 
                   " | Alcance: " . ($id_usuario_filtro ? "Propios" : "Todos");
    GrabarAuditoriaApp($id_usuario, '', 'VER', 'USO_MATERIAL', $descripcion);
    
    echo json_encode($datos_formateados);
    
} catch (Exception $e) {
    error_log("❌ Error en uso_material_mostrar.php: " . $e->getMessage());
    echo json_encode(array("error" => "Error en el servidor: " . $e->getMessage()));
}
?>