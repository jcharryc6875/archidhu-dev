<?php	
echo "<select id='tipo_documental' name='tipo_documental'>";	
$tipos = $consulta;
foreach($tipos as $tipo){
	echo "<option value='".$tipo->getTipodocumentalId()."'";
	echo ">".ucwords(mb_strtolower($tipo->getDescripcion()))."</option>";
}
echo "</select>";
?>