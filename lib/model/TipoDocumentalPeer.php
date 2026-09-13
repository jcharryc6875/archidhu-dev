<?php

/**
 * Subclass for performing query and update operations on the 'tipo_documental' table.
 *
 * 
 *
 * @package lib.model
 */ 
class TipoDocumentalPeer extends BaseTipoDocumentalPeer
{

	public static function getTipoDocumentalCodigo($el_codigo, $subSerieId = 0)
	{
		$c = new Criteria();
		$c->add(TipoDocumentalPeer::CODIGO, trim($el_codigo));
		$c->add(TipoDocumentalPeer::SUBSERIE_ID, $subSerieId);
		return TipoDocumentalPeer::doSelectOne($c);
	}

	public static function getTipoDocIdAnexo($subserie_id)
	{
		try
		{
			if(empty(trim($subserie_id))) { return null; }
			//*******************************************************************
			$c = new Criteria();
			$c->add(TipoDocumentalPeer::SUBSERIE_ID, $subserie_id);
			$c->add(TipoDocumentalPeer::DESCRIPCION, 'Anexo');
			$tipoDocAnexo = TipoDocumentalPeer::doSelectOne($c);
			//*******************************************************************
			if(!empty($tipoDocAnexo)) 
			{
				return $tipoDocAnexo->getTipodocumentalId();
			}
			else
			{
				$d = new Criteria(); 
				$d->add(TipoDocumentalPeer::SUBSERIE_ID, $subserie_id);
				$d->addDescendingOrderByColumn(TipoDocumentalPeer::ORDEN);
				$lastoneTipoDoc = TipoDocumentalPeer::doSelectOne($d);

				if(!empty($lastoneTipoDoc))
				{
					$newOrder = $lastoneTipoDoc->getOrden() + 1;
					$antCodigo = $lastoneTipoDoc->getSubserie()->getCodigo();
					$newCodigo = $antCodigo . '-' . str_pad($newOrder, 3, '0', STR_PAD_LEFT);
					
					$anexo_tipoDoc = $lastoneTipoDoc->copy();
					$anexo_tipoDoc->setDescripcion('Anexo');
					$anexo_tipoDoc->setCodigo($newCodigo);
					$anexo_tipoDoc->setOrden($newOrder);
					$anexo_tipoDoc->save();

					return $anexo_tipoDoc->getTipodocumentalId();
				}
				return null;
			}
			return null;
		}		
		catch(PropelException $ex)
		{
			return null;
		}
		catch(\Exception $ex)
		{
			return null;
		}
		catch(\Throwable $ex)
		{
			return null;
		}
	}

	public static function getOrdenTipoDoc()
	{
		$c = new Criteria();
		$c->addAscendingOrderByColumn(TipoDocumentalPeer::DESCRIPCION);		
		$rs = TipoDocumentalPeer::doSelect($c);
		return $rs; 	
	}
	
	public static function getMaxOrderBySubserie($subserie_id)
	{
		try {
			$conexion = Propel::getConnection();
            //************************************************************************************
			$query = "SELECT MAX(%s) AS max_order FROM %s where %s=" . $subserie_id . "  and %s=";
            $query = sprintf($query, TipoDocumentalPeer::ORDEN, TipoDocumentalPeer::TABLE_NAME, TipoDocumentalPeer::SUBSERIE_ID);
			//************************************************************************************
			$sentencia = $conexion->prepare($query);
			$sentencia->execute();
			$resultset = $sentencia->fetch(PDO::FETCH_OBJ);
			return ($resultset->max_order + 1);
		} catch (PropelException $th) {
			return 1;
		} catch (\Exception $th) {
			return 1;
		} catch (\Throwable $th) {
			return 1;
		}
	}

