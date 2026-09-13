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
      <td>Notas</td>
      <?php if($portransferir != 1){?>
        <td>Soporte</td>
        <td>Ubicaci&oacute;n</td>
      <?php }else{
		    echo "<td>Recibido Por</td>";
      } ?>
	    <td>Usuario Creador</td>
      <td>N&uacute;mero Caja</td>
      <td>N&uacute;mero Carpeta</td>
      <td>Ubicaci&oacute;n Topogr&aacute;fica</td>
    </tr>
   
    <?php 
	$x = 0;     
    while($object = $resultset->fetch()){ 
    ?>  
    <tr>
        <td><?php echo $object[7]; ?></td>
        <td><?php echo $object[0]; ?></td>
        <td style="mso-number-format:'@';"><?php echo htmlentities($object[1]); ?></td>
        <td><?php echo date("Y-m-d",strtotime($object[2])); ?></td>
        <td><?php echo trim($object[3]) ? date("Y-m-d",strtotime($object[3])) : ""; ?></td>
        <td><?php echo htmlentities($object[4]); ?></td>
        <td><?php echo htmlentities($object[5]); ?></td>
        <td><?php echo htmlentities($object[6]); ?></td>
        <td><?php echo $object[8]; ?></td>
        <td><?php echo htmlentities($object[24]); ?></td>       
        <?php if($portransferir != 1){?>
          <td><?php echo htmlentities($object[13]); ?></td>
        <?php }else{
              echo "<td>_______________</td> ";					
        } ?>
        <td>
          <?php  
            switch ($object[12]){
              case 1:
                  echo htmlentities($object[10]);     			
                  break;
              case 2:
                  echo htmlentities($object[9]); 
                  break;
              case 3:
                  echo htmlentities($object[11]); 
                  break;
              default:
                  echo "";
                  break;
            }
          ?>
		</td>
		<td><?php echo htmlentities($object['NOMBRE_CREADOR']); ?></td>
    <td><?php echo htmlentities($object['NUMERO_CAJA']); ?></td>
    <td><?php echo htmlentities($object['NUMERO_CARPETA']); ?></td>
    <td><?php echo sprintf('BODEGA_%s/CUERPO_%s/TORRE_%s/PISO_%s', $object['GEO_BODEGA'], $object['GEO_CUERPO'], $object['GEO_TORRE'], $object['GEO_PISO']); ?></td>
    </tr>
    <?php } ?>
</table>