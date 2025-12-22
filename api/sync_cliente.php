<?php
// ----------------------------
// CONTROL DE ARCHIVO DE FECHA
// ----------------------------
if (!file_exists("last_sync_cliente.txt")) {
    file_put_contents("last_sync_cliente.txt", "2000-01-01 00:00:00");
}
$ultima_sync = trim(file_get_contents("last_sync_cliente.txt"));
if ($ultima_sync === "") {
    $ultima_sync = "2000-01-01 00:00:00";
}

$desde = urlencode($ultima_sync);

// ----------------------------
// OBTENER LOS DATOS REMOTOS
// ----------------------------
$url = "https://montajeseingenieriaarceperusac.pe/almacen/api/getCambios_cliente.php?desde=$desde&token=TOKEN";
$json = file_get_contents($url);
$res  = json_decode($json, true);

include("../_conexion/conexion.php");

foreach ($res["data"] as $r) {

    $id         = $r["id_cliente"];
    $nom        = $r["nom_cliente"];
    $act        = $r["act_cliente"];
    $updated_at = $r["updated_at"];

    // 1️⃣ Verificar si existe localmente
    $sqlSel = "SELECT updated_at FROM cliente WHERE id_cliente=?";
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
            $sqlUpd = "UPDATE cliente SET nom_cliente=?, act_cliente=?, updated_at=? WHERE id_cliente=?";
            $stmtUpd = $con->prepare($sqlUpd);
            $stmtUpd->bind_param("sisi", $nom, $act, $updated_at, $id);
            $stmtUpd->execute();
        }

        // Local es igual o más nuevo → skip
        continue;
    }

    // 2️⃣ No existe → INSERT
    $sqlIns = "INSERT INTO cliente (id_cliente, nom_cliente, act_cliente, updated_at)
               VALUES (?, ?, ?, ?)";
    $stmtIns = $con->prepare($sqlIns);
    $stmtIns->bind_param("isis", $id, $nom, $act, $updated_at);
    $stmtIns->execute();
}

// ----------------------------
// GUARDAR NUEVA FECHA
// ----------------------------
date_default_timezone_set('America/Lima');
file_put_contents("last_sync_cliente.txt", date("Y-m-d H:i:s"));
?>