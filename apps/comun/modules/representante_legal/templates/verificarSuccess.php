<?php use_helper('jQuery');?>
<?php if($es_actual == 1){ ?>
  <?php echo jq_link_to_remote(image_tag('simad/ico_check_green.png',
	array('id'=>"feedcheck",'alt'=>'Este es el representante legal actual para el interesado','title'=>'Este es el representante legal actual para el interesado','width'=>"25")), array(
	'update'    => md5($representante->getPrimaryKey()),
	'url'     => 'representante_legal/uncheckObject?representantelegal_id='.$representante->getPrimaryKey().'&interesado_id='.$interesado_id,
	'failure' => "alert('Ocurrio un error durante la ejecución, Por favor intente de nuevo!')",
    )) ?>
  <?php }else{ ?>
    <?php echo jq_link_to_remote(image_tag('simad/ico_check_red.png',
	array('id'=>"feeduncheck",'alt'=>'Establecer que este sea el representante legal para el interesado','title'=>'Establecer que este sea el representante legal para el interesado','width'=>"25" ,'height'=>"25")), array(
	'update'    => md5($representante->getPrimaryKey()),
	'url'     => 'representante_legal/checkObject?representantelegal_id='.$representante->getPrimaryKey().'&interesado_id='.$interesado_id,
	'failure' => "alert('Ocurrio un error durante la ejecución, Por favor intente de nuevo!')",
    )) ?>
<?php } ?>