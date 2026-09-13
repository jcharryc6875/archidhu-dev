<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$currentForm="com_recibida/consulta";
$currentUser= $sf_user->getAttribute('username', '', 'subscriber') ;
$currentUserId = $sf_user->getAttribute('usuario_id','', 'subscriber');

use_helper('Object','jQuery','UserComponent');
?>
<?php
// Include Navbar
include_partial("navbar_recibida");
?>
<!-- Contenedor Pagina -->
<div class="panel panel-gradient" data-collapsed="0">
  <div class="panel-heading">
    <div class="panel-title">
      Consultar Comunicaci&oacute;n Externa Recibida
    </div>
  </div>
  <!-- Contenedor Contenido Formulario-->
  <div class="panel-body">      
    <?php echo form_tag('com_recibida/list',array('name'=>'form1','method'=>'GET', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate')) ?>
        <div class="form-group">
          <!-- Radicado -->
          <label for="lbRadicado" class="col-sm-1 control-label">Radicado:</label>
          <div class="col-sm-3">
            <?php 
                echo input_tag('radicado', '', array('class'=>'form-control input-sm','placeholder'=>'Buscar por el radicado de la comunicaci&oacute;n'));
            ?>
          </div>
		      <!-- Consecutivo Origen -->
          <label for="lbDetalleAsunto" class="col-sm-1 control-label">Num. oficio/ radicado del remitente:</label>
          <div class="col-sm-3">
            <?php 
                echo object_input_tag($com_recibida,'getRadicadoOrigen', array('class'=>'form-control input-sm','placeholder'=>'Buscar por radicado del remitente'));
            ?>
          </div>
          <!-- Detalle Asunto -->
          <label for="lbDetalleAsunto" class="col-sm-1 control-label">Asunto:</label>
          <div class="col-sm-3">
            <?php 
                echo object_input_tag($com_recibida,'getAsunto', array('class'=>'form-control input-sm','placeholder'=>'Buscar por el asunto'));
            ?>
          </div>
        </div>
        
		    <div class="form-group">    
          <!-- Entidad Origen -->
         <label for="lbentidadorigen" class="col-sm-1 control-label">Remitente:</label>
          <div class="col-sm-3">
            <?php 
                echo input_tag('entidad_origen', '', array('class'=>'form-control input-sm','placeholder'=>'Buscar por entidad remitente'));
            ?>
          </div>
          <!-- Funcionario Destino -->
          <label for="lbfuncionariodestino" class="col-sm-1 control-label">Funcionario Remitente:</label>
          <div class="col-sm-3">
            <?php
                echo input_tag('funcionario_origen', '', array('class'=>'form-control input-sm','placeholder'=>'Buscar por nombre funcionario remitente'));
            ?>
          </div>
          <!-- NIT -->
          <label for="lbnit" class="col-sm-1 control-label">Nit Remitente:</label>
          <div class="col-sm-3">
            <?php
                echo input_tag('nit_origen', '', array('class'=>'form-control input-sm','placeholder'=>'Buscar por nit del remitente'));
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
          <!-- numero fud -->
          <label for="lbnumeroFud" class="col-sm-1 control-label">Numero FUD:</label>
          <div class="col-sm-3">
            <?php
                echo input_tag('numero_fud', '', array('class'=>'form-control input-sm','placeholder'=>'Buscar por numero de declaraci&oacute;n'));
            ?>
          </div>          
        </div>
		
        <div class="form-group">    
          <!-- representante_legal -->
         <label for="lbRepresentanteLegal" class="col-sm-1 control-label">Representante Legal:</label>
          <div class="col-sm-3">
            <?php 
                echo input_tag('nombre_representantelegal', '', array('class'=>'form-control input-sm','placeholder'=>'Buscar primer nombre del representante legal'));
            ?>
          </div>
          <!-- identificacion interesado -->
          <label for="lbnuidrepresentante" class="col-sm-1 control-label">Identificaci&oacute;n Representante:</label>
          <div class="col-sm-3">
            <?php
                echo input_tag('nuid_representantelegal', '', array('class'=>'form-control input-sm','placeholder'=>'Buscar por identificaci&oacute;n del representante legal'));
            ?>
          </div>
          <!-- numero Proceso -->
          <label for="lbnumeroProceso" class="col-sm-1 control-label">Numero Proceso:</label>
          <div class="col-sm-3">
            <?php
                echo input_tag('numero_proceso', '', array('class'=>'form-control input-sm','placeholder'=>'Buscar por numero del proceso'));
            ?>
          </div>
        </div>

        <div class="form-group">
         <!-- Tipo Asunto -->
         <label for="lbTipoAsunto" class="col-sm-1 control-label">Forma Recepci&oacute;n:</label>
         <div class="col-sm-3">
            <?php
                echo object_select_tag($com_recibida, 'getFormarecepcionId', array ('peer_method'=>'getFormaRecepcionOrderAll','related_class' => 'FormaRecepcion','include_custom'=>'Seleccione...','class'=>'form-control input-sm'));                
            ?>
         </div>
         <!-- Regional -->
          <label for="lbgetRegionalId" class="col-sm-1 control-label">Puntos de radicaci&oacute;n:</label>
          <div class="col-sm-3">
            <?php
                echo  object_select_tag($com_recibida, 'getRegionalId', array ('peer_method'=>'getOrdenRegional','related_class' => 'Regional', 'include_custom'=>'Seleccione...','class'=>'form-control input-sm'));
            ?>
          </div>
          
          <!-- Dependencia -->
          <label for="lbDependencia" class="col-sm-1 control-label">Unidad Administrativa:</label>
          <div class="col-sm-3">
            <?php
                echo object_select_tag($com_recibida, 'getDependenciaId', array ('peer_method'=>'getDependenciaAllJoin', 'related_class' => 'Dependencia','include_custom'=>'Seleccione...','class'=>'form-control input-sm select2'));                
            ?>
          </div>    
        </div>
        
        <div class="form-group">
          <!-- Estado -->
          <label for="lbEstado" class="col-sm-1 control-label">Estado:</label>
          <div class="col-sm-3">
            <?php 
                echo  object_select_tag($com_recibida, 'getEstadoComRecibidaId', array ('related_class' => 'EstadoComRecibida', 'include_custom'=>'Seleccione...','class'=>'form-control input-sm'));
            ?>
          </div>
    
         <!-- Estado Diglitalización -->
          <label for="lbEstadoDiglitalizacion" class="col-sm-1 control-label">Estado Diglitalizaci&oacute;n:</label>
          <div class="col-sm-3">
            <?php
                echo  object_select_tag($com_recibida, 'getEstadodigitalizacionId', array ('related_class' => 'EstadoDigitalizacion', 'include_custom'=>'Seleccione...','class'=>'form-control input-sm'));
            ?>
          </div>
          
          <!-- Tramite -->
          <label for="lbDependencia" class="col-sm-1 control-label">Tr&aacute;mite:</label>
          <div class="col-sm-3">
            <?php
                echo  object_select_tag($com_recibida, 'getTipoComRecibidaId', array ('peer_method'=>'getTipoComRecibida','related_class' => 'TipoComRecibida', 'include_custom'=>'Seleccione...','class'=>'form-control input-sm select2'));                
            ?>
          </div>    
        </div>
        
		<div class="form-group">
		  <!-- Tipo Asunto -->
		  <label for="lbTipoProceso" class="col-sm-1 control-label">Tipo Proceso:</label>
          <div class="col-sm-3">
            <?php
                echo object_select_tag($com_recibida, 'getTipoprocesocomId', array ('peer_method'=>'getOrdenTipoProcesoCom','related_class' => 'TipoProcesoCom','include_custom'=>'Seleccione...','class'=>'form-control input-sm'));                
            ?>
          </div>
          <!-- Observaciones -->
          <label for="lbObservaciones" class="col-sm-1 control-label">Observaciones:</label>
          <div class="col-sm-3">
            <?php 
                echo input_tag('observaciones','', array('class'=>'form-control input-sm'));
            ?>
          </div>
        </div>
		
        <div class="form-group">          
          <!-- Fecha Creacion -->
            <label for="lbFechacreacion" class="col-sm-1 control-label">Fecha Radicaci&oacute;n:</label>
            <div class="col-sm-2">
                <div class="input-group">
                  <?php                  
                    echo input_tag('fechaInicial', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','placeholder'=>'AAAA-MM-DD'));
                  ?>
                  <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
                </div>
            </div>
            <label for="lbFechacreacionfinal" class="col-sm-1 control-label">Hasta:</label>
            <div class="col-sm-2">
                <div class="input-group">
                  <?php
                    echo input_tag('fechaFinal', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','placeholder'=>'AAAA-MM-DD'));
                  ?>
                  <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
                </div>
            </div>
            
            <!-- Fecha Cierre -->
            <label for="lbFechacierreDesde" class="col-sm-1 control-label">Fecha Cierre:</label>
            <div class="col-sm-2">
                <div class="input-group">
                  <?php
                  echo input_tag('fechaCierre', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','placeholder'=>'AAAA-MM-DD'));
                  ?>
                  <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
                </div>
            </div>
            <label for="lbfechaCierreHasta" class="col-sm-1 control-label">Hasta:</label>
            <div class="col-sm-2">
                <div class="input-group">
                  <?php
                    echo input_tag('fechaCierreFin', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','placeholder'=>'AAAA-MM-DD'));
                  ?>
                  <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
                </div>
            </div>            
        </div>
        
        <div class="form-group">
          <!-- Periodo -->
          <label for="lbPeriodo" class="col-sm-1 control-label">Periodo:</label>
          <div class="col-sm-2">
            <?php
                $year = date("Y");
                $com_recibida->setPeriodoId($year);
                echo object_select_tag($com_recibida, 'getPeriodoId', array('related_class' => 'Periodo','include_custom'=>'Seleccione...','class'=>'form-control input-sm'));
            ?>
          </div>
          
          <!-- Marca -->
          <label for="lbMarca" class="col-sm-1 control-label">Marca:</label>
          <div class="col-sm-2">
            <select name="marcada" id="marcada" class="form-control input-sm">
              <option value="">Seleccione...</option>
              <option value="1">Si</option>
              <option value="0">No</option>              
            </select>
          </div> 
          
          <!-- Ordenar Por -->
          <label for="lbOrdenarPor:" class="col-sm-1 control-label">Ordenar Por:</label>
          <div class="col-sm-2">
            <?php
                $ordenList = array('1'=>'Fecha','2'=>'Radicado','3'=>'Entidad Origen','4'=>'Asunto');
                echo select_tag('ordenar' , options_for_select($ordenList,'',array('include_custom'=>'Seleccione...',)),array('class' => 'form-control input-sm'));
            ?>
          </div>
    
          <!-- Entregado -->
          <label for="lbEntregado" class="col-sm-1 control-label">Entregado:</label>
          <div class="col-sm-2">
            <select name="entregado" id="entregado" class="form-control input-sm">
              <option value="">Seleccione...</option>
              <option value="0">No</option>
              <option value="1">Si</option>
            </select>
          </div>
        </div>        
        
        <?php
            echo input_hidden_tag('cargousuarioId');
            echo input_hidden_tag('usuario_destino');
            echo component_user_multiple('name_usuario_destino',"usuario_destino","cargousuarioId",array('url'=>'usuario_firma/selectUserSearching','caption'=>'Funcionario Destino', 'values'=>null,'values_text'=>null,'values_cuid'=>null,'option'=>0,'maximumSelectionSize'=>1));  
        ?>
        
        <?php
            echo input_hidden_tag('radicador');
            echo component_user_multiple('name_radicador',"radicador","cargousuarioId",array('url'=>'usuario_firma/selectUserSearching','caption'=>'Funcionario Radicador', 'values'=>null,'values_text'=>null,'values_cuid'=>null,'option'=>0,'maximumSelectionSize'=>1));  
        ?>
        
        <?php
            echo input_hidden_tag('usuario_copia');
            echo component_user_multiple('name_usuario_copia',"usuario_copia","cargousuarioId",array('url'=>'usuario_firma/selectUserSearching','caption'=>'Copia para', 'values'=>null,'values_text'=>null,'values_cuid'=>null,'option'=>0,'maximumSelectionSize'=>1));  
        ?>
		
        <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-blue btn-icon">Consultar Recibida<i class="entypo-search"></i></button>                        
            <?php echo button_to('Deshacer','com_recibida/consulta',array('class' => 'btn btn-red ')); ?>

        </div>
    </div>
    <div class="clear"></div>    
    </form>
  </div>
</div>