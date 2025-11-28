<?php

//-----------------------------------------------------------------------
// Registrar nuevo comprobante
//-----------------------------------------------------------------------
function GrabarComprobante($datos) 
{
    include("../_conexion/conexion.php");

    // Validar y sanitizar datos
    $id_compra = intval($datos['id_compra']);
    $id_tipo_documento = intval($datos['id_tipo_documento']);
    $serie = mysqli_real_escape_string($con, trim($datos['serie']));
    $numero = mysqli_real_escape_string($con, trim($datos['numero']));
    $monto_total_igv = round(floatval($datos['monto_total_igv']), 2);
    $id_detraccion = !empty($datos['id_detraccion']) ? intval($datos['id_detraccion']) : 'NULL';
    $total_pagar = round(floatval($datos['total_pagar']), 2);
    $id_moneda = intval($datos['id_moneda']);
    $id_medio_pago = !empty($datos['id_medio_pago']) ? intval($datos['id_medio_pago']) : 'NULL';
    $id_cuenta_proveedor = !empty($datos['id_cuenta_proveedor']) ? intval($datos['id_cuenta_proveedor']) : 'NULL';
    $archivo_pdf = !empty($datos['archivo_pdf']) ? "'" . mysqli_real_escape_string($con, $datos['archivo_pdf']) . "'" : 'NULL';
    $archivo_xml = !empty($datos['archivo_xml']) ? "'" . mysqli_real_escape_string($con, $datos['archivo_xml']) . "'" : 'NULL';
    $fec_pago = !empty($datos['fec_pago']) ? "'" . mysqli_real_escape_string($con, $datos['fec_pago']) . "'" : 'NULL';
    $id_personal = intval($datos['id_personal']);
    $est_comprobante = isset($datos['est_comprobante']) ? intval($datos['est_comprobante']) : 1;

    // Validar que la compra existe
    $sql_check = "SELECT id_compra FROM compra WHERE id_compra = $id_compra";
    $res_check = mysqli_query($con, $sql_check);
    
    if (!$res_check || mysqli_num_rows($res_check) == 0) {
        mysqli_close($con);
        return "Compra no encontrada.";
    }

    $sql_prov = "SELECT id_proveedor FROM compra WHERE id_compra = $id_compra";
    $res_prov = mysqli_query($con, $sql_prov);
    $row_prov = mysqli_fetch_assoc($res_prov);
    $id_proveedor = intval($row_prov['id_proveedor']);

    // Validar que no exista el mismo comprobante (serie-número-proveedor)
    $sql_existe = "SELECT c.id_comprobante
               FROM comprobante c
               INNER JOIN compra co ON co.id_compra = c.id_compra
               WHERE c.serie = '$serie'
               AND c.numero = '$numero'
               AND c.id_tipo_documento = $id_tipo_documento
               AND co.id_proveedor = $id_proveedor
               AND c.est_comprobante = 1";
    
    $res_existe = mysqli_query($con, $sql_existe);

    if (!$res_existe) {
        $error = mysqli_error($con);
        mysqli_close($con);
        return "Error al validar comprobante duplicado: $error";
    }

    if (mysqli_num_rows($res_existe) > 0) {
        mysqli_close($con);
        return "Ya existe un comprobante con esa serie y número para este proveedor.";
    }

    // Insertar comprobante (SIN voucher_pago)
    $sql = "INSERT INTO comprobante (
                id_compra,
                id_tipo_documento,
                serie,
                numero,
                monto_total_igv,
                id_detraccion,
                total_pagar,
                id_moneda,
                id_medio_pago,
                archivo_pdf,
                archivo_xml,
                est_comprobante,
                fec_registro,
                fec_pago,
                id_personal,
                id_cuenta_proveedor
            ) VALUES (
                $id_compra,
                $id_tipo_documento,
                '$serie',
                '$numero',
                $monto_total_igv,
                $id_detraccion,
                $total_pagar,
                $id_moneda,
                $id_medio_pago,
                $archivo_pdf,
                $archivo_xml,
                $est_comprobante,
                NOW(),
                $fec_pago,
                $id_personal,
                $id_cuenta_proveedor
            )";
    
    $res = mysqli_query($con, $sql);

    if (!$res) {
        $error = mysqli_error($con);
        mysqli_close($con);
        return "Error al registrar el comprobante: $error";
    }

    $id_comprobante = mysqli_insert_id($con);
    mysqli_close($con);
    
    return "SI|$id_comprobante";
}

