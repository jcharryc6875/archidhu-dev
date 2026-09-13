<?php use_helper('Object') ?>

<!-- Contenedor Pagina -->
<div class="panel panel-gradient" data-collapsed="0">
  <div class="panel-heading">
    <div class="panel-title">
      Consultar Privilegios por Perfil
    </div>    
  </div>

  <!-- Contenedor Contenido Formulario-->
  <div class="panel-body">      
    <?php 
    echo form_tag('formas_por_rol/list',array('name'=>'consultar','method'=>'GET', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate')) ?>
    <div class="form-group">
      <!-- Modulo: -->
      <label for="numero_solicitud" class="col-sm-1 control-label">Perfil:</label>
      <div class="col-sm-4">
        <?php echo object_select_tag($rolPrivilegio, 'getRolId', array ('related_class' => 'Rol','include_custom'=>'Seleccione...', 'class'=>'form-control input-sm',)) ?>
      </div>
    </div>
    
    <div class="form-group">
      <!-- Nombre: -->
      <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Privilegio:</label>
      <div class="col-sm-4">
        <!--input size=43 name=nombre type=text value=""/-->                
        <?php echo object_select_tag($rolPrivilegio, 'getFormaId', array ('peer_method'=>'getOrdenForma','related_class' => 'Forma','include_custom'=>'Seleccione...', 'class'=>'form-control input-sm select2',)) ?>
      </div>
    </div>
    
    <div class="form-group">      
      <!-- Ordenar Por: -->
      <label for="lnombre" class="col-sm-1 control-label">Ordenar Por:</label>
      <div class="col-sm-4">        
        <?php $ordenList = array('ROL'=>'Perfil','FORMA'=>'Privilegio') ?>
        <?php echo select_tag('orden' , options_for_select($ordenList,'',array('include_custom'=>'Seleccione',)), array('class'=>'form-control input-sm',)); ?>       
      </div>
    </div>
    
    <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success">Consultar</button>
        </div>    
    </div>
    <div class="clear"></div>    
    </form>
  </div>
</div>