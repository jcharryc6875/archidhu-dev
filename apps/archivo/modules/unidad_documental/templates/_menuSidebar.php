<header class="logo-env">
	<!-- logo -->
	<div class="logo">
		<img class="img-circle" style="border-radius: unset;" src="<?php print $path_theme;?>assets/images/simad/logo-app-compact.png" width="88px" height="32px"/>
	</div>
	<!-- logo collapse icon -->
	<div class="sidebar-collapse">
		<a href="#" class="sidebar-collapse-icon"><!-- add class "with-animation" if you want sidebar to have animation during expanding/collapsing transition -->
			<i class="entypo-menu"></i>
		</a>
	</div>
	<!-- open/close menu icon (do not remove if you want to enable menu on mobile devices) -->
	<div class="sidebar-mobile-menu visible-xs">
		<a href="#" class="with-animation"><!-- add class "with-animation" to support animation -->
			<i class="entypo-menu"></i>
		</a>
	</div>
</header>

<ul id="main-menu" class="main-menu">
	<li>
		<a data-toggle="tooltip" data-original-title="Cerrar registro" href="#" onclick="javascript:parent.jQuery.ReloadAndCloseModalSIMAD();">
			<i class="fa fa-times-circle"></i>
			<span class="title">Cerrar</span>
		</a>
	</li>

	<?php
		// Permisos Editar Unidad Documental
		if(!$unidad_documental->getEstadoTransferencia() && empty($unidad_documental->getEstaCerrado())):
			if($sf_user->checkPerm($currentFormEditar, $currentUser)):
		?>
			<li>
				<a data-toggle="tooltip" data-original-title="Editar este expediente" href="#" onclick="javascript:jQuery.CloseModalAndHrefParent('<?php echo $base_path; ?>/archivo.php/unidad_documental/edit?unidaddocumental_id=<?php  echo $unidad_documental->getPrimaryKey(); ?>');">
					<i class="fa fa-edit" id="pr_alineo_01"></i>
					<span class="title">Editar Expediente</span>
				</a>
			</li>
		<?php
			endif;
		endif;
	?>

	<?php 
		// Permisos Sticker Unidad Documental
		if($sf_user->checkPerm($currentFormSticker, $currentUser)):
		?>					
			<li>
				<a href="#" onclick="window.open('<?php print $base_path;?>/archivo.php/unidad_documental/showRotulo?unidaddocumental_id=<?php echo $unidad_documental->getPrimaryKey(); ?>', 'sticker', 'toolbar=no,menubar=yes,scrollbars=yes,resizable=1,width=400,height=200,top=200')">
					<i class="fa fa-folder"></i>
					<span class="title">R&oacute;tulo Carpeta</span>
				</a>
			</li>
		<?php
		endif;
	?>

	<?php
		// Permisos Solicitar Documento - Prestamo
		if($unidad_documental->getLocalizacionunidaddocumentalId() != 1 )
		{
			if($permisoDeAccesiACarpeta['todo_expediente'] || $permisoDeAccesiACarpeta['solo_prestamo'] || $permisoDeAccesiACarpeta['permiso_pordependencia'])
			{
				if($solicitud_prestamo == 0)
				{
					if($contador == 0)
					{
					?>   
						<li>
							<?php
							echo jq_link_to_function('<i class="fa fa-star-o"></i> <span class="title">Solicitar</span>', 'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/solicitud_prestamo/create?unidaddocumental_id='.$unidad_documental->getUnidaddocumentalId().'","600","400")');
							?>   
						<li>
					<?php
					}
					else 
					{								
					?>   
						<li>
							<?php
								echo '<a><i class="fa fa-star-o"></i> <span class="title">Este expediente ya esta en tu lista de solicitudes de prestamo pendientes</span></a>';
							?>   
						<li>
					<?php								
					}
				}
				else
				{
				?>   
					<li>
						<?php
							echo jq_link_to_function('<i class="fa fa-hand-o-right"></i> <span class="title">Prestado</span>', 'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/solicitud_prestamo/show?unidaddocumental_id='.$unidad_documental->getUnidaddocumentalId().'","960","640")');
						?>   
					<li>
				<?php	
		
				}
			}
		}
	?>

	<?php
		// Permisos Transferir Unidad Documental
		if($sf_user->checkPerm($currentFormTransferir, $currentUser))
		{
			$localizacion = $unidad_documental->getLocalizacionunidaddocumentalId();
			if($localizacion != 3)
			{
				if(!$unidad_documental->getEstadoTransferencia())
				{ 
					$origen_transferencia = $localizacion+4;
					$destino_transferencia = $localizacion+1;			
				?>   
					<li>
						<?php
						echo jq_link_to_function('<i class="glyphicon glyphicon-transfer"></i> <span class="title">Transferir</span>', 'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/transferencia/create?origen_transferencia='.$origen_transferencia.'&destino_transferencia='.$destino_transferencia.'&folios='.$unidad_documental->getFolios().'&unidaddocumental_id='.$unidad_documental->getPrimaryKey().'","640","480")');
						?>   
					<li>
				<?php

				}
				else
				{

				?>   
					<li>
						<?php
						echo image_tag('simad/ico_por_trans.png', array('border'=>"0",'width'=>"25",'align'=>"middle",'title'=>'Pendiente de Transferir')).'Por Transferir';
						?>   
					<li>
				<?php
				}
			}
		}	
	?>

	<?php
		// Permiso Tipos Documentales
		if($permisoDeAccesiACarpeta['todo_expediente'] || $permisoDeAccesiACarpeta['solo_visualizacion'] || $permisoDeAccesiACarpeta['permiso_pordependencia'])
		{
			?>   
			<li>
				<?php  
				echo link_to('<i class="fa fa-file-text" id="pr_alineo_02"></i> <span class="title">Tipos Documentales</span>', 'contenido_documental/list?unidaddocumental_id='.$unidad_documental->getUnidaddocumentalId().'&modulo='.$unidad_documental->getLocalizacionUnidadDocumental()->getDescripcion());
				?>
			</li>
			<?php 
		}
	?>



	<?php
		// Boton Cerrar Unidad Documental
		if($sf_user->checkPerm("ARCHIVO_".$modulo."_CERRAR_EXPEDIENTE", $currentUser) && $unidad_documental->getEstaCerrado() == 0)
		{
			?>   
			<li>
				<?php
					echo link_to('<i class="glyphicon glyphicon-import"></i> <span class="title">Cerrar Expediente</span>', 'unidad_documental/closeInExpediente?unidaddocumental_id='.$unidad_documental->getPrimaryKey());
				?>
			</li>
			<?php 
		}
	?>

	<?php
		// Boton Caratula Unidad Documental
		if($sf_user->checkPerm("ARCHIVO_".$modulo."_CARATULA", $currentUser))
		{				
			?>   
			<li>
				<?php  
					echo link_to('<i class="entypo-picture"></i> <span class="title">Imprimir Carátula</span>', 'unidad_documental/generateCaratula?unidaddocumental_id='.$unidad_documental->getPrimaryKey());
				?>
			</li>
			<?php 
		}
	?>

	<li>
		<?php
			// Reporte Unidad Documental
			echo link_to('<i class="glyphicon glyphicon-check"></i> <span class="title">Hoja de Control</span>', 'unidad_documental/hojaControl?unidaddocumental_id='.$unidad_documental->getUnidaddocumentalId(),array('target'=>'_blank'));
		?>
	</li>
	
	<?php if($sf_user->checkPerm($currentFormReabrir, $currentUser) && !empty($unidad_documental->getEstaCerrado())){ // Permiso Reabrir Unidad Documental ?>
		<li>
			<?php  
				echo link_to('<i class="fa fa-folder-open"></i> <span class="title">Reabrir Expediente</span>', 'unidad_documental/reopenExpediente?unidaddocumental_id='.$unidad_documental->getPrimaryKey().'&localizacion='.$unidad_documental->getLocalizacionunidaddocumentalId());
			?>
		</li>
	<?php } ?>

	<?php
		// Permiso Cambiar Localización Unidad Documental
		if($sf_user->checkPerm($currentFormCambiarLocalizacion, $currentUser) && in_array($unidad_documental->getLocalizacionunidaddocumentalId(),array(2,3)))
		{
		?>   
		
			<li>
				<?php  
				echo link_to(
					'<i class="fa fa-sign-in"></i> <span class="title">Cambiar Localizaci&oacuten</span>', 'unidad_documental/editLocalizacion?unidaddocumental_id='.$unidad_documental->getPrimaryKey().'&localizacion='.$unidad_documental->getLocalizacionunidaddocumentalId()
				);
				?>
			</li>

		<?php 
		}
	?>

	<?php if($sf_user->checkPerm($currentFormDelete, $currentUser)){ ?>
		<li>
			<?php  
				echo link_to('<i class="glyphicon glyphicon-minus-sign"></i> <span class="title">Eliminar</span>', 'unidad_documental/deleting?unidaddocumental_id='.$unidad_documental->getPrimaryKey().'&localizacion='.$unidad_documental->getLocalizacionunidaddocumentalId());
			?>
		</li>
	<?php } ?>
</ul>
