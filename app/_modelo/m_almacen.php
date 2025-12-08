<?php
//-----------------------------------------------------------------------
function MostrarAlmacenesActivosPorCliente($id_cliente)
{
    include("../_conexion/conexion.php");

    $sql = "
        SELECT 
            a.id_almacen,
            a.id_cliente,
            a.id_obra,
            a.nom_almacen,
            c.nom_cliente,
            COALESCE(s.nom_subestacion, 'SIN OBRA') AS nom_obra,
            a.est_almacen
        FROM almacen a
        LEFT JOIN {$bd_complemento}.cliente c 
               ON a.id_cliente = c.id_cliente
        LEFT JOIN {$bd_complemento}.subestacion s 
               ON a.id_obra = s.id_subestacion
        WHERE a.est_almacen = 1
          AND a.id_cliente = $id_cliente
        ORDER BY a.nom_almacen ASC;
    ";

    $res = mysqli_query($con, $sql);
    $resultado = mysqli_fetch_all($res, MYSQLI_ASSOC);

    mysqli_close($con);
    return $resultado;
}
?>