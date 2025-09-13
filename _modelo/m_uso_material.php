<?php
function MostrarUsoMaterial()
{
    include("../_conexion/conexion.php");

    $sql = "SELECT 
                usm.*,
                alm.nom_almacen,
                o.nom_obra,
                c.nom_cliente,
                u.nom_ubicacion,
                per1.nom_personal AS nom_registrado,
                COALESCE(per2.nom_personal, '-') AS nom_solicitante
            FROM uso_material usm
            LEFT JOIN almacen alm ON usm.id_almacen = alm.id_almacen
                LEFT JOIN obra o ON alm.id_obra = o.id_obra
                LEFT JOIN cliente c ON alm.id_cliente = c.id_cliente
            LEFT JOIN ubicacion u ON usm.id_ubicacion = u.id_ubicacion
            LEFT JOIN personal per1 ON usm.id_personal = per1.id_personal
            LEFT JOIN personal per2 ON usm.id_solicitante = per2.id_personal
            ORDER BY usm.id_uso_material DESC";
    $resc = mysqli_query($con, $sql);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    return $resultado;
}
?>