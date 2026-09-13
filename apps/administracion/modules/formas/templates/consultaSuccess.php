<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>

<!-- Contenedor Pagina -->
<div class="panel panel-gradient" data-collapsed="0">
  <div class="panel-heading">
    <div class="panel-title">
      Consultar Privilegio
    </div>    
  </div>

  <!-- Contenedor Contenido Formulario-->
  <div class="panel-body">      
    <?php echo form_tag('formas/list',array('name'=>'consultar','method'=>'GET', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate')) ?>
    <div class="form-group">
      
      <!-- Modulo: -->
      <label for="numero_solicitud" class="col-sm-1 control-label">Modulo:</label>
      <div class="col-sm-4">
        <?php 
            echo object_select_tag($forma, 'getModuloId', array ('related_class' => 'Modulo','include_custom'=>'Seleccione...', 'class'=>'form-control input-sm',)) 
        ?>
      </div>
    </div>
    
    <div class="form-group">
        <!-- Nombre: -->
        <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Nombre:</label>
        <div class="col-sm-4">
        <!--input size=43 name=nombre type=text value=""/-->
        <?php 
           echo input_tag('nombre', '', array('class'=>'form-control input-sm')); 
        ?> 
        </div>
    </div>
    
    <div class="form-group">
      <!-- Descripción: -->
      <label for="ldependencia_id" class="col-sm-1 control-label">Descripci&oacute;n:</label>
      <div class="col-sm-4">
        <!--input size=43 name=descripcion type=text value=""/-->
        <?php 
           echo input_tag('descripcion', '', array('class'=>'form-control input-sm')); 
        ?>
      </div>
    </div>
    
    
    <div class="form-group">
            <!-- Ordenar Por: -->
          <label for="lnombre" class="col-sm-1 control-label">Ordenar Por:</label>
          <div class="col-sm-4">
            <?php
             $ordenList = array('NOMBRE'=>'Nombre','DESCRIPCION'=>'Descripcion');
             echo select_tag('orden' , options_for_select($ordenList,'',array('include_custom'=>'Seleccione',)), array('class'=>'form-control input-sm',));
            ?>
            
            <?php 
                /*
                echo object_select_tag($forma, 'getTipoprocedimientoId', array (
                'related_class' => 'TipoProcedimiento','include_custom'=>'Seleccione...', 'class'=>'form-control input-sm required',
                ));
                */
                ?>
          </div>
      </div>
      
    
    
    <div class="form-group">    
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-5">
            <!--input type="image" name="commit" align="middle" src="/images/simad/ico_consultar.png" alt="Consultar" name="commit"  border="0" id="Login" /-->
            <button type="submit" class="btn btn-success">Consultar</button>
            <!--button type="button" class="btn btn-default">Deshacer</button-->
        </div>    
    </div>
    <div class="clear"></div>    
    </form>
  </div>
</div>