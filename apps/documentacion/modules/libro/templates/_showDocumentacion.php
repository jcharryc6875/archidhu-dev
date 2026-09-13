<div class="row">
	<div class="col-sm-6">
		<div class="col-sm-4"><p><strong>Idioma:</strong></p></div>
		<div class="col-sm-8"><p><?php echo $documentacion->getIdioma(); ?></p></div>
	</div>
	<div class="col-sm-6">
		<div class="col-sm-5"><p><strong>Tipo Documentacion:</strong></p></div>
		<div class="col-sm-7"><p><?php echo $documentacion->getTipodocumentacion(); ?></p></div>
	</div>
</div>

<div class="row">
	<div class="col-sm-6">
		<div class="col-sm-4"><p><strong>Titulo:</strong></p></div>
		<div class="col-sm-8"><p><?php echo $documentacion->getTitulo(); ?></p></div>
	</div>
	<div class="col-sm-6">
		<div class="col-sm-5"><p><strong>Estado:</strong></p></div>
		<div class="col-sm-7"><p><?php echo $documentacion->getEstadodocumentacion()->getDescripcion(); ?></p></div>
	</div>
</div>

<div class="row">
	<div class="col-sm-6">
		<div class="col-sm-4"><p><strong>Otra Informacion Titulo:</strong></p></div>
		<div class="col-sm-8"><p><?php echo $documentacion->getOtraInformacionTitulo(); ?></p></div>
	</div>
	<div class="col-sm-6">
		<div class="col-sm-5"><p><strong>Primera Mencion Responsabilidad:</strong></p></div>
		<div class="col-sm-7"><p><?php echo $documentacion->getPrimeraMencionResponsabilidad(); ?></p></div>
	</div>
</div>

<div class="row">
	<div class="col-sm-6">
		<div class="col-sm-4"><p><strong>Mencion Responsabilidad:</strong></p></div>
		<div class="col-sm-8"><p><?php echo $documentacion->getMencionResponsabilidad(); ?></p></div>
	</div>
	<div class="col-sm-6">
		<div class="col-sm-5"><p><strong>Numero Clasificacion:</strong></p></div>
		<div class="col-sm-7"><p><?php echo $documentacion->getNumeroClasificacion(); ?></p></div>
	</div>
</div>

<div class="row">
	<div class="col-sm-6">
		<div class="col-sm-4"><p><strong>Descriptores Tematicos:</strong></p></div>
		<div class="col-sm-8"><p><?php echo $documentacion->getDescriptoresTematicos(); ?></p></div>
	</div>
	<div class="col-sm-6">
		<div class="col-sm-5"><p><strong>Codigo Barras:</strong></p></div>
		<div class="col-sm-7"><p><?php echo $documentacion->getCodigoBarras(); ?></p></div>
	</div>
</div>
    
<div class="row">	
	<div class="col-sm-6">
		<div class="col-sm-5"><p><strong>Ubicacion:</strong></p></div>
		<div class="col-sm-7"><p><?php echo $documentacion->getUbicacion(); ?></p></div>
	</div>
    <div class="col-sm-6">
		<div class="col-sm-5"><p><strong></strong></p></div>
		<div class="col-sm-7"><p>&nbsp;</p></div>
	</div>
</div>