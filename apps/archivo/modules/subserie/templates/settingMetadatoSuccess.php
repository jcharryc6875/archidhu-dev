<?php 
	use_helper('jQuery');
	if($metadato->getEsObligatorio()){
		echo jq_link_to_remote(image_tag('simad/ico_check_green.png',
			array('id'=>"feedcheck",'alt'=>'Este metadato es obligatorio','title'=>'Este metadato es obligatorio','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
			'update'    => md5($metadato->getPrimaryKey()),
			'url'     => 'subserie/settingMetadato',
			'with'    => "'metadato_id=".$metadato->getPrimaryKey()."'",
			'failure' => "toastr.error('Ocurrio un error, Por favor intente de nuevo!')"
		));
	}else{
		echo jq_link_to_remote(image_tag('simad/ico_check_red.png',
			array('id'=>"feeduncheck",'alt'=>'Este metadato es opcional','title'=>'Este metadato es opcional','width'=>"25" ,'height'=>"25")), array(
			'update'    => md5($metadato->getPrimaryKey()),
			'url'     => 'subserie/settingMetadato',
			'with'    => "'metadato_id=".$metadato->getPrimaryKey()."'",
			'failure' => "toastr.error('Ocurrio un error, Por favor intente de nuevo!')"
		));
	}
?>