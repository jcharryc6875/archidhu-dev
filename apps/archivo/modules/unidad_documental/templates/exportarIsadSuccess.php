<?php

	header("Content-type: application/vnd.ms-excel");
	header("Content-disposition: attachment; filename=resultado.xls"); 
?>
<table width="467" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td colspan="2">REPORTE DE ARCHIVO CENTRAL ISAD(G) </td>
    </tr>
    <tr>
        <td>Fecha Reporte </td>
        <td><?php echo date("Y-m-d") ?></td>
    </tr>
    <tr>
        <td>Hora Reporte </td>
        <td><?php echo date("G:i:s") ?></td>
    </tr>
    <tr>
        <td>Usuario</td>
        <td><?php echo $username;?></td>
    </tr>
</table>
<br />
<table border="1">      
    <tr>
      <td nowrap="nowrap" align="center">Codigo de Barras</td>
      <td nowrap="nowrap" align="center">Titulo</td>
      <td nowrap="nowrap" align="center">Fecha Inicial</td>
      <td nowrap="nowrap" align="center">Fecha Final</td>                  
      <td nowrap="nowrap" align="center">Unidad Administrativa</td>
      <td nowrap="nowrap" align="center">Serie</td>
	  <td nowrap="nowrap" align="center">Subserie</td>
      <td nowrap="nowrap" align="center">Nivel De Descripcion</td>
      <td nowrap="nowrap" align="center">Codigo Referencia</td>
      <td nowrap="nowrap" align="center">Titulo Atribuido</td>
      <td nowrap="nowrap" align="center">Fecha Acumulacion</td>
      <td nowrap="nowrap" align="center">Nombre Productor</td>
      <td nowrap="nowrap" align="center">Rese&ntilde;;a Historica</td>
      <td nowrap="nowrap" align="center">Historia Archivostica</td>
      <td nowrap="nowrap" align="center">Forma De Ingreso</td>
      <td nowrap="nowrap" align="center">Alcanze Contenido</td> 
      <td nowrap="nowrap" align="center">Valoracion,Seleccion o Eliminacion</td>
      <td nowrap="nowrap" align="center">Nuevos Ingresos</td>
      <td nowrap="nowrap" align="center">Organizacion</td>
      <td nowrap="nowrap" align="center">Condiciones Acceso</td>
      <td nowrap="nowrap" align="center">Condiciones Reproduccion</td>
      <td nowrap="nowrap" align="center">Idioma</td>
      <td nowrap="nowrap" align="center">Caracteristicas Fisicas</td>
      <td nowrap="nowrap" align="center">Instrumentos Descripcion</td>
      <td nowrap="nowrap" align="center">Localizacion Originales</td>
      <td nowrap="nowrap" align="center">Localizacion Copias</td>
      <td nowrap="nowrap" align="center">Ex�dientes Relacionados</td>
      <td nowrap="nowrap" align="center">Notas De La Descripcion</td>
      <td nowrap="nowrap" align="center">Notas</td>
      <td nowrap="nowrap" align="center">Nota Archivista</td>
      <td nowrap="nowrap" align="center">Reglas o Norms</td>
      <td nowrap="nowrap" align="center">Fechas Descripcion</td>
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
      <td><?php echo $object[38]; ?></td>
      <td><?php echo $object[14]; ?></td>
      <td><?php echo $object[15]; ?></td>
      <td><?php echo $object[16]; ?></td>
      <td><?php echo $object[17]; ?></td>
      <td><?php echo $object[18]; ?></td>
      <td><?php echo $object[19]; ?></td>
      <td><?php echo $object[20]; ?></td>
      <td><?php echo $object[21]; ?></td>
      <td><?php echo $object[22]; ?></td>
      <td><?php echo $object[23]; ?></td>
      <td><?php echo $object[24]; ?></td>
      <td><?php echo $object[25]; ?></td>
      <td><?php echo $object[26]; ?></td>
      <td><?php echo $object[27]; ?></td>
      <td><?php echo $object[28]; ?></td>
      <td><?php echo $object[29]; ?></td>
      <td><?php echo $object[30]; ?></td>
      <td><?php echo $object[31]; ?></td>
      <td><?php echo $object[32]; ?></td>
      <td><?php echo $object[33]; ?></td>
      <td><?php echo $object[34]; ?></td>
      <td><?php echo $object[35]; ?></td>
      <td><?php echo $object[36]; ?></td>
      <td><?php echo $object[37]; ?></td>              
    </tr>
<?php } ?>
</table>
            