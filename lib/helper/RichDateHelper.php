<?php
 
use_helper('Form');
 
function rich_select_date_tag($name, $value = null, $options = array(), $html_options = array()) {
 
    $context = sfContext::getInstance();
  if (isset($options['culture']))
  {
    $culture = $options['culture'];
    unset($options['culture']);
  }
  else
  {
    $culture = $context->getUser()->getCulture();
  }
 
    // register our javascripts and stylesheets
  $langFile = '/sf/js/calendar/lang/calendar-'.strtolower(substr($culture, 0, 2));
  $jss = array(
    '/sf/js/calendar/calendar',
    is_readable(sfConfig::get('sf_symfony_data_dir').'/web/'.$langFile.'.js') ? $langFile : '/sf/js/calendar/lang/calendar-en',
    '/sf/js/calendar/calendar-setup',
  );
  foreach ($jss as $js)
  {
    $context->getResponse()->addJavascript($js);
  }
 
  $js = '
        function updateSelect(cal) {
            var date = cal.date;
            var selectMonth = document.getElementById("'.get_id_from_name($name).'_month");
            selectMonth.selectedIndex = date.getMonth();
            var selectDay = document.getElementById("'.get_id_from_name($name).'_day");
            selectDay.selectedIndex = (date.getDate() - 1);
            var selectYear = document.getElementById("'.get_id_from_name($name).'_year");
            selectYear.selectedIndex = (date.getFullYear() - '.$options['year_start'].');
        }
    document.getElementById("trigger_'.$name.'").disabled = false;
    Calendar.setup({
            inputField : "'.$name.'_rich_sel_date",
            ifFormat : "%Y-%m-%d",
            button : "trigger_'.$name.'",
            singleClick : true,
            onUpdate : updateSelect,
            showsTime : false,
            range : ['.$options['year_start'].', '.$options['year_end'].'],
            showOthers : false,
            cache : 1,
            weekNumbers : false,
            firstDay : 1
    });
  ';
 
    $html = select_date_tag($name, $value, $options, $html_options);
 
  // calendar button
  $calendar_button = '...';
  $calendar_button_type = 'txt';
  if (isset($options['calendar_button_img']))
  {
    $calendar_button = $options['calendar_button_img'];
    $calendar_button_type = 'img';
    unset($options['calendar_button_img']);
  }
  else if (isset($options['calendar_button_txt']))
  {
    $calendar_button = $options['calendar_button_txt'];
    $calendar_button_type = 'txt';
    unset($options['calendar_button_txt']);
  }
 
  if ($calendar_button_type == 'img')
  {
    $html .= image_tag($calendar_button, array('id' => 'trigger_'.$name, 'style' => 'cursor: pointer'));
  }
  else
  {
    $html .= content_tag('button', $calendar_button, array('type' => 'button', 'disabled' => 'disabled', 'onclick' => 'return false', 'id' => 'trigger_'.$name));
  }
 
  // add javascript
  $html .= content_tag('script', $js, array('type' => 'text/javascript'));
  $html .= '<div id="'.$name.'_rich_sel_date" style="display: inline;"></div>';
 
  return $html;
}