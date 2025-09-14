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

        /* CONTENEDOR PARA ALINEAR CUADROS */
        .cuadros-container {
            position: relative;
            margin-bottom: 20px;
            min-height: 70px;
        }

        /* TÍTULO Y NÚMERO DE USO */
        .titulo-section {
            position: absolute;
            top: 0;
            right: 0;
            width: auto;
        }

        .uso-box {
            border: 2px solid #000;
            padding: 8px 15px;
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            background-color: #f0f0f0;
            min-width: 150px;
        }

        /* ESTADO DEL USO DE MATERIAL */
        .estado-section {
            position: absolute;
            top: 0;
            left: 0;
            width: auto;
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

        /* INFORMACIÓN GENERAL */
        .info-general {
            margin-bottom: 15px;
            border: 1px solid #000;
            padding: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .info-left, .info-right {
            width: 48%;
        }

        .estado-badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 10pt;
            color: white;
        }

        /* TABLA DE MATERIALES */
        .detalles-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 7pt;
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
        .col-unidad { width: 10%; }
        .col-descripcion { width: 35%; }
        .col-observaciones { width: 25%; }
        .col-archivos { width: 10%; }

        /* OBSERVACIONES GENERALES */
        .observaciones-section {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 25px;
            min-height: 50px;
        }

        /* SECCIÓN DE FIRMAS - 3 COLUMNAS */
        .firmas-section {
            margin-top: 30px;
            margin-bottom: 20px;
            overflow: hidden;
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

        <!-- CONTENEDOR PARA CUADROS ALINEADOS -->
        <div class="cuadros-container">
            <!-- ESTADO DEL USO DE MATERIAL -->
            <div class="estado-section">
                <div class="estado-box">
                    ESTADO:<br>
                    ' . $estado_texto . '
                </div>
            </div>

            <!-- TÍTULO DE USO DE MATERIAL -->
            <div class="titulo-section">
                <div class="uso-box">
                    USO DE MATERIAL<br>
                    U' . $numero_uso . '
                </div>
            </div>
        </div>

        <!-- INFORMACIÓN GENERAL -->
        <div class="info-general">
            <div class="info-row">
                <div class="info-left">
                    <div class="mb-5"><span class="field-label">ALMACÉN:</span> ' . $nombre_almacen . '</div>
                    <div class="mb-5"><span class="field-label">UBICACIÓN:</span> ' . $nombre_ubicacion . '</div>
                    <div class="mb-5"><span class="field-label">OBRA:</span> ' . $nombre_obra . '</div>
                </div>
                <div class="info-right">
                    <div class="mb-5"><span class="field-label">CLIENTE:</span> ' . $nombre_cliente . '</div>
                    <div class="mb-5"><span class="field-label">FECHA USO:</span> ' . $fecha_uso . '</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-left">
                    <div class="mb-5"><span class="field-label">SOLICITANTE:</span> ' . $nom_solicitante . '</div>
                </div>
                <div class="info-right">
                    <div class="mb-5"><span class="field-label">REGISTRADO POR:</span> ' . $nom_registrado . '</div>
                </div>
            </div>
        </div>

        <!-- TABLA DE MATERIALES UTILIZADOS -->
        <table class="detalles-table">
            <thead>
                <tr>
                    <th class="col-item">ÍTEM</th>
                    <th class="col-cantidad">CANTIDAD</th>
                    <th class="col-unidad">UNIDAD</th>
                    <th class="col-descripcion">DESCRIPCIÓN DEL MATERIAL</th>
                    <th class="col-observaciones">OBSERVACIONES</th>
                </tr>
            </thead>
            <tbody>
                ' . $detalles_html . '
            </tbody>
        </table>

        <!-- OBSERVACIONES GENERALES -->
        <div class="observaciones-section">
            <div class="mb-10"><span class="field-label">OBSERVACIONES GENERALES:</span></div>
            <div style="min-height: 30px;">
                Este documento certifica el uso de los materiales detallados anteriormente, los cuales han sido 
                extraídos del almacén ' . $nombre_almacen . ' para ser utilizados en la obra ' . $nombre_obra . '.
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