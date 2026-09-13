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
          Crear Cliente
        </div>
      </div>

      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('cliente/update', array('name'=>'form1', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        echo object_input_hidden_tag($cliente, 'getClienteId');
        ?>

        <div class="form-group">  
          <!-- Nombre Cliente -->  
          <label for="nombre_cliente" class="col-sm-2 control-label">Nombre Cliente</label>
          <div class="col-sm-4">
            <?php 
            echo object_input_tag($cliente, 'getNombreCliente', array('class'=>'form-control input-sm required'));
            ?>
          </div>

          <!-- Codigo Cliente -->
          <label for="codigo_cliente" class="col-sm-1 control-label">Codigo Cliente</label>
          <div class="col-sm-4">
            <?php 
            echo object_input_tag($cliente, 'getCodigoCliente', array('class'=>'form-control input-sm required'));
            ?>
          </div>

        </div>

        <div class="form-group">

          <!-- Serie Documental -->
          <label for="serie_documental" class="col-sm-2 control-label">Serie</label>
          <div class="col-sm-6">
            <div class="input-group">
              <?php 
              echo object_input_hidden_tag($cliente, 'getSubserieId');
              $descripcion = "";
              if($cliente->getClienteId() != ''){
                $descripcion = $cliente->getSubserie()->getDescripcion();
              }
              echo input_tag('serie_documental', $descripcion, array('readonly'=>'readonly', 'class'=>'form-control input-sm required'));
              ?>
              <div class="input-group-btn">         
                  <button type="button" class="btn btn-primary btn-sm" onclick="javascript:jQuery.OpenModalSIMAD('<?php print $base_path;?>/archivo.php/unidad_documental/subserie?permiso=1', 500, 300); return false;">Seleccionar</button>
                  <button type="button" class="btn btn-default btn-sm" onclick="javascript:jQuery.LimpiarCampoFormulario('serie_documental');jQuery.LimpiarCampoFormulario('subserie_id');"><i class="entypo-cancel-circled"></i></button>
              </div>
              </div>
            </div>
        </div>

        <div class="form-group">  
          <!-- Contenido -->  
          <label for="contenido" class="col-sm-2 control-label">Contenido</label>
          <div class="col-sm-4">
            <?php 
            echo object_input_tag($cliente, 'getContenido', array('class'=>'form-control input-sm'));
            ?>
          </div>

          <!-- Ubicación -->
          <label for="ubicacion" class="col-sm-1 control-label">Ubicación</label>
          <div class="col-sm-4">
            <?php 
            echo object_input_tag($cliente, 'getUbicacion', array('class'=>'form-control input-sm'));
            ?>
          </div>

        </div>

        <div class="form-group">  
          <!-- Frecuencia Consulta -->  
          <label for="frecconsultacliente_id" class="col-sm-2 control-label">Frecuencia Consulta</label>
          <div class="col-sm-4">
            <?php
            echo object_select_tag($cliente, 'getFrecconsultaclienteId', 
              array(
                'related_class' => 'FrecConsultaCliente', 
                'include_custom' => 'Seleccione...', 
                'class' => 'form-control input-sm required',
              )
            );
            ?>
          </div>

          <!-- Unidad Conservación -->
          <label for="unidconservadora_id" class="col-sm-1 control-label">Unidad Conservación</label>
          <div class="col-sm-4">
            <?php 
            echo object_select_tag($cliente, 'getUnidconservadoraId', 
              array(
                'related_class' => 'Unidconservadora', 
                'include_custom' => 'Seleccione...',
                'class' => 'form-control input-sm required',
              )
            );
            ?>
          </div>

        </div>

        <div class="form-group">  
          <!-- Folios -->  
          <label for="folios" class="col-sm-2 control-label">Folios</label>
          <div class="col-sm-1">
            <?php 
            echo object_input_tag($cliente, 'getFolios', array('class'=>'form-control input-sm'));
            ?>
          </div>

          <!-- Volumen -->
          <label for="volumen" class="col-sm-1 control-label">Volúmen</label>
          <div class="col-sm-1">
            <?php 
            echo  object_input_tag($cliente, 'getVolumen', array('class'=>'form-control input-sm'));
            ?>
          </div>

          <!-- Fecha Apertura -->
          <label for="fecha_apertura" class="col-sm-1 control-label">Fecha Apertura</label>
          <div class="col-sm-2">
            <div class="input-group">
              <?php
              $fecha_inicial = '';
              if($cliente->getFechaApertura()){
                $fecha_inicial = date('m/d/Y', strtotime($cliente->getFechaApertura()));  
              }
              echo input_tag('fecha_apertura', $fecha_inicial, array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>

          <!-- Fecha Cierre -->
          <label for="fecha_cierre" class="col-sm-1 control-label">Fecha Final</label>
          <div class="col-sm-2">
            <div class="input-group">
              <?php
              $fecha_cierre = '';
              if($cliente->getFechaCierre()){
                $fecha_cierre = date('m/d/Y', strtotime($cliente->getFechaCierre()));  
              }
              echo input_tag('fecha_cierre', $fecha_cierre, array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>

        </div>

        <div class="form-group">  
          <!-- Soporte Cliente -->  
          <label for="soporte_cliente_id" class="col-sm-2 control-label">Soporte Cliente</label>
          <div class="col-sm-4">
            <?php 
            echo object_select_tag($cliente, 'getSoporteClienteId', 
              array(
                'related_class' => 'SoporteCliente', 
                'include_custom' => 'Seleccione...', 
                'class' => 'form-control input-sm required',
              )
            ); ?>
          </div>

          <!-- Creado por -->
          <label for="codigo_cliente" class="col-sm-1 control-label">Creado Por</label>
          <div class="col-sm-4">
            <?php
            echo input_hidden_tag('id_creador', $id_creador); 
            echo input_tag('creado_por', $creador, array('readonly' => true, 'class'=>'form-control input-sm'));
            ?>
          </div>

        </div>

        <div class="form-group">  
          <!-- Inventariado Por -->  
          <label for="id_inventariador" class="col-sm-2 control-label">Inventariado Por</label>
          <div class="col-sm-4">
            <?php 
            echo input_hidden_tag('udr_inventariador', $inventariador_id);
            echo object_select_tag($usuarios, 'getUsuarioId', 
              array(
                'related_class' => 'Usuario',
                'name'=>'id_inventariador',
                'id'=>'id_inventariador',
                'include_custom'=>'Seleccione...',
                'class' => 'form-control input-sm required',
              ),
            $inventariador_id);
            ?>
          </div>

          <!-- Responsable -->
          <label for="id_responsable" class="col-sm-1 control-label">Responsable</label>
          <div class="col-sm-4">
            <?php 
              echo input_hidden_tag('udr_responsable', $responsable_id);
              echo object_select_tag($usuarios, 'getUsuarioId', 
                array(
                  'related_class' => 'Usuario',
                  'name' => 'id_responsable',
                  'id' => 'id_responsable',
                  'include_custom' => 'Seleccione...',
                  'class' => 'form-control input-sm required',
                ),
              $responsable_id);
              ?>
          </div>

        </div>

        <div class="form-group">
          <!-- Botonera -->
          <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success">Guardar Cliente</button>
            <a href="<?php print url_for('cliente/list');?>"><button type="button" class="btn btn-default">Cancelar</button></a>
          </div>
        </div>
        <div class="clear"></div>

        </form>
      </div>
    </div>
  </div>
</div>