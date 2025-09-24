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

        /* ESTADO DE LA SALIDA */
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

        /* TÍTULO Y NÚMERO DE SALIDA */
        .titulo-section {
            position: absolute;
            top: 0;
            right: 0;
            width: auto;
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

        /* INFORMACIÓN GENERAL - REORGANIZADA EN DOS FILAS */
        .info-general {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 15px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .info-left, .info-right {
            width: 48%;
        }

        /* SECCIÓN ORIGEN Y DESTINO */
        .origen-destino-section {
            margin-bottom: 15px;
        }

        .origen-destino-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .origen-box, .destino-box {
            width: 48%;
            border: 1px solid #000;
            padding: 10px;
            min-height: 120px;
        }

        .origen-header, .destino-header {
            background-color: #e8f4f8;
            padding: 5px;
            margin: -10px -10px 10px -10px;
            border-bottom: 1px solid #000;
            font-weight: bold;
            text-align: center;
        }

        .origen-header {
            background-color: #fff2e8;
        }

        .destino-header {
            background-color: #e8f8e8;
        }

        /* TABLA DE MATERIALES */
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
        
        .col-item { width: 10%; }
        .col-descripcion { width: 60%; }
        .col-cantidad { width: 15%; }
        .col-unidad { width: 15%; }

        /* OBSERVACIONES */
        .observaciones-section {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 20px;
            min-height: 60px;
        }

        .observaciones-header {
            font-weight: bold;
            margin-bottom: 8px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
        }

        /* SECCIÓN DE FIRMAS */
        .firmas-section {
            margin-top: 30px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .firma-box {
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
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        /* UTILIDADES */
        .field-label {
            font-weight: bold;
            display: inline-block;
            min-width: 120px;
        }

        .field-value {
            display: inline-block;
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

        /* ESTADO */
        .estado-activo {
            color: #006600;
            font-weight: bold;
        }

        .estado-inactivo {
            color: #cc0000;
            font-weight: bold;
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
            <!-- ESTADO DE LA SALIDA -->
            <div class="estado-section">
                <div class="estado-box">
                    ESTADO:<br>
                    <span class="' . ($estado_texto == 'ACTIVO' ? 'estado-activo' : 'estado-inactivo') . '">' . $estado_texto . '</span>
                </div>
            </div>

            <!-- TÍTULO DE SALIDA -->
            <div class="titulo-section">
                <div class="titulo-salida">
                    SALIDA DE MATERIALES<br>
                    S' . $numero_salida . '
                </div>
            </div>
        </div>

        <!-- INFORMACIÓN GENERAL - REORGANIZADA -->

        <div class="info-general">
            <div class="info-row">
                <div class="info-left">
                    <div><strong>N° DOCUMENTO:</strong> ' . $ndoc_salida . '</div>
                    <div><strong>TIPO DE MATERIAL:</strong> ' .$tipo_material. '</div>
                </div>
                <div class="info-right">
                    <div><strong>FECHA DE SALIDA:</strong> ' .$fecha_salida. '</div>
                    <div><strong>FECHA REQUERIDA:</strong> ' .$fecha_requerida. '</div>
                    <div><strong>REGISTRADO POR:</strong> ' .$nom_personal. '</div>
                </div>
            </div>
        </div>

        <table style="width:100%; border-collapse:collapse; margin-bottom:15px; font-size:8pt;">
            <tr>
                <!-- ORIGEN -->
                <td style="width:50%; vertical-align:top; border:1px solid #000; padding:8px;">
                    <div style="background:#fff2e8; font-weight:bold; text-align:center; padding:5px; margin:-8px -8px 8px -8px; border-bottom:1px solid #000;">
                         ORIGEN
                    </div>
                    <div><strong>ALMACÉN:</strong> ' . $almacen_origen . '</div>
                    <div><strong>UBICACIÓN:</strong> ' . $ubicacion_origen . '</div>
                    <div><strong>PERSONAL ENCARGADO:</strong> ' . $personal_encargado . '</div>
                </td>

                <!-- DESTINO -->
                <td style="width:50%; vertical-align:top; border:1px solid #000; padding:8px;">
                    <div style="background:#e8f8e8; font-weight:bold; text-align:center; padding:5px; margin:-8px -8px 8px -8px; border-bottom:1px solid #000;">
                         DESTINO
                    </div>
                    <div><strong>ALMACÉN:</strong> ' . $almacen_destino . '</div>
                    <div><strong>UBICACIÓN:</strong> ' . $ubicacion_destino . '</div>
                    <div><strong>PERSONAL QUE RECIBE:</strong> ' . $personal_recibe . '</div>
                </td>
            </tr>
        </table>



        <!-- TABLA DE MATERIALES -->
        <table class="detalles-table">
            <thead>
                <tr>
                    <th class="col-item">ÍTEM</th>
                    <th class="col-descripcion">DESCRIPCIÓN DEL MATERIAL</th>
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

            <div style="margin-top:20px; font-size:7pt; color:#666;">
                Fecha ' . $fecha_formateada . '
            </div>
        </div>

    </div>
</body>
</html>';
?>