//-----------------------------------------------------------------------
// Subir voucher de pago a un comprobante existente
//-----------------------------------------------------------------------
function SubirVoucherComprobante($id_comprobante, $archivo_voucher, $id_personal_voucher, $enviar_proveedor = false, $enviar_contabilidad = false, $enviar_tesoreria = false, $enviar_compras = false, $fec_voucher)
{
    include("../_conexion/conexion.php");

    // 🔍 DEBUG
    error_log("========== FUNCIÓN SubirVoucherComprobante ==========");
    error_log("id_comprobante recibido: $id_comprobante (tipo: " . gettype($id_comprobante) . ")");
    
    $id_comprobante = intval($id_comprobante);
    $id_personal_voucher = intval($id_personal_voucher);
    
    error_log("id_comprobante después de intval: $id_comprobante");

    if ($id_comprobante <= 0) {
        error_log("❌ ERROR: id_comprobante es <= 0");
        mysqli_close($con);
        return "ID de comprobante inválido.";
    }

    // Verificar que el comprobante existe y está activo
    $sql_check = "SELECT c.*, 
                         CONCAT(c.serie, '-', c.numero) as num_comprobante,
                         co.id_compra,
                         co.id_proveedor, 
                         p.nom_proveedor, 
                         p.ruc_proveedor, 
                         p.mail_proveedor,
                         m.nom_moneda,
                         ped.id_personal as id_personal_pedido,
                         pers.email_personal as email_personal_pedido
                  FROM comprobante c
                  INNER JOIN compra co ON c.id_compra = co.id_compra
                  INNER JOIN proveedor p ON co.id_proveedor = p.id_proveedor
                  INNER JOIN moneda m ON c.id_moneda = m.id_moneda
                  LEFT JOIN pedido ped ON co.id_pedido = ped.id_pedido
                  LEFT JOIN {$bd_complemento}.personal pers ON ped.id_personal = pers.id_personal
                  WHERE c.id_comprobante = $id_comprobante AND c.est_comprobante = 2";
    
    // 🔍 DEBUG
    error_log("SQL ejecutado: $sql_check");
    
    $res_check = mysqli_query($con, $sql_check);
    
    // 🔍 DEBUG
    if (!$res_check) {
        error_log("❌ ERROR EN QUERY: " . mysqli_error($con));
    } else {
        error_log("✅ Query ejecutado OK. Filas encontradas: " . mysqli_num_rows($res_check));
    }
    
    if (!$res_check || mysqli_num_rows($res_check) == 0) {
        error_log("❌ Comprobante no encontrado o está anulado");
        mysqli_close($con);
        return "Comprobante no encontrado o está anulado.";
    }

    $comprobante = mysqli_fetch_assoc($res_check);

    
    // -----------------------------
    // DECIDIR TIPO DE PAGO (1 o 2)
    // -----------------------------
    // $comprobante ya fue obtenido con mysqli_fetch_assoc($res_check)

    $tiene_detraccion = false;
    if (isset($comprobante['id_detraccion']) && !empty($comprobante['id_detraccion'])) {
        $id_detraccion_valor = intval($comprobante['id_detraccion']);
        // Solo es detracción si tiene valor Y no es 13 (percepción)
        $tiene_detraccion = ($id_detraccion_valor > 0 && $id_detraccion_valor != 13);
    }

    // Obtener conteo de pagos activos existentes por tipo (1 = monto, 2 = impuesto)
    $sql_pagos = "
        SELECT 
            COALESCE(SUM(CASE WHEN fg_comprobante_pago = 1 AND est_comprobante_pago = 1 THEN 1 ELSE 0 END),0) AS cnt_monto,
            COALESCE(SUM(CASE WHEN fg_comprobante_pago = 2 AND est_comprobante_pago = 1 THEN 1 ELSE 0 END),0) AS cnt_impuesto
        FROM comprobante_pago
        WHERE id_comprobante = $id_comprobante
    ";
    $res_pagos = mysqli_query($con, $sql_pagos);
    if (!$res_pagos) {
        error_log("❌ ERROR al consultar comprobante_pago: " . mysqli_error($con));
        mysqli_close($con);
        return "Error al verificar pagos existentes.";
    }
    $row_pagos = mysqli_fetch_assoc($res_pagos);
    $cnt_monto = intval($row_pagos['cnt_monto']);
    $cnt_impuesto = intval($row_pagos['cnt_impuesto']);

    // Decidir qué tipo de pago corresponde ahora
    $fg_comprobante_pago = null;

    if ($tiene_detraccion) {
        // Caso: comprobante con detracción -> puede haber hasta 2 pagos (monto + impuesto)
        if ($cnt_monto == 0) {
            // aún no hay pago al proveedor -> este voucher debe ser el pago principal
            $fg_comprobante_pago = 1;
        } elseif ($cnt_monto >= 1 && $cnt_impuesto == 0) {
            // ya hay pago al proveedor, falta la detracción -> ahora es pago impuesto
            $fg_comprobante_pago = 2;
        } else {
            // ya existen ambos pagos activos -> no permitir más
            error_log("⚠️ Comprobante $id_comprobante ya tiene ambos pagos registrados (monto e impuesto).");
            mysqli_close($con);
            return "El comprobante ya tiene registrados los pagos permitidos.";
        }
    } else {
        // Caso: sin detracción -> solo permitido 1 pago (monto)
        if ($cnt_monto == 0) {
            $fg_comprobante_pago = 1;
        } else {
            // ya existe pago monto para comprobante sin detracción -> no permitir otro
            error_log("⚠️ Comprobante $id_comprobante ya tiene pago registrado y no aplica detracción.");
            mysqli_close($con);
            return "El comprobante ya tiene el pago registrado.";
        }
    }

    // A estas alturas $fg_comprobante_pago será 1 o 2
    error_log("Decisión: fg_comprobante_pago = $fg_comprobante_pago (cnt_monto=$cnt_monto, cnt_impuesto=$cnt_impuesto, has_detraccion=" . ($tiene_detraccion?1:0) . ")");

    
    
    // Extraer id_compra del array asociativo
    $id_compra = intval($comprobante['id_compra']);

    // Validar que se proporcionó un archivo
    if (empty($archivo_voucher)) {
        mysqli_close($con);
        return "Debe proporcionar un archivo de voucher.";
    }

    $archivo_voucher_escaped = mysqli_real_escape_string($con, $archivo_voucher);

    // -----------------------------
    // INSERTAR EN comprobante_pago
    // -----------------------------
    $fec_pago_sql = $fec_voucher ? "'$fec_voucher'" : "NULL";

    $sql = "
        INSERT INTO comprobante_pago(
            id_comprobante,
            id_personal_registra,
            fec_pago,
            vou_comprobante_pago,
            fg_comprobante_pago,
            est_comprobante_pago
        ) VALUES (
            $id_comprobante,
            $id_personal_voucher,
            $fec_pago_sql,
            '$archivo_voucher_escaped',
            $fg_comprobante_pago,
            1
        )
    ";
    
    $res = mysqli_query($con, $sql);

    if (!$res) {
        $error = mysqli_error($con);
        mysqli_close($con);
        return "Error al subir el voucher: $error";
    }

    // ================================================
    // 🔥 ACTUALIZAR ESTADO DEL COMPROBANTE (est_comprobante = 3)
    // ================================================

    // Tiene detracción y se acaba de registrar el pago 2 (impuesto)
    if ($tiene_detraccion && $fg_comprobante_pago == 2) {

        $sql_upd = "UPDATE comprobante SET est_comprobante = 3 WHERE id_comprobante = $id_comprobante";
        mysqli_query($con, $sql_upd);

        error_log("🔵 Comprobante $id_comprobante actualizado a estado 3 (pago final con detracción).");
    }

    // Sin detracción o percepción → pago único fg = 1
    if (!$tiene_detraccion && $fg_comprobante_pago == 1) {

        $sql_upd = "UPDATE comprobante SET est_comprobante = 3 WHERE id_comprobante = $id_comprobante";
        mysqli_query($con, $sql_upd);

        error_log("🟢 Comprobante $id_comprobante actualizado a estado 3 (pago único sin detracción).");
    }
    

    // ================================================================
    // CALCULAR TOTAL REAL DE LA COMPRA DESDE DETALLE
    // ================================================================
    $sql_total_compra = "
                        SELECT 
                            COALESCE(
                                SUM(
                                    cd.cant_compra_detalle 
                                    * cd.prec_compra_detalle 
                                    * (1 + (cd.igv_compra_detalle / 100))
                                ), 
                            0) AS total_compra
                        FROM compra_detalle cd
                        WHERE cd.id_compra = $id_compra
                        AND cd.est_compra_detalle = 1
                    ";
    
    $res_total_compra = mysqli_query($con, $sql_total_compra);
    $row_total_compra = mysqli_fetch_assoc($res_total_compra);
    $total_compra = floatval($row_total_compra['total_compra']);

    // ================================================================
    // CALCULAR TOTAL PAGADO (SUMA DE COMPROBANTES EN ESTADO 3)
    // ================================================================
    $sql_total_pagado = "SELECT COALESCE(SUM(monto_total_igv), 0) as total_pagado
                         FROM comprobante
                         WHERE id_compra = $id_compra
                         AND est_comprobante = 3";
    
    $res_pagado = mysqli_query($con, $sql_total_pagado);
    
    if ($res_pagado) {
        $row_pagado = mysqli_fetch_assoc($res_pagado);
        $total_pagado = floatval($row_pagado['total_pagado']);
        
        // Redondear para evitar problemas de precisión
        $total_pagado = round($total_pagado, 2);
        $total_compra = round($total_compra, 2);
        
        // Si el total pagado es igual o mayor al total de la compra
        if ($total_pagado >= $total_compra) {
            // Cambiar estado de la compra a 4 (PAGADO)
            /*$sql_update_compra = "UPDATE compra 
                                  SET est_compra = 4 
                                  WHERE id_compra = $id_compra";
            mysqli_query($con, $sql_update_compra);*/
            
            error_log("✅ COMPRA #$id_compra MARCADA COMO PAGADA → Total: $total_compra | Pagado: $total_pagado");
        } else {
            $saldo = $total_compra - $total_pagado;
            error_log("⚠️ COMPRA #$id_compra AÚN PENDIENTE → Total: $total_compra | Pagado: $total_pagado | Saldo: $saldo");
        }
    }

    mysqli_close($con);

    // ================================================================
    // Enviar correo de confirmación (si aplica)
    // ================================================================
    if ($enviar_proveedor || $enviar_contabilidad || $enviar_tesoreria || $enviar_compras) {

        // Base de destinatarios
        $destinatarios = [];

        // NUEVO: Siempre enviar al personal que registró el pedido
        if (!empty($comprobante['email_personal_pedido'])) {
            $destinatarios[] = trim($comprobante['email_personal_pedido']);
        }

        // 1️⃣ Enviar al proveedor
        if ($enviar_proveedor && !empty($comprobante['mail_proveedor'])) {
            $destinatarios[] = trim($comprobante['mail_proveedor']);
        }

        // 2️⃣ Enviar a contabilidad
        if ($enviar_contabilidad) {
            $destinatarios[] = "contabilidad@arceperu.pe";
        }

        // 3️⃣ Enviar a tesorería
        if ($enviar_tesoreria) {
            $destinatarios[] = "tesoreria@arceperu.pe";
        }

        // NUEVO: Enviar a compras
        if ($enviar_compras) {
            $destinatarios[] = "compras@arceperu.pe";
        }

        // Solo continuar si hay al menos un destinatario
        if (count($destinatarios) > 0) {

            // Unir todos los correos con coma
            $para = implode(", ", $destinatarios);
            $asunto = "Voucher de Pago - Comprobante {$comprobante['num_comprobante']}";
            
            // URL del voucher
            $url_voucher = "https://montajeseingenieriaarceperusac.pe/almacen/_upload/vouchers/" . $archivo_voucher;
            $simbolo = ($comprobante['id_comprobante'] == 1) ? 'S/' : 'U$';
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
                            background-color: #4CAF50;
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
                            border-left: 4px solid #4CAF50;
                            padding: 15px 20px;
                            border-radius: 6px;
                            margin: 20px 0;
                        }
                        .details strong {
                            display: inline-block;
                            min-width: 160px;
                        }
                        .highlight {
                            color: #4CAF50;
                            font-weight: bold;
                        }
                        .button {
                            display: inline-block;
                            background-color: #4CAF50;
                            color: #fff !important;
                            text-decoration: none;
                            padding: 12px 25px;
                            border-radius: 6px;
                            font-weight: bold;
                            margin-top: 10px;
                            transition: background-color 0.2s ease-in-out;
                        }
                        .button:hover {
                            background-color: #45a049;
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
                            <h2>Voucher de Pago</h2>
                        </div>
                        <div class='content'>
                            <p>Estimado(a) <strong>{$comprobante['nom_proveedor']}</strong>,</p>
                            <p>
                                Le informamos que se ha registrado el pago correspondiente al 
                                <strong>Comprobante {$comprobante['num_comprobante']}</strong>.
                            </p>

                            <div class='details'>
                                <p><strong>Comprobante:</strong> {$comprobante['num_comprobante']}</p>
                                <p><strong>RUC:</strong> {$comprobante['ruc_proveedor']}</p>
                                <p><strong>Monto pagado:</strong> $simbolo " . number_format($comprobante['total_pagar'], 2) . "</p>
                                <p><strong>Fecha de pago:</strong> " . date('d/m/Y', strtotime($comprobante['fec_pago'])) . "</p>
                            </div>

                            <p>
                                Puede revisar o descargar el voucher de pago en el siguiente enlace:
                            </p>

                            <p style='text-align: center;'>
                                <a href='{$url_voucher}' class='button' target='_blank'>Ver Voucher de Pago</a>
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
                ? "✅ MAIL OK → enviado a [$para] (Comprobante {$comprobante['num_comprobante']})" 
                : "❌ MAIL FAIL → error al enviar a [$para] (Comprobante {$comprobante['num_comprobante']})";
            error_log($log_msg);

            if ($ok) {
                return "SI|Voucher subido y correo enviado a: $para";
            } else {
                return "SI|Voucher subido pero hubo error al enviar correo";
            }
        } else {
            error_log("⚠️ MAIL SKIP → sin destinatarios válidos (Comprobante {$comprobante['num_comprobante']})");
            return "SI|Voucher subido (sin correos configurados)";
        }
    }

    return "SI|Voucher subido correctamente";
}

