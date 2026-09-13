<?php 
use_helper('jQuery');

if($subserie->getEsVisible()){
	echo jq_link_to_remote(image_tag('simad/ico_check_green.png',
		array('id'=>"feedcheck",'alt'=>'Esta subserie esta habilitada,clic para deshabilitarla','title'=>'Esta subserie esta habilitada,clic para deshabilitarla','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
			'update'    => md5($subserie->getPrimaryKey().$subserie->getSerieId()),
			'url'     => 'subserie/validate?subserie_id='.$subserie->getPrimaryKey(),
			'failure' => "toastr.danger('Ocurrio un error realizando la actividad, Por favor intente de nuevo!');",
			//'success' => "toastr.success('La subserie se deshabilito correctamente, desde ahora no se pueden crear registros con esta subserie');"
		));
}else{
	echo jq_link_to_remote(image_tag('simad/ico_check_red.png',
		array('id'=>"feedcheck",'alt'=>'Esta subserie esta deshabilitada,clic para habilitarla','title'=>'Esta subserie esta deshabilitada,clic para habilitarla','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
			'update'    => md5($subserie->getPrimaryKey().$subserie->getSerieId()),
			'url'     => 'subserie/validate?subserie_id='.$subserie->getPrimaryKey(),
			'failure' => "toastr.danger('Ocurrio un error realizando la actividad, Por favor intente de nuevo!');",
			//'success' => "toastr.success('La subserie se habilito correctamente, desde ahora se pueden crear registros con esta subserie');"
		));
}
?>