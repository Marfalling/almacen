<?php
header("Content-Type: application/vnd.ms-excel");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment;filename=RptInspeccionesAmbiente.xls");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body>

<!-- ------------------------------------------------------------------------------------------------------------------------------- -->

<div class="table-responsive">
    
    <table border="0" class="table table-bordered table-hover table-condensed " >
    
    
    <tr>
        <th bgcolor="#D8D8FC" style="text-align: center">#</th>
        <th bgcolor="#D8D8FC" style="text-align: center">DOI Tipo</th>
        <th bgcolor="#D8D8FC" style="text-align: center">DOI Número</th>
        <th bgcolor="#D8D8FC" style="text-align: center">Tipo Abono</th>
        <th bgcolor="#D8D8FC" style="text-align: center">N° Cuentas a Abonar</th>
        <th bgcolor="#D8D8FC" style="text-align: center">Nombre de Beneficiario</th>
        <th bgcolor="#D8D8FC" style="text-align: center">Importe Abonar</th>
        <th bgcolor="#D8D8FC" style="text-align: center">Tipo Recibo</th>
        <th bgcolor="#D8D8FC" style="text-align: center">N° Documento</th>
        <th bgcolor="#D8D8FC" style="text-align: center">Abono agrupado</th>
        <th bgcolor="#D8D8FC" style="text-align: center">Referencia Orden</th>
        <th bgcolor="#D8D8FC" style="text-align: center">Referencia Comprobante</th>
        <th bgcolor="#D8D8FC" style="text-align: center">Indicador Aviso</th>
        <th bgcolor="#D8D8FC" style="text-align: center">Medio de aviso</th>
        <th bgcolor="#D8D8FC" style="text-align: center">Persona Contacto</th>
        <th bgcolor="#D8D8FC" style="text-align: center">Validación</th>
        
    </tr>
    
    <?php
	$c=0;
    foreach ($consulta as $row): 
    $c++;

           $doi_tipo = $row['doi_tipo'];
           $doi_numero = $row['doi_numero'];
           $tipo_abono = $row['tipo_abono'];
           $nro_cuenta = $row['nro_cuenta'];
           $beneficiario = $row['beneficiario'];
           $importe_abonar = $row['importe_abonar'];
           $tipo_recibo = $row['tipo_recibo'];
           $numero_documento = $row['numero_documento'];
           $abono_agrupado = $row['abono_agrupado'];
           $ref_orden_compra = $row['ref_orden_compra'];
           $ref_comprobante = $row['ref_comprobante'];
           $indicador_aviso = $row['indicador_aviso'];
           $medio_aviso = $row['medio_aviso'];
           $persona_contacto = $row['persona_contacto'];

	?>
    
    <tr>
         <td align="center"><?php echo $c; ?></td>
         
        
        <td><?php echo $doi_tipo; ?></td>   
        <td><?php echo $doi_numero; ?></td>  
        <td><?php echo $tipo_abono; ?></td>  
        <td><?php echo $nro_cuenta; ?></td>
        <td><?php echo $beneficiario; ?></td>  
        <td><?php echo $importe_abonar; ?></td>
        <td><?php echo $tipo_recibo; ?></td>
        <td><?php echo $numero_documento; ?></td>  
        <td><?php echo $abono_agrupado; ?></td>  
        <td><?php echo $ref_orden_compra; ?></td>    
        <td><?php echo $ref_comprobante; ?></td>  
        <td><?php echo $indicador_aviso; ?></td>  
        <td><?php echo $medio_aviso; ?></td>  
        <td><?php echo $persona_contacto; ?></td>
         
    </tr>
    
    <?php endforeach; ?>
    
    </table>
    
</div>



</body>
</html>