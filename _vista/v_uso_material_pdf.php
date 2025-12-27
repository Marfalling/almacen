<?php
$html = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uso de Material</title>
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

        /* HEADER */
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

        .header-right {
            position: absolute;
            right: 10px;
            top: 10px;
            text-align: right;
            font-size: 7pt;
            line-height: 1.2;
        }

        /* ESTADO Y TÍTULO */
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

        .titulo-salida {
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
            margin-bottom: 12px;
            font-size: 7.5pt;
            line-height: 1.4;
        }

        .info-general div {
            margin-bottom: 2px;
        }

        /* TABLA DE DETALLES */
        .detalles-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
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

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        /* 🔹 AJUSTE DE COLUMNAS PARA INCLUIR CENTRO DE COSTO */
        .col-item {
            width: 6%;
        }

        .col-cantidad {
            width: 8%;
        }

        .col-unidad {
            width: 8%;
        }

        .col-descripcion {
            width: 32%;
        }

        .col-centro-costo {
            width: 20%;
        }

        .col-observaciones {
            width: 26%;
        }

        .observaciones-section {
            border: 1px solid #000;
            padding: 8px;
            margin-bottom: 12px;
            min-height: 50px;
            font-size: 7.5pt;
        }

        .observaciones-header {
            font-weight: bold;
            margin-bottom: 4px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 2px;
        }

        .footer {
            text-align: center;
            font-size: 7pt;
            color: #666;
            margin-top: 10px;
            border-top: 1px solid #ddd;
            padding-top: 6px;
        }

        .estado-activo {
            color: #006600;
        }

        .estado-anulado {
            color: #cc0000;
        }

    </style>
</head>
<body>
    <div class="container">

        <!-- ENCABEZADO -->
        <div class="header">
            <div class="header-left">
                <div class="logo">
                    ' . ($imagenLogoBase64 ? '<img src="' . $imagenLogoBase64 . '" alt="Logo">' : '<div style="border:1px solid #000;text-align:center;line-height:50px;font-size:8pt;font-weight:bold;">LOGO</div>') . '
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
                    <span class="' . ($estado_texto == 'ANULADO' ? 'estado-anulado' : 'estado-activo') . '">' . strtoupper($estado_texto) . '</span>
                </div>
            </div>

            <div class="titulo-section">
                <div class="titulo-salida">
                    USO DE MATERIAL<br>
                    U00' . $numero_uso . '
                </div>
            </div>
        </div>

        <!-- INFORMACIÓN GENERAL -->
        <div class="info-general">
            <div><strong>ALMACÉN:</strong> ' . $nombre_almacen . '</div>
            <div><strong>UBICACIÓN:</strong> ' . $nombre_ubicacion . '</div>
            <div><strong>OBRA:</strong> ' . $nombre_obra . '</div>
            <div><strong>CLIENTE:</strong> ' . $nombre_cliente . '</div>
            <div><strong>FECHA USO:</strong> ' . $fecha_uso . '</div>
            <div><strong>SOLICITANTE:</strong> ' . $nom_solicitante . '</div>
            <div><strong>CENTRO DE COSTO (SOLICITANTE):</strong> ' . $centro_costo_solicitante . '</div>
            <div><strong>REGISTRADO POR:</strong> ' . $nom_registrado . '</div>
            <div><strong>CENTRO DE COSTO (REGISTRADOR):</strong> ' . $centro_costo_registrador . '</div>
        </div>

        <!-- TABLA DE MATERIALES -->
        <table class="detalles-table">
            <thead>
                <tr>
                    <th class="col-item">ÍTEM</th>
                    <th class="col-cantidad">CANT.</th>
                    <th class="col-unidad">UND.</th>
                    <th class="col-descripcion">DESCRIPCIÓN</th>
                    <th class="col-centro-costo">CENTRO COSTO</th>
                    <th class="col-observaciones">OBSERVACIONES</th>
                </tr>
            </thead>
            <tbody>
                ' . $detalles_html . '
            </tbody>
        </table>

        <!-- OBSERVACIONES -->
        <div class="observaciones-section">
            <div class="observaciones-header">OBSERVACIONES GENERALES:</div>
            <div>
                Este documento certifica el uso de los materiales detallados, extraídos del almacén ' . $nombre_almacen . ' para su uso en la obra ' . $nombre_obra . '.
            </div>
        </div>

        <div class="footer">
            Generado el ' . $fecha_formateada . '
        </div>

    </div>
</body>
</html>';
?>