<?php

/**
 * @author Javier Fernando Charry
 * @copyright 2017
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
 * 'values_cuid'        cargo usuario id for default values select
 * 'url'                solo url interna eje: usuario_firma/list
 */
 
function component_user_multiple($element_id, $idhidenuser,$idhiddencargo,$options = array())
{   $path_theme = sfConfig::get('theme_simad');
    $base_path = sfConfig::get('base_simad');
	$mod_comun = "/comun.php";
	$base_url = $base_path.$mod_comun;
    //***************************************************************************************
    if (!is_array($options)){
       $options = array($options);
    }
    //***************************************************************************************
    //echo count(preg_split("/[,]+/",$options['values_cuid'], null, PREG_SPLIT_NO_EMPTY));
    $caption = isset($options['caption']) ? $options['caption'] : 'Copia';
    $placeholder = isset($options['placeholder']) ? $options['placeholder'] : 'Digite nombre usuario';
    $class = isset($options['class']) ? $options['class'] : 'form-control input-sm sl';
    $option = isset($options['option']) ? $options['option'] : 0;
    $buttontitle = isset($options['buttontitle']) ? $options['buttontitle'] : "Buscar";
    $maximumSelectionSize = isset($options['maximumSelectionSize']) ? $options['maximumSelectionSize'] : -1;
    $ismultiple = isset($options['ismultiple']) ? $options['ismultiple'] : 1;
    $values_uid = isset($options['values']) ? preg_split("/[,]+/",$options['values'], -1, PREG_SPLIT_NO_EMPTY) : array();
    $values_text = isset($options['values_text']) ? preg_split("/[,]+/",$options['values_text'], -1, PREG_SPLIT_NO_EMPTY) : array();
    $values_cuid = isset($options['values_cuid']) ? preg_split("/[,]+/",$options['values_cuid'], -1, PREG_SPLIT_NO_EMPTY) : array();
    //$values = isset($options['values']) ? preg_split("/[,]+/",$options['values'], -1, PREG_SPLIT_NO_EMPTY) : array();
    $full_url = isset($options['url']) ? $base_url.'/'.$options['url'].'?opcion='.$option : $base_url.'/usuario_firma/selectUserActive?opcion='.$option;
    $coldivwidth = isset($options['coldivwidth']) ? $options['coldivwidth'] : "col-sm-8";
	$toptext = isset($options['toptext']) ? $options['toptext'] : 0;
    $autfirma = isset($options['autfirma']) ? '&autfirma='.$options['autfirma'] : '&autfirma=0';
    $modulo_id = isset($options['modulo_id']) ? '&modulo_id='.$options['modulo_id'] : '&modulo_id=0';
    //$full_url = isset($options['addquery']) ? $full_url.'&'.$options['addquery'] : $full_url;
    $addquery = isset($options['addquery']) ? '&'.$options['addquery'] : "";
    $form_group = isset($options['formgroup']) ? $options['formgroup'] : true;
    //$full_url = isset($options['bytipoprocesocom']) ? $full_url.'&bytipoprocesocom='.trim($options['bytipoprocesocom']) : $full_url;
    $bytipoprocesocom = isset($options['bytipoprocesocom']) ? '&bytipoprocesocom='.trim($options['bytipoprocesocom']) : "";
    $byfilterdep = isset($options['byfilterdep']) ? '&byfilterdep='.trim($options['byfilterdep']) : "";
    $advquery = $addquery.$bytipoprocesocom.$byfilterdep.$autfirma.$modulo_id;
    $full_url = isset($advquery) ? $full_url.$advquery : $full_url;
    //***************************************************************************************
    /*$serach_filter = sfContext::getInstance()->getUser()->getAttribute('form_option');
    if(md5('frmsearch1') == $serach_filter){
        $full_url = $full_url.'&'.base64_encode('frmsearch1').'='.$serach_filter;
        sfContext::getInstance()->getUser()->setAttribute('form_option',md5('none'));
    }*/
    //***************************************************************************************
    $term_list = null;
    for($i=0; $i < count($values_uid); $i++)
    {
        //$cargo_usuario = UsuarioPeer::getUserAndCargoById($values_cuid[$i]);
        $cargo_usuario = CargoUsuarioPeer::retrieveByPK($values_cuid[$i]);
        $cargo_text = trim($cargo_usuario) ? " - ".(trim($cargo_usuario->getCargo())) : "";
        
        if(!empty($cargo_usuario)){
			$img = sfConfig::get('base_simad').$cargo_usuario->getUsuario()->getRutaFoto() ? $cargo_usuario->getUsuario()->getRutaFoto() : sfConfig::get('base_simad').'/images/simad/ico_logo_users.png';
		}else{
			$img = sfConfig::get('base_simad').'/images/simad/ico_logo_users.png';
		}

        if(trim($values_uid[$i])){
            $term_list[] = array("id" => $values_uid[$i], "text" => ($values_text[$i]).$cargo_text, "cid" => (count($values_cuid) ? trim($values_cuid[$i]) : null), "img" => $img);
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
                var oldcuserid = jQuery('#$idhiddencargo').val();
                var olduid = jQuery('#$idhidenuser').val();
                jQuery('#$idhiddencargo').val(oldcuserid + e.choice.cid + ',');
                jQuery('#$idhidenuser').val(olduid + e.val  + ',');
            });            
            //*************************************************************************
            jQuery('#e8_cl$element_id').click(function() {
                jQuery('#$element_id').select2('val', '').trigger('change');
                jQuery('#$idhiddencargo').val('');
                jQuery('#$idhidenuser').val('');
            });
            //************************************************************************
            jQuery('#e2_cl$element_id').click(function() {
                javascript:jQuery.OpenModalSIMAD("$base_url/usuario_firma/consulta?opcion=$option&campoText=$element_id&campoId=$idhidenuser&cargoId=$idhiddencargo$advquery", "1280", "640");
            });
            //************************************************************************
            jQuery('#$element_id').select2('data', $data_json);
            //************************************************************************
            jQuery("#$element_id").on("change", function(e) {
                var idold = [];
                var cidold = [];
                jQuery.each(jQuery('#$element_id').select2('data'), function() {
                    idold.push(this.id);
                    cidold.push(this.cid);
                });
                //********************************************************************
                jQuery('#$idhidenuser').val(idold.join(","));
                jQuery('#$idhiddencargo').val(cidold.join(","));
                //********************************************************************
                if(jQuery("#$element_id").hasClass("strdestcom")){
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

/**
 * Returns a dropZone input tag that will update a DOM element '$element_id'
 * according to the '$options' passed.
 *
 * 'stored_input'       input hidden for stored values name files
 * Possible '$options' are: 
 * 'url'                una url que carga el archivo al servidor ejemplo => xxxx/dzFileUpload
 * 'values_text'        name files exist
 * 'autoProcessQueue'   autoProcessQueue
 * 'uploadMultiple'     uploadMultiple files exist
 * 'addRemoveLinks'     addRemoveLinks
 * 'thumbnailWidth'     thumbnailWidth
 * 'thumbnailHeight'    thumbnailHeight
 * 'parallelUploads'    parallelUploads
 * 'maxFiles'           maxFiles
 * 'dictResponseError'  dictResponseError
 * 'acceptedFiles'      acceptedFiles
 */
 
function component_upload_file($options = array(), $values_text = "", $stored_input = "ruta", $element_id = "myDrop")
{   $path_theme = sfConfig::get('theme_simad');
    $base_path = sfConfig::get('base_simad');
    $app_config = sfConfig::get('sf_config_dir').DIRECTORY_SEPARATOR."app.ini";
    //***************************************************************************************
    $acceptedFiles = null;
    if(file_exists($app_config)){
        $ini_array = parse_ini_file($app_config);
        $acceptedFiles = $ini_array['mtypes'];
    }
    //***************************************************************************************
    if (!is_array($options)){
       $options = array($options);
    }
    //***************************************************************************************    
    $values_text = isset($options['values_text']) ? preg_split("/[,]+/",$options['values_text'], -1, PREG_SPLIT_NO_EMPTY) : array();
    $url_upload = isset($options['url']) ? url_for(trim($options['url'])) : "";
    $autoProcessQueue = isset($options['autoProcessQueue']) ? $options['autoProcessQueue'] : "true";
    $uploadMultiple = isset($options['uploadMultiple']) ? $options['uploadMultiple'] : "false";
    $addRemoveLinks = isset($options['addRemoveLinks']) ? $options['addRemoveLinks'] : "true";
    $thumbnailWidth = isset($options['thumbnailWidth']) ? $options['thumbnailWidth'] : 50;
    $thumbnailHeight = isset($options['thumbnailHeight']) ? $options['thumbnailHeight'] : 50;
    $parallelUploads = isset($options['parallelUploads']) ? $options['parallelUploads'] : 100;
    $maxFiles = isset($options['maxFiles']) ? $options['maxFiles'] : 10;
    $dictResponseError = isset($options['dictResponseError']) ? $options['dictResponseError'] : "Ha ocurrido un error en el server";
    $acceptedFiles = isset($options['acceptedFiles']) ? $options['acceptedFiles'] : $acceptedFiles;
    //***************************************************************************************
    $term_list = array();
    $json_files = json_encode($term_list);
    $max_upload = ini_get('upload_max_filesize');
    $int_maxupload = preg_replace('/[^0-9]/', '', $max_upload);
    //***************************************************************************************
    $html = '
    <input type="hidden" id="'.$stored_input.'" name="'.$stored_input.'" value="'.implode(",",$values_text).'" />
    <div class="dropzone dz-clickable dz-default dz-file-preview" id="'.$element_id.'" multiple="multiple" >
        <div class="dz-message">
            <h2><i class="glyphicon glyphicon-cloud-upload"></i><br/>Arrastre archivos aqui!</h2>o haga clic para seleccionar ('.$max_upload.')
        </div>
    </div>';
    
    $result = <<<EOM
        jQuery(document).ready(function(){
            Dropzone.options.myAwesomeDropzone = false;
            Dropzone.autoDiscover = false;    
            var myDropzone = new Dropzone("div#$element_id", { 
                url: "$url_upload",
                // The configuration we've talked about above
                autoProcessQueue: $autoProcessQueue,       
                uploadMultiple: $uploadMultiple,
                addRemoveLinks: $addRemoveLinks,
                thumbnailWidth: $thumbnailWidth,
                thumbnailHeight: $thumbnailHeight,
                parallelUploads: $parallelUploads,
                maxFiles: $maxFiles,
                dictResponseError: "$dictResponseError",
                acceptedFiles: "$acceptedFiles",
                maxFilesize: $int_maxupload,
                init: function () {
                    this.on("success", function (file, response) {
                        jQuery(file.previewElement).find('[data-dz-name]').html(response.name);
                        var active_value = jQuery('#$stored_input').val() != "" ? jQuery('#$stored_input').val() + "," + response.name : response.name;
                        jQuery('#$stored_input').val(active_value);
                    });
                    this.on("removedfile", function(file) {
                        var obj = jQuery.parseJSON( file.xhr.response );
                        if(obj != null){                    
                            var active_new = [];
                            var active_files = jQuery('#$stored_input').val().split(",");
                            var index = 0;
                            for(i=0; i < active_files.length; i++){
                                if(obj.name != active_files[i]){
                                    active_new.push(active_files[i]);
                                }
                                index++;
                            }
                            jQuery('#$stored_input').val(active_new.join(","));
                        }
                    });
                }
            });
            //**********************************************************************
            //Add existing files into dropzone    
            var existingFiles = $json_files;    
            for (i = 0; i < existingFiles.length; i++) {
                myDropzone.emit("addedfile", existingFiles[i]);
                //myDropzone.emit("thumbnail", existingFiles[i], "/image/url");
                myDropzone.emit("complete", existingFiles[i]);                
            }
        });
EOM;
    $html .= javascript_tag($result);
    return $html;
}
?>