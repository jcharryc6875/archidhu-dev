      <header class="logo-env">

            <!-- logo -->
            <div class="logo">
                <a href="<?php print $base_path; ?>backend.php/resumen">
                    <img class="img-circle" style="border-radius: unset;" src="<?php print $path_theme;?>assets/images/simad/logo-app-compact.png" width="88px" height="32px"/>
                </a>
            </div>

            <!-- logo collapse icon -->
            <div class="sidebar-collapse">
                <a href="#" class="sidebar-collapse-icon"><!-- add class "with-animation" if you want sidebar to have animation during expanding/collapsing transition -->
                    <i class="entypo-menu"></i>
                </a>
            </div>

                            
            <!-- open/close menu icon (do not remove if you want to enable menu on mobile devices) -->
            <div class="sidebar-mobile-menu visible-xs">
                <a href="#" class="with-animation"><!-- add class "with-animation" to support animation -->
                    <i class="entypo-menu"></i>
                </a>
            </div>

      </header>


      <ul id="main-menu" class="main-menu">
              <!-- add class "multiple-expanded" to allow multiple submenus to open -->
              <!-- class "auto-inherit-active-class" will automatically add "active" class for parent elements who are marked already with class "active" -->
              <li>
                  <a data-toggle="tooltip" data-original-title="Cerrar detalles del registro" href="#" onclick="javascript:parent.jQuery.ReloadAndCloseModalSIMAD();">
                      <i class="fa fa-times-circle"></i>
                      <span class="title">Cerrar</span>
                  </a>
              </li>

              <?php 
                      if(!$dataShared['IsViewValid'])
                      { ?>
                          <?php 
                          if($pkusuario_asignado == $currentUser || $sf_user->checkPerm("COM_RECIBIDA_EDIT_PROCESO", $currentUser))
                          { ?>
                              <?php 
                              if($sf_user->checkPerm("COM_RECIBIDA_VINCULAR", $currentUser) && !$com_recibida->getMarcaVinculacion())
                              { 
                                  ?>
              <li>
                  <a data-toggle="tooltip" data-original-title="Iniciar o clasificar el documento en un expediente del archivo" href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/archivo.php/transferencia/create?origen_transferencia=2&comrecibida_id=<?php echo $com_recibida->getPrimaryKey() ?>');">                            
                      <i class="fa fa-star"></i>
                      <span class="title">Iniciar Expediente</span>
                  </a>
              </li>
                                  <?php 
                              } ?>
                              
                              <?php 
                              if($sf_user->checkPerm("COM_RECIBIDA_ASIGNAR_GESTOR", $currentUser) && $com_recibida->getTipoprocesocomId() == 2  && !in_array($com_recibida->getEstadocomrecibidaId(), array(5,14,12)))
                              { ?>
              <li>
                  <a data-toggle="tooltip" data-original-title="Asignar Gestor comunicaci&oacute;n a otro usuario" href="<?php echo $base_path; ?>/recibida.php/com_recibida/reenviar?comrecibida_id=<?php echo $com_recibida->getPrimaryKey() ?>">
                      <i class="entypo-user-add"></i>
                      <span class="title">Asignar Gestor</span>
                  </a>
              </li>
                                  <?php 
                              } ?>
                              
                              <?php 
                              if($sf_user->checkPerm("COM_RECIBIDA_ASIGNAR_GESTOR", $currentUser) && ($com_recibida->getTipoprocesocomId() == 3) && (!$com_recibida->getMarcaVinculacion()) && !in_array($com_recibida->getEstadocomrecibidaId(), array(5,14,12)))
                              { ?>

              <li>
                  <a data-toggle="tooltip" data-original-title="Asignar Gestor comunicaci&oacute;n a otro usuario" href="<?php echo $base_path; ?>/recibida.php/com_recibida/reenviar?comrecibida_id=<?php echo $com_recibida->getPrimaryKey() ?>">
                      <i class="fa fa-male"></i>
                      <span class="title">Reasignar Gestor</span>
                  </a>
              </li>
                                  <?php 
                              } ?>

                              <?php 
                              if($sf_user->checkPerm("COM_RECIBIDA_DEVOLVER_DISTRIBUIDOR", $currentUser) && ($com_recibida->getTipoprocesocomId() == 3) /*&& (!$com_recibida->getMarcaVinculacion())*/ && !in_array($com_recibida->getEstadocomrecibidaId(), array(5,14,12)))
                              { ?>

              <li>
                  <a data-toggle="tooltip" data-original-title="Devolver documento a distribuidor" href="<?php echo $base_path; ?>/recibida.php/com_recibida/devolver?comrecibida_id=<?php echo $com_recibida->getPrimaryKey(); ?>">
                      <i class="fa fa-rotate-left"></i>
                      <span class="title">Devolver Documento</span>
                  </a>
              </li> 
                                  <?php 
                              } ?>

                              <?php 
                              if($sf_user->checkPerm("COM_RECIBIDA_DEVOLVER_RADICADO", $currentUser) && ($com_recibida->getTipoprocesocomId() == 2)  && !in_array($com_recibida->getEstadocomrecibidaId(), array(5,14,12)))
                              { ?>
              <li>
                  <a data-toggle="tooltip" data-original-title="Devolver documento a control de calidad" href="<?php echo $base_path; ?>/recibida.php/com_recibida/devolver?comrecibida_id=<?php echo $com_recibida->getPrimaryKey(); ?>">
                      <i class="fa fa-rotate-left"></i>
                      <span class="title">Devolver Documento</span>
                  </a>
              </li> 
                                  <?php 
                              } ?>
                              
                              <?php 
                              if($sf_user->checkPerm("COM_RECIBIDA_ASIGNAR_DISTRIBUIDOR", $currentUser) && in_array($com_recibida->getTipoprocesocomId(), array(2,6)) && !in_array($com_recibida->getEstadocomrecibidaId(), array(5,14,12)))
                              { ?>

              <li>
                  <a data-toggle="tooltip" data-original-title="Asignar a otra area" href="<?php echo $base_path; ?>/recibida.php/com_recibida/asignarArea?comrecibida_id=<?php echo $com_recibida->getPrimaryKey() ?>">
                      <i class="fa fa-sign-in"></i>
                      <span class="title">Reasignar Area</span>
                  </a>
              </li> 
                                  <?php 
                              } ?>
                              
                              <?php 
                              if($sf_user->checkPerm("COM_RECIBIDA_CREAR_SERVICIO", $currentUser))
                              { ?>
              <li>
                  <a data-toggle="tooltip" data-original-title="Generar solicitud de servicio para esta comunicaci&oacute;n" href="#" onclick="javascript:jQuery.CloseModalAndHrefParent('<?php echo $base_path; ?>/servicios.php/servicio/createCom/com_id/<?php echo $com_recibida->getPrimaryKey(); ?>/modulo_id/3');">
                      <i class="entypo-archive"></i>
                      <span class="title">Generar Solicitud</span>
                  </a>
              </li>
                                  <?php 
                              } ?>

                              <?php 
                              if($sf_user->checkPerm("COM_RECIBIDA_RESPONDER", $currentUser)  && $com_recibida->getMarcaVinculacion() && !in_array($com_recibida->getEstadocomrecibidaId(), array(5,14,12)))
                              { ?>   
              <li>
                  <a data-toggle="tooltip" data-original-title="Responder esta comunicaci&oacute;n" href="#" onclick="javascript:jQuery.CloseModalAndHrefParent('<?php echo $base_path; ?>/enviada.php/com_enviada/create?comrecibida_id=<?php echo $com_recibida->getPrimaryKey().$radicadoOrigen.$radicadoComunicacion ?>');">
                      <i class="fa fa-reply-all"></i>
                      <span class="title">Iniciar Respuesta</span>
                  </a>
              </li>
                                  <?php 
                              } ?>
                              
                              <?php 
                              if($sf_user->checkPerm("COM_RECIBIDA_RESPONDER_EXTERNO", $currentUser) && $com_recibida->getMarcaVinculacion() && empty($com_recibida->getResptaIntegracion()) && !in_array($com_recibida->getEstadocomrecibidaId(), array(5,14,12)))
                              { ?>

              <li id="<?php echo md5($com_recibida->getPrimaryKey()."COM_RECIBIDA_RESPONDER_EXTERNO") ?>">
                  
                      <?php echo jq_link_to_remote('<i class="fa fa-external-link"></i> <span class="title">Respuesta Externa</span>',   array(
                          'update'    => null,
                          'url'     => 'com_recibida/enviarComToWsLex',
                          'with'    => " 'comrecibida_id=".$com_recibida->getPrimaryKey()."'",
                          'loading' => "javascript:jQuery.LoadingStructData();",
                          'complete' => 'try{ var response_value = JSON.parse(XMLHttpRequest.responseText); javascript:jQuery.CloseLoadingStructData(); if(response_value.status == "200"){ jQuery("#'.md5($com_recibida->getPrimaryKey()."COM_RECIBIDA_RESPONDER_EXTERNO").'").remove(); toastr.success(response_value.message) }else{  toastr.error(response_value.message); } }catch(err) { javascript:jQuery.CloseLoadingStructData(); toastr.error(err.message); }',
                          ),array('data-toggle'=>'tooltip', 'data-original-title'=>'Iniciar respuesta de esta comunicaci&oacute;n en una herramienta de gesti&oacute;n de la unidad'));
                      ?>
                      
              </li>
                                  <?php 
                              } ?>
                              
                              <?php 
                              if(($sf_user->checkPerm("COM_RECIBIDA_RESPONDER_EXTERNO2", $currentUser) || $currentUser == 1) && $com_recibida->getMarcaVinculacion() && !in_array($com_recibida->getEstadocomrecibidaId(), array(5,14,12)))
                              { ?>
              <li id="<?php echo md5($com_recibida->getPrimaryKey()."COM_RECIBIDA_RESPONDER_EXTERNO") ?>">
              
                      <?php echo jq_link_to_remote('<i class="fa fa-external-link-square"></i> <span class="title">Forzar Respuesta Externa</span>', array(
                          'update'    => null,
                          'url'     => 'com_recibida/enviarComToWsLex2',
                          'with'    => " 'comrecibida_id=".$com_recibida->getPrimaryKey()."'",
                          'loading' => "javascript:jQuery.LoadingStructData();",
                          'complete' => 'try{ var response_value = JSON.parse(XMLHttpRequest.responseText); javascript:jQuery.CloseLoadingStructData(); if(response_value.status == "200"){ jQuery("#'.md5($com_recibida->getPrimaryKey()."COM_RECIBIDA_RESPONDER_EXTERNO").'").remove(); toastr.success(response_value.message) }else{  toastr.error(response_value.message); } }catch(err) { javascript:jQuery.CloseLoadingStructData(); toastr.error(err.message); }',
                          ),array('data-toggle'=>'tooltip', 'data-original-title'=>'Iniciar respuesta de esta comunicaci&oacute;n en una herramienta de gesti&oacute;n de la unidad, solo envia datos sin la digitalización'));
                      ?>
              </li>               
                                  <?php 
                              } ?>
                              <?php 
                          } ?>


              <li>
                  <a data-toggle="tooltip" data-original-title="Ir al historial de esta comunicaci&oacute;n" href="<?php echo $base_path; ?>recibida.php/com_recibida/historial?comrecibida_id=<?php echo $com_recibida->getPrimaryKey() ?>">
                      <i class="entypo-back-in-time"></i>
                      <span class="title">Historial</span>
                  </a>
              </li>
                          <?php 
                      }
                      else
                      { ?>

              <li>
                  <a data-toggle="tooltip" data-original-title="Regresar al expediente" href="<?php echo $base_path.$dataShared['backurl']; ?>">
                      <i class="entypo-back"></i>
                      <span class="title">Regresar</span>
                  </a>
              </li>
                          <?php 
                      } 
                      ?>

              <!-- SEGUNDA PARTE -->

                      <?php if(!$dataShared['IsViewValid'])
                      { ?>
                          <?php 
                          if($sf_user->checkPerm($currentFormAdjuntar, $currentUser))
                          {?>


              <li>
                  <a data-toggle="tooltip" data-original-title="Adjuntar Digitalizaci&oacute;n" href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/recibida.php/com_recibida/digitalizar?comrecibida_id=<?php echo $com_recibida->getPrimaryKey(); ?>','720','400');">
                      <i class="entypo-attach"></i>
                      <span class="title">Adjuntar Digitalización</span>
                  </a>
              </li>

                              <?php 
                          } ?>
                          
                          <?php 
                          if($sf_user->checkPerm($currentFormSticker, $currentUser))
                          {?>

              <li>
                  <a data-toggle="tooltip" data-original-title="Generar sticker de esta comunicaci&oacute;n" href="#"  onclick="window.open('<?php echo $base_path; ?>/recibida.php/com_recibida/sticker?comrecibida_id=<?php echo $com_recibida->getPrimaryKey(); ?>', 'showSticker', 'toolbar=no,menubar=yes,scrollbars=yes,resizable=1,width=450,height=600,top=0');">
                      <i class="fa fa-paste"></i>
                      <span class="title">Sticker</span>
                  </a>
              </li>                    

                              <?php 
                          } ?>
                          
                          <?php 
                          if($sf_user->checkPerm($currentFormGuia, $currentUser))
                          {?> 

              <li>
                  <a data-toggle="tooltip" data-original-title="Establecer la empresa de mensajeria y el numero de la guia" href="<?php echo $base_path; ?>/recibida.php/com_recibida/guia?comrecibida_id=<?php echo $com_recibida->getPrimaryKey(); ?>">
                      <i class="entypo-newspaper"></i>
                      <span class="title">Guía</span>
                  </a>
              </li>  
                              <?php 
                          } ?>
                          
                          <?php 
                          if(trim($com_recibida->getGuia()) != "" && $com_recibida->getEmpresaMensajeriaId() && trim($com_recibida->getEmpresaMensajeria()->getUrl()) != "")
                          { ?>

              <li>
                  <a data-toggle="tooltip" data-original-title="Num. Guia=<?php echo $com_recibida->getGuia()?>" href="<?php echo $com_recibida->getEmpresaMensajeria()->getUrl().$com_recibida->getGuia(); ?>" target="_blank">
                      <i class="entypo-newspaper"></i>
                      <span class="title">Seguimiento Guía</span>
                  </a>
              </li>

                              <?php 
                          } ?>
                      
                          <?php 
                          if($sf_user->checkPerm($currentFormAnular, $currentUser) && !in_array($com_recibida->getEstadocomrecibidaId(), array(5,14,12)))
                          {?>

              <li>
                  <a data-toggle="tooltip" data-original-title="Anular esta comunicaci&oacute;n" href="<?php echo $base_path; ?>/recibida.php/com_recibida/anular?comrecibida_id=<?php echo $com_recibida->getPrimaryKey(); ?>">
                      <i class="entypo-cancel-squared"></i>
                      <span class="title">Anular</span>
                  </a>
              </li>
                              <?php 
                          } ?>
                          
                          <?php 
                          if($sf_user->checkPerm($currentFormEditar, $currentUser)  && !in_array($com_recibida->getEstadocomrecibidaId(), array(5,14,12)))
                          {?>

              <li>
                  <a data-toggle="tooltip" data-original-title="Editar esta comunicaci&oacute;n" href="<?php echo $base_path; ?>/recibida.php/com_recibida/modificar?comrecibida_id=<?php echo $com_recibida->getPrimaryKey(); ?>">
                      <i class="fa fa-edit" style="margin-left: 4px;"></i> 
                      <span class="title">Editar Comunicación</span>
                  </a>
              </li>

                              <?php 
                          } ?>
                          <?php 
                      } ?>

      </ul>

