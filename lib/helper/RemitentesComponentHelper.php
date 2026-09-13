<?php

/**
 * @author Javier Fernando Charry
 * @copyright 2021
 */


/**
 * Returns a select2 input tag that will update a DOM element '$element_id'
 * according to the '$options' passed.
 *
 * Possible '$options' are: 
 * 'caption'            label input.
 * 'placeholder'        placeholder for the input tag
 * 'class'              css class for input tag, default 'form-control input-sm'
 * 'values'             value user id for set default values select2
 * 'values_text'        text name user for default values select
 * 'url'                solo url interna eje: interesados/list
 */
 
function component_remitente_multiple($element_id, $idhidenuser,$options = array())
{   $path_theme = sfConfig::get('theme_simad');
    $base_path = sfConfig::get('base_simad');
	$mod_comun = "/comun.php";
	$base_url = $base_path.$mod_comun;
    //***************************************************************************************
    if (!is_array($options)){
       $options = array($options);
    }
    //***************************************************************************************
    $caption = isset($options['caption']) ? $options['caption'] : 'Copia';
    $placeholder = isset($options['placeholder']) ? $options['placeholder'] : 'Digite nombre remitente';
    $class = isset($options['class']) ? $options['class'] : 'form-control input-sm sl';
    $option = isset($options['option']) ? $options['option'] : 0;
    $buttontitle = isset($options['buttontitle']) ? $options['buttontitle'] : "Buscar";
    $maximumSelectionSize = isset($options['maximumSelectionSize']) ? $options['maximumSelectionSize'] : -1;
    $ismultiple = isset($options['ismultiple']) ? $options['ismultiple'] : 1;
    $values_uid = isset($options['values']) ? preg_split("/[,]+/",$options['values'], -1, PREG_SPLIT_NO_EMPTY) : null;
    $values_text = isset($options['values_text']) ? preg_split("/[,]+/",$options['values_text'], -1, PREG_SPLIT_NO_EMPTY) : null;    
    $full_url = isset($options['url']) ? $base_url."/".$options['url'].'?opcion='.$option : $base_url.'/directorio_externo/selectRemitenteActive?opcion='.$option;
    $coldivwidth = isset($options['coldivwidth']) ? $options['coldivwidth'] : "col-sm-8";
	$toptext = isset($options['toptext']) ? $options['toptext'] : 0;
    $full_url = isset($options['addquery']) ? $full_url.'&'.$options['addquery'] : $full_url;
    $form_group = isset($options['formgroup']) ? $options['formgroup'] : true;
    //***************************************************************************************
    $term_list = null;
    for($i=0; $i < count($values_uid); $i++){
        $remitente = DirectorioExternoPeer::retrieveByPK($values_uid[$i]);
        $text_name = "";$nuid_text = "";
        if($remitente != null){
            $text_name = htmlentities(trim($remitente->getNombre()));
            $nuid_text = trim($remitente->getNit()) ? trim($remitente->getNit()) : "";
        }
        //***********************************************************************************
        $text_name = $nuid_text ? ($nuid_text." - ".$text_name) : $text_name;
        $avatar = sfConfig::get('base_simad').'/images/simad/ico_logo_users.png';
        //***********************************************************************************
        if(trim($values_uid[$i])){
            $term_list[] = array("id" => $values_uid[$i], "text" => $text_name, "img" => $avatar);
        }
    }
    $data_json = json_encode($term_list);
    //***************************************************************************************
	if($toptext){
		$html = '  
		<div class="row">
			<div class="'.$coldivwidth.'">
				<div class="form-group">
					<label for="lb'.$element_id.'" class="control-label">'.$caption.':</label>
					<div class="input-group">
						<span class="input-group-addon"><i class="entypo-user"></i></span>
						<input type="hidden" name="'.$element_id.'" id="'.$element_id.'" class="'.$class.'" placeholder="'.$placeholder.'" style="width: 100%;" />
						<div class="input-group-btn">
							<button type="button" name="e2_cl'.$element_id.'" id="e2_cl'.$element_id.'" class="btn btn-primary btn-sm">'.$buttontitle.'</button>
							<button id="e8_cl'.$element_id.'" name="e8_cl'.$element_id.'" type="button" class="btn btn-default btn-sm"><i class="entypo-cancel-circled"></i></button>              
                        </div>
					</div>
				</div>
			</div>
		</div>';
	}else{
        $html = $form_group ? '<div class="form-group">' : '';
		$html .= '
		  <label for="lb'.$element_id.'" class="col-sm-1 control-label">'.$caption.':</label>
		  <div class="'.$coldivwidth.'">
			<div class="input-group">
				<span class="input-group-addon"><i class="entypo-user"></i></span>
				<input type="hidden" name="'.$element_id.'" id="'.$element_id.'" class="'.$class.'" placeholder="'.$placeholder.'" style="width: 100%;" />
				<div class="input-group-btn">
				  <button type="button" name="e2_cl'.$element_id.'" id="e2_cl'.$element_id.'" class="btn btn-primary btn-sm">'.$buttontitle.'</button>
				  <button id="e8_cl'.$element_id.'" name="e8_cl'.$element_id.'" type="button" class="btn btn-default btn-sm"><i class="entypo-cancel-circled"></i></button>              
				</div>
			</div>
		  </div>';
        $html .= $form_group ? '</div>' : '';
    }
	
    $result = <<<EOM
        jQuery(document).ready(function(){
            //console.log($data_json);
            jQuery("#$element_id").select2('data', $data_json).trigger("change");
            //*************************************************************************
            jQuery('#$element_id').select2({
                multiple: $ismultiple ,
                allowClear: true,
                formatResult: formatState,
                formatSelection: formatState,
                minimumInputLength: 2,
                maximumSelectionSize: $maximumSelectionSize,
                ajax: {
                    url: '$full_url',
                    dataType: 'json',
                    data: function (term, page) {
                        return {
                            q: term
                          };
                    },
                    results: function (data, page) {
                        return {                    
                            results: data
                        };
                    }
                }
            });
            //*************************************************************************
            jQuery('#$element_id').on('select2-selecting', function(e) { 
                var olduid = jQuery('#$idhidenuser').val();
                jQuery('#$idhidenuser').val(olduid + e.val  + ',');
            });            
            //*************************************************************************
            jQuery('#e8_cl$element_id').click(function() {
                jQuery('#$element_id').select2('val', '');
                jQuery('#$idhidenuser').val('').trigger("change");
            });
            //************************************************************************
            jQuery('#e2_cl$element_id').click(function() {
                javascript:jQuery.OpenModalSIMAD("$base_url/directorio_externo/consulta?opcion=$option&campoText=$element_id&campoId=$idhidenuser", "1280", "640");
            });
            //************************************************************************
            jQuery('#$element_id').select2('data', $data_json);
            //************************************************************************
            jQuery("#$element_id").on("change", function(e) {
                //console.log (jQuery('#$element_id').select2('data'));
                var idold = [];
                var cidold = [];
                jQuery.each(jQuery('#$element_id').select2('data'), function() {
                    idold.push(this.id);
                    cidold.push(this.cid);
                });
                //********************************************************************
                jQuery('#$idhidenuser').val(idold.join(",")).trigger("change");
                //********************************************************************
                if(jQuery("#$element_id").hasClass("strdestcomenv")){
                    if(jQuery('.tploptionset').length > 0){
                        if(cidold.length > 1){
                            if(jQuery('.tploptionset').bootstrapSwitch('isActive') == true){
                                jQuery('.tploptionset').bootstrapSwitch('setActive', false);
                                jQuery('.tploptionset').bootstrapSwitch('setState', false);
                                toastr.warning("No puede usar la opcion de radicar con un documento para enviar a varios destinatarios, la opcion se deshabilitara");
                            }else{
                                jQuery('.tploptionset').bootstrapSwitch('setActive', false);
                            }
                        }else{
                            jQuery('.tploptionset').bootstrapSwitch('setActive', true);
                        }
                    }
                }
            });
            //************************************************************************
        });
EOM;
    $html .= javascript_tag($result);
    return $html;
}