<?php

/**
 * Subclass for performing query and update operations on the 'TRANSFERENCIA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class TransferenciaPeer extends BaseTransferenciaPeer
{
	public static function getTransByIdVinculada($vinculada_id,$estadotrans_id=1)
	{
		$s = new Criteria();
		$s->add(TransferenciaPeer::VINCULADA_ID, $vinculada_id);		
		$s->add(TransferenciaPeer::ESTADOTRANSFERENCIA_ID, $estadotrans_id);
		$transferencia = TransferenciaPeer::doSelectOne($s);
		//*******************************************************************************
		if($transferencia){
			return $transferencia;
		}else{
			return  null;
		}		
	}

	public static function getExpedienteByComRecibidaId($comrecibida_id,$estadotrans_id=2)
	{
		$s = new Criteria();
		$s->add(TransferenciaPeer::COMRECIBIDA_ID, $comrecibida_id);		
		$s->add(TransferenciaPeer::ESTADOTRANSFERENCIA_ID, $estadotrans_id);
		$transferencia = TransferenciaPeer::doSelectOne($s);
		//*******************************************************************************
		if($transferencia){
			return $transferencia->getUnidadDocumental();
		}else{
			return  null;
		}		
	}

	public static function createDefaultTransfer($values_info = array())
	{
		try {
			$transferencia = new Transferencia();
			$transferencia->setEstadotransferenciaId(isset($values_info['estadotrans_id']) ? trim($values_info['estadotrans_id']) : null);
			$transferencia->setUnidaddocumentalId(isset($values_info['unidaddocumental_id']) ? trim($values_info['unidaddocumental_id']) : null);
			$transferencia->setOrigentransferenciaid(isset($values_info['origentransferencia_id']) ? trim($values_info['origentransferencia_id']) : null);
			$transferencia->setComenviadaId(isset($values_info['comenviada_id']) ? trim($values_info['comenviada_id']) : null);
			$transferencia->setCominternaId(isset($values_info['cominterna_id']) ? trim($values_info['cominterna_id']) : null);
			$transferencia->setComrecibidaId(isset($values_info['comrecibida_id']) ? trim($values_info['comrecibida_id']) : null);
			$transferencia->setDocumentacionId(isset($values_info['documentacion_id']) ? trim($values_info['documentacion_id']) : null);
			$transferencia->setFacturaId(isset($values_info['factura_id']) ? trim($values_info['factura_id']) : null);
			$transferencia->setActoadministrativoId(isset($values_info['actoadministrativo_id']) ? trim($values_info['actoadministrativo_id']) : null);
			$transferencia->setTipodocumentalId(isset($values_info['tipodocumental_id']) ? trim($values_info['tipodocumental_id']) : null);
			$transferencia->setDestinotransferenciaId(isset($values_info['destinotransferencia_id']) ? trim($values_info['destinotransferencia_id']) : null);
			$transferencia->setFechaAceptacion(isset($values_info['fecha_acepta']) ? trim($values_info['fecha_acepta']) : null);
			$transferencia->setObservaciones(isset($values_info['observaciones']) ? trim($values_info['observaciones']) : null);
			$transferencia->setFechaCreacion(date('Y-m-d G:i:s'));
			$transferencia->save();
			//*******************************************************************************
			if($transferencia != null ){ return $transferencia; }else{ return null; }
		} catch (PropelException $th) {
			return null;
		} catch (\Exception $th) {
			return null;
		} catch (\Throwable $th) {
			return null;
		}
	}

	public static function createUserTransferencia(Transferencia $transferencia, $usuariotransfer_id, $rol_id = 2)
	{
		try {
			// USUARIO TRANSFERENCIA
			$user_transfer = new UsuarioTransferencia();
			$user_transfer->setRolusuariotransferenciaId($rol_id);
			$user_transfer->setTransferenciaId($transferencia->getPrimaryKey());
			$user_transfer->setUsuarioId($usuariotransfer_id);
			$user_transfer->save();
			//*******************************************************************************
			if($user_transfer != null ){ return $user_transfer; }else{ return null; }
		} catch (PropelException $th) {
			return null;
		} catch (\Exception $th) {
			return null;
		} catch (\Throwable $th) {
			return null;
		}
	}
	
	/**
     * objectActions::addNewTransferenciaAndContenido()
    * funcion para crear una transferencia automatica, crea el contenido documental
    * @return mixed resultado proceso array('isError' => true|false, 'message' => message)
    */
    public static function addAutoTransfAndContenido($unidaddocumental_id,$tipodocumental_id,$pkcom_id,$origentransfer_id,$usuariosolicita_id)
    {
        try {
            $unidad_documental = UnidadDocumentalPeer::retrieveByPK($unidaddocumental_id);
			if($unidad_documental == null){
				return array('isError' => true, 'message' => 'El expediente no existe, no se puede transferir la comunicación');
			}
            //***************************************************************************************************			
            $localizacionexp_id = $unidad_documental->getLocalizacionunidaddocumentalId();
            $fecha_acepta = null;
            $observaciones = "Transferencia automatica desde comunicaciones";
            //***************************************************************************************************
            if($localizacionexp_id == 1){
                $destinotransferencia_id = 1;
                $fecha_acepta = date('Y-m-d G:i:s');
                $estadotrans_id = 2;
            }elseif($localizacionexp_id == 2){
                $destinotransferencia_id = 2;
                $estadotrans_id = 1;
            }else{
                $destinotransferencia_id = 3;
                $estadotrans_id = 1;
            }
            //***************************************************************************************************
            if($unidad_documental != null){
                $data = array();
				//***********************************************************************************************
				if($origentransfer_id == 1)
					$data['cominterna_id'] = $pkcom_id;
				elseif($origentransfer_id == 2)
					$data['comrecibida_id'] = $pkcom_id;
				elseif($origentransfer_id == 3)
					$data['comenviada_id'] = $pkcom_id;
				elseif($origentransfer_id == 8)
					$data['actoadministrativo_id'] = $pkcom_id;
				else
					return array('isError' => true, 'message' => 'Ocurrio un error y no se pudo transferir la comunicación');
				//***********************************************************************************************
                $data['estadotrans_id'] = $estadotrans_id;
                $data['unidaddocumental_id'] = $unidad_documental->getPrimaryKey();
                $data['origentransferencia_id'] = $origentransfer_id;
                $data['tipodocumental_id'] = $tipodocumental_id;
                $data['destinotransferencia_id'] = $destinotransferencia_id;
                $data['fecha_acepta'] = $fecha_acepta;
                $data['observaciones'] = $observaciones;
                //***********************************************************************************************
                $transferencia = TransferenciaPeer::createDefaultTransfer($data);
                //***********************************************************************************************
                if($transferencia != null){
                    $transferencia_user = TransferenciaPeer::createUserTransferencia($transferencia,$usuariosolicita_id);
                    if($transferencia_user == null){
                        $transferencia->delete();
                        return null;
                    }
                    //*******************************************************************************************
                    $transferencia->transferirGestion($usuariosolicita_id);
                    return array('isError' => false, 'message' => 'Comunicación archivada correctamente');
                }else{
                    return array('isError' => true, 'message' => 'Ocurrio un error y no se realizo la transferencia de la comunicación');
                }
            }else{
                return array('isError' => true, 'message' => 'El expediente no es valido, no se puede transferir la comunicación');
            }
		} catch (PropelException $th) {
            return array('isError' => true, 'message' => $th->getMessage());
		} catch (\Exception $th) {
            return array('isError' => true, 'message' => $th->getMessage());
        } catch (\Throwable $th) {
            return array('isError' => true, 'message' => $th->getMessage());
        }
    }
}
