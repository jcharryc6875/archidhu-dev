<?php
class LDAPWrapper {
    private $connection;
    private $config;
    private $connected = false;
    
    public function __construct($config = []) {
        $this->config = array_merge([
            'hosts' => ['localhost'],
            'port' => 389,
            'base_dn' => '',
            'username' => '',
            'password' => '',
            'use_tls' => false,
            'use_ssl' => false,
            'version' => 3,
            'timeout' => 10,
            'follow_referrals' => false,
            'auto_reconnect' => true,
            'max_retries' => 3
        ], $config);
    }
    
    /**
     * Conectar al servidor LDAP/AD
     */
    public function connect() {
        if ($this->connected && $this->connection) {
            return true;
        }
        
        $last_error = '';
        
        // Intentar conectar a cada host
        foreach ($this->config['hosts'] as $host) {
            for ($attempt = 1; $attempt <= $this->config['max_retries']; $attempt++) {
                try {
                    $this->connection = @ldap_connect($host, $this->config['port']);
                    
                    if (!$this->connection) {
                        $last_error = "No se pudo conectar a $host:{$this->config['port']}";
                        continue;
                    }
                    
                    // Configurar opciones
                    $this->setLdapOptions();
                    
                    // TLS/SSL
                    if ($this->config['use_tls'] && !$this->config['use_ssl']) {
                        if (!@ldap_start_tls($this->connection)) {
                            $last_error = "No se pudo iniciar TLS con $host";
                            continue;
                        }
                    }
                    
                    // Bind si hay credenciales
                    if (!empty($this->config['username']) && !empty($this->config['password'])) {
                        if (!$this->bind($this->config['username'], $this->config['password'])) {
                            $last_error = "Bind falló para $host";
                            continue;
                        }
                    } else {
                        // Bind anónimo
                        if (!@ldap_bind($this->connection)) {
                            $last_error = "Bind anónimo falló para $host";
                            continue;
                        }
                    }
                    
                    $this->connected = true;
                    error_log("LDAP conectado exitosamente a: $host");
                    return true;
                    
                } catch (Exception $e) {
                    $last_error = $e->getMessage();
                    if ($attempt < $this->config['max_retries']) {
                        sleep(1); // Esperar antes de reintentar
                    }
                }
            }
        }
        
        throw new Exception("No se pudo conectar a ningún servidor LDAP: " . $last_error);
    }
    