	public static function createTipoDocumentalAnexo($params, $subserie_id, $descripcion) 
    {
        try 
        {
			if(empty(trim($params)) || empty(trim($subserie_id)) || empty(trim($descripcion))) { return null; }
            //****************************************************************************
            $c = new Criteria();
            $c->add(TipoDocumentalPeer::DESCRIPCION, 'anexos');
            $anexosCant = TipoDocumentalPeer::doCount($c);
			//****************************************************************************
			$subserie = SubseriePeer::retrieveByPk($subserie_id);
			$codigo_subserie = $subserie->getCodigo();
			//****************************************************************************
            if($anexosCant == 0)
            {
				$orden = TipoDocumentalPeer::getMaxOrderBySubserie( $subserie_id);
				$codigo = sprintf("%s-%s",$codigo_subserie,str_pad($orden,3,STR_PAD_LEFT));
				//************************************************************************
                $tipoDocumental = new TipoDocumental();
                $tipoDocumental->setSubserieId($subserie_id);
                $tipoDocumental->setCodigo($codigo);
                $tipoDocumental->setDescripcion($descripcion);
                $tipoDocumental->setOrden($orden);
                $tipoDocumental->setEsFormato(1);
				$tipoDocumental->setClasificacion($params['clasificacion']);
				$tipoDocumental->setSoporteunidaddocumentalId($params['soporteunidaddocumental_id']);
                $tipoDocumental->setCierraExpediente(0);
                $tipoDocumental->setEsVisible(1);
                $tipoDocumental->setEsObligatorio(0);
				//************************************************************************
                $tipoDocumental->save();
				//************************************************************************
                return array('error' => false, 'mensaje' => 'Tipo documental anexos creado exitosamente', 'tipo_documental' => $tipoDocumental);
            }
            else
            {
                return array('error' => true, 'mensaje' => 'Tipo documental anexos ya existe', 'tipo_documental' => null);
            }

        } 
        catch (PropelException $th) 
        {
            return array('error' => true, 'mensaje' => 'Error relacionado con la base de datos', 'tipo_documental' => null);
        } 
        catch (\Exception $th) 
        {
            return array('error' => true, 'mensaje' => 'Error en la aplicacion', 'tipo_documental' => null);
        } 
        catch (\Throwable $th) 
        {
            return array('error' => true, 'mensaje' => 'Error en el servidor', 'tipo_documental' => null);
        }
    }

	public static function getCountTipoDocRequiredFaltantes($subserie_id, $unidaddocumental_id, $tipodocumental_id_actual = null)
    {  
        try
        {
			$c = new Criteria();
			$c->add(TipoDocumentalPeer::SUBSERIE_ID, $subserie_id);
			$c->add(TipoDocumentalPeer::ES_OBLIGATORIO, 1);
			//**********************************************************************************************************
			$sql_custom = sprintf("%s NOT IN (",TipoDocumentalPeer::TIPODOCUMENTAL_ID);
			$sql_custom .= sprintf("SELECT DISTINCT %s FROM %s WHERE %s = %s)",ContenidoUnidadDocumentalPeer::TIPODOCUMENTAL_ID,
				ContenidoUnidadDocumentalPeer::TABLE_NAME,ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID,$unidaddocumental_id);

			if(!empty($tipodocumental_id_actual)){
				$cond1 = $c->getNewCriterion(TipoDocumentalPeer::TIPODOCUMENTAL_ID, $sql_custom, Criteria::CUSTOM);
				$cond2 = $c->getNewCriterion(TipoDocumentalPeer::TIPODOCUMENTAL_ID, $tipodocumental_id_actual, Criteria::NOT_EQUAL);
				$cond1->addAnd($cond2);
				$c->add($cond1);
			}else{
				$sql_aux = $c->getNewCriterion(TipoDocumentalPeer::TIPODOCUMENTAL_ID, $sql_custom, Criteria::CUSTOM);
				$c->add($sql_aux);
			}
			//**********************************************************************************************************
			$cantidadTiposFaltantes = TipoDocumentalPeer::doCount($c);
			//**********************************************************************************************************			
			return $cantidadTiposFaltantes > 0 ? 1 : 0;
        } 
        catch(PropelException $ex)
		{
			return 0;
		}
		catch(\Exception $ex)
		{
			return 0;
		}
        catch(\Throwable $ex)
		{
			return 0;
		}
    }

	public static function getCountTipoDocCloseExpFaltantes($subserie_id, $unidaddocumental_id, $tipodocumental_id_actual = null) 
    {  
        try
        {
			$c = new Criteria();
			$c->addJoin(TipoDocumentalPeer::TIPODOCUMENTAL_ID, ContenidoUnidadDocumentalPeer::TIPODOCUMENTAL_ID, Criteria::LEFT_JOIN);
			$c->add(TipoDocumentalPeer::SUBSERIE_ID, $subserie_id);
			$c->add(TipoDocumentalPeer::CIERRA_EXPEDIENTE, 1);
			//************************************************************************************ */
			if(!empty($tipodocumental_id_actual))
			{
				$c->add(TipoDocumentalPeer::TIPODOCUMENTAL_ID, $tipodocumental_id_actual, Criteria::NOT_EQUAL);
			}
			//************************************************************************************* */
			$c->add(ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID, $unidaddocumental_id);
			$cantidadTiposCierreExpediente = TipoDocumentalPeer::doCount($c);			
			return $cantidadTiposCierreExpediente > 0 ? 0 : 1;
        } 
        catch(PropelException $ex)
		{
			return "Error de acceso a la base de datos";
		}
		catch(\Exception $ex)
		{
			return "Error interno de la aplicacion";
		}
        catch(\Throwable $ex)
		{
			return "Error interno del servidor";
		}
    }

