<?php
include("../_conexion/conexion.php");

$id = $_POST["id_subestacion"] ?? null;
$id_cliente = $_POST["id_cliente"] ?? null;
$nom = $_POST["nom_subestacion"] ?? null;
$cod = $_POST["cod_subestacion"] ?? null;
$act = $_POST["act_subestacion"] ?? 1;
$updated_at_remoto = $_POST["updated_at"] ?? null;

// VALIDACIÓN CRÍTICA: evitar duplicados
if ($id === null || $id === "" || !is_numeric($id)) {
    echo json_encode(["status"=>"error","msg"=>"ID_VACIO_O_INVALIDO"]);
    exit;
}

// 1️⃣ Buscar si el registro ya existe
$sqlSelect = "SELECT updated_at FROM subestacion WHERE id_subestacion=?";
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
        $sqlUpdate = "UPDATE subestacion 
                      SET id_cliente=?, cod_subestacion=?, nom_subestacion=?, act_subestacion=?, updated_at=? 
                      WHERE id_subestacion=?";
        $stmtUpdate = $con->prepare($sqlUpdate);
        $stmtUpdate->bind_param("issisi", $id_cliente, $cod, $nom, $act, $updated_at_remoto, $id);
        $stmtUpdate->execute();

        echo json_encode(["status"=>"ok","msg"=>"updated"]);
        exit;

    } else {
        echo json_encode(["status"=>"ok","msg"=>"skipped"]);
        exit;
    }
}

// 4️⃣ Registro NO existe → hacer INSERT
$sqlInsert = "INSERT INTO subestacion (id_subestacion, id_cliente, cod_subestacion, nom_subestacion, act_subestacion, updated_at)
              VALUES (?, ?, ?, ?, ?, ?)";
$stmtInsert = $con->prepare($sqlInsert);
$stmtInsert->bind_param("iissis", $id, $id_cliente, $cod, $nom, $act, $updated_at_remoto);
$stmtInsert->execute();

echo json_encode(["status"=>"ok","msg"=>"inserted"]);
?>