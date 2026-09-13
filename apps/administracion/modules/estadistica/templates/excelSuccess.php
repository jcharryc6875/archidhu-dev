<?php
    header("Content-type: application/vnd.ms-excel");
    header("Content-disposition: attachment; filename=resultado.xls");
?>
<table cellspacing="0" width="98%" align=center border="1">
<h1 align="center">Estadisticas SIMAD 4.0</h1>
<tr>
  <th>Periodo</th>
  <th><?php echo $fecha_inicial;?></th>
  <th><?php echo $fecha_final;?></th>
  <th></th>
  <th></th>
</tr>

<tr>  
  <th align="center">Orden</th>
  <th align="center">Modulo</th>
  <th align="center">Nombre</th>
  <th align="center">Descripcion</th>
  <th align="center">Cantidad</th>
</tr>
</thead>
<tbody>
<?php 
//while ($estadistica->next()){
$i = 0;
while($estadistica = $resultset->fetch()){
?>
<tr>
  <th align="center" ><?php echo $estadistica[1] ?></th>
  <td align="left"><?php echo $estadistica[2] ?></td>       
  <td align="left"><?php echo $estadistica[3] ?></td>       
  <td align="left"><?php echo $estadistica[4] ?></td>       
  <td align="center"><?php echo EstadisticaPeer::getCantidad($estadistica[5],$fecha_inicial,$fecha_final);$i++; ?></td>     
</tr>
<?php } ?>
</tbody>
</table>
