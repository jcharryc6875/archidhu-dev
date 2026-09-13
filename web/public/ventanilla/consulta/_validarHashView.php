<?php if($includeDiv){ ?>
<div class="col-md-8" id="<?php echo md5('strhash');?>">
<?php } ?>
<div class="panel panel-success">
	<div class="panel-heading">
		<div class="panel-title">Lista de Resultados de la Consulta</div>
	</div>
	<?php 
	if(empty($com_enviadas))
	{ 
		?>
				<div class="panel-body">
					<div class="alert alert-default"><strong>No existen Registros</strong>, Intente con diferentes filtros de consulta.</div>
				</div>      
		<?php
	}
	else
	{
		if($verificado_hash)
		{  
				?>
				<!-- Informacion Detalle -->
				<hr />
					<div class="col-sm-12 col-md-12">  
						<div class="row">					
							<div class="col-sm-6">
								<div class="col-sm-4"><p><strong>Radicado:</strong></p></div>
								<div class="col-sm-8">
									<p>
										<?php 
											echo $com_enviadas['RADICADO'] . '  ';
											for ($i = 0; $i < count($com_enviadas); $i++) 
											{
												if ($com_enviadas['ADJUNTOS'][$i]['TIPO_ATTACHMENT'] == 'DIGIT_COM') 
												{ 
													?>
													<a class="tooltip-primary" data-toggle="tooltip" data-original-title="<?php // echo basename($result) ?>" href="<?php echo $com_enviadas['ADJUNTOS'][$i]['URL'];?>" target="_blank">
															<img src="<?php echo $base_path; ?>/images/simad/ico_ver_adj.png" width="25" height="25" align="middle" />   
													</a>
													<?php
													break;
												} 
											}
										?>
									</p>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="col-sm-5"><p><strong>Usuario Asignado:</strong></p></div>
								<div class="col-sm-7"><p><?php echo $com_enviadas['USUARIO_ASIGNADO']; ?></p></div>
							</div>
						</div>

						<div class="row">					
							<div class="col-sm-6">
								<div class="col-sm-4"><p><strong>Dependencia Asignada:</strong></p></div>
								<div class="col-sm-8">
									<p><?php echo $com_enviadas['DEPENDENCIA_ASIGNADA']; ?></p>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="col-sm-5"><p><strong>Usuario Radicador:</strong></p></div>
								<div class="col-sm-7"><p><?php echo $com_enviadas['USUARIO_RADICADOR'] ? $com_enviadas['USUARIO_RADICADOR'] : ""; ?></p></div>
							</div>
						</div>
						<div class="row">					
							<div class="col-sm-6">
								<div class="col-sm-4"><p><strong>Dependencia Radicador:</strong></p></div>
								<div class="col-sm-8">
									<p><?php echo $com_enviadas['DEPENDENCIA_RADICADOR']; ?></p>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="col-sm-4"><p><strong>Anexos:</strong></p></div>
								<div class="col-sm-8">
									<?php 
									for ($i = 0; $i < count($com_enviadas['ADJUNTOS']); $i++) 
									{
										if ($com_enviadas['ADJUNTOS'][$i]["TIPO_ATTACHMENT"] == 'ANEXO') 
										{ 
											?>
											<a class="tooltip-primary" data-toggle="tooltip" data-original-title="<?php // echo basename($result) ?>" href="<?php echo $com_enviadas['ADJUNTOS'][$i]['URL'];?>" target="_blank">
													<img src="<?php echo $base_path; ?>/images/simad/ico-adj-file.png" width="25" height="25" align="middle" />   
											</a>
											<?php
										} 
									}
									?>
								</div>
							</div>
						</div>
						<div class="row">					
							<div class="col-sm-6">
								<div class="col-sm-4"><p><strong>Usuario(s) Firman:</strong></p></div>
								<div class="col-sm-8"> 
									<p>
										<?php 
											/**/
											for ($i = 0; $i < count($com_enviadas['RADICADO_FLUJO']); $i++) 
											{
												if ($com_enviadas['RADICADO_FLUJO'][$i]['ACTIVIDAD_FLUJO'] == 'Firmante') 
												{ 
													echo $com_enviadas['RADICADO_FLUJO'][$i]['USUARIO_ACTIVIDAD'];
												} 
											}
											
										?>
									</p> 
								</div>
							</div>
							<div class="col-sm-6">          
							</div>
						</div>
						<div class="row">
							<?php
								echo '<div class="panel-heading"><div class="panel-title" style="color:green; font-size: 24px !important;">EL CSV ADJUNTADO ES AUT&Eacute;NTICO</div></div>';
							?>
						</div>
					</div>
				<hr />
				<?php
		}
		else
		{
			?>
				<div class="panel-body">
					<div class="alert alert-danger"><strong>EL HASH ADJUNTADO NO ES AUTENTICO, ES INVALIDO</strong></div>
				</div>
			<?php
		}
	}
	?>
</div>
	
<?php if($includeDiv) { ?>
</div> 
<?php } ?>