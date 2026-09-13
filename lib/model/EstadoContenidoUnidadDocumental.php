<?php

/**
 * Subclass for representing a row from the 'estado_contenido_unidad_documental' table.
 *
 * 
 *
 * @package lib.model
 */ 
class EstadoContenidoUnidadDocumental extends BaseEstadoContenidoUnidadDocumental
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
