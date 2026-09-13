<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

use_helper('Object');
use_helper('jQuery');
?>

<div class="row">

  <div class="col-md-12">

    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">

      <div class="panel-heading">
        <div class="panel-title">
          Consultar Prestamos Clientes
        </div>
      </div>

      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('solicitud_prestamo_cliente/list', array('name'=>'consultar','method'=>'GET', 'class' => 'form-horizontal form-groups-bordered validate')); 
        ?>


        <div class="form-group">  
          <!-- Numero Solicitud -->  
          <label for="numSolicitud" class="col-sm-2 control-label">Número Solicitud</label>
          <div class="col-sm-3">
            <?php 
            echo input_tag('numSolicitud' , '', array('class'=>'form-control input-sm'));
            ?>
          </div>

        </div>

        <div class="form-group">  
          <!-- Codigo Cliente -->  
          <label for="codCliente" class="col-sm-2 control-label">Codigo Cliente</label>
          <div class="col-sm-3">
            <?php 
            echo input_tag('codCliente', '', array('class'=>'form-control input-sm'));
            ?>
          </div>

          <!-- Estado Prestamo -->
          <label for="cl_solicitud_prestamo_estado_id" class="col-sm-2 control-label">Estado Prestamo</label>
          <div class="col-sm-3">
            <?php 
            echo object_select_tag($estados , 'getClSolicitudPrestamoEstadoId', 
              array(
                'related_class' => 'ClSolicitudPrestamoEstado', 
                'include_custom'=>'Seleccione...',
                'class'=>'form-control input-sm'
              )
            ); ?>
          </div>

        </div>

        <div class="form-group">  
          <!-- Nombre Cliente -->  
          <label for="nombCliente" class="col-sm-2 control-label">Nombre Cliente</label>
          <div class="col-sm-8">
            <?php 
            echo input_tag('nombCliente', '', array('class'=>'form-control input-sm'));
            ?>
          </div>
        </div>

         <div class="form-group">

          <!-- Fecha Prestamo -->
          <label for="fechaInicial" class="col-sm-2 control-label">Fecha Prestamo</label>
          <div class="col-sm-2">
            <div class="input-group">
              <?php
              echo input_tag('fechaInicial', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>

          <label for="fechaFinal" class="col-sm-1 control-label">Hasta</label>
          <div class="col-sm-2">
            <div class="input-group">
              <?php
              echo input_tag('fechaFinal', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>

        </div>

        <div class="form-group">  

          <!-- Nombre Usuario -->
          <label for="usuario_id" class="col-sm-2 control-label">Nombre Usuario</label>
          <div class="col-sm-3">
            <?php 
            echo object_select_tag($usuarios , 'getUsuarioId', 
              array(
                'related_class' => 'Usuario', 
                'include_custom'=>'Seleccione...',
                'class' => 'form-control input-sm'

              )
            );
            ?>
          </div>

        </div>

        <div class="form-group">
          <!-- Botonera -->
          <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success">Consultar Prestamo</button>
            <a href="<?php print url_for('solicitud_prestamo_cliente/consulta');?>"><button type="button" class="btn btn-default">Cancelar</button></a>
          </div>
        </div>
        <div class="clear"></div>

      </div>
    </div>
  </div>
</div>