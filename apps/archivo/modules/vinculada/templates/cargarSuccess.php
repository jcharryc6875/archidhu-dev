<?php
if($consulta != null)
{
    echo "<select id='tipo_documental' name='tipo_documental' class='form-control input-sm required'>";	
    $tipos = $consulta;
    foreach($tipos as $tipo){
    	echo "<option value='".$tipo->getTipodocumentalId()."'";
    	echo ">".ucwords(mb_strtolower($tipo->getDescripcion()))."</option>";
    }
    echo "</select>";
}else{ ?>
    <select name="tipo_documental" disabled="disabled" id="tipo_documental" class="form-control input-sm required">
        <option value="">Seleccione expediente...</option>
    </select>
<?php } ?>