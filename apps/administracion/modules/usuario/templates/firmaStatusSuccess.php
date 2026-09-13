<?php
	use_helper('Object','jQuery');
    $usuario_id = $usuario->getPrimaryKey();
?>

<?php if($tipoconfig == 'firma_mecanica'){ ?>
    <a href="#" onclick="<?php echo jq_remote_function(array(
            'update' => 'uconfigmsing',
            'url' => url_for('/administracion.php/usuario/firmaStatus'),
            'with'    => "'usuario_id=".$usuario_id."&firmamecanica=1'",
        )) ?>">
        <?php if($sf_user->getAttribute('firma_mecanica', '', 'subscriber') == 0){ ?>
            <i class="entypo-check" style="background-color: red;"></i>
            <span>Habilitar Firma Mec&aacute;nica</span>
        <?php }else{ ?>
            <i class="entypo-check" style="background-color: green;"></i>
            <span>Deshabilitar Firma Mec&aacute;nica</span>
        <?php } ?>
    </a>
<?php }elseif($tipoconfig == 'firma_digital'){ ?>
    <a href="#" onclick="<?php echo jq_remote_function(array(
            'update' => 'uconfigesing',
            'url' => url_for('/administracion.php/usuario/firmaStatus'),
            'with'    => "'usuario_id=".$usuario_id."&firmadigital=1'",
        )) ?>">
        <?php if($sf_user->getAttribute('firma_digital', '', 'subscriber') == 0){ ?>
            <i class="entypo-check" style="background-color: red;"></i>
            <span>Habilitar Firma Digital</span>
        <?php }else{ ?>
            <i class="entypo-check" style="background-color: green;"></i>
            <span>Deshabilitar Firma Digital</span>
        <?php } ?>
    </a>
<?php }elseif($tipoconfig == 'firma_desatendida'){ ?>
    <a href="#" onclick="<?php echo jq_remote_function(array(
            'update' => 'uconfigsingdes',
            'url' => url_for('/administracion.php/usuario/firmaStatus'),
            'with'    => "'usuario_id=".$usuario_id."&firmadesatendida=1'",
        )) ?>">
        <?php if($sf_user->getAttribute('firma_desatendida', '', 'subscriber') == 0){ ?>
            <i class="entypo-check" style="background-color: red;"></i>
            <span>Habilitar Firma Desatendida</span>
        <?php }else{ ?>
            <i class="entypo-check" style="background-color: green;"></i>
            <span>Deshabilitar Firma Desatendida</span>
        <?php } ?>
    </a>
<?php } ?>