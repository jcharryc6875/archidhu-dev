<?php 
use_helper('jQuery');

if($cartelera_informativa->getEstadopublicacionId() == StatusPublicacion::Publicado)
{
    echo jq_link_to_remote(image_tag('simad/ico_check_green.png',array('id'=>"feedcheck",'alt'=>'Despublicar este registro','title'=>'Despublicar este registro','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), 
    array(
        'update'  => md5($cartelera_informativa->getPrimaryKey().$cartelera_informativa->getModuloId()),
        'url'     => 'cartelera_informativa/onPublish',
        'with'    => "'cartelerainformativa_id=".$cartelera_informativa->getPrimaryKey()."'",
        'failure' => "toastr.danger('Ocurrio un error realizando la actividad, Por favor intente de nuevo!');",
    ));
}
else
{
    echo jq_link_to_remote(image_tag('simad/ico_check_red.png', array('id'=>"feedcheck",'alt'=>'Publicar este registro nuevamente','title'=>'Publicar este registro nuevamente','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
        'update'  => md5($cartelera_informativa->getPrimaryKey().$cartelera_informativa->getModuloId()),
        'url'     => 'cartelera_informativa/onPublish',
        'with'    => "'cartelerainformativa_id=".$cartelera_informativa->getPrimaryKey()."'",
        'failure' => "toastr.danger('Ocurrio un error realizando la actividad, Por favor intente de nuevo!');",
    ));
}
?>