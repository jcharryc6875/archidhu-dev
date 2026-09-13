<?php 
use_helper('jQuery');
if($servicio_asociado->getEstaActivo())
{
	echo jq_link_to_remote(image_tag('simad/ico_check_green.png',array('class'=>'tooltip-primary','data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title'=>'Clic para deshabilitarlo','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), 
	array(
		'url'     => 'tipo_documental/enableTipoServicio?unidaddocumentaltiposervicio_id='.$servicio_asociado->getPrimaryKey(),
		'failure' => "toastr.danger('Ocurrio un error realizando la actividad, Por favor intente de nuevo!');",
		'success' => "jQuery('#".md5($servicio_asociado->getPrimaryKey().$servicio_asociado->getUnidaddocumentalId())."').html(data);toastr.success('Este registro se deshabilitó');"
	));
}
else
{
	echo jq_link_to_remote(image_tag('simad/ico_check_red.png', array('class'=>'tooltip-primary','data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title'=>'Clic para habilitarlo','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), 
	array(
		'url'     => 'tipo_documental/enableTipoServicio?unidaddocumentaltiposervicio_id='.$servicio_asociado->getPrimaryKey(),
		'failure' => "toastr.danger('Ocurrio un error realizando la actividad, Por favor intente de nuevo!');",
		'success' => "jQuery('#".md5($servicio_asociado->getPrimaryKey().$servicio_asociado->getUnidaddocumentalId())."').html(data);toastr.success('Este registro se habilitó');"
	));
}
?>