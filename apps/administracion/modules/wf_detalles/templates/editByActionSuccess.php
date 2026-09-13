<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>
<?php echo object_input_hidden_tag($wf_transicion, 'getWfTransicionId'); ?>
<!-- Accion a ejecutar -->
<label for="lbgetWfTransicionId" class="col-sm-1 control-label">Descripci&oacute;n:</label>
<div class="col-sm-4">
    <?php 
        echo object_input_tag($wf_transicion, 'getDescripcion', array ('related_class' => 'WfTransicion','class'=>'form-control input-sm required',));
    ?>
</div>