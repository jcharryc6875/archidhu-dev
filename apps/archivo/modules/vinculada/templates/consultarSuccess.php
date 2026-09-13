<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

use_helper('Object');
?>
<div class="row">

  <div class="col-md-12">

    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">

      <div class="panel-heading">
        <div class="panel-title">
          Consultar Gestión - Vinculada
        </div>
      </div>

      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php
        echo form_tag('vinculada/list', array('name'=>'consultar', 'method'=>'GET', 'class' => 'form-horizontal form-groups-bordered'));
        ?>

        <div class="form-group">  
          <!-- Descripcion -->  
          <label for="descripcion" class="col-sm-2 control-label">Descripción</label>
          <div class="col-sm-4">
            <?php 
            echo input_tag('descripcion', '', array('class'=>'form-control input-sm'));
            ?>
          </div>
        </div>

        <div class="form-group">  
          <!-- Descripcion -->  
          <label for="estado" class="col-sm-2 control-label">Estado</label>
          <div class="col-sm-4">
            <?php 
            $opciones = array('' => 'Pendiente', '1' => 'Aceptado', '2' => 'Rechazado');
            echo select_tag('estado', options_for_select($opciones), array('class' => 'form-control input-sm'));
            ?>
          </div>
        </div>

        <div class="form-group">
          <!-- Botonera -->
          <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success">Consultar Vinculada</button>
            <a href="<?php print url_for('vinculada/consultar');?>"><button type="button" class="btn btn-default">Cancelar</button></a>
            <a href="<?php print url_for('vinculada/list');?>"><button type="button" class="btn btn-default">Regresar</button></a>
          </div>
        </div>
        <div class="clear"></div>

        </form>
      </div>
    </div>
  </div>

</div>