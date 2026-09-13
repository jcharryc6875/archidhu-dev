<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>
<select name="wf_transicion_id" id="wf_transicion_id" class="form-control input-sm required" 
        onchange="<?php /*echo jq_remote_function(array(                                    
                    	'update'    => '',
                    	'url'     => $base_path.'/administracion.php/wf_detalles/reloadActividadAsync',
                        'with'     => "'wftat=' + jQuery('#wf_transicion_id option:selected').val()",
                        'success' => "updateJSON('actividad_inicial',data);updateJSON('actividad_final',data);",
                        'script' => true,
                    ));*/?>">
    <option value="0">Seleccione...</option>
    <?php                     
    foreach($lista_wf_transiciones as $wf_transicion)
    {
        echo "<option value='".$wf_transicion->getPrimaryKey()."'";
        if($wf_transicion->getPrimaryKey() == $wftat_id){
            echo " selected ";
        }
        echo ">".$wf_transicion->getDescripcion()."</option>";
    }
    ?>
</select>
