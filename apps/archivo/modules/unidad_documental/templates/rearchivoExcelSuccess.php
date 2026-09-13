<?php

	header("Content-type: application/vnd.ms-excel");
	header("Content-disposition: attachment; filename=".md5($unidad_documental->getPrimaryKey().date('YmdGis')).".xls"); 
?>

<table width="99%" height="134" cellpadding="0" cellspacing="0" align="center" border="1">
<caption style="background-color: lightgray;">
     <h1>Indice Electr&oacute;nico del Expediente</h1>
</caption>
<tr>
    <th width="95" height="20" class="ColorRight" scope="row">ID Expediente: </th>
    <td colspan="12" style="text-align: left;"><?php echo $unidad_documental->getCodigoBarras();?></td>
</tr>
<tr>
    <th width="95" height="20" class="ColorRight" scope="row">Localizaci&oacute;n:</th>
    <td colspan="12" style="text-align: left;">Archivo <?php echo $unidad_documental->getLocalizacionUnidadDocumental()->getDescripcion();?></td>
</tr>
<tr>
    <th width="95" height="20" class="ColorRight" scope="row">Nombre Expediente:</th>
    <td colspan="12" style="text-align: left;"><?php echo $unidad_documental->getTitulo();?></td>
</tr>
<tr>
    <th width="95" height="20" class="ColorRight" scope="row">Fecha Inicial:</th>
    <td colspan="12" style="text-align: left;"><?php echo $unidad_documental->getFechaApertura();?></td>
</tr>
<tr>
    <th width="95" height="20" class="ColorRight" scope="row">Fecha Final:</th>
    <td colspan="12" style="text-align: left;"><?php echo $unidad_documental->getFechaCierre();?></td>
</tr>
<tr>
    <th width="95" height="20" class="ColorRight" scope="row">Responsable</th>
    <td colspan="12" style="text-align: left;"><?php echo $responsable->getUsuario()->getNombre()." ".$responsable->getUsuario()->getApellido();?></td>
</tr>
<tr>
    <th width="95" height="20" class="ColorRight" scope="row">No. Folios </th>
    <td colspan="12" style="text-align: left;"><?php echo $unidad_documental->getFolios();?></td>
</tr>
<tr>
    <th width="95" height="20" class="ColorRight" scope="row">Dependencia</td>
    <td colspan="12" style="text-align: left;"><?php echo $unidad_documental->getSubserie()->getSerie()->getDependencia()->getNombre(); ?></td>
</tr>
<tr>
    <th width="95" height="20" class="ColorRight" scope="row">Serie</td>
    <td colspan="12"><?php echo $unidad_documental->getSubserie()->getSerie()->getDescripcion(); ?></td>
</tr>
<tr>
    <th width="95" height="20" class="ColorRight" scope="row">Subserie</td>
    <td colspan="12" style="text-align: left;"><?php echo $unidad_documental->getSubserie()->getDescripcion(); ?></td>
</tr>
</table>
<br/>
<?php
$cantidad_registros = count($contenido);
if($cantidad_registros != 0){
?>
<table cellspacing="2" cellpadding="3" width="100%" align="center" border="1">
    <tr>
        <th scope="col" class="nobg">ID</th>
        <th scope="col" class="nobg">Nombre Documento</th>
        <th scope="col" class="nobg">Tipo Documental</th>
        <th scope="col" class="nobg">Fecha Documento</th>
        <th scope="col" class="nobg">Fecha Inserci&oacute;n</th>
        <th scope="col" class="nobg">Checksum</th>
        <th scope="col" class="nobg">Hash</th>
        <th scope="col" class="nobg">Pag. Inicial</th>
        <th scope="col" class="nobg">Pag. Final</th>
		<th scope="col" class="nobg">Orden</th>
        <th scope="col" class="nobg">Formato</th>
        <th scope="col" class="nobg">Tama&ntilde;o</th>
        <th scope="col" class="nobg">Origen</th>
    </tr>
    <?php 
    $fila="spec";
    foreach ($contenido as $contenido_unidad_documental):?>
        <tr <?php //echo $fila;
            $version = trim($unidad_documental->getSubserie()->getSerie()->getDependencia()->getVersionInst());
            if($fila=="specalt"){
                $fila="spec";
            }else{
                $fila="specalt";
            }
            ?>>  
            <td><?php echo sprintf("%s-%s-%s",trim($contenido_unidad_documental->getTipoDocumental()->getCodigo()),$contenido_unidad_documental->getPrimaryKey(),$version); ?></td>
            <td><?php echo trim($contenido_unidad_documental->getDescripcion()) ?></td>
            <td><?php echo trim($contenido_unidad_documental->getTipoDocumental()->getDescripcion()); ?></td>
            <td><?php echo $contenido_unidad_documental->getFechaDocumento("Y-m-d") ?></td>
            <td><?php echo $contenido_unidad_documental->getFechaCreacion("Y-m-d") ?></td>
            <td><?php echo $contenido_unidad_documental->getValorHuella() ?></td>
            <td><?php echo $contenido_unidad_documental->getFuncEncryp() ?></td>
            <td><?php echo $contenido_unidad_documental->getFolioInicial() ?></td>
            <td><?php echo $contenido_unidad_documental->getFolioFinal() ?></td>
			<td><?php echo $contenido_unidad_documental->getOrdenContenido() ?></td>
            <td><?php echo $contenido_unidad_documental->getFormatFile() ?></td>
            <td><?php echo $contenido_unidad_documental->getSizeFile() ?></td>
            <td><?php echo $contenido_unidad_documental->getOrigenDocumento() ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php
    }else{
        echo "<p class='all_texto'>No Existen Registros</p>";
    }// CERRAMOS CANTIDAD DE REGISTROS
?>