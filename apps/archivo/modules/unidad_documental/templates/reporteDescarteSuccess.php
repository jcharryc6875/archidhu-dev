<?php    
	header("Content-type: application/vnd.ms-excel");
	header("Content-disposition: attachment; filename=reporte_descarte_documental.xls");
    $simad_util = new simad_util(); 
?>

<h2>REPORTE ARCHIVO CENTRAL DESCARTE DOCUMENTAL</h2>              
<table width="467" border="0" cellspacing="0" cellpadding="0" border="1">              
  <tr>
    <td>Fecha Reporte </td>
    <td><?php echo date("Y-m-d") ?></td>
  </tr>
  <tr>
    <td >Hora Reporte </td>
    <td ><?php echo date("G:i:s") ?></td>
  </tr>
  <tr>
    <td>Usuario</td>
    <td><?php echo $username;?></td>
  </tr>
</table>
<br />
<table border="1">
    <tr>                  
      <td nowrap="nowrap" align="center"><b>ID Expediente</b></td>
      <td nowrap="nowrap" align="center"><b>Nombre Expediente</b></td>
      <td nowrap="nowrap" align="center"><b>Fecha Inicial</b></td>
      <td nowrap="nowrap" align="center"><b>Fecha Final</b></td>
      <td nowrap="nowrap" align="center"><b>Fecha Vencimiento</b></td>
      <td nowrap="nowrap" align="center"><b>Unidad Administrativa</b></td>
      <td nowrap="nowrap" align="center"><b>Serie</b></td>
	  <td nowrap="nowrap" align="center"><b>Subserie</b></td>
      <td nowrap="nowrap" align="center"><b>Ubicacion</b></td>
      <td nowrap="nowrap" align="center"><b>Disposicion Final</b></td>                  
    </tr>               
      <?php $x = 0;
      while($object = $resultset->fetch()){ ?>  
    <tr>  
	  <td nowrap="nowrap" align="center"><?php echo $object[0]; ?></td>
      <td><?php echo $object[1]; ?></td>
 	  <td><?php echo $object[2]; ?></td>
 	  <td><?php echo $object[3]; ?></td>
      <td><?php echo $object[14]; ?></td>
 	  <td><?php echo $object[4]; ?></td>
 	  <td><?php echo $object[5]; ?></td>
  	  <td><?php echo $object[6]; ?></td>
      <td><?php echo $object[9]; ?></td>
      <td><?php echo $simad_util->getAllDescartes($object[15]); ?></td>                                         
    </tr>
    <?php } ?>
</table>
            