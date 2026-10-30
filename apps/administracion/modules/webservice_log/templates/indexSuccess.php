<?php
$base_path = sfConfig::get('base_simad');
use_helper('Object', 'jQuery');
?>
<div class="panel panel-gradient" data-collapsed="0">
  <div class="panel-heading">
    <div class="panel-title">Consulta de Log de Servicios Web</div>
  </div>
  <div class="panel-body">
    <?php echo form_tag('webservice_log/list', array('id' => 'frmwslog', 'name' => 'frmwslog', 'method' => 'post', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered')) ?>
      <div class="form-group">
        <label class="col-sm-1 control-label">M&eacute;todo</label>
        <div class="col-sm-3"><?php echo input_tag('nombre_metodo', '', array('class' => 'form-control input-sm')) ?></div>
        <label class="col-sm-1 control-label">Usuario</label>
        <div class="col-sm-3"><?php echo input_tag('user_name', '', array('class' => 'form-control input-sm')) ?></div>
        <label class="col-sm-1 control-label">V&aacute;lido</label>
        <div class="col-sm-2">
          <?php echo select_tag('request_valid', options_for_select(array('' => 'Todos', '1' => 'Si', '0' => 'No')), array('class' => 'form-control input-sm')) ?>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-1 control-label">Operaci&oacute;n</label>
        <div class="col-sm-3"><?php echo input_tag('tipo_operacion', '', array('class' => 'form-control input-sm')) ?></div>
        <label class="col-sm-1 control-label">Desde</label>
        <div class="col-sm-2">
          <div class="input-group">
            <?php echo input_tag('fecha_inicial', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd', 'placeholder' => 'AAAA-MM-DD')) ?>
            <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
          </div>
        </div>
        <label class="col-sm-1 control-label">Hasta</label>
        <div class="col-sm-2">
          <div class="input-group">
            <?php echo input_tag('fecha_final', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd', 'placeholder' => 'AAAA-MM-DD')) ?>
            <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
          </div>
        </div>
      </div>
      <div class="form-group">
        <div class="col-sm-offset-1 col-sm-5">
          <?php
          echo jq_submit_to_remote('search', 'Consultar', array(
            'update' => 'table-paginate',
            'url' => 'webservice_log/list',
            'loading' => "javascript:jQuery.LoadingStructData();",
            'complete' => 'javascript:jQuery.CloseLoadingStructData();',
          ), array('class' => 'btn btn-success'));
          ?>
        </div>
      </div>
    </form>
  </div>
</div>

<div id="table-paginate" class="panel panel-primary">
  <?php include_partial('listPartial', array('pager' => $pager, 'parametros' => $parametros)) ?>
</div>
