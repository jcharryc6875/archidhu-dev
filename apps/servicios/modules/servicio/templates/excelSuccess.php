<?php
header("Content-type: application/vnd.ms-excel");
header("Content-disposition: attachment; filename=resultado.xls"); 
?>
<h1>Solicitudes de Servicio</h1>

<table border="1">
    <tr>
        <td align="center">Num. Servicio</td>
        <td align="center">Radicado</td>
        <td align="center">Usuario Solicita</td>
        <td align="center">Destino</td>
        <td align="center">Direccion</td>
        <td align="center">Detalle Del Servicio</td>
        <td align="center">Fecha Solicitud</td>
        <td align="center">Estado</td>
        <td align="center">Folios</td>
        <td align="center">Firma Recibido</td>
    </tr>
    <?php 
    while($object = $resultset->fetch()){ ?>
    <tr>
          <td><?php echo $object[7]; ?></td>	               
          <td><?php echo $object[0]; ?></td>
          <td><?php echo $object[1]." ".$object[2]; ?></td>
          <td><?php echo $object[3]; ?></td>
          <td><?php echo $object[9]; ?></td>
          <td><?php echo $object[10]; ?></td>
          <td><?php echo $object[4]; ?></td>
          <td><?php echo $object[6]; ?></td>
          <?php $est =  $object[5]; ?>      
          <td><?php echo $object[9]; ?></td>
          <td>_________________________</td>
    </tr>
    <?php } ?>
</table>