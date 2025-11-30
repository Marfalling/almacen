<?php
// ----------------------------
// CONTROL DE ARCHIVO DE FECHA
// ----------------------------
if (!file_exists("last_sync_personal.txt")) {
    file_put_contents("last_sync_personal.txt", "2000-01-01 00:00:00");
}
$ultima_sync = trim(file_get_contents("last_sync_personal.txt"));
if ($ultima_sync === "") {
    $ultima_sync = "2000-01-01 00:00:00";
}

$desde = urlencode($ultima_sync);

// ----------------------------
// OBTENER LOS DATOS REMOTOS
// ----------------------------
$url = "https://montajeseingenieriaarceperusac.pe/almacen/api/getCambios_personal.php?desde=$desde&token=TOKEN";
$json = file_get_contents($url);
$res  = json_decode($json, true);

include("../_conexion/conexion.php");

foreach ($res["data"] as $r) {

    $id         = $r["id_personal"];
    $id_cargo   = $r["id_cargo"];
    $id_area    = $r["id_area"];
    $id_tipo    = $r["id_tipo"];
    $nom        = $r["nom_personal"];
    $dni        = $r["dni_personal"];
    $cel        = $r["cel_personal"];
    $email      = $r["email_personal"];
    $pass       = $r["pass_personal"];
    $act        = $r["act_personal"];
    $updated_at = $r["updated_at"];

    // 1️⃣ Verificar si existe localmente
    $sqlSel = "SELECT updated_at FROM personal WHERE id_personal=?";
    $stmtSel = $con->prepare($sqlSel);
    $stmtSel->bind_param("i", $id);
    $stmtSel->execute();
    $resultSel = $stmtSel->get_result();

    if ($resultSel->num_rows > 0) {

        // Ya existe → comparo fechas
        $row = $resultSel->fetch_assoc();
        $local_updated = $row["updated_at"];

        if (strtotime($updated_at) > strtotime($local_updated)) {

            // Remoto es más nuevo → UPDATE
            $sqlUpd = "UPDATE personal SET id_cargo=?, id_area=?, id_tipo=?, nom_personal=?, dni_personal=?, cel_personal=?, email_personal=?, pass_personal=?, act_personal=?, updated_at=? WHERE id_personal=?";
            $stmtUpd = $con->prepare($sqlUpd);
            $stmtUpd->bind_param("iiisssssis", $id_cargo, $id_area, $id_tipo, $nom, $dni, $cel, $email, $pass, $act, $updated_at, $id);
            $stmtUpd->execute();
        }

        // Local es igual o más nuevo → skip
        continue;
    }

    // 2️⃣ No existe → INSERT
    $sqlIns = "INSERT INTO personal (id_personal, id_cargo, id_area, id_tipo, nom_personal, dni_personal, cel_personal, email_personal, pass_personal, act_personal, updated_at)
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtIns = $con->prepare($sqlIns);
    $stmtIns->bind_param("iiisssssis", $id, $id_cargo, $id_area, $id_tipo, $nom, $dni, $cel, $email, $pass, $act, $updated_at);
    $stmtIns->execute();
}

// ----------------------------
// GUARDAR NUEVA FECHA
// ----------------------------
date_default_timezone_set('America/Lima');
file_put_contents("last_sync_personal.txt", date("Y-m-d H:i:s"));
?>