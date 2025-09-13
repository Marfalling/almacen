<?php
require_once("../_conexion/sesion.php");
require_once("../_conexion/conexion.php");

// Configurar la respuesta JSON
header('Content-Type: application/json');

// Verificar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'tipo_mensaje' => 'error',
        'mensaje' => 'Método no permitido'
    ]);
    exit;
}

// Recibir parámetros
$id_uso_material = isset($_POST['id_uso_material']) ? intval($_POST['id_uso_material']) : 0;

if ($id_uso_material <= 0) {
    echo json_encode([
        'tipo_mensaje' => 'error',
        'mensaje' => 'ID de uso de material no válido'
    ]);
    exit;
}

// Verificar que el uso de material existe y está en estado pendiente
$sql_verificar = "SELECT est_uso_material FROM uso_material WHERE id_uso_material = $id_uso_material";
$result_verificar = mysqli_query($con, $sql_verificar);

if (!$result_verificar || mysqli_num_rows($result_verificar) == 0) {
    echo json_encode([
        'tipo_mensaje' => 'error',
        'mensaje' => 'Uso de material no encontrado'
    ]);
    mysqli_close($con);
    exit;
}

$uso_data = mysqli_fetch_assoc($result_verificar);

if ($uso_data['est_uso_material'] != 1) {
    $estado_texto = '';
    switch ($uso_data['est_uso_material']) {
        case 0:
            $estado_texto = 'anulado';
            break;
        case 2:
            $estado_texto = 'ya aprobado';
            break;
        default:
            $estado_texto = 'en estado inválido';
    }
    
    echo json_encode([
        'tipo_mensaje' => 'error',
        'mensaje' => 'No se puede aprobar. El uso de material está ' . $estado_texto
    ]);
    mysqli_close($con);
    exit;
}

// Actualizar el estado a aprobado (2)
$sql_aprobar = "UPDATE uso_material 
                SET est_uso_material = 2 
                WHERE id_uso_material = $id_uso_material";

if (mysqli_query($con, $sql_aprobar)) {
    echo json_encode([
        'tipo_mensaje' => 'success',
        'mensaje' => 'Uso de material aprobado correctamente'
    ]);
} else {
    echo json_encode([
        'tipo_mensaje' => 'error',
        'mensaje' => 'Error al aprobar el uso de material: ' . mysqli_error($con)
    ]);
}

mysqli_close($con);
?>