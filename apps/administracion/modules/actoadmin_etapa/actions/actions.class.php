<?php

/**
 * actoadmin_etapa actions.
 *
 * Administración de las etapas configurables del flujo de aprobación
 * de Actos Administrativos (UARIV-202605).
 *
 * @package    simad
 * @subpackage actoadmin_etapa
 */
class actoadmin_etapaActions extends sfActions
{
  const FORMA_ADMINISTRAR = 'ADMINISTRAR_FLUJO_ACTO_ADMINISTRATIVO';

  private function verificaPrivilegio()
  {
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    if (!$this->getUser()->checkPerm(self::FORMA_ADMINISTRAR, $usuariologuiado)) {
      $this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
    }
  }

  public function executeIndex()
  {
    $this->verificaPrivilegio();
    //*********************************************************************************************************
    $c = new Criteria();
    $c->addAscendingOrderByColumn(ActoadminEtapaPeer::ORDEN);
    $this->etapas = ActoadminEtapaPeer::doSelect($c);
  }

  public function executeCreate()
  {
    $this->verificaPrivilegio();
    $this->setTemplate('edit');
    $this->actoadmin_etapa = new ActoadminEtapa();
  }

  public function executeEdit($request)
  {
    $this->verificaPrivilegio();
    $this->forward404Unless($this->actoadmin_etapa = ActoadminEtapaPeer::retrieveByPk($request->getParameter('actoadminetapa_id')));
  }

  public function executeUpdate($request)
  {
    $this->verificaPrivilegio();
    $this->forward404Unless($request->isMethod(sfRequest::POST));
    //*********************************************************************************************************
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $ahora = date('Y-m-d H:i:s');
    //*********************************************************************************************************
    if (!$request->getParameter('actoadminetapa_id')) {
      $actoadmin_etapa = new ActoadminEtapa();
      $actoadmin_etapa->setFechaCreacion($ahora);
      $object_old = null;
      //*******************************************************************************************************
      $ultimoOrden = ActoadminEtapaPeer::doSelectOne((new Criteria())->addDescendingOrderByColumn(ActoadminEtapaPeer::ORDEN));
      $actoadmin_etapa->setOrden($ultimoOrden ? $ultimoOrden->getOrden() + 1 : 1);
    } else {
      $this->forward404Unless($actoadmin_etapa = ActoadminEtapaPeer::retrieveByPk($request->getParameter('actoadminetapa_id')));
      $object_old = clone $actoadmin_etapa;
    }
    //*********************************************************************************************************
    $actoadmin_etapa->setNombre(trim($request->getParameter('nombre')));
    $actoadmin_etapa->setRolusuarioactoadministvoId($request->getParameter('rolusuarioactoadministvo_id'));
    $actoadmin_etapa->setTagPrefijo(strtoupper(trim($request->getParameter('tag_prefijo'))));
    $actoadmin_etapa->setEstaActivo($request->getParameter('esta_activo') ? 1 : 0);
    $actoadmin_etapa->setFechaModificacion($ahora);
    $actoadmin_etapa->save();
    //*********************************************************************************************************
    AuditLogPeer::guardarAuditoriaLite('ActoadminEtapa', $object_old, $actoadmin_etapa, ModulesEnable::ActosAdministrativos, $actoadmin_etapa->getPrimaryKey(), $usuariologuiado);
    //*********************************************************************************************************
    $this->getUser()->setFlash('success', 'La etapa fue almacenada satisfactoriamente.');
    $this->redirect('actoadmin_etapa/index');
  }

  public function executeToggleActivo($request)
  {
    $this->verificaPrivilegio();
    $this->forward404Unless($request->isMethod(sfRequest::POST));
    $status = 400;
    //*********************************************************************************************************
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $actoadminetapa_id = $request->getParameter('actoadminetapa_id');
    $estaActivo = $request->getParameter('esta_activo');
    //*********************************************************************************************************
    try {
      $actoadmin_etapa = ActoadminEtapaPeer::retrieveByPk($actoadminetapa_id);
      if ($actoadmin_etapa) {
        $object_old = clone $actoadmin_etapa;
        $actoadmin_etapa->setEstaActivo($estaActivo == 'true' ? 1 : 0);
        $actoadmin_etapa->setFechaModificacion(date('Y-m-d H:i:s'));
        $actoadmin_etapa->save();
        AuditLogPeer::guardarAuditoriaLite('ActoadminEtapa', $object_old, $actoadmin_etapa, ModulesEnable::ActosAdministrativos, $actoadmin_etapa->getPrimaryKey(), $usuariologuiado);
        $status = 200;
      }
    } catch (PropelException $th) {
      $status = 400;
    } catch (\Exception $th) {
      $status = 400;
    }
    //*********************************************************************************************************
    http_response_code($status);
    exit;
  }

  public function executeReorder($request)
  {
    $this->verificaPrivilegio();
    $this->forward404Unless($request->isMethod(sfRequest::POST));
    $status = 400;
    //*********************************************************************************************************
    $ordenIds = $request->getParameter('orden_ids');
    //*********************************************************************************************************
    try {
      if (is_array($ordenIds) && count($ordenIds) > 0) {
        $con = Propel::getConnection(ActoadminEtapaPeer::DATABASE_NAME);
        $con->beginTransaction();
        try {
          $orden = 1;
          foreach ($ordenIds as $actoadminetapa_id) {
            $actoadmin_etapa = ActoadminEtapaPeer::retrieveByPk($actoadminetapa_id);
            if ($actoadmin_etapa) {
              $actoadmin_etapa->setOrden($orden);
              $actoadmin_etapa->setFechaModificacion(date('Y-m-d H:i:s'));
              $actoadmin_etapa->save();
            }
            $orden++;
          }
          $con->commit();
          $status = 200;
        } catch (Exception $e) {
          $con->rollBack();
          throw $e;
        }
      }
    } catch (PropelException $th) {
      $status = 400;
    } catch (\Exception $th) {
      $status = 400;
    }
    //*********************************************************************************************************
    http_response_code($status);
    exit;
  }

  public function executeConfiguracion()
  {
    $this->verificaPrivilegio();
    $this->actoadmin_configuracion = ActoadminConfiguracionPeer::getConfiguracionActual();
  }

  public function executeUpdateConfiguracion($request)
  {
    $this->verificaPrivilegio();
    $this->forward404Unless($request->isMethod(sfRequest::POST));
    //*********************************************************************************************************
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $dias_retencion = $request->getParameter('dias_retencion_borrador');
    //*********************************************************************************************************
    if($dias_retencion === null || !is_numeric($dias_retencion) || $dias_retencion < 0){
      $this->getUser()->setFlash('notice', 'Los días de retención deben ser un número mayor o igual a 0 (0 = nunca depurar).');
      $this->redirect('actoadmin_etapa/configuracion');
    }
    //*********************************************************************************************************
    $actoadmin_configuracion = ActoadminConfiguracionPeer::getConfiguracionActual();
    $actoadmin_configuracion->setDiasRetencionBorrador((int)$dias_retencion);
    $actoadmin_configuracion->setUsuarioId($usuariologuiado);
    $actoadmin_configuracion->setFechaModificacion(date('Y-m-d H:i:s'));
    $actoadmin_configuracion->save();
    //*********************************************************************************************************
    $this->getUser()->setFlash('success', 'La configuración fue almacenada satisfactoriamente.');
    $this->redirect('actoadmin_etapa/configuracion');
  }
}