//-----------------------------------------------------------------------
// Editar comprobante existente
//-----------------------------------------------------------------------
function EditarComprobante($id_comprobante, $datos) 
{
    include("../_conexion/conexion.php");

    $id_comprobante = intval($id_comprobante);

    if ($id_comprobante <= 0) {
        mysqli_close($con);
        return "ID de comprobante inválido.";
    }

    // Verificar que el comprobante existe y no está anulado
    $sql_check = "SELECT est_comprobante FROM comprobante WHERE id_comprobante = $id_comprobante";
    $res_check = mysqli_query($con, $sql_check);
    
    if (!$res_check || mysqli_num_rows($res_check) == 0) {
        mysqli_close($con);
        return "Comprobante no encontrado.";
    }

    $row = mysqli_fetch_assoc($res_check);
    if (intval($row['est_comprobante']) === 0) {
        mysqli_close($con);
        return "No se puede editar un comprobante anulado.";
    }

    // Validar y sanitizar datos
    $id_compra = intval($datos['id_compra']);
    $id_tipo_documento = intval($datos['id_tipo_documento']);
    $serie = mysqli_real_escape_string($con, trim($datos['serie']));
    $numero = mysqli_real_escape_string($con, trim($datos['numero']));
    $monto_total_igv = round(floatval($datos['monto_total_igv']), 2);
    $id_detraccion = !empty($datos['id_detraccion']) ? intval($datos['id_detraccion']) : 'NULL';
    $total_pagar = round(floatval($datos['total_pagar']), 2);
    $id_moneda = intval($datos['id_moneda']);
    $id_medio_pago = !empty($datos['id_medio_pago']) ? intval($datos['id_medio_pago']) : 'NULL';
    $id_cuenta_proveedor = !empty($datos['id_cuenta_proveedor']) ? intval($datos['id_cuenta_proveedor']) : 'NULL';
    $archivo_pdf = !empty($datos['archivo_pdf']) ? "'" . mysqli_real_escape_string($con, $datos['archivo_pdf']) . "'" : 'NULL';
    $archivo_xml = !empty($datos['archivo_xml']) ? "'" . mysqli_real_escape_string($con, $datos['archivo_xml']) . "'" : 'NULL';
    $fec_pago = !empty($datos['fec_pago']) ? "'" . mysqli_real_escape_string($con, $datos['fec_pago']) . "'" : 'NULL';

    // Validar que no exista otro comprobante con la misma serie-número
    $sql_existe = "SELECT id_comprobante 
                   FROM comprobante 
                   WHERE serie = '$serie' 
                   AND numero = '$numero' 
                   AND id_tipo_documento = $id_tipo_documento
                   AND id_comprobante != $id_comprobante
                   AND est_comprobante = 1";
    $res_existe = mysqli_query($con, $sql_existe);
    
    if (mysqli_num_rows($res_existe) > 0) {
        mysqli_close($con);
        return "Ya existe otro comprobante con la serie y número ingresados.";
    }

    // Actualizar comprobante (SIN voucher_pago)
    $sql = "UPDATE comprobante SET
                id_compra = $id_compra,
                id_tipo_documento = $id_tipo_documento,
                serie = '$serie',
                numero = '$numero',
                monto_total_igv = $monto_total_igv,
                id_detraccion = $id_detraccion,
                total_pagar = $total_pagar,
                id_moneda = $id_moneda,
                id_medio_pago = $id_medio_pago,
                archivo_pdf = $archivo_pdf,
                archivo_xml = $archivo_xml,
                fec_pago = $fec_pago,
                id_cuenta_proveedor = $id_cuenta_proveedor
            WHERE id_comprobante = $id_comprobante";
    
    $res = mysqli_query($con, $sql);

    if (!$res) {
        $error = mysqli_error($con);
        mysqli_close($con);
        return "Error al editar el comprobante: $error";
    }

    if (mysqli_affected_rows($con) == 0) {
        mysqli_close($con);
        return "No se realizaron cambios en el comprobante.";
    }

    mysqli_close($con);
    return "SI";
}

