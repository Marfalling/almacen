<?php
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
            LEFT JOIN {$bd_complemento}.personal per1 ON c.id_personal = per1.id_personal
            LEFT JOIN {$bd_complemento}.personal per2 ON c.id_personal_aprueba_tecnica = per2.id_personal
            LEFT JOIN {$bd_complemento}.personal per3 ON c.id_personal_aprueba_financiera = per3.id_personal
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
            LEFT JOIN {$bd_complemento}.personal per1 ON c.id_personal = per1.id_personal
            LEFT JOIN {$bd_complemento}.personal per2 ON c.id_personal_aprueba_tecnica = per2.id_personal
            LEFT JOIN {$bd_complemento}.personal per3 ON c.id_personal_aprueba_financiera = per3.id_personal
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

function CompraEsEditable($id_compra)
{
    include("../_conexion/conexion.php");
    
    $sql = "SELECT c.est_compra, c.id_pedido 
            FROM compra c 
            WHERE c.id_compra = $id_compra";
    
    $resultado = mysqli_query($con, $sql);
    $compra = mysqli_fetch_assoc($resultado);
    
    if (!$compra || $compra['est_compra'] != 1) {
        mysqli_close($con);
        return false;
    }
    
    $id_pedido = $compra['id_pedido'];
    $sql_verificados = "SELECT COUNT(*) as total_verificados 
                        FROM pedido_detalle 
                        WHERE id_pedido = $id_pedido 
                        AND cant_fin_pedido_detalle IS NOT NULL 
                        AND est_pedido_detalle <> 0";
    
    $resultado_verificados = mysqli_query($con, $sql_verificados);
    $row = mysqli_fetch_assoc($resultado_verificados);
    
    mysqli_close($con);
    
    return ($row['total_verificados'] == 0);
}

function AprobarCompra($id_compra, $id_personal)
{
    include("../_conexion/conexion.php");

    $sql_check = "SELECT est_compra FROM compra WHERE id_compra = '$id_compra'";
    $res_check = mysqli_query($con, $sql_check);
    $row_check = mysqli_fetch_array($res_check, MYSQLI_ASSOC);

    if ($row_check && $row_check['est_compra'] == 2) {
        mysqli_close($con);
        return false;
    }

    $sql_update = "UPDATE compra 
                   SET est_compra = 2, 
                       id_personal_aprueba = '$id_personal'
                   WHERE id_compra = '$id_compra'";

    $res_update = mysqli_query($con, $sql_update);

    mysqli_close($con);
    return $res_update;
}

function AprobarCompraTecnica($id_compra, $id_personal)
{
    include("../_conexion/conexion.php");

    $sql_check = "SELECT c.est_compra, c.id_pedido, c.id_personal_aprueba_tecnica, c.id_personal_aprueba_financiera 
                  FROM compra c 
                  WHERE c.id_compra = '$id_compra'";
    $res_check = mysqli_query($con, $sql_check);
    $row = mysqli_fetch_array($res_check, MYSQLI_ASSOC);

    if (!$row || $row['est_compra'] == 0 || $row['est_compra'] == 3) {
        mysqli_close($con);
        return false;
    }

    if (!empty($row['id_personal_aprueba_tecnica'])) {
        mysqli_close($con);
        return false;
    }

    $sql_update = "UPDATE compra 
                   SET id_personal_aprueba_tecnica = '$id_personal'
                   WHERE id_compra = '$id_compra'";
    $res_update = mysqli_query($con, $sql_update);

    if ($res_update) {
        // Si ya tiene aprobación financiera, cambiar estado a aprobado
        if (!empty($row['id_personal_aprueba_financiera'])) {
            mysqli_query($con, "UPDATE compra SET est_compra = 2 WHERE id_compra = '$id_compra'");
            
            // AGREGAR: Verificar si todas las órdenes del pedido están aprobadas
            verificarYCompletarPedido($row['id_pedido'], $con);
        }
    }

    mysqli_close($con);
    return $res_update;
}

