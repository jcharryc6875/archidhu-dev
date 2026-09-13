<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
//*****************************************************************************************************
$id_modulo = $localizacion->getLocalizacionunidaddocumentalId();
$uri = $_SERVER['REQUEST_URI'];
?>
<!-- filtros basicos de Archivo -->
<div class="row">
    <div class="col-md-12">		
    	<div class="panel-group joined" id="accordion-test-2">    		
    		<div class="panel panel-default">
    			<div class="panel-heading">
    				<h5 class="panel-title text-right text-danger">
    					<a data-toggle="collapse" data-parent="#accordion-test-2" href="#collapseTwo-2" class="collapsed">
    						<small>Filtros rapidos(clic aqui)</small>
    					</a>
    				</h5>
    			</div>
    			<div id="collapseTwo-2" class="panel-collapse collapse">
                    <div class="row">
                      <div class="col-md-12">  
                        <div class="panel-body">    				
        					<!-- BEGIN SECCION FILTROS RAPIDOS --->
                            <div id="filterlist">                                   
                                <div class="form-group">
                                    <label for="buscar_por" class="col-sm-1 control-label">Buscar por:</label>
                                    <div class="col-sm-2">         
                                        <select id="filters" name="filters" onchange="<?php echo jq_remote_function(array(
                                                      'update'=>'filterlist',
                                                      'url'=>'unidad_documental/loadFilterData?localizacionunidaddocumental_id='.$localizacion->getPrimaryKey(),
                                                      'with'=>"'&filterselect=' + this.options[this.selectedIndex].value",
                                                      'loading' => "javascript:jQuery.LoadingStructData();",
                                                      'complete' => "javascript:jQuery.CloseLoadingStructData();",
                                                      'script' => true,
                                                      ))
                                                    ?>" class="form-control input-sm" style="max-width: 250px;">
                                            <option value="" >Seleccione...</option>
                                            <?php foreach($listfilters as $key => $value){ ?>
                                                <option value="<?php echo $key ?>">
                                                <?php echo $value ?></option>
                                            <?php } ?>
                                        </select>        
                                    </div>                                                                          
                                </div>
                            </div>
                            <!-- END SECCION FILTROS RAPIDOS --->
                        </div>
                      </div>
                    </div>
	            </div>
   		    </div>    		
   	    </div>
    </div>
</div>
<!-- Fin filtros basicos de archivo -->