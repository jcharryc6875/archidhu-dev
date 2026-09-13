<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
$ruta_base = substr(sfConfig::get('base_simad'), -1) == "/" ? sfConfig::get('base_simad') : sfConfig::get('base_simad').'/';

$modulo = $sf_params->get('modulo');
$unidaddocumental_id = $contenido_unidad_documental->getUnidaddocumental()->getPrimaryKey();
$currentFormEditarTipo   = $sf_user->checkPerm($modulo."_EDITAR_TIPO_DOCUMENTAL", $currentUser);
$currentFormCompartirTipo   = $sf_user->checkPerm($modulo."_COMPARTIR_CONTENIDO", $currentUser);
?>
<!-- Iconos inicio -->
<div class="panel-options">
    <?php
        // Editar Tipo Documental
        if($currentFormEditarTipo && $contenido_unidad_documental->getUnidaddocumental()->getEstadotransferencia() == 0){ 
            echo link_to('<i class="fa fa-edit fa-2x" style="margin-right: 7px;"></i>', 'contenido_documental/edit?contenidounidaddocumental_id='.$contenido_unidad_documental->getContenidounidaddocumentalId().'&unidaddocumental_id='.$unidaddocumental_id.'&modulo='.$modulo,array('data-placement'=>"bottom",'data-original-title'=>'Editar este contenido documental', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip'));
        }
        //********************************************************************************************************************
        if($contenido_unidad_documental->getUnidaddocumental()->getEstadotransferencia() == 0){
            $array_solicitudes = array();
            //****************************************************************************************************************
            if($contador == 0 && $detalles_prestamo == null && $contador_unidad_documental == 0 && $toda_unidad_documental == 0)
            {
                echo link_to('<i class="fa fa-external-link fa-2x"></i>', 'solicitud_prestamo/update?unidaddocumental_id='.$contenido_unidad_documental->getUnidaddocumentalId().'&contenidounidaddocumental_id='.$contenido_unidad_documental->getContenidounidaddocumentalId().'&modulo='.$modulo,array('data-placement'=>"bottom",'data-original-title'=>'Solicitar prestado este documento', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip'));
            }
            elseif($contador)
            {
                echo '<i class="fa fa-check-square-o fa-2x tooltip-primary" data-toggle="tooltip" data-original-title="El documento se encuentra esta en su lista de solicitudes prendientes"></i>';
            }
            elseif($detalles_prestamo != null)
            {
                echo jq_link_to_function('<i class="fa fa-external-link fa-2x"></i>','javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/solicitud_prestamo/showTipo?detalleprestamo_id='.$detalles_prestamo->getPrimaryKey().'","600","400")',array('data-placement'=>"bottom",'data-original-title'=>'El Documento se encuentra prestado', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip'));		
            }
        }
        //*****************************************************************************************************************
        if($currentFormCompartirTipo){
            echo link_to_function('<i class="entypo-share fa-2x"></i>', 'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/contenido_documental/consultarExp?contenidounidaddocumental_id='.$contenido_unidad_documental->getContenidounidaddocumentalId().'")',array('data-placement'=>"bottom",'data-original-title'=>'Compartir este tipo documental con otro expediente', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip'));
        }
    ?>
</div>