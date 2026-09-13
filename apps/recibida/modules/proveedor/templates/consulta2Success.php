<?php use_helper('Object') ?>

<?php echo input_hidden_tag('opcion',$opcion)?>
<?php echo input_hidden_tag('campoId',$campoId)?>
<?php echo input_hidden_tag('campoText',$campoText)?>

<?php  ?>
<!-- Contenedor Pagina -->
<div class="panel panel-gradient" data-collapsed="0">
  <div class="panel-heading">
    <div class="panel-title">
      Consultar Proveedor
    </div>    
  </div>

  <!-- Contenedor Contenido Formulario-->
  <div class="panel-body">      
    <?php echo form_tag('proveedor/escogelist2',array('name'=>'consultar','method'=>'GET', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate')) ?>
    
    <div class="form-group">
      <!-- Nombre -->
      <label for="numero_solicitud" class="col-sm-1 control-label">Nombre:</label>
      <div class="col-sm-4">
        <?php 
            echo object_input_tag($proveedor, 'getNombre', array('class'=>'form-control input-sm',)) 
        ?>
      </div>
      <!-- Nombre -->
      <label for="numero_solicitud" class="col-sm-1 control-label">Nit:</label>
      <div class="col-sm-4">
        <?php 
            echo object_input_tag($proveedor, 'getNit', array ('class'=>'form-control input-sm',)); 
        ?>
      </div>
    </div>
    
    <div class="form-group">
      <!-- Nombre -->
      <label for="numero_solicitud" class="col-sm-1 control-label">Representante Legal:</label>
      <div class="col-sm-4">
        <?php echo object_input_tag($proveedor, 'getRepresentanteLegal', array ('class'=>'form-control input-sm',)); ?>
      </div>
      <!-- Nombre -->
      <label for="numero_solicitud" class="col-sm-1 control-label">Identificacion R1:</label>
      <div class="col-sm-4">
        <?php 
            echo object_input_tag($proveedor, 'getIdentificacionRl', array('class'=>'form-control input-sm',)) 
        ?>
      </div>
    </div>
    
    <div class="form-group">
      <!-- Nombre -->
      <label for="numero_solicitud" class="col-sm-1 control-label">Direccion:</label>
      <div class="col-sm-4">
        <?php 
            echo object_input_tag($proveedor, 'getDireccion', array('class'=>'form-control input-sm',)) 
        ?>
      </div>
      <!-- Nombre -->
      <label for="numero_solicitud" class="col-sm-1 control-label">Telefono:</label>
      <div class="col-sm-4">
        <?php 
            echo object_input_tag($proveedor, 'getTelefono', array('class'=>'form-control input-sm',)) 
        ?>
      </div>
    </div>
    
    <div class="form-group">
    <!-- Nombre -->
      <label for="numero_solicitud" class="col-sm-1 control-label">Naturaleza Juridica:</label>
      <div class="col-sm-4">
        <?php 
            echo object_input_tag($proveedor, 'getNaturalezaJuridica', array('class'=>'form-control input-sm',)) 
        ?>
      </div>      
    </div>
    
    <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-5">            
            <button type="submit" class="btn btn-blue btn-icon">Consultar<i class="entypo-search"></i></button>
            <button type="button" class="btn btn-default">Deshacer</button>
        </div>
    </div>
    <div class="clear"></div>    
    </form>
  </div>
</div>
