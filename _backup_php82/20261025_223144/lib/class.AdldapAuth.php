<?php

require __DIR__ . '/../lib/Adldap2/autoload.php';
require __DIR__ . '/../lib/EmailTools/autoload.php';

use Adldap\Adldap;
use Adldap\Models\User;

class AdldapAuth
{
    private $adldap;
    private $provider;
    private $config;
    private $maxRetries = 3;
    private $lastError = null;

    /**
     * Constructor
    */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->initialize();
    }

    /**
     * Inicializar Adldap2 con reintentos
    */
    private function initialize()
    {
        $retryDelay = 1;
        $lastException = null;

        for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
            try {
                // Crear nueva instancia en cada intento
                $this->adldap = new Adldap();
                $this->adldap->addProvider($this->config);
                $this->provider = $this->adldap->connect();

                // Conexión exitosa, salir del loop
                $this->lastError = null;
                return true;
            } catch (\Adldap\Auth\BindException $e) {
                $lastException = $e;
                $this->lastError = [
                    'attempt' => $attempt,
                    'type' => 'BindException',
                    'message' => $e->getMessage(),
                    'time' => date('Y-m-d H:i:s')
                ];

                // Log para diagnóstico
                $this->logError("LDAP Bind intento {$attempt}/{$this->maxRetries} falló", $e);

                if ($attempt < $this->maxRetries) {
                    sleep($retryDelay);
                    $retryDelay *= 2; // Backoff exponencial: 1s, 2s, 4s
                }
            } catch (\Adldap\Connections\ConnectionException $e) {
                $lastException = $e;
                $this->lastError = [
                    'attempt' => $attempt,
                    'type' => 'ConnectionException',
                    'message' => $e->getMessage(),
                    'time' => date('Y-m-d H:i:s')
                ];

                $this->logError("LDAP Connection intento {$attempt}/{$this->maxRetries} falló", $e);

                if ($attempt < $this->maxRetries) {
                    sleep($retryDelay);
                    $retryDelay *= 2;
                }
            } catch (\Exception $e) {
                $lastException = $e;
                $this->lastError = [
                    'attempt' => $attempt,
                    'type' => get_class($e),
                    'message' => $e->getMessage(),
                    'time' => date('Y-m-d H:i:s')
                ];

                $this->logError("LDAP Error general intento {$attempt}/{$this->maxRetries}", $e);

                if ($attempt < $this->maxRetries) {
                    sleep($retryDelay);
                    $retryDelay *= 2;
                }
            }
        }

        // Todos los intentos fallaron
        throw new Exception(
            'Error de autenticación administrativa después de ' . $this->maxRetries . ' intentos. ' .
                'Último error: ' . ($lastException ? $lastException->getMessage() : 'Desconocido')
        );
    }

    /**
     * Log de errores para diagnóstico
     */
    private function logError($mensaje, \Exception $e)
    {
        $logEntry = sprintf(
            "[%s] %s | Host: %s | Error: %s | Trace: %s\n",
            date('Y-m-d H:i:s'),
            $mensaje,
            implode(',', $this->config['hosts'] ?? ['N/A']),
            $e->getMessage(),
            $e->getTraceAsString()
        );

        // Ajusta la ruta según tu estructura
        $logFile = sfConfig::get('sf_log_dir') . DIRECTORY_SEPARATOR . 'ldap_errors.log';
        $logDir = dirname($logFile);

        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        @file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Obtener último error (útil para diagnóstico)
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * Verificar conectividad antes de operaciones críticas
     */
    public function isConnected()
    {
        try {
            // Intenta una operación simple para verificar conexión
            $this->provider->search()->limit(1)->get();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Reconectar si es necesario
     */
    public function reconnectIfNeeded()
    {
        if (!$this->isConnected()) {
            $this->initialize();
        }
    }

    /**
     * Buscar usuario por username
     */
    public function findUser($username)
    {
        try {
            $user = $this->provider->search()
                ->where('samaccountname', '=', $username)
                ->first();

            return $user;
        } catch (\Exception $e) {
            // Intentar reconectar y reintentar una vez
            try {
                $this->reconnectIfNeeded();
                return $this->provider->search()
                    ->where('samaccountname', '=', $username)
                    ->first();
            } catch (\Exception $e2) {
                $this->logError("Error buscando usuario: {$username}", $e2);
                return null;
            }
        }
    }

    /**
     * Verificar si el usuario está activo
     */
    public function isUserActive($user)
    {
        if (!$user) {
            return ['activo' => false, 'razon' => 'Usuario no encontrado', 'code' => 'USER_LDAP_NOT_FOUND'];
        }

        // Verificar si la cuenta está deshabilitada
        if ($user->isDisabled()) {
            return ['activo' => false, 'razon' => 'Cuenta deshabilitada', 'code' => 'USER_LDAP_ERROR_DISABLED'];
        }

        // Verificar si la cuenta está expirada
        if ($user->isExpired()) {
            return ['activo' => false, 'razon' => 'Cuenta expirada', 'code' => 'USER_LDAP_ERROR_EXPIRED'];
        }

        return ['activo' => true, 'razon' => 'Usuario activo', 'code' => 'USER_LDAP_ENABLE'];
    }

    /**
     * Autenticar usuario
     */
    public function authenticate($username, $password)
    {
        try {
            $user = $this->findUser($username);

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Usuario no encontrado',
                    'code' => 'USER_LDAP_NOT_FOUND'
                ];
            }

            $estado = $this->isUserActive($user);

            if (!$estado['activo']) {
                return [
                    'success' => false,
                    'message' => $estado['razon'],
                    'code' => $estado['code']
                ];
            }

            // Intentar autenticación con reintentos
            $authResult = $this->attemptAuthWithRetry($user, $password);

            if ($authResult['success']) {
                $grupos = $user->getGroups()->map(function ($group) {
                    return $group->getCommonName();
                })->toArray();

                return [
                    'success' => true,
                    'message' => 'Autenticación exitosa',
                    'code' => 'USER_LDAP_SUCCESS',
                    'user' => [
                        'username' => $user->getAccountName(),
                        'name' => $user->getCommonName(),
                        'email' => $user->getEmail(),
                        'displayName' => $user->getDisplayName(),
                        'dn' => $user->getDistinguishedName(),
                        'groups' => $grupos,
                        'firstName' => $user->getFirstName(),
                        'lastName' => $user->getLastName(),
                    ]
                ];
            } else {
                return $authResult;
            }
        } catch (\Adldap\Auth\BindException $e) {
            $this->logError("BindException en authenticate para: {$username}", $e);
            return [
                'success' => false,
                'message' => 'Error de autenticación: ' . $e->getMessage(),
                'code' => 'AUTH_LDAP_ERROR'
            ];
        } catch (\Exception $e) {
            $this->logError("Exception en authenticate para: {$username}", $e);
            return [
                'success' => false,
                'message' => 'Error del sistema: ' . $e->getMessage(),
                'code' => 'SYSTEM_LDAP_ERROR'
            ];
        }
    }

    /**
     * Intento de autenticación con reintentos
     */
    private function attemptAuthWithRetry($user, $password)
    {
        $maxAttempts = 2;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                if ($this->provider->auth()->attempt($user->getDistinguishedName(), $password)) {
                    return ['success' => true];
                } else {
                    // Contraseña incorrecta, no reintentar
                    return [
                        'success' => false,
                        'message' => 'Contraseña incorrecta',
                        'code' => 'USER_LDAP_INVALID_PASSWORD'
                    ];
                }
            } catch (\Adldap\Auth\BindException $e) {
                // Podría ser error de conexión, reintentar
                if ($attempt < $maxAttempts) {
                    sleep(1);
                    $this->reconnectIfNeeded();
                    continue;
                }
                throw $e;
            }
        }

        return [
            'success' => false,
            'message' => 'Error de autenticación después de múltiples intentos',
            'code' => 'AUTH_LDAP_RETRY_FAILED'
        ];
    }

    /**
     * Obtener todos los usuarios
     */
    public function getAllUsers()
    {
        try {
            return $this->provider->search()->users()->get();
        } catch (\Exception $e) {
            $this->logError("Error obteniendo todos los usuarios", $e);
            return [];
        }
    }

    /**
     * Buscar usuarios por filtro
     */
    public function searchUsers($filter)
    {
        try {
            return $this->provider->search()
                ->users()
                ->where('cn', 'contains', $filter)
                ->orWhere('samaccountname', 'contains', $filter)
                ->orWhere('mail', 'contains', $filter)
                ->get();
        } catch (\Exception $e) {
            $this->logError("Error buscando usuarios con filtro: {$filter}", $e);
            return [];
        }
    }

    /**
     * Verificar si el usuario pertenece a un grupo
     */
    public function userInGroup($username, $groupName)
    {
        $user = $this->findUser($username);
        return $user ? $user->inGroup($groupName) : false;
    }

    /**
     * Obtener proveedor
     */
    public function getProvider()
    {
        return $this->provider;
    }
}
