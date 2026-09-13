<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

use_helper('jQuery');
?>
<?php if($subserie_por_usuario->getVisualizacion()){ ?>
    <?php echo jq_link_to_remote(image_tag($base_path.'/images/simad/bullet_green.png',
    array('id'=>"feedcheck",'alt'=>'eliminar permiso','title'=>'eliminar permiso','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
    'update'    => md5('consulta'.$subserie_por_usuario->getPrimaryKey()),
    'url'     => 'subserie_por_usuario/consultaPerm?subserieporusuario_id='.$subserie_por_usuario->getPrimaryKey(),		
    //'loading' => "Element.show('indicator_fondo');Element.show('indicator');",
    'failure' => "alert('Ocurrio un error al procesar la solicitud, Por favor intente de nuevo!')",
    //'complete' => "Element.hide('indicator_fondo');Element.hide('indicator');",
    )); ?>
<?php }else{ ?>
    <?php echo jq_link_to_remote(image_tag($base_path.'/images/simad/bullet_red.png',
    array('id'=>"feeduncheck",'alt'=>'agregar permiso','title'=>'asignar permiso','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
    'update'    => md5('consulta'.$subserie_por_usuario->getPrimaryKey()),
    'url'     => 'subserie_por_usuario/consultaPerm?subserieporusuario_id='.$subserie_por_usuario->getPrimaryKey(),		
    //'loading' => "Element.show('indicator_fondo');Element.show('indicator');",
    'failure' => "alert('Ocurrio un error al procesar la solicitud, Por favor intente de nuevo!')",
    //'complete' => "Element.hide('indicator_fondo');Element.hide('indicator');",
    )) ?>
<?php } ?>