//-----------------------------------------------------------------------
// Anular comprobante
//-----------------------------------------------------------------------
function AnularComprobante($id_comprobante)
{
    include("../_conexion/conexion.php");

    $id_comprobante = intval($id_comprobante);

    if ($id_comprobante <= 0) {
        mysqli_close($con);
        return ['success' => false, 'message' => 'ID de comprobante inválido.'];
    }

    // Iniciar transacción
    mysqli_begin_transaction($con);

    try {
        // Verificar que el comprobante existe
        $sql = "SELECT c.*, co.id_compra, co.est_compra
                FROM comprobante c
                LEFT JOIN compra co ON c.id_compra = co.id_compra
                WHERE c.id_comprobante = $id_comprobante
                FOR UPDATE";
        
        $res = mysqli_query($con, $sql);
        
        if (!$res || mysqli_num_rows($res) == 0) {
            mysqli_rollback($con);
            mysqli_close($con);
            return ['success' => false, 'message' => 'Comprobante no encontrado.'];
        }

        $comprobante = mysqli_fetch_assoc($res);

        if (intval($comprobante['est_comprobante']) === 0) {
            mysqli_rollback($con);
            mysqli_close($con);
            return ['success' => false, 'message' => 'El comprobante ya está anulado.'];
        }

        // Anular el comprobante
        $sql_upd = "UPDATE comprobante SET est_comprobante = 0 WHERE id_comprobante = $id_comprobante";
        
        if (!mysqli_query($con, $sql_upd)) {
            $err = mysqli_error($con);
            mysqli_rollback($con);
            mysqli_close($con);
            return ['success' => false, 'message' => "Error al anular comprobante: $err"];
        }

        mysqli_commit($con);
        mysqli_close($con);
        
        return [
            'success' => true,
            'message' => 'Comprobante anulado correctamente.',
            'id_comprobante' => $id_comprobante
        ];

    } catch (Exception $e) {
        mysqli_rollback($con);
        mysqli_close($con);
        return ['success' => false, 'message' => 'Excepción: ' . $e->getMessage()];
    }
}

//-----------------------------------------------------------------------
// Consultar comprobante por ID
//-----------------------------------------------------------------------
function ConsultarComprobante($id_comprobante) 
{
    include("../_conexion/conexion.php");

    $id_comprobante = intval($id_comprobante);

    // LOG: Verificar que la variable existe
    error_log("BD_COMPLEMENTO: " . (isset($bd_complemento) ? $bd_complemento : "NO DEFINIDA"));

    $sql = "SELECT c.*,
                   CONCAT(c.serie, '-', c.numero) as num_comprobante,
                   td.nom_tipo_documento,
                   co.id_proveedor,
                   pc.nro_cuenta_corriente as nro_cuenta_proveedor,
                   co.fec_compra,
                   co.obs_compra,
                   p.nom_proveedor,
                   p.ruc_proveedor,
                   p.mail_proveedor,
                   m.nom_moneda,
                   CASE 
                       WHEN m.id_moneda = 1 THEN 'S/.'
                       WHEN m.id_moneda = 2 THEN 'US$'
                       ELSE 'S/.'
                   END as simbolo_moneda,
                   mp.nom_medio_pago,
                   d.nombre_detraccion,
                   d.porcentaje as porcentaje_detraccion,
                   per.nom_personal,
                   -- NUEVOS CAMPOS: Vouchers
                   (SELECT cp_prov.vou_comprobante_pago 
                       FROM comprobante_pago cp_prov 
                       WHERE cp_prov.id_comprobante = c.id_comprobante 
                       AND cp_prov.fg_comprobante_pago = 1 
                       AND cp_prov.est_comprobante_pago = 1
                       LIMIT 1) as voucher_proveedor,
                   
                   (SELECT cp_det.vou_comprobante_pago 
                       FROM comprobante_pago cp_det 
                       WHERE cp_det.id_comprobante = c.id_comprobante 
                       AND cp_det.fg_comprobante_pago = 2 
                       AND cp_det.est_comprobante_pago = 1
                       LIMIT 1) as voucher_detraccion,
                    CASE 
                       WHEN c.id_detraccion = 13 THEN 
                           -- PERCEPCIÓN: total_pagar es MAYOR (se suma)
                           ROUND(c.total_pagar - c.monto_total_igv, 2)
                       WHEN c.id_detraccion IS NOT NULL THEN 
                           -- DETRACCIÓN: monto_total_igv es MAYOR (se resta)
                           ROUND(c.monto_total_igv - c.total_pagar, 2)
                       ELSE 
                           0
                    END as monto_detraccion,
                   CASE 
                       WHEN c.est_comprobante = 1 THEN 'Activo'
                       WHEN c.est_comprobante = 0 THEN 'Anulado'
                       ELSE 'Desconocido'
                   END as estado_texto
            FROM comprobante c
            INNER JOIN tipo_documento td ON c.id_tipo_documento = td.id_tipo_documento
            INNER JOIN compra co ON c.id_compra = co.id_compra
            INNER JOIN proveedor p ON co.id_proveedor = p.id_proveedor
            LEFT JOIN proveedor_cuenta pc ON c.id_cuenta_proveedor = pc.id_proveedor_cuenta
            INNER JOIN moneda m ON c.id_moneda = m.id_moneda
            LEFT JOIN medio_pago mp ON c.id_medio_pago = mp.id_medio_pago
            LEFT JOIN detraccion d ON c.id_detraccion = d.id_detraccion
            LEFT JOIN {$bd_complemento}.personal per ON c.id_personal = per.id_personal
            WHERE c.id_comprobante = $id_comprobante";
    
    // ⭐ LOG: Guardar el SQL generado
    error_log("SQL: " . $sql);
    
    $res = mysqli_query($con, $sql);

    if (!$res) {
        $error = mysqli_error($con);
        // ⭐ LOG: Guardar el error
        error_log("ERROR SQL: " . $error);
        mysqli_close($con);
        return false;
    }

    $num_rows = mysqli_num_rows($res);
    // ⭐ LOG: Cantidad de filas
    error_log("Filas encontradas: " . $num_rows);

    if ($num_rows == 0) {
        mysqli_close($con);
        return false;
    }

    $comprobante = mysqli_fetch_assoc($res);
    
    // ⭐ LOG: Datos obtenidos
    error_log("Datos: " . json_encode($comprobante));
    
    mysqli_close($con);
    
    return $comprobante;
}

