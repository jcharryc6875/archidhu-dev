<?php
$web_dir = sfConfig::get('sf_web_dir');
 $style_menu = 2;
/*$style_menu = 1; */

if($style_menu == 1)
{
	$style_name = 'horizontal';
}
else if ($style_menu == 2)
{
	$style_name = 'aurea';
}
else
{
	$style_name = 'vertical';
}


include_once($web_dir.'/theme/neon/template/layout-'.$style_name.'.inc.php');

?>