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

    // Actualizar estado si ya está cancelada
    $nuevoSaldo = $compra['saldo'] - $monto;
    if ($nuevoSaldo <= 0) {
        $sql_upd = "UPDATE compra SET est_compra = 4 WHERE id_compra = $id_compra";
        mysqli_query($con, $sql_upd);
    }

    // ================================================================
// Enviar correo de confirmación al proveedor (si aplica)
// ================================================================
    if ($enviarCorreo) {
        require_once("m_proveedor.php");
        $prov = ObtenerProveedor($compra['id_proveedor']);

        if ($prov && !empty($prov['mail_proveedor'])) {

            // ---------------------------------------
            // 📧 Datos del correo
            // ---------------------------------------
            $para = trim($prov['mail_proveedor']); // destinatario real
            $asunto = "Confirmación de Pago - Orden de Compra #$id_compra";

            // Cuerpo HTML del correo
            $mensaje = "
            <html>
            <body style='font-family: Arial, sans-serif; color: #333;'>
                <h2>Confirmación de Pago</h2>
                <p>Estimado(a) <strong>{$prov['nom_proveedor']}</strong>,</p>
                <p>Se ha registrado un pago de <strong>S/ " . number_format($monto, 2) . "</strong> 
                correspondiente a su Orden de Compra <strong>#{$id_compra}</strong>.</p>
                <p><strong>Saldo pendiente:</strong> S/ " . number_format($nuevoSaldo, 2) . "</p>
                <p>Saludos cordiales,<br>Equipo de Compras ARCEPERU</p>
            </body>
            </html>";

            // Cabeceras del correo
            $cabeceras  = "MIME-Version: 1.0\r\n";
            $cabeceras .= "Content-type: text/html; charset=UTF-8\r\n";
            $cabeceras .= "From: ARCEPERU <karengarcia9699@gmail.com>\r\n";
            $cabeceras .= "Bcc: karengarcia9699@gmail.com\r\n";
            $cabeceras .= "X-Mailer: PHP/" . phpversion() . "\r\n";

            // ---------------------------------------
            // 🚀 Enviar correo
            // ---------------------------------------
            $ok = @mail($para, $asunto, $mensaje, $cabeceras);

            // ---------------------------------------
            // 🧾 Log de resultado
            // ---------------------------------------
            $log_msg = $ok 
                ? "✅ MAIL OK → enviado a $para (OC $id_compra)" 
                : "❌ MAIL FAIL → error al enviar a $para (OC $id_compra)";
            error_log($log_msg);

        } else {
            error_log("⚠️ MAIL SKIP → proveedor sin correo electrónico (OC $id_compra)");
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
                   per.nom_personal
            FROM pago p
            LEFT JOIN proveedor_cuenta pc ON p.id_proveedor_cuenta = pc.id_proveedor_cuenta
            LEFT JOIN {$bd_complemento}.personal per ON p.id_personal = per.id_personal
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

    // VERIFICAR SI LA COMPRA EXISTE PRIMERO
    $sql_check = "SELECT id_compra FROM compra WHERE id_compra = $id_compra";
    $res_check = mysqli_query($con, $sql_check);
    
    if (!$res_check || mysqli_num_rows($res_check) == 0) {
        mysqli_close($con);
        return false;
    }

    //  CONSULTA PRINCIPAL CON JOINS
    $sql = "SELECT c.*, 
                   p.nom_proveedor, 
                   p.ruc_proveedor,
                   CASE 
                       WHEN m.id_moneda = 1 THEN 'S/.'
                       WHEN m.id_moneda = 2 THEN 'US$'
                       ELSE 'S/.'
                   END as sim_moneda,
                   d.nombre_detraccion, 
                   d.porcentaje as porcentaje_detraccion,
                   r.nombre_detraccion as nombre_retencion, 
                   r.porcentaje as porcentaje_retencion,
                   per.nombre_detraccion as nombre_percepcion, 
                   per.porcentaje as porcentaje_percepcion
            FROM compra c
            INNER JOIN proveedor p ON c.id_proveedor = p.id_proveedor
            LEFT JOIN moneda m ON c.id_moneda = m.id_moneda
            LEFT JOIN detraccion d ON c.id_detraccion = d.id_detraccion
            LEFT JOIN detraccion r ON c.id_retencion = r.id_detraccion
            LEFT JOIN detraccion per ON c.id_percepcion = per.id_detraccion
            WHERE c.id_compra = $id_compra";
    
    $res = mysqli_query($con, $sql);

    if (!$res) {
        mysqli_close($con);
        return false;
    }

    if (mysqli_num_rows($res) == 0) {
        mysqli_close($con);
        return false;
    }

    $compra = mysqli_fetch_assoc($res);

    //  CALCULAR SUBTOTAL E IGV
    $sql_detalle = "SELECT 
                        COALESCE(SUM(cd.cant_compra_detalle * cd.prec_compra_detalle), 0) as subtotal,
                        COALESCE(SUM((cd.cant_compra_detalle * cd.prec_compra_detalle) * (cd.igv_compra_detalle / 100)), 0) as total_igv
                    FROM compra_detalle cd
                    WHERE cd.id_compra = $id_compra
                    AND cd.est_compra_detalle = 1";
    
    $res_detalle = mysqli_query($con, $sql_detalle);
    
    if (!$res_detalle) {
        error_log("❌ ERROR SQL detalle: " . mysqli_error($con));
        mysqli_close($con);
        return false;
    }
    
    $fila_detalle = mysqli_fetch_assoc($res_detalle);
    
    $subtotal = floatval($fila_detalle['subtotal']);
    $total_igv = floatval($fila_detalle['total_igv']);
    $total_con_igv = $subtotal + $total_igv;

    // 🔹 CALCULAR AFECTACIONES
    $monto_detraccion = 0;
    $monto_retencion = 0;
    $monto_percepcion = 0;
    
    if (!empty($compra['porcentaje_detraccion'])) {
        $monto_detraccion = ($total_con_igv * floatval($compra['porcentaje_detraccion'])) / 100;
    }
    
    if (!empty($compra['porcentaje_retencion'])) {
        $monto_retencion = ($total_con_igv * floatval($compra['porcentaje_retencion'])) / 100;
    }
    
    if (!empty($compra['porcentaje_percepcion'])) {
        $monto_percepcion = ($total_con_igv * floatval($compra['porcentaje_percepcion'])) / 100;
    }

    // 🔹 TOTAL A PAGAR
    $monto_total = $total_con_igv;
    
    if ($monto_detraccion > 0) {
        $monto_total -= $monto_detraccion;
    }
    if ($monto_retencion > 0) {
        $monto_total -= $monto_retencion;
    }
    if ($monto_percepcion > 0) {
        $monto_total += $monto_percepcion;
    }

    //  CALCULAR MONTO PAGADO
    $sql_pagado = "SELECT COALESCE(SUM(monto), 0) as pagado
                   FROM pago
                   WHERE id_compra = $id_compra
                     AND est_pago = 1";
    $res_pagado = mysqli_query($con, $sql_pagado);
    $fila_pagado = mysqli_fetch_assoc($res_pagado);
    $monto_pagado = floatval($fila_pagado['pagado']);

    // CALCULAR SALDO
    $saldo = $total_con_igv - $monto_pagado;

    //  ARMAR ARRAY COMPLETO
    $compra['subtotal'] = round($subtotal, 2);
    $compra['total_igv'] = round($total_igv, 2);
    $compra['total_con_igv'] = round($total_con_igv, 2);
    $compra['monto_detraccion'] = round($monto_detraccion, 2);
    $compra['monto_retencion'] = round($monto_retencion, 2);
    $compra['monto_percepcion'] = round($monto_percepcion, 2);
    $compra['monto_total'] = round($monto_total, 2);
    $compra['monto_pagado'] = round($monto_pagado, 2);
    $compra['saldo'] = round($saldo, 2);

    mysqli_close($con);
        
    return $compra;
}

// Anular pago
function AnularPago($id_pago)
{
    include("../_conexion/conexion.php");

    $id_pago = intval($id_pago);

    if ($id_pago <= 0) {
        mysqli_close($con);
        return ['success' => false, 'message' => 'Pago inválido.'];
    }

    // Iniciar transacción
    mysqli_begin_transaction($con);

    try {
        // 1) Obtener pago y verificar
        $sql = "SELECT p.*, c.id_compra, c.est_compra
                FROM pago p
                LEFT JOIN compra c ON p.id_compra = c.id_compra
                WHERE p.id_pago = $id_pago
                FOR UPDATE";
        $res = mysqli_query($con, $sql);
        if (!$res || mysqli_num_rows($res) == 0) {
            mysqli_rollback($con);
            mysqli_close($con);
            return ['success' => false, 'message' => 'Pago no encontrado.'];
        }
        $pago = mysqli_fetch_assoc($res);

        if (intval($pago['est_pago']) === 0) {
            mysqli_rollback($con);
            mysqli_close($con);
            return ['success' => false, 'message' => 'El pago ya está anulado.'];
        }

        $id_compra = intval($pago['id_compra']);
        if ($id_compra <= 0) {
            mysqli_rollback($con);
            mysqli_close($con);
            return ['success' => false, 'message' => 'Pago sin compra asociada.'];
        }

        // 2) Anular el pago (marcar est_pago = 0)
        $sql_upd = "UPDATE pago SET est_pago = 0 WHERE id_pago = $id_pago";
        if (!mysqli_query($con, $sql_upd)) {
            $err = mysqli_error($con);
            mysqli_rollback($con);
            mysqli_close($con);
            return ['success' => false, 'message' => "Error al anular pago: $err"];
        }

        // 3) RECALCULAR MONTO TOTAL CORRECTAMENTE (con detracciones/retenciones/percepciones)
        // 3.1) Obtener subtotal e IGV
        $sql_detalle = "SELECT 
                            COALESCE(SUM(cd.cant_compra_detalle * cd.prec_compra_detalle), 0) as subtotal,
                            COALESCE(SUM((cd.cant_compra_detalle * cd.prec_compra_detalle) * (cd.igv_compra_detalle / 100)), 0) as total_igv
                        FROM compra_detalle cd
                        WHERE cd.id_compra = $id_compra
                        AND cd.est_compra_detalle = 1";
        
        $res_detalle = mysqli_query($con, $sql_detalle);
        $fila_detalle = mysqli_fetch_assoc($res_detalle);
        
        $subtotal = floatval($fila_detalle['subtotal']);
        $total_igv = floatval($fila_detalle['total_igv']);
        $total_con_igv = $subtotal + $total_igv;
        
        // 3.2) Obtener detracciones/retenciones/percepciones
        $sql_afectaciones = "SELECT 
                                d.porcentaje as porcentaje_detraccion,
                                r.porcentaje as porcentaje_retencion,
                                per.porcentaje as porcentaje_percepcion
                             FROM compra c
                             LEFT JOIN detraccion d ON c.id_detraccion = d.id_detraccion
                             LEFT JOIN detraccion r ON c.id_retencion = r.id_detraccion
                             LEFT JOIN detraccion per ON c.id_percepcion = per.id_detraccion
                             WHERE c.id_compra = $id_compra";
        
        $res_afect = mysqli_query($con, $sql_afectaciones);
        $afect = mysqli_fetch_assoc($res_afect);
        
        // 3.3) Calcular montos de afectaciones
        $monto_detraccion = 0;
        $monto_retencion = 0;
        $monto_percepcion = 0;
        
        if (!empty($afect['porcentaje_detraccion'])) {
            $monto_detraccion = ($total_con_igv * floatval($afect['porcentaje_detraccion'])) / 100;
        }
        
        if (!empty($afect['porcentaje_retencion'])) {
            $monto_retencion = ($total_con_igv * floatval($afect['porcentaje_retencion'])) / 100;
        }
        
        if (!empty($afect['porcentaje_percepcion'])) {
            $monto_percepcion = ($total_con_igv * floatval($afect['porcentaje_percepcion'])) / 100;
        }
        
        // 3.4) Calcular TOTAL A PAGAR (igual que en ConsultarCompraPago)
        $monto_total = $total_con_igv;
        
        if ($monto_detraccion > 0) {
            $monto_total -= $monto_detraccion;
        }
        if ($monto_retencion > 0) {
            $monto_total -= $monto_retencion;
        }
        if ($monto_percepcion > 0) {
            $monto_total += $monto_percepcion;
        }
        
        // 4) Recalcular monto pagado válido (solo est_pago = 1)
        $sql_pagado = "SELECT COALESCE(SUM(monto), 0) AS pagado
                       FROM pago
                       WHERE id_compra = $id_compra
                         AND est_pago = 1";
        $res_pagado = mysqli_query($con, $sql_pagado);
        $fila_pagado = mysqli_fetch_assoc($res_pagado);
        $monto_pagado = floatval($fila_pagado['pagado']);

        // 5) Calcular nuevo saldo - AHORA SOBRE total_con_igv
        $nuevoSaldo = round($total_con_igv - $monto_pagado, 2);

        // 6) Si la compra estaba marcada como PAGADA (4) y ahora queda saldo, revertir a APROBADA (2)
        if (intval($pago['est_compra']) === 4 && $nuevoSaldo > 0) {
            $sql_compra = "UPDATE compra SET est_compra = 2 WHERE id_compra = $id_compra";
            if (!mysqli_query($con, $sql_compra)) {
                $err = mysqli_error($con);
                mysqli_rollback($con);
                mysqli_close($con);
                return ['success' => false, 'message' => "Error al actualizar estado de compra: $err"];
            }
        }

        mysqli_commit($con);
        mysqli_close($con);
        
        return [
            'success' => true,
            'message' => 'Pago anulado correctamente.',
            'id_compra' => $id_compra,
            'nuevo_saldo' => $nuevoSaldo
        ];

    } catch (Exception $e) {
        mysqli_rollback($con);
        mysqli_close($con);
        return ['success' => false, 'message' => 'Excepción: ' . $e->getMessage()];
    }
}