//-----------------------------------------------------------------------
// Listar comprobantes con filtros
//-----------------------------------------------------------------------
function ListarComprobantes($filtros = []) 
{
    include("../_conexion/conexion.php");

    $sql = "SELECT c.id_comprobante,
                   c.serie,
                   c.numero,
                   CONCAT(c.serie, '-', c.numero) as num_comprobante,
                   c.monto_total_igv,
                   c.total_pagar,
                   c.fec_registro,
                   c.fec_pago,
                   c.est_comprobante,
                   c.voucher_pago,
                   td.nom_tipo_documento,
                   p.nom_proveedor,
                   p.ruc_proveedor,
                   m.nom_moneda,
                   mp.nom_medio_pago,
                   co.id_compra,
                   CASE 
                       WHEN m.id_moneda = 1 THEN 'S/.'
                       WHEN m.id_moneda = 2 THEN 'US$'
                       ELSE 'S/.'
                   END as simbolo_moneda,
                   CASE 
                       WHEN c.est_comprobante = 1 THEN 'Activo'
                       WHEN c.est_comprobante = 0 THEN 'Anulado'
                       ELSE 'Desconocido'
                   END as estado_texto
            FROM comprobante c
            INNER JOIN tipo_documento td ON c.id_tipo_documento = td.id_tipo_documento
            INNER JOIN compra co ON c.id_compra = co.id_compra
            INNER JOIN proveedor p ON co.id_proveedor = p.id_proveedor
            INNER JOIN moneda m ON c.id_moneda = m.id_moneda
            LEFT JOIN medio_pago mp ON c.id_medio_pago = mp.id_medio_pago
            WHERE 1=1";

    // Filtro por proveedor
    if (!empty($filtros['id_proveedor'])) {
        $id_proveedor = intval($filtros['id_proveedor']);
        $sql .= " AND co.id_proveedor = $id_proveedor";
    }

    // Filtro por tipo de documento
    if (!empty($filtros['id_tipo_documento'])) {
        $id_tipo_documento = intval($filtros['id_tipo_documento']);
        $sql .= " AND c.id_tipo_documento = $id_tipo_documento";
    }

    // Filtro por estado
    if (isset($filtros['est_comprobante'])) {
        $est_comprobante = intval($filtros['est_comprobante']);
        $sql .= " AND c.est_comprobante = $est_comprobante";
    }

    // Filtro por compra
    if (!empty($filtros['id_compra'])) {
        $id_compra = intval($filtros['id_compra']);
        $sql .= " AND c.id_compra = $id_compra";
    }

    // Filtro por rango de fechas
    if (!empty($filtros['fecha_desde'])) {
        $fecha_desde = mysqli_real_escape_string($con, $filtros['fecha_desde']);
        $sql .= " AND DATE(c.fec_registro) >= '$fecha_desde'";
    }

    if (!empty($filtros['fecha_hasta'])) {
        $fecha_hasta = mysqli_real_escape_string($con, $filtros['fecha_hasta']);
        $sql .= " AND DATE(c.fec_registro) <= '$fecha_hasta'";
    }

    // Filtro por búsqueda (serie, número, proveedor)
    if (!empty($filtros['buscar'])) {
        $buscar = mysqli_real_escape_string($con, $filtros['buscar']);
        $sql .= " AND (c.serie LIKE '%$buscar%' 
                   OR c.numero LIKE '%$buscar%' 
                   OR p.nom_proveedor LIKE '%$buscar%'
                   OR p.ruc_proveedor LIKE '%$buscar%')";
    }

    $sql .= " ORDER BY c.fec_registro DESC";

    $res = mysqli_query($con, $sql);

    if (!$res) {
        mysqli_close($con);
        return [];
    }

    $comprobantes = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $comprobantes[] = $row;
    }

    mysqli_close($con);
    return $comprobantes;
}

//-----------------------------------------------------------------------
// Listar comprobantes de una compra específica
//-----------------------------------------------------------------------
function MostrarComprobantesCompra($id_compra) 
{
    include("../_conexion/conexion.php");
    
    $id_compra = intval($id_compra);

    $sql = "SELECT c.*,
                   CONCAT(c.serie, '-', c.numero) as num_comprobante,
                   td.nom_tipo_documento,
                   m.nom_moneda,
                   mp.nom_medio_pago,
                   per.nom_personal,
                   CASE 
                       WHEN c.id_detraccion = 13 THEN 
                           -- PERCEPCIÓN: total_pagar es MAYOR
                           ROUND(c.total_pagar - c.monto_total_igv, 2)
                       WHEN c.id_detraccion IS NOT NULL THEN 
                           -- DETRACCIÓN: monto_total_igv es MAYOR
                           ROUND(c.monto_total_igv - c.total_pagar, 2)
                       ELSE 
                           0
                   END as monto_detraccion,

                    -- Verificar si existe pago al proveedor (fg=1)
                    (SELECT COUNT(*) FROM comprobante_pago cp1 
                        WHERE cp1.id_comprobante = c.id_comprobante 
                        AND cp1.fg_comprobante_pago = 1 
                        AND cp1.est_comprobante_pago = 1) as tiene_pago_proveedor,
                    
                    -- Verificar si existe pago de detracción (fg=2)
                    (SELECT COUNT(*) FROM comprobante_pago cp2 
                        WHERE cp2.id_comprobante = c.id_comprobante 
                        AND cp2.fg_comprobante_pago = 2 
                        AND cp2.est_comprobante_pago = 1) as tiene_pago_detraccion,
                
                    -- Obtener voucher de pago al proveedor (fg=1)
                   (SELECT cp_prov.vou_comprobante_pago 
                       FROM comprobante_pago cp_prov 
                       WHERE cp_prov.id_comprobante = c.id_comprobante 
                       AND cp_prov.fg_comprobante_pago = 1 
                       AND cp_prov.est_comprobante_pago = 1
                       LIMIT 1) as voucher_proveedor,

                   -- Obtener voucher de pago de detracción (fg=2)
                   (SELECT cp_det.vou_comprobante_pago 
                       FROM comprobante_pago cp_det 
                       WHERE cp_det.id_comprobante = c.id_comprobante 
                       AND cp_det.fg_comprobante_pago = 2 
                       AND cp_det.est_comprobante_pago = 1
                       LIMIT 1) as voucher_detraccion,


                   CASE 
                       WHEN m.id_moneda = 1 THEN 'S/.'
                       WHEN m.id_moneda = 2 THEN 'US$'
                       ELSE 'S/.'
                   END as simbolo_moneda,
                   CASE 
                       WHEN c.est_comprobante = 1 THEN 'Activo'
                       WHEN c.est_comprobante = 0 THEN 'Anulado'
                       ELSE 'Desconocido'
                   END as estado_texto
            FROM comprobante c
            INNER JOIN tipo_documento td ON c.id_tipo_documento = td.id_tipo_documento
            INNER JOIN moneda m ON c.id_moneda = m.id_moneda
            LEFT JOIN medio_pago mp ON c.id_medio_pago = mp.id_medio_pago
            LEFT JOIN {$bd_complemento}.personal per ON c.id_personal = per.id_personal
            WHERE c.id_compra = $id_compra
            ORDER BY c.fec_registro DESC";
    
    $res = mysqli_query($con, $sql);

    $comprobantes = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $comprobantes[] = $row;
    }

    mysqli_close($con);
    return $comprobantes;
}

