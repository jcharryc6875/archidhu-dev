<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
//*****************************************************************************************************
switch($localizacion_object->getPrimaryKey())
{
    case 1:
        $nombre_modulo = ucfirst(strtolower($localizacion_object->getDescripcion()));
        break;
    case 2:
        $nombre_modulo = ucfirst(strtolower($localizacion_object->getDescripcion()));
        break;
    case 3:
        $nombre_modulo = ucfirst(strtolower($localizacion_object->getDescripcion()));
        break;
}
//******************************************************************************************************
$modulo  = strtoupper($localizacion_object->getDescripcion());
$currentFormExportar    	= $modulo."_EXPORTAR";
$currentFormExpTrans     	= $modulo."_EXPORTAR_TRANSFERENCIAS";
$currentFormExpSinTRD     	= $modulo."_EXPORTAR_EXPEDIENTES_SIN_TRD";
$currentFormExpIsad     	= $modulo."_EXPORTAR_EXPEDIENTES_ISAD";
$currentFormExpNtc4095     	= $modulo."_EXPORTAR_EXPEDIENTES_NTC4095";
$currentExportDescarte     	= $modulo."_REPORTE_DESCARTE_DOCUMENTAL";
$currentFormDelete    	    = "ARCHIVO_".$modulo."_ELIMINAR_CODIGO";
$currentFormRemplazar    	= "ARCHIVO_".$modulo."_REMPLAZAR_DATA";
$currentFormMarcaDelete     = "ARCHIVO_MARCAR_POR_ELIMINACION";
$currentFormSticker   	    = "ARCHIVO_".$modulo."_STICKER";
$currentFormFuid   	        = "ARCHIVO_".$modulo."_GENERAR_FUID";

$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');

use_helper('Form','jQuery');
?>

