<?php
include("../_conexion/conexion.php");

$id = $_POST["id_area"] ?? null;
$nom = $_POST["nom_area"] ?? null;
$act = $_POST["act_area"] ?? 1;
$updated_at_remoto = $_POST["updated_at"] ?? null;

// VALIDACIÓN CRÍTICA: evitar duplicados
if ($id === null || $id === "" || !is_numeric($id)) {
    echo json_encode(["status"=>"error","msg"=>"ID_VACIO_O_INVALIDO"]);
    exit;
}

// 1️⃣ Buscar si el registro ya existe
$sqlSelect = "SELECT updated_at FROM area WHERE id_area=?";
$stmtSel = $con->prepare($sqlSelect);
$stmtSel->bind_param("i", $id);
$stmtSel->execute();
$resultSel = $stmtSel->get_result();

if ($resultSel->num_rows > 0) {

    // Registro EXISTE localmente
    $row = $resultSel->fetch_assoc();
    $updated_at_local = $row["updated_at"];

    // 2️⃣ Comparar fechas
    if (strtotime($updated_at_remoto) > strtotime($updated_at_local)) {

        // 3️⃣ REMOTO es más reciente → hacer UPDATE
        $sqlUpdate = "UPDATE area 
                      SET nom_area=?, act_area=?, updated_at=? 
                      WHERE id_area=?";
        $stmtUpdate = $con->prepare($sqlUpdate);
        $stmtUpdate->bind_param("sisi", $nom, $act, $updated_at_remoto, $id);
        $stmtUpdate->execute();

        echo json_encode(["status"=>"ok","msg"=>"updated"]);
        exit;

    } else {
        echo json_encode(["status"=>"ok","msg"=>"skipped"]);
        exit;
    }
}

// 4️⃣ Registro NO existe → hacer INSERT
$sqlInsert = "INSERT INTO area (id_area, nom_area, act_area, updated_at)
              VALUES (?, ?, ?, ?)";
$stmtInsert = $con->prepare($sqlInsert);
$stmtInsert->bind_param("isis", $id, $nom, $act, $updated_at_remoto);
$stmtInsert->execute();

echo json_encode(["status"=>"ok","msg"=>"inserted"]);
?>