<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Devolución N° <?php echo $numero_devolucion; ?></title>
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

        /* CUADROS */
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

        .titulo-devolucion {
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

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .info-left, .info-right {
            width: 48%;
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
            vertical-align: top;
        }

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

        /* FIRMAS */
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

        .estado-activo { color: #006600; font-weight: bold; }
        .estado-inactivo { color: #cc0000; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <div class="header">
            <div class="header-left">
                <div class="logo">
                    <?php echo ($imagenLogoBase64 ? '<img src="'.$imagenLogoBase64.'" alt="Logo">' : '<div style="border:1px solid #000; height:60px; text-align:center; line-height:60px;">LOGO</div>'); ?>
                </div>
                <div>MONTAJES E INGENIERÍA ARCE PERÚ S.A.C.</div>
            </div>
            <div class="header-right">
                Calle 3, N° 177- Urb la Grimanesa - CALLAO - CALLAO<br>
                Teléf: 572-3220 ANEXO 11 - 12<br>
                RUC: 20550259321
            </div>
        </div>

        <!-- CUADROS -->
        <div class="cuadros-container">
            <div class="estado-section">
                <div class="estado-box">
                    ESTADO:<br>
                    <span class="<?php echo ($estado_texto == 'ACTIVO' ? 'estado-activo' : 'estado-inactivo'); ?>">
                        <?php echo $estado_texto; ?>
                    </span>
                </div>
            </div>

            <div class="titulo-section">
                <div class="titulo-devolucion">
                    DEVOLUCIÓN<br>
                    Nº <?php echo $numero_devolucion; ?>
                </div>
            </div>
        </div>

        <!-- INFORMACIÓN GENERAL -->
        <div class="info-general">
            <div class="info-row">
                <div class="info-left">
                    <div><strong>Fecha de Devolución:</strong> <?php echo $fecha_devolucion; ?></div>
                    <div><strong>Registrado por:</strong> <?php echo htmlspecialchars($nom_personal); ?></div>
                </div>
                <div class="info-right">
                    <div><strong>Almacén:</strong> <?php echo htmlspecialchars($almacen); ?></div>
                    <div><strong>Ubicación:</strong> <?php echo htmlspecialchars($ubicacion); ?></div>
                </div>
            </div>
        </div>

        <!-- TABLA DE DETALLES -->
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
                <?php echo $detalles_html; ?>
            </tbody>
        </table>

        <!-- OBSERVACIONES -->
        <div class="observaciones-section">
            <div class="observaciones-header">OBSERVACIONES:</div>
            <div><?php echo htmlspecialchars($observaciones); ?></div>
        </div>

        <!-- FOOTER CON FIRMAS -->
        <div class="footer">
            <div class="firmas-section">
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
                Fecha <?php echo $fecha_formateada; ?>
            </div>
        </div>
    </div>
</body>
</html>