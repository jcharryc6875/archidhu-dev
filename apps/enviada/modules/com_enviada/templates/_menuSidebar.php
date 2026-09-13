<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$url_file = $ruta_alias_word;
$submit_time = $sf_user->getAttribute('submit_time', '', 'subscriber');
?>
<header class="logo-env">
    <!-- logo -->
    <div class="logo">
        <img class="img-circle" style="border-radius: unset;" src="<?php print $path_theme; ?>assets/images/simad/logo-app-compact.png" width="88px" height="32px" />
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
    <li>
        <a data-toggle="tooltip" data-original-title="Cerrar detalles del registro" href="#" onclick="javascript:parent.jQuery.ReloadAndCloseModalSIMAD();">
            <i class="fa fa-times-circle" id="pr_alineo_02"></i>
            <span class="title">Cerrar</span>
        </a>
    </li>
                                        
            <?php if(!$dataShared['IsViewValid']){ ?>
                <?php if($com_enviada->getEstadocomenviadaId() != 1){ ?>
                    <?php if($sf_user->checkPerm("COM_ENVIADA_VINCULAR", $currentUser)){ ?>
                        <li>
                            <a data-toggle="tooltip" data-original-title="Vincular comunicaci&oacute;n a un expediente del archivo" href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/archivo.php/transferencia/create?origen_transferencia=3&comenviada_id=<?php echo $com_enviada->getPrimaryKey() ?>','960','600')">
                                <i class="entypo-archive"></i>
                                <span class="title">Archivar Expediente</span>
                            </a>
                        </li>
                    <?php } ?>

                    <?php if($sf_user->checkPerm("COM_ENVIADA_ASOCIAR_TRAMITE_ENTRADA", $currentUser)){ ?>
                        <li>
                            <a data-toggle="tooltip" data-original-title="Asociar este radicado a un tr&aacute;mite de entrada" href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/enviada.php/com_enviada/asociarEntrante?comenviada_id=<?php echo $com_enviada->getComenviadaId() ?>');">
                                <i class="entypo-link"></i>
                                <span class="title">Asociar Entrante</span>
                            </a>
                        </li>
                    <?php } ?>

                    <?php if ($sf_user->checkPerm("COM_ENVIADA_REMPLAZAR_FILE_DIGIT", $currentUser) && $com_enviada->getIsCreateWord()) { ?> 
                        <li>
                            <a data-toggle="tooltip" data-original-title="Reemplaza el archivo digitalizado actual" onclick="javascript:jQuery.OpenModalSIMAD('<?php print url_for('com_enviada/uploadTemplateEdit?comenviada_id='.$com_enviada->getPrimaryKey()); ?>', 800, 400);" href="#">
                                <i class="entypo-arrows-ccw"></i>
                                <span class="title">Actualizar Documento</span>
                            </a>
                        </li>
                    <?php } ?>

                    <?php if($sf_user->checkPerm("COM_ENVIADA_ENVIAR_COPIA_USUARIO", $currentUser)){ ?>
                        <li>
                            <a data-toggle="tooltip" data-original-title="Enviar copia de esta comunicaci&oacute;n a otro usuario" href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/enviada.php/com_enviada/addCopia?comenviada_id=<?php echo $com_enviada->getPrimaryKey() ?>','640','480')">
                                <i class="fa fa-reply-all"></i>
                                <span class="title">Enviar Copia</span>
                            </a>
                        </li>
                    <?php } ?>
                    
                    <?php if($sf_user->checkPerm("COM_ENVIADA_DUPLICAR", $currentUser)){ ?>
                        <li>
                            <a data-toggle="tooltip" data-original-title="Duplicar esta comunicaci&oacute;n" href="#" onclick="javascript:jQuery.CloseModalAndHrefParent('<?php echo $base_path; ?>/enviada.php/com_enviada/duplicar?comenviada_id=<?php echo $com_enviada->getPrimaryKey(); ?>');">
                                <i class="fa fa-copy"></i>
                                <span class="title">Duplicar</span>
                            </a>
                        </li>
                    <?php } ?>

                    <?php if($sf_user->checkPerm("COM_ENVIADA_GENERAR_SOLICITUD", $currentUser) && ($com_enviada->getEstadocomenviadaId() != 1)){ ?>
                        <li>
                            <a data-toggle="tooltip" data-original-title="Generar solicitud de servicio para esta comunicaci&oacute;n" href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/servicios.php/servicio/createCom/com_id/<?php echo $com_enviada->getPrimaryKey(); ?>/modulo_id/4');">
                                <i class="entypo-archive" id="pr_alineo_03"></i>
                                <span class="title">Generar Solicitud</span>
                            </a>
                        </li>
                    <?php } ?>
                <?php } ?>
                
                <?php if(($com_enviada->getFirmadoDigital() == 3) && ($sf_user->checkPerm("FIRMA_DIGITAL_RELANZAR", $currentUser) || in_array($currentUser, $usuarios_firman))){ ?>
                    <li>
                        <?php 
                            echo jq_link_to_remote(
                                '<i class="fa fa-edit"></i> <span class="title">Enviar Firma</span>', 
                            array(
                                'update'    => null,
                                'url'     => 'com_enviada/singWsContract',
                                'with'    => " 'comenviada_id=".$com_enviada->getPrimaryKey()."'",
                                'loading' => "javascript:jQuery.LoadingStructData();",
                                'complete' => 'try{ var response_value = JSON.parse(XMLHttpRequest.responseText);javascript:jQuery.CloseLoadingStructData(); if(response_value.httpStatus == 200){ toastr.success(response_value.message);setTimeout(function(){ document.location.reload(); }, 5000); }else{ toastr.error(response_value.message); } }catch(err) { javascript:jQuery.CloseLoadingStructData(); toastr.error(err.message); }',
                            ),array('data-toggle'=>'tooltip', 'data-original-title'=>'Enviar la comunicacion a firma digital, parece que ocurrio un error al enviar para firma en el primer intento'));
                        ?> 
                    </li>
                <?php } ?>

                <?php if(($permisoRadicarFirmaElectronica || ($aprobacion_count <= 1)) && ($com_enviada->getEstadocomenviadaId() == 1)){ ?>
                    <?php if($sf_user->checkPerm("COM_ENVIADA_RADICAR", $currentUser) && ($com_enviada->getEstadocomenviadaId() == 1) && count($usuarios_firman) >= 1){ ?>
                        <?php if(in_array($currentUser, $users_asignado)){ ?>
                            <?php if(count($users_aprueban) <= 1 && in_array($currentUser, $usuarios_firman) || ($com_enviada->getFirmaDesatendida() == 1 && count($aprobacion_ulist) == 0)){ ?>
                                <li>
                                    <a data-toggle="tooltip"  class="confirm-link" data-method-type = "href" data-original-title="Radicar esta comunicaci&oacute;n" data-submit_time="<?php echo $submit_time; ?>" data-endpoint="<?php echo url_for(array('module' => 'com_enviada', 'action' => 'radicar', 'comenviada_id' => $com_enviada->getPrimaryKey())); ?>" href="#">
                                        <i class="fa fa-save" id="pr_alineo_02"></i>
                                        <span class="title">Firmar y Radicar</span>
                                    </a>
                                </li>                                
                            <?php }elseif(in_array($currentUser, $users_aprueban) || in_array($currentUser, $users_asignado)){ ?>
                                <li>
                                    <a data-toggle="tooltip"  class="confirm-link" data-original-title="Aprobar y Enviar" data-submit_time="<?php echo $submit_time; ?>" data-endpoint="<?php echo url_for(array('module' => 'com_enviada', 'action' => 'singComCheck', 'comenviada_id' => $com_enviada->getPrimaryKey())); ?>" href="#">
                                        <i class="fa fa-check"></i>
                                        <span class="title">Aprobar y Enviar</span>
                                    </a> 
                                </li>
                            <?php } ?> 
                        <?php }elseif((count($users_aprueban) == 0 && in_array($currentUser, $users_asignado))){ ?>
                            <li>
                                <a data-toggle="tooltip"  class="confirm-link" data-method-type = "href" data-original-title="Radicar esta comunicaci&oacute;n" data-submit_time="<?php echo $submit_time; ?>" data-endpoint="<?php echo url_for(array('module' => 'com_enviada', 'action' => 'radicar', 'comenviada_id' => $com_enviada->getPrimaryKey())); ?>" href="#">
                                    <i class="fa fa-save"></i>
                                    <span class="title">Firmar y Radicar</span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php 
                    } ?>
                <?php }elseif((in_array($currentUser, $users_aprueban) || in_array($currentUser, $users_asignado)) && ($com_enviada->getEstadocomenviadaId() == 1)){ ?>
                    <li>
                        <a data-toggle="tooltip"  class="confirm-link" data-original-title="Aprobar y enviar al siguiente usuario" data-submit_time="<?php echo $submit_time; ?>" data-endpoint="<?php echo url_for(array('module' => 'com_enviada', 'action' => 'singComCheck', 'comenviada_id' => $com_enviada->getPrimaryKey())); ?>" href="#">
                            <i class="fa fa-check"></i>
                            <span class="title">Aprobar y Enviar</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if($sf_user->checkPerm("COM_ENVIADA_DEVOLUCIONES_FLUJO", $currentUser) && ($com_enviada->getEstadocomenviadaId() == 1)){ ?>
                    <?php if((in_array($currentUser, $users_asignado)) || ($sf_user->checkPerm("COM_ENVIADA_DEVOLVER_DOCUMENTO_TODAS", $currentUser))){ ?>
                        <li>											
                            <a href="#" class="dropdown processrejectenv" style="cursor: pointer;">
                                <i class="entypo-back dropdown-toggle" data-toggle="dropdown"></i>
                                <span class="title">Devolver Documento</span>
                            </a>											
                            <ul class="dropdown-menu dropdown-green" role="menu"></ul>
                        </li>
                    <?php } ?>
                <?php } ?>
                
                <?php if($sf_user->checkPerm("COM_ENVIADA_EDITAR", $currentUser) && ($com_enviada->getEstadocomenviadaId() == 1) && /*!$com_enviada->getIsCreateWord() &&*/ (in_array($currentUser, $users_asignado))) { ?>
                    <li>
                        <a data-toggle="tooltip" data-original-title="Editar el borrador de esta comunicaci&oacute;n" href="javascript:jQuery.CloseModalAndHrefParent('<?php echo $base_path; ?>/enviada.php/com_enviada/edit?comenviada_id=<?php  echo $com_enviada->getPrimaryKey(); ?>');">
                            <i class="fa fa-edit"></i>
                            <span class="title">Editar Borrador</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if($com_enviada->getFirmadoDigital() == 1){ ?>
                    <li>
                        <?php 
                            echo jq_link_to_remote(
                                '<i class="fa fa-eye" id="pr_alineo_02"></i> <span class="title">Visualizar Pdf</span>', 
                                array(
                                    'update'  => null,
                                    'url'     => url_for('com_enviada/viewImageDigit'),
                                    'with'    => "'q_vars=".base64_encode($com_enviada->getPrimaryKey())."&vtoken=".md5($com_enviada->getRadicado().$currentUser.$com_enviada->getFechaCreacion())."'",
                                    'loading' => "javascript:jQuery.LoadingStructData();",
                                    'complete' => "javascript:jQuery.CloseLoadingStructData(); try{ var response_value = JSON.parse(XMLHttpRequest.responseText); if(response_value.status == 200){ toastr.success(response_value.message); window.open(response_value.url_file, 'MyWindow'); }else{ toastr.error(response_value.message); } }catch(err) { toastr.error(err.message); }",
                                ),array('data-original-title'=>$com_enviada->getEstadodigitalizacion()->getDescripcion(), 'data-toggle' => 'tooltip', 'data-placement' => 'top')
                            );
                        ?> 
                    </li>
                    <?php }elseif($com_enviada->getIsCreateWord()){ ?>
                        <li>
                            <a data-toggle="tooltip" data-original-title="Ver documento de la comunicaci&oacute;n" href="<?php echo $base_path; ?>/enviada.php/com_enviada/showPdfByWord?comenviada_id=<?php echo $com_enviada->getPrimaryKey()?>" target="_new">
                                <i class="fa fa-eye" id="pr_alineo_02"></i>
                                <span class="title">Visualizar Pdf</span>
                            </a>
                        </li>
                    <?php }else { ?>
                        <li>
                            <a data-toggle="tooltip" data-original-title="Ver documento pdf de la comunicaci&oacute;n" href="<?php echo $base_path; ?>/enviada.php/com_enviada/showpdf?comenviada_id=<?php echo $com_enviada->getPrimaryKey()?>" target="_new">
                                <i class="fa fa-eye" id="pr_alineo_02"></i>
                                <span class="title">Visualizar Pdf</span>
                            </a>
                        </li>
                    <?php } ?>
                
                <?php 
            }
            else
            { ?>
                <a data-toggle="tooltip" data-original-title="Regresar al expediente" href="<?php echo $base_path.$dataShared['backurl']; ?>">
                    <img src="<?php echo $base_path; ?>/images/simad/ico_regresar.png" title="" width="25" align="middle" />Regresar
                </a>

    <li>
        <a data-toggle="tooltip" data-original-title="Regresar al expediente" href="<?php echo $base_path.$dataShared['backurl']; ?>">
            <i class="entypo-back"></i>
            <span class="title">Regresar</span>
        </a>
    </li>
                <?php 
            } ?>
    

    <!-- SEGUNDA PARTE -->



                <?php if(!$dataShared['IsViewValid'])
                { ?>
                    <?php 
                    if($sf_user->checkPerm("COM_ENVIADA_DIGITALIZAR", $currentUser) && ($com_enviada->getEstadocomenviadaId() != 1) && ($com_enviada->getFirmadoDigital() > 1))
                    {?>
                        
    <li>
        <a data-toggle="tooltip" data-original-title="Adjuntar Digitalizacion" href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/enviada.php/com_enviada/digitalizar?comenviada_id=<?php echo $com_enviada->getPrimaryKey(); ?>','400','250'); return false;">
            <i class="entypo-attach"></i>
            <span class="title">Adjuntar Digitalizacion</span>
        </a>
    </li>

                        <?php 
                    } ?>
                
                    <?php 
                    if($com_enviada->getGuia()!= "" && $com_enviada->getEmpresaMensajeria() != "" && $com_enviada->getEmpresaMensajeria()->getUrl() != "")
                    { ?>

    <li>
        <a data-toggle="tooltip" data-original-title="Num. Guia=<?php echo $com_enviada->getGuia()?>" href="<?php echo $com_enviada->getEmpresaMensajeria()->getUrl().$com_enviada->getGuia(); ?>" target="_blank">
            <i class="entypo-newspaper" id="pr_alineo_03"></i>
            <span class="title">Seguimiento Guia</span>
        </a>
    </li>
                        <?php 
                    } ?>
                    
                    <?php 
                    if($sf_user->checkPerm("COM_ENVIADA_GUIA", $currentUser) && $com_enviada->getEstadocomenviadaId() != 1)
                    { ?> 

    <li>
        <a data-toggle="tooltip" data-original-title="Establecer la empresa de mensajeria y el numero de la guia" href="<?php echo $base_path; ?>/enviada.php/com_enviada/guia?comenviada_id=<?php echo $com_enviada->getPrimaryKey(); ?>">
            <i class="entypo-newspaper" id="pr_alineo_03"></i>
            <span class="title">Guía</span>
        </a>
    </li>
                        <?php 
                    } ?>
                
                    <?php 
                    if($sf_user->checkPerm("COM_ENVIADA_STICKER", $currentUser) && $com_enviada->getEstadocomenviadaId() != 1)
                    { ?>
                                        
    <li>
        <a data-toggle="tooltip" data-original-title="Generar sticker de esta comunicaci&oacute;n" href="#"  onclick="window.open('<?php echo $base_path; ?>/enviada.php/com_enviada/showSticker?comenviada_id=<?php echo $com_enviada->getPrimaryKey(); ?>', 'showSticker', 'toolbar=no,menubar=yes,scrollbars=yes,resizable=1,width=450,height=600,top=0');">
            <i class="fa fa-paste" id="pr_alineo_04"></i>
            <span class="title">Sticker</span>
        </a>
    </li>

                        <?php 
                    } ?>
                    
                    <?php 
                    if($sf_user->checkPerm("COM_ENVIADA_ANULAR", $currentUser) && (!in_array($com_enviada->getEstadocomenviadaId(),array(4,6)) || $sf_user->checkPerm("COM_ENVIADA_ANULAR_ALL", $currentUser)))
                    { ?>

    <li>
        <a data-toggle="tooltip" data-original-title="Anular esta comunicaci&oacute;n" href="<?php echo $base_path; ?>/enviada.php/com_enviada/anular?comenviada_id=<?php echo $com_enviada->getPrimaryKey(); ?>">
            <i class="entypo-cancel-squared"></i>
            <span class="title">Anular Comunicación</span>
        </a>
    </li>
                        <?php 
                    } ?>
                    
                    <?php 
                    if($sf_user->checkPerm("COM_ENVIADA_EDITAR_SIN_CAMBIAR_RADICADO", $currentUser) & $com_enviada->getEstadocomenviadaId() != 1 && !$com_enviada->getIsCreateWord() && (!$com_enviada->getFirmadoDigital()))
                    { ?>

    <li>
        <a data-toggle="tooltip" data-original-title="Editar sin cambiar radicado de la comunicaci&oacute;n" href="<?php echo $base_path; ?>/enviada.php/com_enviada/editSinCR?comenviada_id=<?php  echo $com_enviada->getPrimaryKey(); ?>" >
            <i class="fa fa-edit"></i>
            <span class="title">Editar Comunicación</span>
        </a>
    </li>
                        <?php 
                    } ?>
                    <?php 
                } ?>

</ul>