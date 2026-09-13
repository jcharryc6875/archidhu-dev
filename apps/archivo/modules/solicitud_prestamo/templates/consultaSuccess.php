<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

use_helper('Object','jQuery','UserComponent');
?>
<!-- Imported scripts on this page -->
<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Consultar Solicitudes
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
            echo form_tag('solicitud_prestamo/list', array('name'=>'form1', 'method'=>'POST', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        ?>
        <div class="form-group">  
          <!-- Codigo de Barras -->  
          <label for="codBarras" class="col-sm-1 control-label">ID Expediente</label>
          <div class="col-sm-3">
            <?php 
                echo input_tag('codBarras' , null, array('class'=>'form-control input-sm','placeholder'=>'Numero unico del expediete'));
            ?>
          </div>
		  <!-- Titulo -->  
          <label for="codBarras" class="col-sm-1 control-label">Nombre Expediente</label>
          <div class="col-sm-3">
            <?php 
                echo input_tag('titulo_expediente' , null, array('class'=>'form-control input-sm','placeholder'=>'Nombre del expediete'));
            ?>
          </div>
        </div>
        <div class="form-group">  
          <!-- Numero Solicitud -->
          <label for="numSolicitud" class="col-sm-1 control-label">Numero Solicitud</label>
          <div class="col-sm-3">
            <?php 
                echo input_tag('numSolicitud' , null, array('class'=>'form-control input-sm','data-validate'=>'number,maxlength[11]','placeholder'=>'Numero unico del servicio'));
            ?>
          </div>
		  <!-- Numero Solicitud -->
          <label for="solicitud_prestamo_estado_id" class="col-sm-1 control-label">Estado</label>
          <div class="col-sm-3">
            <?php 
                $estados->setSolicitudprestamoestadoId(1);
                echo object_select_tag($estados , 'getSolicitudPrestamoEstadoId', 
                  array(
                    'related_class' => 'SolicitudPrestamoEstado', 
                    'include_custom'=>'Seleccione',
                    'class' => 'form-control input-sm' 
                  )
                );
            ?>
          </div>
        </div>     
		<?php
            echo input_hidden_tag('cargousuarioId');
            echo input_hidden_tag('usuario_id');
            echo component_user_multiple('usuariodata',"usuario_id","cargousuarioId",array('url'=>'usuario_firma/selectUserSearching','caption'=>'Funcionario Destino', 'values'=>null,'values_text'=>null,'values_cuid'=>null,'option'=>0,'maximumSelectionSize'=>1));  
        ?>
        
        <div class="form-group">
          <!-- Fecha Vencimiento -->
          <label for="fechaInicial" class="col-sm-1 control-label">Fecha Solicitud</label>
          <div class="col-sm-2">
            <div class="input-group">
              <?php
                echo input_tag('fechaInicial', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','placeholder'=>'AAAA-MM-DD'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>
          
          <label for="fechaFinal" class="col-sm-1 control-label">Hasta</label>
          <div class="col-sm-2">
            <div class="input-group">
              <?php
                echo input_tag('fechaFinal', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','placeholder'=>'AAAA-MM-DD'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>
        </div>
        <div class="form-group">
          <!-- Botonera -->
          <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-blue btn-icon">Consultar Solicitud<i class="entypo-search"></i></button>
            <?php echo button_to('Cancelar','solicitud_prestamo/consulta',array('class' => 'btn btn-red')); ?>
          </div>
        </div>
        <div class="clear"></div>
        </form>
      </div>
    </div>
  </div>
</div>