<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$currentFormAceptar   	= "TRANSFERENCIA_ACEPTAR";
$currentFormRechazar  	= "TRANSFERENCIA_RECHAZAR";
$currentUser= $sf_user->getAttribute('usuario_id', '', 'subscriber');
use_helper('jQuery');
use_helper('Object');  
?>

<div class="row">
  <div class="col-md-12">
  <!-- Contenedor Pagina -->
  <div class="panel panel-gradient" data-collapsed="0">

    <div class="panel-heading">
      <div class="panel-title">
        Detalles De Transferencia
      </div>
    </div>

        <!-- Contenedor Contenido Formulario-->
    <div class="panel-body">
      <!-- Informacion Detalle -->
      <div class="col-sm-12 col-md-12">

          <div class="row">
            <div class="col-sm-6">
              <div class="col-sm-4"><p><strong>Estado Transferencia</strong></p></div>
              <div class="col-sm-8"><p><?php echo $transferencia->getEstadotransferencia()->getDescripcion(); ?></p></div>
            </div>
            <div class="col-sm-6">
              <div class="col-sm-5"><p><strong>Fecha Creaci&oacute;n</strong></p></div>
              <div class="col-sm-7"><p><?php echo $transferencia->getFechaCreacion(); ?></p></div>
            </div>
          </div>

          <div class="row">
            <div class="col-sm-6">
              <div class="col-sm-4"><p><strong>Origen Transferencia</strong></p></div>
              <div class="col-sm-8"><p><?php echo $transferencia->getOrigentransferencia()->getDescripcion(); ?></p></div>
            </div>
            <div class="col-sm-6">
              <div class="col-sm-5"><p><strong>Destino Transferencia</strong></p></div>
              <div class="col-sm-7"><p><?php echo $transferencia->getDestinotransferencia()->getDescripcion(); ?></p></div>
            </div>
          </div>

          <?php
          $origen_transferencia = $transferencia->getOrigentransferenciaId();
          if($origen_transferencia == 1 ){
          ?>

          <div class="row">
            <div class="col-sm-6">
              <div class="col-sm-4"><p><strong>Comunicacion Interna:</strong></p></div>
              <div class="col-sm-8">
                <p>
                  <?php 
                  if($transferencia->getCominterna()->getEstadodigitalizacionId() == 2){ 
                  ?>
                    <a target="_new" href="<?php echo $internaDirApacheDig?>/<?php echo $transferencia->getCominterna()->getPeriodoId() ?>/<?php echo $transferencia->getCominterna()->getRadicado() ?><?php echo $parametroFormatoDig?>"><?php echo $transferencia->getCominterna()->getRadicado() ?></a>
                  <?php 
                  }else{ 
                    echo $transferencia->getCominterna()->getRadicado();
                  }
                  ?>
                </p>
              </div>
            </div>
          </div>

          <?php 
          }else if($origen_transferencia == 2){
          ?>
          <div class="row">
            <div class="col-sm-6">
              <div class="col-sm-4"><p><strong>Comunicaci&oacute;n Entrante</strong></p></div>
              <div class="col-sm-8"><p>
                <?php 
                if($transferencia->getComrecibida()->getEstadodigitalizacionId() == 2){
                ?>
                <a target="_new" href="<?php echo $recibidaDirApacheDig?>/<?php echo $transferencia->getComrecibida()->getPeriodoId() ?>/<?php echo $transferencia->getComrecibida()->getRadicado() ?><?php echo $parametroFormatoDig?>"><?php echo $transferencia->getComrecibida()->getRadicado() ?></a></td>
                <?php 
                }else{ 
                  echo $transferencia->getComrecibida()->getRadicado();
                }
                ?>
              </p></div>
            </div>
          </div>

          <?php 
          }else if($origen_transferencia == 3){ 
          ?>
          <div class="row">
            <div class="col-sm-6">
              <div class="col-sm-4"><p><strong>Comunicaci&oacute;n Saliente</strong></p></div>
              <div class="col-sm-8"><p>
                  <?php 
                  if($transferencia->getComenviada()->getEstadodigitalizacionId()==2){ 
                  ?>
                    <a target="_new" href="<?php echo $enviadaDirApacheDig?>/<?php echo $transferencia->getComenviada()->getPeriodoId() ?>/<?php echo $transferencia->getComenviada()->getRadicado() ?><?php echo $parametroFormatoDig?>">
                    <?php echo $transferencia->getComenviada()->getRadicado(); ?></a>
                  <?php 
                  }else{
                    echo $transferencia->getComenviada()->getRadicado();
                  }
                  ?>
              </p></div>
            </div>
          </div>

          <?php }else if($origen_transferencia == 4){ ?>
          <div class="row">
            <div class="col-sm-6">
              <div class="col-sm-4"><p><strong>Registro Vinculado</strong></p></div>
              <div class="col-sm-8"><p>
                  <a target=_new href="<?php echo $transferencia->getVinculada()->getRuta(); ?>">
                    <?php echo image_tag('simad/ico_ver_adj.png', array('border'=>"0",'width'=>"25",'height'=>"25",'align'=>"middle",'data-original-title'=>'Ver archivo adjunto', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip', 'data-placement' => 'top')); ?>
                  </a>
              </p></div>
            </div>
          </div>

          <?php
          }else if($origen_transferencia == 4){
          ?>

          <div class="row">
            <div class="col-sm-6">
              <div class="col-sm-4"><p><strong>Archivo Gesti&oacute;n</strong></p></div>
              <div class="col-sm-8"><p>
                  <?php echo $transferencia->getUnidaddocumental()->getTitulo(); ?>
              </p></div>
            </div>
          </div>

          <?php
          }else if($origen_transferencia == 5){
          ?>

          <div class="row">
            <div class="col-sm-6">
              <div class="col-sm-4"><p><strong>Archivo Gesti&oacute;n</strong></p></div>
              <div class="col-sm-8"><p><?php echo $transferencia->getUnidaddocumental()->getTitulo();?></p></div>
            </div>
            <div class="col-sm-6">
              <div class="col-sm-5"><p><strong>Codigo de Barras</strong></p></div>
              <div class="col-sm-7"><p><?php echo $transferencia->getUnidaddocumental()->getCodigoBarras();?></p></div>
            </div>
          </div>

          <div class="row">
            <div class="col-sm-6">
              <div class="col-sm-4"><p><strong>Fecha Inicial</strong></p></div>
              <div class="col-sm-8"><p><?php echo $transferencia->getUnidaddocumental()->getFechaApertura(); ?></p></div>
            </div>
            <div class="col-sm-6">
              <div class="col-sm-5"><p><strong>Fecha Final</strong></p></div>
              <div class="col-sm-7"><p><?php echo $transferencia->getUnidaddocumental()->getFechaCierre(); ?></p></div>
            </div>
          </div>

          <div class="row">
            <div class="col-sm-6">
              <div class="col-sm-4"><p><strong>Contenido</strong></p></div>
              <div class="col-sm-8"><p><?php echo $transferencia->getUnidaddocumental()->getContenido(); ?></p></div>
            </div>
            <div class="col-sm-6">
              <div class="col-sm-5"><p><strong>Notas</strong></p></div>
              <div class="col-sm-7"><p><?php echo $transferencia->getUnidaddocumental()->getNotas(); ?></p></div>
            </div>
          </div>
          <?php
          }else if($origen_transferencia == 6){
          ?>
          <div class="row">
            <div class="col-sm-6">
              <div class="col-sm-4"><p><strong>Archivo Central</strong></p></div>
              <div class="col-sm-8"><p><?php echo $transferencia->getUnidaddocumental()->getTitulo(); ?></p></div>
            </div>
          </div>
          <?php
          }
          ?>

          <div class="row">
            <div class="col-sm-6">
              <div class="col-sm-4"><p><strong>Solicitado Por</strong></p></div>
              <div class="col-sm-8"><p><?php echo $usuario_solicita != null ? $usuario_solicita->getUsuario() : "n/a"; ?></p></div>
            </div>
          </div>

          <?php
          if($transferencia->getEstadotransferenciaId() == 2){
          ?>

          <div class="row">
            <div class="col-sm-6">
              <div class="col-sm-4"><p><strong>Fecha Aceptaci&oacute;n</strong></p></div>
              <div class="col-sm-8"><p><?php echo $transferencia->getFechaAceptacion();?></p></div>
            </div>
            <div class="col-sm-6">
              <div class="col-sm-5"><p><strong>Usuario Acepta</strong></p></div>
              <div class="col-sm-7"><p><?php echo $usuario_acepta != null ? $usuario_acepta->getUsuario() : "n/a"; ?></p></div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <div class="col-sm-4"><p><strong>Observaciones Aceptaci&oacute;n</strong></p></div>
              <div class="col-sm-8"><p><?php echo $transferencia->getObservaciones(); ?></p></div>
            </div>
          </div>
          <?php
          }
          ?>

          <?php
          if($transferencia->getEstadotransferenciaId() == 3){
          ?>

          <div class="row">
            <div class="col-sm-6">
              <div class="col-sm-4"><p><strong>Fecha de Rechazo</strong></p></div>
              <div class="col-sm-8"><p><?php echo $transferencia->getFechaAceptacion();?></p></div>
            </div>
            <div class="col-sm-6">
              <div class="col-sm-5"><p><strong>Usuario Rechaza</strong></p></div>
              <div class="col-sm-7"><p><?php echo $usuario_acepta->getUsuario(); ?></p></div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <div class="col-sm-4"><p><strong>Observaciones Rechazo</strong></p></div>
              <div class="col-sm-8"><p><?php echo $transferencia->getObservaciones();?></p></div>
            </div>
          </div>
          <?php
          }
          ?>


      </div>
      <hr />

      <?php
      if($transferencia->getEstadotransferenciaId() == 1):
      ?>
      <!-- Opciones Detalle -->
      <div class="col-sm-12 col-md-12">
        <div class="form-group">

          <?php
          // Aceptar Transferencia
          if($sf_user->checkPerm($currentFormAceptar, $currentUser)){
            echo link_to(image_tag('simad/ico_cambiar_localizacion.png', array('border'=>"0",'width'=>"25",'align'=>"middle",'title'=>'Aceptar Transferencia')).'Aceptar Transferencia', 'transferencia/aceptar?transferencia_id='.$transferencia->getTransferenciaId(), array('class' => 'btn btn-white btn-sm'));
          }
          ?>

          <?php
          // Rechazar Transferencia
          if($sf_user->checkPerm($currentFormRechazar, $currentUser)){
            echo link_to(image_tag('simad/ico_anular.png', array('border'=>"0",'width'=>"25",'align'=>"middle",'title'=>'Rechazar Transferencia')).'Rechazar Transferencia', 'transferencia/rechazar?transferencia_id='.$transferencia->getTransferenciaId(), array('class' => 'btn btn-white btn-sm'));
          }
          ?>

        </div>
      </div>
      <?php
      endif;
      ?>

      </div>
    </div>
  </div>
</div>