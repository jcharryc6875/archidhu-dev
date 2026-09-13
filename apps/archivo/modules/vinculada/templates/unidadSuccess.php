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
          Consultar Unidad Documental
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('vinculada/resultado', array('name'=>'form1','method'=>'GET', 'class' => 'form-horizontal form-groups-bordered'));
        ?>        
        
        <div class="form-group">            
          <!-- Codigo de Barras -->
          <label for="lbcodigo_barras" class="col-sm-2 control-label">Codigo Barras:</label>
          <div class="col-sm-8">
            <?php echo input_tag('codigo_barras' , '', array('class'=>'form-control input-sm')); ?>
          </div>
        </div>
        
        <div class="form-group">            
          <!-- Titulo -->
          <label for="lbTitulo" class="col-sm-2 control-label">Titulo:</label>
          <div class="col-sm-8">
            <?php echo input_tag('titulo' , '', array('class'=>'form-control input-sm')); ?>
          </div>
        </div>
        
        <div class="form-group">
            <!-- Botonera -->
            <div class="col-sm-offset-4 col-sm-5">
                <button type="submit" class="btn btn-blue btn-icon">Consultar Expediente<i class="entypo-search"></i></button>             
                <?php echo button_to('Deshacer','vinculada/unidad',array('class' => 'btn btn-red')); ?>
            </div>
        </div>
        <div class="clear"></div>    
      </form>
      </div>
    </div>
  </div>
</div>