<?php

/**
 * @author 
 * @copyright 2008
 */

 
function show_modal_files($array_url, $img_name, $parametros = array(), $digit_com = 'ANEXO')
{
  $base_path = sfConfig::get('base_simad');
	$mod_comun = "/images/simad/";
  $ext = ".png";
	$img_src = $base_path . $mod_comun . $img_name . $ext;

  $ancho = isset($parametros['ancho']) ? $parametros['ancho'] : null ;
  $alto = isset($parametros['alto']) ? $parametros['alto'] : null ;
  $html = '';

  foreach($array_url as $row)
  {
      if($row['TIPO_ATTACHMENT'] == $digit_com)
      {
          if(!empty($ancho) || !empty($alto))
          {
            $js_modal = "javascript:jQuery.OpenModalSIMAD('" . trim($row['URL']) . "'," . $ancho. "," . $alto . ")";
          }
          else
          {
            $js_modal = "javascript:jQuery.OpenModalSIMAD('" . trim($row['URL']) . "')";
          }
  
          $html .= '
          <a class="tooltip-primary" data-toggle="tooltip" data-original-title="' . basename(trim($row['URL'])) . '" href="#" onclick="' . $js_modal .  '">
              <img src="' . $img_src . '" width="25" height="25" align="middle" /> 
          </a>';
      }
  }
  return $html;
}


function show_modal_single($url, $img_name, $parametros = array(), $tooltip = 'digitalizado')
{
  $base_path = sfConfig::get('base_simad');
	$mod_comun = "/images/simad/";
  $ext = ".png";
	$img_src = $base_path . $mod_comun . $img_name . $ext;

  $ancho = isset($parametros['ancho']) ? $parametros['ancho'] : null ;
  $alto = isset($parametros['alto']) ? $parametros['alto'] : null ;
  $endpoint = isset($parametros['endpoint']) ? 'data-endpoint = "'.url_for($parametros['endpoint']).'" ' : " ";
  $qthumb = isset($parametros['qthumb']) ? 'data-qthumb = "'.($parametros['qthumb']).'" ' : " ";
  $qsource = isset($parametros['qsource']) ? 'data-qsource = "'.($parametros['qsource']).'" ' : " ";
  $ndoc_text = isset($parametros['ndoc_text']) ? 'data-ndoc_text = "'.($parametros['ndoc_text']).'" ' : " ";

  $html = '';

  if(!empty($ancho) || !empty($alto))
  {
    $js_modal = "javascript:jQuery.OpenModalSIMAD('" . trim($url) . "'," . $ancho. "," . $alto . ")";
  }
  else
  {
    $js_modal = "javascript:jQuery.OpenModalSIMAD('" . trim($url) . "')";
  }

  $html = '<div id="thubmfixed">
  <a class="popover-toggle tooltip-primary gsthumbimg" data-toggle="tooltip" '.$endpoint.$qthumb.$qsource.$ndoc_text.'data-original-title="' . $tooltip . '" href="#" onclick="' . $js_modal .  '">
      <img src="' . $img_src . '" width="25" height="25" align="middle" /> 
  </a></div>';

  return $html; 
}

?>

