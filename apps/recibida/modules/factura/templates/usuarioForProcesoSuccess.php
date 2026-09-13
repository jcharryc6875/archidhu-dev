<?php if($usuarios_procesos != null && count($usuarios_procesos)){ ?>
<select name="usuario_proceso" id="usuario_proceso" class="form-control input-sm required">
    <option value="">Seleccione...</option>
        <?php foreach($usuarios_procesos as $usuario):?>
        <option value="<?php  echo $usuario->getPrimaryKey()?>">
        <?php echo $usuario->getNombre()." ".$usuario->getApellido();?>
        </option>
    <?php endforeach; ?>
</select>
<?php }else{ ?>
<select name="usuario_selected" id="usuario_selected" disabled="disabled" class="form-control input-sm required">
    <option value="">Seleccione usuario...</option>
</select>
<?php } ?>