	public static function verifTipoDocCloseExp($subserie_id, $tipodocumental_id_actual = null) 
    {  
        try
        {
			$c = new Criteria();
			$c->add(TipoDocumentalPeer::SUBSERIE_ID, $subserie_id);
			$c->add(TipoDocumentalPeer::CIERRA_EXPEDIENTE, 1);
			$c->add(TipoDocumentalPeer::TIPODOCUMENTAL_ID, $tipodocumental_id_actual);

			$stmt = TipoDocumentalPeer::doCount($c);

			return empty($stmt) ? false : true;
        } 
        catch(PropelException $ex)
		{
			return false;
		}
		catch(\Exception $ex)
		{
			return false;
		}
        catch(\Throwable $ex)
		{
			return false;
		}
    }

	public static function verifTipoDocIsRequired($subserie_id, $tipodocumental_id_actual = null) 
    {  
        try
        {
			$c = new Criteria();
			$c->add(TipoDocumentalPeer::SUBSERIE_ID, $subserie_id);
			$c->add(TipoDocumentalPeer::ES_OBLIGATORIO, 1);
			$c->addSelectColumn(TipoDocumentalPeer::TIPODOCUMENTAL_ID);

			$stmt = TipoDocumentalPeer::doSelectStmt($c);
			$tipoDocumentalObligatoriosIds = array();
			while ($row = $stmt->fetch(PDO::FETCH_NUM)) 
			{
				$tipoDocumentalObligatoriosIds[] = $row[0];
			}

			if(in_array($tipodocumental_id_actual, $tipoDocumentalObligatoriosIds)) 
			{
				return true; 
			} 
			else 
			{
				return false;
			}

        } 
        catch(PropelException $ex)
		{
			return "Error de acceso a la base de datos";
		}
		catch(\Exception $ex)
		{
			return "Error interno de la aplicacion";
		}
        catch(\Throwable $ex)
		{
			return "Error interno del servidor";
		}
    }

	public static function getOrdenTipoDocumental()
	{
		$c = new Criteria();
		$c->setDistinct();
		$c->addAscendingOrderByColumn(TipoDocumentalPeer::DESCRIPCION);		
		$rs = TipoDocumentalPeer::doSelect($c);
		return $rs; 	
	}

	public static function getTipoDocListBySubserie($subserie_id = 0)
	{
		$c = new Criteria();
		$c->add(TipoDocumentalPeer::SUBSERIE_ID,$subserie_id);
		$c->addAscendingOrderByColumn(TipoDocumentalPeer::DESCRIPCION);		
		$rs = TipoDocumentalPeer::doSelect($c);
		return $rs; 	
	}

	public static function getTipoDocByCodigo($codigo = null)
	{
		$c = new Criteria();
		$c->add(TipoDocumentalPeer::CODIGO,$codigo);
		$rs = TipoDocumentalPeer::doSelectOne($c);
		return $rs; 	
	}

	public static function getTipoDocByNombreAndCodigo($codigo = null, $nombre = null)
    {
		if(empty(trim($codigo)) || empty(trim($nombre))) { return null; }
		//*******************************************************************
		$c = new Criteria();
        $c->add(TipoDocumentalPeer::CODIGO,$codigo);
        $c->add(TipoDocumentalPeer::DESCRIPCION,$nombre);
        $object = TipoDocumentalPeer::doSelectOne($c);
        //*******************************************************************
		return $object;
	}

	public static function guardarAuditoria($registro_anterior,$registro_nuevo)
    {
        try{ 
            $campos_objeto = TipoDocumentalPeer::getFieldNames();
            $valor_anterior = array();
            $valor_nuevo = array();

            foreach($campos_objeto as $field){
                $instanceMethod = 'get'.$field;
                $valor_anterior[] = !empty($registro_anterior) ? $registro_anterior->$instanceMethod() : null;
                $valor_nuevo[] = $registro_nuevo->$instanceMethod();
            }
            //**************************************************************************************
			AuditLogPeer::guardarAuditoriaLite(1,$campos_objeto,$valor_anterior,$valor_nuevo,$registro_nuevo->getCodigo());
        }catch (PropelException $ex){       
            $msg_error = $ex->getMessage();
        }catch (\Exception $ex){       
            $msg_error = $ex->getMessage();       
		}catch (\Throwable $ex){       
            $msg_error = $ex->getMessage();
        }
    }
}
