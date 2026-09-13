<?php
use_helper('Object');

?>
<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Consultar Transferencias
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('transferencia/list', array('name'=>'form1','method'=>'GET', 'class' => 'form-horizontal form-groups-bordered'));
        echo input_hidden_tag('localizacionunidaddocumental_id', $sf_params->get('localizacionunidaddocumental_id')); 
        ?>
        <div class="form-group">  
          <!-- Numero Transferencia -->  
          <label for="numTransferencia" class="col-sm-2 control-label">Número Transferencia</label>
          <div class="col-sm-3">
            <?php 
            echo input_tag('numTransferencia' , '', array('class'=>'form-control input-sm')); 
            ?>
          </div>
          <!-- Codigo de Barras -->
          <label for="codigo_barras" class="col-sm-2 control-label">Codigo de Barras</label>
          <div class="col-sm-3">
            <?php echo input_tag('codigo_barras' , '', array('class'=>'form-control input-sm')); ?>
          </div>
        </div>

        <div class="form-group">  
          <!-- Origen Transferencia -->  
          <label for="origen_transferencia" class="col-sm-2 control-label">Origen Transferencia</label>
          <div class="col-sm-3">
            <?php 
            echo object_select_tag($OrigenTransferencia, 'getOrigentransferenciaid', array (
            'related_class' => 'OrigenTransferencia',
            'name'=>'origen_transferencia',
            'id'=>'origen_transferencia',
            'class'=>'form-control input-sm',
            'include_custom'=>'Seleccione Origen Transferencia...',
            ),0);             
            ?>
          </div>

          <!-- Estado Transferencia -->
          <label for="estado_transferencia" class="col-sm-2 control-label">Estado Transferencia</label>
          <div class="col-sm-3">
            <?php 
            echo object_select_tag($EstadoTransferencia, 'getEstadotransferenciaId', array (
            'related_class' => 'EstadoTransferencia',
            'name'=>'estado_transferencia',
            'id'=>'estado_transferencia',
            'class'=>'form-control input-sm',
            'include_custom'=>'Seleccione Un Estado...',
            ),0);             
            ?>
          </div>

        </div>

        <div class="form-group">  
          <!-- Fecha Inicial -->
          <label for="desde" class="col-sm-2 control-label">Fecha Solicitud</label>
          <div class="col-sm-2">
            <div class="input-group">
              <?php
              echo input_tag('desde', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>

          <label for="hasta" class="col-sm-1 control-label">Hasta</label>
          <div class="col-sm-2">
            <div class="input-group">
              <?php
              echo input_tag('hasta', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>

        </div>

        <div class="form-group">  
          
          <!-- Usuario Solicita -->
          <label for="usuario_solicita" class="col-sm-2 control-label">Usuario Solicita</label>
          <div class="col-sm-4">
            <?php 
            echo object_select_tag($Usuarios, 'getUsuarioId', array (
            'peer_method'=>'getAllUser','related_class' => 'Usuario',
            'name'=>'usuario_solicita',
            'id'=>'usuario_solicita',
            'class'=>'form-control input-sm',
            'include_custom'=>'Seleccione Un Usuario...',
            ),0);             
            ?>
          </div>
          
          <!-- Esta Marcado -->
          <label for="marcado" class="col-sm-1 control-label">Esta Marcado</label>
          <div class="col-sm-2">
            <?php 
            $marcado = array('1' => 'Si', '0' => 'No');
            echo select_tag('marcado', options_for_select($marcado, '', array('include_custom' => 'Seleccione...')), array('class'=>'form-control input-sm'));
            ?>
          </div>
          
        </div>

        <div class="form-group">
          <!-- Botonera -->
          <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success">Consultar Transferencia</button>
          </div>
        </div>
        <div class="clear"></div>

        </form>
      </div>
    </div>
  </div>
</div>