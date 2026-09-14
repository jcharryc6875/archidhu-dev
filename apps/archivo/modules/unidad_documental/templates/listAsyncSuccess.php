<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
//*****************************************************************************************************
switch($localizacion->getPrimaryKey())
{
    case 1:
        $nombre_modulo = ucfirst(mb_strtolower($localizacion->getDescripcion()));
        break;
    case 2:
        $nombre_modulo = ucfirst(mb_strtolower($localizacion->getDescripcion()));
        break;
    case 3:
        $nombre_modulo = ucfirst(mb_strtolower($localizacion->getDescripcion()));
        break;
}
//******************************************************************************************************
$modulo  = strtoupper($localizacion->getDescripcion());
$currentFormExportar   = $modulo."_EXPORTAR";
$currentFormExpTrans   = $modulo."_EXPORTAR_TRANSFERENCIAS";
$currentFormExpSinTRD  = $modulo."_EXPORTAR_EXPEDIENTES_SIN_TRD";
$currentFormExpIsad    = $modulo."_EXPORTAR_EXPEDIENTES_ISAD";
$currentFormExpNtc4095 = $modulo."_EXPORTAR_EXPEDIENTES_NTC4095";
$currentExportDescarte = $modulo."_REPORTE_DESCARTE_DOCUMENTAL";
$currentFormSticker   	    = "ARCHIVO_".$modulo."_STICKER";
//******************************************************************************************************
$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');
use_helper('Form','jQuery');
?>
<?php
$cantidad_registros = $pager->getNbResults();	    
if($cantidad_registros == 0):
?>
<div class="panel panel-primary">
      <div class="panel-heading">
        <div class="panel-title">Archivo <?php echo $nombre_modulo; ?></div>
      </div>

      <div class="panel-body">
      	<?php $mensajeVacio = isset($mensajeListaVacia) ? $mensajeListaVacia : ConsultaPermisoHelper::MSG_SIN_REGISTROS; ?>
      	<?php if (ConsultaPermisoHelper::esMensajeSinPermiso($mensajeVacio)): ?>
      		<?php echo ConsultaPermisoHelper::htmlAlertaSinPermiso($mensajeVacio); ?>
      	<?php else: ?>
      		<div class="alert alert-default"><?php echo $mensajeVacio; ?></div>
      	<?php endif; ?>
  	  </div>
</div>


