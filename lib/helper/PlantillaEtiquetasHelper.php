<?php

/**
 * Ayuda con las etiquetas que se pueden escribir en una plantilla Word de un modulo.
 * Pinta un boton discreto y, oculto, el contenido que el JS muestra como popover (simad-api.js).
 *
 * @param int $modulo_id ModulesEnable::ComInterna | ComEnviada | ActosAdministrativos
 * @return string HTML, o cadena vacia si el catalogo de variables no esta disponible
 */
function plantilla_etiquetas_ayuda($modulo_id)
{
  try {
    $grupos = PlantillaVariables::getAgrupadasByModulo($modulo_id);
  } catch (RuntimeException $e) {
    return '';
  }
  if (!count($grupos)) {
    return '';
  }
  $escape = function ($texto) {
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
  };
  $contenido_id = 'tplcom-ayuda-contenido-' . (int) $modulo_id;
  //*************************************************************************************
  $html = '<button type="button" class="btn btn-default btn-icon tplcom-ayuda-etiquetas" data-contenido="#' . $contenido_id . '" data-toggle="tooltip" data-original-title="Ver las etiquetas que puede usar en la plantilla">'
    . '<i class="entypo-info-circled"></i>Etiquetas disponibles</button>';
  //*************************************************************************************
  $html .= '<div id="' . $contenido_id . '" class="tplcom-oculto">';
  $html .= '<p class="tplcom-pop-intro">Escriba la etiqueta en el documento Word tal cual, con sus llaves y corchetes. '
    . 'Al firmar y radicar, el sistema la reemplaza por su valor. Las que no est&eacute;n en esta lista quedan sin reemplazar.</p>';
  $html .= '<div class="tplcom-pop-lista">';
  foreach ($grupos as $grupo => $variables) {
    $html .= '<div class="tplcom-pop-grupo">' . $escape($grupo) . '</div>';
    foreach ($variables as $variable) {
      $etiqueta = '{[' . $variable['clave'] . ']}';
      $html .= '<div class="tplcom-pop-item">'
        . '<button type="button" class="tplcom-copiar" data-etiqueta="' . $escape($etiqueta) . '" title="Copiar etiqueta" aria-label="Copiar etiqueta"><i class="entypo-docs"></i></button>'
        . '<code class="tplcom-etiqueta-clave">' . $escape($etiqueta) . '</code>'
        . '<span class="tplcom-etiqueta-desc">' . $escape($variable['descripcion']) . '</span>'
        . '</div>';
    }
  }
  $html .= '</div></div>';

  return $html;
}
