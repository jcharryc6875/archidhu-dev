<?php use_helper('Object') ?>

<?php echo input_hidden_tag('opcion',$opcion)?>
<?php echo input_hidden_tag('campoId',$campoId)?>
<?php echo input_hidden_tag('campoText',$campoText)?>
<?php echo object_input_hidden_tag($proveedor, 'getProveedorId') ?>

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
    <?php echo form_tag('proveedor/list',array('name'=>'consultar','method'=>'GET', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate')) ?>
    
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
      <label for="numero_solicitud" class="col-sm-1 control-label">Codigo SAP:</label>
      <div class="col-sm-4">
        <?php echo input_tag('codigoSap', '', array ('class'=>'form-control input-sm',)) ?>
      </div>
      <!-- Nombre -->
      <label for="numero_solicitud" class="col-sm-1 control-label">Naturaleza Juridica:</label>
      <div class="col-sm-4">
        <?php 
            echo object_input_tag($proveedor, 'getNaturalezaJuridica', array('class'=>'form-control input-sm',)) 
        ?>
      </div>
    </div>
    
    <div class="form-group">
      <!-- Nombre -->
      <label for="numero_solicitud" class="col-sm-1 control-label">Pais:</label>
      <div class="col-sm-4">
        <?php 
            echo object_select_tag($proveedor, 'getPaisId', array ('related_class' => 'Pais','include_custom'=>'Selecione...','selected'=>$sf_params->get('pais_id'), 'class'=>'form-control input-sm',)); ?>
      </div>
      <!-- Nombre -->
      <label for="numero_solicitud" class="col-sm-1 control-label">Estado:</label>
      <div class="col-sm-4">
        <?php  echo object_select_tag($proveedor, 'getProvEstadoId', array (
  'related_class' => 'ProvEstado', 'include_custom'=>'Selecione...','selected'=>$sf_params->get('prov_estado_id'), 'class'=>'form-control input-sm',)); ?>
      </div>
    </div>
    
    <div class="form-group">
      <!-- Nombre -->
      <label for="numero_solicitud" class="col-sm-2 control-label">Fecha Perido de validez Inicial:</label>
      <div class="col-sm-3">  
         <div class="input-group">
             <?php echo  input_tag('fecha_validez_inicial_1', ''/*time()*/, array('class'=>'form-control input-sm datepicker', 'data-format'=>'yyyy-mm-dd','readonly'=>'readonly', 'type'=>'text')); ?>
             <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
         </div>  
      </div>
      <!-- Nombre -->
      <label for="numero_solicitud" class="col-sm-2 control-label">Fecha Perido de validez Final:</label>
      <div class="col-sm-3">
        <div class="input-group">      
            <?php echo  input_tag('fecha_validez_final_1', ''/*time()*/, array('class'=>'form-control input-sm datepicker', 'data-format'=>'yyyy-mm-dd','readonly'=>'readonly', 'type'=>'text')); ?>
            <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
        </div>  
      </div>
    </div>
    
    <div class="form-group">
    <!-- Nombre -->
      <label for="numero_solicitud" class="col-sm-1 control-label">Ordenar por:</label>
      <div class="col-sm-4">
        <?php $ordenList = array('FECHA_CREACION'=>'Fecha Creacion','NOMBRE'=>'Proveedor','REPRESENTANTE_LEGAL'=>'Representante Legal') ?>
        <?php echo select_tag('ordenar' , options_for_select($ordenList,'',array('include_custom'=>'Seleccione',)), array( 'class'=>'form-control input-sm',)) ?>
      </div>      
    </div>
    
    <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-blue btn-icon">Consultar<i class="entypo-search"></i></button>
            <?php echo button_to('Deshacer','proveedor/consultageneral',array('class' => 'btn btn-red')); ?>
        </div>
    </div>
    <div class="clear"></div>    
    </form>
  </div>
</div>








