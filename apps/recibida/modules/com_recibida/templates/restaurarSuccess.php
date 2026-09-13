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
          Comunicaciones Recibidas Restauradas
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
      <?php 
        echo form_tag('com_recibida/uploads', array('name'=>'form1', 'multipart'=> true, 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
      ?>
       <div class="form-group">
          <!-- adjuntos -->          
          <div class="col-sm-2">            
              <?php 
                echo label_for('msg1' ,'Las  comunicaciones fueron restauradas satisfactoriamente', array ('class'=>'form-control input-sm',));
              ?>
          </div>          
      </div>
      <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-5">
            <button type="button" class="btn btn-default btn-sm" onclick="javascript:parent.jQuery.ReloadAndCloseModalSIMAD();"><i class="entypo-cancel-circled"></i>Finalizar</button>
        </div>
      </div>
      <div class="clear"></div>
      </form>
      </div>
    </div>
  </div>
</div>    