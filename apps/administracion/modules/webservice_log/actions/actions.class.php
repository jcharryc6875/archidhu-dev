<?php

/**
 * Consulta de solo lectura del log de servicios web (WEBSERVICE_LOG).
 *
 * @package    simad
 * @subpackage webservice_log
 */
class webservice_logActions extends sfActions
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
    $this->verificaPrilegio('webservice_log/index');
    $this->initPager($request);
  }

  /**
   * Listado paginado asíncrono: devuelve solo el HTML de la tabla y el paginador.
   */
  public function executeList(sfWebRequest $request)
  {
    $this->verificaPrilegio('webservice_log/index');
    $this->initPager($request);
    $this->setLayout(false);
  }

  /**
   * Descarga el request o el response de un log como archivo XML (o JSON si el contenido lo es).
   */
  public function executeDownload(sfWebRequest $request)
  {
    $this->verificaPrilegio('webservice_log/download');
    //*****************************************************************************
    $log = WebserviceLogPeer::retrieveByPk($request->getParameter('id'));
    $this->forward404Unless($log);
    $tipo = $request->getParameter('tipo') === 'response' ? 'response' : 'request';
    $contenido = (string) ($tipo === 'response' ? $log->getResponseInfo() : $log->getRequestInfo());
    // El cliente saliente guarda el XML escapado con htmlspecialchars()
    if (substr(ltrim($contenido), 0, 4) === '&lt;') {
      $contenido = htmlspecialchars_decode($contenido);
    }
    $esJson = in_array(substr(ltrim($contenido), 0, 1), array('{', '['), true);
    //*****************************************************************************
    $response = $this->getResponse();
    $response->setContentType($esJson ? 'application/json' : 'application/xml');
    $response->setHttpHeader('Content-Disposition', sprintf('attachment; filename="ws_log_%d_%s.%s"', $log->getPrimaryKey(), $tipo, $esJson ? 'json' : 'xml'));
    $this->setLayout(false);
    return $this->renderText($contenido);
  }

  private function initPager(sfWebRequest $request)
  {
    $c = new Criteria();
    $c->addJoin(WebserviceLogPeer::USUARIO_ID, UsuarioPeer::USUARIO_ID);
    $this->parametros = "&a=1";

    if ($metodo = trim($request->getParameter('nombre_metodo'))) {
      $c->add(WebserviceLogPeer::NOMBRE_METODO, '%' . $metodo . '%', Criteria::LIKE);
      $this->parametros .= "&nombre_metodo=" . urlencode($metodo);
    }
    if ($operacion = trim($request->getParameter('tipo_operacion'))) {
      $c->add(WebserviceLogPeer::TIPO_OPERACION, '%' . $operacion . '%', Criteria::LIKE);
      $this->parametros .= "&tipo_operacion=" . urlencode($operacion);
    }
    if ($usuario = trim($request->getParameter('user_name'))) {
      $c->add(UsuarioPeer::USER_NAME, '%' . $usuario . '%', Criteria::LIKE);
      $this->parametros .= "&user_name=" . urlencode($usuario);
    }
    $valido = $request->getParameter('request_valid');
    if ($valido === '0' || $valido === '1') {
      $c->add(WebserviceLogPeer::REQUEST_VALID, (int) $valido);
      $this->parametros .= "&request_valid=" . $valido;
    }
    // FECHA_CREACION es VARCHAR 'Y-m-d G:i:s' (hora sin cero a la izquierda): el límite final es el día siguiente
    $desde = $request->getParameter('fecha_inicial');
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $desde)) {
      $c->add(WebserviceLogPeer::FECHA_CREACION, $desde, Criteria::GREATER_EQUAL);
      $this->parametros .= "&fecha_inicial=" . $desde;
    }
    $hasta = $request->getParameter('fecha_final');
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $hasta)) {
      $c->addAnd(WebserviceLogPeer::FECHA_CREACION, date('Y-m-d', strtotime($hasta . ' +1 day')), Criteria::LESS_THAN);
      $this->parametros .= "&fecha_final=" . $hasta;
    }
    $c->addDescendingOrderByColumn(WebserviceLogPeer::WEBSERVICELOG_ID);

    $pager = new sfPropelPager('WebserviceLog', self::PAGE_SIZE);
    $pager->setPeerMethod('doSelectResumen');
    $pager->setCriteria($c);
    $pager->setPage($request->getParameter('page', 1));
    $pager->init();
    $this->pager = $pager;
  }
}