//-----------------------------------------------------------------------
// Verificar si existe comprobante con serie-número
//-----------------------------------------------------------------------
function ExisteComprobante($serie, $numero, $id_tipo_documento, $id_comprobante_excluir = 0) 
{
    include("../_conexion/conexion.php");

    $serie = mysqli_real_escape_string($con, trim($serie));
    $numero = mysqli_real_escape_string($con, trim($numero));
    $id_tipo_documento = intval($id_tipo_documento);
    $id_comprobante_excluir = intval($id_comprobante_excluir);

    $sql = "SELECT id_comprobante 
            FROM comprobante 
            WHERE serie = '$serie' 
            AND numero = '$numero' 
            AND id_tipo_documento = $id_tipo_documento
            AND est_comprobante = 1";
    
    if ($id_comprobante_excluir > 0) {
        $sql .= " AND id_comprobante != $id_comprobante_excluir";
    }

    $res = mysqli_query($con, $sql);
    $existe = mysqli_num_rows($res) > 0;

    mysqli_close($con);
    return $existe;
}

// ====================================================================
// FUNCIONES AUXILIARES PARA CATÁLOGOS
// ====================================================================
function ConsultarTiposDocumento() {
    include("../_conexion/conexion.php");
    $sql = "SELECT * FROM tipo_documento WHERE est_tipo_documento = 1 ORDER BY nom_tipo_documento";
    $res = mysqli_query($con, $sql);
    $datos = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $datos[] = $row;
    }
    mysqli_close($con);
    return $datos;
}

function ConsultarMonedas() {
    include("../_conexion/conexion.php");
    $sql = "SELECT * FROM moneda WHERE est_moneda = 1 ORDER BY nom_moneda";
    $res = mysqli_query($con, $sql);
    $datos = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $datos[] = $row;
    }
    mysqli_close($con);
    return $datos;
}

function ConsultarDetracciones() {
    include("../_conexion/conexion.php");
    $sql = "SELECT * FROM detraccion WHERE est_detraccion = 1 ORDER BY nombre_detraccion";
    $res = mysqli_query($con, $sql);
    $datos = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $datos[] = $row;
    }
    mysqli_close($con);
    return $datos;
}

function ConsultarMediosPago() {
    include("../_conexion/conexion.php");
    $sql = "SELECT * FROM medio_pago WHERE est_medio_pago = 1 ORDER BY nom_medio_pago";
    $res = mysqli_query($con, $sql);
    $datos = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $datos[] = $row;
    }
    mysqli_close($con);
    return $datos;
}

