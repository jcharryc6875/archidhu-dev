<?php

/**
 * Subclass for representing a row from the 'tipo_com_recibida' table.
 *
 * 
 *
 * @package lib.model
 */ 
class TipoComRecibida extends BaseTipoComRecibida
{
	function __toString(){		
		return $this->getDescripcion(). ' - ' .$this->getDiasRespuesta(). ' dias respuesta';
	}

	function getDescripcionCompuesta(){		
		return $this->getDescripcion(). ' - ' .$this->getDiasRespuesta(). ' dias respuesta';
	}
	
	public function getTipoDocumentalAndCodigo(){
		if($this->getTipodocumentalId()){
			return $this->getTipoDocumental()->getCodigo() . ' / ' . $this->getTipoDocumental()->getDescripcion();
		}else{
			return "";
		}
	}
}
