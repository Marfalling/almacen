<?php
// ==============================================================
// ARCHIVO: v_excel_reporte_pedidos.php
// ==============================================================

header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment;filename=" . $filename);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>

<div class="table-responsive">
    
    <table border="1" class="table table-bordered table-hover table-condensed">
    
    <!-- ENCABEZADO -->
    <tr>
        <th bgcolor="#4472C4" style="text-align: center; color: #FFFFFF; font-weight: bold;">Código Pedido</th>
        <th bgcolor="#4472C4" style="text-align: center; color: #FFFFFF; font-weight: bold;">Tipo Pedido</th>
        <th bgcolor="#4472C4" style="text-align: center; color: #FFFFFF; font-weight: bold;">Almacén</th>
        <th bgcolor="#4472C4" style="text-align: center; color: #FFFFFF; font-weight: bold;">Ubicación</th>
        <th bgcolor="#4472C4" style="text-align: center; color: #FFFFFF; font-weight: bold;">Solicitante</th>
        <th bgcolor="#4472C4" style="text-align: center; color: #FFFFFF; font-weight: bold;">Fecha Pedido</th>
        <th bgcolor="#4472C4" style="text-align: center; color: #FFFFFF; font-weight: bold;">Estado</th>
        <th bgcolor="#70AD47" style="text-align: center; color: #FFFFFF; font-weight: bold;">Código Orden</th>
        <th bgcolor="#70AD47" style="text-align: center; color: #FFFFFF; font-weight: bold;">Proveedor</th>
        <th bgcolor="#70AD47" style="text-align: center; color: #FFFFFF; font-weight: bold;">Fecha Registro</th>
        <th bgcolor="#70AD47" style="text-align: center; color: #FFFFFF; font-weight: bold;">Tipo Pago</th>
        <th bgcolor="#70AD47" style="text-align: center; color: #FFFFFF; font-weight: bold;">Moneda</th>
        <th bgcolor="#70AD47" style="text-align: center; color: #FFFFFF; font-weight: bold;">Registrado Por</th>
        <th bgcolor="#70AD47" style="text-align: center; color: #FFFFFF; font-weight: bold;">Aprob. Financiera Por</th>
        <th bgcolor="#FFC000" style="text-align: center; color: #000000; font-weight: bold;">TOTAL</th>
    </tr>
    
    <!-- DATOS -->
    <?php
    if (!empty($consulta)) {
        foreach ($consulta as $row) {
    ?>
    <tr>
        <td><?php echo htmlspecialchars($row['codigo_pedido']); ?></td>
        <td><?php echo htmlspecialchars($row['tipo_pedido']); ?></td>
        <td><?php echo htmlspecialchars($row['almacen']); ?></td>
        <td><?php echo htmlspecialchars($row['ubicacion']); ?></td>
        <td><?php echo htmlspecialchars($row['solicitante']); ?></td>
        <td style="text-align: center;"><?php echo $row['fecha_pedido']; ?></td>
        <td style="text-align: center;"><?php echo htmlspecialchars($row['estado_pedido']); ?></td>
        <td style="text-align: center;"><?php echo htmlspecialchars($row['codigo_orden']); ?></td>
        <td><?php echo htmlspecialchars($row['proveedor']); ?></td>
        <td style="text-align: center;"><?php echo htmlspecialchars($row['fecha_registro']); ?></td>
        <td style="text-align: center;"><?php echo htmlspecialchars($row['tipo_pago']); ?></td>
        <td style="text-align: center;"><?php echo htmlspecialchars($row['moneda']); ?></td>
        <td><?php echo htmlspecialchars($row['registrado_por']); ?></td>
        <td><?php echo htmlspecialchars($row['aprobado_financiera']); ?></td>
        <td style="text-align: right; font-weight: bold;"><?php echo htmlspecialchars($row['total']); ?></td>
    </tr>
    <?php 
        }
    } else {
    ?>
    <tr>
        <td colspan="15" style="text-align: center; color: #999;">No hay datos disponibles</td>
    </tr>
    <?php } ?>
    
    </table>
    
</div>

</body>
</html>