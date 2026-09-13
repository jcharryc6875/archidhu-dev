<?php

/**
 * Subclass for performing query and update operations on the 'COMRECIBIDA_USUARIO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ComrecibidaUsuarioPeer extends BaseComrecibidaUsuarioPeer
{
    public static function getCurrentListProcess($comrecibida_id)
    {
        try
        {
            $c = new Criteria();

            // SELECT DISTINCT con columnas específicas
            $c->setDistinct();
            $c->addSelectColumn(ComrecibidaUsuarioPeer::COMRECIBIDAUSUARIO_ID);
            $c->addSelectColumn(ComrecibidaUsuarioPeer::TIPOPROCESOCOM_ID);
            $c->addSelectColumn(ComrecibidaUsuarioPeer::ESTA_ASIGNADA);
            $c->addSelectColumn(ComrecibidaUsuarioPeer::FECHA_ASIGNA);
            $c->addSelectColumn(TipoProcesoComPeer::DESCRIPCION . ' AS PROCESO_ACTUAL');

            $c->addJoin(ComrecibidaUsuarioPeer::TIPOPROCESOCOM_ID, TipoProcesoComPeer::TIPOPROCESOCOM_ID, Criteria::INNER_JOIN);
            $c->add(ComrecibidaUsuarioPeer::COMRECIBIDA_ID, $comrecibida_id);
            $c->addAscendingOrderByColumn(ComrecibidaUsuarioPeer::COMRECIBIDAUSUARIO_ID);

            // Ejecutar la consulta
            $stmt = ComrecibidaUsuarioPeer::doSelectStmt($c);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            //************************************************************************************
            if($results != null)
            {
                return $results;
            }
            else
            {
                return null;
            }
                
        }
        catch(PropelException $ex)
        {
            return $ex->getMessage();
        }
        catch(\Exception $ex)
        {
            return $ex->getMessage();
        }
        catch(\Throwable $ex)
        {
            return $ex->getMessage();
        }
    }
    
    public static function getCurrentUserAsig($comrecibida_id = null)
    {
        $c = new Criteria();
        $c->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);
        $c->add(ComrecibidaUsuarioPeer::COMRECIBIDA_ID,$comrecibida_id);
        $com_object = ComrecibidaUsuarioPeer::doSelectOne($c);
    	//************************************************************************************
        if($com_object != null){
            return $com_object;
        }else{
            return null;
        }
    }

    public static function envioMasivoRespExterna($marcausuario_id = -1, $unidaddocumental_id = -1)
    {
        $log_dir = sfConfig::get('sf_log_dir').DIRECTORY_SEPARATOR.'enviadoslexcli.log';
	    $c = new Criteria();
        $c->add(ComRecibidaPeer::MARCA,$marcausuario_id);
        $marca  = ComRecibidaPeer::doSelect($c);
        simad_util::writetolog($log_dir,'Total registros => '.count($marca));
        foreach ($marca as $com_recibida) {
            $c2 = new Criteria();
            $c2->add(WebserviceLogPeer::TIPO_OPERACION,'%'.$com_recibida->getRadicado().'%',Criteria::LIKE);
            $c2->add(WebserviceLogPeer::NOMBRE_METODO,'InformacionRadicadoEntrada');
            $ws_log = WebserviceLogPeer::doSelectOne($c2);
			
			$enviarRespExt = true;
            if($ws_log != null){
                $isNotError = strpos($ws_log->getMensaje(), 'Enviado Exitoso');
                if ($isNotError !== false){
                    $codigo_envio = str_replace("Enviado Exitoso => ","",$ws_log->getMensaje());
                    simad_util::writetolog($log_dir,'Radicado '.trim($com_recibida->getRadicado()).' ya fue enviado ha LEX Codigo => '.$codigo_envio);
					$com_recibida->setResptaIntegracion($codigo_envio);
                    $com_recibida->setMarca(0);
                    $com_recibida->save();
					
					$enviarRespExt = false;
                }
			}
			//***************************************************************************************
            if($enviarRespExt){
				$marca_vinculacion = (int) $com_recibida->getMarcaVinculacion();
                $tipodocumental_id = 21676;
                $pkcomid = $com_recibida->getPrimaryKey();
                $origentransferencia_id = 2;
				$usuarioorigen_id = 4249;
                //$current_user = ComrecibidaUsuarioPeer::getCurrentUserAsig($pkcomid);
                //$usuarioorigen_id = $current_user != null ? $current_user->getUsuarioId() : $marcausuario_id;
                if(!$marca_vinculacion){
                    $response_transfer = TransferenciaPeer::addAutoTransfAndContenido($unidaddocumental_id,$tipodocumental_id,$pkcomid,$origentransferencia_id,$usuarioorigen_id);
                    $isTransfer = $response_transfer['isError'];
                }else{
                    $isTransfer = true;
                }
                
                if(!$isTransfer){
                    simad_util::writetolog($log_dir,'Radicado '.trim($com_recibida->getRadicado()).' enviado para transferencia');
                    
                    foreach(ComRecibidaPeer::getListIntersadosByComId($pkcomid) as $com_interesado){
                        UnidaddocumentalInteresadosPeer::addNewInteresadoByComId($unidaddocumental_id,$com_interesado->getInteresadoId());
                    }
                    
                    $response_data = $com_recibida->enviarRespuestaExterna();
                    if($response_data['status'] == 200){
                        simad_util::writetolog($log_dir,'Radicado '.trim($com_recibida->getRadicado()).' enviado respuesta externa');
                        $com_recibida->setMarca(0);
                        $com_recibida->save();
                    }else{
                        simad_util::writetolog($log_dir,'La comunicación con radicado'.trim($com_recibida->getRadicado()).' se archivo pero ocurrio un error al enviar para respuesta externa');
                    }
                }elseif($marca_vinculacion == 1){
                    $response_data = $com_recibida->enviarRespuestaExterna();
                    if($response_data['status'] == 200){
                        simad_util::writetolog($log_dir,'Radicado '.trim($com_recibida->getRadicado()).' enviado respuesta externa');
                        $com_recibida->setMarca(0);
                        $com_recibida->save();
                    }else{
                        simad_util::writetolog($log_dir,'La comunicación con radicado'.trim($com_recibida->getRadicado()).' se archivo pero ocurrio un error al enviar para respuesta externa');
                    }
                }else{
                    simad_util::writetolog($log_dir,'Ocurrio un error al al archivar el radicado => '.trim($com_recibida->getRadicado()));
                }
            }
        }
    }
}
