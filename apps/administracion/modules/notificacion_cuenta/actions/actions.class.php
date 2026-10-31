<?php

/**
 * Administracion de las cuentas remitentes de notificaciones por correo (NOTIFICACION_CUENTA):
 * alta y edicion, activar o desactivar, cuenta principal y prueba del servicio.
 *
 * @package    simad
 * @subpackage notificacion_cuenta
 */
class notificacion_cuentaActions extends sfActions
{
  const SECRETO_MASCARA = '********';

  public function preExecute()
  {
    if (!$this->getUser()->isAuthenticated()) {
      $this->redirect(sfConfig::get('base_simad') . "/backend.php/security/login");
    }
  }

  private function tienePrivilegio($currentForm)
  {
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    return $this->getUser()->checkPerm($currentForm, $usuariologuiado);
  }

  private function verificaPrilegio($currentForm)
  {
    if (!$this->tienePrivilegio($currentForm)) {
      $this->redirect(sfConfig::get('base_simad') . '/no_autorizado.html');
    }
  }

  private function renderJson(array $data)
  {
    $this->getResponse()->setContentType('application/json');
    return $this->renderText(json_encode($data));
  }

  /**
   * Respuesta JSON de las acciones AJAX; null si la peticion es valida y el usuario tiene el permiso.
   */
  private function validarAjax(sfWebRequest $request, $form)
  {
    if (!$request->isMethod(sfRequest::POST)) {
      return $this->renderJson(array('ok' => false, 'message' => 'Metodo no permitido'));
    }
    if (!$this->tienePrivilegio($form)) {
      return $this->renderJson(array('ok' => false, 'message' => 'La funcionalidad esta restringida por privilegios de administrador'));
    }
    return null;
  }

  public function executeIndex(sfWebRequest $request)
  {
    $this->verificaPrilegio('notificacion_cuenta/index');
    //*****************************************************************************
    $this->cuentas = NotificacionCuentaQuery::create()
      ->orderByEsPrincipal(Criteria::DESC)
      ->orderByActivo(Criteria::DESC)
      ->orderByPrioridad(Criteria::ASC)
      ->orderByNotificacioncuentaId(Criteria::ASC)
      ->find();
    $this->puedeEditar = $this->tienePrivilegio('notificacion_cuenta/save');
    $this->puedeProbar = $this->tienePrivilegio('notificacion_cuenta/test');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->verificaPrilegio('notificacion_cuenta/save');
    //*****************************************************************************
    $id = (int) $request->getParameter('id');
    if ($id > 0) {
      $this->cuenta = NotificacionCuentaPeer::retrieveByPk($id);
      $this->forward404Unless($this->cuenta);
    } else {
      $this->cuenta = $this->nuevaCuenta();
    }
  }

  public function executeSave(sfWebRequest $request)
  {
    $this->verificaPrilegio('notificacion_cuenta/save');
    $this->forward404Unless($request->isMethod(sfRequest::POST));
    //*****************************************************************************
    $id = (int) $request->getParameter('id');
    $cuenta = $id > 0 ? NotificacionCuentaPeer::retrieveByPk($id) : $this->nuevaCuenta();
    $this->forward404Unless($cuenta);
    $anterior = clone $cuenta;
    $reiniciarEstado = $this->aplicarFormulario($cuenta, $request);
    //*****************************************************************************
    $error = $this->validarCuenta($cuenta, $request);
    if ($error !== null) {
      $this->cuenta = $cuenta;
      $this->getUser()->setFlash('message_error', $error);
      $this->setTemplate('edit');
      return sfView::SUCCESS;
    }
    //*****************************************************************************
    if ($cuenta->isNew()) {
      $cuenta->setFechaCreacion(date('Y-m-d H:i:s'));
      // La primera cuenta registrada queda como principal.
      $cuenta->setEsPrincipal(NotificacionCuentaQuery::create()->count() == 0 ? 1 : 0);
    } elseif ($reiniciarEstado) {
      // Cambiaron datos de conexion: el estado anterior ya no es valido hasta volver a probar.
      $cuenta->setEstadoOperativo(NotificacionCuentaResolver::ESTADO_SIN_PROBAR);
      $cuenta->setUltimoError(null);
    }
    $cuenta->save();
    NotificacionCuentaResolver::invalidarCache();
    $this->auditar($anterior, $cuenta);
    //*****************************************************************************
    $this->getUser()->setFlash('message_success', 'La cuenta se guardo correctamente. Pruebe el servicio antes de activarla.');
    $this->redirect('notificacion_cuenta/edit?id=' . $cuenta->getPrimaryKey());
  }

