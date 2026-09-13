<?php

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'prod', false);
sfContext::createInstance($configuration)->dispatch();
//*******************************************************************************

$config = require_once(dirname(__FILE__). '/../config/ldap_settings.php');

try {
    $auth = new AdldapAuth($config);
    
    echo "✓ Conexión exitosa a Active Directory<br>";
    echo "✓ Base DN: " . $config['base_dn'] . "<br>";
    
    $uac_username = "wilson.torres";
    $uac_password = "&qU97lh&/$";
    // Buscar un usuario de prueba
    $user = $auth->findUser($uac_username);
    echo "✓ Usuarios encontrados: " . count($user) . "<br>";
    if($user != null){
        $isActiveUser = $auth->isUserActive($user);
        if($isActiveUser['activo'] === true){
            echo $isActiveUser['razon']. "<br>";        
            $auth_user = $auth->authenticate($uac_username,$uac_password);
            if($auth_user['success'] === true){
                var_dump($auth_user);
            }else{
                echo $auth_user['code'] . " / " . $auth_user['message']. "<br>";
            }
        }else{
            echo $isActiveUser['razon']. "<br>";
        }
    }else{
        echo "✗ Error: Usuario no existe <br>";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage();
}

exit;

// Script rápido para descubrir AD
/*echo "=== DESCUBRIMIENTO AUTOMÁTICO AD/LDAP ===\n";

// Obtener dominio actual
$hostname = gethostname();
$domain = "uariv.local";

// Intentar extraer dominio del hostname
if (strpos($hostname, '.') !== false) {
    $parts = explode('.', $hostname, 2);
    $domain = $parts[1];
}

echo "Dominio detectado: $domain\n\n";

// Probar conexiones comunes
$common_ports = [389, 636, 3268, 3269];
$common_hosts = [
    $domain,
    "dc1.$domain", "dc2.$domain",
    "dc01.$domain", "dc02.$domain",
    "ad.$domain", "ldap.$domain",
    "localhost", "127.0.0.1"
];

foreach ($common_hosts as $host) {
    foreach ($common_ports as $port) {
        $service = getservbyport($port, "tcp");
        $conn = @ldap_connect($host, $port);
        
        if ($conn) {
            ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
            
            if (@ldap_bind($conn)) {
                // Obtener Base DN
                $search = @ldap_read($conn, "", "(objectClass=*)", ["defaultNamingContext"]);
                if ($search) {
                    $entries = ldap_get_entries($conn, $search);
                    $base_dn = $entries[0]['defaultnamingcontext'][0] ?? 'No encontrado';
                    
                    echo "✓ SERVIDOR ENCONTRADO: $host:$port ($service)\n";
                    echo "  Base DN: $base_dn\n";
                    
                    // Probar autenticación
                    echo "  Probando autenticación...\n";
                    // Aquí puedes probar con un usuario de prueba
                }
            }
            ldap_close($conn);
        }
    }
}

exit;*/

// Parámetros
$ldapUrl     = 'ldap://30.0.1.4:389';
$baseDn      = 'DC=uariv,DC=local';
$adminDn     = 'suariv@uariv.local';
$adminPass   = 'N3w$$upp0rt19*';

// Datos de login
$username = $_POST['username'] ?? 'wilson.torres';
$password = $_POST['password'] ?? '&qU97lh&/$';

// 1. Conectar
$ds = ldap_connect($ldapUrl);
if (!$ds) {
    die('ERROR: No se pudo inicializar ldap_connect');
}
ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
ldap_set_option($ds, LDAP_OPT_NETWORK_TIMEOUT, 5);

// 2. Bind como admin
if (!@ldap_bind($ds, $adminDn, $adminPass)) {
    die('ERROR de bind admin: ' . ldap_error($ds));
}

// 3. Buscar DN y userAccountControl
$filter     = sprintf('(sAMAccountName=%s)', ldap_escape($username, '', LDAP_ESCAPE_FILTER));
$attributes = ['dn', 'userAccountControl'];
$search     = ldap_search($ds, $baseDn, $filter, $attributes);
$entries    = ldap_get_entries($ds, $search);

if ($entries['count'] === 0) {
    die('Usuario no encontrado.');
}

$userDn = $entries[0]['dn'];
$uac    = (int)$entries[0]['useraccountcontrol'][0];

// 4. Verificar si la cuenta está deshabilitada
if ($uac & 0x2) {
    die('Error: la cuenta de usuario está deshabilitada.');
}

// 5. Intentar bind con credenciales del usuario
if (@ldap_bind($ds, $userDn, $password)) {
    // Éxito: usuario activo y credenciales válidas
    echo "¡Bienvenido, {$username}!";
    // Aquí inicias tu sesión, etc.
} else {
    die('Credenciales inválidas.');
}

// 6. Cerrar
ldap_unbind($ds);
