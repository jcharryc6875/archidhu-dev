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

    <?php
    if (!$dataShared['IsViewValid']) { ?>
        <?php
        if ($estadocominterna_id != 1) { ?>

            <?php
            if ($sf_user->checkPerm("COM_INTERNA_REENVIAR", $currentUser)) { ?>
                <li>
                    <a data-toggle="tooltip" data-original-title="Reenviar comunicacion" onclick="javascript:jQuery.CloseModalAndHrefParent('<?php echo $base_path; ?>/interna.php/com_interna/reenviar?cominterna_id=<?php echo $com_interna->getCominternaId(); ?>');" href="#">
                        <i class="fa fa-mail-forward"></i>
                        <span class="title">Reenviar</span>
                    </a>
                </li>
            <?php
            } ?>

            <?php
            if ($sf_user->checkPerm("COM_INTERNA_REMPLAZAR_FILE_DIGIT", $currentUser) && $com_interna->getIsCreateWord()) { ?>
                <li>
                    <a data-toggle="tooltip" data-original-title="Reemplaza el archivo digitalizado actual" onclick="javascript:jQuery.OpenModalSIMAD('<?php print url_for('com_interna/uploadTemplateEdit?cominterna_id='.$com_interna->getPrimaryKey()); ?>', 800, 400);" href="#">
                        <i class="entypo-arrows-ccw"></i>
                        <span class="title">Actualizar Documento</span>
                    </a>
                </li>
            <?php
            } ?>

            <li>
                <a data-toggle="tooltip" data-original-title="Ver el historial de esta comunicacion" href="<?php echo $base_path; ?>/interna.php/com_interna/historial?codigo_reen_resp=<?php echo $com_interna->getCodigoReenResp(); ?>&cominterna_id=<?php echo $com_interna->getPrimaryKey(); ?>">
                    <i class="entypo-back-in-time" id="pr_alineo_03"></i>
                    <span class="title">Historial</span>
                </a>
            </li>

            <?php
            if ($sf_user->checkPerm("COM_INTERNA_DUPLICAR", $currentUser)) { ?>

                <li>
                    <a data-toggle="tooltip" data-original-title="Duplicar esta comunicacion" onclick="javascript:jQuery.CloseModalAndHrefParent('<?php echo $base_path; ?>/interna.php/com_interna/duplicar?cominterna_id=<?php echo $com_interna->getPrimaryKey(); ?>')">
                        <i class="fa fa-copy"></i>
                        <span class="title">Duplicar</span>
                    </a>
                </li>
            <?php
            } ?>

            <?php
            if ($sf_user->checkPerm("COM_INTERNA_REASIGNAR", $currentUser) && !in_array($estadocominterna_id, array(4, 6))) { ?>


                <li>
                    <a data-toggle="tooltip" data-original-title="Asigna un nuevo destinatario para la comunicación" href="<?php echo url_for('com_interna/reasignar?cominterna_id=' . $com_interna->getPrimaryKey()) ?>">
                        <i class="fa fa-sign-in"></i>
                        <span class="title">Reasignar</span>
                    </a>
                </li>

            <?php
            } ?>

            <?php
            if ($sf_user->checkPerm("COM_INTERNA_CREAR_SERVICIO", $currentUser) && !in_array($estadocominterna_id, array(4, 6))) { ?>
                <li>
                    <a data-toggle="tooltip" data-original-title="Generar solicitud de servicio para esta comunicacion" href="#" onclick="javascript:jQuery.CloseModalAndHrefParent('<?php echo $base_path; ?>/servicios.php/servicio/createCom/com_id/<?php echo $com_interna->getPrimaryKey(); ?>/modulo_id/2');">
                        <i class="entypo-archive"></i>
                        <span class="title">Generar Solicitud</span>
                    </a>
                </li>
            <?php
            } ?>

            <?php
            if (!in_array($estadocominterna_id, array(4, 6))) { ?>
                <li>
                    <a data-toggle="tooltip" data-original-title="Responder la comunicacion" onclick="javascript:jQuery.CloseModalAndHrefParent('<?php echo $base_path; ?>/interna.php/com_interna/responder?cominterna_id=<?php echo $com_interna->getPrimaryKey(); ?>')">
                        <i class="fa fa-external-link"></i>
                        <span class="title">Responder</span>
                    </a>
                </li>
            <?php
            } ?>
        <?php
        } ?>

        <?php
        if ($com_interna->getFirmadoDigital() == 1) {
            echo "<li>";
            echo jq_link_to_remote(
                '<i class="fa fa-file-text" id="pr_alineo_02"></i> <span class="title">Visualizar PDF</span>',
                array(
                    'update'  => null,
                    'url'     => url_for('com_interna/viewImageDigit'),
                    'with'    => "'q_vars=" . base64_encode($com_interna->getPrimaryKey()) . "&vtoken=" . md5($com_interna->getRadicado() . $currentUser . $com_interna->getFechaCreacion()) . "'",
                    'loading' => "javascript:jQuery.LoadingStructData();",
                    'complete' => "javascript:jQuery.CloseLoadingStructData();",
                    'complete' => "javascript:jQuery.CloseLoadingStructData(); try{ var response_value = JSON.parse(XMLHttpRequest.responseText); if(response_value.status == 200){ toastr.success(response_value.message); window.open(response_value.url_file, 'MyWindow'); }else{ toastr.error(response_value.message); } }catch(err) { toastr.error(err.message); }",
                ),
                array('data-original-title' => $com_interna->getEstadodigitalizacion()->getDescripcion(), 'data-toggle' => 'tooltip', 'data-placement' => 'top')
            );
            echo "</li>";
        } elseif ($com_interna->getIsCreateWord()) {
            $url_file = $ruta_alias_word; ?>

            <li>
                <a data-toggle="tooltip" data-original-title="Ver documento de la comunicaci&oacute;n" href="<?php echo $base_path; ?>/interna.php/com_interna/showPdfByWord?cominterna_id=<?php echo $com_interna->getPrimaryKey() ?>" target="_new">
                    <i class="fa fa-eye" id="pr_alineo_02"></i>
                    <span class="title">Visualizar Pdf</span>
                </a>
            </li>
        <?php
        } else { ?>
            <li>
                <a data-toggle="tooltip" data-original-title="Generar comunicacion en pdf" href="<?php echo $base_path; ?>/interna.php/com_interna/showpdf?cominterna_id=<?php echo $com_interna->getPrimaryKey() ?>" target="_new">
                    <i class="fa fa-eye" id="pr_alineo_02"></i>
                    <span class="title">Visualizar Pdf</span>
                </a>
            </li>
        <?php } ?>

        <?php if ($estadocominterna_id == 1) { ?>
            <?php if ($permisoRadicarFirmaElectronica || ($aprobacion_count <= 1)) { ?>
                <?php if ($sf_user->checkPerm("COM_INTERNA_RADICAR", $currentUser) && (count($usuarios_firman) >= 1)) { ?>
                    <?php if (($currentUser == $usuario_asignado)) { ?>
                        <?php if ($aprobacion_count <= 1 && in_array($currentUser, $usuarios_firman) || ($com_interna->getFirmaDesatendida() == 1 && count($aprobacion_ulist) == 0)) { ?>
                            <li>
                                <a data-toggle="tooltip" class="confirm-link" data-method-type = "href" data-original-title="Radicar esta comunicaci&oacute;n" data-submit_time="<?php echo $submit_time; ?>" data-endpoint="<?php echo url_for(array('module' => 'com_interna', 'action' => 'radicar', 'vshow' => md5($com_interna->getCominternaId()), 'cominterna_id' => $com_interna->getCominternaId())); ?>" href="#">
                                    <i class="fa fa-save"></i>
                                    <span class="title">Firmar y Radicar</span>
                                </a>
                            </li>
                        <?php } elseif (in_array($currentUser, $users_aprueban) || ($currentUser == $usuario_asignado)) { ?>
                            <li>
                                <a data-toggle="tooltip" class="confirm-link" data-method-type = "ajax" data-original-title="Radicar esta comunicaci&oacute;n" data-submit_time="<?php echo $submit_time; ?>" data-endpoint="<?php echo url_for(array('module' => 'com_interna', 'action' => 'singComCheck', 'cominterna_id' => $com_interna->getPrimaryKey())); ?>" href="#">
                                    <i class="fa fa-check"></i>
                                    <span class="title">Aprobar y Enviar</span>
                                </a>
                            </li>
                        <?php } ?>
                    <?php } elseif ((count($users_aprueban) == 0 && $currentUser == $usuario_asignado)) { ?>
                        <li>
                            <a data-toggle="tooltip" class="confirm-link" data-method-type = "href" data-original-title="Radicar esta comunicaci&oacute;n" data-submit_time="<?php echo $submit_time; ?>" data-endpoint="<?php echo url_for(array('module' => 'com_interna', 'action' => 'radicar', 'vshow' => md5($com_interna->getCominternaId()), 'cominterna_id' => $com_interna->getCominternaId())); ?>" href="#">
                                <i class="fa fa-edit"></i>
                                <span class="title">Firmar y Radicar</span>
                            </a>
                        </li>
                    <?php } ?>
                <?php } ?>
            <?php } elseif ($currentUser == $usuario_asignado) { ?>
                <li>
                    <a data-toggle="tooltip" class="confirm-link" data-method-type = "ajax" data-original-title="Aprobar y Enviar" data-submit_time="<?php echo $submit_time; ?>" data-endpoint="<?php echo url_for(array('module' => 'com_interna', 'action' => 'singComCheck', 'cominterna_id' => $com_interna->getPrimaryKey())); ?>" href="#">
                        <i class="fa fa-check"></i>
                        <span class="title">Aprobar y Enviar</span>
                    </a>
                </li>
            <?php } ?>

            <?php if($sf_user->checkPerm("COM_INTERNA_DEVOLUCIONES_FLUJO", $currentUser)){ ?>
                <?php if(($currentUser == $usuario_asignado) || ($sf_user->checkPerm("COM_INTERNA_DEVOLVER_DOCUMENTO_TODAS", $currentUser))){ ?>
                    <li>											
                        <a href="#" class="dropdown processreject" style="cursor: pointer;">
                            <i class="entypo-back dropdown-toggle" data-toggle="dropdown"></i>
                            <span class="title processreject">Devolver Documento</span>
                        </a>											
                        <ul class="dropdown-menu dropdown-green" role="menu"></ul>
                    </li>

                    
                <?php } ?>
            <?php } ?>
        <?php } ?>

        <?php if ($sf_user->checkPerm("COM_INTERNA_EDITAR", $currentUser) && ($estadocominterna_id == 1) && ($currentUser == $usuario_asignado)) { ?>
            <li>
                <a data-toggle="tooltip" data-original-title="Editar el borrador de esta comunicacion" href="#" onclick="javascript:jQuery.CloseModalAndHrefParent('<?php echo $base_path; ?>/interna.php/com_interna/edit?cominterna_id=<?php echo $com_interna->getCominternaId(); ?>');">
                    <i class="fa fa-edit"></i>
                    <span class="title">Editar Borrador</span>
                </a>
            </li>
        <?php } ?>
    <?php } else { ?>
        <li>
            <a data-toggle="tooltip" data-original-title="Regresar al expediente" href="<?php echo $base_path . $dataShared['backurl']; ?>">
                <i class="entypo-back"></i>
                <span class="title">Regresar</span>
            </a>
        </li>
    <?php } ?>


    <!-- SEGUNDA PARTE -->


    <?php
    if (!$dataShared['IsViewValid']) { ?>
        <?php
        if ($sf_user->checkPerm("COM_INTERNA_DIGITALIZAR", $currentUser) && $estadocominterna_id != 1 && $com_interna->getRadicado() != 'Sin Radicar') { ?>

            <li>
                <a data-toggle="tooltip" data-original-title="Adjuntar Digitalizacion" href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/interna.php/com_interna/digitalizar?cominterna_id=<?php echo $com_interna->getCominternaId() ?>','400','250'); return false;">
                    <i class="entypo-attach"></i>
                    <span class="title">Adjuntar Digitalización</span>
                </a>
            </li>

        <?php
        } ?>

        <?php
        if ($estadocominterna_id != 1 && $com_interna->getRadicado() != 'Sin Radicar') { ?>

            <li>
                <a data-toggle="tooltip" data-original-title="Vincular comunicaci&oacute;n a un expediente de archivo" href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/archivo.php/transferencia/create?origen_transferencia=1&cominterna_id=<?php echo $com_interna->getPrimaryKey(); ?>','960','600')">
                    <i class="entypo-archive"></i>
                    <span class="title">Archivar Documento</span>
                </a>
            </li>

        <?php
        } ?>

        <?php
        if ($sf_user->checkPerm("COM_INTERNA_STICKER", $currentUser) && $estadocominterna_id != 1 && $com_interna->getRadicado() != 'Sin Radicar') { ?>

            <li>
                <a data-toggle="tooltip" data-original-title="Generar sticker de esta comunicacion" href="#" onclick="window.open('<?php echo $base_path; ?>/interna.php/com_interna/showSticker?cominterna_id=<?php echo $com_interna->getCominternaId() ?>', 'showSticker', 'toolbar=no,menubar=yes,scrollbars=yes,resizable=1,width=450,height=600,top=0');">
                    <i class="fa fa-paste" id="pr_alineo_04"></i>
                    <span class="title">Sticker</span>
                </a>
            </li>

        <?php
        } ?>

        <?php
        if ($com_interna->getInstancia() != '') { ?>

            <li>
                <a data-toggle="tooltip" data-original-title="Ver WorkFlow" href="<?php echo $base_path; ?>/administracion.php/wf_instancia_bitacora/list?instancia_id=<?php echo $com_interna->getInstancia() ?>">
                    <i class="fa fa-cogs"></i>
                    <span class="title">Workflow</span>
                </a>
            </li>
        <?php
        } ?>

        <?php
        if ($sf_user->checkPerm("COM_INTERNA_ANULAR", $currentUser)  && !in_array($estadocominterna_id, array(4, 5, 7, 8, 9))) { ?>

            <li>
                <a data-toggle="tooltip" data-original-title="Anular esta comunicacion" href="<?php echo $base_path; ?>/interna.php/com_interna/anular?cominterna_id=<?php echo $com_interna->getCominternaId() ?>">
                    <i class="fa fa-times" id="pr_alineo_04"></i>
                    <span class="title">Anular Comunicación</span>
                </a>
            </li>
        <?php
        } ?>

        <?php if ($sf_user->checkPerm("COM_INTERNA_PUBLICAR", $currentUser)  && !in_array($estadocominterna_id, array(1, 4, 6))) { ?>

            <li>
                <a data-toggle="tooltip" data-original-title=">Publicar esta comunicacion" href="<?php echo $base_path; ?>/interna.php/com_interna/publicar?cominterna_id=<?php echo $com_interna->getCominternaId() ?>">
                    <i class="entypo-publish" id="pr_alineo_04"></i>
                    <span class="title">Publicar Comunicación</span>
                </a>
            </li>
        <?php } ?>

        <?php
        if ($sf_user->checkPerm("COM_INTERNA_SOBRE", $currentUser)  && $estadocominterna_id != 1  && $com_interna->getRadicado() != 'Sin Radicar') { ?>

            <li>
                <a data-toggle="tooltip" data-original-title="Ver Sobre de la comunicacion" href="#" onclick="window.open('<?php echo $base_path; ?>/interna.php/com_interna/sobre?cominterna_id=<?php echo $com_interna->getCominternaId() ?>', 'showSticker', 'toolbar=no,menubar=yes,scrollbars=yes,resizable=1,width=450,height=600,top=0');">
                    <i class="fa fa-envelope"></i>
                    <span class="title">Generar Sobre</span>
                </a>
            </li>

        <?php
        } ?>

        <?php
        if (!empty($aprobadores_name)) {
            if ($sf_user->checkPerm($currentFormVerAprobaciones, $currentUser) || $currentUser == $creador_id || $is_usuario_aprobador) {

        ?>
                <li>
                    <?php

                    echo jq_link_to_function(
                        '<i class="fa fa-check"></i> <span class="title">Aprobaciones</span>',
                        'javascript:jQuery.OpenModalSIMAD("' . $base_path . '/administracion.php/com_aprobaciones/index?modulo_id=2&consecutivo_id=' . $com_interna->getPrimaryKey() . '&user_id=' . $creador_id . '","600","350")',
                        array('data-toggle' => 'tooltip', 'data-original-title' => 'Aprobar esta comunicacion', 'id' => 'buttonaprob')
                    );

                    ?>
                <li>
            <?php
            }
        }
            ?>

            <?php
            if ($com_interna->getEstadoComInternaId() != 1) {
                if ($sf_user->checkPerm("COM_INTERNA_EDITAR_SIN_CAMBIAR_RADICADO", $currentUser)  && (!$com_interna->getIsCreateWord()) && (!$com_interna->getFirmadoDigital() == 1)) { ?>

                <li>
                    <a data-toggle="tooltip" data-original-title="Editar sin cambiar el radicado de la comunicación" href="<?php echo $base_path; ?>/interna.php/com_interna/editSinCR?cominterna_id=<?php echo $com_interna->getPrimaryKey() ?>">
                        <i class="fa fa-edit"></i>
                        <span class="title">Editar</span>
                    </a>
                </li>
        <?php
                }
            }
        ?>

        <?php
        if ($sf_user->checkPerm("COM_INTERNA_GUIA", $currentUser)  && $estadocominterna_id != 1  && $com_interna->getRadicado() != 'Sin Radicar') { ?>

            <li>
                <a data-toggle="tooltip" data-original-title="Establecer guia de la comunicacion" href="<?php echo $base_path; ?>/interna.php/com_interna/guia?cominterna_id=<?php echo $com_interna->getPrimaryKey() ?>">
                    <i class="entypo-newspaper"></i>
                    <span class="title">Guia</span>
                </a>
            </li>

        <?php
        } ?>

        <?php
        if ($com_interna->getGuia() != "") { ?>

            <li>
                <a data-toggle="tooltip" data-original-title="Num. Guia=<?php echo $com_interna->getGuia() ?>" href="<?php echo $com_interna->getEmpresaMensajeria()->getUrl() . $com_interna->getGuia() ?>" target="_blank">
                    <i class="entypo-newspaper"></i>
                    <span class="title">Seguimiento Guia</span>
                </a>
            </li>



        <?php
        } ?>
    <?php
    } ?>




</ul>