<?php
header("Content-type: application/vnd.ms-excel");
header("Content-disposition: attachment; filename=resultado.xls");
?>
<table border="1" align="center">
<h3 align="center">
REPORTE DE DOCUMENTOS
  </h3>
<tr>
<th>Ubicaci&oacute;n: </th>
<td align="center"><?php echo $usuario_name->getRegional() ?></td>
</tr>
<tr>
<th>Fecha: </th>
<td align="center"><?php echo date("Y-m-d") ?></td>
</tr>
<tr>
<th>Hora: </th>
<td align="center"><?php echo date("G:i:s") ?></td>
</tr>
<tr>
<th>Funcionario: </th>
<td align="center"><?php echo $usuario_name->getUsername() ?></td>
</tr>

<tr>
<th>Proveedor: </th>
<td align="center"><b><?php echo $periodo_validez->getProveedor()->getNombre() ?></b></td>
</tr>

<tr>
<th>Nit: </th>
<td align="center"><b><?php echo $periodo_validez->getProveedor()->getNit() ?></b></td>
</tr>

<tr>
<th>Periodo De Validez: </th>
<td align="center"><b><?php echo $periodo_validez->getFechaInicial() ?> al <?php echo $periodo_validez->getFechaFinal() ?></b></td>
</tr>

</table>

<br />    

<table border="1">
<body>
<h4 align="center">Documentos del Proveedor</h4>
<thead>
<tr>            
    <th>Tipo Documento</th>
    <th>Estado Documento</th>
    <th>Fecha Creacion</th>
    <th>Fecha Recibido</th>
    <th>Observaciones</th>
    <th>Adjuntos</th>
</tr>
</thead>
<tbody>  
<?php 
while($object = $resultset->fetch()){ ?>
<tr>          
       <td><?php echo $object[0]; ?></td>
       <td><?php echo $object[1]; ?></td>
       <td><?php echo $object[2]; ?></td>
       <td><?php echo $object[3]; ?></td>
       <td><?php echo $object[4]; ?></td>
       <td><?php echo basename($object[5]) ?></td>
</tr>
<?php } ?>
  </tbody>
</table>