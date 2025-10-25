<?php
//-----------------------------------------------------------------------
// Insertar nuevo pago de una compra
//-----------------------------------------------------------------------
function GrabarPago($id_compra, $id_proveedor_cuenta, $monto, $comprobante, $id_personal, $enviarCorreo = 0, $enviarCorreo2 = 0, $enviarCorreo3 = 0) 
{
    include("../_conexion/conexion.php");

    $id_compra = intval($id_compra);
    $id_personal = intval($id_personal);
    $id_proveedor_cuenta = intval($id_proveedor_cuenta);
    $monto = round(floatval($monto), 2);
    $enviarCorreo = intval($enviarCorreo);
    $enviarCorreo2 = intval($enviarCorreo2);
    $enviarCorreo3 = intval($enviarCorreo3);

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
    //$comprobante = mysqli_real_escape_string($con, $comprobante);
    //$comprobante = trim($comprobante);

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
    // Enviar correo de confirmación (si aplica)
    // ================================================================
    if ($enviarCorreo || $enviarCorreo2 || $enviarCorreo3) {

        require_once("m_proveedor.php");
        $prov = ObtenerProveedor($compra['id_proveedor']);

        // Base de destinatarios
        $destinatarios = [];

        // 1️⃣ Enviar al proveedor
        if ($enviarCorreo && $prov && !empty($prov['mail_proveedor'])) {
            $destinatarios[] = trim($prov['mail_proveedor']);
        }

        // 2️⃣ Enviar a contabilidad
        if ($enviarCorreo2) {
            $destinatarios[] = "contabilidad@arceperu.pe";
        }

        // 3️⃣ Enviar a tesorería
        if ($enviarCorreo3) {
            $destinatarios[] = "tesoreria@arceperu.pe";
        }

        // Solo continuar si hay al menos un destinatario
        if (count($destinatarios) > 0) {

            // Unir todos los correos con coma
            $para = implode(", ", $destinatarios);
            $asunto = "Confirmación de Pago - Orden de Compra C00$id_compra";
            $url_comprobante = "https://montajeseingenieriaarceperusac.pe/almacen/" . $comprobante;

             $mensaje = "
                    <html>
                        <head>
                            <meta charset='UTF-8'>
                            <style>
                                body {
                                font-family: 'Segoe UI', Arial, sans-serif;
                                background-color: #f4f6f8;
                                color: #333;
                                margin: 0;
                                padding: 0;
                                }
                                .container {
                                max-width: 600px;
                                margin: 40px auto;
                                background-color: #ffffff;
                                border-radius: 10px;
                                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                                overflow: hidden;
                                }
                                .header {
                                background-color: #f57c00;
                                color: #fff;
                                text-align: center;
                                padding: 20px 10px;
                                }
                                .header h2 {
                                margin: 0;
                                font-size: 22px;
                                letter-spacing: 0.5px;
                                }
                                .content {
                                padding: 30px 40px;
                                line-height: 1.6;
                                }
                                .content p {
                                margin-bottom: 15px;
                                }
                                .details {
                                background-color: #f1f5ff;
                                border-left: 4px solid #f57c00;
                                padding: 15px 20px;
                                border-radius: 6px;
                                margin: 20px 0;
                                }
                                .details strong {
                                display: inline-block;
                                min-width: 160px;
                                }
                                .highlight {
                                color: #f57c00;
                                font-weight: bold;
                                }

                                .button {
                                display: inline-block;
                                background-color: #f57c00;
                                color: #fff !important;
                                text-decoration: none;
                                padding: 12px 25px;
                                border-radius: 6px;
                                font-weight: bold;
                                margin-top: 10px;
                                transition: background-color 0.2s ease-in-out;
                                }

                                .button:hover {
                                background-color: #e46a00;
                                }
                                .footer {
                                background-color: #fafafa;
                                text-align: center;
                                font-size: 13px;
                                color: #777;
                                padding: 15px 10px;
                                border-top: 1px solid #eee;
                                }
                            </style>
                        </head>
                        <body>
                            <div class='container'>
                                <div class='header'>
                                <h2>Confirmación de Pago</h2>
                                </div>
                                <div class='content'>
                                <p>Estimado(a) <strong>{$prov['nom_proveedor']}</strong>,</p>
                                <p>
                                    Le informamos que se ha registrado correctamente un pago correspondiente a su 
                                    <strong>Orden de Compra C00{$id_compra}</strong>.
                                </p>

                                <div class='details'>
                                    <p><strong>Monto pagado:</strong> S/ " . number_format($monto, 2) . "</p>
                                    <p><strong>Saldo pendiente:</strong> S/ " . number_format($nuevoSaldo, 2) . "</p>
                                </div>

                                <p>
                                    Puede revisar o descargar el comprobante de pago en el siguiente enlace:
                                </p>

                                <p style='text-align: center;'>
                                    <a href='{$url_comprobante}' class='button' target='_blank'>Ver Comprobante de Pago</a>
                                </p>

                                <p>
                                    Agradecemos su colaboración y atención. Si requiere mayor información o tiene consultas
                                    sobre este pago, no dude en comunicarse con nuestro equipo de compras.
                                </p>

                                <p>Atentamente,<br>
                                <strong>Equipo de Compras ARCE PERÚ</strong><br>
                                <small>notificaciones@montajeseingenieriaarceperusac.pe</small></p>
                                </div>
                                <div class='footer'>
                                © " . date('Y') . " ARCE PERÚ — Todos los derechos reservados
                                </div>
                            </div>
                        </body>
                    </html>";

            // Cabeceras del correo
            $cabeceras  = "MIME-Version: 1.0\r\n";
            $cabeceras .= "Content-type: text/html; charset=UTF-8\r\n";
            $cabeceras .= "From: ARCE PERÚ <notificaciones@montajeseingenieriaarceperusac.pe>\r\n";
            $cabeceras .= "Bcc: notificaciones@montajeseingenieriaarceperusac.pe\r\n";
            $cabeceras .= "X-Mailer: PHP/" . phpversion() . "\r\n";

            // 🚀 Enviar correo
            $ok = @mail($para, $asunto, $mensaje, $cabeceras);

            // 🧾 Log de resultado
            $log_msg = $ok 
                ? "✅ MAIL OK → enviado a [$para] (OC C00$id_compra)" 
                : "❌ MAIL FAIL → error al enviar a [$para] (OC C00$id_compra)";
            error_log($log_msg);
        } else {
            error_log("⚠️ MAIL SKIP → sin destinatarios válidos (OC C00$id_compra)");
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