<?php use_helper('Object') ?>
<?php use_helper('jQuery')?>
<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Escoger Archivo
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
      <?php 
        echo form_tag('prov_documento/uploads', array('name'=>'subir', 'multipart'=> true, 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
      ?>
      
      <div class="form-group">
          <!-- adjuntos -->          
          <div class="col-sm-2">
            <div class="input-group">
              <?php if ($sf_request->hasError('file')): ?>
              <?php echo $sf_request->getError('file') ?>
              <?php endif; ?>
              <?php echo input_file_tag('file' ,'', array ('readonly'=>'true', 'class'=>'form-control input-sm',));?>       
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