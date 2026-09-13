<?php

/**
 * Subclass for representing a row from the 'subserie' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Subserie extends BaseSubserie
{
	public function __toString(){
		return $this->getDescripcion().' / '.$this->getSerie().' / '.$this->getSerie()->getDependencia();
	}

	public function getCustomTrdDescription(){
		$custom_subserie = $this->getCodigo() . " - ".$this->getDescripcion();
		$custom_serie = $this->getSerie()->getCodigo() . " - ".$this->getSerie()->getDescripcion();
		$custom_dependencia = $this->getSerie()->getDependencia()->getCodigo() . " - ".$this->getSerie()->getDependencia()->getNombre();
		return sprintf("%s / %s / %s",$custom_subserie,$custom_serie,$custom_dependencia);
	}
}
