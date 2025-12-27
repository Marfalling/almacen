<?php
$html = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Interno de Compra</title>
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
        
        .header {
            position: relative;
            margin-bottom: 15px;
            border: 2px solid #000;
            padding: 10px;
            height: 75px;
            background-color: #fff;
        }
        
        .logo {
            position: absolute;
            left: 10px;
            top: 10px;
            width: 70px;
            height: 45px;
        }
        
        .logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        
        .titulo {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            margin-top: 18px;
            text-transform: uppercase;
            color: #000;
        }
        
        .info-documento {
            position: absolute;
            right: 10px;
            top: 10px;
            border: 1px solid #000;
            font-size: 7pt;
            background-color: #f8f8f8;
        }
        
        .info-documento div {
            padding: 2px 6px;
            border-bottom: 1px solid #000;
            text-align: left;
            background-color: #fff;
            min-width: 100px;
        }
        
        .info-documento div:last-child {
            border-bottom: none;
        }
        
        .codigo-pedido {
            text-align: left;
            margin-bottom: 12px;
            font-size: 10pt;
            font-weight: bold;
            padding: 6px 8px;
            border: 1px solid #000;
            background-color: #f0f0f0;
        }
        
        .info-general {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 7.5pt;
            border: 2px solid #000;
        }
        
        .info-general td {
            border: 1px solid #000;
            padding: 3px 6px;
            height: 20px;
            vertical-align: middle;
        }
        
        .info-general .label {
            background-color: #e8e8e8;
            font-weight: bold;
            width: 25%;
            text-align: left;
        }
        
        .info-general .value {
            width: 25%;
            background-color: #fff;
        }
        
        .detalles-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 7.5pt;
            border: 2px solid #000;
        }
        
        .detalles-table th {
            background-color: #d0d0d0;
            border: 1px solid #000;
            padding: 5px 3px;
            text-align: center;
            font-weight: bold;
            font-size: 7.5pt;
            height: 28px;
            line-height: 1.1;
        }
        
        .detalles-table td {
            border: 1px solid #000;
            padding: 4px 5px;
            vertical-align: top;
            background-color: #fff;
        }
        
        .text-center {
            text-align: center;
        }
        
        .item-col {
            width: 5%;
            text-align: center;
        }
        
        .cantidad-col {
            width: 7%;
            text-align: center;
        }
        
        .unidad-col {
            width: 7%;
            text-align: center;
        }
        
        .descripcion-col {
            width: 28%;
            text-align: left;
            padding-left: 6px;
        }
        
        .centro-costo-col {
            width: 15%;
            text-align: left;
            padding-left: 6px;
            font-size: 7pt;
        }
        
        .ot-col {
            width: 8%;
            text-align: center;
        }
        
        .comentarios-col {
            width: 30%;
            text-align: left;
            padding-left: 6px;
            font-size: 7pt;
        }
        
        .comentarios-texto {
            margin-bottom: 5px;
            line-height: 1.3;
        }
        
        .imagenes-container {
            margin-top: 5px;
            padding: 3px;
        }
        
        .imagen-item {
            display: inline-block;
            margin: 2px;
            vertical-align: top;
        }
        
        .imagen-item img {
            width: auto;
            height: auto;
            max-width: 100px;
            max-height: 100px;
            border: 1px solid #666;
            object-fit: contain;
        }
        
        .imagen-item.inline img {
            max-width: 80px;
            max-height: 80px;
        }
        
        .imagen-item.small img {
            max-width: 60px;
            max-height: 60px;
        }
        
        .seccion {
            margin-bottom: 12px;
            border: 2px solid #000;
        }
        
        .seccion-titulo {
            background-color: #d0d0d0;
            border-bottom: 1px solid #000;
            padding: 5px 8px;
            font-weight: bold;
            font-size: 8pt;
            text-transform: uppercase;
        }
        
        .seccion-contenido {
            padding: 8px 10px;
            min-height: 40px;
            font-size: 7.5pt;
            background-color: #fff;
            line-height: 1.3;
        }
        
        .footer {
            text-align: center;
            font-size: 7pt;
            color: #666;
            padding: 5px 0;
            margin-top: 10px;
            border-top: 1px solid #ccc;
        }
        
        .detalles-table tr {
            page-break-inside: avoid;
        }

        .comentarios-col {
            max-height: 250px;
            overflow: hidden;
        }
        
        .detalles-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .comentarios-texto strong {
            color: #000;
            font-weight: bold;
            font-size: 7.5pt;
        }
        
        .personal-item {
            margin-left: 8px;
            line-height: 1.5;
            padding: 1px 0;
        }
        
        .comentarios-texto > div {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            ' . ($imagenLogoBase64 ? '<div class="logo"><img src="' . $imagenLogoBase64 . '" alt="Logo"></div>' : '<div class="logo" style="border: 1px solid #000; text-align: center; line-height: 45px; font-size: 8pt; font-weight: bold;">LOGO</div>') . '
            
            <div class="titulo">Pedido Interno de Compra</div>
            
            <div class="info-documento">
                <div>COM-PR-001-02</div>
                <div>Versión: 00</div>
                <div>Fecha: ' . date('d.m.Y') . '</div>
                <div>Página 1 de 1</div>
            </div>
        </div>
        
        <div class="codigo-pedido">
            Código de pedido: N° ' . htmlspecialchars($codigo_pedido, ENT_QUOTES, 'UTF-8') . '
        </div>
        
        <table class="info-general">
            <tr>
                <td class="label">OBRA/ÁREA:</td>
                <td class="value" colspan="3">' . htmlspecialchars($nombre_obra, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
            <tr>
                <td class="label">SOLICITANTE:</td>
                <td class="value">' . htmlspecialchars($nom_personal, ENT_QUOTES, 'UTF-8') . '</td>
                <td class="label">FECHA SOLICITUD:</td>
                <td class="value">' . htmlspecialchars($fecha_solicitud, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
            <tr>
                <td class="label">CENTRO COSTO SOLICITANTE:</td>
                <td class="value">' . htmlspecialchars($centro_costo_solicitante, ENT_QUOTES, 'UTF-8') . '</td>
                <td class="label">FECHA REQUERIDA:</td>
                <td class="value">' . htmlspecialchars($fecha_requerida, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
            <tr>
                <td class="label">TELÉFONO:</td>
                <td class="value">' . htmlspecialchars($telefono, ENT_QUOTES, 'UTF-8') . '</td>
                <td class="label">LUGAR ENTREGA:</td>
                <td class="value">' . htmlspecialchars($lugar_entrega, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
            <tr>
                <td class="label">ALMACÉN:</td>
                <td class="value">' . htmlspecialchars($almacen, ENT_QUOTES, 'UTF-8') . '</td>
                <td class="label">UBICACIÓN:</td>
                <td class="value">' . htmlspecialchars($ubicacion, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
        </table>
        
        <table class="detalles-table">
            <thead>
                <tr>
                    <th class="item-col">ITEM</th>
                    <th class="cantidad-col">CANT.</th>
                    <th class="unidad-col">UNIDAD</th>
                    <th class="descripcion-col">DESCRIPCIÓN DETALLADA</th>
                    <th class="centro-costo-col">CENTRO COSTO</th>
                    <th class="ot-col">OT</th>
                    <th class="comentarios-col">COMENTARIOS</th>
                </tr>
            </thead>
            <tbody>
                ' . $detalles_html . '
            </tbody>
        </table>
        
        <div class="seccion">
            <div class="seccion-titulo">Aclaraciones sobre la solicitud y entrega:</div>
            <div class="seccion-contenido">' . htmlspecialchars($aclaraciones, ENT_QUOTES, 'UTF-8') . '</div>
        </div>
    </div>
    
    <div class="footer">
        Generado el ' . $fecha_formateada . ' 
    </div>
</body>
</html>';

?>