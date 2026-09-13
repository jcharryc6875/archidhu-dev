<?php use_helper('Object') ?>
<?php use_helper('jQuery') ?>

<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Enviar Alertas De Recordatorio
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('prov_periodo_validez/reloadAlertas', array('name'=>'form1','role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        echo input_hidden_tag('prov_periodo_validez_id',$prov_periodo_validez_id);
        ?>

        <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Impuestos:</label>
          <div class="col-sm-4">
            <?php echo checkbox_tag('impuestos', array('class'=>'form-control input-sm',)) ?>
          </div>
          
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Contabilidad:</label>
          <div class="col-sm-4">
            <?php echo checkbox_tag('contabilidad', array('class'=>'form-control input-sm',)) ?>
          </div>
      </div>
      
      <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Tesoreria:</label>
          <div class="col-sm-4">
            <?php echo checkbox_tag('tesoreria', array('class'=>'form-control input-sm',)) ?>
          </div>
          
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Compras:</label>
          <div class="col-sm-4">
            <?php echo checkbox_tag('compras', array('class'=>'form-control input-sm',)) ?>
          </div>
      </div>

      <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success">Enviar Alertas</button>
        </div>
      </div>
      <div class="clear"></div>
      </form>
      </div>
    </div>
  </div>
</div>