  public function executeToggleActivo(sfWebRequest $request)
  {
    if (($respuesta = $this->validarAjax($request, 'notificacion_cuenta/save')) !== null) {
      return $respuesta;
    }
    //*****************************************************************************
    $cuenta = NotificacionCuentaPeer::retrieveByPk((int) $request->getParameter('id'));
    if (!$cuenta) {
      return $this->renderJson(array('ok' => false, 'message' => 'La cuenta no existe'));
    }
    $activar = !$cuenta->getActivo();
    if (!$activar && $cuenta->getEsPrincipal()) {
      return $this->renderJson(array('ok' => false, 'message' => 'La cuenta principal no se puede desactivar: defina otra cuenta como principal primero'));
    }
    if ($activar && $cuenta->getEstadoOperativo() === NotificacionCuentaResolver::ESTADO_SIN_PROBAR) {
      return $this->renderJson(array('ok' => false, 'message' => 'Pruebe la conexion de la cuenta antes de activarla'));
    }
    //*****************************************************************************
    $anterior = clone $cuenta;
    $cuenta->setActivo($activar ? 1 : 0);
    $cuenta->save();
    NotificacionCuentaResolver::invalidarCache();
    $this->auditar($anterior, $cuenta);
    return $this->renderJson(array('ok' => true, 'message' => $activar ? 'Cuenta activada' : 'Cuenta desactivada'));
  }

  public function executeSetPrincipal(sfWebRequest $request)
  {
    if (($respuesta = $this->validarAjax($request, 'notificacion_cuenta/save')) !== null) {
      return $respuesta;
    }
    //*****************************************************************************
    $cuenta = NotificacionCuentaPeer::retrieveByPk((int) $request->getParameter('id'));
    if (!$cuenta) {
      return $this->renderJson(array('ok' => false, 'message' => 'La cuenta no existe'));
    }
    if (!$cuenta->getActivo()) {
      return $this->renderJson(array('ok' => false, 'message' => 'Active la cuenta antes de definirla como principal'));
    }
    //*****************************************************************************
    $con = Propel::getConnection(NotificacionCuentaPeer::DATABASE_NAME);
    $con->beginTransaction();
    try {
      NotificacionCuentaQuery::create()->update(array('EsPrincipal' => 0), $con);
      NotificacionCuentaQuery::create()->filterByPrimaryKey($cuenta->getPrimaryKey())->update(array('EsPrincipal' => 1), $con);
      $con->commit();
    } catch (Exception $e) {
      $con->rollBack();
      throw $e;
    }
    NotificacionCuentaResolver::invalidarCache();
    return $this->renderJson(array('ok' => true, 'message' => 'Cuenta principal actualizada'));
  }

  public function executeTestConnection(sfWebRequest $request)
  {
    if (($respuesta = $this->validarAjax($request, 'notificacion_cuenta/test')) !== null) {
      return $respuesta;
    }
    //*****************************************************************************
    $cuenta = NotificacionCuentaPeer::retrieveByPk((int) $request->getParameter('id'));
    if (!$cuenta) {
      return $this->renderJson(array('ok' => false, 'message' => 'La cuenta no existe'));
    }
    $resultado = NotificacionMailTester::probarConexion(NotificacionCuentaResolver::aArray($cuenta));
    NotificacionCuentaResolver::registrarPrueba($cuenta->getPrimaryKey(), $resultado['ok'], $resultado['error']);
    NotificacionCuentaResolver::invalidarCache();
    return $this->renderJson($resultado);
  }

