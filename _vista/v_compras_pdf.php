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
            margin: 15mm 10mm 15mm 10mm;
            size: A4;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 8pt;
            line-height: 1.2;
            color: #000;
            padding: 5px;
        }
        
        .container {
            max-width: 100%;
            margin: 0 auto;
        }
        
        /* HEADER SECTION */
        .header {
            border: 2px solid #000;
            padding: 10px;
            margin-bottom: 15px;
            position: relative;
            height: 90px;
        }
        
        .header-left {
            position: absolute;
            left: 15px;
            top: 15px;
            font-size: 8pt;
            line-height: 1.1;
        }
        
        .logo {
            width: 80px;
            margin-right: 15px;
            margin-bottom: 5px;
        }

        .logo img {
            max-width: 100%;
            max-height: 60px;
            object-fit: contain;
        }
        
        .logo-placeholder {
            width: 100%;
            height: 60px;
            border: 1px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .header-right {
            position: absolute;
            right: 15px;
            top: 15px;
            text-align: right;
            font-size: 8pt;
            line-height: 1.3;
        }

        /* TÍTULO Y NÚMERO DE ORDEN - ESTILO DEL PRIMER CÓDIGO */
        .titulo-orden {
            position: absolute;
            right: 15px;
            top: 15px;
            border: 2px solid #000;
            padding: 8px 15px;
            font-weight: bold;
            font-size: 12pt;
            text-align: center;
            background-color: #f0f0f0;
        }

        .titulo-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            position: relative;
        }

        .titulo-left {
            flex: 1;
        }

        .orden-box {
            border: 2px solid #000;
            padding: 8px 15px;
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            background-color: #f0f0f0;
            min-width: 150px;
            position: absolute;
            right: 0;
        }

        /* INFORMACIÓN DEL PROVEEDOR */
        .proveedor-section {
            margin-bottom: 15px;
        }

        .proveedor-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .proveedor-left, .proveedor-right {
            width: 48%;
        }

        /* MENSAJE FORMAL */
        .mensaje-formal {
            margin-bottom: 15px;
            line-height: 1.4;
        }

        /* INFORMACIÓN DE LA OBRA Y FECHA */
        .obra-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            align-items: flex-start;
        }

        .obra-left {
            width: 60%;
        }

        .obra-right {
            width: 35%;
            text-align: right;
        }

        /* TABLA DE PRODUCTOS */
        .tabla-productos {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 7pt;
        }

        .tabla-productos th {
            background-color: #e0e0e0;
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: center;
            font-weight: bold;
        }

        .tabla-productos td {
            border: 1px solid #000;
            padding: 3px 2px;
            vertical-align: top;
        }
            
        .detalles-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8pt;
        }
        
        .detalles-table th {
            background-color: #d0d0d0;
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            font-weight: bold;
            font-size: 8pt;
        }
        
        .detalles-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
        }
        
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        
        .col-item { width: 8%; }
        .col-cantidad { width: 12%; }
        .col-descripcion { width: 50%; }
        .col-precio { width: 15%; }
        .col-total { width: 15%; }

        /* TOTALES */
        .totales-section {
            float: right;
            width: 300px;
            margin-bottom: 15px;
        }
        
        .totales-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
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

        /* INFORMACIÓN ADICIONAL DE LA COMPRA */
        .info-compra {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 25px;
        }

        .info-compra-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .info-compra-left, .info-compra-right {
            width: 48%;
        }

        /* SECCIÓN DE FIRMAS - 3 COLUMNAS */
        .firmas-section {
            margin-top: 30px;
            margin-bottom: 20px;
            overflow: hidden; /* Para limpiar los floats */
        }

        .columnA {
            float: left;
            width: 33.3%;
            padding: 10px;
            text-align: center;
            height: 80px;
            border-top: 1px solid #000;
        }

        .columnB {
            float: left;
            width: 33.3%;
            padding: 10px;
            text-align: center;
            height: 80px;
            border-top: 1px solid #000;
        }

        .columnC {
            float: left;
            width: 33.3%;
            padding: 10px;
            text-align: center;
            height: 80px;
            border-top: 1px solid #000;
        }

        .firma-label {
            font-size: 8pt;
            font-weight: bold;
            margin-top: 50px;
            display: block;
        }

        /* FOOTER */
        .footer {
            text-align: center;
            font-size: 7pt;
            color: #666;
            margin-top: 20px;
        }

        /* UTILIDADES */
        .field-label {
            font-weight: bold;
            display: inline-block;
            min-width: 100px;
        }

        .mb-5 { margin-bottom: 5px; }
        .mb-10 { margin-bottom: 10px; }
        .mb-15 { margin-bottom: 15px; }
        .mb-20 { margin-bottom: 20px; }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
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
                Calle 3, N° 177- Urb la Grimanesa - CALLAO - CALLAO<br>
                Teléf: 572-3220 ANEXO 11 - 12<br>
                RUC: 20550259321
            </div>
        </div>

        <!-- TÍTULO DE ORDEN - ESTILO DEL PRIMER CÓDIGO -->
        <div class="titulo-section">
            <div class="titulo-left"></div>
            <div class="titulo-orden">
                ORDEN COMPRA<br>
                C' . $numero_orden . '
            </div>
        </div>

        <!-- INFORMACIÓN DEL PROVEEDOR -->
        <div class="proveedor-section">
            <div class="proveedor-row">
                <div class="proveedor-left">
                    <div class="mb-5"><span class="field-label">PROVEEDOR:</span> ' . $nom_proveedor . '</div>
                    <div class="mb-5"><span class="field-label">RUC:</span> ' . $ruc_proveedor . '</div>
                    <div class="mb-5"><span class="field-label">CONTACTO:</span> ' . $cont_proveedor . '</div>
                </div>
                <div class="proveedor-right">
                    <div class="mb-5"><span class="field-label">TELÉFONO:</span> ' . $tel_proveedor . '</div>
                    <div class="mb-5"><span class="field-label">DIRECCIÓN:</span> ' . $dir_proveedor . '</div>
                </div>
            </div>
        </div>

        <!-- MENSAJE FORMAL -->
        <div class="mensaje-formal">
            <div class="mb-10"><span class="field-label">Muy Sres. Nuestros:</span></div>
            <div>Sírvanse suministrarnos los materiales contenidos en la orden de compra, la misma que pasamos a detallar:</div>
        </div>

        <!-- INFORMACIÓN DE LA OBRA Y FECHA -->
        <div class="obra-info">
            <div class="obra-left">
                <div class="mb-10">
                    <span class="field-label">REFERENCIA DE LA OBRA:</span> ' . $nombre_obra . '
                </div>
                <div class="mb-10">
                    <span class="field-label">OBSERVACIONES:</span> ' . $observaciones . '
                </div>
            </div>
            <div class="obra-left">
                <div class="mb-10">
                    <span class="field-label">FECHA:</span> ' . $fecha_compra . '
                </div>
            </div>
        </div>

        <!-- TABLA DE PRODUCTOS - ESTILO DEL PRIMER CÓDIGO -->
        <table class="detalles-table">
            <thead>
                <tr>
                    <th class="col-item">POS</th>
                    <th class="col-cantidad">CANT.</th>
                    <th class="col-descripcion">ESPECIFICACIÓN</th>
                    <th class="col-precio">PRECIO U.</th>
                    <th class="col-total">IMPORTE</th>
                </tr>
            </thead>
            <tbody>
                ' . $detalles_html . '
            </tbody>
        </table>

        <!-- TOTALES - ESTILO DEL PRIMER CÓDIGO -->
        <div class="totales-section">
            <table class="totales-table">
                <tr>
                    <td class="label">SUB TOTAL</td>
                    <td class="value">' . $subtotal_formateado . '</td>
                </tr>
                <tr>
                    <td class="label">IGV 18.00%</td>
                    <td class="value">' . $igv_formateado . '</td>
                </tr>
                <tr>
                    <td class="label">PERCEPCIÓN</td>
                    <td class="value">0.00</td>
                </tr>
                <tr style="background-color: #f0f0f0;">
                    <td class="label"><strong>TOTAL</strong></td>
                    <td class="value"><strong>' . $total_formateado . '</strong></td>
                </tr>
            </table>
        </div>
        
        <div class="clearfix"></div>

        <!-- INFORMACIÓN ADICIONAL DE LA COMPRA -->
        <div class="info-compra">
            <div class="info-compra-row">
                <div class="info-compra-left">
                    <div class="mb-5"><span class="field-label">DIRECCIÓN DE ENVÍO:</span> ' . $lugar_entrega . '</div>
                    <div class="mb-5"><span class="field-label">REFERENCIA:</span> ' . $aclaraciones . '</div>
                    <div class="mb-5"><span class="field-label">PLAZO DE ENTREGA:</span> ' . $plazo_entrega . '</div>
                    <div class="mb-5"><span class="field-label">SOLICITADO POR:</span> ' . $nom_personal . '</div>
                </div>
                <div class="info-compra-right">
                    <div class="mb-5"><span class="field-label">MONEDA:</span> ' . $moneda . '</div>
                    <div class="mb-5"><span class="field-label">FECHA REQ.:</span> ' . $fecha_requerida . '</div>
                    <div class="mb-5"><span class="field-label">CONDICIONES DE PAGO:</span> ' . $portes . '</div>
                    <div class="mb-5"><span class="field-label">TELÉFONO:</span> ' . $telefono . '</div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN DE FIRMAS - 3 COLUMNAS -->
        <div class="firmas-section">
            <div class="firmas-container">
                <div class="columnA">
                    <span class="firma-label">V°B° GERENCIA</span>
                </div>
                <div class="columnB">
                    <span class="firma-label">V°B° DPTO. COMPRAS</span>
                </div>
                <div class="columnC">
                    <span class="firma-label">V°B° ADMINISTRACIÓN</span>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            Generado el ' . $fecha_formateada . '
        </div>
    </div>
</body>
</html>';
?>