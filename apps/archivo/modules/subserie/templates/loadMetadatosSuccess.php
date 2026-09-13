<?php 
use_helper('jQuery');

foreach ($list_metadatos as $metadato) {
	$options = array('class' => 'form-control');
	$options['placeholder'] = mb_strtolower($metadato->getMetaDatos()->getDescripcion());
	$name_field = ucwords(mb_strtolower($metadato->getMetaDatos()->getNombre()));
	$name_input = md5($metadato->getMetaDatos()->getPrimaryKey().$metadato->getMetaDatos()->getNombre());

	if($isSearch){
		$options['class'] = $metadato->getMetaDatos()->getObligatorioConsulta() ? "form-control required" : "form-control";
		$label_text = $metadato->getMetaDatos()->getObligatorioConsulta() ? $name_field."<span class='ctrlreq'>(*)</span>" : $name_field;
	}else{
		$options['class'] = $metadato->getMetaDatos()->getEsObligatorio() ? "form-control required" : "form-control";
		$label_text = $metadato->getMetaDatos()->getEsObligatorio() ? $name_field."<span class='ctrlreq'>(*)</span>" : $name_field;
	}
	
	if($row_position == 'top'){
		echo '<div class="row dinamicallfields">';				
			if($metadato->getMetaDatos()->getTipodatoId() == 3){
				echo '<div class="col-sm-3">';
					echo '<div class="form-group">';
						echo '<label for="'.md5($metadato->getPrimaryKey()).'" class="control-label">'.$label_text.':</label>';
						echo '<div class="input-group">';
							$options['class'] = $options['class'].' input-sm datepicker';
							$options['data-format'] = 'yyyy-mm-dd';
							$options['placeholder'] = 'aaaa-mm-dd';
							echo input_tag($name_input, null, $options);
							echo '<div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>';
						echo '</div>';
					echo '</div>';
				echo '</div>';
			}else{
				echo '<div class="col-sm-5">';
					echo '<div class="form-group">';
						echo '<label for="'.md5($metadato->getPrimaryKey()).'" class="control-label">'.$label_text.':</label>';
						$metadato->getMetaDatos()->getRegex() ? $options['data-rule-pattern'] = $metadato->getMetaDatos()->getRegex() : '';
						$metadato->getMetaDatos()->getSize() ? $options['data-rule-maxlength'] = $metadato->getMetaDatos()->getSize() : '';
						echo input_tag($name_input, null, $options);
					echo '</div>';
				echo '</div>';
			}		
		echo '</div>';
	}else{
		echo '<div class="form-group">';				
			if($metadato->getMetaDatos()->getTipodatoId() == 3){
				echo '<label for="'.md5($metadato->getPrimaryKey()).'" class="col-sm-1 control-label">'.$label_text.':</label>';
				echo '<div class="col-sm-4">';
					echo '<div class="input-group">';
						$options['class'] = $options['class'].' input-sm datepicker';
						$options['data-format'] = 'yyyy-mm-dd';
						$options['placeholder'] = 'AAAA-MM-DD';
						echo input_tag($name_input, null, $options);
						echo '<div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>';
					echo '</div>';
				echo '</div>';
			}else{
				echo '<label for="'.md5($metadato->getPrimaryKey()).'" class="col-sm-1 control-label">'.$label_text.':</label>';
				echo '<div class="col-sm-4">';
					$metadato->getMetaDatos()->getRegex() ? $options['data-rule-pattern'] = $metadato->getMetaDatos()->getRegex() : '';
					$metadato->getMetaDatos()->getSize() ? $options['data-rule-maxlength'] = $metadato->getMetaDatos()->getSize() : '';
					echo input_tag($name_input, null, $options);
				echo '</div>';
			}		
		echo '</div>';
	}
}
?>