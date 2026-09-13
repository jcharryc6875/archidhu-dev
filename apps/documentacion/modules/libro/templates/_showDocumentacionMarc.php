<div class="row">
	<div class="col-sm-6">
		<div class="col-sm-4"><p><strong>041 0# $a:</strong></p></div>
		<div class="col-sm-8"><p><?php echo $documentacion->getIdioma(); ?></p></div>
	</div>
	
    <div class="col-sm-6">
		<div class="col-sm-4"><p><strong>245 ## $a:</strong></p></div>
		<div class="col-sm-8"><p><?php echo $documentacion->getTitulo(); ?></p></div>
	</div>
    
</div>

<div class="row">
	<div class="col-sm-6">
		<div class="col-sm-4"><p><strong>245 ## $b:</strong></p></div>
		<div class="col-sm-8"><p><?php echo $documentacion->getOtraInformacionTitulo(); ?></p></div>
	</div>
	<div class="col-sm-6">
		<div class="col-sm-5"><p><strong>242 ## $c:</strong></p></div>
		<div class="col-sm-7"><p><?php echo $documentacion->getPrimeraMencionResponsabilidad(); ?></p></div>
	</div>
</div>

<div class="row">
	<div class="col-sm-6">
		<div class="col-sm-4"><p><strong>245 ## $c:</strong></p></div>
		<div class="col-sm-8"><p><?php echo $documentacion->getMencionResponsabilidad(); ?></p></div>
	</div>
	<div class="col-sm-6">
		<div class="col-sm-5"><p><strong>084 ## $a:</strong></p></div>
		<div class="col-sm-7"><p><?php echo $documentacion->getNumeroClasificacion(); ?></p></div>
	</div>
</div>

<div class="row">
	<div class="col-sm-6">
		<div class="col-sm-4"><p><strong>600 ## $a:</strong></p></div>
		<div class="col-sm-8"><p><?php echo $documentacion->getDescriptoresTematicos(); ?></p></div>
	</div>
	<div class="col-sm-6">
		<div class="col-sm-5"><p><strong>024 ## $a:</strong></p></div>
		<div class="col-sm-7"><p><?php echo $documentacion->getCodigoBarras(); ?></p></div>
	</div>
</div>
    
<div class="row">
    <div class="col-sm-6">
		<div class="col-sm-4"><p><strong>852 ## $a:</strong></p></div>
		<div class="col-sm-8"><p><?php echo $documentacion->getUbicacion(); ?></p></div>
	</div>
	<div class="col-sm-6">
		<div class="col-sm-5"><p><strong>&nbsp;</strong></p></div>
		<div class="col-sm-7"><p>&nbsp;</p></div>
	</div>   
</div>