<?php use_helper('jQuery');?>
<?php if($contenido_unidad_documental->getVerificacioncontunidaddocId() == 1){ ?>
  <?php echo jq_link_to_remote(image_tag('simad/ico_check_green.png',
	array('id'=>"feedcheck",'alt'=>'Registro verificado','title'=>'Registro verificado','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
	'update'    => md5($contenido_unidad_documental->getPrimaryKey()),
	'url'     => 'contenido_documental/verificarContenido?contenidounidaddocumental_id='.$contenido_unidad_documental->getPrimaryKey(),
	//'confirm'  => 'Esta seguro de eliminar los contenidos marcados! \n Esta accion no se podra deshacer',
	//'loading' => "Element.show('indicator_fondo');Element.show('indicator');",
	//'success' => "simad_refresh();",
	'failure' => "alert('Ocurrio un error durante la verificacion, Por favor intente de nuevo!')",
	//'complete' => "Element.hide('indicator_fondo');Element.hide('indicator');",
    )) ?>
  <?php }else{ ?>
    <?php echo jq_link_to_remote(image_tag('simad/ico_check_red.png',
	array('id'=>"feeduncheck",'alt'=>'Registro no verificado','title'=>'Registro no verificado','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
	'update'    => md5($contenido_unidad_documental->getPrimaryKey()),
	'url'     => 'contenido_documental/verificarContenido?contenidounidaddocumental_id='.$contenido_unidad_documental->getPrimaryKey(),		
	//'loading' => "Element.show('indicator_fondo');Element.show('indicator');",
	'failure' => "alert('Ocurrio un error durante la verificacion, Por favor intente de nuevo!')",
	//'complete' => "Element.hide('indicator_fondo');Element.hide('indicator');",
    )) ?>
<?php } ?>