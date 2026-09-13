<?php use_helper('jQuery');?>
<?php if($servicio_anexos->getEsActual()){ ?>
		<?php echo jq_link_to_remote(image_tag('simad/ico_check_green.png',
			array('style'=>'vertical-align: text-top;','id'=>"feedcheck",'width'=>"25" ,'height'=>"25")), array(
			'update'    => md5($servicio_anexos->getFechaCreacion().$servicio_anexos->getPrimaryKey()),
			'url'     => 'servicio/viewOrHide?servicioanexo_id='.$servicio_anexos->getPrimaryKey(),
			'script' => true,
			'loading'  => 'javascript:jQuery.LoadingStructData();',
			'complete'  => "javascript:jQuery.CloseLoadingStructData();",
			'failure' => "alert('Ocurrio un error, Por favor intente de nuevo!')",
		),array('class'=>'tooltip-primary','data-toggle'=>'tooltip','data-original-title'=>"Esta imagen se encuentra activa")) ?>
  <?php }else{ ?>
		<?php echo jq_link_to_remote(image_tag('simad/ico_check_red.png',
			array('style'=>'vertical-align: text-top;','id'=>'feeduncheck','width'=>"25" ,'height'=>"25")), array(
			'update'    => md5($servicio_anexos->getFechaCreacion().$servicio_anexos->getPrimaryKey()),
			'url'     => 'servicio/viewOrHide?servicioanexo_id='.$servicio_anexos->getPrimaryKey(),
			'script' => true,
			'loading'  => 'javascript:jQuery.LoadingStructData();',
			'complete'  => "javascript:jQuery.CloseLoadingStructData();",
			'failure' => "alert('Ocurrio un error, Por favor intente de nuevo!')",
		),array('class'=>'tooltip-primary','data-toggle'=>'tooltip','data-original-title'=>"Esta imagen se encuentra inactiva")) ?>
<?php } ?>