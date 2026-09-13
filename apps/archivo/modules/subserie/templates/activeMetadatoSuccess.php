<?php 
	use_helper('jQuery');
	if($metadato->getEsActivo()){
		echo jq_link_to_remote(image_tag('simad/ico_check_green.png',
			array('id'=>"feedcheck",'alt'=>'Desactivar metadato','title'=>'Desactivar metadato','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
				'update'    => md5($metadato->getPrimaryKey().'activeMetadato'),
				'url'     => 'subserie/activeMetadato',
				'with'    => "'metadato_id=".$metadato->getPrimaryKey()."'",
				'failure' => "toastr.error('Ocurrio un error, Por favor intente de nuevo!')"
			));
	}else{
		echo jq_link_to_remote(image_tag('simad/ico_check_red.png',
			array('id'=>"feeduncheck",'alt'=>'Activar metadato','title'=>'Activar metadato','width'=>"25" ,'height'=>"25")), array(
				'update'    => md5($metadato->getPrimaryKey().'activeMetadato'),
				'url'     => 'subserie/activeMetadato',
				'with'    => "'metadato_id=".$metadato->getPrimaryKey()."'",
				'failure' => "toastr.error('Ocurrio un error, Por favor intente de nuevo!')"
			));
	}
?>