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

    // Validar que no exista el mismo comprobante (serie-número)
    $sql_existe = "SELECT id_comprobante 
                   FROM comprobante 
                   WHERE serie = '$serie' 
                   AND numero = '$numero' 
                   AND id_tipo_documento = $id_tipo_documento
                   AND est_comprobante = 1";
    $res_existe = mysqli_query($con, $sql_existe);
    
    if (mysqli_num_rows($res_existe) > 0) {
        mysqli_close($con);
        return "Ya existe un comprobante con la serie y número ingresados.";
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
                id_personal
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
                $id_personal
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
function SubirVoucherComprobante($id_comprobante, $archivo_voucher, $enviar_proveedor = false, $enviar_contabilidad = false, $enviar_tesoreria = false)
{
    include("../_conexion/conexion.php");

    // 🔍 DEBUG
    error_log("========== FUNCIÓN SubirVoucherComprobante ==========");
    error_log("id_comprobante recibido: $id_comprobante (tipo: " . gettype($id_comprobante) . ")");
    
    $id_comprobante = intval($id_comprobante);
    
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
                         m.nom_moneda
                  FROM comprobante c
                  INNER JOIN compra co ON c.id_compra = co.id_compra
                  INNER JOIN proveedor p ON co.id_proveedor = p.id_proveedor
                  INNER JOIN moneda m ON c.id_moneda = m.id_moneda
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

    // Extraer id_compra del array asociativo
    $id_compra = intval($comprobante['id_compra']);

    // Validar que se proporcionó un archivo
    if (empty($archivo_voucher)) {
        mysqli_close($con);
        return "Debe proporcionar un archivo de voucher.";
    }

    $archivo_voucher_escaped = mysqli_real_escape_string($con, $archivo_voucher);

    // Actualizar el voucher
    $sql = "UPDATE comprobante 
            SET voucher_pago = '$archivo_voucher_escaped',
                fec_pago = COALESCE(fec_pago, NOW()),
                est_comprobante = 3
            WHERE id_comprobante = $id_comprobante";
    
    $res = mysqli_query($con, $sql);

    if (!$res) {
        $error = mysqli_error($con);
        mysqli_close($con);
        return "Error al subir el voucher: $error";
    }

    // ================================================================
    // CALCULAR TOTAL REAL DE LA COMPRA DESDE DETALLE
    // ================================================================
    $sql_total_compra = "SELECT COALESCE(SUM(cd.cant_compra_detalle), 0) as total_compra
                         FROM compra_detalle cd
                         WHERE cd.id_compra = $id_compra
                         AND cd.est_compra_detalle = 1";
    
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
            $sql_update_compra = "UPDATE compra 
                                  SET est_compra = 4 
                                  WHERE id_compra = $id_compra";
            mysqli_query($con, $sql_update_compra);
            
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
    if ($enviar_proveedor || $enviar_contabilidad || $enviar_tesoreria) {

        // Base de destinatarios
        $destinatarios = [];

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

        // Solo continuar si hay al menos un destinatario
        if (count($destinatarios) > 0) {

            // Unir todos los correos con coma
            $para = implode(", ", $destinatarios);
            $asunto = "Voucher de Pago - Comprobante {$comprobante['num_comprobante']}";
            
            // URL del voucher
            $url_voucher = "https://montajeseingenieriaarceperusac.pe/almacen/_upload/vouchers/" . $archivo_voucher;

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
                                <p><strong>Monto pagado:</strong> {$comprobante['simbolo_moneda']} " . number_format($comprobante['total_pagar'], 2) . "</p>
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
                fec_pago = $fec_pago
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

    // ⭐ LOG: Verificar que la variable existe
    error_log("BD_COMPLEMENTO: " . (isset($bd_complemento) ? $bd_complemento : "NO DEFINIDA"));

    $sql = "SELECT c.*,
                   CONCAT(c.serie, '-', c.numero) as num_comprobante,
                   td.nom_tipo_documento,
                   co.id_proveedor,
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
//-----------------------------------------------------------------------
// Consultar compra por ID (para gestión de comprobantes)
//-----------------------------------------------------------------------
function ConsultarCompraCom($id_compra)
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
                   END as simbolo_moneda,
                   det.nombre_detraccion,
                   det.porcentaje as porcentaje_detraccion,
                   per.nom_personal as nom_personal_crea,
                   -- Personal que aprobó financieramente
                   per_fin.nom_personal as nom_personal_aprueba_financiera
                   
            FROM compra c
            INNER JOIN proveedor p ON c.id_proveedor = p.id_proveedor
            INNER JOIN moneda m ON c.id_moneda = m.id_moneda
            LEFT JOIN detraccion det ON c.id_detraccion = det.id_detraccion
            LEFT JOIN {$bd_complemento}.personal per ON c.id_personal = per.id_personal
            LEFT JOIN {$bd_complemento}.personal per_fin ON c.id_personal_aprueba_financiera = per_fin.id_personal
            WHERE c.id_compra = $id_compra";
    
    $res = mysqli_query($con, $sql);
    
    if (!$res) {
        error_log("Error en ConsultarCompra: " . mysqli_error($con));
        mysqli_close($con);
        return false;
    }
    
    $compra = mysqli_num_rows($res) > 0 ? mysqli_fetch_assoc($res) : false;

    //  CALCULAR SUBTOTAL E IGV
    $sql_detalle = "SELECT 
                        COALESCE(SUM(cd.cant_compra_detalle * cd.prec_compra_detalle), 0) as subtotal,
                        COALESCE(SUM((cd.cant_compra_detalle * cd.prec_compra_detalle) * (cd.igv_compra_detalle / 100)), 0) as total_igv
                    FROM compra_detalle cd
                    WHERE cd.id_compra = $id_compra
                    AND cd.est_compra_detalle = 1";
    
    $res_detalle = mysqli_query($con, $sql_detalle);
    
    if (!$res_detalle) {
        error_log("ERROR SQL detalle: " . mysqli_error($con));
        mysqli_close($con);
        return false;
    }
    
    $fila_detalle = mysqli_fetch_assoc($res_detalle);
    
    $subtotal = floatval($fila_detalle['subtotal']);
    $total_igv = floatval($fila_detalle['total_igv']);
    $total_con_igv = $subtotal + $total_igv;

    //CALCULAR AFECTACIONES
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

    // 3️⃣ Calcular TOTAL PAGADO (comprobantes con voucher)
    $sql_pagado = "SELECT 
                    COALESCE(SUM(total_pagar), 0) as total_pagado_comprobantes,
                    COUNT(*) as cantidad_comprobantes_pagados
                   FROM comprobante
                   WHERE id_compra = $id_compra
                   AND est_comprobante = 3";  // ← ESTADO 3 = PAGADO
    
    $res_pagado = mysqli_query($con, $sql_pagado);
    $row_pagado = mysqli_fetch_assoc($res_pagado);
    
    $total_pagado = floatval($row_pagado['total_pagado_comprobantes']);
    $cantidad_pagados = intval($row_pagado['cantidad_comprobantes_pagados']);
    
    // 4️⃣ Calcular SALDO PENDIENTE
    $saldo_pendiente = $total_con_igv - $total_pagado;

    //  ARMAR ARRAY COMPLETO
    $compra['subtotal'] = round($subtotal, 2);
    $compra['total_igv'] = round($total_igv, 2);
    $compra['total_con_igv'] = round($total_con_igv, 2);
    $compra['monto_detraccion'] = round($monto_detraccion, 2);
    $compra['monto_retencion'] = round($monto_retencion, 2);
    $compra['monto_percepcion'] = round($monto_percepcion, 2);
    $compra['monto_total'] = round($monto_total, 2);

    $compra['monto_pagado'] = round($total_pagado, 2);
    $compra['saldo'] = round($saldo_pendiente, 2);
    
    mysqli_close($con);
    return $compra;
}
?>