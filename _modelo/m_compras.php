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

function ConsultarCompra($id_compra)
{
    include("../_conexion/conexion.php");
    
    $sql = "SELECT 
                c.*,
                pe.cod_pedido,
                pe.fec_req_pedido,
                pe.ot_pedido,
                pe.cel_pedido,
                pe.lug_pedido,
                pe.acl_pedido,
                p.nom_proveedor,
                p.ruc_proveedor,
                p.dir_proveedor,
                p.tel_proveedor,
                p.cont_proveedor,
                per1.nom_personal,
                per1.ape_personal,
                a.nom_almacen,
                o.nom_obra,
                m.nom_moneda,
                c.denv_compra,
                c.plaz_compra,
                c.port_compra
            FROM compra c
            LEFT JOIN pedido pe ON c.id_pedido = pe.id_pedido
            LEFT JOIN proveedor p ON c.id_proveedor = p.id_proveedor
            LEFT JOIN personal per1 ON c.id_personal = per1.id_personal
            LEFT JOIN almacen a ON pe.id_almacen = a.id_almacen
            LEFT JOIN obra o ON a.id_obra = o.id_obra
            LEFT JOIN moneda m ON c.id_moneda = m.id_moneda
            WHERE c.id_compra = '$id_compra'";
    
    $resc = mysqli_query($con, $sql);
    
    if (!$resc) {
        echo "Error en la consulta SQL: " . mysqli_error($con);
        mysqli_close($con);
        return array();
    }
    
    $resultado = array();
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }
    
    mysqli_close($con);
    return $resultado;
}

function ConsultarCompraDetalle($id_compra)
{
    include("../_conexion/conexion.php");
    
    $sql = "SELECT 
                cd.*,
                pd.prod_pedido_detalle,
                pd.com_pedido_detalle,
                pd.req_pedido,
                pr.nom_producto,
                pr.cod_material,
                um.nom_unidad_medida
            FROM compra_detalle cd
            LEFT JOIN compra c ON cd.id_compra = c.id_compra
            LEFT JOIN pedido_detalle pd ON cd.id_producto = pd.id_producto 
                AND pd.id_pedido = c.id_pedido
            LEFT JOIN producto pr ON cd.id_producto = pr.id_producto
            LEFT JOIN unidad_medida um ON pr.id_unidad_medida = um.id_unidad_medida
            WHERE cd.id_compra = '$id_compra'
            AND cd.est_compra_detalle = 1
            ORDER BY cd.id_compra_detalle";
    
    $resc = mysqli_query($con, $sql);
    
    if (!$resc) {
        echo "Error en la consulta SQL: " . mysqli_error($con);
        mysqli_close($con);
        return array();
    }
    
    $resultado = array();
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }
    
    mysqli_close($con);
    return $resultado;
}

function AnularPedido($id_pedido, $id_personal)
{
    include("../_conexion/conexion.php");

    // Verificar si el pedido ya está anulado
    $sql_check = "SELECT est_pedido FROM pedido WHERE id_pedido = '$id_pedido'";
    $res_check = mysqli_query($con, $sql_check);
    $row_check = mysqli_fetch_array($res_check, MYSQLI_ASSOC);

    if ($row_check && $row_check['est_pedido'] == 0) {
        // El pedido ya está anulado
        mysqli_close($con);
        return false;
    }

    // Actualizar el estado del pedido a anulado (0)
    $sql_update = "UPDATE pedido 
                   SET est_pedido = 0
                   WHERE id_pedido = '$id_pedido'";

    $res_update = mysqli_query($con, $sql_update);

    mysqli_close($con);
    return $res_update;
}
?>