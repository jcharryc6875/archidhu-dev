<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$currentForm="com_enviada/consulta";
$currentUser= $sf_user->getAttribute('username', '', 'subscriber') ;
$currentUserId= $sf_user->getAttribute('usuario_id','', 'subscriber');

use_helper('Object','jQuery','UserComponent');
?>
<!-- Contenedor Pagina -->
<div class="panel panel-gradient" data-collapsed="0">
  <!-- Navbar -->
  <?php include_once("_navbar_actoadm.php"); ?>
  <div class="panel-heading">
    <div class="panel-title">
      Consultar Actos Administrativos
    </div>
  </div>
  <!-- Contenedor Contenido Formulario-->
  <div class="panel-body">      
    <?php echo form_tag('acto_administrativo/list',array('name'=>'form1','method'=>'GET', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate')) ?>
        <div class="form-group">
            <!-- Radicado -->
            <label for="lbrazonsocial" class="col-sm-1 control-label">N&uacute;mero Acto Administrativo:</label>
            <div class="col-sm-3">
                <?php 
                    echo input_tag('consecutivo_documento', '', array('class'=>'form-control input-sm','placeholder'=>'Buscar por el n&uacute;mero acto administrativo o parte del mismo'));
                ?>
            </div>
            <!-- Tipo Plantilla -->
            <label for="lbgetPlantillasCom" class="col-sm-1 control-label">Tipo Plantilla:</label>
            <div class="col-sm-3">
                <?php
                  echo object_select_tag($acto_administrativo, 'getPlantillascomId', array('peer_method'=>'getPlantillasComListActos','related_class' => 'PlantillasCom','include_custom'=>'Selecione...','class'=>'form-control input-sm'));
                ?>
            </div>
            <!-- Prioridad -->
            <label for="lbgetPlantillasCom" class="col-sm-1 control-label">Prioridad:</label>
            <div class="col-sm-3">
                <?php
                    echo object_select_tag($acto_administrativo, 'getPrioridadcomId', array('related_class' => 'PrioridadCom','include_custom'=>'Selecione...','class'=>'form-control input-sm'));
                ?>
            </div>
        </div>
        
        <div class="form-group">
          <!-- Asunto -->
          <label for="lbgetAsunto" class="col-sm-1 control-label">Asunto:</label>
          <div class="col-sm-3">
            <?php 
                echo object_input_tag($acto_administrativo, 'getAsunto', array('class'=>'form-control input-sm','placeholder'=>'Buscar por el asunto'));
            ?>
          </div>
          <!-- Asunto -->
          <label for="lbgetAsunto" class="col-sm-1 control-label">N&uacute;mero Resoluci&oacute;n:</label>
          <div class="col-sm-3">
            <?php 
                echo object_input_tag($acto_administrativo, 'getNumeroResolucion', array('class'=>'form-control input-sm','placeholder'=>'Buscar por numero de resolucion del acto administrativo'));
            ?>
          </div>
          <!-- Contenido -->
          <label for="lbgetAsunto" class="col-sm-1 control-label">Contenido:</label>
          <div class="col-sm-3">
            <?php 
                echo object_input_tag($acto_administrativo, 'getContenido', array('class'=>'form-control input-sm','placeholder'=>'Buscar por el contenido'));
            ?>
          </div>
        </div>
        
        <div class="form-group">          
          <!-- Estado -->
          <label for="lbEstado" class="col-sm-1 control-label">Estado:</label>
          <div class="col-sm-3">
            <?php
                echo object_select_tag($acto_administrativo, 'getEstadoactoadministrativoId', array ('related_class' => 'EstadoActoAdministrativo','include_custom'=>'Seleccione...','class'=>'form-control input-sm'));                
            ?>
          </div>
          
          <!-- Estado Diglitalizacion -->
          <label for="lbEstadoDigitalizacion" class="col-sm-1 control-label">Estado Diglitalizaci&oacute;n:</label>
          <div class="col-sm-3">
            <?php                
                echo object_select_tag($acto_administrativo, 'getEstadodigitalizacionId', array ('related_class' => 'EstadoDigitalizacion','include_custom'=>'Seleccione...','class'=>'form-control input-sm',));
            ?>
          </div> 
          
          <!-- Regional -->
          <label for="lbgetRegionalId" class="col-sm-1 control-label">Regional:</label>
          <div class="col-sm-3">
            <?php
                echo select_tag('regional_id',objects_for_select($regionales,'getRegionalId','getRegionalAll','',array('include_custom'=>'Seleccione...',)) , array ('name'=>'regional_id','id'=>'regional_id','class'=>'form-control input-sm'));
            ?>
          </div>
        </div>
        
        <div class="form-group">    
            <!-- primer nombre interesado -->
            <label for="lbinteresado" class="col-sm-1 control-label">Interesado Primer Nombre:</label>
            <div class="col-sm-3">
              <?php 
                  echo input_tag('pnombre_interesado', '', array('class'=>'form-control input-sm','placeholder'=>'Buscar primer nombre del interesado'));
              ?>
            </div>
            <!-- segundo nombre interesado -->
            <label for="lbinteresado" class="col-sm-1 control-label">Interesado Segundo Nombre:</label>
            <div class="col-sm-3">
              <?php 
                  echo input_tag('snombre_interesado', '', array('class'=>'form-control input-sm','placeholder'=>'Buscar segundo nombre del interesado'));
              ?>
            </div>
            <!-- nuid interesado -->
            <label for="lbnuidInteresado" class="col-sm-1 control-label">Identificaci&oacute;n Interesado:</label>
            <div class="col-sm-3">
              <?php
                  echo input_tag('nuid_interesado', '', array('class'=>'form-control input-sm','placeholder'=>'Buscar por identificaci&oacute;n del interesado'));
              ?>
            </div>
        </div>
		
        <div class="form-group">    
            <!-- primer apellido interesado -->
            <label for="lbinteresado" class="col-sm-1 control-label">Interesado Primer Apellido:</label>
            <div class="col-sm-3">
              <?php 
                  echo input_tag('papellido_interesado', '', array('class'=>'form-control input-sm','placeholder'=>'Buscar primer apellido del interesado'));
              ?>
            </div>
            <!-- segundo nombre interesado -->
            <label for="lbinteresado" class="col-sm-1 control-label">Interesado Segundo Apellido:</label>
            <div class="col-sm-3">
              <?php 
                  echo input_tag('sapellido_interesado', '', array('class'=>'form-control input-sm','placeholder'=>'Buscar segundo apellido del interesado'));
              ?>
            </div>
            <!-- Periodo -->
            <label for="lbPeriodo" class="col-sm-1 control-label">Periodo:</label>
            <div class="col-sm-3">
              <?php
                  $year = date("Y");
                  $acto_administrativo->setPeriodoId($year);
                  echo object_select_tag($acto_administrativo, 'getPeriodoId', array('related_class' => 'Periodo','include_custom'=>'Seleccione...','class'=>'form-control input-sm'));
              ?>
            </div>
        </div>
		
        <div class="form-group">          
          <!-- Fecha Creacion -->
            <label for="lbFechacreacion" class="col-sm-1 control-label">Fecha Acto Administrativo:</label>
            <div class="col-sm-2">
                <div class="input-group">
                  <?php
                  echo input_tag('fechaCreaInicial', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','placeholder'=>'AAAA-MM-DD'));
                  ?>
                  <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
                </div>
            </div>
            <label for="lbFechacreacionfinal" class="col-sm-1 control-label">Hasta:</label>
            <div class="col-sm-2">
                <div class="input-group">
                  <?php
                    echo input_tag('fechaCreaFinal', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','placeholder'=>'AAAA-MM-DD'));
                  ?>
                  <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <!-- Dependencia -->
            <label for="lbDependencia" class="col-sm-1 control-label">Unidad Administrativa Origen:</label>
            <div class="col-sm-5">
              <?php
                  echo object_select_tag($acto_administrativo, 'getDependenciaId', array ('peer_method'=>'getDependenciaAllJoin', 'related_class' => 'Dependencia','include_custom'=>'Seleccione...','class'=>'form-control input-sm select2'));                
              ?>
            </div>
            <!-- Ordenar Por -->
            <label for="lbOrdenarPor:" class="col-sm-1 control-label">Ordenar Por:</label>
            <div class="col-sm-2">
              <?php
                  $ordenList = array('FECHA_CREACION'=>'Fecha','NUMERO_RESOLUCION'=>'Número Acto Administrativo','ASUNTO'=>'Asunto','DEPENDENCIA'=>'Dependencia');
                  echo select_tag('orden' , options_for_select($ordenList,'',array('include_custom'=>'Seleccione',)),array('class' => 'form-control input-sm'));
              ?>
            </div>
            <!-- Entregado -->
            <label for="lbEntregado" class="col-sm-1 control-label">Entregado:</label>
            <div class="col-sm-2">
              <select name="estaEntregado" id="estaEntregado" class="form-control input-sm">
                <option value="">Seleccione...</option>
                <option value="0">No</option>
                <option value="1">Si</option>
              </select>
            </div>  
        </div>
		
        <?php
          echo input_hidden_tag('cargousuarioId');
          echo input_hidden_tag('usuarioFirma');
          echo component_user_multiple('name_usuarioFirma',"usuarioFirma","cargousuarioId",array('url'=>'usuario_firma/selectUserSearching','caption'=>'Firmado por', 'values'=>null,'values_text'=>null,'values_cuid'=>null,'option'=>0,'maximumSelectionSize'=>1));  
        ?>
        
        <?php
          echo input_hidden_tag('usuarioProyecto');
          echo component_user_multiple('name_usuarioProyecto',"usuarioProyecto","cargousuarioId",array('url'=>'usuario_firma/selectUserSearching','caption'=>'Proyect&oacute;', 'values'=>null,'values_text'=>null,'values_cuid'=>null,'option'=>0,'maximumSelectionSize'=>1));  
        ?>
        
        <?php
          echo input_hidden_tag('usuarioDestino');
          echo component_user_multiple('name_usuarioDestino',"usuarioDestino","cargousuarioId",array('url'=>'usuario_firma/selectUserSearching','caption'=>'Destinatario', 'values'=>null,'values_text'=>null,'values_cuid'=>null,'option'=>0,'maximumSelectionSize'=>1));  
        ?>

        <?php
          echo input_hidden_tag('usuarioCopia');
          echo component_user_multiple('name_usuarioCopia',"usuarioCopia","cargousuarioId",array('url'=>'usuario_firma/selectUserSearching','caption'=>'Copia Para', 'values'=>null,'values_text'=>null,'values_cuid'=>null,'option'=>0,'maximumSelectionSize'=>1));  
        ?>

        <div class="form-group">
            <!-- Botonera -->
            <div class="col-sm-offset-4 col-sm-5">
                <button type="submit" class="btn btn-blue btn-icon">Consultar Acto Administrativo<i class="entypo-search"></i></button>            
                <?php echo button_to('Deshacer','com_enviada/consulta',array('class' => 'btn btn-red ')); ?>

            </div>
        </div>
        <div class="clear"></div>    
    </form>
  </div>
</div>