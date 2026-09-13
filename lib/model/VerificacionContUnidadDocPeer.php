<?php

/**
 * Subclass for performing query and update operations on the 'VERIFICACION_CONT_UNIDAD_DOC' table.
 *
 * 
 *
 * @package lib.model
 */ 
class VerificacionContUnidadDocPeer extends BaseVerificacionContUnidadDocPeer
{
    public static function getVerificacionContUnidadDocumentalDescripcion($la_descripcion)
	{
		$c = new Criteria();
		$c->add(VerificacionContUnidadDocPeer::DESCRIPCION, $la_descripcion);
		return VerificacionContUnidadDocPeer::doSelectOne($c);
	}
}
