<?php use_helper('Form','jQuery','Object') ?>
<?php if($is_display){ ?>
<?php echo javascript_tag("
   jQuery('#newvalue').hide();
   jQuery('#rowlocalizacion').remove();
   jQuery('#elmfechacierre').hide();
") ?>
<div id="trditemdependencia">
  <div class="form-group">  
    <!-- Dependencia -->
    <label for="remplazarpor" class="col-sm-1 control-label">Dependencia:</label>
    <div class="col-sm-6">
        <input type="hidden" name="form_tag" id="form_tag" value="<?php echo $form_tag ?>"/>  
        <input type="hidden" name="permiso_id" id="permiso_id" value="<?php echo $permiso?>"/>
        <select class="form-control input-sm" name="dependencia_id" id="dependencia_id" onchange="<?php
        	echo jq_remote_function(array(
               'update' => 'cbseries',
               'url' => 'unidad_documental/cargarCombos',
               'with' => "'id_seleccionado=' + this.options[this.selectedIndex].value + '&combo_seleccionado=dependencias&form_tag='+jQuery('#form_tag').val()+'&id_permiso=".$permiso."&addcomp=1'",
               'loading'  => "jQuery('#serie_id').prop('disabled','disabled');jQuery('#subserie_id').prop('disabled','disabled');jQuery('#serie_id').val('0');jQuery('#subserie_id').val('0');",
               //'complete' => visual_effect('fade', 'indicator_fondo').visual_effect('fade', 'indicator'),
             )) ?>">
    	   <option value="">Seleccione Dependencia...</option>
         <?php
        		$dependencia_compuesto = "";
        		foreach($dependencias as $dependencia)
                {
                  $dependencia_nombre = $dependencia->getNombre();
                  $dependencia_nombre = ($dependencia_nombre);
                  $dependencia_compuesto = $dependencia_nombre.(trim($dependencia->getCodigo()) ? " - ".$dependencia->getCodigo()." - " : " - ").$dependencia->getTipoTabla()." - ".$dependencia->getEntidad();
          		  echo "<option value='".$dependencia->getDependenciaId()."'";      			
        		  echo ">".$dependencia_compuesto."</option>";
        		}
         ?> 	  
    	</select>
  </div>
 </div> 
</div>

<div id="trditemserie">
  <div class="form-group">  
    <!-- Series -->
    <label for="remplazarpor" class="col-sm-1 control-label">Series:</label>
    <div class="col-sm-6">
        <div id="cbseries">
            <select class="form-control input-sm" id="serie_id" name="serie_id" disabled="disabled">                
                	<option value="" selected="selected">Seleccione Dependencia</option>
            </select>
        </div>
    </div>
  </div>
</div>

<div id="trditemsubserie">
  <div class="form-group">  
    <!-- Subseries -->
    <label for="remplazarpor" class="col-sm-1 control-label">Subseries:</label>
    <div class="col-sm-6">
        <div id="cbsubserie">
            <select class="form-control input-sm required" id="subserie_id" name="subserie_id" disabled="disabled">            
                <option value="" selected="selected">Seleccione Serie</option>
            </select>
        </div>        
    </div>
  </div>
</div>
<?php }elseif($display_localizacion){ ?>
<?php echo javascript_tag("        
    if(jQuery('#trditemsubserie') != null)
    {
        jQuery('#trditemsubserie').remove();
        jQuery('#trditemserie').remove();
        jQuery('#trditemdependencia').remove();
    }
    jQuery('#newvalue').hide();
    jQuery('#elmfechacierre').show();
") ?>
<div id="rowlocalizacion">
    <div class="form-group">
      <!-- Localizacion -->
      <label for="lbLocalizacion" class="col-sm-1 control-label">Localizaci&oacute;n:</label>
      <div class="col-sm-6">
        <select class="form-control input-sm required" name="newlocalizacion" id="newlocalizacion">
            <option value="">Seleccione...</option>
             <?php
            		foreach($list_localizacion as $item)
                    {
              		  echo "<option value='".$item->getPrimaryKey()."'";      			
            		  echo ">".$item->getDescripcion()."</option>";
            		}
             ?> 	  
    	</select>
      </div>
    </div>    
</div>
<?php }else{ ?>
    <?php echo javascript_tag("        
        if(jQuery('#trditemsubserie') != null)
        {
            jQuery('#trditemsubserie').remove();
            jQuery('#trditemserie').remove();
            jQuery('#trditemdependencia').remove();
            jQuery('#rowlocalizacion').remove();
        }
        jQuery('#newvalue').show();
        jQuery('#elmfechacierre').hide();
    ") ?>
<?php } ?>