    /**
     * Configurar opciones LDAP
     */
    private function setLdapOptions() {
        ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, $this->config['version']);
        ldap_set_option($this->connection, LDAP_OPT_REFERRALS, $this->config['follow_referrals']);
        ldap_set_option($this->connection, LDAP_OPT_NETWORK_TIMEOUT, $this->config['timeout']);
        ldap_set_option($this->connection, LDAP_OPT_TIMELIMIT, $this->config['timeout']);
        ldap_set_option($this->connection, LDAP_OPT_TIMEOUT, $this->config['timeout']);
    }
    
    /**
     * Autenticar usuario
     */
    public function authenticate($username, $password, $base_dn = null) {
        if (empty($username) || empty($password)) {
            throw new Exception("Username y password son requeridos");
        }
        
        $base_dn = $base_dn ?: $this->config['base_dn'];
        
        try {
            // Formatear username según el tipo de servidor
            $user_dn = $this->formatUsername($username, $base_dn);
            
            // Intentar bind con las credenciales del usuario
            if ($this->bind($user_dn, $password)) {
                // Obtener información del usuario
                $user_info = $this->getUserInfo($username, $base_dn);
                return [
                    'success' => true,
                    'user' => $user_info
                ];
            }
            
            return [
                'success' => false,
                'error' => 'Credenciales inválidas'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Bind con el servidor LDAP
     */
    public function bind($username, $password) {
        if (!$this->connection) {
            $this->connect();
        }
        
        return @ldap_bind($this->connection, $username, $password);
    }
    
    /**
     * Formatear username según el servidor
     */
    private function formatUsername($username, $base_dn) {
        $username = trim($username);
        
        // Si ya está formateado, dejarlo así
        if (strpos($username, '@') !== false || strpos($username, '\\') !== false) {
            return $username;
        }
        
        // Para Active Directory - formato UPN
        if ($this->isActiveDirectory()) {
            $domain = $this->extractDomainFromDN($base_dn);
            return $username . '@' . $domain;
        }
        
        // Para LDAP estándar - formato DN
        return "uid=" . $this->escape($username) . "," . $base_dn;
    }
    
    /**
     * Detectar si es Active Directory
     */
    private function isActiveDirectory() {
        if (!$this->connection) return false;
        
        try {
            $search = @ldap_read($this->connection, "", "(objectClass=*)", ["supportedCapabilities"]);
            if ($search) {
                $entries = ldap_get_entries($this->connection, $search);
                if (isset($entries[0]['supportedcapabilities'])) {
                    return true; // AD tiene este atributo
                }
            }
        } catch (Exception $e) {
            // Ignorar error, probablemente no es AD
        }
        
        return false;
    }
    
    /**
     * Extraer dominio desde Base DN
     */
    private function extractDomainFromDN($base_dn) {
        preg_match_all('/DC=([^,]+)/i', $base_dn, $matches);
        return implode('.', $matches[1]);
    }
    
    /**
     * Obtener información del usuario
     */
    public function getUserInfo($username, $base_dn = null) {
        if (!$this->connection) {
            $this->connect();
        }
        
        $base_dn = $base_dn ?: $this->config['base_dn'];
        $search_filter = $this->getUserSearchFilter($username);
        
        $attributes = [
            'dn', 'uid', 'cn', 'givenname', 'sn', 'mail',
            'displayname', 'memberof', 'department', 'title',
            'telephonenumber', 'samaccountname', 'userprincipalname'
        ];
        
        $search = @ldap_search($this->connection, $base_dn, $search_filter, $attributes);
        if (!$search) {
            throw new Exception("Error en búsqueda: " . ldap_error($this->connection));
        }
        
        $entries = ldap_get_entries($this->connection, $search);
        if ($entries['count'] == 0) {
            throw new Exception("Usuario no encontrado");
        }
        
        return $this->formatUserEntry($entries[0]);
    }
    
    /**
     * Crear filtro de búsqueda según el tipo de servidor
     */
    private function getUserSearchFilter($username) {
        $escaped_username = $this->escape($username);
        
        if ($this->isActiveDirectory()) {
            // Para AD, buscar por samaccountname o userprincipalname
            return "(|(samaccountname=$escaped_username)(userprincipalname=$escaped_username))";
        } else {
            // Para LDAP estándar, buscar por uid
            return "(uid=$escaped_username)";
        }
    }
    
    /**
     * Formatear entrada de usuario
     */
    private function formatUserEntry($entry) {
        $user = ['dn' => $entry['dn']];
        
        $attribute_map = [
            'samaccountname' => 'username',
            'uid' => 'username',
            'cn' => 'full_name',
            'givenname' => 'first_name',
            'sn' => 'last_name',
            'mail' => 'email',
            'displayname' => 'display_name',
            'department' => 'department',
            'title' => 'title',
            'telephonenumber' => 'phone',
            'userprincipalname' => 'upn'
        ];
        
        foreach ($attribute_map as $ldap_attr => $user_attr) {
            if (isset($entry[$ldap_attr][0])) {
                $user[$user_attr] = $entry[$ldap_attr][0];
            }
        }
        
        // Grupos
        if (isset($entry['memberof'])) {
            $user['groups'] = [];
            for ($i = 0; $i < $entry['memberof']['count']; $i++) {
                $user['groups'][] = $entry['memberof'][$i];
            }
        }
        
        return $user;
    }
    
    /**
     * Buscar en LDAP
     */
    public function search($filter, $base_dn = null, $attributes = []) {
        if (!$this->connection) {
            $this->connect();
        }
        
        $base_dn = $base_dn ?: $this->config['base_dn'];
        
        $search = @ldap_search($this->connection, $base_dn, $filter, $attributes);
        if (!$search) {
            throw new Exception("Error en búsqueda: " . ldap_error($this->connection));
        }
        
        $entries = ldap_get_entries($this->connection, $search);
        return $this->formatSearchResults($entries);
    }
    
    /**
     * Formatear resultados de búsqueda
     */
    private function formatSearchResults($entries) {
        $results = [];
        
        for ($i = 0; $i < $entries['count']; $i++) {
            $entry = $entries[$i];
            $formatted_entry = ['dn' => $entry['dn']];
            
            foreach ($entry as $key => $value) {
                if ($key === 'count' || is_int($key)) continue;
                
                if (is_array($value) && isset($value['count'])) {
                    if ($value['count'] == 1) {
                        $formatted_entry[$key] = $value[0];
                    } else {
                        $formatted_entry[$key] = [];
                        for ($j = 0; $j < $value['count']; $j++) {
                            $formatted_entry[$key][] = $value[$j];
                        }
                    }
                } else {
                    $formatted_entry[$key] = $value;
                }
            }
            
            $results[] = $formatted_entry;
        }
        
        return $results;
    }
    
    /**
     * Verificar conexión
     */
    public function isConnected() {
        if (!$this->connection || !$this->connected) {
            return false;
        }
        
        // Verificar que la conexión sigue activa
        try {
            $search = @ldap_read($this->connection, "", "(objectClass=*)", [], 1);
            return $search !== false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Reconectar si es necesario
     */
    public function reconnectIfNeeded() {
        if (!$this->isConnected()) {
            $this->close();
            return $this->connect();
        }
        return true;
    }
    
    /**
     * Cerrar conexión
     */
    public function close() {
        if ($this->connection) {
            @ldap_close($this->connection);
            $this->connection = null;
            $this->connected = false;
        }
    }
    
    /**
     * Escapar valores para LDAP
     */
    public function escape($value) {
        return ldap_escape($value, "", LDAP_ESCAPE_FILTER);
    }
    
    /**
     * Obtener error último
     */
    public function getLastError() {
        if ($this->connection) {
            return ldap_error($this->connection);
        }
        return "No hay conexión activa";
    }
    
    /**
     * Destructor
     */
    public function __destruct() {
        $this->close();
    }
}

class LDAPFactory {
    private static $instances = [];
    
    /**
     * Crear conexión para Active Directory
     */
    public static function createForAD($domain, $admin_user = '', $admin_pass = '') {
        $config = [
            'hosts' => self::discoverDomainControllers($domain),
            'base_dn' => self::domainToDN($domain),
            'username' => $admin_user,
            'password' => $admin_pass,
            'use_tls' => true,
            'timeout' => 10
        ];
        
        $key = md5(serialize($config));
        if (!isset(self::$instances[$key])) {
            self::$instances[$key] = new LDAPWrapper($config);
        }
        
        return self::$instances[$key];
    }
    
    /**
     * Crear conexión para LDAP estándar
     */
    public static function createForLDAP($hosts, $base_dn, $bind_dn = '', $bind_pass = '') {
        $config = [
            'hosts' => (array)$hosts,
            'base_dn' => $base_dn,
            'username' => $bind_dn,
            'password' => $bind_pass,
            'use_tls' => true,
            'timeout' => 10
        ];
        
        $key = md5(serialize($config));
        if (!isset(self::$instances[$key])) {
            self::$instances[$key] = new LDAPWrapper($config);
        }
        
        return self::$instances[$key];
    }
    
    /**
     * Descubrir Domain Controllers
     */
    private static function discoverDomainControllers($domain) {
        $controllers = [];
        
        // DNS SRV records
        $srv_records = @dns_get_record("_ldap._tcp.dc._msdcs.$domain", DNS_SRV);
        if ($srv_records) {
            foreach ($srv_records as $record) {
                $controllers[] = $record['target'];
            }
        }
        
        // Common names
        $common_names = ["dc1.$domain", "dc2.$domain", "dc01.$domain", "dc02.$domain", $domain];
        foreach ($common_names as $name) {
            if (checkdnsrr($name, 'A')) {
                $controllers[] = $name;
            }
        }
        
        return array_unique($controllers);
    }
    
    /**
     * Convertir dominio a DN
     */
    private static function domainToDN($domain) {
        $parts = explode('.', $domain);
        $dn_parts = [];
        foreach ($parts as $part) {
            $dn_parts[] = "DC=$part";
        }
        return implode(',', $dn_parts);
    }
}


try {
    // Parámetros
    $ldapUrl     = 'ldap://172.20.176.15:389';
    $baseDn      = 'DC=uariv,DC=local';
    $adminDn     = 'suariv';
    $adminPass   = 'N3w$$upp0rt19*';
    $controller  =  '172.20.176.15';
    $domain      =  'uariv.local';

    // Datos de login
    $username = $_POST['username'] ?? 'guimar.ayala';
    $password = $_POST['password'] ?? 'Gaat*98765';

    $ldap = LDAPFactory::createForAD($controller, "{$adminDn}@{$domain}", $adminPass);
    
    // Autenticar usuario
    $result = $ldap->authenticate($username, $password);
    
    if ($result['success']) {
        echo "Usuario autenticado: " . $result['user']['full_name'] . "\n";
        echo "Email: " . $result['user']['email'] . "\n";
        echo "Departamento: " . $result['user']['department'] . "\n";
    } else {
        echo "Error: " . $result['error'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Error de conexión: " . $e->getMessage() . "\n";
}