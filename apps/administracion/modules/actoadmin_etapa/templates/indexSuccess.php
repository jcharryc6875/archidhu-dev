<?php
use_helper('Object', 'jQuery');
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_javascript($path_theme.'assets/js/jquery-ui/js/jquery-ui-1.10.3.custom.js');
?>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-info">
      <div class="panel-heading">
        <div class="panel-title">Etapas del flujo de aprobación de Actos Administrativos</div>
      </div>
      <div class="panel-body with-table">
        <p class="help-block">
          Arrastre las filas por el ícono <span class="glyphicon glyphicon-move"></span> para definir el orden secuencial en que se ejecutan las etapas.
        </p>
        <table class="table table-bordered table-hover table-striped">
          <thead>
            <tr>
              <th style="width: 5%;"></th>
              <th style="width: 10%;">Orden</th>
              <th>Nombre de la etapa</th>
              <th>Tipo de participante</th>
              <th style="width: 10%;">Prefijo etiqueta</th>
              <th class="text-center" style="width: 10%;">Estado</th>
              <th class="text-center" style="width: 10%;">Opciones</th>
            </tr>
          </thead>
          <tbody id="actoadminEtapaSortable">
            <?php foreach ($etapas as $etapa) : ?>
              <tr data-etapa-id="<?php echo $etapa->getPrimaryKey(); ?>">
                <td class="text-center actoadmin-etapa-drag"><span class="glyphicon glyphicon-move"></span></td>
                <td class="text-center actoadmin-etapa-orden"><?php echo $etapa->getOrden(); ?></td>
                <td><?php echo $etapa->getNombre(); ?></td>
                <td><?php echo $etapa->getRolUsuarioActoAdministvo(); ?></td>
                <td><?php echo $etapa->getTagPrefijo(); ?></td>
                <td class="text-center">
                  <?php echo jq_link_to_remote($etapa->getEstaActivo() ? image_tag('simad/ico_check_green.png', array('width' => "22", 'height' => "22")) : image_tag('simad/ico_check_red.png', array('width' => "22", 'height' => "22")), array(
                    'update'  => false,
                    'url'     => 'actoadmin_etapa/toggleActivo?actoadminetapa_id='.$etapa->getPrimaryKey().'&esta_activo='.($etapa->getEstaActivo() ? 'false' : 'true'),
                    'success' => 'window.location.reload();',
                    'failure' => "alert('Ocurrió un error realizando el proceso, por favor intente de nuevo.')",
                  ), array('data-original-title' => $etapa->getEstaActivo() ? 'Etapa activa' : 'Etapa inactiva', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip')); ?>
                </td>
                <td class="text-center">
                  <?php echo link_to(image_tag('simad/ico_editar.png', array('border' => "0", 'width' => "22", 'align' => "middle")), 'actoadmin_etapa/edit?actoadminetapa_id='.$etapa->getPrimaryKey(), array('data-original-title' => 'Editar esta etapa', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip')); ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <hr />
        <div class="row">
          <div class="col-sm-12 form-group">
            <a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/actoadmin_etapa/create">
              <img border="0" src="<?php echo $base_path; ?>/images/simad/ico_crear_nuevo.png" width="25" align="middle" />Crear Nueva Etapa
            </a>
            <a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/actoadmin_etapa/configuracion">
              <span class="glyphicon glyphicon-cog"></span> Configuración de Retención de Versiones
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
