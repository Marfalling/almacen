<?php
include("../_conexion/conexion.php");

$id = $_POST["id_personal"] ?? null;
$id_cargo  = $_POST["id_cargo"] ?? null;
$id_area = $_POST["id_area"] ?? null;
$id_tipo = $_POST["id_tipo"] ?? null;
$nom = $_POST["nom_personal"] ?? null;
$dni = $_POST["dni_personal"] ?? null;
$cel = $_POST["cel_personal"] ?? null;
$email = $_POST["email_personal"] ?? null;
$pass = $_POST["pass_personal"] ?? null;
$act = $_POST["act_personal"] ?? 1;
$updated_at_remoto = $_POST["updated_at"] ?? null;

// VALIDACIÓN CRÍTICA: evitar duplicados
if ($id === null || $id === "" || !is_numeric($id)) {
    echo json_encode(["status"=>"error","msg"=>"ID_VACIO_O_INVALIDO"]);
    exit;
}

// 1️⃣ Buscar si el registro ya existe
$sqlSelect = "SELECT updated_at FROM personal WHERE id_personal=?";
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
        $sqlUpdate = "UPDATE personal 
                      SET id_cargo=?, id_area=?, id_tipo=?, nom_personal=?, dni_personal=?, cel_personal=?, email_personal=?, pass_personal=?, act_personal=?, updated_at=? 
                      WHERE id_personal=?";
        $stmtUpdate = $con->prepare($sqlUpdate);
        $stmtUpdate->bind_param("iiisssssis", $id_cargo, $id_area, $id_tipo, $nom, $dni, $cel, $email, $pass, $act, $updated_at_remoto, $id);
        $stmtUpdate->execute();

        echo json_encode(["status"=>"ok","msg"=>"updated"]);
        exit;

    } else {
        echo json_encode(["status"=>"ok","msg"=>"skipped"]);
        exit;
    }
}

// 4️⃣ Registro NO existe → hacer INSERT
$sqlInsert = "INSERT INTO personal (id_personal, id_cargo, id_area, id_tipo, nom_personal, dni_personal, cel_personal, email_personal, pass_personal, act_personal, updated_at)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmtInsert = $con->prepare($sqlInsert);
$stmtInsert->bind_param("iiissssssis", $id, $id_cargo, $id_area, $id_tipo, $nom, $dni, $cel, $email, $pass, $act, $updated_at_remoto);
$stmtInsert->execute();

echo json_encode(["status"=>"ok","msg"=>"inserted"]);
?>