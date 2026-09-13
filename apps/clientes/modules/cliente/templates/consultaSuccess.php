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
          Consultar Cliente
        </div>
      </div>

      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('cliente/list', array('name'=>'form1','method'=>'GET', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        ?>

        
        <div class="form-group">  
          <!-- Nombre Cliente -->  
          <label for="nombre_cliente" class="col-sm-2 control-label">Nombre Cliente</label>
          <div class="col-sm-4">
            <?php 
            echo object_input_tag($cliente, 'getNombreCliente', array('class'=>'form-control input-sm'));
            ?>
          </div>

          <!-- Codigo Cliente -->
          <label for="codigo_cliente" class="col-sm-1 control-label">Codigo Cliente</label>
          <div class="col-sm-3">
            <?php 
            echo object_input_tag($cliente, 'getCodigoCliente', array('class'=>'form-control input-sm'));
            ?>
          </div>

        </div>

        <!-- Serie Documental -->
        <div class="form-group">
            <label for="serie_documental" class="col-sm-2 control-label">Serie</label>
            <div class="col-sm-5">
              <div class="input-group">
                <?php 
                echo input_hidden_tag('subserie_id', '');
                echo input_tag('serie_documental', '', array('readonly'=>'readonly', 'class'=>'form-control input-sm required'));
                ?>
                <div class="input-group-btn">         
                    <button type="button" class="btn btn-primary btn-sm" onclick="javascript:jQuery.OpenModalSIMAD('<?php print $base_path;?>/archivo.php/unidad_documental/subserie?permiso=<?php print $permiso;?>&form_tag=consulta', 500, 300); return false;">Seleccionar</button>
                    <button type="button" class="btn btn-default btn-sm" onclick="javascript:jQuery.LimpiarCampoFormulario('serie_documental');jQuery.LimpiarCampoFormulario('subserie_id');"><i class="entypo-cancel-circled"></i></button>
                  </div>
                </div>
            </div>
        </div>

        <!-- Contenido Cliente -->
        <div class="form-group">
            <label for="contenido" class="col-sm-2 control-label">Contenido</label>
            <div class="col-sm-5">
              <?php echo object_input_tag($cliente, 'getContenido', array('class' => 'form-control input-sm')); ?>
            </div>
        </div>

        <div class="form-group">  
          <!-- Soporte Cliente -->  
          <label for="soporte_cliente_id" class="col-sm-2 control-label">Soporte</label>
          <div class="col-sm-4">
            <?php 
            echo object_select_tag($cliente, 'getSoporteClienteId', 
              array(
                'related_class' => 'SoporteCliente',
                'include_custom'=> 'Seleccione Un Soporte...',
                'class' => 'form-control input-sm'
              )
            );
            ?>
          </div>

          <!-- Estado -->
          <label for="cliente_estado_id" class="col-sm-1 control-label">Estado</label>
          <div class="col-sm-4">
            <?php 
            echo object_select_tag($cliente, 'getClienteEstadoId', 
              array(
                'related_class' => 'ClienteEstado',
                'include_custom' => 'Seleccione un Estado...',
                'class' => 'form-control input-sm'
              )
            );
            ?>
          </div>

        </div>

        <div class="form-group">  
          <!-- Frecuencia de Consulta -->  
          <label for="frecconsultacliente_id" class="col-sm-2 control-label">Frecuencia Consulta</label>
          <div class="col-sm-4">
            <?php
            echo object_select_tag($cliente, 'getFrecconsultaclienteId', 
              array(
                'related_class' => 'FrecConsultaCliente',
                'include_custom' => 'Seleccione Frecuencia de Consulta...',
                'class' => 'form-control input-sm'
              )
            );
            ?>
          </div>

          <!-- Unidad Conservadora -->
          <label for="unidconservadora_id" class="col-sm-1 control-label">Unidad Conservadora</label>
          <div class="col-sm-4">
            <?php
            echo object_select_tag($cliente, 'getUnidconservadoraId', 
              array(
                'related_class' => 'Unidconservadora',
                'include_custom' => 'Seleccione Unidad Conservadora...',
                'class' => 'form-control input-sm'
              )
            );
            ?>
          </div>
        </div>

        <div class="form-group">

          <!-- Fecha Apertura -->
          <label for="fecha_apertura" class="col-sm-2 control-label">Fecha Apertura</label>
          <div class="col-sm-2">
            <div class="input-group">
              <?php
              echo input_tag('fecha_apertura', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>

          <label for="fecha_apertura_final" class="col-sm-1 control-label">Hasta</label>
          <div class="col-sm-2">
            <div class="input-group">
              <?php
              echo input_tag('fecha_apertura_final', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>

        </div>

        <div class="form-group">
          <!-- Fecha Cierre -->
          <label for="fecha_cierre" class="col-sm-2 control-label">Fecha Cierre</label>
          <div class="col-sm-2">
            <div class="input-group">
              <?php
              echo input_tag('fecha_cierre', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>

          <label for="fecha_cierre_final" class="col-sm-1 control-label">Hasta</label>
          <div class="col-sm-2">
            <div class="input-group">
              <?php
              echo input_tag('fecha_cierre_final', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>
        </div>

         <div class="form-group">
          <!-- Fecha Vencimiento -->
          <label for="fechavencimiento" class="col-sm-2 control-label">Fecha Vencimiento</label>
          <div class="col-sm-2">
            <div class="input-group">
              <?php
              echo input_tag('fechavencimiento', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>

          <label for="fecha_vencimiento_final" class="col-sm-1 control-label">Hasta</label>
          <div class="col-sm-2">
            <div class="input-group">
              <?php
              echo input_tag('fecha_vencimiento_final', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>
        </div>

         <div class="form-group">
          <!-- Fecha Creación -->
          <label for="fecha_creacion_inicial" class="col-sm-2 control-label">Fecha Creación</label>
          <div class="col-sm-2">
            <div class="input-group">
              <?php
              echo input_tag('fecha_creacion_inicial', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>

          <label for="fecha_creacion_final" class="col-sm-1 control-label">Hasta</label>
          <div class="col-sm-2">
            <div class="input-group">
              <?php
              echo input_tag('fecha_creacion_final', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>
        </div>

        <div class="form-group">  
          <!-- Creado Por -->  
          <label for="id_creador" class="col-sm-2 control-label">Creado Por</label>
          <div class="col-sm-4">
            <?php
            echo input_hidden_tag('udr_creador');
            echo object_select_tag($userCliente, 'getUsuarioId', 
              array(
                'related_class' => 'Usuario',
                'name' => 'id_creador',
                'id' => 'id_creador',
                'include_custom' => 'Seleccione un Usuario...',
                'class' => 'form-control input-sm'
              )
            );
            ?>
          </div>

          <!-- Inventariado Por -->
          <label for="id_inventariador" class="col-sm-1 control-label">Inventariado Por</label>
          <div class="col-sm-4">
            <?php 
            echo input_hidden_tag('udr_inventariador');
            echo object_select_tag($userCliente, 'getUsuarioId', 
              array(
                'related_class' => 'Usuario',
                'name' => 'id_inventariador',
                'id' => 'id_inventariador',
                'include_custom' => 'Seleccione un Usuario...',
                'class' => 'form-control input-sm'
              )
            ); ?>
          </div>
        </div>

        <div class="form-group">  
          <!-- Responsable -->  
          <label for="id_responsable" class="col-sm-2 control-label">Responsable</label>
          <div class="col-sm-4">
            <?php 
            echo input_hidden_tag('form_origen', 'consultar');
            echo input_hidden_tag('udr_responsable');
            echo object_select_tag($userCliente, 'getUsuarioId', 
              array(
                'related_class' => 'Usuario',
                'name' => 'id_responsable',
                'id' => 'id_responsable',
                'include_custom' => 'Seleccione un Usuario...',
                'class' => 'form-control input-sm'
              )
            );
            ?>
          </div>
        </div>

        <div class="form-group">
          <!-- Botonera -->
          <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success">Consultar Cliente</button>
            <a href="<?php print url_for('cliente/list');?>"><button type="button" class="btn btn-default">Cancelar</button></a>
          </div>
        </div>
        <div class="clear"></div>


        </form>
      </div>

    </div>
  </div>
</div>