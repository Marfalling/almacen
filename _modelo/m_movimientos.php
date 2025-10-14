<?php
//=======================================================================
// MODELO: m_movimiento.php
//=======================================================================

function MostrarMovimientos($fecha_inicio = null, $fecha_fin = null) {
    include("../_conexion/conexion.php");

    $where = "";
    if ($fecha_inicio && $fecha_fin) {
        $where = "WHERE DATE(m.fec_movimiento) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    } else {
        // por defecto, solo fecha actual
        $where = "WHERE DATE(m.fec_movimiento) = CURDATE()";
    }

    $sql = "SELECT m.*, 
                   p.nom_personal,
                   pr.nom_producto,
                   a.nom_almacen,
                   u.nom_ubicacion
            FROM movimiento m
            LEFT JOIN {$bd_complemento}.personal p ON m.id_personal = p.id_personal
            LEFT JOIN producto pr ON m.id_producto = pr.id_producto
            LEFT JOIN almacen a ON m.id_almacen = a.id_almacen
            LEFT JOIN ubicacion u ON m.id_ubicacion = u.id_ubicacion
            $where
            ORDER BY m.fec_movimiento DESC";

    $res = mysqli_query($con, $sql);
    $resultado = [];

    while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
        $resultado[] = $row;
    }

    mysqli_close($con);
    return $resultado;
}