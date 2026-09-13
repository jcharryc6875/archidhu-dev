<?php

/**
 * Clase para manejar sesiones únicas por usuario
 */
class SessionManager
{
    const SESSION_LIFETIME = 1800; // 30 minutos en segundos
    //const INACTIVITY_TIMEOUT = 1800; // 30 minutos de inactividad permitida
    
    /**
     * Inicia una nueva sesión única para el usuario
     * 
     * @param int $userId ID del usuario
     * @param bool $forceLogout Si debe cerrar sesiones existentes
     * @return bool
     */
    public static function startUniqueSession($usuario_id, $forceLogout = true)
    {
        try {
            // Si se debe forzar logout, cerrar sesiones existentes
            if ($forceLogout) {
                UsuarioSessionsPeer::terminateUserSessions($usuario_id);
            } else {
                // Verificar si ya existe una sesión activa
                if (UsuarioSessionsPeer::hasActiveSession($usuario_id)) {
                    return false; // Ya existe una sesión activa
                }
            }
            //****************************************************************************************************
            $sessionId = session_id();
            $timeout = AppConfig::get('INACTIVITY_TIMEOUT', 1800);
            //****************************************************************************************************
            // Crear registro en la base de datos
            $userSession = new UsuarioSessions();
            $userSession->setUsuarioId($usuario_id);
            $userSession->setSessionId($sessionId);
            $userSession->setIpAdress(IpUtils::getUserIp());
            $userSession->setUserAgent($_SERVER['HTTP_USER_AGENT'] ?? '');
            $userSession->setLastActivity(date('Y-m-d G:i:s'));
            $userSession->setCreatedAt(date('Y-m-d G:i:s'));
            $userSession->setUpdatedAt(date('Y-m-d G:i:s'));
            $userSession->setExpiresAt(date('Y-m-d G:i:s', time() + $timeout));
            $userSession->setIsActive(true);
            $userSession->save();
            //****************************************************************************************************
            sfContext::getInstance()->getUser()->setAttribute('session_record_id', $userSession->getUsuariosessionsId(), 'subscriber');
            sfContext::getInstance()->getUser()->setAttribute('session_start_time', time(), 'subscriber');
            sfContext::getInstance()->getUser()->setAttribute('session_last_activity', time(), 'subscriber');
            //****************************************************************************************************
            return true;
        } catch (PropelException $e) {
            error_log("Error starting unique session: " . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            error_log("Error starting unique session: " . $e->getMessage());
            return false;
        } catch (\Throwable $e) {
            error_log("Error starting unique session: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Valida si la sesión actual es válida
     * 
     * @return bool
     */
    public static function validateCurrentSession()
    {
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $sessionId = session_id();

        if (empty($usuariologuiado) || empty($sessionId)) {
            return false;
        }
        
        try {
            $session_record_id = sfContext::getInstance()->getUser()->getAttribute('session_record_id', '', 'subscriber');
            $sessionRecord = null;

            if (!empty($session_record_id)) {
                $sessionRecord = UsuarioSessionsPeer::retrieveByPK($session_record_id);
            }

            if (!$sessionRecord) {
                $sessionRecord = self::findSessionByUserAndSid($usuariologuiado, $sessionId);
                if ($sessionRecord) {
                    // Actualizar el session_record_id en los atributos de sfUser
                    sfContext::getInstance()->getUser()->setAttribute(
                        'session_record_id',
                        $sessionRecord->getUsuariosessionsId(),
                        'subscriber'
                    );
                }
            }
            
            if (!$sessionRecord || !$sessionRecord->getIsActive()) {
                return false;
            }
            
            // Verificar si no ha expirado
            $expiresAt = strtotime($sessionRecord->getExpiresAt());
            if ($expiresAt < time()) {
                self::terminateCurrentSession();
                return false;
            }
            
            // Verificar que el session_id coincida
            if ($sessionRecord->getSessionId() !== $sessionId) {
                // El session_id no coincide. ANTES de invalidar, verificar si
                // existe otra sesión activa para este usuario con el session_id
                // actual (PHP puede haber regenerado el ID por session locking).
                $matchingSession = self::findSessionByUserAndSid($usuariologuiado, $sessionId);
                
                if ($matchingSession && $matchingSession->getIsActive()) {
                    // Existe un registro con el session_id actual → usar ese
                    sfContext::getInstance()->getUser()->setAttribute(
                        'session_record_id',
                        $matchingSession->getUsuariosessionsId(),
                        'subscriber'
                    );
                    $sessionRecord = $matchingSession;
                } else {
                    // No existe registro con el session_id actual.
                    // Posible causa: session locking regeneró el ID.
                    // En vez de invalidar, ACTUALIZAR el session_id en el registro existente.
                    // Esto es seguro porque ya verificamos que el usuario_id coincide
                    // y el session_record_id estaba en los atributos de sfUser.
                    self::logSessionEvent('SESSION_ID_MISMATCH', $usuariologuiado, [
                        'record_id'  => $sessionRecord->getUsuariosessionsId(),
                        'db_sid'     => substr($sessionRecord->getSessionId(), 0, 20),
                        'php_sid'    => substr($sessionId, 0, 20),
                    ]);
                    
                    // Actualizar el SESSION_ID en la BD al actual
                    $sessionRecord->setSessionId($sessionId);
                    // Continuar con la actualización normal abajo
                }
            }

            $timeout = AppConfig::get('INACTIVITY_TIMEOUT', 1800);
            // Actualizar tiempo de expiración y última actividad
            $sessionRecord->setExpiresAt(date('Y-m-d G:i:s', time() + $timeout));
            $sessionRecord->setLastActivity(date('Y-m-d G:i:s'));
            $sessionRecord->setUpdatedAt(date('Y-m-d G:i:s'));
            $sessionRecord->save();
            
            // Actualizar también en la sesión PHP para JavaScript
            sfContext::getInstance()->getUser()->setAttribute('session_last_activity', time(), 'subscriber');
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error validating session: " . $e->getMessage());
            return true;
        }
    }
    
    /**
     * Busca una sesión activa por usuario_id y session_id.
     * Fallback cuando el session_record_id no funciona.
     */
    private static function findSessionByUserAndSid($usuarioId, $sessionId)
    {
        try {
            $c = new Criteria();
            $c->add(UsuarioSessionsPeer::USUARIO_ID, $usuarioId);
            $c->add(UsuarioSessionsPeer::SESSION_ID, $sessionId);
            $c->add(UsuarioSessionsPeer::IS_ACTIVE, 1);
            $c->setLimit(1);
            $sessions = UsuarioSessionsPeer::doSelect($c);
            return !empty($sessions) ? $sessions[0] : null;
        } catch (Exception $e) {
            error_log("Error in findSessionByUserAndSid: " . $e->getMessage());
            return null;
        }
    }
 
    /**
     * Log de eventos de sesión para diagnóstico.
     * Escribe al error_log de PHP para trazabilidad.
     */
    private static function logSessionEvent($event, $usuarioId, $data = [])
    {
        $info = json_encode($data, JSON_UNESCAPED_SLASHES);
        error_log("[SessionManager][{$event}] usuario={$usuarioId} server=" 
            . gethostname() . " {$info}");
    }

    /**
     * Termina la sesión actual
     */
    public static function terminateCurrentSession()
    {
        //$usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $session_record_id = sfContext::getInstance()->getUser()->getAttribute('session_record_id', '', 'subscriber');
        //***************************************************************************************************
        try {
            if (!empty($session_record_id)) {
                $sessionRecord = UsuarioSessionsPeer::retrieveByPK($session_record_id);
                if ($sessionRecord) {
                    $sessionRecord->setUpdatedAt(date('Y-m-d G:i:s'));
                    $sessionRecord->setIsActive(0);
                    $sessionRecord->save();
                }
            }
            //************************************************************************************************
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            //************************************************************************************************
            session_destroy();
        } catch (PropelException $e) {
            error_log("Error terminating session: " . $e->getMessage());
        } catch (\Exception $e) {
            error_log("Error terminating session: " . $e->getMessage());
        } catch (\Throwable $e) {
            error_log("Error terminating session: " . $e->getMessage());
        }
    }
    
    /**
     * Limpia sesiones expiradas
     */
    public static function cleanExpiredSessions()
    {
        try {
            $c = new Criteria();
            $c->add(UsuarioSessionsPeer::EXPIRES_AT, date('Y-m-d G:i:s'), Criteria::LESS_THAN);
            $expiredSessions = UsuarioSessionsPeer::doSelect($c);
            
            foreach ($expiredSessions as $session) {
                $session->delete();
            }
        } catch (PropelException $e) {
            error_log("Error cleaning expired sessions: " . $e->getMessage());
        } catch (\Exception $e) {
            error_log("Error cleaning expired sessions: " . $e->getMessage());
        } catch (\Throwable $e) {
            error_log("Error cleaning expired sessions: " . $e->getMessage());
        }
    }
    
    /**
     * Actualiza la última actividad del usuario (para actividad via AJAX)
     * 
     * @return array Estado de la sesión
     */
    public static function updateActivity()
    {
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $session_record_id = sfContext::getInstance()->getUser()->getAttribute('session_record_id', '', 'subscriber');

        if (empty($usuariologuiado) || empty($session_record_id)) {
            return array('status' => 'error', 'message' => 'No session found');
        }
        
        try {
            $sessionRecord = UsuarioSessionsPeer::retrieveByPK($session_record_id);
            
            if (!$sessionRecord || !$sessionRecord->getIsActive()) {
                return array('status' => 'expired', 'message' => 'Session expired');
            }
            
            // Verificar si no ha expirado por inactividad
            $lastActivity = $sessionRecord->getLastActivity();
            $inactivityTime = time() - strtotime($lastActivity);
            $timeout = AppConfig::get('INACTIVITY_TIMEOUT', 1800);

            if ($inactivityTime > $timeout) {
                // Sesión expirada por inactividad
                self::terminateCurrentSession();
                return array('status' => 'expired', 'message' => 'Session expired due to inactivity');
            }
            
            // Actualizar actividad
            $sessionRecord->setLastActivity(date('Y-m-d G:i:s'));
            $sessionRecord->setUpdatedAt(date('Y-m-d G:i:s'));
            $sessionRecord->setExpiresAt(date('Y-m-d G:i:s', time() + $timeout));
            $sessionRecord->save();
            
            sfContext::getInstance()->getUser()->setAttribute('session_last_activity', time(), 'subscriber');
            
            $timeRemaining = $timeout - $inactivityTime;
            
            return array(
                'status' => 'active',
                'time_remaining' => $timeRemaining,
                'last_activity' => $sessionRecord->getLastActivity()
            );
        } catch (PropelException $e) {
            error_log("Error updating activity: " . $e->getMessage());
            return array('status' => 'error', 'message' => 'Internal error');
        } catch (\Exception $e) {
            error_log("Error updating activity: " . $e->getMessage());
            return array('status' => 'error', 'message' => 'Internal error');
        } catch (\Throwable $e) {
            error_log("Error updating activity: " . $e->getMessage());
            return array('status' => 'error', 'message' => 'Internal error');
        }
    }
    
    /**
     * Verifica si la sesión ha expirado por inactividad
     * 
     * @return bool
     */
    public static function isSessionExpiredByInactivity()
    {
        $session_record_id = sfContext::getInstance()->getUser()->getAttribute('session_record_id', '', 'subscriber');

        if (empty($session_record_id)) {
            return true;
        }
        
        try {
            $sessionRecord = UsuarioSessionsPeer::retrieveByPK($session_record_id);
            
            if (!$sessionRecord) {
                return true;
            }
            
            $lastActivity = $sessionRecord->getLastActivity();
            $inactivityTime = time() - $lastActivity;
            $timeout = AppConfig::get('INACTIVITY_TIMEOUT', 1800);

            return $inactivityTime > $timeout;
        
        } catch (PropelException $e) {
            error_log("Error checking inactivity: " . $e->getMessage());
            return true;
        } catch (\Exception $e) {
            error_log("Error checking inactivity: " . $e->getMessage());
            return true;
        } catch (\Throwable $e) {
            error_log("Error checking inactivity: " . $e->getMessage());
            return true;
        }
    }
    
    /**
     * Obtiene el tiempo restante antes de que expire la sesión por inactividad
     * 
     * @return int Segundos restantes
     */
    public static function getTimeUntilExpiry()
    {
        $session_record_id = sfContext::getInstance()->getUser()->getAttribute('session_record_id', '', 'subscriber');

        if (empty($session_record_id)) {
            return 0;
        }
        
        try {
            $sessionRecord = UsuarioSessionsPeer::retrieveByPK($session_record_id);
            
            if (!$sessionRecord) {
                return 0;
            }
            
            $lastActivity = $sessionRecord->getLastActivity();
            $inactivityTime = time() - $lastActivity;
            $timeout = AppConfig::get('INACTIVITY_TIMEOUT', 1800);
            
            return max(0, $timeout - $inactivityTime);
            
        } catch (Exception $e) {
            error_log("Error getting time until expiry: " . $e->getMessage());
            return 0;
        }
    }
     /* 
     * @param int $userId
     * @return array
     */
    public static function getUserActiveSessions($userId)
    {
        try {
            $criteria = new Criteria();
            $criteria->add(UsuarioSessionsPeer::USUARIO_ID, $userId);
            $criteria->add(UsuarioSessionsPeer::IS_ACTIVE, true);
            $criteria->add(UsuarioSessionsPeer::EXPIRES_AT, date('Y-m-d H:i:s'), Criteria::GREATER_THAN);
            $criteria->addDescendingOrderByColumn(UsuarioSessionsPeer::UPDATED_AT);
            
            $sessions = UsuarioSessionsPeer::doSelect($criteria);
            
            $result = array();
            foreach ($sessions as $session) {
                $result[] = array(
                    'id' => $session->getId(),
                    'ip_address' => $session->getIpAddress(),
                    'user_agent' => $session->getUserAgent(),
                    'created_at' => $session->getCreatedAt(),
                    'updated_at' => $session->getUpdatedAt(),
                    'expires_at' => $session->getExpiresAt()
                );
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Error getting user active sessions: " . $e->getMessage());
            return array();
        }
    }
}