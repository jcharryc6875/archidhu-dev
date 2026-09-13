<?php

/**
 * Subclass for representing a row from the 'PROB_APROBADO_PARA_CREACION' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProbAprobadoParaCreacion extends BaseProbAprobadoParaCreacion
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
