<?php 
function GuardarRetiroMaterial($id_reporte, $materiales)
{
    include("../_conexion/conexion.php");

    if (!is_array($materiales) || empty($materiales)) {
        return;
    }

    // 1. Insertar en retiro
    $sql = "INSERT INTO retiro (id_reporte, fec_retiro, est_retiro) VALUES (?, NOW(), 1)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id_reporte);

    if ($stmt->execute()) {
        $id_retiro = $stmt->insert_id;

        // 2. Insertar detalles
        $sqlDetalle = "INSERT INTO retiro_detalle (id_retiro, mat_retiro_detalle, cant_retiro_detalle, est_retiro_detalle) 
                       VALUES (?, ?, ?, 1)";
        $stmtDetalle = $con->prepare($sqlDetalle);

        foreach ($materiales as $item) {
            $partes = explode(" - ", $item);
            $material = trim($partes[0]);
            $cantidad = isset($partes[1]) ? (float) trim($partes[1]) : 1;

            $stmtDetalle->bind_param("isd", $id_retiro, $material, $cantidad);
            $stmtDetalle->execute();
        }

        $stmtDetalle->close();
    }

    $stmt->close();
    $con->close();
}
function ActualizarRetiroMaterial_($id_reporte, $materiales, $materialesAll)
{
    include("../_conexion/conexion.php");

    $id_retiro = ConsultarRetiroID($id_reporte);
    if (is_null($id_retiro)) {
        return;
    }

    if (!is_array($materialesAll) || empty($materialesAll)) {
        EliminarRetiroyDetalle($id_retiro);
        return;
    }

    EliminarRetiroDetalleNoA($id_retiro);

    $sqlDetalle = "INSERT INTO retiro_detalle (id_retiro, mat_retiro_detalle, cant_retiro_detalle, est_retiro_detalle) 
                   VALUES (?, ?, ?, ?)";
    $stmtDetalle = $con->prepare($sqlDetalle);

    foreach ($materiales as $item) {
        $material = isset($item['material']) ? trim($item['material']) : '';
        $cantidad = isset($item['cantidad']) ? (float) $item['cantidad'] : 1;
        $estado = 1; // <-- CORRECTO

        if ($material === '') continue;

        $stmtDetalle->bind_param("isdi", $id_retiro, $material, $cantidad, $estado);
        $stmtDetalle->execute();
    }

    $stmtDetalle->close();
}
function ActualizarRetiroMaterial($id_reporte, $materiales, $materialesAll)
{
    include("../_conexion/conexion.php");

    // 1️⃣ Consultar si ya existe retiro
    $id_retiro = ConsultarRetiroID($id_reporte);

    // 2️⃣ Si no existe y hay materiales => Crear nuevo retiro
    if (is_null($id_retiro)) {
        if (!is_array($materialesAll) || empty($materialesAll)) {
            return; // No hay nada que registrar
        }

        $sqlNuevoRetiro = "INSERT INTO retiro (id_reporte, fec_retiro, est_retiro) VALUES (?, NOW(), 1)";
        $stmtNuevo = $con->prepare($sqlNuevoRetiro);
        $stmtNuevo->bind_param("i", $id_reporte);
        $stmtNuevo->execute();
        $id_retiro = $stmtNuevo->insert_id; // ID generado
        $stmtNuevo->close();
    }

    // 3️⃣ Si no hay materiales => eliminar retiro existente
    if (!is_array($materialesAll) || empty($materialesAll)) {
        EliminarRetiroyDetalle($id_retiro);
        return;
    }

    // 4️⃣ Eliminar materiales no incluidos en la nueva lista
    EliminarRetiroDetalleNoA($id_retiro);

    // 5️⃣ Insertar o actualizar los materiales enviados
    $sqlDetalle = "INSERT INTO retiro_detalle (id_retiro, mat_retiro_detalle, cant_retiro_detalle, est_retiro_detalle) 
                   VALUES (?, ?, ?, ?)";
    $stmtDetalle = $con->prepare($sqlDetalle);

    foreach ($materiales as $item) {
        $material = isset($item['material']) ? trim($item['material']) : '';
        $cantidad = isset($item['cantidad']) ? (float) $item['cantidad'] : 1;
        $estado = 1; // activo

        if ($material === '') continue;

        $stmtDetalle->bind_param("isdi", $id_retiro, $material, $cantidad, $estado);
        $stmtDetalle->execute();
    }

    $stmtDetalle->close();
}

function ConsultarRetiroMaterial($id_reporte)
{
    include("../_conexion/conexion.php");

    $materiales = [];

    // Buscar el retiro asociado al reporte
    $sql = "SELECT 
		rd.mat_retiro_detalle, 
		rd.cant_retiro_detalle,
		rd.id_material,
		rd.cod_retiro_detalle_act,
		rd.mat_retiro_detalle_act,
		CASE 
			WHEN rd.id_material = 0 
			AND rd.cod_retiro_detalle_act = '' 
			AND rd.mat_retiro_detalle_act = '' 
			THEN 1 
			ELSE 0 
		END AS estado
	FROM retiro r
	INNER JOIN retiro_detalle rd ON rd.id_retiro = r.id_retiro
	WHERE r.id_reporte = ? 
	AND r.est_retiro = 1 
	AND rd.est_retiro_detalle = 1;
	";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id_reporte);
    $stmt->execute();
    $result = $stmt->get_result();

   while ($row = $result->fetch_assoc()) {
		$materiales[] = [
			'material' => $row['mat_retiro_detalle'],
			'cantidad' => $row['cant_retiro_detalle'],
			'estado' => (int)$row['estado']
		];
	}


    $stmt->close();
    $con->close();

    return $materiales;
}
function ConsultarRetiroID($id_reporte) 
{
    include("../_conexion/conexion.php");

    $sql = "SELECT id_retiro FROM retiro WHERE id_reporte = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id_reporte);
    $stmt->execute();
    $stmt->bind_result($id_retiro);

    if ($stmt->fetch()) {
        $stmt->close();
        return $id_retiro;
    }

    $stmt->close();
    return null; // o lanzar excepción si no se encuentra
}
function EliminarRetiroyDetalle($id_retiro) 
{
    include("../_conexion/conexion.php");

    // Primero elimina los detalles
    $sql1 = "DELETE FROM retiro_detalle WHERE id_retiro = ?";
    $stmt1 = $con->prepare($sql1);
    $stmt1->bind_param("i", $id_retiro);
    $stmt1->execute();
    $stmt1->close();

    // Luego elimina el retiro principal
    $sql2 = "DELETE FROM retiro WHERE id_retiro = ?";
    $stmt2 = $con->prepare($sql2);
    $stmt2->bind_param("i", $id_retiro);
    $stmt2->execute();
    $stmt2->close();
}
function EliminarRetiroDetalleNoA($id_retiro) 
{
    include("../_conexion/conexion.php");

    $sql = "DELETE FROM retiro_detalle 
            WHERE id_retiro = ? 
              AND id_material = 0 
              AND cod_retiro_detalle_act = '' 
              AND mat_retiro_detalle_act = ''";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id_retiro);
    $stmt->execute();
    $stmt->close();
}
?>