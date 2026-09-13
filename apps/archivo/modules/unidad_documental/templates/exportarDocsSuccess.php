<?php
	header("Content-type: application/vnd.ms-excel");
	header("Content-disposition: attachment; filename=resultado.xls"); 
?>
<table width="467" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2">REPORTE DE ARCHIVO </td>
  </tr>
  <tr>
    <td >Fecha Reporte </td>
    <td ><?php echo date("Y-m-d") ?></td>
  </tr>
  <tr>
    <td >Hora Reporte </td>
    <td ><?php echo date("G:i:s") ?></td>
  </tr>
  <tr>
    <td>Usuario</td>
    <td ><?php echo $username;?></td>
  </tr>
</table>
<br />
<table border="1">
    <tr>
      <td>Id Expediente</td>
      <td>N&uacute;mero Expediente</td>
      <td>Nombre Expediente</td>
      <td>Fecha Inicial</td>
      <td>Fecha Final</td>                  
      <td>Dependencia</td>
      <td>Serie</td>
      <td>Subserie</td>
      <td>Folios</td>				  
      <td>Soporte</td>
      <td>N&uacute;mero Caja</td>
      <td>N&uacute;mero Carpeta</td>
      <td>Ubicaci&oacute;n Topogr&aacute;fica</td>
      <td>Descripci&oacute;n Documento</td>
      <td>Tipo Documental</td>
      <td>Formato Archivo</td>
      <td>Filesize(bytes)</td>
      <td>Fecha Documento</td>
    </tr>
   
    <?php 
	  $x = 0;     
    while($object = $resultset->fetch()){ 
    ?>  
    <tr>
	    <td><?php echo $object[7]; ?></td>
        <td><?php echo htmlentities($object[0]); ?></td>
        <td style="mso-number-format:'@';"><?php echo htmlentities($object[1]); ?></td>
        <td><?php echo date("Y-m-d",strtotime($object[2])); ?></td>
        <td><?php echo trim($object[3]) ? date("Y-m-d",strtotime($object[3])) : ""; ?></td>
        <td><?php echo htmlentities($object[4]); ?></td>
        <td><?php echo htmlentities($object[5]); ?></td>
        <td><?php echo htmlentities($object[6]); ?></td>
        <td><?php echo $object[17]; ?></td>
        <td><?php echo htmlentities($object[13]); ?></td>
        <td><?php echo htmlentities($object['NUMERO_CAJA']); ?></td>
        <td><?php echo htmlentities($object['NUMERO_CARPETA']); ?></td>
        <td><?php echo sprintf('BODEGA_%s/CUERPO_%s/TORRE_%s/PISO_%s', $object['GEO_BODEGA'], $object['GEO_CUERPO'], $object['GEO_TORRE'], $object['GEO_PISO']); ?></td>
        <td><?php echo htmlentities($object[14]); ?></td>
        <td><?php echo htmlentities($object[15]); ?></td>
        <td><?php echo $object[18]; ?></td>
        <td><?php echo $object[19]; ?></td>
        <td><?php echo trim($object[20]) ? date("Y-m-d",strtotime($object[20])) : ""; ?></td>
    </tr>
    <?php } ?>
</table>