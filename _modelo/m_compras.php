<?php

/*function MostrarCompras()
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
}*/

function MostrarCompras()
{
    include("../_conexion/conexion.php");

    $sql = "SELECT 
                c.*,
                pe.cod_pedido,
                p.nom_proveedor,
                per1.nom_personal AS nom_registrado,
                COALESCE(per2.nom_personal, '-') AS nom_aprobado_tecnica,
                COALESCE(per3.nom_personal, '-') AS nom_aprobado_financiera
            FROM compra c
            LEFT JOIN pedido pe ON c.id_pedido = pe.id_pedido
            LEFT JOIN proveedor p ON c.id_proveedor = p.id_proveedor 
            LEFT JOIN personal per1 ON c.id_personal = per1.id_personal
            LEFT JOIN personal per2 ON c.id_personal_aprueba_tecnica = per2.id_personal
            LEFT JOIN personal per3 ON c.id_personal_aprueba_financiera = per3.id_personal
            ORDER BY c.id_compra DESC";

    $resc = mysqli_query($con, $sql);

    $resultado = [];
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    return $resultado;
}

function MostrarComprasFecha($fecha_inicio = null, $fecha_fin = null)
{
    include("../_conexion/conexion.php");

    $where = "";
    if ($fecha_inicio && $fecha_fin) {
        $where = "WHERE DATE(c.fec_compra) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    } else {
        // Por defecto, solo la fecha actual
        $where = "WHERE DATE(c.fec_compra) = CURDATE()";
    }

    $sql = "SELECT 
                c.*,
                pe.cod_pedido,
                p.nom_proveedor,
                per1.nom_personal AS nom_registrado,
                COALESCE(per2.nom_personal, '-') AS nom_aprobado_tecnica,
                COALESCE(per3.nom_personal, '-') AS nom_aprobado_financiera
            FROM compra c
            LEFT JOIN pedido pe ON c.id_pedido = pe.id_pedido
            LEFT JOIN proveedor p ON c.id_proveedor = p.id_proveedor 
            LEFT JOIN personal per1 ON c.id_personal = per1.id_personal
            LEFT JOIN personal per2 ON c.id_personal_aprueba_tecnica = per2.id_personal
            LEFT JOIN personal per3 ON c.id_personal_aprueba_financiera = per3.id_personal
            $where
            ORDER BY c.id_compra DESC";

    $resc = mysqli_query($con, $sql) or die("Error en consulta: " . mysqli_error($con));

    $resultado = [];
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

// ===================================================================
// Aprobar compra técnicamente
// ===================================================================
function AprobarCompraTecnica($id_compra, $id_personal)
{
    include("../_conexion/conexion.php");

    // Verificar estado y aprobaciones previas
    $sql_check = "SELECT est_compra, id_personal_aprueba_tecnica, id_personal_aprueba_financiera 
                  FROM compra 
                  WHERE id_compra = '$id_compra'";
    $res_check = mysqli_query($con, $sql_check);
    $row = mysqli_fetch_array($res_check, MYSQLI_ASSOC);

    if (!$row || $row['est_compra'] == 0 || $row['est_compra'] == 3) {
        mysqli_close($con);
        return false; // no se puede aprobar
    }

    // Si ya estaba aprobado técnicamente 
    if (!empty($row['id_personal_aprueba_tecnica'])) {
        mysqli_close($con);
        return false;
    }

    // Guardar aprobación técnica
    $sql_update = "UPDATE compra 
                   SET id_personal_aprueba_tecnica = '$id_personal'
                   WHERE id_compra = '$id_compra'";
    $res_update = mysqli_query($con, $sql_update);

    // Verificar si ahora ya tiene ambas aprobaciones para cambiar estado
    if ($res_update) {
        if (!empty($row['id_personal_aprueba_financiera'])) {
            mysqli_query($con, "UPDATE compra SET est_compra = 2 WHERE id_compra = '$id_compra'");
        }
    }

    mysqli_close($con);
    return $res_update;
}

// ===================================================================
// Aprobar compra financieramente
// ===================================================================
function AprobarCompraFinanciera($id_compra, $id_personal)
{
    include("../_conexion/conexion.php");

    // Verificar estado y aprobaciones previas
    $sql_check = "SELECT est_compra, id_personal_aprueba_tecnica, id_personal_aprueba_financiera 
                  FROM compra 
                  WHERE id_compra = '$id_compra'";
    $res_check = mysqli_query($con, $sql_check);
    $row = mysqli_fetch_array($res_check, MYSQLI_ASSOC);

    if (!$row || $row['est_compra'] == 0 || $row['est_compra'] == 3) {
        mysqli_close($con);
        return false; // no se puede aprobar
    }

    // Si ya estaba aprobado financieramente 
    if (!empty($row['id_personal_aprueba_financiera'])) {
        mysqli_close($con);
        return false;
    }

    // Guardar aprobación financiera
    $sql_update = "UPDATE compra 
                   SET id_personal_aprueba_financiera = '$id_personal'
                   WHERE id_compra = '$id_compra'";
    $res_update = mysqli_query($con, $sql_update);

    // Verificar si ahora ya tiene ambas aprobaciones para cambiar estado
    if ($res_update) {
        if (!empty($row['id_personal_aprueba_tecnica'])) {
            mysqli_query($con, "UPDATE compra SET est_compra = 2 WHERE id_compra = '$id_compra'");
        }
    }

    mysqli_close($con);
    return $res_update;
}


function AnularCompra($id_compra, $id_personal)
{
    include("../_conexion/conexion.php");

    // verificar si la compra ya está anulada
    $sql_check = "SELECT est_compra FROM compra WHERE id_compra = '$id_compra'";
    $res_check = mysqli_query($con, $sql_check);
    $row_check = mysqli_fetch_array($res_check, MYSQLI_ASSOC);

    if ($row_check && $row_check['est_compra'] == 0){
        // La compra ya está anulada
        mysqli_close($con);
        return false;
    }

    // Obtener el id_pedido asociado a esta compra
    $sql_pedido = "SELECT id_pedido FROM compra WHERE id_compra = '$id_compra'";
    $res_pedido = mysqli_query($con, $sql_pedido);
    $row_pedido = mysqli_fetch_array($res_pedido, MYSQLI_ASSOC);
    $id_pedido = $row_pedido['id_pedido'];

    //Obtener los productos que estaban en esta orden de compra
    $sql_productos = "SELECT id_producto FROM compra_detalle 
                      WHERE id_compra = '$id_compra' AND est_compra_detalle = 1";
    $res_productos = mysqli_query($con, $sql_productos);
    
    $productos_en_compra = array();
    while ($row_prod = mysqli_fetch_array($res_productos, MYSQLI_ASSOC)) {
        $productos_en_compra[] = $row_prod['id_producto'];
    }

    //Revertir el estado de los items del pedido que estaban en esta orden
    // Solo si no están en otra orden de compra activa
    if (!empty($productos_en_compra)) {
        foreach ($productos_en_compra as $id_producto) {
            // Verificar si este producto está en otra orden de compra activa del mismo pedido
            $sql_check_otras = "SELECT COUNT(*) as total 
                               FROM compra_detalle cd
                               INNER JOIN compra c ON cd.id_compra = c.id_compra
                               WHERE cd.id_producto = '$id_producto' 
                               AND c.id_pedido = '$id_pedido'
                               AND c.id_compra != '$id_compra'
                               AND c.est_compra != 0
                               AND cd.est_compra_detalle = 1";
            
            $res_check_otras = mysqli_query($con, $sql_check_otras);
            $row_check_otras = mysqli_fetch_array($res_check_otras, MYSQLI_ASSOC);
            
            // Si NO está en otra orden activa, revertir su estado a 1 (disponible)
            if ($row_check_otras['total'] == 0) {
                $sql_revertir = "UPDATE pedido_detalle 
                                SET est_pedido_detalle = 1 
                                WHERE id_pedido = '$id_pedido' 
                                AND id_producto = '$id_producto'
                                AND est_pedido_detalle = 2";
                mysqli_query($con, $sql_revertir);
            }
        }
    }

    // Actualizar el estado de la compra a anulada (0)
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