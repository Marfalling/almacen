<?php
// Llamada a funciones
require_once("_modelo/m_reporte.php");
require_once("_modelo/m_retiro.php");

// Obtener los datos enviados desde Android
$id_reporte = $_POST['id_reporte'];
$id_usuario = $_POST['id_usuario'];
$id_reporte_tipo = 1; // Aviso
$id_tipo_obra = $_POST['id_tipo_obra'];
$id_vigilante = $_POST['id_vigilante'];
$id_turno = $_POST['id_turno'];
$num = $_POST['num'];
$ot = $_POST['ot'];
$ubit = $_POST['ubit'];
$fec = $_POST['fec'];
$hi = $_POST['hi'];
$hf = $_POST['hf'];
$det = $_POST['det'];
$hext = $_POST['hext'];
$lat = $_POST['lti'];
$lon = $_POST['lgi'];
$act = 1; // Activo

/*
$materiales = [];
if (!empty($_POST['materiales'])) {
    $tmp = json_decode($_POST['materiales'], true);
    if (is_array($tmp)) {
        $materiales = $tmp;
    }
}

$materialesAll = [];
if (!empty($_POST['materialesAll'])) {
    $tmp = json_decode($_POST['materialesAll'], true);
    if (is_array($tmp)) {
        $materialesAll = $tmp;
    }
}
*/

$materiales = [];
if (!empty($_POST['materiales'])) {
    // normaliza a UTF-8 antes de decodificar
    $raw = mb_convert_encoding($_POST['materiales'], 'UTF-8', 'UTF-8, ISO-8859-1');
    $tmp = json_decode($raw, true, 512, JSON_INVALID_UTF8_SUBSTITUTE);
    if (is_array($tmp)) {
        $materiales = $tmp;
    }
}

$materialesAll = [];
if (!empty($_POST['materialesAll'])) {
    // normaliza a UTF-8 antes de decodificar
    $raw = mb_convert_encoding($_POST['materialesAll'], 'UTF-8', 'UTF-8, ISO-8859-1');
    $tmp = json_decode($raw, true, 512, JSON_INVALID_UTF8_SUBSTITUTE);
    if (is_array($tmp)) {
        $materialesAll = $tmp;
    }
}

//----------------------------------------------------------------------------
// Datos de la brigada del usuario en ese momento
$usuario = ConsultarReporteUsuario($id_usuario);

// Validar que haya resultados
if (!isset($usuario[0]['id_brigada'])) 
{
    error_log("❌ Error: No se encontró id_brigada para el usuario $id_usuario en reporte_editar_sin_archivos_.php");
    echo json_encode([
        "status" => "error",
        "message" => "No se registró, vuelva a intentarlo."
    ]);
    exit;
}

$id_brigada = $usuario[0]['id_brigada'];

//----------------------------------------------------------------------------
// Verificar si ya existe el número de reporte archivado y crear otra versión
$verificar = VerificarArchivado($num, $id_brigada);

// Validar que haya resultados antes de acceder a $verificar[0]
if (!isset($verificar[0]['cantidad'])) 
{
    error_log("❌ Error: No se encontró cantidad de reportes archivados para num $num y brigada $id_brigada en reporte_editar_sin_archivos_.php");
    $cantidad = 0;
} 
else 
{
    $cantidad = $verificar[0]['cantidad'];
}

if ($cantidad > 0) { // Si existe al menos un registro con ese nombre
    $contador = 1;
    do {
        $num_temp = $num . "($contador)";
        $verifiar = VerificarArchivado($num_temp, $id_brigada);
        $cantidad = $verifiar[0]['cantidad'];
        $contador++;
    } while ($cantidad > 0); // Mientras exista un archivo con ese nombre
    $num = $num_temp;
}
//----------------------------------------------------------------------------

// Grabar reporte y obtener el ID del reporte
$resp = ActualizarReporte1($id_reporte,$id_usuario, $id_reporte_tipo, $id_tipo_obra, $id_vigilante, $id_turno, $num, $ot, $ubit, $fec, $hi, $hf, $det, $hext, $lat, $lon, $act);

if ($resp == 0) { // Datos incompletos
    $rpta = ["status" => "error", "message" => "Debe completar todos los datos."];
} elseif ($resp == -1) { // Error al actualizar el reporte
    $rpta = ["status" => "error", "message" => "Error al actualizar datos. Vuelva a intentarlo."];
}
else{
    //Registrar retiros de material
    ActualizarRetiroMaterial($id_reporte,$materiales, $materialesAll);
    $rpta = ["status" => "success", "message" => "Actualización exitosa."];
}

// Respuesta al app
echo json_encode($rpta);
?>
