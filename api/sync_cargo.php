<?php
// ----------------------------
// CONTROL DE ARCHIVO DE FECHA
// ----------------------------
if (!file_exists("last_sync_cargo.txt")) {
    file_put_contents("last_sync_cargo.txt", "2000-01-01 00:00:00");
}
$ultima_sync = trim(file_get_contents("last_sync_cargo.txt"));
if ($ultima_sync === "") {
    $ultima_sync = "2000-01-01 00:00:00";
}

$desde = urlencode($ultima_sync);

// ----------------------------
// OBTENER LOS DATOS REMOTOS
// ----------------------------
$url = "https://montajeseingenieriaarceperusac.pe/almacen/api/getCambios_cargo.php?desde=$desde&token=TOKEN";
$json = file_get_contents($url);
$res  = json_decode($json, true);

include("../_conexion/conexion.php");

foreach ($res["data"] as $r) {

    $id         = $r["id_cargo"];
    $nom        = $r["nom_cargo"];
    $act        = $r["act_cargo"];
    $updated_at = $r["updated_at"];

    // 1️⃣ Verificar si existe localmente
    $sqlSel = "SELECT updated_at FROM cargo WHERE id_cargo=?";
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
            $sqlUpd = "UPDATE cargo SET nom_cargo=?, act_cargo=?, updated_at=? WHERE id_cargo=?";
            $stmtUpd = $con->prepare($sqlUpd);
            $stmtUpd->bind_param("sisi", $nom, $act, $updated_at, $id);
            $stmtUpd->execute();
        }

        // Local es igual o más nuevo → skip
        continue;
    }

    // 2️⃣ No existe → INSERT
    $sqlIns = "INSERT INTO cargo (id_cargo, nom_cargo, act_cargo, updated_at)
               VALUES (?, ?, ?, ?)";
    $stmtIns = $con->prepare($sqlIns);
    $stmtIns->bind_param("isis", $id, $nom, $act, $updated_at);
    $stmtIns->execute();
}

// ----------------------------
// GUARDAR NUEVA FECHA
// ----------------------------
date_default_timezone_set('America/Lima');
file_put_contents("last_sync_cargo.txt", date("Y-m-d H:i:s"));
?>