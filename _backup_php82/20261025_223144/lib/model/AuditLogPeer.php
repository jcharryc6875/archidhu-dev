<?php

/**
 * Subclass for performing query and update operations on the 'AUDIT_LOG' table.
 *
 * 
 *
 * @package lib.model
 */ 
class AuditLogPeer extends BaseAuditLogPeer
{
    public static function guardarAuditoria($modulo, $campos, $valor_anterior, $valor_nuevo, $codigo_principal,$usuario_id,$object_class,$pkcomId = null, $toperation = null)
	{
		try {
            $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
            $usuario_id = !empty($usuario_id) ? $usuario_id : $usuariologuiado;
            //*************************************************************************************
			$audit_log = new AuditLog();
			$audit_log->setUsuarioId($usuario_id);
			$audit_log->setModuloId($modulo);
			//*************************************************************************************
			$campos_string = "";
			$valor_anterior_string = "";
			$valor_nuevo_string = "";
			$tipo_operacion = $toperation;
			//*************************************************************************************
			foreach ($campos as $campo) {
				$campos_string .= $campo . "|";
			}
			//*************************************************************************************
			foreach ($valor_anterior as $valor) {
				$valor_anterior_string .= $valor . "|";
			}
			//*************************************************************************************
			foreach ($valor_nuevo as $valor) {
				$valor_nuevo_string .= $valor . "|";
			}
			if (empty($tipo_operacion)) {
				$tipo_operacion = AuditLogPeer::getTipoOperacion($valor_anterior, $valor_nuevo);
			}
			//*************************************************************************************
			$client_agent = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "(none)";
			//*************************************************************************************
			$audit_log->setCampos($campos_string);
			$audit_log->setValorAnterior($valor_anterior_string);
			$audit_log->setValorNuevo($valor_nuevo_string);
			$audit_log->setFechaCreacion(date("Y-m-d G:i:s"));
			$audit_log->setCodigoPrincipal($codigo_principal);
			$audit_log->setClientIp(AuditLogPeer::getRealIP());
			$audit_log->setClientAgent($client_agent);
			$audit_log->setObjectClass($object_class);
			$audit_log->setConsecutivoId($pkcomId);
			$audit_log->setTipoOperacion($tipo_operacion);
			$audit_log->save();
			//******************************************************************
			AuditLogPeer::addDataHashLog($audit_log);
		} catch (Throwable $th) {
				$logfilename = sfConfig::get("sf_log_dir") . DIRECTORY_SEPARATOR . "access_app.log";
				simad_util::writetolog($logfilename, $th);
			}
		}

	/**
	 * Clasifica la operacion: edit (anterior y nuevo con datos), create (solo nuevo) o delete (solo anterior).
	 */
	public static function getTipoOperacion(array $valor_anterior, array $valor_nuevo)
	{
		$hay_anterior = count74(array_filter($valor_anterior, function ($v) { return !empty($v); })) > 0;
		$hay_nuevo = count74(array_filter($valor_nuevo, function ($v) { return !empty($v); })) > 0;
		if ($hay_anterior && !$hay_nuevo)
			return "delete";
		if (!$hay_anterior && $hay_nuevo)
			return "create";
		return "edit";
	}

    public static function guardarAuditoriaLite($object_class, $registro_anterior, $registro_nuevo, $modulo_id, $codigo_principal,$usuario_id = null, $toperation = null)
	{
		try {
			$peer_class = sprintf("%sPeer", $object_class);
			$campos_objeto = $peer_class::getFieldNames();
			$valor_anterior = array();
			$valor_nuevo = array();

			foreach ($campos_objeto as $field) {
				$instanceMethod = 'get' . $field;
				$valor_anterior[] = $registro_anterior != null ? $registro_anterior->$instanceMethod() : "";
				$valor_nuevo[] = $registro_nuevo != null ? $registro_nuevo->$instanceMethod() : "";
				
			}
			//**************************************************************************************
			// isNew() evita que los valores por defecto de un objeto vacio cuenten como datos
			if (empty($toperation)) {
				if ($registro_anterior == null || $registro_anterior->isNew())
					$toperation = "create";
				elseif ($registro_nuevo == null || $registro_nuevo->isNew() || $registro_nuevo->isDeleted())
					$toperation = "delete";
			}
			$registro_pk = $registro_nuevo != null ? $registro_nuevo : $registro_anterior;
			$pkObjectId = $registro_pk != null ? $registro_pk->getPrimaryKey() : null;
			AuditLogPeer::guardarAuditoria($modulo_id, $campos_objeto, $valor_anterior, $valor_nuevo, $codigo_principal,$usuario_id,$object_class,$pkObjectId, $toperation);
		} catch (Throwable $ex) {
			$msg_error = $ex->getMessage();
		}
	}
	
	public static function addDataHashLog(AuditLog $audit_log)
	{
		$dstorage = array();
		$object_class = "AuditLog";
		$field_exclude = array('HashLog'/*,'FuncEncryp'*/);
		try {
			$peer_class = sprintf("%sPeer", $object_class);
			$campos_objeto = $peer_class::getFieldNames();
			foreach ($campos_objeto as $field) {
				if (!in_array($field, $field_exclude)) {
					$instanceMethod = 'get' . $field;
					$dstorage[] = $audit_log->$instanceMethod();
				}
			}
		} catch (PropelException $ex) {
			$msg_error = $ex->getMessage();
		} catch (Exception $ex) {
			$msg_error = $ex->getMessage();
		}
		//********************************************************************
		$str_encrypt = implode("", $dstorage);
		$hashLog = hash('sha256', $str_encrypt);
		$audit_log->setHashLog($hashLog);
		$audit_log->save();
	}
	
    public static function getRealIP()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP']))
			return $_SERVER['HTTP_CLIENT_IP'];

		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
			return $_SERVER['HTTP_X_FORWARDED_FOR'];

		return $_SERVER['REMOTE_ADDR'];
	}

	public static function getClientAgent()
	{
		if (!empty($_SERVER['HTTP_USER_AGENT']))
			return $_SERVER['HTTP_USER_AGENT'];
	}
}
