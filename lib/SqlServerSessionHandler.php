<?php

/**
 * ============================================================================
 * SqlServerSessionHandler.php
 * ============================================================================
 * 
 * Session handler para PHP 7.4 que almacena sesiones en SQL Server.
 * Reemplaza el handler de archivos (session.save_handler = files) que causa
 * session locking sobre rutas UNC en entornos con balanceador de carga.
 * 
 * VENTAJAS:
 *   - Elimina flock() sobre archivos UNC (causa raíz del session locking)
 *   - Row-level locking en vez de file locking (1-5ms vs 0-271s)
 *   - Funciona con múltiples servidores sin carpeta compartida
 *   - GC manejado por SQL Server (más eficiente que PHP)
 *   - Compatible con Symfony 1.4 / sfSessionStorage
 * 
 * INSTALACIÓN:
 *   1. Crear la tabla PHP_SESSIONS en SQL Server (ver script SQL abajo)
 *   2. Copiar este archivo a lib/SqlServerSessionHandler.php
 *   3. Registrar el handler (ver opciones abajo)
 * 
 * REGISTRO OPCIÓN A - En ProjectConfiguration.class.php (RECOMENDADO):
 *   class ProjectConfiguration extends sfProjectConfiguration
 *   {
 *       public function setup()
 *       {
 *           // ... plugins, etc.
 *           require_once(sfConfig::get('sf_lib_dir') . '/SqlServerSessionHandler.php');
 *           SqlServerSessionHandler::register();
 *       }
 *   }
 * 
 * REGISTRO OPCIÓN B - En php.ini:
 *   ; No cambiar save_handler en php.ini, el registro es programático
 *   ; Solo asegurar que session.save_path NO apunte al UNC
 * 
 * TABLA SQL:
 *   CREATE TABLE PHP_SESSIONS (
 *       SESSION_ID VARCHAR(128) NOT NULL,
 *       SESSION_DATA VARBINARY(MAX) NULL,
 *       SESSION_LIFETIME INT NOT NULL DEFAULT 1800,
 *       SESSION_TIME INT NOT NULL,
 *       CREATED_AT DATETIME2 NOT NULL DEFAULT GETDATE(),
 *       PRIMARY KEY CLUSTERED (SESSION_ID)
 *   );
 * 
 * ============================================================================
 */
class SqlServerSessionHandler implements \SessionHandlerInterface
{
    /** @var resource|null Conexión PDO a SQL Server */
    private $pdo = null;
    
    /** @var string Nombre de la tabla de sesiones */
    private $table = 'PHP_SESSIONS';
    
    /** @var bool Si la conexión fue exitosa */
    private $connected = false;
    
    /** @var string DSN de conexión */
    private $dsn;
    
    /** @var string|null Usuario de BD */
    private $username;
    
    /** @var string|null Password de BD */
    private $password;
    
    /**
     * Registra este handler como el session handler de PHP.
     * Llamar ANTES de session_start() (antes del dispatch de Symfony).
     * 
     * @param array $options Opciones de conexión (opcional, auto-detecta de Propel)
     * @return bool
     */
    public static function register(array $options = [])
    {
        // No registrar si la sesión ya está activa o si ya se enviaron headers
        if (session_status() === PHP_SESSION_ACTIVE || headers_sent()) {
            return false;
        }
        
        $handler = new self($options);
        session_set_save_handler($handler, true);
        
        return true;
    }
    
    /**
     * Constructor.
     * Si no se pasan opciones, las toma de la configuración de Propel/Symfony.
     */
    public function __construct(array $options = [])
    {
        if (!empty($options)) {
            $this->dsn = $options['dsn'] ?? '';
            $this->username = $options['username'] ?? null;
            $this->password = $options['password'] ?? null;
            $this->table = $options['table'] ?? 'PHP_SESSIONS';
        }
    }
    
    /**
     * Obtiene o crea la conexión PDO.
     * Reutiliza la conexión de Propel si está disponible.
     */
    private function getConnection()
    {
        if ($this->pdo !== null) {
            return $this->pdo;
        }
        
        try {
            // Opción 1: Reutilizar la conexión de Propel (recomendado)
            if (class_exists('Propel') && Propel::isInit()) {
                $con = Propel::getConnection();
                if ($con instanceof \PDO) {
                    $this->pdo = $con;
                    $this->connected = true;
                    return $this->pdo;
                }
                // DebugPDO o WrappedConnection de Propel
                if (method_exists($con, 'getWrappedConnection')) {
                    $this->pdo = $con->getWrappedConnection();
                    $this->connected = true;
                    return $this->pdo;
                }
                // Propel 1.x con DebugPDO
                if ($con instanceof DebugPDO) {
                    // DebugPDO extiende PDO, se puede usar directamente
                    $this->pdo = $con;
                    $this->connected = true;
                    return $this->pdo;
                }
            }
            
            // Opción 2: Crear conexión propia desde configuración de Propel
            if (empty($this->dsn)) {
                $this->loadPropelConfig();
            }
            
            if (!empty($this->dsn)) {
                $this->pdo = new \PDO($this->dsn, $this->username, $this->password, [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_TIMEOUT => 5,
                ]);
                $this->connected = true;
            }
            
        } catch (\Exception $e) {
            error_log("[SqlServerSessionHandler] Error de conexión: " . $e->getMessage());
            $this->connected = false;
        }
        
        return $this->pdo;
    }
    
