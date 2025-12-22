<?php
include("../_conexion/conexion.php");

$desde = $_GET["desde"] ?? "2000-01-01 00:00:00";

$sql = "SELECT * FROM cliente WHERE updated_at > ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $desde);
$stmt->execute();

$res = $stmt->get_result();
$data = [];

while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    "status" => "ok",
    "data" => $data
]);
?>