<?php
//=======================================================================
// MODELO: m_movimiento.php
//=======================================================================

function MostrarMovimientos($fecha_inicio = null, $fecha_fin = null, $id_personal_filtro = null) {
    include("../_conexion/conexion.php");

    $where = "";
    
    // Filtro de fechas
    if ($fecha_inicio && $fecha_fin) {
        $where = "WHERE DATE(m.fec_movimiento) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    } else {
        $where = "WHERE DATE(m.fec_movimiento) = CURDATE()";
    }
    
    // Filtro por personal
    if ($id_personal_filtro !== null && $id_personal_filtro > 0) {
        $id_personal_filtro = intval($id_personal_filtro);
        $where .= " AND m.id_personal = $id_personal_filtro";
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
    
    if (!$res) {
        error_log("❌ Error en MostrarMovimientos: " . mysqli_error($con));
        mysqli_close($con);
        return array();
    }
    
    $resultado = [];
    while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
        $resultado[] = $row;
    }

    mysqli_close($con);
    return $resultado;
}