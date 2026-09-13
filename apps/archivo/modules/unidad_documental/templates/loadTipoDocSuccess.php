
<?php 
use_helper('jQuery');
echo '<option value="" selected>Seleccione expediente...</option>';
foreach ($list_tipodocs as $tipo_documental) {
	echo '<option value="'.$tipo_documental->getPrimaryKey().'">'.$tipo_documental->getDescripcion().'</option>';
}
?>