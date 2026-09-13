<?php use_helper('jQuery');?>
<?php if($object->getEnviarAlerta()){ ?>
  <?php echo jq_link_to_remote(image_tag('simad/bullet_green.png',
	array('id'=>"feedcheck", 'border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
	'update'    => md5('wf_detalles/enviarAlerta?wfactividadtransicionusuario_id'.$object->getPrimaryKey()),
    'url'     => 'wf_detalles/enviarAlerta?wfactividadtransicionusuario_id='.$object->getPrimaryKey(),
    'loading'  => "javascript:jQuery.LoadingStructData()",
    'complete'  => "javascript:jQuery.CloseLoadingStructData()",
	'failure' => "alert('Ocurrio un error durante la verificacion, Por favor intente de nuevo!')",
    'script' => true,	
    ),array('class'=>'tooltip-primary','data-toggle'=>'tooltip','data-original-title'=>'El envio de alertas para este usuario esta activo,clic para desactivar')) ?>
  <?php }else{ ?>
    <?php echo jq_link_to_remote(image_tag('simad/bullet_red.png',
	array('id'=>"feeduncheck",'border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
    'update'    => md5('wf_detalles/enviarAlerta?wfactividadtransicionusuario_id'.$object->getPrimaryKey()),
    'url'     => 'wf_detalles/enviarAlerta?wfactividadtransicionusuario_id='.$object->getPrimaryKey(),
    'loading'  => "javascript:jQuery.LoadingStructData()",
    'complete'  => "javascript:jQuery.CloseLoadingStructData()",
	'failure' => "alert('Ocurrio un error durante la verificacion, Por favor intente de nuevo!')",
    'script' => true,
    ),array('class'=>'tooltip-primary','data-toggle'=>'tooltip','data-original-title'=>'El envio de alertas para este usuario esta inactivo,clic para activar')) ?>
<?php } ?>