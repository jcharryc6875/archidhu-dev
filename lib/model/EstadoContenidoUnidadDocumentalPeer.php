<?php

/**
 * Subclass for performing query and update operations on the 'ESTADO_CONTENIDO_UNIDAD_DOCUMENTAL' table.
 *
 * 
 *
 * @package lib.model
 */ 
class EstadoContenidoUnidadDocumentalPeer extends BaseEstadoContenidoUnidadDocumentalPeer
{
    public static function getEstadoContenidoUnidadDocumentalDescripcion($la_descripcion)
	{
		$c = new Criteria();
		$c->add(EstadoContenidoUnidadDocumentalPeer::DESCRIPCION, $la_descripcion);
		return EstadoContenidoUnidadDocumentalPeer::doSelectOne($c);
	}
}
