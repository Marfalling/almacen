<?php
header("Content-Type: application/vnd.ms-excel");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment;filename=" . $filename);
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
        <th bgcolor="#993366" style="text-align: center; color: #FFFFFF;">#</th>
        <th bgcolor="#993366" style="text-align: center; color: #FFFFFF;">DOI Tipo</th>
        <th bgcolor="#993366" style="text-align: center; color: #FFFFFF;">DOI Numero</th>
        <th bgcolor="#993366" style="text-align: center; color: #FFFFFF;">Tipo Abono</th>
        <th bgcolor="#993366" style="text-align: center; color: #FFFFFF;">Nro Cuentas a Abonar</th>
        <th bgcolor="#993366" style="text-align: center; color: #FFFFFF;">Nombre de Beneficiario</th>
        <th bgcolor="#993366" style="text-align: center; color: #FFFFFF;">Importe Abonar</th>
        <th bgcolor="#993366" style="text-align: center; color: #FFFFFF;">Tipo Recibo</th>
        <th bgcolor="#993366" style="text-align: center; color: #FFFFFF;">Nro Documento</th>
        <th bgcolor="#993366" style="text-align: center; color: #FFFFFF;">Abono agrupado</th>
        <th bgcolor="#969696" style="text-align: center; color: #FFFFFF;">Moneda</th>
        <th bgcolor="#969696" style="text-align: center; color: #FFFFFF;">Referencia Orden</th>
        <th bgcolor="#969696" style="text-align: center; color: #FFFFFF;">Referencia Comprobante</th>
        <th bgcolor="#969696" style="text-align: center; color: #FFFFFF;">Indicador Aviso</th>
        <th bgcolor="#969696" style="text-align: center; color: #FFFFFF;">Medio de aviso</th>
        <th bgcolor="#969696" style="text-align: center; color: #FFFFFF;">Persona Contacto</th>
        <th bgcolor="#969696" style="text-align: center; color: #FFFFFF;">Estado</th>
        <th bgcolor="#969696" style="text-align: center; color: #FFFFFF;">Validacion</th>
        
    </tr>
    
    <?php
	$c=0;
    foreach ($consulta as $row): 
    $c++;

           $doi_tipo = $row['doi_tipo'];
           $doi_numero = $row['doi_numero'];
           $tipo_abono = $row['tipo_abono'];
           //$nro_cuenta = $row['nro_cuenta'];
           $nro_cuenta = str_replace([' ', '-'], '', $row['nro_cuenta']);
           $beneficiario = $row['beneficiario'];
           $importe_abonar = $row['importe_abonar'];
           $tipo_recibo = $row['tipo_recibo'];
           $numero_documento = $row['numero'];
           $abono_agrupado = $row['abono_agrupado'];

           $id_moneda = $row['id_moneda'];
           // Asignar nombre de moneda según id_moneda
           if ($id_moneda == 1) {
               $nom_moneda = 'SOLES';
           } elseif ($id_moneda == 2) {
               $nom_moneda = 'DOLARES';
           } else {
               $nom_moneda = 'OTROS';
           }

           $ref_orden_compra = $row['ref_orden_compra'];
           $ref_comprobante = $row['ref_comprobante'];
           $indicador_aviso = $row['indicador_aviso'];
           $medio_aviso = $row['medio_aviso'];
           $persona_contacto = $row['persona_contacto'];

           $est_comp = $row['est_comprobante'];
           // Asignar nombre de ESTADO según est_comprobante
           if ($est_comp == 1) {
               $nom_estado = 'PENDIENTE';
           } elseif ($est_comp == 2) {
               $nom_estado = 'POR PAGAR';
           } else {
               $nom_estado = 'PAGADO';
           }

	?>
    
    <tr>
         <td align="center"><?php echo $c; ?></td>
         
        
        <td><?php echo $doi_tipo; ?></td>   
        <td><?php echo $doi_numero; ?></td>  
        <td><?php echo $tipo_abono; ?></td>  
        <td style="mso-number-format:'\@';"><?php echo $nro_cuenta; ?></td>
        <td><?php echo $beneficiario; ?></td>  
        <td><?php echo $importe_abonar; ?></td>
        <!--<td style="mso-number-format:'0.00';"><?php echo number_format($importe_abonar, 2, '.', ''); ?></td>-->
        <td><?php echo $tipo_recibo; ?></td>
        <!--<td><?php echo $numero_documento; ?></td>  -->
        <td style="mso-number-format:'\@';"><?php echo $numero_documento; ?></td>
        <td><?php echo $abono_agrupado; ?></td>
        <td><?php echo $nom_moneda; ?></td>   
        <td><?php echo $ref_orden_compra; ?></td>    
        <td><?php echo $ref_comprobante; ?></td>  
        <td><?php echo $indicador_aviso; ?></td>  
        <td><?php echo $medio_aviso; ?></td>
        <td><?php echo $persona_contacto; ?></td>
        <td><?php echo $nom_estado; ?></td> 
         
    </tr>
    
    <?php endforeach; ?>
    
    </table>
    
</div>



</body>
</html>