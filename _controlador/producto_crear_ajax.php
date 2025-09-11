<?php
header('Content-Type: application/json');
require_once("../_conexion/sesion.php");



require_once("../_modelo/m_producto.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Obtener datos del formulario
        $id_producto_tipo = $_POST['id_producto_tipo'];
        $id_material_tipo = $_POST['id_material_tipo'];
        $id_unidad_medida = $_POST['id_unidad_medida'];
        $cod_material = strtoupper(trim($_POST['cod_material']));
        $nom_producto = strtoupper(trim($_POST['nom_producto']));
        $nser_producto = strtoupper(trim($_POST['nser_producto']));
        $mod_producto = strtoupper(trim($_POST['mod_producto']));
        $mar_producto = strtoupper(trim($_POST['mar_producto']));
        $det_producto = trim($_POST['det_producto']);
        $fuc_producto = $_POST['fuc_producto'];
        $fpc_producto = $_POST['fpc_producto'];
        $fuo_producto = $_POST['fuo_producto'];
        $fpo_producto = $_POST['fpo_producto'];
        $est = isset($_POST['est']) ? 1 : 0;

        // Validaciones básicas
        if (empty($id_producto_tipo) || empty($id_material_tipo) || empty($id_unidad_medida) || empty($nom_producto)) {
            echo json_encode([
                'success' => false, 
                'message' => 'Por favor complete todos los campos obligatorios'
            ]);
            exit;
        }

        // Función para subir archivos
        function subirArchivo($archivo, $prefijo) {
            if ($archivo['error'] == 0) {
                $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
                $extensiones_permitidas = array('pdf', 'jpg', 'jpeg');
                
                if (in_array($extension, $extensiones_permitidas)) {
                    // Crear directorio si no existe
                    $directorio = "../_uploads/documentos/";
                    if (!file_exists($directorio)) {
                        mkdir($directorio, 0777, true);
                    }
                    
                    // Generar nombre único
                    $nombre_archivo = $prefijo . '_' . date('YmdHis') . '_' . uniqid() . '.' . $extension;
                    $ruta_completa = $directorio . $nombre_archivo;
                    
                    if (move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
                        return $nombre_archivo;
                    }
                }
            }
            return '';
        }

        // Procesar archivos subidos
        $dcal_producto = '';
        $dope_producto = '';

        if (isset($_FILES['dcal_archivo']) && $_FILES['dcal_archivo']['size'] > 0) {
            $dcal_producto = subirArchivo($_FILES['dcal_archivo'], 'calibrado');
        }

        if (isset($_FILES['dope_archivo']) && $_FILES['dope_archivo']['size'] > 0) {
            $dope_producto = subirArchivo($_FILES['dope_archivo'], 'operatividad');
        }

        // Llamar a la función para grabar el producto con todos los parámetros
        $resultado = GrabarProducto(
            $id_producto_tipo, 
            $id_material_tipo, 
            $id_unidad_medida, 
            $cod_material, 
            $nom_producto,
            $nser_producto, 
            $mod_producto, 
            $mar_producto, 
            $det_producto, 
            $fuc_producto,
            $fpc_producto, 
            $dcal_producto, 
            $fuo_producto, 
            $fpo_producto, 
            $dope_producto, 
            $est
        );

        if ($resultado === "SI") {
            // Obtener el producto recién creado para devolverlo
            include("../_conexion/conexion.php");
            $sqlUltimo = "SELECT p.*, um.nom_unidad_medida, um.id_unidad_medida 
                         FROM producto p 
                         INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida 
                         WHERE p.nom_producto = '$nom_producto' 
                         ORDER BY p.id_producto DESC LIMIT 1";
            $resUltimo = mysqli_query($con, $sqlUltimo);
            $productoCreado = mysqli_fetch_assoc($resUltimo);
            mysqli_close($con);

            echo json_encode([
                'success' => true, 
                'message' => 'Producto creado exitosamente',
                'producto' => [
                    'id_producto' => $productoCreado['id_producto'],
                    'nom_producto' => $productoCreado['nom_producto'],
                    'id_unidad_medida' => $productoCreado['id_unidad_medida'],
                    'nom_unidad_medida' => $productoCreado['nom_unidad_medida']
                ]
            ]);
        } else if ($resultado === "NO") {
            echo json_encode([
                'success' => false, 
                'message' => 'Ya existe un producto con ese código de material'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Error al crear el producto'
            ]);
        }

    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'message' => 'Error interno: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Método no permitido'
    ]);
}
?>