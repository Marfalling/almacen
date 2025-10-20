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
            margin: 15mm 10mm 15mm 10mm;
            size: A4;
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

        /* HEADER */
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

        .header-right {
            position: absolute;
            right: 15px;
            top: 15px;
            text-align: right;
            font-size: 8pt;
            line-height: 1.3;
        }

        /* ESTADO Y TÍTULO */
        .cuadros-container {
            position: relative;
            margin-bottom: 20px;
            min-height: 70px;
        }

        .estado-section {
            position: absolute;
            top: 0;
            left: 0;
        }

        .estado-box {
            border: 2px solid #000;
            padding: 8px 15px;
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            background-color: #f0f0f0;
            min-width: 150px;
        }

        .titulo-section {
            position: absolute;
            top: 0;
            right: 0;
        }

        .titulo-salida {
            border: 2px solid #000;
            padding: 8px 15px;
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            background-color: #f0f0f0;
            min-width: 180px;
        }

        /* INFORMACIÓN GENERAL */
        .info-general {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 15px;
        }

        .info-general div {
            margin-bottom: 3px;
        }

        /* TABLA DE DETALLES */
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
        }

        .detalles-table td {
            border: 1px solid #000;
            padding: 4px 6px;
        }

        .observaciones-section {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 15px;
            min-height: 60px;
        }

        .observaciones-header {
            font-weight: bold;
            margin-bottom: 5px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
        }

        .footer {
            text-align: center;
            font-size: 7pt;
            color: #666;
            margin-top: 15px;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }

    </style>
</head>
<body>
    <div class="container">

        <!-- ENCABEZADO -->
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

        <!-- ESTADO Y TÍTULO -->
        <div class="cuadros-container">
            <div class="estado-section">
                <div class="estado-box">
                    ESTADO:<br>
                    <span>' . strtoupper($estado_texto) . '</span>
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
            <div><strong>REGISTRADO POR:</strong> ' . $nom_registrado . '</div>
        </div>

        <!-- TABLA DE MATERIALES -->
        <table class="detalles-table">
            <thead>
                <tr>
                    <th>ÍTEM</th>
                    <th>CANTIDAD</th>
                    <th>UNIDAD</th>
                    <th>DESCRIPCIÓN DEL MATERIAL</th>
                    <th>OBSERVACIONES</th>
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
                Este documento certifica el uso de los materiales detallados anteriormente, los cuales han sido 
                extraídos del almacén ' . $nombre_almacen . ' para ser utilizados en la obra ' . $nombre_obra . '.
            </div>
        </div>

        <div class="footer">
            Generado el ' . $fecha_formateada . '
        </div>

    </div>
</body>
</html>';
?>
