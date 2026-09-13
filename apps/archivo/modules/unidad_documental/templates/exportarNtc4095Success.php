<?php

	header("Content-type: application/vnd.ms-excel");
	header("Content-disposition: attachment; filename=resultado.xls"); 
?>
<table width="467" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2">REPORTE DE ARCHIVO CENTRAL ISAD(G) </td>
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
      <td nowrap="nowrap" align="center">Codigo de Barras</td>
      <td nowrap="nowrap" align="center">Titulo</td>
      <td nowrap="nowrap" align="center">Fecha Apertura</td>
      <td nowrap="nowrap" align="center">Fecha Cierre</td>                  
      <td nowrap="nowrap" align="center">Unidad Administrativa</td>
      <td nowrap="nowrap" align="center">Serie</td>
	  <td nowrap="nowrap" align="center">Subserie</td>
      <td nowrap="nowrap" align="center">Nivel De Descripcion</td>
      <td nowrap="nowrap" align="center">Area De Descripcion</td>
      <td nowrap="nowrap" align="center">Campo Descrito</td>
      <td nowrap="nowrap" align="center">Valor Campo</td>                  
    </tr>               
    <?php
    $x = 0;     
    while($object = $unidad_documental->fetch()){ 
    ?>  
    <tr>  
	  <td nowrap="nowrap" align="center"><?php echo $object[0]; ?></td>
      <td><?php echo $object[1]; ?></td>
 	  <td><?php echo $object[2]; ?></td>
 	  <td><?php echo $object[3]; ?></td>
 	  <td><?php echo $object[4]; ?></td>
 	  <td><?php echo $object[5]; ?></td>
  	  <td><?php echo $object[6]; ?></td>
      <td><?php echo $object[14]; ?></td>
      <td><?php echo $object[15]; ?></td>
      <td><?php echo $object[16]; ?></td>
      <td><?php echo $object[17]; ?></td>                           
    </tr>
    <?php } ?>
</table>
            