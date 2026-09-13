<?php

/**
 * Subclass for performing query and update operations on the 'SERIE' table.
 *
 * 
 *
 * @package lib.model
 */ 
class SeriePeer extends BaseSeriePeer
{
	public static function guardarAuditoria($registro_anterior, $registro_nuevo)
    {
        try {
            $campos_objeto = SeriePeer::getFieldNames();
            $valor_anterior = array();
            $valor_nuevo = array();
            $usuario_conectado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
            //**************************************************************************************
            foreach ($campos_objeto as $field) {
                $instanceMethod = 'get' . $field;
                $valor_anterior[] = !empty($registro_anterior) ? $registro_anterior->$instanceMethod() : null;
                $valor_nuevo[] = $registro_nuevo->$instanceMethod();
            }
            //**************************************************************************************
            AuditLogPeer::guardarAuditoria(
                ModulesEnable::Seguridad,
                $campos_objeto,
                $valor_anterior,
                $valor_nuevo,
                $registro_nuevo->getCodigo(),
                $usuario_conectado,
                SeriePeer::getOMClass()
            );
        } catch (PropelException $ex) {
            $msg_error = $ex->getMessage();
        } catch (Exception $ex) {
            $msg_error = $ex->getMessage();
        }
    }

    public static function getSerieByNombreAndCodigo($codigo = null, $nombre = null)
    {
		if(empty(trim($codigo)) || empty(trim($nombre))) { return ""; }
		//*******************************************************************
		$c = new Criteria();
        $c->add(SeriePeer::CODIGO,$codigo);
        $c->add(SeriePeer::DESCRIPCION,$nombre);
        $object = SeriePeer::doSelectOne($c);
        //*******************************************************************
		return $object;
	}
}