function AprobarCompraFinanciera($id_compra, $id_personal)
{
    include("../_conexion/conexion.php");

    $sql_check = "SELECT c.est_compra, c.id_pedido, c.id_personal_aprueba_tecnica, c.id_personal_aprueba_financiera 
                  FROM compra c 
                  WHERE c.id_compra = '$id_compra'";
    $res_check = mysqli_query($con, $sql_check);
    $row = mysqli_fetch_array($res_check, MYSQLI_ASSOC);

    if (!$row || $row['est_compra'] == 0 || $row['est_compra'] == 3) {
        mysqli_close($con);
        return false;
    }

    if (!empty($row['id_personal_aprueba_financiera'])) {
        mysqli_close($con);
        return false;
    }

    $sql_update = "UPDATE compra 
                   SET id_personal_aprueba_financiera = '$id_personal'
                   WHERE id_compra = '$id_compra'";
    $res_update = mysqli_query($con, $sql_update);

    if ($res_update) {
        // Si ya tiene aprobación técnica, cambiar estado a aprobado
        if (!empty($row['id_personal_aprueba_tecnica'])) {
            mysqli_query($con, "UPDATE compra SET est_compra = 2 WHERE id_compra = '$id_compra'");
            
            // AGREGAR: Verificar si todas las órdenes del pedido están aprobadas
            verificarYCompletarPedido($row['id_pedido'], $con);
        }
    }

    mysqli_close($con);
    return $res_update;
}

function verificarYCompletarPedido($id_pedido, $con = null)
{
    $cerrar_conexion = false;
    
    if ($con === null) {
        include("../_conexion/conexion.php");
        $cerrar_conexion = true;
    }
    
    // Verificar estado actual del pedido
    $sql_estado = "SELECT est_pedido FROM pedido WHERE id_pedido = $id_pedido";
    $res_estado = mysqli_query($con, $sql_estado);
    $row_estado = mysqli_fetch_assoc($res_estado);
    
    // Solo actualizar si está en PENDIENTE (1) o COMPLETADO (2)
    if (!$row_estado || !in_array($row_estado['est_pedido'], [1, 2])) {
        if ($cerrar_conexion) mysqli_close($con);
        return;
    }
    
    // Verificar si hay órdenes pendientes (sin aprobar completamente)
    $sql_pendientes = "SELECT COUNT(*) as total_pendientes
                      FROM compra 
                      WHERE id_pedido = $id_pedido 
                      AND est_compra = 1";
    
    $resultado = mysqli_query($con, $sql_pendientes);
    $row = mysqli_fetch_assoc($resultado);
    
    // Si NO hay órdenes pendientes, marcar pedido como APROBADO
    if ($row['total_pendientes'] == 0) {
        // Verificar que haya al menos una orden aprobada
        $sql_aprobadas = "SELECT COUNT(*) as total_aprobadas
                         FROM compra 
                         WHERE id_pedido = $id_pedido 
                         AND est_compra = 2";
        
        $resultado_aprobadas = mysqli_query($con, $sql_aprobadas);
        $row_aprobadas = mysqli_fetch_assoc($resultado_aprobadas);
        
        if ($row_aprobadas['total_aprobadas'] > 0) {
            //  Actualizar a APROBADO (estado 3)
            $sql_aprobar = "UPDATE pedido SET est_pedido = 3 WHERE id_pedido = $id_pedido";
            mysqli_query($con, $sql_aprobar);
        }
    }
    
    if ($cerrar_conexion) {
        mysqli_close($con);
    }
}

// Nueva función para consultar UNA compra específica por su ID
function ConsultarCompraPorId($id_compra)
{
    include("../_conexion/conexion.php");
    
    $id_compra = intval($id_compra);
    
    $sql = "SELECT c.*, 
                p.nom_proveedor,
                p.ruc_proveedor,
                m.nom_moneda,
                CASE 
                    WHEN m.id_moneda = 1 THEN 'S/.'
                    WHEN m.id_moneda = 2 THEN 'US$'
                    ELSE 'S/.'
                END as sim_moneda,
                per.nom_personal,
                per_tec.nom_personal AS nom_aprobado_tecnica,
                per_fin.nom_personal AS nom_aprobado_financiera
            FROM compra c
            INNER JOIN proveedor p ON c.id_proveedor = p.id_proveedor
            INNER JOIN moneda m ON c.id_moneda = m.id_moneda
            LEFT JOIN {$bd_complemento}.personal per ON c.id_personal = per.id_personal
            LEFT JOIN {$bd_complemento}.personal per_tec ON c.id_personal_aprueba_tecnica = per_tec.id_personal
            LEFT JOIN {$bd_complemento}.personal per_fin ON c.id_personal_aprueba_financiera = per_fin.id_personal
            WHERE c.id_compra = $id_compra";
    
    $resultado = mysqli_query($con, $sql);
    
    if (!$resultado) {
        error_log("Error en ConsultarCompraPorId SQL: " . mysqli_error($con));
        mysqli_close($con);
        return array();
    }
    
    $compra = mysqli_fetch_assoc($resultado);
    
    mysqli_close($con);
    return $compra ? array($compra) : array();
}

