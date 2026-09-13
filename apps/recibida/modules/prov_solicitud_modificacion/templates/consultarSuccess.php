<?php use_helper('Object') ?>

<?php  ?>
<!-- Contenedor Pagina -->
<div class="panel panel-gradient" data-collapsed="0">
  <div class="panel-heading">
    <div class="panel-title">
      Consultar Solicitud De Modificacion
    </div>
  </div>

  <!-- Contenedor Contenido Formulario-->
  <div class="panel-body">      
    <?php echo form_tag('prov_solicitud_modificacion/index',array('name'=>'consultar','method'=>'GET', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate')) ?>
    <div class="form-group">
      <!-- Codigo Forma -->
      <label for="numero_solicitud" class="col-sm-1 control-label">Razon Social:</label>
      <div class="col-sm-4">
        <input type="text" name="proveedor_nombre" id="proveedor_nombre" value="" class="form-control input-sm" />
      </div>
      <!-- Tipo Procedimiento -->
      <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Estado:</label>
      <div class="col-sm-4">        
        <?php 
            echo object_select_tag($prov_solicitud_modificacion, 'getProvEstadoSolModId', array ('related_class' => 'ProvEstadoSolMod', 'include_custom'=>'Selecione...', 'class'=>'form-control input-sm',)); 
        ?>
      </div>
    </div>
    
    <div class="form-group">
      <!-- Dependencia -->
      <label for="ldependencia_id" class="col-sm-1 control-label">Usuario Solicitante:</label>
      <div class="col-sm-4"> 
        <?php echo object_select_tag($prov_solicitud_modificacion, 'getUsuarioId', array ('related_class' => 'Usuario', 'include_custom'=>'Selecione...', 'class'=>'form-control input-sm',)) ?>
      </div>
      
      <!-- Nombre -->
      <label for="lnombre" class="col-sm-1 control-label">Descripcion:</label>
      <div class="col-sm-4">
        <?php echo object_input_tag($prov_solicitud_modificacion, 'getDescripcion', array ('class'=>'form-control input-sm',))?>
      </div>
    </div>
    
    <div class="form-group">
      <!-- Nombre Archivo -->
      <label for="lextension" class="col-sm-1 control-label">Ordenar Por:</label>
      <div class="col-sm-4">
        <?php 
            $ordenList = array('FECHA_CREACION'=>'Fecha Creacion','NOMBRE'=>'Nombre Proveedor','USUARIO_ID'=>'Usuario Solicita','FECHA_RESPUESTA'=>'Fecha Respuesta');
            echo select_tag('ordenar' , options_for_select($ordenList,'',array('include_custom'=>'Seleccione',)),array('class'=>'form-control input-sm')); 
        ?>
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