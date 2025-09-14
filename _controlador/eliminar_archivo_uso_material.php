<?php
require_once("../_conexion/sesion.php");
require_once("../_conexion/conexion.php");

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo 'MÉTODO NO PERMITIDO';
    exit;
}

// Obtener parámetros
$nombre_archivo = isset($_POST['nombre_archivo']) ? $_POST['nombre_archivo'] : '';
$id_detalle = isset($_POST['id_detalle']) ? intval($_POST['id_detalle']) : 0;

// Validar parámetros
if (empty($nombre_archivo) || $id_detalle <= 0) {
    echo 'PARÁMETROS INVÁLIDOS';
    exit;
}

// Sanitizar nombre de archivo para evitar ataques de directorio
$nombre_archivo = basename($nombre_archivo);

// Verificar que el archivo pertenece al detalle especificado
$sql_verificar = "SELECT id_uso_material_detalle_documento 
                  FROM uso_material_detalle_documento 
                  WHERE id_uso_material_detalle = $id_detalle 
                  AND nom_uso_material_detalle_documento = '" . mysqli_real_escape_string($con, $nombre_archivo) . "'
                  AND est_uso_material_detalle_documento = 1";

$result_verificar = mysqli_query($con, $sql_verificar);

if (!$result_verificar || mysqli_num_rows($result_verificar) == 0) {
    echo 'ARCHIVO NO ENCONTRADO EN LA BASE DE DATOS';
    exit;
}

$row = mysqli_fetch_assoc($result_verificar);
$id_documento = $row['id_uso_material_detalle_documento'];

// Ruta del archivo
$ruta_archivo = "../_archivos/uso_material/" . $nombre_archivo;

// Iniciar transacción
mysqli_autocommit($con, false);

try {
    // Marcar el documento como eliminado en la base de datos
    $sql_eliminar_db = "UPDATE uso_material_detalle_documento 
                        SET est_uso_material_detalle_documento = 0 
                        WHERE id_uso_material_detalle_documento = $id_documento";
    
    if (!mysqli_query($con, $sql_eliminar_db)) {
        throw new Exception('Error al eliminar registro de la base de datos: ' . mysqli_error($con));
    }
    
    // Eliminar archivo físico si existe
    if (file_exists($ruta_archivo)) {
        if (!unlink($ruta_archivo)) {
            throw new Exception('Error al eliminar archivo físico');
        }
    }
    
    // Confirmar transacción
    mysqli_commit($con);
    echo 'OK';
    
} catch (Exception $e) {
    // Revertir transacción
    mysqli_rollback($con);
    echo $e->getMessage();
} finally {
    mysqli_close($con);
}
?>