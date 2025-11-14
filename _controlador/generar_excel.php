<?php
ob_start(); // 🔵 MUY IMPORTANTE: actíva el búfer para no romper el Excel

require_once("../_conexion/conexion.php");
require_once("../_modelo/m_comprobante.php");

echo "<pre>";
echo "DIR: " . __DIR__ . "\n";
echo "Buscando autoload en:\n";
echo __DIR__ . "/../_complemento/vendor/autoload.php";
echo "</pre>";

if (!file_exists(__DIR__ . "/../_complemento/vendor/autoload.php")) {
    die("❌ ERROR: autoload.php NO EXISTE en esa ubicación");
}

echo "✔ autoload encontrado";
exit;

require_once(__DIR__ . "/../_complemento/vendor/autoload.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// ============================
// 1️⃣ Crear Excel
// ============================
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Cabeceras
$headers = [
    'ID Comprobante', 'ID Compra', 'Tipo Documento', 'Serie', 'Número',
    'Monto Total IGV', 'Total a Pagar', 'Moneda', 'Fecha Registro', 'Estado'
];

$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $col++;
}

// ============================
// 2️⃣ Obtener comprobantes
// ============================
$comprobantes = obtenerComprobantesEstado1($con);

if (empty($comprobantes)) {
    die("No hay comprobantes con estado 1.");
}

// ============================
// 3️⃣ Llenar Excel
// ============================
$fila = 2;

foreach ($comprobantes as $row) {
    $sheet->setCellValue("A$fila", $row['id_comprobante']);
    $sheet->setCellValue("B$fila", $row['id_compra']);
    $sheet->setCellValue("C$fila", $row['id_tipo_documento']);
    $sheet->setCellValue("D$fila", $row['serie']);
    $sheet->setCellValue("E$fila", $row['numero']);
    $sheet->setCellValue("F$fila", $row['monto_total_igv']);
    $sheet->setCellValue("G$fila", $row['total_pagar']);
    $sheet->setCellValue("H$fila", $row['id_moneda']);
    $sheet->setCellValue("I$fila", $row['fec_registro']);
    $sheet->setCellValue("J$fila", $row['est_comprobante']);
    $fila++;
}

// ============================
// 4️⃣ Enviar Excel al navegador
// ============================
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="comprobantes.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

// Limpia buffer (evita texto basura en XLSX)
ob_end_clean();

// ============================
// 5️⃣ Actualizar estados SOLO si Excel se generó
// ============================
ActualizarComprobantesEstado();

exit;
?>