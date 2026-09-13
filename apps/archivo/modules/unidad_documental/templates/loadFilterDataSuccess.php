<?php 
use_helper('Form','jQuery');
?>
<div class="row">
  <div class="col-md-12">  
    <div class="panel-body">       
        <div class="form-group">
            <form id="sform1" name="sform1" method="POST" action="<?php print 'unidad_documental/listAsync?localizacionunidaddocumental_id='.$localizacion_id.$filtros_consulta; ?>" class="" role="form">     
            <label for="buscar_por" class="col-sm-1 control-label">Buscar por:</label>
            <div class="col-sm-2">        
                <select id="filters" name="filters" onchange="<?php echo jq_remote_function(array(
                              'update'=>'filterlist',
                              'url'=>'unidad_documental/loadFilterData?localizacionunidaddocumental_id='.$localizacion_id,              
                              'with'=>"'&filterselect=' + this.options[this.selectedIndex].value",
                              'loading' => "javascript:jQuery.LoadingStructData();",
							  'complete' => "javascript:jQuery.CloseLoadingStructData();",
                              'script' => true,
                              ))
                            ?>" class="form-control input-sm" style="max-width: 250px">
                    <option value="" >Seleccione...</option>
                    <?php foreach($listfilters as $key => $value){ ?>
                        <option value="<?php echo $key ?>"
                        <?php if($key == $filterselect){ ?>  selected="selected" <?php } ?>>
                        <?php echo $value ?></option>
                    <?php } ?>
                </select>
            </div>
            <?php if(trim($filterselect)){ ?>       
            <?php
            if(mb_strtolower($filterselect) == "codigo_barras")
            {
                echo '<div class="col-sm-2">';
                echo '<input class="form-control input-sm" type="text" size="50" value="" id="codigo_barras" name="codigo_barras" placeholder="Digite Id expediente..." style="max-width: 250px">';
                echo '</div>';
            }
            if(mb_strtolower($filterselect) == "titulo")
            {
                echo '<div class="col-sm-2">';
                echo '<input class="form-control input-sm" type="text" size="50" value="" id="titulo" name="titulo" placeholder="Digite titulo..." style="max-width: 250px">';
                echo '</div>';
            }
            
            if(mb_strtolower($filterselect) == "ubicacionenhistorico")
            {
                echo '<div class="col-sm-2">';
                echo '<input class="form-control input-sm" type="text" size="50" value="" id="ubicacionenhistorico" name="ubicacionenhistorico" placeholder="Digite ubicacion..." style="max-width: 250px">';
                echo '</div>'; 
            }
            
            if(mb_strtolower($filterselect) == "ubicacionencentral")
            {
                echo '<div class="col-sm-2">';
                echo '<input class="form-control input-sm" type="text" size="50" value="" id="ubicacionencentral" name="ubicacionencentral" placeholder="Digite ubicacion..." style="max-width: 250px">';
                echo '</div>';
            }
            
            if(mb_strtolower($filterselect) == "ubicacionengestion")
            {
                echo '<div class="col-sm-2">';
                echo '<input class="form-control input-sm" type="text" size="50" value="" id="ubicacionengestion" name="ubicacionengestion" placeholder="Digite ubicacion..." style="max-width: 250px">';
                echo '</div>';
            }
            
            if(mb_strtolower($filterselect) == "dependencia" || $loadareas){ ?>
            <div class="col-sm-2">
                <select class="form-control input-sm" name="dependencia_id" id="dependencia_id" onchange="<?php
                	echo jq_remote_function(array(
                       'update' => 'filterlist',
                       'url' => 'unidad_documental/loadFilterData?localizacionunidaddocumental_id='.$localizacion_id,
                       'with' => "'dependencia_id=' + this.options[this.selectedIndex].value + '&combo_load=serie'",
                       'loading' => "javascript:jQuery.LoadingStructData();",
                       'complete' => "javascript:jQuery.CloseLoadingStructData();",
                     )) ?>" style="max-width: 250px">
                   <option value="">Seleccione Dependencia...</option>
                 <?php
                		$dependencia_compuesto = "";
                		foreach($dependencias as $dependencia)
                        {
                          $dependencia_nombre = $dependencia->getNombre();
                          $dependencia_nombre = ($dependencia_nombre);
                          $dependencia_compuesto = $dependencia_nombre.(trim($dependencia->getCodigo()) ? " - ".$dependencia->getCodigo()." - " : " - ").$dependencia->getTipoTabla()." - ".$dependencia->getEntidad();
                  		  echo "<option value='".$dependencia->getDependenciaId()."'";
                          if($dependencia->getDependenciaId() == $dependencia_id){ echo 'selected="selected"'; }
                		  echo ">".$dependencia_compuesto."</option>";
                		}
                 ?> 	  
                </select>
            </div>            
            <?php } ?>
            
            <?php if($comboload == "serie" || $loadseries){ ?>
            <div class="col-sm-2">
                <select class="form-control input-sm" name="serie_id" id="serie_id" onchange="<?php
                	echo jq_remote_function(array(
                       'update' => 'filterlist',
                       'url' => 'unidad_documental/loadFilterData?localizacionunidaddocumental_id='.$localizacion_id,
                       'with' => "'&dependencia_id='+ jQuery('#dependencia_id').val() + '&serie_id=' + this.options[this.selectedIndex].value + '&combo_load=subserie'",
					   'loading' => "javascript:jQuery.LoadingStructData();",
                       'complete' => "javascript:jQuery.CloseLoadingStructData();",
                     )) ?>" style="max-width: 250px">
                   <option value="">Seleccione serie...</option>
                 <?php		
                		foreach($series as $serie)
                        {          
                          $nombre_compuesto = $serie->getDescripcion();          
                  		  echo "<option value='".$serie->getSerieId()."'";
                          if($serie->getSerieId() == $serie_id){ echo 'selected="selected"'; }
                		  echo ">".$nombre_compuesto."</option>";
                		}
                 ?> 	  
                </select>
            </div>            
            <?php } ?>
            
            <?php if($comboload == "subserie" || $loadsubseries){ ?>
            <div class="col-sm-2">
                <select class="form-control input-sm" name="subserie_id" id="subserie_id" style="max-width: 250px">
                <option value="">Seleccione subserie...</option>
                <?php		
            	foreach($subseries as $subserie)
                {          
                  $nombre_compuesto = $subserie->getDescripcion();
            	  echo "<option value='".$subserie->getSubserieId()."'";      			
            	  echo ">".$nombre_compuesto."</option>";
            	}
                ?> 	  
                </select>
            </div>            
            <?php } ?>
            
            <div class="col-sm-1">
            <?php echo jq_submit_to_remote('buscar','Buscar expedientes',array(
                            'url'      => 'unidad_documental/listAsync?localizacionunidaddocumental_id='.$localizacion_id.$_SESSION['parametros_consulta'],
                            'update'   => 'tablelist',
                            'loading' => "javascript:jQuery.LoadingStructData();",
							'complete' => "javascript:jQuery.CloseLoadingStructData();",
                            'script' => true,
                          ),array('class'=>'btn btn-success' ,'method' => 'post','title' => 'Buscar expedientes','id'=>'buscar'));
            ?>
            </div>
        <?php } ?>
        </form>
        </div>
    </div>
  </div>
</div>