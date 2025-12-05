<?php
// Llamada a funciones
require_once("_modelo/m_reporte.php");

// Obtener los datos enviados desde Android
$id_reporte = $_POST['id_reporte'];
$id_usuario = $_POST['id_usuario'];
$id_reporte_tipo = 1; // Aviso
$id_turno = $_POST['id_turno'];
$num = $_POST['num'];
$ot = $_POST['ot'];
$ubit = $_POST['ubit'];
$vig = $_POST['vig'];
$fec = $_POST['fec'];
$hi = $_POST['hi'];
$hf = $_POST['hf'];
$det = $_POST['det'];
$hext = $_POST['hext'];
$lat = $_POST['lti'];
$lon = $_POST['lgi'];
$act = 1; // Activo

// Grabar reporte y obtener el ID del reporte
$resp = ActualizarReporte($id_reporte,$id_usuario, $id_reporte_tipo, $id_turno, $num, $ot, $ubit, $vig, $fec, $hi, $hf, $det, $hext, $lat, $lon, $act);

if ($resp == 0) { // Datos incompletos
    $rpta = ["status" => "error", "message" => "Debe completar todos los datos."];
} elseif ($resp == -1) { // Error al actualizar el reporte
    $rpta = ["status" => "error", "message" => "Error al actualizar datos. Vuelva a intentarlo."];
}
else {
    // Ruta donde se guardarán los archivos
    $upload_dir = '../_controlador/evidencias/';
    
    // Asegúrate de que la carpeta de destino exista
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Procesar los archivos enviados
    $all_files_uploaded = true; // Variable de control para verificar si todos los archivos se subieron correctamente

    if (!empty($_FILES)) 
    {
        foreach ($_FILES as $key => $file) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                $filename_ = basename($file['name']);
                $filename = uniqid() . "_" . $filename_;
                $target_path = $upload_dir . $filename;

                // Mover el archivo al directorio de destino
                if (move_uploaded_file($file['tmp_name'], $target_path)) {
                    $doc = "evidencias/" . $filename; 
                    // Guardar evidencia en base de datos
                    if (!GrabarReporteEvidencia($id_reporte, $doc)) {
                        $all_files_uploaded = false;
                        $rpta = ["status" => "error", "message" => "Error al guardar evidencia en la base de datos."];
                        break;
                    }
                } else {
                    $all_files_uploaded = false;
                    $rpta = ["status" => "error", "message" => "Error al mover algún archivo."];
                    break;
                }
            } else {
                $all_files_uploaded = false;
                $rpta = ["status" => "error", "message" => "Error al subir algún archivo. Código de error: " . $file['error']];
                break;
            }
        }
    }

    if ($all_files_uploaded) {
        $rpta = ["status" => "success", "message" => "Actualización exitosa."];
    }
}

/*
else {
    // Ruta donde se guardarán los archivos
    $upload_dir = '../_controlador/evidencias/';
    
    // Asegúrate de que la carpeta de destino exista
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Medir el tiempo de inicio
    $start_time = microtime(true);
    $time_limit = 10; // Tiempo límite en segundos

    // Variables de control
    $all_files_uploaded = true; // Variable de control para verificar si todos los archivos se subieron correctamente
    $uploaded_files_count = 0;  // Contador de archivos subidos exitosamente

    // Procesar los archivos enviados
    foreach ($_FILES as $key => $file) {
        // Verificar si el tiempo límite ha sido excedido
        $current_time = microtime(true);
        if (($current_time - $start_time) > $time_limit) {
            $all_files_uploaded = false;
            $rpta = [
                "status" => "success",
                "message" => "Actualización exitosa, pero solo se lograron subir $uploaded_files_count evidencia(s) debido al tiempo límite excedido."
            ];
            break;
        }

        if ($file['error'] === UPLOAD_ERR_OK) {
            $filename_ = basename($file['name']);
            $filename = uniqid() . "_" . $filename_;
            $target_path = $upload_dir . $filename;

            // Mover el archivo al directorio de destino
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                $doc = "evidencias/" . $filename; 
                // Guardar evidencia en base de datos
                if (!GrabarReporteEvidencia($id_reporte, $doc)) {
                    $all_files_uploaded = false;
                    $rpta = ["status" => "error", "message" => "Error al guardar evidencia en la base de datos."];
                    break;
                }

                // Incrementar el contador de archivos subidos exitosamente
                $uploaded_files_count++;
            } else {
                $all_files_uploaded = false;
                $rpta = ["status" => "error", "message" => "Error al mover algún archivo."];
                break;
            }
        } else {
            $all_files_uploaded = false;
            $rpta = ["status" => "error", "message" => "Error al subir algún archivo. Código de error: " . $file['error']];
            break;
        }
    }

    // Si todos los archivos fueron subidos dentro del tiempo límite
    if ($all_files_uploaded) {
        $rpta = ["status" => "success", "message" => "Actualización exitosa."];
    }
}
*/

// Respuesta al app
echo json_encode($rpta);
?>
