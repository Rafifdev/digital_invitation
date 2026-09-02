<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// HTTP protocol
$config['force_https'] = FALSE;

// REST Output Format
$config['rest_default_format'] = 'json';

// REST Supported Output Formats
$config['rest_supported_formats'] = [
    'json',
    'array',
    'csv',
    'html',
    'jsonp',
    'php',
    'serialized',
    'xml',
];

// REST Status Field Name
$config['rest_status_field_name'] = 'status';

// REST Message Field Name
$config['rest_message_field_name'] = 'message';

// Enable Emulate Request
$config['enable_emulate_request'] = TRUE;

// REST Realm
$config['rest_realm'] = 'REST API';

// REST Login
$config['rest_auth'] = FALSE;

// REST Login Source
$config['auth_source'] = 'ldap';

// Allow Authentication and API Keys
$config['allow_auth_and_keys'] = TRUE;

// REST Login Class and Function
$config['auth_library_class'] = '';
$config['auth_library_function'] = '';

// Override auth types for specific class/method
// $config['auth_override_class_method']['deals']['view'] = 'none';
// $config['auth_override_class_method']['deals']['insert'] = 'digest';
// $config['auth_override_class_method']['accounts']['user'] = 'basic';
// $config['auth_override_class_method']['dashboard']['*'] = 'basic';


// ---Uncomment list line for the wildard unit test
// $config['auth_override_class_method']['wildcard_test_cases']['*'] = 'basic';

// Override auth types for specfic

// ---Uncomment list line for the wildard unit test
// $config['auth_override_class_method_http']['wildcard_test_cases']['*']['options'] = 'basic';

// REST Login Usernames
$config['rest_valid_logins'] = ['admin' => '1234'];

// Global IP Whitelisting
$config['rest_ip_whitelist_enabled'] = FALSE;

// REST IP Whitelist
$config['rest_ip_whitelist'] = '';

// Global IP Blacklisting
$config['rest_ip_blacklist_enabled'] = FALSE;

// REST IP Blacklist
$config['rest_ip_blacklist'] = '';

// REST Database Group
$config['rest_database_group'] = 'default';

// REST API Keys Table Name
$config['rest_keys_table'] = 'keys';

// REST Enable Keys
$config['rest_enable_keys'] = TRUE;

// REST Table Key Column Name
$config['rest_key_column'] = 'key';

// REST API Limits method
$config['rest_limits_method'] = 'ROUTED_URL';

// REST Key Length
$config['rest_key_length'] = 40;

// REST API Key Variable
$config['rest_key_name'] = 'X-Api-Key';

// REST Enable Logging
$config['rest_enable_logging'] = FALSE;

// REST API Logs Table Name
$config['rest_logs_table'] = 'logs';

// REST Method Access Control
$config['rest_enable_access'] = FALSE;

// REST API Access Table Name
$config['rest_access_table'] = 'access';

// REST API Param Log Format
$config['rest_logs_json_params'] = FALSE;

// REST Enable Limits
$config['rest_enable_limits'] = FALSE;

// REST API Limits Table Name
$config['rest_limits_table'] = 'limits';

// REST Ignore HTTP Accept
$config['rest_ignore_http_accept'] = FALSE;

// REST AJAX Only
$config['rest_ajax_only'] = FALSE;

// REST Language File
$config['rest_language'] = 'english';

// CORS Check
$config['check_cors'] = true;

// CORS Allowable Headers
$config['allowed_cors_headers'] = [
  'Origin',
  'X-Requested-With',
  'Content-Type',
  'Accept',
  'Access-Control-Request-Method',
  'X-Token',
  'X-Api-Key',
];

// CORS Allowable Methods
$config['allowed_cors_methods'] = [
  'GET',
  'POST',
  'OPTIONS',
  'PUT',
  'PATCH',
  'DELETE'
];

// CORS Allow Any Domain
$config['allow_any_cors_domain'] = true;

// CORS Allowable Domains
$config['allowed_cors_origins'] = [''];
