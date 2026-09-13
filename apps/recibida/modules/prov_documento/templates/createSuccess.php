<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>

<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Adjuntar Documento Del Proveedor
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('prov_documento/updatecreate', array('name'=>'form1','multipart=true', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        echo object_input_hidden_tag($prov_documento, 'getProvDocumentoId');
        echo input_hidden_tag('prov_periodo_validez_id', $prov_periodo_validez_id);
        ?>
        
        <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Tipo de Documento:</label>
          <div class="col-sm-2">       
            <?php echo object_select_tag($prov_documento, 'getProvListaDocsId', array ('related_class' => 'ProvListaDocs','class'=>'form-control input-sm required','include_custom'=>'Seleccione...')) ?>
          </div>          
        </div>
        
        <div class="form-group">          
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Fecha Recibido:</label>
          <div class="col-sm-2">               
            <div class="input-group">
             <?php echo  input_tag('fecha_recibido', '', array('class'=>'form-control input-sm datepicker required', 'data-format'=>'yyyy-mm-dd', 'type'=>'text', 'readonly'=>'true',)); ?>
             <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>
        </div>
        
        <div class="form-group">
          <!-- Adjuntos -->
          <label for="lbadjuntos" class="col-sm-1 control-label">Adjunto:</label>
          <div class="col-sm-6">
              <div class="input-group"> 
                <?php 
                $ruta = explode(',',$prov_documento->getRuta());    		    
                $archivos = "";
    		    $archivos .= trim(basename($ruta[0]));
                ?>
                <input class="form-control input-sm required" name="ruta" id="ruta" readonly="true" type="text" value="<?php echo $archivos ?>" />                           
                <div class="input-group-btn">         
                    <button type="button" class="btn btn-primary btn-sm" onclick="javascript:jQuery.OpenModalSIMAD('<?php print $base_path;?>/recibida.php/prov_documento/file', 480, 320); return false;">Seleccionar</button>
                    <button type="button" class="btn btn-default btn-sm" onclick="javascript:jQuery.LimpiarCampoFormulario('ruta');"><i class="entypo-cancel-circled"></i></button>
                </div>
              </div>                
          </div>
          <?php if($prov_documento->getPrimaryKey()){ ?>
              <!-- Imagenes -->
              <label for="lbadjuntos" class="col-sm-1 control-label">Imagenes:</label>
              <div class="col-sm-2">
                <div class="input-group"> 
                    <?php  
                     $arr = explode(",",$prov_documento->getRuta());
                     foreach ($arr as $result):
                        if(trim(basename($result))):
                    ?>
                            <a href="<?php  echo $result; ?>" target="_blank" class="tooltip-primary" data-toggle="tooltip" data-original-title="<?php echo basename($result) ?>">
                                <img width="25" align="middle" src="<?php echo $base_path; ?>/images/simad/ico_ver_adj.png"/>
                            </a>
                    <?php
                        endif; 
                     endforeach; 
                    ?>
                </div>
              </div>
          <?php } ?>
        </div>

        <div class="form-group">         
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-2 control-label">Verificacion:</label>
          <div class="col-sm-4">        
            <?php echo object_select_tag($prov_documento, 'getProvEstadoDocID', array ('related_class' => 'ProvEstadoDoc','class'=>'form-control input-sm required','include_custom'=>'Seleccione...')); ?>
          </div>
        </div>
      
        <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-2 control-label">Observaciones:</label>
          <div class="col-sm-6"> 
            <?php  
                echo object_textarea_tag($prov_documento, 'getObservaciones', array ('class'=>'form-control input-sm required',)); 
            ?>
          </div>
        </div>
      
        <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success"><?php echo $prov_documento->getPrimaryKey() ? "Guardar Cambios" : "Guardar Documento"?> </button>
                <?php 
                if ($prov_documento->getPrimaryKey()):                 
                    echo button_to('Eliminar','prov_documento/delete?prov_documento_id=' . $prov_documento->getPrimaryKey().'&confirm=Esta Seguro?', array('class' => 'btn btn-white btn-sm tooltip-primary', "data-toggle" => "tooltip", "data-original-title" => "Eliminar Documento")); 
                endif; 
                ?>
        </div>
      </div>
      <div class="clear"></div>
      </form>
      </div>
    </div>
  </div>
</div>