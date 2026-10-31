<?php

/**
 * Consulta de solo lectura de la auditoria de envios de correo (NOTIFICACION_ENVIO_LOG).
 *
 * @package    simad
 * @subpackage notificacion_envio_log
 */
class notificacion_envio_logActions extends sfActions
{
  const PAGE_SIZE = 25;

  public function preExecute()
  {
    if (!$this->getUser()->isAuthenticated()) {
      $this->redirect(sfConfig::get('base_simad') . "/backend.php/security/login");
    }
  }

  private function verificaPrilegio($currentForm)
  {
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    if (!$this->getUser()->checkPerm($currentForm, $usuariologuiado)) {
      $this->redirect(sfConfig::get('base_simad') . '/no_autorizado.html');
    }
  }

  public function executeIndex(sfWebRequest $request)
  {
    $this->verificaPrilegio('notificacion_envio_log/index');
    $this->initPager($request);
  }

  /**
   * Listado paginado asincrono: devuelve solo el HTML de la tabla y el paginador.
   */
  public function executeList(sfWebRequest $request)
  {
    $this->verificaPrilegio('notificacion_envio_log/index');
    $this->initPager($request);
    $this->setLayout(false);
  }

  private function initPager(sfWebRequest $request)
  {
    $this->cuentas = array();
    foreach (NotificacionCuentaPeer::doSelect(new Criteria()) as $cuenta) {
      $this->cuentas[$cuenta->getPrimaryKey()] = $cuenta->getNombre();
    }
    //*****************************************************************************
    $c = new Criteria();
    $this->parametros = "&a=1";

    $cuentaId = (int) $request->getParameter('cuenta_id');
    if ($cuentaId > 0) {
      $c->add(NotificacionEnvioLogPeer::NOTIFICACIONCUENTA_ID, $cuentaId);
      $this->parametros .= "&cuenta_id=" . $cuentaId;
    }
    $resultado = $request->getParameter('resultado');
    if ($resultado === NotificacionEnvioLogger::RESULTADO_OK || $resultado === NotificacionEnvioLogger::RESULTADO_ERROR) {
      $c->add(NotificacionEnvioLogPeer::RESULTADO, $resultado);
      $this->parametros .= "&resultado=" . $resultado;
    }
    if ($destino = trim($request->getParameter('destinatario'))) {
      $c->add(NotificacionEnvioLogPeer::DESTINATARIOS, '%' . $destino . '%', Criteria::LIKE);
      $this->parametros .= "&destinatario=" . urlencode($destino);
    }
    $desde = $request->getParameter('fecha_inicial');
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $desde)) {
      $c->add(NotificacionEnvioLogPeer::FECHA, $desde . ' 00:00:00', Criteria::GREATER_EQUAL);
      $this->parametros .= "&fecha_inicial=" . $desde;
    }
    $hasta = $request->getParameter('fecha_final');
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $hasta)) {
      $c->addAnd(NotificacionEnvioLogPeer::FECHA, date('Y-m-d', strtotime($hasta . ' +1 day')) . ' 00:00:00', Criteria::LESS_THAN);
      $this->parametros .= "&fecha_final=" . $hasta;
    }
    $c->addDescendingOrderByColumn(NotificacionEnvioLogPeer::NOTIFICACIONENVIOLOG_ID);

    $pager = new sfPropelPager('NotificacionEnvioLog', self::PAGE_SIZE);
    $pager->setCriteria($c);
    $pager->setPage($request->getParameter('page', 1));
    $pager->init();
    $this->pager = $pager;
  }
}
