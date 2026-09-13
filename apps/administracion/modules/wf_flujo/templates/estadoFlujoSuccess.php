<?php use_helper('jQuery');?>
<?php if($wf_flujo->getEstaActivo() == 1){ ?>
  <?php echo jq_link_to_remote(image_tag('simad/ico_check_green.png',
	array('id'=>"feedcheck",'alt'=>'Este flujo esta habilitado','title'=>'El flujo esta habilitado','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
	'update'    => md5('wf_flujo/estadoFlujo?wf_flujo_id'.$wf_flujo->getPrimaryKey()),
	'url'     => 'wf_flujo/estadoFlujo?wf_flujo_id='.$wf_flujo->getPrimaryKey(),
	'failure' => "alert('Ocurrio un error realizando el proceso, Por favor intente de nuevo!')",
	//'complete' => "Element.hide('indicator_fondo');Element.hide('indicator');",
    )) ?>
  <?php }else{ ?>
    <?php echo jq_link_to_remote(image_tag('simad/ico_check_red.png',
	array('id'=>"feeduncheck",'alt'=>'Este flujo actualmente esta deshabilitado','title'=>'El flujo actualmente esta deshabilitado','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
	'update'    => md5('wf_flujo/estadoFlujo?wf_flujo_id'.$wf_flujo->getPrimaryKey()),
	'url'     => 'wf_flujo/estadoFlujo?wf_flujo_id='.$wf_flujo->getPrimaryKey(),
	'failure' => "alert('Ocurrio un error realizando el proceso, Por favor intente de nuevo!')",
    )) ?>
<?php } ?>