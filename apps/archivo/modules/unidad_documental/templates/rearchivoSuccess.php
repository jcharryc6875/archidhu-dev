<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
            
?>
<style>
/*table{
    background-color: grey;
    width: 800px;
    border-bottom: 1px solid black;
    border-spacing: 0;
    border-collapse: collapse;
}
table tr td{
    border-bottom: 1px solid black;
    border-left: 1px solid black;
    border-right: 1px solid black;
}
table tr th{
    border-top: 1px solid black;
    border-bottom: 1px solid black;
    border-left: 1px solid black;
    border-right: 1px solid black;
    font: bold;
    color: black;
}*/
</style>
<h1 style="text-align: left; width: 100%;">Hoja De Control <?php echo ucwords(mb_strtolower($unidad_documental->getLocalizacionunidaddocumental()->getDescripcion())); ?></h1>
<table style="background-color: lightblue;">  
  <thead>
      <tr>
          <th>ID Expediente</th>
          <th>Localizaci&oacute;n</th>
          <th>Fecha Inicial</th>
          <th>Fecha Final</th>
          <th>Responsable</th>
          <th>Folios</th>
          <th>Unidad Administrativa</th>
          <th>Serie</th>
          <th>Subserie</th>                              
      </tr>
  </thead>
  <tbody>
    <tr>
        <td><?php print $unidad_documental->getCodigoBarras(); ?></td>
        <td><?php print "Archivo ".ucwords(mb_strtolower($unidad_documental->getLocalizacionUnidadDocumental())); ?></td>
        <td><?php print $unidad_documental->getFechaApertura(); ?></td>
        <td><?php print $unidad_documental->getFechaCierre(); ?></td>
        <td><?php print $responsable->getUsuario()->getNombre()." ".$responsable->getUsuario()->getApellido(); ?></td>
        <td><?php print $unidad_documental->getFolios(); ?></td>
        <td><?php print $unidad_documental->getSubserie()->getSerie()->getDependencia()->getNombre()." - ".$unidad_documental->getSubserie()->getSerie()->getDependencia()->getEntidad(); ?></td>
        <td><?php print $unidad_documental->getSubserie()->getSerie()->getDescripcion(); ?></td>
        <td><?php print $unidad_documental->getSubserie()->getDescripcion(); ?></td>
    </tr>
  </tbody>                                            
</table>			

<?php
if($cantidad_registros){    
?>
<hr />
<table>
  <thead>
      <tr>
          <th>Tipo Documental</th>
          <th>Descripcion</th>
          <th>Folios</th>
          <th>Fecha Inserci&oacute;n</th>
          <th>Fecha Creaci&oacute;n</th>
      </tr>
  </thead>
  <tbody>
  <?php                     	
   	$c=0;$i=0;
    foreach ($contenido as $contenido_unidad_documental): ?>
    <tr>
        <td><?php echo $contenido_unidad_documental->getTipoDocumental()->getDescripcion(); ?></td>
        <td><?php echo $contenido_unidad_documental->getDescripcion(); ?></td>
        <td><?php echo $contenido_unidad_documental->getFolios(); ?></td>
        <td><?php echo $contenido_unidad_documental->getFechaDocumento(); ?></td>
        <td><?php echo $contenido_unidad_documental->getFechaCreacion(); ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>                                                                      
<?php 
}else{
echo "&nbsp;";
} 
?>            	            