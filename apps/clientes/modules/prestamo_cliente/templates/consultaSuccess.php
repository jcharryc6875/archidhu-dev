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
        echo form_tag('prestamo_cliente/list', array('name'=>'consultar','method'=>'GET', 'class' => 'form-horizontal form-groups-bordered validate')); 
        ?>


        <div class="form-group">  
          <!-- Numero Prestamo -->  
          <label for="numPrestamo" class="col-sm-2 control-label">Número Prestamo</label>
          <div class="col-sm-3">
            <?php 
            echo input_tag('numPrestamo' , '', array('class'=>'form-control input-sm'));
            ?>
          </div>

        </div>

        <div class="form-group">  
          <!-- Codigo Cliente -->  
          <label for="CodCliente" class="col-sm-2 control-label">Codigo Cliente</label>
          <div class="col-sm-3">
            <?php 
            echo input_tag('CodCliente', '', array('class'=>'form-control input-sm'));
            ?>
          </div>

          <!-- Estado Prestamo -->
          <label for="cl_estado_prestamo_id" class="col-sm-2 control-label">Estado Prestamo</label>
          <div class="col-sm-3">
            <?php 
            echo object_select_tag($estado , 'getClEstadoPrestamoId', 
              array(
                'related_class' => 'ClEstadoPrestamo', 
                'include_custom'=>'Seleccione...',
                'class'=>'form-control input-sm'
              )
            ); ?>
          </div>

        </div>

        <div class="form-group">  
          <!-- Regional -->  
          <label for="regional_id" class="col-sm-2 control-label">Ubicaci&oacute;n</label>
          <div class="col-sm-3">
            <?php 
            echo object_select_tag($regional, 'getRegionalId', 
              array(
                'peer_method'=>'getOrdenRegional', 
                'related_class' => 'Regional', 
                'include_custom'=>'Seleccione...',
                'class'=>'form-control input-sm'
              )
            );
            ?>
          </div>

          <!-- Dependencia -->
          <label for="dependencia_id" class="col-sm-2 control-label">Unidad Administrativa</label>
          <div class="col-sm-3">
            <?php 
            echo object_select_tag($dependencia, 'getDependenciaId', 
              array(
                'peer_method'=>'getDependenciaAll',
                'related_class' => 'Dependencia', 
                'include_custom' => 'Seleccione...',
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

          <label for="vencidos" class="col-sm-2 control-label">Vencidos</label>
          <div class="col-sm-2">
              <?php
              echo checkbox_tag('vencidos', 1, false, array('class' => ''));
              ?>
          </div>

        </div>

        <div class="form-group">  
          <!-- Subserie Documental -->  
          <label for="subserie_id" class="col-sm-2 control-label">Subserie Documental</label>
          <div class="col-sm-3">
              <?php 
              echo object_select_tag($subserie , 'getSubserieId', 
                array(
                  'related_class' => 'Subserie', 
                  'include_custom' => 'Seleccione...',
                  'class' => 'form-control input-sm'
                )
              );
              ?>
          </div>

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
          <!-- Usuario Responsable -->  
          <label for="responsable" class="col-sm-2 control-label">Usuario Responsable</label>
          <div class="col-sm-3">
            <?php
            echo object_select_tag($usuarios , 'getUsuarioId', 
              array(
                'related_class' => 'Usuario', 
                'include_custom' => 'Seleccione...',
                'id' => 'responsable',
                'class' => 'form-control input-sm'
              )
            );
            ?>
          </div>

          <!-- Prestado Por -->
          <label for="prestadoPor" class="col-sm-2 control-label">Prestado Por</label>
          <div class="col-sm-3">
            <?php 
            echo object_select_tag($usuarios , 'getUsuarioId', 
              array(
                'related_class' => 'Usuario', 
                'include_custom' => 'Seleccione...', 
                'id' => 'prestadoPor',
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
            <a href="<?php print url_for('prestamo_cliente/consulta');?>"><button type="button" class="btn btn-default">Cancelar</button></a>
          </div>
        </div>
        <div class="clear"></div>

      </div>
    </div>
  </div>
</div>