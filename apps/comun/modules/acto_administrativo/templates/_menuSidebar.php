<header class="logo-env">
    <!-- logo -->
    <div class="logo">
        <a href="#">
            <img class="img-circle" style="border-radius: unset;" src="<?php print $path_theme; ?>assets/images/simad/logo-app-compact.png" width="88px" height="32px" />
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
    <li>
        <a data-toggle="tooltip" data-original-title="Cerrar detalles del registro" href="#" onclick="javascript:parent.jQuery.ReloadAndCloseModalSIMAD();">
            <i class="fa fa-times-circle" id="pr_alineo_02"></i>
            <span class="title">Cerrar</span>
        </a>
    </li>
                                        
    <?php if(!$dataShared['IsViewValid']){ ?>
        <?php if(!in_array($acto_administrativo->getEstadoactoadministrativoId(),array(1,2,3,4,5))){ ?>
            <?php if($sf_user->checkPerm("ACTO_ADMINISTRATIVO_VINCULAR", $currentUser)){ ?>
                <li>
                    <a data-toggle="tooltip" data-original-title="Vincular comunicaci&oacute;n a un expediente del archivo" href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/archivo.php/transferencia/create?origen_transferencia=8&actoadministrativo_id=<?php echo $acto_administrativo->getPrimaryKey() ?>','960','600')">
                        <i class="entypo-archive"></i>
                        <span class="title">Archivar Expediente</span>
                    </a>
                </li>
            <?php } ?>
        <?php } ?>
        
        <?php if(in_array($acto_administrativo->getEstadoactoadministrativoId(),array(6,7,9))){ ?>
            <?php if($sf_user->checkPerm("ACTO_ADMINISTRATIVO_ENVIAR_MENSAJERIA", $currentUser)){ ?>
                <li>
                    <a data-toggle="tooltip" data-original-title="Generar solicitud de servicio para esta comunicaci&oacute;n" href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/servicios.php/servicio/createCom/com_id/<?php echo $acto_administrativo->getPrimaryKey(); ?>/modulo_id/17');">
                        <i class="entypo-archive" id="pr_alineo_03"></i>
                        <span class="title">Generar Solicitud</span>
                    </a>
                </li>
            <?php } ?>
        <?php } ?>

        <?php if(($acto_administrativo->getFirmadoDigital() == 3) && ($sf_user->checkPerm("FIRMA_DIGITAL_RELANZAR", $currentUser) || in_array($currentUser, $usuarios_firman))){ ?>
            <li>
                <?php 
                    echo jq_link_to_remote(
                        '<i class="fa fa-edit"></i> <span class="title">Enviar Firma</span>', 
                    array(
                        'update'    => null,
                        'url'     => 'acto_administrativo/singWsContract',
                        'with'    => " 'actoadministrativo_id=".$acto_administrativo->getPrimaryKey()."'",
                        'loading' => "javascript:jQuery.LoadingStructData();",
                        'complete' => 'try{ var response_value = JSON.parse(XMLHttpRequest.responseText);javascript:jQuery.CloseLoadingStructData(); if(response_value.httpStatus == 200){ toastr.success(response_value.message);setTimeout(function(){ document.location.reload(); }, 5000); }else{ toastr.error(response_value.message); } }catch(err) { javascript:jQuery.CloseLoadingStructData(); toastr.error(err.message); }',
                    ),array('data-toggle'=>'tooltip', 'data-original-title'=>'Enviar la comunicacion a firma digital, parece que ocurrio un error al enviar para firma en el primer intento'));
                ?> 
            </li>
        <?php } ?>

        <?php if($permisoRadicarFirmaElectronica  && $aprobacion_count <= 1 && (in_array($acto_administrativo->getEstadoactoadministrativoId(),array(1,2,3,4)))){ ?>
            <?php if($sf_user->checkPerm("ACTO_ADMINISTRATIVO_RADICAR", $currentUser) && ($acto_administrativo->getEstadoactoadministrativoId() == 1) && count($usuarios_firman) >= 1){ ?>
                <?php if(in_array($currentUser, $users_asignado)){ ?>
                    <?php if(count($users_aprueban) <= 1 && in_array($currentUser, $usuarios_firman) || (/*$acto_administrativo->getFirmaDesatendida() == 1 &&*/ count($aprobacion_ulist) == 0)){ ?>
                        <li>
                            <a data-toggle="tooltip" data-original-title="Firmar y generar acto administrativo" href="<?php echo $base_path; ?>/comun.php/acto_administrativo/radicar?actoadministrativo_id=<?php echo $acto_administrativo->getPrimaryKey(); ?>">
                                <i class="fa fa-gavel"></i>
                                <span class="title">Firmar y Generar</span>
                            </a>
                        </li>
                    <?php }elseif(in_array($currentUser, $users_aprueban) || in_array($currentUser, $users_asignado)){ ?>
                        <li>
                            <?php 
                                echo jq_link_to_remote(
                                    '<i class="fa fa-check"></i> <span class="title">Aprobar y Enviar</span>', 
                                array(
                                    'update'    => null,
                                    'url'     => 'acto_administrativo/singComCheck',
                                    'with'    => "'actoadministrativo_id=".$acto_administrativo->getPrimaryKey()."'",
                                    'loading' => "javascript:jQuery.LoadingStructData();",
                                    'complete' => 'try{ var response_value = JSON.parse(XMLHttpRequest.responseText);javascript:jQuery.CloseLoadingStructData(); jQuery.handleSingComCheckResponse(response_value, '.$acto_administrativo->getPrimaryKey().'); }catch(err) { javascript:jQuery.CloseLoadingStructData(); toastr.error(err.message); }',
                                ),array('data-toggle'=>'tooltip', 'data-original-title'=>'Aprobar y enviar al siguiente usuario'));
                            ?> 
                        </li>
                    <?php } ?>
                <?php }elseif((count($users_aprueban) == 0 && in_array($currentUser, $users_asignado))){ ?>
                    <li>
                        <a data-toggle="tooltip" data-original-title="Firmar y generar este acto administrativo" href="<?php echo $base_path; ?>/comun.php/acto_administrativo/radicar?actoadministrativo_id=<?php echo $acto_administrativo->getPrimaryKey(); ?>">
                            <i class="fa fa-edit"></i>
                            <span class="title">Firmar y Generar</span>
                        </a>
                    </li>
                <?php } ?>
            <?php } ?>
        <?php }elseif((in_array($currentUser, $users_aprueban) || in_array($currentUser, $users_asignado)) && (in_array($acto_administrativo->getEstadoactoadministrativoId(),array(1,2,3,4)))){ ?>
            <li>
                <?php 
                    echo jq_link_to_remote(
                        '<i class="fa fa-check"></i> <span class="title">Aprobar y Enviar</span>', 
                        array(
                            'update'    => null,
                            'url'     => 'acto_administrativo/singComCheck',
                            'with'    => "'actoadministrativo_id=".$acto_administrativo->getPrimaryKey()."'",
                            'loading' => "javascript:jQuery.LoadingStructData();",
                            'complete' => 'try{ var response_value = JSON.parse(XMLHttpRequest.responseText);javascript:jQuery.CloseLoadingStructData(); jQuery.handleSingComCheckResponse(response_value, '.$acto_administrativo->getPrimaryKey().'); }catch(err) { javascript:jQuery.CloseLoadingStructData(); toastr.error(err.message); }',
                        ),array('data-toggle'=>'tooltip', 'data-original-title'=>'Aprobar y enviar al siguiente usuario'));
                ?> 
            </li>
        <?php } ?>

        <?php if($sf_user->checkPerm("ACTO_ADMINISTRATIVO_DEVOLUCIONES_FLUJO", $currentUser) && (in_array($acto_administrativo->getEstadoactoadministrativoId(),array(1,2,3,4)))){ ?>
            <?php if((in_array($currentUser, $users_asignado)) || ($sf_user->checkPerm("ACTO_ADMINISTRATIVO_DEVOLVER_DOCUMENTO_TODAS", $currentUser))){ ?>
                <li>
                    <a href="#" data-toggle="tooltip" data-original-title="Devolver este tramite" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/comun.php/acto_administrativo/rejectedObs?actoadministrativo_id=<?php echo $acto_administrativo->getPrimaryKey()?>',720,400);">  
                        <i class="entypo-back dropdown-toggle" data-toggle="dropdown"></i>
                        <span class="title">Devolver Documento</span>
                    </a>
                </li>
                <?php 
            } ?>
            <?php 
        } ?>
        
        <?php if($sf_user->checkPerm("ACTO_ADMINISTRATIVO_EDITAR", $currentUser) && (in_array($acto_administrativo->getEstadoactoadministrativoId(),array(1,2,3,4))) && (in_array($currentUser, $users_asignado))){ ?>
            <li>
                <a data-toggle="tooltip" data-original-title="Editar el borrador de este acto administrativo" href="javascript:jQuery.CloseModalAndHrefParent('<?php echo $base_path; ?>/comun.php/acto_administrativo/edit?actoadministrativo_id=<?php  echo $acto_administrativo->getPrimaryKey(); ?>');">
                    <i class="fa fa-edit"></i>
                    <span class="title">Editar Borrador</span>
                </a>
            </li>
        <?php } ?>

        <?php if ($sf_user->checkPerm("ACTO_ADMINISTRATIVO_PUBLICAR", $currentUser)  && in_array($acto_administrativo->getEstadoactoadministrativoId(), array(6, 7, 9))) { ?>

            <li>
                <a data-toggle="tooltip" data-original-title=">Publicar este acto administrativo" href="<?php echo $base_path; ?>/comun.php/acto_administrativo/publicar?actoadministrativo_id=<?php echo $acto_administrativo->getPrimaryKey() ?>">
                    <i class="entypo-publish" id="pr_alineo_03"></i>
                    <span class="title">Publicar Acto Administrativo</span>
                </a>
            </li>
        <?php } ?>

        <?php if ($acto_administrativo->getFirmadoDigital() == 1 && $acto_administrativo->getEstadodigitalizacionId() == 2) { ?>
            <li>
                <?php 
                    echo jq_link_to_remote(
                        '<i class="fa fa-eye" id="pr_alineo_02"></i> <span class="title">Visualizar Pdf</span>', 
                        array(
                            'update'  => null,
                            'url'     => url_for('acto_administrativo/viewImageDigit'),
                            'with'    => "'q_vars=".base64_encode($acto_administrativo->getPrimaryKey())."&vtoken=".md5($acto_administrativo->getNumeroResolucion().$currentUser.$acto_administrativo->getFechaCreacion())."'",
                            'loading' => "javascript:jQuery.LoadingStructData();",
                            'complete' => "javascript:jQuery.CloseLoadingStructData(); try{ var response_value = JSON.parse(XMLHttpRequest.responseText); if(response_value.status == 200){ toastr.success(response_value.message); window.open(response_value.url_file, 'MyWindow'); }else{ toastr.error(response_value.message); } }catch(err) { toastr.error(err.message); }",
                            ),array('data-original-title'=>$acto_administrativo->getEstadoActoAdministrativo()->getTooltip(), 'data-toggle' => 'tooltip', 'data-placement' => 'top')
                        );
                ?> 
            </li>
        <?php }else { ?>
            <li>
                <a data-toggle="tooltip" data-original-title="Previsualizar este documento" href="<?php echo $base_path; ?>/comun.php/acto_administrativo/showpdf?actoadministrativo_id=<?php echo $acto_administrativo->getPrimaryKey()?>" target="_new">
                    <i class="fa fa-eye" id="pr_alineo_02"></i>
                    <span class="title">Visualizar Pdf</span>
                </a>
            </li>
        <?php } ?>
                
    <?php }else{ ?>                
        <li>
            <a data-toggle="tooltip" data-original-title="Regresar al expediente" href="<?php echo $base_path.$dataShared['backurl']; ?>">
                <i class="entypo-back"></i>
                <span class="title">Regresar</span>
            </a>
        </li>
    <?php } ?>
    <!-- SEGUNDA PARTE -->


    <?php if(!$dataShared['IsViewValid']){ ?>
        <?php if($sf_user->checkPerm("ACTO_ADMINISTRATIVO_ANULAR", $currentUser) && (!in_array($acto_administrativo->getEstadoactoadministrativoId(),array(1,2,3,4,5)) || $sf_user->checkPerm("ACTO_ADMINISTRATIVO_ANULAR_ALL", $currentUser))){ ?>
            <li>
                <a data-toggle="tooltip" data-original-title="Anular esta comunicaci&oacute;n" href="<?php echo $base_path; ?>/comun.php/acto_administrativo/anular?actoadministrativo_id=<?php echo $acto_administrativo->getPrimaryKey(); ?>">
                    <i class="entypo-cancel-squared"></i>
                    <span class="title">Anular Comunicaci&oacute;n</span>
                </a>
            </li>
        <?php } ?>
    <?php } ?>
</ul>