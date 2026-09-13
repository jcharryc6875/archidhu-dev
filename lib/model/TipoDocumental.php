<?php

/**
 * Subclass for representing a row from the 'tipo_documental' table.
 *
 * 
 *
 * @package lib.model
 */ 
class TipoDocumental extends BaseTipoDocumental
{
	public function __toString(){
		// Solo modifica la salida si estamos en el backend
		if (sfContext::hasInstance()) {
			$module = sfContext::getInstance()->getModuleName();
			$app = sfConfig::get('sf_app');
			
			// Cambia según tu nombre de app/module si es distinto
			if ($app === 'backend' && in_array($module, ['tipo_correspondencia_recibida'])) {
				return $this->getTipoDocumentalAndCodigo();
			}
		}

		return $this->getDescripcion();
	}
	
	public function getTipoDocumentalAndCodigo(){
		return sprintf("%s / %s", $this->getCodigo(), $this->getDescripcion());
	}
}
