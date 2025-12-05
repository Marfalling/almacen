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

//----------------------------------------------------------------------------
//Datos de la brigada del usuario en ese momento
$usuario = ConsultarReporteUsuario($id_usuario);
$id_brigada = $usuario[0]['id_brigada'];

//Para verificar si ya existe el numero de reporte archivado, y cree otra versión
$verifiar = VerificarArchivado($num, $id_brigada);
$cantidad = $verifiar[0]['cantidad'];

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
$resp = ActualizarReporte($id_reporte,$id_usuario, $id_reporte_tipo, $id_turno, $num, $ot, $ubit, $vig, $fec, $hi, $hf, $det, $hext, $lat, $lon, $act);

if ($resp == 0) { // Datos incompletos
    $rpta = ["status" => "error", "message" => "Debe completar todos los datos."];
} elseif ($resp == -1) { // Error al actualizar el reporte
    $rpta = ["status" => "error", "message" => "Error al actualizar datos. Vuelva a intentarlo."];
}
else{
    $rpta = ["status" => "success", "message" => "Actualización exitosa."];
}

// Respuesta al app
echo json_encode($rpta);
?>
