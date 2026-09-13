<?php 
use_helper('jQuery');

if($tipo_documental->getEsObligatorio())
{
	echo jq_link_to_remote(image_tag('simad/ico_check_green.png',
		array('class'=>'tooltip-primary','data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title'=>'Este tipo documental es obligatorio,clic para deshabilitarlo','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), 
		array(
			//'update'  => md5($tipo_documental->getPrimaryKey().$tipo_documental->getSubserieId()),
			'url'     => 'tipo_documental/validate?tipodocumental_id='.$tipo_documental->getPrimaryKey(),
			'failure' => "toastr.danger('Ocurrio un error realizando la actividad, Por favor intente de nuevo!');",
			'success' => "jQuery('#".md5($tipo_documental->getPrimaryKey().$tipo_documental->getSubserieId())."').html(data);toastr.success('El tipo documental se configuro como un documento NO obligatorio, desde este momento no se solicitara el documento para cerrar el expediente');"
		));
}
else
{
	echo jq_link_to_remote(image_tag('simad/ico_check_red.png', 
		array('id'=>"feedcheck",'alt'=>'Este tipo documental no es obligatorio,clic para habilitarlo','title'=>'Este tipo documental no es obligatorio,clic para habilitarlo','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
			//'update'  => md5($tipo_documental->getPrimaryKey().$tipo_documental->getSubserieId()),
			'url'     => 'tipo_documental/validate?tipodocumental_id='.$tipo_documental->getPrimaryKey(),
			'failure' => "toastr.danger('Ocurrio un error realizando la actividad, Por favor intente de nuevo!');",
			'success' => "jQuery('#".md5($tipo_documental->getPrimaryKey().$tipo_documental->getSubserieId())."').html(data);toastr.success('El tipo documental se configuro como un documento obligatorio, desde este momento se debera crear este documento para poder cerrar los expedientes');"
		));
}
?>