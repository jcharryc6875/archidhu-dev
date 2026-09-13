<?php

	header("Content-type: application/vnd.ms-excel");
	header("Content-disposition: attachment; filename=resultado.xls"); 
?>
<table width="467" border="0" cellspacing="0" cellpadding="0" border="1">
    <tr>
        <td colspan="2">REPORTE DE VINCULACIONES </td>
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
      <td nowrap="nowrap" align="center">Consecutivo</td>
      <td nowrap="nowrap" align="center">Descripcion</td>
      <td nowrap="nowrap" align="center">Folios</td>
      <td nowrap="nowrap" align="center">Fecha Vinculacion</td>                  
      <td nowrap="nowrap" align="center">Codigo Barras</td>
      <td nowrap="nowrap" align="center">Titulo</td>
	  <td nowrap="nowrap" align="center">Tipo Documental</td>
	  <td nowrap="nowrap" align="center">Usuario Vinculo</td>
      <td nowrap="nowrap" align="center">Recibido Por</td>			      			                  
    </tr>
               
    <?php 				
    $x = 0;     
    while($object = $resultset->fetch()){ 
    ?>  
    <tr>    
        <td nowrap="nowrap" align="center"><?php echo $object[0]; ?></td>
        <td><?php echo $object[1]; ?></td>
        <td><?php echo $object[2]; ?></td>
        <td><?php echo $object[3]; ?></td>
        <td><?php echo $object[4]; ?></td>
        <td><?php echo $object[5]; ?></td>
        <td><?php echo $object[6]; ?></td>
        <td><?php echo $object[7].' - '.$object[8]; ?></td>		  	  
        <td><?php echo $username; ?></td>			  
        <td>_______________</td>;
    </tr>
    <?php } ?>
</table>
            