<?php
else:
?>            
<div class="panel panel-primary">
    <!-- Paginador -->
    <div class="dataTables_wrapper">
      <div class="row">
        <div class="col-xs-6 col-left">
          <div class="dataTables_info" id="table-2_info" role="status" aria-live="polite">Mostrando del <?php print $pager->getFirstIndice();?> al <?php print $pager->getLastIndice(); ?> de <?php print $pager->getNbResults();?></div>
        </div>
        <div class="col-xs-6 col-right">
          <div class="dataTables_paginate paging_bootstrap" id="table-2_paginate">
            <?php
                echo use_helper('Pagination');
                echo $paginacion_data = pager_navigation($pager, 'unidad_documental/list', $filtros_consulta);
            ?>
          </div>
        </div>
      </div>
    </div>
    <!-- Fin Paginador -->
                
  <div class="panel-heading">
    <div class="panel-title">Lista Archivo <?php echo $nombre_modulo; ?></div>                
  </div>

  <div class="panel-body with-table">
  	<?php
	$filtros_consulta = str_replace("%","|",$filtros_consulta);
	echo form_tag('unidad_documental/list?'.$filtros_consulta, array('name'=>'listar'));
	?>                
        <table class="table table-bordered table-hover table-striped responsive" id="table-1">
          <thead>
            <tr>
            	<th class="text-center" style="width: 5%" data-hide="phone">Marca</th>
				<th class="text-center" style="width: 10%">ID Expediente</th>
				<th class="text-center" style="width: 8%">Numero Caja</th>
				<th class="text-center" style="width: 6%">Imagenes</th>
				<th class="text-center">Subserie</th>
				<th class="text-center" style="width: 25%">Nombre Expediente</th>
				<th class="text-center" style="width: 8%">Fecha Inicial</th>
				<?php if($localizacion->getPrimaryKey() != 1){ ?>
				  <th class="text-center" style="width: 8%">Fecha Final</th>
				<?php } ?>                            
				<th class="text-center" style="width: 10%">Ubicaci&oacute;n</th>
			</tr>
          </thead>
          <tbody>
          	<?php
          	$con = 1;
            foreach ($pager->getResults() as $unidad_documental):
            ?>
            <tr>
				<td class="text-center">
				<?php 
				echo input_hidden_tag(sprintf("iiRec%02d", $con), $unidad_documental->getPrimaryKey());
				$marca_user = false;       
				if($unidad_documental->getMarca() == $currentUser){
					$marca_user = true;
				}
				echo checkbox_tag(sprintf("marca%02d", $con), $unidad_documental->getPrimaryKey(), $marca_user, 
					array('onclick' => jq_remote_function(array('update'  => '', 'url' => 'unidad_documental/marcar?unidaddocumental_id='.$unidad_documental->getPrimaryKey())))
				);
				?>
				</td>
				<td class="text-center">
					<?php 
						echo jq_link_to_function($unidad_documental->getCodigoBarras() ? $unidad_documental->getCodigoBarras() : "Sin Codigo Barras", 'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/unidad_documental/show?unidaddocumental_id='.$unidad_documental->getUnidaddocumentalId().'&localizacionunidaddocumental_id='.$unidad_documental->getLocalizacionunidaddocumentalId().'")'); 
					?>
				</td>
				<td class="text-center"><?php echo $unidad_documental->getNumeroCaja(); ?></td>
				<td class="text-center">
					<?php          
					if($unidad_documental->countContenidoUnidadDocumentals()){
						echo jq_link_to_function(image_tag('simad/ico_ver_adj.png', array('border'=>"0",'width'=>"25",'height'=>"25",'align'=>"middle",'data-original-title'=>'Ver tipos documentales de este expediente', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip', 'data-placement' => 'top')), 'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/contenido_documental/list?unidaddocumental_id='.$unidad_documental->getUnidaddocumentalId().'&modulo='.$unidad_documental->getLocalizacionunidaddocumental().'")');             
					}else{
						echo image_tag('simad/ico_anular.png', array('data-original-title' => 'Unidad documental sin imagen', 'border' => '0', 'width'=> '25', 'height' => '25','align' => 'middle', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip', 'data-placement' => 'top'));
					} 
					?>
				</td>
				<td><?php echo $unidad_documental->getSubserie()->getDescripcion(); ?></td>
				<td class="text-justify"><?php echo trim($unidad_documental->getNumeroIdentificacion()) ? trim($unidad_documental->getNumeroIdentificacion())." - ".$unidad_documental->getTitulo() : $unidad_documental->getTitulo(); ?></td>
				<td class="text-center"><?php echo substr($unidad_documental->getFechaApertura(), 0, 10); ?></td>
				<?php if($localizacion->getPrimaryKey() != 1){ ?>
				  <td class="text-center"><?php echo substr($unidad_documental->getFechaCierre(), 0, 10); ?></td>
				<?php } ?>    						
				<td class="text-center">
					<?php
					switch ($unidad_documental->getLocalizacionunidaddocumentalId()){
						case 1:
							echo $unidad_documental->getUbicacionengestion().'&nbsp;'; 
							break;
						case 2:
							echo $unidad_documental->getUbicacionencentral().'&nbsp;'; 
							break;
						case 3:
							echo $unidad_documental->getUbicacionenhistorico().'&nbsp;'; 
							break;
						default:
								echo "";
								break;
					}
					?>
				</td>
			</tr>
            <?php
            $con++;
            endforeach;
            ?>
          </tbody>
        </table>        
        <!-- Opciones Listar Unidad Documental -->
        <div class="row">
        	<div class="col-sm-12 form-group">                            
                <?php
	        	 // Boton Marcar Todos
	        	 echo jq_link_to_function(image_tag('simad/ico_check.png', array('border'=>"0",'width'=>"25",'align'=>"middle",'title'=>'Marcar todos los expedientes consultados')).'Marcar Todos','javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/unidad_documental/marcarTodos?'.$filtros_consulta.'","400","300")', array('class' => 'btn btn-white btn-sm'));
	        	?>
                
	        	<?php
	        	 // Boton Desmarcar Todos
	        	 echo jq_link_to_function(image_tag('simad/ico_uncheck.png', array('border'=>"0",'width'=>"25",'align'=>"middle",'title'=>'Desmarcar todos los expedientes consultados')).'Desmarcar Todos','javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/unidad_documental/desmarcar","400","300")', array('class' => 'btn btn-white btn-sm'));
	        	?>

	        	<?php
	        	// Boton Exportar
	        	if($sf_user->checkPerm($currentFormExportar, $currentUser)){ 
	        	?>
	        	<a href="<?php echo $base_path; ?>/archivo.php/unidad_documental/exportar?<?php echo $filtros_consulta?>" class="btn btn-white btn-sm"><img border=0 src="<?php print $base_path;?>/images/simad/ico_exportar.png" alt="Exportar" width="25" align="middle" />Exportar</a>			
				<?php
				} 
				?>

				<?php
				// Exportar Transferencias
				if($sf_user->checkPerm($currentFormExpTrans, $currentUser)){ 
				?>
	         		<a class="btn btn-white btn-sm" href="<?php print $base_path;?>/archivo.php/unidad_documental/exportar?portransferir=1&localizacionunidaddocumental_id=1" ><img border=0 src="<?php print $base_path;?>/images/simad/ico_exportar.png" alt="Exportar" width="25" height="" align="middle" />Trans. Aceptadas </a>
	         		<a class="btn btn-white btn-sm" href="<?php print $base_path;?>/archivo.php/unidad_documental/exportar?portransferir=0&localizacionunidaddocumental_id=1" ><img border=0 src="<?php print $base_path;?>/images/simad/ico_exportar.png" alt="Exportar" width="25" height="" align="middle" />Trans. Pendientes</a>
				<?php
		     	}
		     	?>

		     	<?php
		     	// Boton Exportar sin TRD
		     	if($sf_user->checkPerm($currentFormExpSinTRD, $currentUser) && $sf_params->get('localizacionunidaddocumental_id') == 2  && 1 != 1){ 
				?>
					<a class="btn btn-white btn-sm" href="<?php print $base_path;?>/archivo.php/unidad_documental/exportarSinTrd?sinaplicaciontrd=1&localizacionunidaddocumental_id=2" ><img border="0" src="<?php print $base_path;?>/images/simad/ico_exportar.png" alt="Exportar" width="25" height="" align="middle" />Exp. Sin TRD</a>
				<?php
				}
				?>

				<?php 
				// Exportar ISAD
				if($sf_user->checkPerm($currentFormExpIsad, $currentUser) && $sf_params->get('localizacionunidaddocumental_id') == 2){
				?>
        			<a class="btn btn-white btn-sm" href="<?php print $base_path;?>/archivo.php/unidad_documental/exportarIsad?<?php echo $filtros_consulta?>" ><img border="0" src="<?php print $base_path;?>/images/simad/ico_isad.png" alt="Exportar" width="25" height="" align="middle" />Reporte ISAD(G)</a>
    			<?php
    			}
    			?>

    			<?php
    			// Exportar NTC
    			if($sf_user->checkPerm($currentFormExpNtc4095, $currentUser) && $sf_params->get('localizacionunidaddocumental_id') == 2 && 1 != 1){
				?>
					<a class="btn btn-white btn-sm" href="<?php print $base_path;?>/archivo.php/unidad_documental/exportarNtc4095?<?php echo $filtros_consulta?>" ><img border="0" src="<?php print $base_path;?>/images/simad/ico_4095.png" alt="Exportar" width="25" height="" align="middle" />Reporte Norma 4095</a>
				<?php
				}
				?>

				<?php
				// Exportar Descarte 
				if($sf_user->checkPerm($currentExportDescarte, $currentUser) && $sf_params->get('localizacionunidaddocumental_id') == 2  && 1 != 1){ 
				?>
        			<a class="btn btn-white btn-sm" href="<?php print $base_path;?>/archivo.php/unidad_documental/reporteDescarte?<?php echo $filtros_consulta?>" ><img border="0" src="<?php print $base_path;?>/images/simad/ico_report.png" alt="Exportar" width="25" height="" align="middle" />Reporte Descarte</a>
    			<?php
    			}
    			?>
				
				<?php
				// Boton Sticker Unidad Documental
				if($sf_user->checkPerm($currentFormSticker, $currentUser)){ 
					if(($localizacion->getPrimaryKey() && $sf_params->get('marcado') != "") || $sf_params->get('id_responsable') != ""){
						echo link_to(image_tag('simad/ico_add_sticker.png', array('border'=>"0",'width'=>"25",'align'=>"middle",'title'=>'Imprimir Sticker')).'Imprimir Sticker', 'unidad_documental/stickers?'.$filtros_consulta.$paramsBusqueda, array('popup' => true, 'class' => 'btn btn-white btn-sm'));
					}
				}
				?>
                   
                <?php 
                    if($sf_user->checkPerm($currentFormDelete, $currentUser) && $sf_params->get('localizacionunidaddocumental_id') && $sf_params->get('marcado') != ""){
                          echo jq_link_to_remote(image_tag($base_path.'/images/simad/ico_delete.png',array('id'=>"feedback",'title'=>'Borrar Expediente Marcados','border'=>"0",'width'=>"25" ,'align'=>"middle")).'Borrar Marcados',array(
                            	'update'    => '',
                            	'url'     => 'unidad_documental/deleteMarcados?localizacionunidaddocumental_id='.$sf_params->get('localizacionunidaddocumental_id'),
                        		'confirm'  => 'Esta seguro de eliminar los expedientes seleccionados! \n Esta accion no se puedra deshacer',
                        		//'loading' => "Element.show('indicator_fondo');Element.show('indicator');",
                        		'success' => "simad_refresh();",
                        		'failure' => "alert('Ocurrio un error al procesar la solicitud,intenta de nuevo');",
                        		'complete' => "alert('Se completo la solicitud de eliminaci�n');",
                            ),array('class' => 'btn btn-white btn-sm'));
                    } 
                ?>                             
                
                <?php 
                    if($sf_user->checkPerm($currentFormMarcaDelete, $currentUser) && $sf_params->get('localizacionunidaddocumental_id') != 1 && $sf_params->get('marcado') != ""){
                          echo jq_link_to_function(image_tag($base_path.'/images/simad/ico_expediente_delete.png',array('id'=>"feedback",'title'=>'Marcar expediente para eliminacion por descarte documental','border'=>"0",'width'=>"25" ,'align'=>"middle")).'Marcar Eliminacion','javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/unidad_documental/eliminacionDescarte?localizacionunidaddocumental_id='.$sf_params->get('localizacionunidaddocumental_id').'", 550, 380)',array('class' => 'btn btn-white btn-sm'));
                    } 
                ?>
                
                <?php 
                    if($sf_user->checkPerm($currentFormRemplazar, $currentUser) && $sf_params->get('marcado') != ""){
                          echo jq_link_to_function(image_tag($base_path.'/images/simad/ico_remplazar.png',array('id'=>"feedback",'title'=>'Remplazar informacion de los expedientes seleccionados','border'=>"0",'width'=>"25" ,'align'=>"middle")).'Remplazar Data','javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/unidad_documental/replaceData?localizacionunidaddocumental_id='.$sf_params->get('localizacionunidaddocumental_id').'&modulo='.$modulo.'", 550, 380)',array('class' => 'btn btn-white btn-sm'));
                    } 
                ?>
    
				<?php
				// Boton Regresar
				if($sf_params->get('form_origen') == "consultar"){
					echo jq_link_to_function(image_tag('simad/ico_devolver.png', array('border'=>"0",'width'=>"25",'align'=>"middle",'title'=>'Regresar')).'Regresar', 'javascript:history.go(-1)', array('class' => 'btn btn-white btn-sm'));
				}
				?>        		        	
        	</div>
    	</div>               		        
        <!-- Paginador -->
        <div class="dataTables_wrapper">
          <div class="row">
            <div class="col-xs-6 col-left">
              <div class="dataTables_info" id="table-2_info" role="status" aria-live="polite">Mostrando del <?php print $pager->getFirstIndice();?> al <?php print $pager->getLastIndice(); ?> de <?php print $pager->getNbResults();?></div>
            </div>
            <div class="col-xs-6 col-right">
              <div class="dataTables_paginate paging_bootstrap" id="table-2_paginate">
                <?php
                echo $paginacion_data;
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
</div>         
<?php
endif;
?>