  public function executeTestSend(sfWebRequest $request)
  {
    if (($respuesta = $this->validarAjax($request, 'notificacion_cuenta/test')) !== null) {
      return $respuesta;
    }
    //*****************************************************************************
    $cuenta = NotificacionCuentaPeer::retrieveByPk((int) $request->getParameter('id'));
    if (!$cuenta) {
      return $this->renderJson(array('ok' => false, 'message' => 'La cuenta no existe'));
    }
    $destino = trim($request->getParameter('destino'));
    if (!filter_var($destino, FILTER_VALIDATE_EMAIL)) {
      return $this->renderJson(array('ok' => false, 'message' => 'El correo de destino no es valido'));
    }
    $resultado = NotificacionMailTester::enviarPrueba(NotificacionCuentaResolver::aArray($cuenta), $destino);
    NotificacionCuentaResolver::registrarPrueba($cuenta->getPrimaryKey(), $resultado['ok'], $resultado['error']);
    NotificacionCuentaResolver::invalidarCache();
    return $this->renderJson($resultado);
  }

  private function nuevaCuenta()
  {
    $cuenta = new NotificacionCuenta();
    $cuenta->setTipo(NotificacionCuentaResolver::TIPO_OAUTH2);
    $cuenta->setHostSmtp('smtp.office365.com');
    $cuenta->setPuerto(587);
    $cuenta->setSecureSmtp('tls');
    $cuenta->setAuthSmtp(1);
    $cuenta->setScope(OAuthTokenProviderSimad::DEFAULT_SCOPE);
    $cuenta->setPrioridad(100);
    $cuenta->setActivo(0);
    $cuenta->setEsPrincipal(0);
    $cuenta->setEstadoOperativo(NotificacionCuentaResolver::ESTADO_SIN_PROBAR);
    return $cuenta;
  }

  /**
   * Copia el formulario a la cuenta. Los secretos vacios conservan el valor actual.
   *
   * @return bool true si cambio algun dato de conexion (obliga a probar de nuevo)
   */
  private function aplicarFormulario(NotificacionCuenta $cuenta, sfWebRequest $request)
  {
    $texto = function ($campo) use ($request) {
      return trim((string) $request->getParameter($campo));
    };
    $nuevo = array(
      'Nombre' => $texto('nombre'),
      'Tipo' => strtoupper($texto('tipo')),
      'EmailRemitente' => strtolower($texto('email_remitente')),
      'NombreRemitente' => $texto('nombre_remitente'),
      'HostSmtp' => $texto('host_smtp'),
      'Puerto' => (int) $texto('puerto'),
      'SecureSmtp' => strtolower($texto('secure_smtp')),
      'AuthSmtp' => $request->getParameter('auth_smtp') ? 1 : 0,
      'UsuarioSmtp' => $texto('usuario_smtp'),
      'TenantId' => $texto('tenant_id'),
      'ClientId' => $texto('client_id'),
      'Scope' => $texto('scope'),
      'Prioridad' => (int) $texto('prioridad'),
    );
    $conexion = array('Tipo', 'HostSmtp', 'Puerto', 'SecureSmtp', 'AuthSmtp', 'UsuarioSmtp', 'TenantId', 'ClientId', 'Scope', 'EmailRemitente');
    $cambioConexion = false;
    foreach ($nuevo as $campo => $valor) {
      $getter = 'get' . $campo;
      if (in_array($campo, $conexion, true) && (string) $cuenta->$getter() !== (string) $valor) {
        $cambioConexion = true;
      }
      $setter = 'set' . $campo;
      $cuenta->$setter($valor);
    }
    foreach (array('pass_smtp' => 'PassSmtp', 'client_secret' => 'ClientSecret') as $campo => $propiedad) {
      $valor = (string) $request->getParameter($campo);
      if ($valor !== '') {
        $setter = 'set' . $propiedad;
        $cuenta->$setter(SED::encryption($valor));
        $cambioConexion = true;
      }
    }
    return $cambioConexion;
  }

