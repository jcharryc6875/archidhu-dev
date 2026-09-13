<?php
require_once(sfConfig::get('sf_lib_dir')."/barcode/applib_barcode.php");
?>
<table class=Sticker >
<tbody>
<tr>
   <td colSpan="2">Unidad Administrativa: </td>
   <td ><?php echo $cliente->getSubserie()->getSerie()->getDependencia(); ?></td>
</tr>
<tr>
   <td colspan="2">Serie: </td>
   <td><?php echo $cliente->getSubserie()->getSerie(); ?></td>
</tr>
<tr>
	<td colspan="2">Subserie: </td>
	<td><?php echo $cliente->getSubserie(); ?></td>
</tr>
<tr>
	<td colspan="2">Nombre Cliente: </td>
	<td><?php echo $cliente->getNombreCliente(); ?></td>
  </tr>
<tr>
	<td colspan="2">Fecha apertura: </td>
	<td><?php echo $cliente->getFechaApertura(); ?></td>
</tr>
<tr>	
    <td colspan="2">Responsable: </td>
    <td><?php echo $nombre_responsable; ?></td>
</tr>
<tr>
  <td colspan="2">
  <?php
		  
		    $sticker_dir="./";
		    $cadena = $cliente->getCodigoCliente();
            if($cadena!=""){
                $nombre_imagen=getBarcode( $cadena ,"");
            }
			
		
			echo "<img src=\"/simad/tmp/$nombre_imagen\"/>";
		  ?> </td>
  </tr>
 </tbody> 
</table>