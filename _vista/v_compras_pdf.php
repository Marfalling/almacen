<?php
$html = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de Compra</title>
    <style>
        @page {
            margin: 12mm 8mm 12mm 8mm;
            size: A4;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 8pt;
            line-height: 1.2;
            color: #000;
            padding: 0;
            margin: 0;
        }
        
        .container {
            max-width: 100%;
            margin: 0 auto;
        }
        
        /* HEADER SECTION */
        .header {
            border: 2px solid #000;
            padding: 10px;
            margin-bottom: 12px;
            position: relative;
            height: 75px;
        }
        
        .header-left {
            position: absolute;
            left: 10px;
            top: 10px;
            font-size: 7pt;
            line-height: 1.1;
        }
        
        .logo {
            width: 70px;
            margin-right: 10px;
            margin-bottom: 3px;
        }

        .logo img {
            max-width: 100%;
            max-height: 50px;
            object-fit: contain;
        }
        
        .logo-placeholder {
            width: 100%;
            height: 50px;
            border: 1px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7pt;
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .header-right {
            position: absolute;
            right: 10px;
            top: 10px;
            text-align: right;
            font-size: 7pt;
            line-height: 1.2;
        }

        /* CUADROS DE ESTADO Y TÍTULO */
        .cuadros-container {
            position: relative;
            margin-bottom: 15px;
            min-height: 60px;
        }

        .estado-section {
            position: absolute;
            top: 0;
            left: 0;
        }
        
        .estado-box {
            border: 2px solid #000;
            padding: 6px 12px;
            text-align: center;
            font-size: 10pt;
            font-weight: bold;
            background-color: #f0f0f0;
            min-width: 120px;
        }

        .titulo-section {
            position: absolute;
            top: 0;
            right: 0;
        }

        .titulo-compra {
            border: 2px solid #000;
            padding: 6px 12px;
            text-align: center;
            font-size: 10pt;
            font-weight: bold;
            background-color: #f0f0f0;
            min-width: 150px;
        }

        /* INFORMACIÓN GENERAL */
        .info-general {
            border: 1px solid #000;
            padding: 8px;
            margin-bottom: 10px;
            font-size: 7.5pt;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .info-left, .info-right {
            width: 48%;
        }

        .info-general div {
            margin-bottom: 2px;
            line-height: 1.3;
        }

        /* MENSAJE FORMAL */
        .mensaje-formal {
            margin-bottom: 10px;
            line-height: 1.4;
            font-size: 7.5pt;
        }

        /* TABLA DE PRODUCTOS */
        .detalles-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 7pt;
        }
        
        .detalles-table th {
            background-color: #d0d0d0;
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: center;
            font-weight: bold;
            line-height: 1.1;
        }
        
        .detalles-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            vertical-align: top;
            line-height: 1.3;
        }
        
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        
        .col-item { width: 6%; }
        .col-cantidad { width: 8%; }
        .col-unidad { width: 8%; }
        .col-descripcion { width: 35%; }
        .col-centro-costo { width: 18%; }
        .col-precio { width: 12%; }
        .col-total { width: 13%; }

        /* TOTALES */
        .totales-section {
            float: right;
            width: 300px;
            margin-bottom: 10px;
        }
        
        .totales-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
        }
        
        .totales-table td {
            border: 1px solid #000;
            padding: 4px 8px;
        }
        
        .totales-table .label {
            background-color: #e8e8e8;
            font-weight: bold;
            text-align: right;
            width: 60%;
        }
        
        .totales-table .value {
            background-color: #fff;
            text-align: right;
            width: 40%;
        }

        /* SECCIÓN DE FIRMAS */
        .firmas-section {
            margin-top: 20px;
            margin-bottom: 15px;
            overflow: hidden;
        }

        .firma-box {
            float: left;
            width: 33.3%;
            padding: 8px;
            text-align: center;
            height: 70px;
            border-top: 1px solid #000;
        }

        .firma-label {
            font-size: 7.5pt;
            font-weight: bold;
            margin-top: 45px;
            display: block;
        }

        /* FOOTER */
        .footer {
            text-align: center;
            font-size: 7pt;
            color: #666;
            margin-top: 15px;
            border-top: 1px solid #ddd;
            padding-top: 6px;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .field-label {
            font-weight: bold;
            display: inline-block;
            min-width: 100px;
        }

        .mb-10 { margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <div class="header">
            <div class="header-left">
                <div class="logo">
                    ' . ($imagenLogoBase64 ? '<img src="' . $imagenLogoBase64 . '" alt="Logo">' : '<div class="logo-placeholder">LOGO</div>') . '
                </div>
                <div>MONTAJES E INGENIERÍA ARCE PERÚ S.A.C.</div>
            </div>
            <div class="header-right">
                Calle 3, N° 177- Urb la Grimanesa - CALLAO<br>
                Teléf: 572-3220 ANEXO 11 - 12<br>
                RUC: 20550259321
            </div>
        </div>

        <!-- ESTADO Y TÍTULO -->
        <div class="cuadros-container">
            <div class="estado-section">
                <div class="estado-box">
                    ESTADO:<br>
                    <span>' . $estado_texto . '</span>
                </div>
            </div>

            <div class="titulo-section">
                <div class="titulo-compra">
                    ORDEN DE ' . $tipo_orden . '<br>
                    ' . $codigo_orden . '
                </div>
            </div>
        </div>

        <!-- INFORMACIÓN DEL PROVEEDOR -->
        <div class="info-general">
            <div class="info-row">
                <div class="info-left">
                    <div><strong>PROVEEDOR:</strong> ' . $nom_proveedor . '</div>
                    <div><strong>RUC:</strong> ' . $ruc_proveedor . '</div>
                    <div><strong>CONTACTO:</strong> ' . $cont_proveedor . '</div>
                </div>
                <div class="info-right">
                    <div><strong>TELÉFONO:</strong> ' . $tel_proveedor . '</div>
                    <div><strong>EMAIL:</strong> ' . $email_proveedor . '</div>
                    <div><strong>DIRECCIÓN:</strong> ' . $dir_proveedor . '</div>
                </div>
            </div>
        </div>

        <!-- MENSAJE FORMAL -->
        <div class="mensaje-formal">
            <div class="mb-10"><span class="field-label">Muy Sres. Nuestros:</span></div>
            <div>Sírvanse suministrarnos ' . ($tipo_orden == 'COMPRA' ? 'los materiales' : 'los servicios') . ' contenidos en la orden de ' . strtolower($tipo_orden) . ', la misma que pasamos a detallar:</div>
        </div>

        <!-- INFORMACIÓN DE LA OBRA -->
        <div class="info-general">
            <div class="info-row">
                <div class="info-left">
                    <div><strong>REFERENCIA OBRA:</strong> ' . $nombre_obra . '</div>
                    <div><strong>OBSERVACIONES:</strong> ' . $observaciones . '</div>
                </div>
                <div class="info-right">
                    <div><strong>FECHA:</strong> ' . $fecha_compra . '</div>
                    <div><strong>FECHA REQ.:</strong> ' . $fecha_requerida . '</div>
                </div>
            </div>
        </div>

        <!-- TABLA DE PRODUCTOS -->
        <table class="detalles-table">
            <thead>
                <tr>
                    <th class="col-item">ITEM</th>
                    <th class="col-cantidad">CANT.</th>
                    <th class="col-unidad">UND.</th>
                    <th class="col-descripcion">ESPECIFICACIÓN</th>
                    <th class="col-centro-costo">CENTRO COSTO</th>
                    <th class="col-precio">PRECIO U.</th>
                    <th class="col-total">IMPORTE</th>
                </tr>
            </thead>
            <tbody>
                ' . $detalles_html . '
            </tbody>
        </table>

        <!-- TOTALES -->
        <div class="totales-section">
            <table class="totales-table">
                <tr>
                    <td class="label">SUB TOTAL</td>
                    <td class="value">' . $simbolo_moneda . ' ' . $subtotal_formateado . '</td>
                </tr>
                <tr>
                    <td class="label">IGV TOTAL</td>
                    <td class="value">' . $simbolo_moneda . ' ' . $igv_formateado . '</td>
                </tr>
                <tr>
                    <td class="label"><strong>TOTAL CON IGV</strong></td>
                    <td class="value"><strong>' . $simbolo_moneda . ' ' . $total_con_igv_formateado . '</strong></td>
                </tr>';
                
        // Mostrar DETRACCIÓN si existe
        if ($tiene_detraccion) {
            $html .= '
                <tr style="background-color: #fff3cd;">
                    <td class="label">' . $nombre_detraccion . ' (' . $porcentaje_detraccion . '%)</td>
                    <td class="value">-' . $simbolo_moneda . ' ' . number_format($monto_detraccion, 2) . '</td>
                </tr>';
        }

        // Mostrar RETENCIÓN si existe
        if ($tiene_retencion) {
            $html .= '
                <tr style="background-color: #d1ecf1;">
                    <td class="label">' . $nombre_retencion . ' (' . $porcentaje_retencion . '%)</td>
                    <td class="value">-' . $simbolo_moneda . ' ' . number_format($monto_retencion, 2) . '</td>
                </tr>';
        }

        // Mostrar PERCEPCIÓN si existe
        if ($tiene_percepcion) {
            $html .= '
                <tr style="background-color: #d4edda;">
                    <td class="label">' . $nombre_percepcion . ' (' . $porcentaje_percepcion . '%)</td>
                    <td class="value">+' . $simbolo_moneda . ' ' . number_format($monto_percepcion, 2) . '</td>
                </tr>';
        }

        $html .= '
                <tr>
                    <td class="label"><strong>TOTAL A PAGAR</strong></td>
                    <td class="value"><strong>' . $simbolo_moneda . ' ' . $total_formateado . '</strong></td>
                </tr>
            </table>
        </div>

        <div class="clearfix"></div>

        <!-- INFORMACIÓN ADICIONAL -->
        <div class="info-general">
            <div class="info-row">
                <div class="info-left">
                    <div><strong>DIRECCIÓN ENVÍO:</strong> ' . $lugar_entrega . '</div>
                    <div><strong>REFERENCIA:</strong> ' . $aclaraciones . '</div>
                    <div><strong>SOLICITADO POR:</strong> ' . $nom_personal . '</div>
                    <div><strong>CENTRO COSTO SOLICITANTE:</strong> ' . $centro_costo_personal . '</div>
                </div>
                <div class="info-right">
                    <div><strong>MONEDA:</strong> ' . $moneda . '</div>
                    <div><strong>CONDICIÓN PAGO:</strong> ' . $condicion_pago . '</div>';
                    
        //  AGREGAR PLAZO DE ENTREGA SI EXISTE
        if (!empty($plazo_entrega_texto)) {
            $html .= '
                    <div><strong>PLAZO ENTREGA:</strong> ' . htmlspecialchars($plazo_entrega_texto, ENT_QUOTES, 'UTF-8') . '</div>';
        }

        $html .= '
                    <div><strong>TELÉFONO:</strong> ' . $telefono . '</div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN DE FIRMAS -->
        <div class="footer">
            <div class="firmas-section clearfix">
                <div class="firma-box">
                    <span class="firma-label">ENTREGADO POR</span>
                </div>
                <div class="firma-box">
                    <span class="firma-label">RECIBIDO POR</span>
                </div>
                <div class="firma-box">
                    <span class="firma-label">V°B° SUPERVISIÓN</span>
                </div>
            </div>

            <div style="margin-top:15px; font-size:7pt; color:#666;">
                Fecha ' . $fecha_formateada . '
            </div>
        </div>
    </div>
</body>
</html>';
?>