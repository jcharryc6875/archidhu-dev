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
  }

  /**
   * Respuesta JSON paginada. No selecciona REQUEST_INFO/RESPONSE_INFO: pueden ser muy grandes.
   */
  public function executeList(sfWebRequest $request)
  {
    $this->verificaPrilegio('webservice_log/index');
    //*****************************************************************************
    $c = new Criteria();
    $c->addJoin(WebserviceLogPeer::USUARIO_ID, UsuarioPeer::USUARIO_ID);

    if ($metodo = trim($request->getParameter('nombre_metodo'))) {
      $c->add(WebserviceLogPeer::NOMBRE_METODO, '%' . $metodo . '%', Criteria::LIKE);
    }
    if ($operacion = trim($request->getParameter('tipo_operacion'))) {
      $c->add(WebserviceLogPeer::TIPO_OPERACION, '%' . $operacion . '%', Criteria::LIKE);
    }
    if ($usuario = trim($request->getParameter('user_name'))) {
      $c->add(UsuarioPeer::USER_NAME, '%' . $usuario . '%', Criteria::LIKE);
    }
    if ($request->getParameter('request_valid') !== null && $request->getParameter('request_valid') !== '') {
      $c->add(WebserviceLogPeer::REQUEST_VALID, (int) $request->getParameter('request_valid'));
    }
    // FECHA_CREACION es VARCHAR 'Y-m-d G:i:s' (hora sin cero a la izquierda): el límite final es el día siguiente
    $desde = $request->getParameter('fecha_inicial');
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $desde)) {
      $c->add(WebserviceLogPeer::FECHA_CREACION, $desde, Criteria::GREATER_EQUAL);
    }
    $hasta = $request->getParameter('fecha_final');
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $hasta)) {
      $c->addAnd(WebserviceLogPeer::FECHA_CREACION, date('Y-m-d', strtotime($hasta . ' +1 day')), Criteria::LESS_THAN);
    }
    //*****************************************************************************
    $total = WebserviceLogPeer::doCount($c);
    $page = max(1, (int) $request->getParameter('page', 1));

    $c->clearSelectColumns();
    $c->addSelectColumn(WebserviceLogPeer::WEBSERVICELOG_ID);
    $c->addSelectColumn(WebserviceLogPeer::FECHA_CREACION);
    $c->addSelectColumn(WebserviceLogPeer::NOMBRE_METODO);
    $c->addSelectColumn(UsuarioPeer::USER_NAME);
    $c->addSelectColumn(WebserviceLogPeer::REQUEST_VALID);
    $c->addSelectColumn(WebserviceLogPeer::TIPO_OPERACION);
    $c->addSelectColumn(WebserviceLogPeer::MENSAJE);
    $c->addDescendingOrderByColumn(WebserviceLogPeer::WEBSERVICELOG_ID);
    $c->setLimit(self::PAGE_SIZE);
    $c->setOffset(($page - 1) * self::PAGE_SIZE);

    $rows = array();
    $stmt = WebserviceLogPeer::doSelectStmt($c);
    while ($r = $stmt->fetch(PDO::FETCH_NUM)) {
      $rows[] = array(
        'id' => (int) $r[0],
        'fecha' => $r[1],
        'metodo' => $r[2],
        'usuario' => $r[3],
        'valido' => (int) $r[4],
        'operacion' => $r[5],
        'mensaje' => $r[6],
      );
    }

    $this->getResponse()->setContentType('application/json');
    return $this->renderText(json_encode(array(
      'total' => $total,
      'page' => $page,
      'pages' => (int) ceil($total / self::PAGE_SIZE),
      'rows' => $rows,
    )));
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
}
