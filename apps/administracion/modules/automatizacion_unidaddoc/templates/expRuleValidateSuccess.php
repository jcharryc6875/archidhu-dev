<?php 
use_helper('jQuery');

if($expediente_regla->getEstaActivo()){
    echo jq_link_to_remote(image_tag('simad/ico_check_green.png',array('width'=>"25" ,'height'=>"25",'align'=>"middle")), 
    array(
        'update'  => md5($expediente_regla->getPrimaryKey().$expediente_regla->getSubserieId()),
        'url'     => 'automatizacion_unidaddoc/expRuleValidate?expedientereglas_id='.$expediente_regla->getPrimaryKey(),
        'failure' => "toastr.danger('Ocurrio un error realizando la actividad, Por favor intente de nuevo!');",
    ),array('class'=>'tooltip-primary','data-toggle'=>'tooltip','data-original-title'=>'Esta subserie esta habilitada,clic para deshabilitarla'));
}else{
    echo jq_link_to_remote(image_tag('simad/ico_check_red.png', array('width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
        'update'  => md5($expediente_regla->getPrimaryKey().$expediente_regla->getSubserieId()),
        'url'     => 'automatizacion_unidaddoc/expRuleValidate?expedientereglas_id='.$expediente_regla->getPrimaryKey(),
        'failure' => "toastr.danger('Ocurrio un error realizando la actividad, Por favor intente de nuevo!');",
    ),array('class'=>'tooltip-primary','data-toggle'=>'tooltip','data-original-title'=>'Esta regla esta deshabilitada,clic para habilitarla'));
}
?>