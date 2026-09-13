<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>
<?php echo object_input_hidden_tag($wf_actividad, 'getWfActividadId'); ?>
<!-- Actividad -->
<label for="lbgetWfActividadId" class="col-sm-1 control-label">Descripci&oacute;n:</label>
<div class="col-sm-4">
    <?php 
        echo object_input_tag($wf_actividad, 'getDescripcion', array ('related_class' => 'WfActividad','class'=>'form-control input-sm required',));
    ?>
</div>