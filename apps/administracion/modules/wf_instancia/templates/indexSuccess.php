<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<link href="<?php echo $base_path; ?>/css/workflow.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="base" style="margin-top: 3%; margin-left: 25%;">
<a href="<?php echo $base_path; ?>/administracion.php/wf_estado/list" title="Ir a Estados de los  workflows"><div id="estado" class="ok"></div></a>
<a href="<?php echo $base_path; ?>/administracion.php/wf_actividad/list" title="Ir a Actividades de los  workflows"><div id="actividades" class="no"></div></a>
<a href="<?php echo $base_path; ?>/administracion.php/wf_flujo/list" title="Ir a los Worflows"><div id="workflow" class="ok"></div></a>
<a href="#" title="Ir a Permisos de los workflows"><div id="permisos" class="ok"></div></a>
<a href="<?php echo $base_path; ?>/administracion.php/wf_transicion/list" title="Ir a Acciones de los  workflows"><div id="acciones" class="ok"></div></a>
</div>
</body>
</html>
