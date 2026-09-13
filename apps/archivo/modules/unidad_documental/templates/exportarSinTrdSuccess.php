<?php

	header("Content-type: application/vnd.ms-excel");
	header("Content-disposition: attachment; filename=resultado.xls"); 
?>
<table width="467" border="0" cellspacing="0" cellpadding="0" border="1">
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
    <tr >      
        <td nowrap="nowrap" align="center">Codigo de Barras</td>
        <td nowrap="nowrap" align="center">Titulo</td>
        <td nowrap="nowrap" align="center">Fecha Apertura</td>
        <td nowrap="nowrap" align="center">Fecha Cierre</td>                  
        <td nowrap="nowrap" align="center">Unidad Administrativa</td>
        <td nowrap="nowrap" align="center">Serie</td>
        <td nowrap="nowrap" align="center">Subserie</td>
        <td nowrap="nowrap" align="center">Folios</td>				  
        <td nowrap="nowrap" align="center">Responsable</td>
        <?php if($portransferir != 1){?>
            <td nowrap="nowrap" align="center">Folios</td>
            <td nowrap="nowrap" align="center">Soporte</td>
            <td nowrap="nowrap" align="center">Ubicacion</td>
        <?php }else{
		    echo "<td>Recibido Por</td>";
	    } ?>
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
        <td><?php echo $object[8]; ?></td>		  	  
        <td><?php echo $username; ?></td>    			 			 
        <?php if($portransferir != 1){?>
          <td><?php echo $object[8]; ?></td>                  
          <td><?php echo $object[13]; ?></td>
          <?php }else{
              echo "<td>_______________</td> ";					
            } 
          ?>       
        <td>
        <?php        
        switch ($object[12]){
        case 1:
        	echo $object[10];       			
        	break;
        case 2:
        	echo $object[9]; 
        	break;
        case 3:
        	echo $object[11]; 
        	break;
        default:
        	echo "";
        	break;
        }
        ?>
        </td> 
    </tr>
    <?php } ?>
</table>
            