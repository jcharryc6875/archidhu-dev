<?php 
use_helper('jQuery');

if($serie->getEsVisible()){
	echo jq_link_to_remote(image_tag('simad/ico_check_green.png',
		array('id'=>"feedcheck",'alt'=>'Esta serie esta habilitada,clic para deshabilitarla','title'=>'Esta serie esta habilitada,clic para deshabilitarla','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
			'update'    => md5($serie->getPrimaryKey().$serie->getDependenciaId()),
			'url'     => 'serie/validate?serie_id='.$serie->getPrimaryKey(),
			'failure' => "toastr.danger('Ocurrio un error realizando la actividad, Por favor intente de nuevo!');",
			//'success' => "toastr.success('La serie se deshabilito correctamente, desde ahora no se pueden crear registros con esta serie');"
		));
}else{
	echo jq_link_to_remote(image_tag('simad/ico_check_red.png',
		array('id'=>"feedcheck",'alt'=>'Esta serie esta deshabilitada,clic para habilitarla','title'=>'Esta serie esta deshabilitada,clic para habilitarla','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
			'update'    => md5($serie->getPrimaryKey().$serie->getDependenciaId()),
			'url'     => 'serie/validate?serie_id='.$serie->getPrimaryKey(),
			'failure' => "toastr.danger('Ocurrio un error realizando la actividad, Por favor intente de nuevo!');",
			//'success' => "toastr.success('La serie se habilito correctamente, desde ahora se pueden crear registros con esta serie');"
		));
}
?>