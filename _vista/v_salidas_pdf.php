<?php
$html = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documento de Salida/Traslado</title>
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

        /* CONTENEDOR PARA ALINEAR CUADROS */
        .cuadros-container {
            position: relative;
            margin-bottom: 15px;
            min-height: 60px;
        }

        /* ESTADO DE LA SALIDA */
        .estado-section {
            position: absolute;
            top: 0;
            left: 0;
            width: auto;
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

        /* TÍTULO Y NÚMERO DE SALIDA */
        .titulo-section {
            position: absolute;
            top: 0;
            right: 0;
            width: auto;
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

        /* TABLA DE MATERIALES */
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
        
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        
        /* 🔹 AJUSTE DE COLUMNAS PARA INCLUIR CENTRO DE COSTO */
        .col-item { width: 6%; }
        .col-descripcion { width: 40%; }
        .col-centro-costo { width: 24%; }
        .col-cantidad { width: 15%; }
        .col-unidad { width: 15%; }

        /* OBSERVACIONES */
        .observaciones-section {
            border: 1px solid #000;
            padding: 8px;
            margin-bottom: 15px;
            min-height: 50px;
            font-size: 7.5pt;
        }

        .observaciones-header {
            font-weight: bold;
            margin-bottom: 4px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 2px;
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

        /* TABLA ORIGEN/DESTINO */
        .origen-destino-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 7.5pt;
        }

        .origen-destino-table td {
            width: 50%;
            vertical-align: top;
            border: 1px solid #000;
            padding: 8px;
        }

        .origen-header {
            background: #fff2e8;
            font-weight: bold;
            text-align: center;
            padding: 5px;
            margin: -8px -8px 8px -8px;
            border-bottom: 1px solid #000;
        }

        .destino-header {
            background: #e8f8e8;
            font-weight: bold;
            text-align: center;
            padding: 5px;
            margin: -8px -8px 8px -8px;
            border-bottom: 1px solid #000;
        }

        .origen-destino-table div {
            margin-bottom: 2px;
            line-height: 1.3;
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
                Calle 3, N° 177- Urb la Grimanesa - CALLAO<br>
                Teléf: 572-3220 ANEXO 11 - 12<br>
                RUC: 20550259321
            </div>
        </div>

        <!-- CONTENEDOR PARA CUADROS ALINEADOS -->
        <div class="cuadros-container">
            <!-- ESTADO DE LA SALIDA -->
            <div class="estado-section">
                <div class="estado-box">
                    ESTADO:<br>
                    <span>' . $estado_texto . '</span>
                </div>
            </div>

            <!-- TÍTULO DE SALIDA -->
            <div class="titulo-section">
                <div class="titulo-salida">
                    SALIDA DE MATERIALES<br>
                    S00' . $numero_salida . '
                </div>
            </div>
        </div>

        <!-- INFORMACIÓN GENERAL -->
        <div class="info-general">
            <div class="info-row">
                <div class="info-left">
                    <div><strong>N° DOCUMENTO:</strong> ' . $ndoc_salida . '</div>
                    <div><strong>TIPO MATERIAL:</strong> ' . $tipo_material . '</div>
                </div>
                <div class="info-right">
                    <div><strong>FECHA SALIDA:</strong> ' . $fecha_salida . '</div>
                    <div><strong>FECHA REQUERIDA:</strong> ' . $fecha_requerida . '</div>
                    <div><strong>REGISTRADO POR:</strong> ' . $nom_personal . '</div>
                    <div><strong>CENTRO DE COSTO (REGISTRADOR):</strong> ' . $centro_costo_registrador . '</div>
                </div>
            </div>
        </div>

        <!-- ORIGEN Y DESTINO -->
        <table class="origen-destino-table">
            <tr>
                <!-- ORIGEN -->
                <td>
                    <div class="origen-header">ORIGEN</div>
                    <div><strong>ALMACÉN:</strong> ' . $almacen_origen . '</div>
                    <div><strong>UBICACIÓN:</strong> ' . $ubicacion_origen . '</div>
                    <div><strong>ENCARGADO:</strong> ' . $personal_encargado . '</div>
                    <div><strong>CENTRO DE COSTO (ENCARGADO):</strong> ' . $centro_costo_encargado . '</div>
                </td>

                <!-- DESTINO -->
                <td>
                    <div class="destino-header">DESTINO</div>
                    <div><strong>ALMACÉN:</strong> ' . $almacen_destino . '</div>
                    <div><strong>UBICACIÓN:</strong> ' . $ubicacion_destino . '</div>
                    <div><strong>RECIBE:</strong> ' . $personal_recibe . '</div>
                    <div><strong>CENTRO DE COSTO (RECIBE):</strong> ' . $centro_costo_recibe . '</div>
                </td>
            </tr>
        </table>

        <!-- TABLA DE MATERIALES -->
        <table class="detalles-table">
            <thead>
                <tr>
                    <th class="col-item">ÍTEM</th>
                    <th class="col-descripcion">DESCRIPCIÓN</th>
                    <th class="col-centro-costo">CENTRO DE COSTO</th>
                    <th class="col-cantidad">CANTIDAD</th>
                    <th class="col-unidad">UNIDAD</th>
                </tr>
            </thead>
            <tbody>
                ' . $detalles_html . '
            </tbody>
        </table>

        <!-- OBSERVACIONES -->
        <div class="observaciones-section">
            <div class="observaciones-header">OBSERVACIONES:</div>
            <div>' . $observaciones . '</div>
        </div>

        <div class="clearfix"></div>

        <!-- FOOTER CON FIRMAS -->
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