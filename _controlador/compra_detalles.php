<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_compras.php");

header('Content-Type: application/json');

if (!isset($_POST['accion'])) {
    echo json_encode(['success' => false, 'message' => 'Acción no especificada']);
    exit;
}

$accion = $_POST['accion'];

if ($accion === 'obtener_detalle') {
    $id_compra = isset($_POST['id_compra']) ? intval($_POST['id_compra']) : 0;
    
    if ($id_compra <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID de compra no válido']);
        exit;
    }
    
    try {
        // Usa la función correcta que busca por ID de compra
        $compra_data = ConsultarCompraPorId($id_compra);
        
        if (empty($compra_data)) {
            echo json_encode(['success' => false, 'message' => 'Orden de compra no encontrada']);
            exit;
        }
        
        //  AGREGAR TIPO DE PRODUCTO A LOS DATOS DE COMPRA
        include("../_conexion/conexion.php");
        
        $sql_tipo = "SELECT p.id_producto_tipo 
                     FROM pedido p
                     INNER JOIN compra c ON c.id_pedido = p.id_pedido
                     WHERE c.id_compra = ?";
        $stmt_tipo = $con->prepare($sql_tipo);
        $stmt_tipo->bind_param("i", $id_compra);
        $stmt_tipo->execute();
        $result_tipo = $stmt_tipo->get_result();
        $row_tipo = $result_tipo->fetch_assoc();
        $stmt_tipo->close();
        
        // Agregar el tipo al array de compra
        if ($row_tipo) {
            $compra_data[0]['id_producto_tipo'] = $row_tipo['id_producto_tipo'];
        } else {
            $compra_data[0]['id_producto_tipo'] = 1; // Por defecto: material
        }
        
        // Obtener detalles de la compra
        $detalles = ConsultarCompraDetalle($id_compra);
        
        echo json_encode([
            'success' => true,
            'compra' => $compra_data[0],
            'detalles' => $detalles
        ]);
        
    } catch (Exception $e) {
        error_log("Error en compra_detalles.php: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}
?>