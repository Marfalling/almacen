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
            margin: 15mm 10mm 15mm 10mm; /* top right bottom left */
            size: A4;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #000;
            padding: 5px; /* Padding adicional al body */
            max-width: 100%;
        }
        
        .container {
            max-width: 100%;
            margin: 0 auto;
        }
        
        .header {
            position: relative;
            margin-bottom: 25px;
            border: 2px solid #000;
            padding: 15px;
            height: 90px;
            background-color: #fff;
        }
        
        .logo {
            position: absolute;
            left: 15px;
            top: 15px;
            width: 80px;
            height: 50px;
        }
        
        .logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        
        .titulo {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin-top: 25px;
            text-transform: uppercase;
            color: #000;
        }
        
        .info-documento {
            position: absolute;
            right: 15px;
            top: 15px;
            border: 1px solid #000;
            font-size: 8pt;
            background-color: #f8f8f8;
        }
        
        .info-documento div {
            padding: 3px 8px;
            border-bottom: 1px solid #000;
            text-align: left;
            background-color: #fff;
            min-width: 120px;
        }
        
        .info-documento div:last-child {
            border-bottom: none;
        }
        
        .codigo-pedido {
            text-align: left;
            margin-bottom: 20px;
            font-size: 11pt;
            font-weight: bold;
            padding: 8px 10px;
            border: 1px solid #000;
            background-color: #f0f0f0;
        }
        
        .info-general {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9pt;
            border: 2px solid #000; /* Borde exterior más grueso */
        }
        
        .info-general td {
            border: 1px solid #000;
            padding: 6px 10px;
            height: 28px;
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
            margin-bottom: 20px;
            font-size: 9pt;
            border: 2px solid #000; /* Borde exterior más grueso */
        }
        
        .detalles-table th {
            background-color: #d0d0d0;
            border: 1px solid #000;
            padding: 8px 6px;
            text-align: center;
            font-weight: bold;
            font-size: 9pt;
            height: 35px;
        }
        
        .detalles-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
            background-color: #fff;
            min-height: 25px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .item-col {
            width: 8%;
            text-align: center;
        }
        
        .cantidad-col {
            width: 12%;
            text-align: center;
        }
        .ot-col {
            width: 12%;
            text-align: center;
            position: relative;

        }
        
        .descripcion-col {
            width: 45%;
            text-align: left;
            padding-left: 10px;
        }
        
        .comentarios-col {
            width: 35%;
            text-align: left;
            position: relative;
            padding-left: 10px;
        }
        
        .comentarios-texto {
            margin-bottom: 8px;
            line-height: 1.4;
        }
        
        .imagenes-container {
            margin-top: 8px;
            padding: 5px;
        }
        
        .imagen-item {
            display: inline-block;
            margin: 3px;
            vertical-align: top;
        }
        
        .imagen-item img {
            width: auto;
            height: auto;
            max-width: 120px;
            max-height: 120px;
            border: 1px solid #666;
            object-fit: contain;
        }
        
        .seccion {
            margin-bottom: 20px;
            border: 2px solid #000; /* Borde exterior más grueso */
        }
        
        .seccion-titulo {
            background-color: #d0d0d0;
            border-bottom: 1px solid #000;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 9pt;
            text-transform: uppercase;
        }
        
        .seccion-contenido {
            padding: 12px 15px;
            min-height: 50px;
            font-size: 9pt;
            background-color: #fff;
            line-height: 1.4;
        }
        
        .footer {
            position: fixed;
            bottom: 15mm;
            left: 15mm;
            right: 15mm;
            text-align: center;
            font-size: 8pt;
            color: #666;
            padding: 5px 0;
            border-top: 1px solid #ccc;
        }
        
        .detalles-table tr {
            page-break-inside: avoid;
            max-height: 300px;
        }

        .comentarios-col {
            max-height: 280px;
            overflow: hidden;
        }
        
        /* Estilos adicionales para mejorar la presentación */
        .detalles-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .detalles-table tbody tr:hover {
            background-color: #f0f0f0;
        }

        /* ESTILOS PARA PERSONAL */
        .comentarios-texto strong {
            color: #000;
            font-weight: bold;
            font-size: 9pt;
        }
        
        .personal-item {
            margin-left: 10px;
            line-height: 1.6;
            padding: 2px 0;
        }
        
        .personal-separador {
            margin: 8px 0;
            border-top: 1px dashed #ccc;
        }
        
        /* espaciado entre secciones de comentarios */
        .comentarios-texto > div {
            margin-bottom: 8px;
        }
        
        /* Mejores márgenes para elementos internos */
        table {
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            ' . ($imagenLogoBase64 ? '<div class="logo"><img src="' . $imagenLogoBase64 . '" alt="Logo"></div>' : '<div class="logo" style="border: 1px solid #000; text-align: center; line-height: 50px; font-size: 10pt; font-weight: bold;">LOGO</div>') . '
            
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
                <td class="label">NOMBRE DE LA OBRA/ÁREA:</td>
                <td class="value">' . htmlspecialchars($nombre_obra, ENT_QUOTES, 'UTF-8') . '</td>
                <td class="label">FECHA DE SOLICITUD:</td>
                <td class="value">' . htmlspecialchars($fecha_solicitud, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
            <tr>
                <td class="label">N° DE OT:</td>
                <td class="value">' . htmlspecialchars($ot_pedido, ENT_QUOTES, 'UTF-8') . '</td>
                <td class="label">RECEPCIONISTA:</td>
                <td class="value">' . htmlspecialchars($nom_personal, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
            <tr>
                <td class="label">SOLICITANTE:</td>
                <td class="value">' . htmlspecialchars($nom_personal, ENT_QUOTES, 'UTF-8') . '</td>
                <td class="label">LUGAR DE ENTREGA:</td>
                <td class="value">' . htmlspecialchars($lugar_entrega, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
            <tr>
                <td class="label">TELÉFONO:</td>
                <td class="value">' . htmlspecialchars($telefono, ENT_QUOTES, 'UTF-8') . '</td>
                <td class="label">FECHA REQUERIDA:</td>
                <td class="value">' . htmlspecialchars($fecha_requerida, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
            <tr>
                <td class="label">ALMACÉN:</td>
                <td class="value">' . htmlspecialchars($almacen, ENT_QUOTES, 'UTF-8') . '</td>
                <td class="label">UBICACIÓN:</td>
                <td class="value">' . htmlspecialchars($ubicacion, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
            </tr>
        </table>
        
        <table class="detalles-table">
            <thead>
                <tr>
                    <th class="item-col">ITEM</th>
                    <th class="cantidad-col">CANTIDAD</th>
                    <th class="descripcion-col">DESCRIPCIÓN DETALLADA</th>
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