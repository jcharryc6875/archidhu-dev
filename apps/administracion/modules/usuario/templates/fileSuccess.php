<?php
use_helper('Object','jQuery');
?>
<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Seleccionar Archivo
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
      <?php 
        echo form_tag('usuario/uploads', array('name'=>'subir', 'multipart'=> true, 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
		echo input_hidden_tag('efirma',$sf_params->get('qvars'));
      ?>
      
       <?php if ($sf_request->hasError('file')): ?>
            <?php 
            echo '<div class="alert alert-danger">'. $sf_request->getError('file') .'</div>' 
            ?> 
       <?php endif; ?>

      <div class="form-group">
          <!-- adjuntos -->          
          <div class="col-sm-2">
            <div class="input-group">
              <?php 
                echo input_file_tag('file' ,'', array ('class'=>'data-readonly form-control input-sm required',));
              ?>              
            </div>
          </div>          
      </div>
      <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success">Adjuntar</button>
        </div>
      </div>
      <div class="clear"></div>
      </form>
      </div>
    </div>
  </div>
</div>