<div class="row">
	<div class="col-md-12">
		<?php
        $cantidad_registros = $pager->getNbResults();
        echo form_tag('unidad_documental/list?'.$filtros_consulta, array('name'=>'listar','class' => 'form-horizontal form-groups-bordered validate'));
		// Include Navbar Archivo
		include_once("_navbar_archivo.php");
        echo input_hidden_tag('localizacionunidaddocumental_id',$sf_params->get('localizacionunidaddocumental_id'));
		?>
        <div id="tablelist" >
	    <?php	    
	    if($cantidad_registros == 0):
	    ?>
		<div class="panel panel-primary">
		      <div class="panel-heading">
		        <div class="panel-title">Archivo <?php echo $nombre_modulo; ?></div>
		      </div>

		      <div class="panel-body">
		      	<div class="alert alert-default"><strong>No existen Registros</strong>, Intente con diferentes filtros de consulta.</div>
	      	  </div>
      	</div>
	    <?php else: ?>
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
				?>                
    		        <table class="table table-bordered table-hover table-striped responsive" id="table-1">
    		          <thead>
                        <tr>
    		            	<th class="text-center" style="width: 5%" data-hide="phone">Marca</th>
    						<th class="text-center" style="width: 10%">N&uacute;mero Expediente</th>
    						<th class="text-center" style="width: 6%">Imagenes</th>
    						<th class="text-center">Subserie</th>
    						<th class="text-center" style="width: 25%">Nombre Expediente</th>
    						<th class="text-center" style="width: 8%">Fecha Inicial</th>
                            <?php if($localizacion_object->getPrimaryKey() != 1){ ?>
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
                            <?php if($localizacion_object->getPrimaryKey() != 1){ ?>
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
        
							<?php if($sf_user->checkPerm($currentFormExportar, $currentUser)){  ?>
								<div class="btn-group">
									<button type="button" class="btn btn-white btn-sm dropdown-toggle" data-toggle="dropdown">
										<?php echo image_tag('simad/ico_expor_informe.png', array('border'=>"0",'width'=>"25",'align'=>"middle",'title'=>'Exportar los expedientes actuales')); ?>
										<span>Exportar</span>
										<span class="caret"></span>
									</button>
									<ul class="dropdown-menu dropdown-default" role="menu">
										<li>
											<a target="_blank" href="<?php echo url_for('unidad_documental/exportar?'.$filtros_consulta); ?>" class="tooltip-primary" data-toggle = "tooltip" data-original-title = "Exporta solo la lista de expedientes">
												<?php echo image_tag('simad/ico_exportar.png', array('border'=>"0",'width'=>"20",'align'=>"middle",)); ?>
												<span>Expedientes</span>
											</a>
										</li>
										<li>
											<a target="_blank" href="<?php echo url_for('unidad_documental/exportarDocs?'.$filtros_consulta); ?>" class="tooltip-primary" data-toggle = "tooltip" data-original-title = "Exporta la lista de expedientes y los contenidos documentales">
												<?php echo image_tag('simad/ico_exportar.png', array('border'=>"0",'width'=>"20",'align'=>"middle",)); ?>
												<span>Expedientes y Documentos</span>
											</a>
										</li>
									</ul>
								</div>
        					<?php } ?>
        
        					<?php if($sf_user->checkPerm($currentFormExpTrans , $currentUser) && $sf_params->get('localizacionunidaddocumental_id') != 3 ){ // Exportar Transferencias ?>
        		         		<a target="_blank" class="btn btn-white btn-sm" href="<?php print $base_path;?>/archivo.php/unidad_documental/exportarTransferencias?portransferir=1&localizacionunidaddocumental_id=<?php echo $sf_params->get('localizacionunidaddocumental_id'); ?>" ><img border=0 src="<?php print $base_path;?>/images/simad/ico_exportar.png" alt="Exportar" width="25" height="" align="middle" />Trans. Aceptadas</a>
        		         		<a target="_blank" class="btn btn-white btn-sm" href="<?php print $base_path;?>/archivo.php/unidad_documental/exportarTransferencias?portransferir=0&localizacionunidaddocumental_id=<?php echo $sf_params->get('localizacionunidaddocumental_id'); ?>" ><img border=0 src="<?php print $base_path;?>/images/simad/ico_exportar.png" alt="Exportar" width="25" height="" align="middle" />Trans. Pendientes</a>
        					<?php } ?>
        
            				<?php if($sf_user->checkPerm($currentExportDescarte, $currentUser) && $sf_params->get('localizacionunidaddocumental_id') == 2  && 1 != 1){ // Exportar Descarte ?>
                    			<a class="btn btn-white btn-sm" href="<?php print $base_path;?>/archivo.php/unidad_documental/reporteDescarte?<?php echo $filtros_consulta?>" >
									<img border="0" src="<?php print $base_path;?>/images/simad/ico_report.png" alt="Exportar" width="25" height="" align="middle" />Reporte Descarte
								</a>
                			<?php } ?>
							
							<?php if($sf_user->checkPerm($currentFormFuid, $currentUser) && $isEnableFuid == true){ // Exportar Fuid ?>
                    			<a class="btn btn-white btn-sm" target="_new" href="<?php print url_for('unidad_documental/genFuid')."?$filtros_consulta";?>">
									<img border="0" src="<?php print $base_path;?>/images/simad/ico_report.png" alt="Exportar" width="25" height="" align="middle" />Generar FUID
								</a>
                			<?php } ?>
        
                			<?php
							if($sf_user->checkPerm($currentFormSticker, $currentUser)){ // Boton Sticker Unidad Documental
								if(($localizacion_object->getPrimaryKey() && $sf_params->get('marcado') != "") || $sf_params->get('id_responsable') != ""){ ?>
									<div class="btn-group">
										<button type="button" class="btn btn-white btn-sm dropdown-toggle" data-toggle="dropdown">
											<?php echo image_tag('simad/ico_add_sticker.png', array('border' => "0", 'width' => "25", 'align' => "middle", 'title' => 'Generar rotulos caja y carpeta, hojas de control de los expedientes marcados')); ?>
											<span>Rotulaci&oacute;n expedientes</span>
											<span class="caret"></span>
										</button>
										<ul class="dropdown-menu dropdown-default" role="menu" style="background-color: #fff;">
											<li>
												<a class="btn btn-white btn-sm" target="_blank" href="<?php echo url_for('unidad_documental/stickers?'.$filtros_consulta.$paramsBusqueda); ?>" class="tooltip-primary" data-toggle="tooltip" data-original-title="Generar rotulos de carpeta de los expedientes marcados">
													<?php echo image_tag('simad/ico_add_sticker.png', array('border' => "0", 'width' => "20", 'align' => "middle")); ?>
													<span style="color: #000!important;">Rotulo(s) Carpeta(s)</span>
												</a>
											</li>
											<li>
												<a class="btn btn-white btn-sm" target="_blank" href="<?php echo url_for('unidad_documental/batchHojaControl?'.$filtros_consulta.$paramsBusqueda); ?>" class="tooltip-primary" data-toggle="tooltip" data-original-title="Generar hoja de control de los expedientes marcados">
													<?php echo image_tag('simad/ico_add_sticker.png', array('border' => "0", 'width' => "20", 'align' => "middle")); ?>
													<span style="color: #000!important;">Hojas(s) de Control(s)</span>
												</a>
											</li>
											<li>  
												<a class="btn btn-white btn-sm" target="_blank" href="<?php echo url_for('unidad_documental/rotuloCaja'); ?>" class="tooltip-primary" data-toggle="tooltip" data-original-title="Generar los rotulos de la caja">
													<?php echo image_tag('simad/ico_add_sticker.png', array('border' => "0", 'width' => "20", 'align' => "middle")); ?>
													<span style="color: #000!important;">Rotulo(s) Caja</span>
												</a>
											</li>
										</ul>
									</div>
								<?php } ?>
							<?php } ?>
                               
                            <?php 
                                if($sf_user->checkPerm($currentFormDelete, $currentUser) && $sf_params->get('localizacionunidaddocumental_id') && $sf_params->get('marcado') != ""){
									echo jq_link_to_remote(image_tag($base_path.'/images/simad/ico_delete.png',array('id'=>"feedback",'title'=>'Borrar Expediente Marcados','border'=>"0",'width'=>"25" ,'align'=>"middle")).'Borrar Marcados',array(
										'update'    => '',
										'url'     => 'unidad_documental/deleteMarcados?localizacionunidaddocumental_id='.$sf_params->get('localizacionunidaddocumental_id'),
										'confirm'  => 'Esta seguro de eliminar los expedientes seleccionados! \n Esta accion no se puedra deshacer',
										//'loading' => "Element.show('indicator_fondo');Element.show('indicator');",
										'success' => "simad_refresh();",
										'failure' => "alert('Ocurrio un error al procesar la solicitud,intenta de nuevo');",
										'complete' => "alert('Se completo la solicitud de eliminación');",
									),array('class' => 'btn btn-white btn-sm'));
                                } 
                            ?>                             
                            
                            <?php 
                                if($sf_user->checkPerm($currentFormMarcaDelete, $currentUser) && $sf_params->get('localizacionunidaddocumental_id') != 1 && $sf_params->get('marcado') != ""){
									echo jq_link_to_function(image_tag($base_path.'/images/simad/ico_expediente_delete.png',array('id'=>"feedback",'title'=>'Marcar expediente para eliminacion por descarte documental','border'=>"0",'width'=>"25" ,'align'=>"middle")).'Marcar Eliminacion','javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/unidad_documental/eliminacionDescarte?localizacionunidaddocumental_id='.$sf_params->get('localizacionunidaddocumental_id').'", 550, 380)',array('class' => 'btn btn-white btn-sm'));
                                } 
                            ?>
                            
                            <?php 
                                if($sf_user->checkPerm($currentFormRemplazar, $currentUser) && !empty($sf_params->get('marcado'))){
									echo jq_link_to_function(image_tag($base_path.'/images/simad/ico_remplazar.png',array('id'=>"feedback",'title'=>'Remplazar informacion de los expedientes seleccionados','border'=>"0",'width'=>"25" ,'align'=>"middle")).'Remplazar Data','javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/unidad_documental/replaceData?localizacionunidaddocumental_id='.$sf_params->get('localizacionunidaddocumental_id').'&modulo='.$modulo.'", 550, 380)',array('class' => 'btn btn-white btn-sm'));
                                } 
                            ?>
                
							<?php
							// Boton Regresar
							if($sf_params->get('form_origen') == "consultar"){
								echo jq_link_to_function(image_tag('simad/ico_devolver.png', array('border'=>"0",'width'=>"25",'align'=>"middle",'title'=>'Regresar')).'Regresar', 'javascript:history.go(-1)', array('class' => 'btn btn-white btn-sm'));
							}
							?>

							<?php if($sf_user->checkPerm($modulo."_DESCARGAR_DOCUMENTOS", $currentUser)){ ?>
								<a href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo url_for('unidad_documental/downloadBatchDocs?localizacionunidaddocumental_id='.$sf_params->get('localizacionunidaddocumental_id')); ?>','1280','640');" class="btn btn-sm btn-white tooltip-primary" data-toggle="tooltip" data-original-title="Descargar todos los documento(s) asociados a los expedientes marcados">
									<?php echo image_tag('simad/ico_expor_informe.png', array('border'=>"0",'width'=>"25",'align'=>"middle")); ?>
									<span>Descargar Documentos</span>
								</a>
							<?php } ?>
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
                    <!-- Fin Paginador -->
                </div>
              </div>
		<?php
		endif;
		?>  
        </div>
      </form>
	</div>
</div>