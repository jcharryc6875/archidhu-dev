<?php if($directorio_select){ ?>
<?php use_helper('Object','jQuery') ?>
<?php if($option){ ?>
    <?php echo javascript_tag("
        jQuery('#funcionario_destino').val('".html_entity_decode($directorio_select->getFuncionario())."');
        jQuery('#cargo_destinatario').val('".html_entity_decode($directorio_select->getCargo())."');
        jQuery('#direccion_destinatario').val('".html_entity_decode($directorio_select->getDireccion())."');
    ")?>
<?php }else{ ?>
    <?php echo javascript_tag("
        jQuery('#prefijo').val('".html_entity_decode($directorio_select->getPrefijo())."');
        jQuery('#funcionario_destino').val('".html_entity_decode($directorio_select->getFuncionario())."');
        jQuery('#cargo_destinatario').val('".html_entity_decode($directorio_select->getCargo())."');
        jQuery('#direccion_destinatario').val('".html_entity_decode($directorio_select->getDireccion())."');
    ")?>
<?php } ?>
<?php } ?>
