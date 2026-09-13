<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');

$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');
?>
<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
        <?php 
        if($sf_params->get('cod_msg') == 1)
        { 
        ?>
            <div class="alert alert-danger">
                <p>Los expediente se actualizaron exitosamente!</p>
            </div>
        <?php
        }elseif($sf_params->get('cod_msg') == 2){ 
        ?>
            <div class="alert alert-danger">
                <p>Se encontro un error y no se pudo realizar el proceso solicitado!</p>
            </div>    
        <?php
        } 
        ?>  
      <div class="panel-heading">
        <div class="panel-title">
          Remplazar Datos Expedientes
        </div>
      </div>

      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('unidad_documental/updateReplaceData', array('name'=>'form1', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate')); 
        echo input_hidden_tag('localizacionunidaddocumental_id', $localizacion);
        echo input_hidden_tag('modulo', $sf_params->get('modulo'));
        ?>
        
        <div class="form-group">  
          <!-- remplazarpor -->
          <label for="remplazarpor" class="col-sm-1 control-label">Remplazar campo:</label>
          <div class="col-sm-6">
            <?php 
            $dataList = array('UBICACION'=>'Ubicacion','TITULO'=>'Nombre Expediente','CONTENIDO'=>'Contenido','SUBSERIE_ID'=>'Subserie', 'LOCALIZACION'=>'Localizaci&oacute;n');
            echo select_tag('data_column' , options_for_select($dataList,'',array('include_custom'=>'Seleccione...',)), 
                array('class' => 'form-control input-sm','onchange'=>jq_remote_function(array(
                   'update' => 'newvalue',
                   'url' => 'unidad_documental/replaceDataTrd',
                   'with' => "'id_seleccionado=' + this.options[this.selectedIndex].value + '&combo_seleccionado=dependencias&form_tag=1&id_permiso=1&localizacionunidaddocumental_id=$localizacion'",
                   //'loading'  => "Element.show('indicator_fondo');Element.show('indicator');",
                   'script' => true,
                   'position' => 'before',
                   //'complete' => "Element.hide('indicator_fondo');Element.hide('indicator');",
             ))));
            ?>
          </div>
        </div>
        
        <div id="newvalue">
            <div class="form-group">
              <!-- Observaciones -->
              <label for="Observaciones" class="col-sm-1 control-label">Observaciones:</label>
              <div class="col-sm-6">
                <?php 
                    echo textarea_tag('new_value', '', array('class' => 'form-control input-sm','size' => '30x3'));
                ?>                                
              </div>
            </div>
        </div>
        
        <?php
        if(trim($localizacion)){
        ?>
        <div id="elmfechacierre" style="display: none;">
            <div class="form-group">
                <label for="fecha_cierre" class="col-sm-1 control-label">Fecha Final:</label>
                <div class="col-sm-2">
                    <div class="input-group">
                      <?php
                        echo input_tag('fecha_cierre', null, array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd'));
                      ?>                  
                      <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        }
        ?>
        
        <div class="form-group">
          <!-- Botonera -->
          <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success" onclick="return confirm('Esta seguro de realizar esta acci&oacute;n, los cambios no se podran revertir?');">Aceptar y Remplazar</button>
            <?php echo jq_link_to_function('Cancelar','javascript:parent.jQuery.CloseModalSIMAD();',array('class' => 'btn btn-red ')); ?>
          </div>
        </div>
        <div class="clear"></div>
        </form>
      </div>
    </div>
  </div>
</div>