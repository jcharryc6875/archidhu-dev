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
            //if($sf_user->checkPerm($currentFormEditar, $currentUser)  && !in_array($com_recibida->getEstadocomrecibidaId(), array(5,14,12)))
            //if($sf_user->checkPerm("COM_RECIBIDA_ASIGNAR_GESTOR", $currentUser) && ($com_recibida->getTipoprocesocomId() == 3) && (!$com_recibida->getMarcaVinculacion()) && !in_array($com_recibida->getEstadocomrecibidaId(), array(5,14,12)))
            if(1 == 1)
            {?>

                <li>
                    <a data-toggle="tooltip" data-original-title="Editar esta comunicaci&oacute;n" href="<?php echo $base_path; ?>/administracion.php/automatizacion_unidaddoc/edit?expedientereglas_id=<?php echo $expediente_reglas->getPrimaryKey(); ?>">
                        <i class="fa fa-edit" style="margin-left: 4px;"></i> 
                        <span class="title">Editar</span>
                    </a>
                </li>

                <?php 
            } 
        ?>
    </ul>