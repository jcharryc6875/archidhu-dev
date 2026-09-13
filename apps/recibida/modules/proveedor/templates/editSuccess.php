<?php use_helper('Object') ?>

<div class="row">
  <div class="col-md-12">
    <?php
	// Include NavbarArchivo
	include_once("_navbar_proveedor.php");        
	?>
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          <?php echo $proveedor->getPrimaryKey() ? "Editar" : "Crear" ?> Proveedor
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('proveedor/update', array('name'=>'form1','role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        echo object_input_hidden_tag($proveedor, 'getProveedorId');
        ?>
        <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Razon Social:</label>
          <div class="col-sm-4">
            <?php echo input_tag('nombre', $proveedor->getNombre(), array ('class'=>'form-control input-sm',)) ?>
          </div>
          
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Nombre Comercial:</label>
          <div class="col-sm-4">
            <?php echo input_tag('nombre_comercial', ($proveedor->getNombreComercial()), array('class'=>'form-control input-sm',)) ?>
          </div>
      </div>
      
      <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Nit:</label>
          <div class="col-sm-4">
            <?php echo input_tag('nit',$proveedor->getNit(), array ('class'=>'form-control input-sm',)) ?>
          </div>
          
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Representante Legal:</label>
          <div class="col-sm-4">
            <?php echo input_tag('representante_legal',($proveedor->getRepresentanteLegal()), array('class'=>'form-control input-sm',)) ?>
          </div>
      </div>
      
      <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Identificacion Rl:</label>
          <div class="col-sm-4">
            <?php echo input_tag('identificacion_rl',$proveedor->getIdentificacionRl(), array ('class'=>'form-control input-sm',)) ?>
          </div>
          
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Direccion:</label>
          <div class="col-sm-4">
            <?php echo input_tag('direccion',$proveedor->getDireccion(), array('class'=>'form-control input-sm',)) ?>
          </div>
      </div>
      
      <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Telefono:</label>
          <div class="col-sm-4">
            <?php echo input_tag('telefono',$proveedor->getTelefono(), array ('class'=>'form-control input-sm',)) ?>
          </div>
          
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Naturaleza Juridica:</label>
          <div class="col-sm-4">
            <?php echo input_tag('naturaleza_juridica',$proveedor->getNaturalezaJuridica(), array('class'=>'form-control input-sm',)) ?>
          </div>
      </div>
      
      <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Fecha Ingreso:</label>
          <div class="col-sm-4">          
<?php // echo object_input_date_tag($proveedor, 'getFechaIngreso', array ('rich' => true, 'readonly'=>'true','class'=>'jsrequired',)) ?>
            <div class="input-group">
             <?php echo  input_tag('getFechaIngreso', $proveedor, array('class'=>'form-control input-sm datepicker', 'data-format'=>'yyyy-mm-dd','readonly'=>'readonly', 'type'=>'text')); ?>
             <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
         </div>

            
          </div>
          
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Pais:</label>
          <div class="col-sm-4">
            <?php echo object_select_tag($proveedor, 'getPaisId', array ('related_class' => 'Pais','class'=>'form-control input-sm required', 'include_custom'=>'Selecione...','selected'=>$sf_params->get('pais_id'),)) ?>
          </div>
      </div>
            
      <div class="form-group">
      </div>
      
      <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success"><?php echo $proveedor->getPrimaryKey() ? "Guardar Cambios" : "Guardar"?> </button>
            <?php 
            if($proveedor->getPrimaryKey()){ 
            echo button_to('Regresar a proveedor','proveedor/show?proveedor_id=' . $proveedor->getPrimaryKey(), array('class' => 'btn btn-red')); 
             } ?>
        </div>
      </div>
      <div class="clear"></div>
      </form>
      </div>
    </div>
  </div>
</div>