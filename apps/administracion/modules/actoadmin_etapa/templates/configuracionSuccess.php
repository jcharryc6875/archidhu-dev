<?php
use_helper('Object', 'jQuery');
$base_path = sfConfig::get('base_simad');
?>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">Configuración del Flujo de Aprobación de Actos Administrativos</div>
      </div>
      <?php if ($sf_user->hasFlash('notice')): ?>
        <div class="alert alert-danger"><strong>Opps! </strong><?php echo $sf_user->getFlash('notice') ?></div>
      <?php endif ?>
      <?php if ($sf_user->hasFlash('success')): ?>
        <div class="alert alert-success"><strong>Excelente! </strong><?php echo $sf_user->getFlash('success') ?></div>
      <?php endif ?>
      <div class="panel-body">
        <?php echo form_tag('actoadmin_etapa/updateConfiguracion', array('name' => 'form1', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate')); ?>

        <div class="form-group">
          <label for="lbdias_retencion_borrador" class="col-sm-3 control-label">Días de retención de versiones preliminares:</label>
          <div class="col-sm-3">
            <?php echo object_input_tag($actoadmin_configuracion, 'getDiasRetencionBorrador', array('class' => 'form-control input-sm required', 'data-validate' => 'min[0]')); ?>
            <span class="help-block">0 = nunca depurar las versiones preliminares (borradores) de los actos ya radicados.</span>
          </div>
        </div>

        <hr />
        <div class="row">
          <div class="col-sm-12 form-group">
            <button type="submit" class="btn btn-primary btn-sm">
              <span class="glyphicon glyphicon-floppy-disk"></span> Guardar
            </button>
            <a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/actoadmin_etapa/index">Volver a Etapas</a>
          </div>
        </div>
        </form>
      </div>
    </div>
  </div>
</div>
