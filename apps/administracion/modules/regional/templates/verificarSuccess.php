<?php use_helper('jQuery');?>
<?php if(trim($regional->getEsVisible())){ ?>
  <?php echo jq_link_to_remote(image_tag('simad/ico_check_green.png',
	array('id'=>"feedcheck",'alt'=>'La regional esta activa, haga clic para inactivar','title'=>'La regional esta activa, haga clic para inactivar','border'=>"0",'width'=>"35" ,'height'=>"35",'align'=>"middle")), array(
	'update'    => md5($regional->getPrimaryKey()),
	'url'     => 'regional/verificar?regional_id='.$regional->getPrimaryKey(),
	'failure' => "alert('Ocurrio un error durante el proceso, Por favor intente de nuevo!')",
  )) ?>
  <?php }else{ ?>
    <?php echo jq_link_to_remote(image_tag('simad/ico_check_red.png',
	array('id'=>"feeduncheck",'alt'=>'La regional esta inactiva, haga clic para activar','title'=>'La sociedad esta inactiva, haga clic para activar','border'=>"0",'width'=>"35" ,'height'=>"35",'align'=>"middle")), array(
	'update'    => md5($regional->getPrimaryKey()),
	'url'     => 'regional/verificar?regional_id='.$regional->getPrimaryKey(),
	'failure' => "alert('Ocurrio un error durante el proceso, Por favor intente de nuevo!')",
  )) ?>
<?php } ?>
                        