  /**
   * @return string|null mensaje del primer error de validacion
   */
  private function validarCuenta(NotificacionCuenta $cuenta, sfWebRequest $request)
  {
    if ($cuenta->getNombre() === '' || strlen($cuenta->getNombre()) > 100) {
      return 'El nombre es obligatorio (maximo 100 caracteres)';
    }
    if (!in_array($cuenta->getTipo(), array(NotificacionCuentaResolver::TIPO_SMTP, NotificacionCuentaResolver::TIPO_OAUTH2), true)) {
      return 'El tipo de autenticacion no es valido';
    }
    $remitente = $cuenta->getEmailRemitente();
    if (!filter_var($remitente, FILTER_VALIDATE_EMAIL)) {
      return 'El correo remitente no es una cuenta de correo valida';
    }
    $dominiosPermitidos = $this->getDominiosPermitidos();
    if (count($dominiosPermitidos) > 0 && !in_array(substr(strrchr($remitente, '@'), 1), $dominiosPermitidos, true)) {
      return 'El dominio del correo remitente no esta permitido';
    }
    if ($cuenta->getHostSmtp() === '') {
      return 'El servidor SMTP es obligatorio';
    }
    if ($cuenta->getPuerto() < 1 || $cuenta->getPuerto() > 65535) {
      return 'El puerto no es valido';
    }
    if (!in_array($cuenta->getSecureSmtp(), array('', 'tls', 'ssl'), true)) {
      return 'El tipo de seguridad debe ser TLS, SSL o ninguno';
    }
    if ($cuenta->getTipo() === NotificacionCuentaResolver::TIPO_OAUTH2) {
      if ($cuenta->getTenantId() === '' || $cuenta->getClientId() === '') {
        return 'Tenant ID y Client ID son obligatorios para OAuth2';
      }
      if ((string) $cuenta->getClientSecret() === '') {
        return 'El Client Secret es obligatorio para OAuth2';
      }
    } elseif ($cuenta->getAuthSmtp() && (string) $cuenta->getPassSmtp() === '') {
      return 'La contrasena es obligatoria cuando el servidor requiere autenticacion';
    }
    if ($cuenta->getPrioridad() < 1) {
      return 'La prioridad debe ser un numero mayor a cero';
    }
    return null;
  }

  private function getDominiosPermitidos()
  {
    $config = simad_util::readConfigFileApp(array('domains_enable'));
    if (is_array($config) && !empty($config['domains_enable'])) {
      return preg_split("/[,]+/", $config['domains_enable'], -1, PREG_SPLIT_NO_EMPTY);
    }
    return array();
  }

  /**
   * Auditoria de cambios sin exponer los secretos (ni siquiera cifrados).
   */
  private function auditar(NotificacionCuenta $anterior, NotificacionCuenta $nueva)
  {
    $registros = array();
    foreach (array($anterior, $nueva) as $cuenta) {
      $copia = clone $cuenta;
      foreach (array('setPassSmtp' => 'getPassSmtp', 'setClientSecret' => 'getClientSecret') as $setter => $getter) {
        if ((string) $copia->$getter() !== '') {
          $copia->$setter(self::SECRETO_MASCARA);
        }
      }
      $registros[] = $copia;
    }
    AuditLogPeer::guardarAuditoriaLite(NotificacionCuentaPeer::getOMClass(), $registros[0], $registros[1], ModulesEnable::Seguridad, $nueva->getPrimaryKey());
  }
}