function ConsultarCompra($id_pedido)
{
    include("../_conexion/conexion.php");
    
    $id_pedido = intval($id_pedido);
    
    $sql = "SELECT c.*, 
                p.nom_proveedor,
                p.ruc_proveedor,
                m.nom_moneda,
                per.nom_personal,
                per_tec.nom_personal AS nom_aprobado_tecnica,
                per_fin.nom_personal AS nom_aprobado_financiera
            FROM compra c
            INNER JOIN proveedor p ON c.id_proveedor = p.id_proveedor
            INNER JOIN moneda m ON c.id_moneda = m.id_moneda
            LEFT JOIN {$bd_complemento}.personal per ON c.id_personal = per.id_personal
            LEFT JOIN {$bd_complemento}.personal per_tec ON c.id_personal_aprueba_tecnica = per_tec.id_personal
            LEFT JOIN {$bd_complemento}.personal per_fin ON c.id_personal_aprueba_financiera = per_fin.id_personal
            WHERE c.id_pedido = $id_pedido
            ORDER BY c.id_compra DESC";
    
    $resultado = mysqli_query($con, $sql);
    
    if (!$resultado) {
        error_log("Error en ConsultarCompra SQL: " . mysqli_error($con));
        mysqli_close($con);
        return array();
    }
    
    $compras = array();
    while ($row = mysqli_fetch_assoc($resultado)) {
        $compras[] = $row;
    }
    
    mysqli_close($con);
    return $compras;
}

function ConsultarCompraDetalle($id_compra)
{
    include("../_conexion/conexion.php");
    
    $id_compra = intval($id_compra); // Sanitizar entrada
    
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
            WHERE cd.id_compra = $id_compra
            AND cd.est_compra_detalle = 1
            ORDER BY cd.id_compra_detalle";
    
    $resc = mysqli_query($con, $sql);
    
    if (!$resc) {
        error_log("Error en ConsultarCompraDetalle SQL: " . mysqli_error($con));
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

    $sql_check = "SELECT est_pedido FROM pedido WHERE id_pedido = '$id_pedido'";
    $res_check = mysqli_query($con, $sql_check);
    $row_check = mysqli_fetch_array($res_check, MYSQLI_ASSOC);

    if ($row_check && $row_check['est_pedido'] == 0) {
        mysqli_close($con);
        return false;
    }

    $sql_update = "UPDATE pedido 
                   SET est_pedido = 0
                   WHERE id_pedido = '$id_pedido'";

    $res_update = mysqli_query($con, $sql_update);

    mysqli_close($con);
    return $res_update;
}

function ObtenerComprasProximasVencer($dias_anticipacion = 3)
{
    include("../_conexion/conexion.php");
    
    $sql = "SELECT 
                c.id_compra,
                c.fec_compra,
                c.plaz_compra,
                c.est_compra,
                DATE_ADD(c.fec_compra, INTERVAL c.plaz_compra DAY) as fecha_vencimiento,
                DATEDIFF(DATE_ADD(c.fec_compra, INTERVAL c.plaz_compra DAY), CURDATE()) as dias_restantes,
                p.nom_proveedor,
                pe.cod_pedido
            FROM compra c
            LEFT JOIN proveedor p ON c.id_proveedor = p.id_proveedor
            LEFT JOIN pedido pe ON c.id_pedido = pe.id_pedido
            WHERE c.est_compra = 1 
            AND c.plaz_compra IS NOT NULL 
            AND c.plaz_compra >= 1
            AND DATEDIFF(DATE_ADD(c.fec_compra, INTERVAL c.plaz_compra DAY), CURDATE()) BETWEEN 0 AND $dias_anticipacion
            ORDER BY fecha_vencimiento ASC";
    
    $resultado = mysqli_query($con, $sql);
    
    if (!$resultado) {
        mysqli_close($con);
        return array();
    }
    
    $compras = array();
    while ($row = mysqli_fetch_assoc($resultado)) {
        $compras[] = $row;
    }
    
    mysqli_close($con);
    return $compras;
}