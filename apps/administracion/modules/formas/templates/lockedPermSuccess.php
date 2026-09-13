<?php
use_helper('Object','jQuery');
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
?>
<?php if($forma->getIsPublic()){ ?>
      <?php echo jq_link_to_remote(image_tag($base_path.'/images/simad/ico_perm-unlocked.png',
    	array('id'=>"feedcheck",'alt'=>'Este privilegio es publico','title'=>'Este privilegio es publico','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
    	'update'    => md5($forma->getPrimaryKey()),
    	'url'     => 'formas/lockedPerm?forma_id='.$forma->getPrimaryKey(),		
    	//'loading' => "Element.show('indicator_fondo');Element.show('indicator');",
    	'failure' => "alert('Ocurrio un error al bloquear el permiso, Por favor intente de nuevo!')",
    	//'complete' => "Element.hide('indicator_fondo');Element.hide('indicator');",
     )) ?>
     <?php }else{ ?>
        <?php echo jq_link_to_remote(image_tag($base_path.'/images/simad/ico_perm-locked.png',
    	array('id'=>"feeduncheck",'alt'=>'Este privilegio es privado','title'=>'Este privilegio es privado','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
    	'update'    => md5($forma->getPrimaryKey()),
    	'url'     => 'formas/lockedPerm?forma_id='.$forma->getPrimaryKey(),		
    	//'loading' => "Element.show('indicator_fondo');Element.show('indicator');",
    	'failure' => "alert('Ocurrio un error al desbloquear el permiso, Por favor intente de nuevo!')",
    	//'complete' => "Element.hide('indicator_fondo');Element.hide('indicator');",
     )) ?>
<?php } ?>