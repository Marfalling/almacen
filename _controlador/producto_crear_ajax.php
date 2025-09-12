<?php
header('Content-Type: application/json');
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_producto.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Función para convertir tamaños (ej: "2M" a bytes)
        function parse_size($size) {
            $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
            $size = preg_replace('/[^0-9\.]/', '', $size);
            if ($unit) {
                return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
            } else {
                return round($size);
            }
        }

        // Verificar límites de PHP
        $max_filesize = min(
            parse_size(ini_get('upload_max_filesize')),
            parse_size(ini_get('post_max_size'))
        );

        // Obtener y validar datos del formulario
        $id_producto_tipo = $_POST['id_producto_tipo'] ?? '';
        $id_material_tipo = $_POST['id_material_tipo'] ?? '';
        $id_unidad_medida = $_POST['id_unidad_medida'] ?? '';
        $cod_material = strtoupper(trim($_POST['cod_material'] ?? ''));
        $nom_producto = trim($_POST['nom_producto'] ?? '');
        $nser_producto = strtoupper(trim($_POST['nser_producto'] ?? ''));
        $mod_producto = strtoupper(trim($_POST['mod_producto'] ?? ''));
        $mar_producto = strtoupper(trim($_POST['mar_producto'] ?? ''));
        $det_producto = trim($_POST['det_producto'] ?? '');
        $fuc_producto = $_POST['fuc_producto'] ?? '';
        $fpc_producto = $_POST['fpc_producto'] ?? '';
        $fuo_producto = $_POST['fuo_producto'] ?? '';
        $fpo_producto = $_POST['fpo_producto'] ?? '';
        $est = isset($_POST['est']) ? 1 : 0;

        // Validaciones básicas
        if (empty($id_producto_tipo) || empty($id_material_tipo) || empty($id_unidad_medida) || empty($nom_producto)) {
            echo json_encode([
                'success' => false, 
                'message' => 'Por favor complete todos los campos obligatorios'
            ]);
            exit;
        }

        // Validar longitud del nombre del producto
        if (strlen($nom_producto) > 250) {
            echo json_encode([
                'success' => false, 
                'message' => 'El nombre del producto no puede superar los 250 caracteres'
            ]);
            exit;
        }

        // Función optimizada para subir archivos
        function subirArchivo($archivo, $prefijo, $max_size = 10485760) {
            $resultado = [
                'success' => false,
                'filename' => '',
                'error' => ''
            ];

            // Verificar si hay archivo
            if (!isset($archivo) || $archivo['error'] == UPLOAD_ERR_NO_FILE) {
                return $resultado; // No hay archivo, no es error
            }

            // Verificar errores de upload
            switch ($archivo['error']) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $resultado['error'] = 'El archivo supera el tamaño máximo permitido';
                    return $resultado;
                case UPLOAD_ERR_PARTIAL:
                    $resultado['error'] = 'El archivo se subió parcialmente';
                    return $resultado;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $resultado['error'] = 'Falta directorio temporal';
                    return $resultado;
                case UPLOAD_ERR_CANT_WRITE:
                    $resultado['error'] = 'Error de escritura en el servidor';
                    return $resultado;
                default:
                    $resultado['error'] = 'Error desconocido al subir archivo';
                    return $resultado;
            }

            // Verificar tamaño del archivo
            if ($archivo['size'] > $max_size) {
                $resultado['error'] = 'El archivo supera los ' . number_format($max_size / 1048576, 1) . 'MB permitidos';
                return $resultado;
            }

            // Verificar si el archivo está vacío
            if ($archivo['size'] <= 0) {
                $resultado['error'] = 'El archivo está vacío';
                return $resultado;
            }

            // Verificar extensión
            $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            $extensiones_permitidas = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
            
            if (!in_array($extension, $extensiones_permitidas)) {
                $resultado['error'] = 'Formato de archivo no permitido. Use: ' . implode(', ', $extensiones_permitidas);
                return $resultado;
            }

            // Verificar tipo MIME
            if (!file_exists($archivo['tmp_name'])) {
                $resultado['error'] = 'Archivo temporal no encontrado';
                return $resultado;
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo === false) {
                $resultado['error'] = 'Error al verificar tipo de archivo';
                return $resultado;
            }

            $mime_type = finfo_file($finfo, $archivo['tmp_name']);
            finfo_close($finfo);

            $tipos_mime_permitidos = [
                'pdf' => 'application/pdf',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];

            // Validación MIME
            $mime_valido = false;
            foreach ($tipos_mime_permitidos as $ext => $mime_esperado) {
                if ($extension === $ext && $mime_type === $mime_esperado) {
                    $mime_valido = true;
                    break;
                }
            }

            if (!$mime_valido) {
                $resultado['error'] = 'Tipo de archivo no válido';
                return $resultado;
            }

            // Crear directorio si no existe
            $directorio = "../_uploads/documentos/";
            if (!file_exists($directorio)) {
                if (!mkdir($directorio, 0755, true)) {
                    $resultado['error'] = 'No se pudo crear el directorio de destino';
                    return $resultado;
                }
            }

            // Generar nombre de archivo optimizado (máximo 50 caracteres)
            $prefijo_limpio = preg_replace('/[^a-zA-Z0-9]/', '', $prefijo);
            $prefijo_limpio = substr($prefijo_limpio, 0, 8); // Máximo 8 caracteres
            
            $timestamp = date('YmdHis'); // 14 caracteres
            $random = substr(md5(uniqid(rand(), true)), 0, 4); // 4 caracteres aleatorios
            
            // Nombre base: prefijo(8) + _ + timestamp(14) + _ + random(4) = 27 caracteres + extensión
            $nombre_archivo = $prefijo_limpio . '_' . $timestamp . '_' . $random . '.' . $extension;
            $ruta_completa = $directorio . $nombre_archivo;

            // Verificar que no exista (muy improbable pero por seguridad)
            $contador = 1;
            while (file_exists($ruta_completa) && $contador < 10) {
                $nombre_sin_ext = pathinfo($nombre_archivo, PATHINFO_FILENAME);
                $nombre_archivo = $nombre_sin_ext . $contador . '.' . $extension;
                $ruta_completa = $directorio . $nombre_archivo;
                $contador++;
            }

            // Mover archivo
            if (move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
                if (file_exists($ruta_completa) && filesize($ruta_completa) > 0) {
                    $resultado['success'] = true;
                    $resultado['filename'] = $nombre_archivo;
                } else {
                    $resultado['error'] = 'El archivo no se pudo verificar correctamente';
                }
            } else {
                $resultado['error'] = 'Error al mover el archivo. Verifique permisos.';
            }

            return $resultado;
        }

        // Procesar archivos subidos
        $dcal_producto = '';
        $dope_producto = '';

        // Archivo de calibrado
        if (isset($_FILES['dcal_archivo'])) {
            $resultado_cal = subirArchivo($_FILES['dcal_archivo'], 'cal', 10485760);
            if (!$resultado_cal['success'] && !empty($resultado_cal['error'])) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Error en archivo de calibrado: ' . $resultado_cal['error']
                ]);
                exit;
            }
            $dcal_producto = $resultado_cal['filename'];
        }
        // Archivo de operatividad
        if (isset($_FILES['dope_archivo'])) {
            $resultado_ope = subirArchivo($_FILES['dope_archivo'], 'ope', 10485760);
            if (!$resultado_ope['success'] && !empty($resultado_ope['error'])) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Error en archivo de operatividad: ' . $resultado_ope['error']
                ]);
                exit;
            }
            $dope_producto = $resultado_ope['filename'];
        }

        // Llamar a la función para grabar el producto
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
            // Obtener el producto recién creado
            include("../_conexion/conexion.php");
            $nom_producto_escaped = mysqli_real_escape_string($con, $nom_producto);
            $sqlUltimo = "SELECT p.*, um.nom_unidad_medida, um.id_unidad_medida 
                         FROM producto p 
                         INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida 
                         WHERE p.nom_producto = '$nom_producto_escaped' 
                         ORDER BY p.id_producto DESC LIMIT 1";
            $resUltimo = mysqli_query($con, $sqlUltimo);
            
            if ($resUltimo && mysqli_num_rows($resUltimo) > 0) {
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
            } else {
                if (isset($con)) mysqli_close($con);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Producto creado pero no se pudo recuperar la información'
                ]);
            }
            
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
            'message' => 'Error interno del servidor'
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Método no permitido'
    ]);
}
?>