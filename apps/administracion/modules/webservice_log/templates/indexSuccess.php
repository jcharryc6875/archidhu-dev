<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_javascript($path_theme . 'assets/js/webservice-log.js');
?>
<div class="panel panel-gradient" data-collapsed="0">
  <div class="panel-heading">
    <div class="panel-title">Consulta de Log de Servicios Web</div>
  </div>
  <div class="panel-body">
    <form id="wslog-form" class="form-horizontal form-groups-bordered" role="form"
          data-list-url="<?php echo $base_path; ?>/administracion.php/webservice_log/list"
          data-download-url="<?php echo $base_path; ?>/administracion.php/webservice_log/download">
      <div class="form-group">
        <label class="col-sm-1 control-label">Método</label>
        <div class="col-sm-3"><input type="text" name="nombre_metodo" class="form-control input-sm" /></div>
        <label class="col-sm-1 control-label">Usuario</label>
        <div class="col-sm-3"><input type="text" name="user_name" class="form-control input-sm" /></div>
        <label class="col-sm-1 control-label">Válido</label>
        <div class="col-sm-2">
          <select name="request_valid" class="form-control input-sm">
            <option value="">Todos</option>
            <option value="1">Sí</option>
            <option value="0">No</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-1 control-label">Operación</label>
        <div class="col-sm-3"><input type="text" name="tipo_operacion" class="form-control input-sm" /></div>
        <label class="col-sm-1 control-label">Desde</label>
        <div class="col-sm-2"><input type="text" name="fecha_inicial" class="form-control input-sm datepicker" data-format="yyyy-mm-dd" placeholder="AAAA-MM-DD" /></div>
        <label class="col-sm-1 control-label">Hasta</label>
        <div class="col-sm-2"><input type="text" name="fecha_final" class="form-control input-sm datepicker" data-format="yyyy-mm-dd" placeholder="AAAA-MM-DD" /></div>
      </div>
      <div class="form-group">
        <div class="col-sm-offset-1 col-sm-5">
          <button type="submit" class="btn btn-success">Consultar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="panel panel-primary">
  <div class="panel-body with-table">
    <div id="wslog-estado" class="alert alert-default hidden"></div>
    <div class="table-responsive">
      <table id="wslog-tabla" class="table table-bordered table-hover table-striped table-condensed">
        <thead>
          <tr>
            <th class="text-center">Consecutivo</th>
            <th class="text-center">Fecha</th>
            <th class="text-center">Método</th>
            <th class="text-center">Usuario</th>
            <th class="text-center">Válido</th>
            <th class="text-center">Operación</th>
            <th class="text-center">Mensaje</th>
            <th class="text-center">Descargar</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
    <div class="row">
      <div class="col-sm-12 text-center">
        <button type="button" id="wslog-prev" class="btn btn-default btn-sm">Anterior</button>
        <span id="wslog-pagina"></span>
        <button type="button" id="wslog-next" class="btn btn-default btn-sm">Siguiente</button>
      </div>
    </div>
  </div>
</div>
