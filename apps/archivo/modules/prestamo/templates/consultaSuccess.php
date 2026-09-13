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
          Consultar Prestamos
        </div>
      </div>

      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('prestamo/list', array('name'=>'consultar','method'=>'GET', 'class' => 'form-horizontal form-groups-bordered validate')); 
        ?>


        <div class="form-group">  
          <!-- Numero Prestamo -->  
          <label for="prestamo_id" class="col-sm-2 control-label">Número Prestamo</label>
          <div class="col-sm-3">
            <?php 
            echo input_tag('prestamo_id' , '', array('class'=>'form-control input-sm'));
            ?>
          </div>

          <!-- Consecutivo Prestamo -->
          <label for="numPrestamo" class="col-sm-2 control-label">Consecutivo Prestamo</label>
          <div class="col-sm-3">
            <?php 
            echo input_tag('numPrestamo' , '', array('class'=>'form-control input-sm'));
            ?>
          </div>

        </div>

        <div class="form-group">  
          <!-- Codigo Barras -->  
          <label for="CodBarras" class="col-sm-2 control-label">Codigo Barras</label>
          <div class="col-sm-3">
            <?php 
            echo input_tag('CodBarras', '', array('class'=>'form-control input-sm'));
            ?>
          </div>

          <!-- Estado Prestamo -->
          <label for="estado_prestamo_id" class="col-sm-2 control-label">Estado Prestamo</label>
          <div class="col-sm-3">
            <?php 
            echo object_select_tag($estado , 'getEstadoPrestamoId', 
              array(
                'related_class' => 'EstadoPrestamo', 
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
            echo object_select_tag($dependencia,'getDependenciaId', 
              array(
                'peer_method'=>'getDependenciaAll',
                'related_class' => 'Dependencia', 
                'include_custom'=>'Seleccione...',
                'class'=>'form-control input-sm'
              )
            ); ?>
          </div>

        </div>

        <div class="form-group">  
          <!-- Titulo -->  
          <label for="titulo" class="col-sm-2 control-label">Titulo</label>
          <div class="col-sm-8">
            <?php 
            echo input_tag('titulo', '', array('class'=>'form-control input-sm'));
            ?>
          </div>
        </div>

        <div class="form-group">  
          <!-- Observaciones -->  
          <label for="observaciones" class="col-sm-2 control-label">Observaciones</label>
          <div class="col-sm-8">
            <?php 
            echo input_tag('observaciones', '', array('class'=>'form-control input-sm'));
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
            <div id="contenedor_usuario_subserie">
              <select name="subserie_id" id="subserie_id" class="form-control input-sm">
                <option value="0">Seleccione Subserie...</option>
                <?php
                /*
                  while($object = $subseries->fetch()){
                    echo "<option value='".$object[0]."'";                   
                    echo ">".$object[1].' - '.$object[2].' - '.$object[3].' - '.$object[4]."</option>";
                  }
                */
                ?>
              </select>
            </div>
          </div>

          <!-- Nombre Usuario -->
          <label for="usuario_id" class="col-sm-2 control-label">Nombre Usuario</label>
          <div class="col-sm-3">
            <div id="contenedor_usuarios">
              <select name="usuario_id" id="usuario_id" class="form-control input-sm">
                <option value="0">Seleccione Usuario...</option>
                <?php
                  foreach($usuarios as $usuario){
                    echo "<option value='".$usuario->getUsuarioId()."'";            
                    echo ">".$usuario->getNombre()." - ".$usuario->getApellido()."</option>";
                  }
                ?>
              </select>
            </div>
          </div>

        </div>

        <div class="form-group">  
          <!-- Usuario Responsable -->  
          <label for="responsable" class="col-sm-2 control-label">Usuario Responsable</label>
          <div class="col-sm-3">
            <div id="contenedor_usuario_responsable">
              <select name="responsable" id="responsable" class="form-control input-sm">
                <option value="0">Seleccione Usuario...</option>
                <?php
                  foreach($usuarios as $usuario){
                    echo "<option value='".$usuario->getUsuarioId()."'";            
                    echo ">".$usuario->getNombre()." - ".$usuario->getApellido()."</option>";
                  }
                ?>
              </select>
            </div>
          </div>

          <!-- Prestado Por -->
          <label for="prestadoPor" class="col-sm-2 control-label">Prestado Por</label>
          <div class="col-sm-3">
            <div id="contenedor_usuario_prestamo">
              <select name="prestadoPor" id="prestadoPor" class="form-control input-sm">
                <option value="0">Seleccione Usuario...</option>
                <?php
                  foreach($usuarios as $usuario){
                    echo "<option value='".$usuario->getUsuarioId()."'";            
                    echo ">".$usuario->getNombre()." - ".$usuario->getApellido()."</option>";
                  }
                ?>
              </select>
            </div>
          </div>

        </div>

        <div class="form-group">
          <!-- Botonera -->
          <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-blue btn-icon">Consultar Prestamo<i class="entypo-search"></i></button>
            <?php echo button_to('Cancelar','prestamo/consulta',array('class' => 'btn btn-red')); ?>
          </div>
        </div>
        <div class="clear"></div>

      </div>
    </div>
  </div>
</div>