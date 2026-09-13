<?php use_helper('Object') ?>

<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Liberar Periodo para Aprobacion
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('prov_periodo_validez/updatepaso1', array('name'=>'form1','role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        echo object_input_hidden_tag($prov_periodo_validez, 'getProvPeriodoValidezId');
        ?>
        <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Observaciones:</label>
          <div class="col-sm-4">
            <?php echo textarea_tag('observaciones','',array('cols'=>50, 'class'=>'form-control input-sm',)); ?>
          </div>
        </div>
         <div class="form-group"> 
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Usuario de Compras:</label>
          <div class="col-sm-4">
            <?php // echo input_tag('nombre_comercial', utf8_decode($proveedor->getNombreComercial()), array('class'=>'form-control input-sm',)) ?>
            
            <select name="usuario_compras_id" class="form-control input-sm"><option value="0">Seleccione... </option>
              <?php 
              //echo object_input_tag($prov_periodo_validez, 'getObservaciones', array ('size' => 80,));
              
              foreach($usuarios_compras as $usuarios_compra)
              {
            	echo "<option value=".$usuarios_compra->getUsuarioId().">".$usuarios_compra->getUsuario()."</option>";
              }
               ?>
            </select>
            
          </div>
        </div>
      
      <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-5">        
            <button type="submit" class="btn btn-success">Aprobar y enviar</button>
            <?php 
            echo button_to('Regresar a proveedor','prov_periodo_validez/show?prov_periodo_validez_id=' . $prov_periodo_validez->getPrimaryKey(), array('class' => 'btn btn-red')); 
            ?>
        </div>
      </div>
      <div class="clear"></div>
      </form>
      </div>
    </div>
  </div>
</div>