    /**
     * Carga la configuración de conexión desde los archivos de Propel/Symfony.
     */
    private function loadPropelConfig()
    {
        try {
            // Leer runtime-conf.xml o databases.yml de Symfony
            $databasesFile = sfConfig::get('sf_config_dir') . '/databases.yml';
            if (file_exists($databasesFile)) {
                $config = sfYaml::load($databasesFile);
                $dsn = $config['all']['propel']['param']['dsn'] 
                    ?? $config['prod']['propel']['param']['dsn'] 
                    ?? '';
                $this->dsn = $dsn;
                $this->username = $config['all']['propel']['param']['username'] 
                    ?? $config['prod']['propel']['param']['username'] 
                    ?? null;
                $this->password = $config['all']['propel']['param']['password'] 
                    ?? $config['prod']['propel']['param']['password'] 
                    ?? null;
            }
        } catch (\Exception $e) {
            error_log("[SqlServerSessionHandler] Error leyendo config: " . $e->getMessage());
        }
    }
    
    // =========================================================================
    // SessionHandlerInterface
    // =========================================================================
    
    /**
     * Abrir sesión.
     */
    public function open($savePath, $sessionName): bool
    {
        return $this->getConnection() !== null;
    }
    
    /**
     * Cerrar sesión. No cierra la conexión PDO (la reutiliza Propel).
     */
    public function close(): bool
    {
        return true;
    }
    
    /**
     * Leer datos de sesión.
     * Usa UPDLOCK + ROWLOCK para row-level locking mínimo.
     */
    public function read($sessionId): string
    {
        try {
            $pdo = $this->getConnection();
            if (!$pdo) {
                return '';
            }
            
            $stmt = $pdo->prepare(
                "SELECT SESSION_DATA FROM {$this->table} WITH (UPDLOCK, ROWLOCK) 
                 WHERE SESSION_ID = :sid AND SESSION_TIME > :time"
            );
            $stmt->execute([
                ':sid' => $sessionId,
                ':time' => time() - (int)ini_get('session.gc_maxlifetime'),
            ]);
            
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($row && $row['SESSION_DATA'] !== null) {
                // Los datos se guardan como VARBINARY, devolver como string
                return is_resource($row['SESSION_DATA']) 
                    ? stream_get_contents($row['SESSION_DATA']) 
                    : (string)$row['SESSION_DATA'];
            }
            
            return '';
            
        } catch (\Exception $e) {
            error_log("[SqlServerSessionHandler] Error en read({$sessionId}): " . $e->getMessage());
            return '';
        }
    }
    
    /**
     * Escribir datos de sesión.
     * Usa MERGE (upsert) para insertar o actualizar en una sola operación atómica.
     */
    public function write($sessionId, $sessionData): bool
    {
        try {
            $pdo = $this->getConnection();
            if (!$pdo) {
                return false;
            }
            
            $lifetime = (int)ini_get('session.gc_maxlifetime');
            $time = time();
            
            // MERGE = INSERT si no existe, UPDATE si existe (atómico)
            $sql = "MERGE {$this->table} WITH (HOLDLOCK) AS target
                    USING (SELECT :sid AS SESSION_ID) AS source
                    ON target.SESSION_ID = source.SESSION_ID
                    WHEN MATCHED THEN
                        UPDATE SET 
                            SESSION_DATA = :data,
                            SESSION_LIFETIME = :lifetime,
                            SESSION_TIME = :time
                    WHEN NOT MATCHED THEN
                        INSERT (SESSION_ID, SESSION_DATA, SESSION_LIFETIME, SESSION_TIME, CREATED_AT)
                        VALUES (:sid2, :data2, :lifetime2, :time2, GETDATE());";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':sid' => $sessionId,
                ':data' => $sessionData,
                ':lifetime' => $lifetime,
                ':time' => $time,
                ':sid2' => $sessionId,
                ':data2' => $sessionData,
                ':lifetime2' => $lifetime,
                ':time2' => $time,
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            error_log("[SqlServerSessionHandler] Error en write({$sessionId}): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Destruir sesión.
     */
    public function destroy($sessionId): bool
    {
        try {
            $pdo = $this->getConnection();
            if (!$pdo) {
                return false;
            }
            
            $stmt = $pdo->prepare("DELETE FROM {$this->table} WHERE SESSION_ID = :sid");
            $stmt->execute([':sid' => $sessionId]);
            
            return true;
            
        } catch (\Exception $e) {
            error_log("[SqlServerSessionHandler] Error en destroy({$sessionId}): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Garbage collection — eliminar sesiones expiradas.
     * Con SQL Server esto es eficiente: un solo DELETE con WHERE.
     */
    public function gc($maxlifetime): bool
    {
        try {
            $pdo = $this->getConnection();
            if (!$pdo) {
                return false;
            }
            
            $stmt = $pdo->prepare(
                "DELETE FROM {$this->table} WHERE SESSION_TIME < :time"
            );
            $stmt->execute([':time' => time() - $maxlifetime]);
            
            return true;
            
        } catch (\Exception $e) {
            error_log("[SqlServerSessionHandler] Error en gc: " . $e->getMessage());
            return false;
        }
    }
}