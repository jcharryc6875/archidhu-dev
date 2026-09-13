<?php 
use_helper('jQuery');

if($dependencia->getEsActual()){
	echo jq_link_to_remote(image_tag('simad/ico_check_green.png',
		array('id'=>"feedcheck",'alt'=>'Esta dependencia esta habilitada,clic para deshabilitarla','title'=>'Esta dependencia esta habilitada,clic para deshabilitarla','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
			'update'    => md5($dependencia->getPrimaryKey().$dependencia->getEntidadId()),
			'url'     => 'dependencia/validate?dependencia_id='.$dependencia->getPrimaryKey(),
			'failure' => "toastr.danger('Ocurrio un error realizando la actividad, Por favor intente de nuevo!');",
			//'success' => "toastr.success('La dependencia se deshabilito correctamente, desde ahora no se pueden crear registros con esta dependencia');"
		));
}else{
	echo jq_link_to_remote(image_tag('simad/ico_check_red.png',
		array('id'=>"feedcheck",'alt'=>'Esta dependencia esta deshabilitada,clic para habilitarla','title'=>'Esta dependencia esta deshabilitada,clic para habilitarla','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
			'update'    => md5($dependencia->getPrimaryKey().$dependencia->getEntidadId()),
			'url'     => 'dependencia/validate?dependencia_id='.$dependencia->getPrimaryKey(),
			'failure' => "toastr.danger('Ocurrio un error realizando la actividad, Por favor intente de nuevo!');",
			//'success' => "toastr.success('La dependencia se habilito correctamente, desde ahora se pueden crear registros con esta dependencia');"
		));
}
?>