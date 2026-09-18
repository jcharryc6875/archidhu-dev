<?php
use_helper('Object', 'jQuery');
$base_path = sfConfig::get('base_simad');
?>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          <?php echo $actoadmin_etapa->getPrimaryKey() ? "Editar" : "Crear" ?> Etapa del Flujo de Aprobación
        </div>
      </div>
      <?php if ($sf_user->hasFlash('notice')): ?>
        <div class="alert alert-danger"><strong>Opps! </strong><?php echo $sf_user->getFlash('notice') ?></div>
      <?php endif ?>
      <?php if ($sf_user->hasFlash('success')): ?>
        <div class="alert alert-success"><strong>Excelente! </strong><?php echo $sf_user->getFlash('success') ?></div>
      <?php endif ?>
      <div class="panel-body">
        <?php
        echo form_tag('actoadmin_etapa/update', array('name' => 'form1', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        echo object_input_hidden_tag($actoadmin_etapa, 'getActoadminetapaId');
        ?>

        <div class="form-group">
          <label for="lbgetNombre" class="col-sm-2 control-label">Nombre de la etapa(*):</label>
          <div class="col-sm-4">
            <?php echo object_input_tag($actoadmin_etapa, 'getNombre', array('data-validate' => 'maxlength[100]', 'class' => 'form-control input-sm required')); ?>
          </div>
          <label for="lbgetRolusuarioactoadministvoId" class="col-sm-2 control-label">Tipo de participante(*):</label>
          <div class="col-sm-4">
            <?php echo object_select_tag($actoadmin_etapa, 'getRolusuarioactoadministvoId', array('related_class' => 'RolUsuarioActoAdministvo', 'include_custom' => 'Seleccione...', 'class' => 'form-control input-sm required')); ?>
          </div>
        </div>

        <div class="form-group">
          <label for="lbgetTagPrefijo" class="col-sm-2 control-label">Prefijo de etiqueta(*):</label>
          <div class="col-sm-4">
            <?php echo object_input_tag($actoadmin_etapa, 'getTagPrefijo', array('data-validate' => 'maxlength[30]', 'class' => 'form-control input-sm required', 'placeholder' => 'Ej: FIRMANTE, REVISOR, APROBADOR')); ?>
            <span class="help-block">
              En la plantilla del acto administrativo esta etapa se referenciará como <code>{{PREFIJO_1}}</code>, <code>{{PREFIJO_2}}</code>... según la cantidad de participantes asignados.
            </span>
          </div>
          <label class="col-sm-2 control-label">Etapa activa:</label>
          <div class="col-sm-4">
            <div class="checkbox">
              <label>
                <?php echo object_checkbox_tag($actoadmin_etapa, 'getEstaActivo'); ?>
                La etapa se ejecuta en el flujo (desactívela para retirarla sin borrar su histórico)
              </label>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-2 control-label">Permite edición:</label>
          <div class="col-sm-10">
            <div class="checkbox">
              <label>
                <?php echo object_checkbox_tag($actoadmin_etapa, 'getPermiteEdicion'); ?>
                Por defecto, los usuarios asignados a esta etapa pueden editar el contenido del acto administrativo
              </label>
            </div>
            <span class="help-block">
              Valor por defecto para todos los actos que pasen por esta etapa. Si se desactiva, ningún participante de esta etapa podrá editar el contenido ni cargar un nuevo archivo Word (solo ver/descargar), salvo que se habilite explícitamente para un acto puntual desde su pantalla de configuración de flujo.
            </span>
          </div>
        </div>

        <hr />
        <div class="row">
          <div class="col-sm-12 form-group">
            <button type="submit" class="btn btn-primary btn-sm">
              <span class="glyphicon glyphicon-floppy-disk"></span> Guardar
            </button>
            <a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/actoadmin_etapa/index">Cancelar</a>
          </div>
        </div>
        </form>
      </div>
    </div>
  </div>
</div>
