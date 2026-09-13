<?php
// config/ldap.php

return [
    // Configuración del servidor LDAP
    'hosts' => ['30.0.1.4'], // Puede ser IP o hostname
    
    // Base DN para búsquedas
    'base_dn' => 'dc=uariv,dc=local',
    
    // Usuario administrador para bind
    'username' => 'suariv@uariv.local', // o 'cn=admin,dc=miempresa,dc=local'
    'password' => 'N3w$$upp0rt19*',
    
    // Puerto LDAP
    'port' => 389, // 389 para LDAP, 636 para LDAPS
    
    // Usar SSL/TLS
    'use_ssl' => false, // true para LDAPS (puerto 636)
    'use_tls' => false, // true para StartTLS (puerto 389)
    
    // Timeout de conexión
    'timeout' => 15,
    
    // Seguir referencias
    'follow_referrals' => false,
    
    // Esquema (para Active Directory)
    'schema' => Adldap\Schemas\ActiveDirectory::class,
];