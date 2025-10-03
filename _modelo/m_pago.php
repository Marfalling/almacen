<?php
//-----------------------------------------------------------------------
// Insertar nuevo pago de una compra
//-----------------------------------------------------------------------
function GrabarPago($id_compra, $id_proveedor_cuenta, $monto, $comprobante, $id_personal, $enviarCorreo = 0) {
    include("../_conexion/conexion.php");

    $id_compra = intval($id_compra);
    $id_personal = intval($id_personal);
    $id_proveedor_cuenta = intval($id_proveedor_cuenta);
    $monto = round(floatval($monto), 2);
    $enviarCorreo = intval($enviarCorreo);

    // Validar compra usando m_pago
    $compra = ConsultarCompraPago($id_compra);
    if (!$compra) {
        mysqli_close($con);
        return "Compra no encontrada.";
    }

    // Validar saldo
    if ($monto > $compra['saldo']) {
        mysqli_close($con);
        return "El monto ingresado excede el saldo pendiente.";
    }

    // Escapar comprobante
    $comprobante = mysqli_real_escape_string($con, $comprobante);

    // Insertar pago
    $sql = "INSERT INTO pago (id_compra, id_proveedor_cuenta, monto, comprobante, fec_pago, id_personal, enviar_correo)
            VALUES ($id_compra, $id_proveedor_cuenta, $monto, '$comprobante', NOW(), $id_personal, $enviarCorreo)";
    $res = mysqli_query($con, $sql);

    if (!$res) {
        $error = mysqli_error($con);
        mysqli_close($con);
        return "Error al registrar el pago: $error";
    }

    // ✅ Actualizar estado si ya está cancelada
    $nuevoSaldo = $compra['saldo'] - $monto;
    if ($nuevoSaldo <= 0) {
        $sql_upd = "UPDATE compra SET est_compra = 3 WHERE id_compra = $id_compra";
        mysqli_query($con, $sql_upd);
    }

    // Enviar correo si aplica
    if ($enviarCorreo) {
        require_once("m_proveedor.php");
        $prov = ObtenerProveedor($compra['id_proveedor']);
        if ($prov && !empty($prov['mail_proveedor'])) {
            $para = $prov['mail_proveedor'];
            $asunto = "Confirmación de Pago - Orden de Compra #$id_compra";
            $mensaje = "Estimado(a) {$prov['nom_proveedor']},\n\n".
                       "Se ha registrado un pago de S/ " . number_format($monto, 2) .
                       " correspondiente a su Orden de Compra #$id_compra.\n\n".
                       "Saldo pendiente: S/ " . number_format($nuevoSaldo, 2) . "\n\n".
                       "Saludos,\nEquipo de Compras";
            @mail($para, $asunto, $mensaje);
        }
    }

    mysqli_close($con);
    return "SI";
}

//-----------------------------------------------------------------------
// Listar pagos de una compra
//-----------------------------------------------------------------------
function MostrarPagosCompra($id_compra) {
    include("../_conexion/conexion.php");
    $id_compra = intval($id_compra);

    $sql = "SELECT p.*, 
                   pc.banco_proveedor, 
                   pc.nro_cuenta_corriente AS num_cuenta,
                   per.nom_personal,
                   per.ape_personal
            FROM pago p
            LEFT JOIN proveedor_cuenta pc ON p.id_proveedor_cuenta = pc.id_proveedor_cuenta
            LEFT JOIN personal per ON p.id_personal = per.id_personal
            WHERE p.id_compra = $id_compra
            ORDER BY p.fec_pago ASC";
    $res = mysqli_query($con, $sql);

    $pagos = [];
    while ($row = mysqli_fetch_assoc($res)) {
        // Alias para mantener compatibilidad con la vista
        $row['fecha_reg'] = $row['fec_pago'];  
        $pagos[] = $row;
    }

    mysqli_close($con);
    return $pagos;
}

//-----------------------------------------------------------------------
// Consultar una compra con info de pagos
//-----------------------------------------------------------------------
function ConsultarCompraPago($id_compra) {
    include("../_conexion/conexion.php");

    $id_compra = intval($id_compra);

    // Traer info principal de la OC con proveedor
    $sql = "SELECT c.*, p.nom_proveedor, p.ruc_proveedor
            FROM compra c
            INNER JOIN proveedor p ON c.id_proveedor = p.id_proveedor
            WHERE c.id_compra = $id_compra";
    $res = mysqli_query($con, $sql);

    if (!$res || mysqli_num_rows($res) == 0) {
        mysqli_close($con);
        
        return false;
    }

    $compra = mysqli_fetch_assoc($res);

    // Calcular monto total de la OC
    $sql_total = "SELECT SUM(cd.cant_compra_detalle * cd.prec_compra_detalle) as total
                  FROM compra_detalle cd
                  WHERE cd.id_compra = $id_compra";
    $res_total = mysqli_query($con, $sql_total);
    $fila_total = mysqli_fetch_assoc($res_total);
    $monto_total = $fila_total['total'] ?? 0;

    // Calcular monto pagado
    $sql_pagado = "SELECT SUM(monto) as pagado
                   FROM pago
                   WHERE id_compra = $id_compra";
    $res_pagado = mysqli_query($con, $sql_pagado);
    $fila_pagado = mysqli_fetch_assoc($res_pagado);
    $monto_pagado = $fila_pagado['pagado'] ?? 0;

    // Saldo pendiente
    $saldo = $monto_total - $monto_pagado;

    // Armar array con todo
    $compra['monto_total'] = $monto_total;
    $compra['monto_pagado'] = $monto_pagado;
    $compra['saldo'] = $saldo;

    mysqli_close($con);
    return $compra;
}
?>