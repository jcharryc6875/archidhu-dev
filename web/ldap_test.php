<?php
/**
 * Probe de Domain Controllers + LDAP/STARTTLS/LDAPS bind con diagnóstico AD
 * PHP 7.4 compatible
 */

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once(__DIR__."/../lib/adLDAP/src/adLDAP.php");

//////////////////////////////////////
// CONFIGURA AQUÍ TUS PARÁMETROS
//////////////////////////////////////
$domain            = 'uariv.local';        // tu dominio AD
$baseDn            = 'DC=uariv,DC=local';  // base DN
$serviceUserUpn    = 'suariv@uariv.local'; // cuenta de servicio (recomendado UPN)
$servicePassword   = 'N3w$$upp0rt19*';         // contraseña

$ad = new adLDAP([
    'account_suffix'      => '@uariv.local',
    'base_dn'             => 'DC=uariv,DC=local',
    'domain_controllers'  => ['172.20.176.15'], // solo los que pasaron OK
    'admin_username'      => $serviceUserUpn,
    'admin_password'      => $servicePassword,
    'use_ssl'             => true,
    'use_tls'             => false,
    'ad_port'             => 389,
    'recursive_groups'    => true,
    'real_primarygroup'   => true,
    'network_timeout'     => 5,
]);

$conn = $ad->getLdapConnection();
@ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
@ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);