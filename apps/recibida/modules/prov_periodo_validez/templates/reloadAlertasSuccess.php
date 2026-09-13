<?php use_helper('Object') ?>
<?php use_helper('jQuery')?>

<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Envio De Alertas
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('com_recibidas/updateDesmarcar', array('name'=>'fin','role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        echo input_hidden_tag('prov_periodo_validez_id',$prov_periodo_validez_id);
        ?>

        <div class="form-group">
          <!-- Tipo Procedimiento -->
          <div class="col-sm-4">
            <?php echo label_for('label1','Las Alertas Fueron Eviadas Exitosamente.')?>
          </div>
        </div>
      <div class="clear"></div>
      </form>
      </div>
    </div>
  </div>
</div>        