//-----------------------------------------------------------------------
// Consultar compra por ID (para gestión de comprobantes)
//-----------------------------------------------------------------------
function ConsultarCompraCom($id_compra)
{
    include("../_conexion/conexion.php");
    global $bd_complemento;

    $id_compra = intval($id_compra);

    // === 1. Datos generales de la compra ===
    $sql = "SELECT c.*,
                   p.nom_proveedor, 
                   p.ruc_proveedor,
                   m.nom_moneda,
                   CASE 
                       WHEN m.id_moneda = 1 THEN 'S/.'
                       WHEN m.id_moneda = 2 THEN 'US$'
                       ELSE 'S/.'
                   END AS simbolo_moneda,
                    COALESCE(d_det.nombre_detraccion, d_ret.nombre_detraccion, d_per.nombre_detraccion)
                        AS nombre_detraccion,
                    COALESCE(d_det.porcentaje, d_ret.porcentaje, d_per.porcentaje)
                        AS porcentaje_detraccion,
                   per.nom_personal AS nom_personal_crea,
                   per_fin.nom_personal AS nom_personal_aprueba_financiera,
                   pc.banco_proveedor,
                   pc.nro_cuenta_corriente
            FROM compra c
            INNER JOIN proveedor p ON c.id_proveedor = p.id_proveedor
            LEFT JOIN proveedor_cuenta pc ON pc.id_proveedor = p.id_proveedor
            INNER JOIN moneda m ON c.id_moneda = m.id_moneda
            LEFT JOIN detraccion d_det ON d_det.id_detraccion = c.id_detraccion
            LEFT JOIN detraccion d_ret ON d_ret.id_detraccion = c.id_retencion
            LEFT JOIN detraccion d_per ON d_per.id_detraccion = c.id_percepcion
            LEFT JOIN {$bd_complemento}.personal per ON per.id_personal = c.id_personal
            LEFT JOIN {$bd_complemento}.personal per_fin ON per_fin.id_personal = c.id_personal_aprueba_financiera
            WHERE c.id_compra = $id_compra";

    $res = mysqli_query($con, $sql);
    if (!$res) return false;

    $compra = mysqli_fetch_assoc($res);
    if (!$compra) return false;

    // === 2. Subtotal + IGV ===
    $sql_detalle = "
        SELECT 
            SUM(cd.cant_compra_detalle * cd.prec_compra_detalle) AS subtotal,
            SUM((cd.cant_compra_detalle * cd.prec_compra_detalle)*(cd.igv_compra_detalle/100)) AS total_igv
        FROM compra_detalle cd
        WHERE cd.id_compra = $id_compra
          AND cd.est_compra_detalle = 1
    ";

    $res_detalle = mysqli_query($con, $sql_detalle);
    $fila = mysqli_fetch_assoc($res_detalle);

    $subtotal = floatval($fila['subtotal']);
    $total_igv = floatval($fila['total_igv']);
    $total_con_igv = $subtotal + $total_igv;

    // === 3. Determinar AFECTACIONES SEGÚN id_detraccion ===
    $porcentaje = floatval($compra['porcentaje_detraccion']); // viene del SQL

    $tipo_afectacion = '';
    $id_afectacion = '';

    if (!empty($compra['id_detraccion'])) {
        $tipo_afectacion = 'detraccion';
        $id_afectacion = $compra['id_detraccion'];
    }
    elseif (!empty($compra['id_retencion'])) {
        $tipo_afectacion = 'retencion';
        $id_afectacion = $compra['id_retencion'];
    }
    elseif (!empty($compra['id_percepcion'])) {
        $tipo_afectacion = 'percepcion';
        $id_afectacion = $compra['id_percepcion'];
    }

    // Calcular monto en base al tipo
    $monto_afectacion = ($total_con_igv * $porcentaje) / 100;
    
    // === 4. TOTAL A PAGAR ===
    if ($tipo_afectacion === 'percepcion') {
        $monto_total = $total_con_igv + $monto_afectacion;
    }
    else {
        $monto_total = $total_con_igv - $monto_afectacion;
    }

    // ============================================================
    // 5. OBTENER TODOS LOS COMPROBANTES DE LA COMPRA Y SI TIENEN VOUCHER PAGADO (fg=1/fg=2)
    //    NOTA: comprobante_pago NO TIENE monto, inferimos lo pagado por comprobante
    // ============================================================
    /*$sql_pagado = "
        SELECT COALESCE(SUM(total_pagar),0) AS total_pagado
        FROM comprobante
        WHERE id_compra = $id_compra
          AND est_comprobante = 3
    ";*/
    $sql_comprobantes = "
        SELECT 
            c.id_comprobante,
            c.monto_total_igv AS monto_total_igv_comprobante,
            c.total_pagar AS total_pagar_comprobante,
            c.id_detraccion AS id_detraccion_comprobante,
            d.id_detraccion_tipo,
            MAX(CASE WHEN cp.fg_comprobante_pago = 1 AND cp.est_comprobante_pago = 1 THEN 1 ELSE 0 END) AS has_fg1_paid,
            MAX(CASE WHEN cp.fg_comprobante_pago = 2 AND cp.est_comprobante_pago = 1 THEN 1 ELSE 0 END) AS has_fg2_paid,
            COUNT(cp.id_comprobante_pago) AS total_vouchers
        FROM comprobante c
        LEFT JOIN comprobante_pago cp ON cp.id_comprobante = c.id_comprobante
        LEFT JOIN detraccion d ON d.id_detraccion = c.id_detraccion
        WHERE c.id_compra = $id_compra
        GROUP BY c.id_comprobante, c.monto_total_igv, c.total_pagar, c.id_detraccion, d.id_detraccion_tipo
    ";

    $res_comp = mysqli_query($con, $sql_comprobantes);
    if (!$res_comp) {
        // en caso de error, retornamos error
        mysqli_close($con);
        return false;
    }

    $total_pagado = 0.0;

    while ($rowC = mysqli_fetch_assoc($res_comp)) {
        $c_id = intval($rowC['id_comprobante']);
        $monto_total_comprobante = floatval($rowC['monto_total_igv_comprobante']); // siempre el valor real del comprobante
        $total_pagar_comprobante = floatval($rowC['total_pagar_comprobante']);   // lo que el proveedor debe recibir para este comprobante
        $id_det_comprobante = $rowC['id_detraccion_comprobante']; // puede ser NULL o un id

        $has_fg1 = intval($rowC['has_fg1_paid']) === 1;
        $has_fg2 = intval($rowC['has_fg2_paid']) === 1;

        // ======= LÓGICA DE PAGO POR COMPROBANTE =======
        // Si este comprobante es percepción (id == id_percepcion de la compra o id == constante),
        // se considera que solo se usa fg=1 y que fg=1 paga TOTAL_PAGAR (que incluye la percepción).
        $es_percepcion = false;
        if (!empty($id_det_comprobante)) {
            // si id_det_comprobante coincide con id_percepcion de la compra
            if (!empty($rowC['id_detraccion_tipo']) && intval($rowC['id_detraccion_tipo']) == 3) {
                $es_percepcion = true;
            }
        }

        if ($es_percepcion) {
            // Si existe voucher (fg=1) aprobado => se considera pagado el total_pagar_comprobante (que incluye percepción)
            if ($has_fg1) {
                $total_pagado += $total_pagar_comprobante;
            }
            // si no hay voucher, no se considera pagado
        } else {
            // Sin percepción: puede ser sin afectación o con detracción/retención
            // Regla:
            //  - si hay fg1 pagado => se considera pagado el total_pagar_comprobante (la parte al proveedor)
            //  - si además hay fg2 pagado => se considera pagada la parte restante: (monto_total_comprobante - total_pagar_comprobante)
            if ($has_fg1) {
                $total_pagado += $total_pagar_comprobante;
            }

            if ($has_fg2) {
                // la segunda parte (detracción/retención) = diferencia entre el total de la factura y lo entregado al proveedor
                $monto_second = $monto_total_comprobante - $total_pagar_comprobante;
                // por seguridad: no sumar negativo
                if ($monto_second > 0) {
                    $total_pagado += $monto_second;
                }
            }
        }
    }

    // === 6. SALDO ===
    if ($tipo_afectacion === 'percepcion') {
        $saldo = $monto_total - $total_pagado;
    }
    else {
        $saldo = $total_con_igv - $total_pagado;
    }
    

    // === 7. Construir respuesta ===
    $compra['subtotal'] = round($subtotal, 2);
    $compra['total_igv'] = round($total_igv, 2);
    $compra['total_con_igv'] = round($total_con_igv, 2);

    $compra['tipo_afectacion'] = $tipo_afectacion;
    $compra['porcentaje_detraccion'] = $porcentaje;
    $compra['monto_detraccion'] = round($monto_afectacion, 2);
    $compra['id_afectacion'] = $id_afectacion;

    $compra['monto_total']  = round($monto_total, 2);
    $compra['monto_pagado'] = round($total_pagado, 2);
    $compra['saldo']        = round($saldo, 2);

    $compra['pagado'] = ($compra['monto_pagado'] >= $compra['monto_total']) ? 1 : 0;

    $compra['cuentas'] = ConsultarCuentasPorCompra($id_compra);

    mysqli_close($con);
    return $compra;
}

function ConsultarCuentasPorCompra($id_compra)
{
    include("../_conexion/conexion.php");

    $id_compra = intval($id_compra);

    $sql = "SELECT pc.id_proveedor_cuenta,
                pc.banco_proveedor,
                pc.nro_cuenta_corriente
            FROM proveedor_cuenta pc
            INNER JOIN proveedor p ON pc.id_proveedor = p.id_proveedor
            INNER JOIN compra c ON c.id_proveedor = p.id_proveedor
            WHERE c.id_compra = $id_compra
            AND pc.est_proveedor_cuenta = 1"; // si manejas un estado activo

    $res = mysqli_query($con, $sql);

    if (!$res) {
        error_log('Error al consultar cuentas por compra: ' . mysqli_error($con));
        mysqli_close($con);
        return [];
    }

    $cuentas = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $cuentas[] = $row;
    }

    mysqli_close($con);
    return $cuentas;
}

function ActualizarComprobantesEstado($id_moneda)
{
    include("../_conexion/conexion.php");

    // Actualizar todos los comprobantes con est_comprobante = 1 a est_comprobante = 2
    $sql = "UPDATE comprobante 
            SET est_comprobante = 2 
            WHERE est_comprobante = 1
                AND id_moneda = $id_moneda";

    $res = mysqli_query($con, $sql);

    if (!$res) {
        $error = mysqli_error($con);
        mysqli_close($con);
        return "Error al actualizar comprobantes: $error";
    }

    $filas_actualizadas = mysqli_affected_rows($con);
    mysqli_close($con);

    return "SI|$filas_actualizadas";
}

