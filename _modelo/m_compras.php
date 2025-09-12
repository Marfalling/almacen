<?php
function MostrarCompras()
{
    include("../_conexion/conexion.php");

    $sql = "SELECT 
                c.*,
                pe.cod_pedido,
                p.nom_proveedor,
                per1.nom_personal AS nom_registrado,
                COALESCE(per2.nom_personal, '-') AS nom_aprobado
            FROM compra c
            LEFT JOIN pedido pe ON c.id_pedido = pe.id_pedido
            LEFT JOIN proveedor p ON c.id_proveedor = p.id_proveedor 
            LEFT JOIN personal per1 ON c.id_personal = per1.id_personal
            LEFT JOIN personal per2 ON c.id_personal_aprueba = per2.id_personal
            ORDER BY c.id_compra DESC";
    $resc = mysqli_query($con, $sql);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    return $resultado;
}
function AprobarCompra($id_compra, $id_personal)
{
    include("../_conexion/conexion.php");

    // Primero, verificar si la compra ya está aprobada
    $sql_check = "SELECT est_compra FROM compra WHERE id_compra = '$id_compra'";
    $res_check = mysqli_query($con, $sql_check);
    $row_check = mysqli_fetch_array($res_check, MYSQLI_ASSOC);

    if ($row_check && $row_check['est_compra'] == 2) {
        // La compra ya está aprobada
        mysqli_close($con);
        return false;
    }

    // Actualizar el estado de la compra a aprobado (2)
    $sql_update = "UPDATE compra 
                   SET est_compra = 2, 
                       id_personal_aprueba = '$id_personal'
                   WHERE id_compra = '$id_compra'";

    $res_update = mysqli_query($con, $sql_update);

    mysqli_close($con);
    return $res_update;
}
function AnularCompra($id_compra, $id_personal)
{
    include("../_conexion/conexion.php");

    // Primero, verificar si la compra ya está anulada
    $sql_check = "SELECT est_compra FROM compra WHERE id_compra = '$id_compra'";
    $res_check = mysqli_query($con, $sql_check);
    $row_check = mysqli_fetch_array($res_check, MYSQLI_ASSOC);

    if ($row_check && $row_check['est_compra'] == 0){
        // La compra ya está anulada
        mysqli_close($con);
        return false;
    }

    // Actualizar el estado de la compra a anulada (3)
    $sql_update = "UPDATE compra 
                   SET est_compra = 0
                   WHERE id_compra = '$id_compra'";

    $res_update = mysqli_query($con, $sql_update);

    mysqli_close($con);
    return $res_update;
}
?>