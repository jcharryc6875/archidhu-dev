<?php
use_helper('Object', 'jQuery');
$opcionesCuenta = array('' => 'Todas') + $cuentas;
?>
<div class="container col-md-12">
  <div class="col-sm-2" style="padding-right:2px;padding-left:5px">
    <div class="panel panel-warning panel-shadow" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">Filtros</div>
      </div>
      <div class="panel-body">
        <?php echo form_tag('notificacion_envio_log/list', array('id' => 'frmnelog', 'name' => 'frmnelog', 'method' => 'post', 'role' => 'form', 'class' => 'form-groups-bordered')) ?>
        <div class="row">
          <div class="col-sm-12">
            <div class="form-group">
              <label class="control-label">Cuenta:</label>
              <?php echo select_tag('cuenta_id', options_for_select($opcionesCuenta), array('class' => 'form-control input-sm')) ?>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            <div class="form-group">
              <label class="control-label">Resultado:</label>
              <?php echo select_tag('resultado', options_for_select(array('' => 'Todos', 'OK' => 'Enviados', 'ERROR' => 'Con error')), array('class' => 'form-control input-sm')) ?>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            <div class="form-group">
              <label class="control-label">Destinatario:</label>
              <?php echo input_tag('destinatario', '', array('class' => 'form-control input-sm', 'placeholder' => 'Correo destino')) ?>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            <div class="form-group">
              <label class="control-label">Fecha desde:</label>
              <div class="input-group">
                <?php echo input_tag('fecha_inicial', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd', 'placeholder' => 'AAAA-MM-DD')) ?>
                <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            <div class="form-group">
              <label class="control-label">Fecha hasta:</label>
              <div class="input-group">
                <?php echo input_tag('fecha_final', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd', 'placeholder' => 'AAAA-MM-DD')) ?>
                <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            <?php
            echo jq_submit_to_remote('search', 'Consultar', array(
              'update' => 'table-paginate',
              'url' => 'notificacion_envio_log/list',
              'loading' => "javascript:jQuery.LoadingStructData();",
              'complete' => 'javascript:jQuery.CloseLoadingStructData();',
            ), array('class' => 'btn btn-blue tooltip-primary', 'data-toggle' => 'tooltip', 'data-original-title' => 'Consultar los registros'));
            ?>
            <button type="reset" class="btn btn-default">Limpiar</button>
          </div>
        </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-10" style="padding-left:2px">
    <div id="table-paginate" class="panel panel-gradient email-body panel-shadow">
      <?php include_partial('listPartial', array('pager' => $pager, 'parametros' => $parametros, 'cuentas' => $cuentas)) ?>
    </div>
  </div>
</div>