function obtenerComprobantesEstado1($id_moneda)
{
    include("../_conexion/conexion.php");

    $sql = "
    SELECT 
        c.id_comprobante,
        c.id_compra,
        c.id_tipo_documento,
        td.nom_tipo_documento,
        c.serie,
        c.numero,
        c.total_pagar,
        c.fec_registro,
        c.est_comprobante,
        c.id_moneda,

        -- Datos del proveedor
        p.id_proveedor,
        p.nom_proveedor,
        p.ruc_proveedor,
        p.cont_proveedor,

        -- Datos de cuenta
        pc.banco_proveedor,
        pc.nro_cuenta_corriente,

        -- =====================
        -- CAMPOS CALCULADOS
        -- =====================

        -- DOI TIPO (reglas según longitud y formato)
        CASE 
            WHEN LENGTH(p.ruc_proveedor) = 11 THEN 'R'
            WHEN LENGTH(p.ruc_proveedor) = 8 AND p.ruc_proveedor REGEXP '^[0-9]+$' THEN 'L'
            WHEN LENGTH(p.ruc_proveedor) = 9 THEN 'E'
            WHEN p.ruc_proveedor REGEXP '^[A-Za-z][0-9A-Za-z]{7}$' THEN 'P'
            ELSE 'M'
        END AS doi_tipo,

        p.ruc_proveedor AS doi_numero,

        -- Tipo abono (BBVA = 'P', otros bancos = 'I')
        CASE
            WHEN pc.banco_proveedor LIKE '%BBVA%' THEN 'P'
            ELSE 'I'
        END AS tipo_abono,

        CASE
            WHEN pc.banco_proveedor LIKE '%BBVA%' 
                THEN pc.nro_cuenta_corriente
            ELSE pc.nro_cuenta_interbancaria
        END AS nro_cuenta,

        -- Beneficiario
        p.nom_proveedor AS beneficiario,

        -- Importe con 2 decimales
        ROUND(COALESCE(c.total_pagar, 0), 2) AS importe_abonar,

         -- Tipo recibo (F = factura, B = boleta, R = recibo/honorario, fallback = primera letra)
        CASE
            WHEN LOWER(td.nom_tipo_documento) LIKE '%factura%' THEN 'F'
            WHEN LOWER(td.nom_tipo_documento) LIKE '%boleta%' THEN 'B'
            WHEN LOWER(td.nom_tipo_documento) LIKE '%recibo%' THEN 'R'
            WHEN LOWER(td.nom_tipo_documento) LIKE '%honorario%' THEN 'R'
            ELSE UPPER(LEFT(td.nom_tipo_documento,1))
        END AS tipo_recibo,

        -- N° Documento (PENDIENTE)
        '' AS numero_documento,

        -- Abono agrupado
        'N' AS abono_agrupado,

        -- Referencia servicio/compra
        CONCAT('C00', c.id_compra) AS ref_orden_compra,

        -- Referencia comprobante
        CONCAT(c.serie, '-', c.numero) AS ref_comprobante,

        -- Indicador aviso
        'E' AS indicador_aviso,

        -- Medio aviso
        'contabilidad@arceperu.pe' AS medio_aviso,

        -- Persona contacto
        p.cont_proveedor AS persona_contacto

    FROM comprobante c
    INNER JOIN proveedor_cuenta pc 
        ON pc.id_proveedor_cuenta = c.id_cuenta_proveedor
    INNER JOIN proveedor p 
        ON p.id_proveedor = pc.id_proveedor
    INNER JOIN tipo_documento td
        ON td.id_tipo_documento = c.id_tipo_documento
    WHERE c.est_comprobante = 1
        AND c.id_moneda = $id_moneda
    ORDER BY c.id_comprobante ASC
    ";

    $res = mysqli_query($con, $sql);

    $comprobantes = [];

    if ($res && mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $comprobantes[] = $row;
        }
    }
    return $comprobantes;
}

function obtenerComprobantesGeneral()
{
    include("../_conexion/conexion.php");

    $sql = "
    SELECT 
        c.id_comprobante,
        c.id_compra,
        c.id_tipo_documento,
        td.nom_tipo_documento,
        c.serie,
        c.numero,
        c.total_pagar,
        c.fec_registro,
        c.est_comprobante,
        c.id_moneda,

        -- Datos del proveedor
        p.id_proveedor,
        p.nom_proveedor,
        p.ruc_proveedor,
        p.cont_proveedor,

        -- Datos de cuenta
        pc.banco_proveedor,
        pc.nro_cuenta_corriente,

        -- =====================
        -- CAMPOS CALCULADOS
        -- =====================

        -- DOI TIPO (reglas según longitud y formato)
        CASE 
            WHEN LENGTH(p.ruc_proveedor) = 11 THEN 'R'
            WHEN LENGTH(p.ruc_proveedor) = 8 AND p.ruc_proveedor REGEXP '^[0-9]+$' THEN 'L'
            WHEN LENGTH(p.ruc_proveedor) = 9 THEN 'E'
            WHEN p.ruc_proveedor REGEXP '^[A-Za-z][0-9A-Za-z]{7}$' THEN 'P'
            ELSE 'M'
        END AS doi_tipo,

        p.ruc_proveedor AS doi_numero,

        -- Tipo abono (BBVA = 'P', otros bancos = 'I')
        CASE
            WHEN pc.banco_proveedor LIKE '%BBVA%' THEN 'P'
            ELSE 'I'
        END AS tipo_abono,

        CASE
            WHEN pc.banco_proveedor LIKE '%BBVA%' 
                THEN pc.nro_cuenta_corriente
            ELSE pc.nro_cuenta_interbancaria
        END AS nro_cuenta,

        -- Beneficiario
        p.nom_proveedor AS beneficiario,

        -- Importe con 2 decimales
        FORMAT(c.total_pagar, 2) AS importe_abonar,

         -- Tipo recibo (F = factura, B = boleta, R = recibo/honorario, fallback = primera letra)
        CASE
            WHEN LOWER(td.nom_tipo_documento) LIKE '%factura%' THEN 'F'
            WHEN LOWER(td.nom_tipo_documento) LIKE '%boleta%' THEN 'B'
            WHEN LOWER(td.nom_tipo_documento) LIKE '%recibo%' THEN 'R'
            WHEN LOWER(td.nom_tipo_documento) LIKE '%honorario%' THEN 'R'
            ELSE UPPER(LEFT(td.nom_tipo_documento,1))
        END AS tipo_recibo,

        -- N° Documento (PENDIENTE)
        '' AS numero_documento,

        -- Abono agrupado
        'N' AS abono_agrupado,

        -- Referencia servicio/compra
        CONCAT('C00', c.id_compra) AS ref_orden_compra,

        -- Referencia comprobante
        CONCAT(c.serie, '-', c.numero) AS ref_comprobante,

        -- Indicador aviso
        'E' AS indicador_aviso,

        -- Medio aviso
        'contabilidad@arceperu.pe' AS medio_aviso,

        -- Persona contacto
        p.cont_proveedor AS persona_contacto

    FROM comprobante c
    INNER JOIN proveedor_cuenta pc 
        ON pc.id_proveedor_cuenta = c.id_cuenta_proveedor
    INNER JOIN proveedor p 
        ON p.id_proveedor = pc.id_proveedor
    INNER JOIN tipo_documento td
        ON td.id_tipo_documento = c.id_tipo_documento
    WHERE c.est_comprobante != 0
    ORDER BY c.id_comprobante ASC
    ";

    $res = mysqli_query($con, $sql);

    $comprobantes = [];

    if ($res && mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $comprobantes[] = $row;
        }
    }
    return $comprobantes;
}

function ObtenerTotalComprobantesRegistrados($id_compra) {
    include __DIR__ . "/../_conexion/conexion.php";

    $sql = "SELECT SUM(monto_total_igv) AS total_registrado
            FROM comprobante
            WHERE id_compra = $id_compra AND est_comprobante != 0"; // solo activos

    $res = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($res);

    return floatval($row['total_registrado']);
}

?>